<?php


namespace App\Http\Controllers;
use app\Models\TesteModel;
use finfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\EmailIrregular;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class TesteController extends Controller
{
    public function todos(Request $request)
    {
        $dataInicial = $request->input('dataInicial');
        $dataFinal = $request->input('dataFinal');

        if (!$dataInicial || !strtotime($dataInicial)) {
            $dataEncontrada = DB::table('respondente')
                ->where('fk_questionario', 21)
                ->whereIn('status', [0, 1, 2, 5])
                ->orderBy('respondido_em', 'asc')
                ->value(DB::raw('DATE(respondido_em)'));

            if (!$dataEncontrada) {
                $dataEncontrada = DB::table('respondente')
                    ->where('fk_questionario', 21)
                    ->orderBy('respondido_em', 'desc')
                    ->value(DB::raw('DATE(respondido_em)'));
            }

            if (!$dataEncontrada) {
                return response()->json([
                    'dataUsada' => null,
                    'dados' => []
                ]);
            }

            $dataInicial = $dataEncontrada;
        }

        $query = DB::table('respondente')
            ->select(
                '*',
                DB::raw("DATE_FORMAT(respondido_em, '%d/%m/%Y') AS respondido_em")
            )
            ->where('fk_questionario', 21);

        if ($dataFinal) {
            $query->whereDate('respondido_em', '>=', $dataInicial)
                ->whereDate('respondido_em', '<=', $dataFinal);
        } else {
            $query->whereDate('respondido_em', '=', $dataInicial);
        }

        $dados = $query->get();

        // --- CARREGAR O JSON DA TABELA ---
        $caminho = storage_path('app/data/tabela.json');
        $tabelaJson = [];
        if (file_exists($caminho)) {
            $conteudo = file_get_contents($caminho);
            $tabelaJson = json_decode($conteudo, true);
            if (!is_array($tabelaJson)) {
                $tabelaJson = [];
            }
        }

        // --- FUNÇÃO PARA NORMALIZAR NOMES (remover acentos, ç -> c, lowercase) ---
        $normalizarNome = function ($nome) {
            if (!$nome) return '';
            return strtolower(Str::ascii($nome));
        };

        // --- PROCESSAR OS DADOS E ANEXAR 'instituicao' ---
        $dados = $dados->map(function ($item) use ($tabelaJson, $normalizarNome) {
            $respostas = json_decode($item->respostas, true) ?: [];

            $item->telefone = $respostas['161'] ?? null;

            $codigoTipo = $respostas['166'] ?? null;

            switch ($codigoTipo) {
                case '478':
                    $item->tipo = 'MEIA-PASSAGEM';
                    break;
                case '476':
                    $item->tipo = 'IDOSO-60';
                    break;
                case '477':
                    $item->tipo = 'IDOSO-65';
                    break;
                default:
                    $item->tipo = null;
            }

            $nomeOriginal = $item->nome ?? '';

            if ($item->tipo === 'MEIA-PASSAGEM' && !empty($tabelaJson)) {
                $nomeBancoNormalizado = $normalizarNome($item->nome ?? '');

                // Filtra todos os registros que batem com o nome
                $registrosEncontrados = collect($tabelaJson)->filter(function ($registro) use ($normalizarNome, $nomeBancoNormalizado) {
                    return $normalizarNome($registro['nome'] ?? '') === $nomeBancoNormalizado;
                });

                $quantidade = $registrosEncontrados->count();

                if ($quantidade === 1) {
                    $item->instituicao = $registrosEncontrados->first()['instituicao'] ?? 'VERIFICAR TABELA';
                } elseif ($quantidade > 1) {
                    $item->instituicao = 'DUPLICADO - VERIFICAR TABELA';
                } else {
                    $item->instituicao = 'VERIFICAR TABELA';
                }
            }

            unset($item->respostas, $item->foi_lida);

            return $item;
        });

        return response()->json([
            'dataUsada' => $dataInicial,
            'dados' => $dados
        ]);
    }

    public function buscarPorNomeOuCpf(Request $request)
    {
        $termo = $request->input('termo');

        if (!$termo) {
            return response()->json(['error' => 'Parâmetro "termo" é obrigatório.'], 400);
        }

        // Normaliza o termo para facilitar buscas (sem acento e minúsculo)
        $termoNormalizado = strtolower(Str::ascii($termo));

        $dados = DB::table('respondente')
            ->select(
                'id_respondente',
                'nome',
                'cpf',
                'email',
                DB::raw("DATE_FORMAT(respondido_em, '%d/%m/%Y %H:%i') AS respondido_em"),
                DB::raw("DATE_FORMAT(baixado_em, '%d/%m/%Y %H:%i') AS baixado_em"),
                'status'
            )
            ->where('fk_questionario', 21) // <-- FILTRO ADICIONADO AQUI
            ->where(function ($query) use ($termo, $termoNormalizado) {
                $query->where('cpf', 'LIKE', "%$termo%")
                    ->orWhereRaw("LOWER(CONVERT(nome USING utf8)) LIKE ?", ["%" . $termoNormalizado . "%"]);
            })
            ->orderBy('respondido_em', 'desc')
            ->get();

        return response()->json($dados);
    }



    public function resumoStatus()
    {
        // Consulta agrupando por status
        $status = DB::table('respondente')
            ->select('status', DB::raw('COUNT(*) as total'))
            ->where('fk_questionario', 21)
            ->groupBy('status')
            ->orderBy('status')
            ->get();

        // Legendas dos status
        $legendas = [
            0 => 'Pendente',
            1 => 'Regular',
            2 => 'Irregular',
            3 => 'Regular com email enviado',
            4 => 'Irregular com email enviado',
            5 => 'Bloqueado sem email',
            6 => 'Bloqueado com email',
            7 => 'Cancelado',
        ];

        // Inicializa resultado com todos os status zerados
        $resultado = [];
        foreach ($legendas as $codigo => $descricao) {
            $resultado[$codigo] = [
                'status' => $codigo,
                'descricao' => $descricao,
                'quantidade' => 0,
            ];
        }

        // Preenche com os dados reais retornados do banco
        $totalGeral = 0;
        foreach ($status as $item) {
            if (isset($resultado[$item->status])) {
                $resultado[$item->status]['quantidade'] = $item->total;
            } else {
                // Caso apareça algum status não mapeado
                $resultado[$item->status] = [
                    'status' => $item->status,
                    'descricao' => 'Desconhecido',
                    'quantidade' => $item->total,
                ];
            }
            $totalGeral += $item->total;
        }

        return response()->json([
            'resumo' => array_values($resultado), // remove as chaves dos índices
            'total_registros' => $totalGeral
        ]);
    }


    public function pegarParametros(Request $request)
    {
        $status = $request->query('status');

        $parametros = DB::select("SELECT * FROM respondente WHERE status_fotos = ?", [$status]);

        return response()->json($parametros);
    }

    public function atualizarStatus(Request $request)
    {
        $id = $request->input('id');
        $status = $request->input('status');

        DB::update("UPDATE respondente SET status = ? WHERE id_respondente = ?", [$status, $id]);
        return response()->json(['mensagem' => 'status atualizado com sucesso.']);
    }

    public function enviarComPhpMailer(Request $request)
    {
        require base_path('vendor/autoload.php');

        // Verifica se o respondente existe
        $respondente = DB::table('respondente')->where('id_respondente', $request->id)->first();
        if (!$respondente) {
            return response()->json(['error' => 'Respondente não encontrado.']);
        }

        // Verifica se já foi enviado
        if ($respondente->status == 4) {
            return response()->json(['message' => 'Email de irregularidade já foi enviado anteriormente.']);
        }

        $mail = new \PHPMailer\PHPMailer\PHPMailer(true);

        try {
            // Configuração do servidor SMTP
            $mail->isSMTP();
            $mail->Host = 'mail.amttdetra.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'questionario@amttdetra.com';
            $mail->Password = 'Paran@2022';
            $mail->SMTPSecure = 'ssl';
            $mail->Port = 465;

            // Remetente e destinatário
            $mail->setFrom('questionario@amttdetra.com', 'CONECTAPG');
            $mail->addAddress($request->email, $request->nome);

            // Configurações do e-mail
            $mail->isHTML(true);
            $mail->CharSet = 'UTF-8';
            $mail->Subject = 'Situação Irregular Detectada';

            // Corpo do e-mail com Blade
            $body = \View::make('emails.irregular', ['nome' => $request->nome])->render();
            $mail->Body = $body;

            // Envia o e-mail
            $mail->send();

            // Captura quem executou e quando
            if (!isset($_SESSION)) session_start();
            $baixadoPor = $_SESSION['nomeServidor'] ?? 'Desconhecido';
            $baixadoEm = now();

            // Atualiza status, quem enviou e o momento
            DB::update("
                UPDATE respondente 
                SET status = 4, baixado_por = ?, baixado_em = ? 
                WHERE id_respondente = ?", 
                [$baixadoPor, $baixadoEm, $request->id]
            );

            return response()->json(['message' => 'Email enviado com sucesso e status atualizado.']);

        } catch (\Exception $e) {
            return response()->json(['error' => "Erro ao enviar e-mail: {$mail->ErrorInfo}"]);
        }
    }

    public function enviarComTextoExtra(Request $request)
    {
        require base_path('vendor/autoload.php');

        // Verifica se o respondente existe
        $respondente = DB::table('respondente')->where('id_respondente', $request->id)->first();
        if (!$respondente) {
            return response()->json(['error' => 'Respondente não encontrado.']);
        }

        // Verifica se já foi enviado
        if ($respondente->status == 4) {
            return response()->json(['message' => 'Email de irregularidade já foi enviado anteriormente.']);
        }

        $mail = new \PHPMailer\PHPMailer\PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = 'mail.amttdetra.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'questionario@amttdetra.com';
            $mail->Password = 'Paran@2022';
            $mail->SMTPSecure = 'ssl';
            $mail->Port = 465;

            $mail->setFrom('questionario@amttdetra.com', 'CONECTAPG');
            $mail->addAddress($request->email, $request->nome);

            $mail->isHTML(true);
            $mail->CharSet = 'UTF-8';
            $mail->Subject = 'Situação Irregular Detectada';

            // Adiciona variável com texto extra
            $textoExtra = $request->texto_extra ?? null;

            $body = \View::make('emails.irregularComTexto', [
                'nome' => $request->nome,
                'texto_extra' => $textoExtra
            ])->render();
            $mail->Body = $body;

            $mail->send();

            if (!isset($_SESSION)) session_start();
            $baixadoPor = $_SESSION['nomeServidor'] ?? 'Desconhecido';
            $baixadoEm = now();

            DB::update("
                UPDATE respondente 
                SET status = 4, baixado_por = ?, baixado_em = ? 
                WHERE id_respondente = ?", 
                [$baixadoPor, $baixadoEm, $request->id]
            );

            return response()->json(['message' => 'Email enviado com sucesso com texto extra e status atualizado.']);

        } catch (\Exception $e) {
            return response()->json(['error' => "Erro ao enviar e-mail: {$mail->ErrorInfo}"]);
        }
    }


    public function enviarComPhpMailerBloqueado(Request $request)
    {
        require base_path('vendor/autoload.php');

        // Verifica se o respondente existe
        $respondente = DB::table('respondente')->where('id_respondente', $request->id)->first();
        if (!$respondente) {
            return response()->json(['error' => 'Respondente não encontrado.']);
        }

        // Verifica se já foi enviado
        if ($respondente->status == 6) {
            return response()->json(['message' => 'Email de bloqueio já foi enviado anteriormente.']);
        }

        $mail = new \PHPMailer\PHPMailer\PHPMailer(true);

        try {
            // Configuração do servidor SMTP
            $mail->isSMTP();
            $mail->Host = 'mail.amttdetra.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'questionario@amttdetra.com';
            $mail->Password = 'Paran@2022';
            $mail->SMTPSecure = 'ssl';
            $mail->Port = 465;

            // Remetente e destinatário
            $mail->setFrom('questionario@amttdetra.com', 'CONECTAPG');
            $mail->addAddress($request->email, $request->nome);

            // Configurações do e-mail
            $mail->isHTML(true);
            $mail->CharSet = 'UTF-8';
            $mail->Subject = 'Situação Irregular Detectada';

            // Corpo do e-mail com Blade
            $body = \View::make('emails.bloqueado', ['nome' => $request->nome])->render();
            $mail->Body = $body;

            // Envia o e-mail
            $mail->send();

            // Captura quem executou e quando
            if (!isset($_SESSION)) session_start();
            $baixadoPor = $_SESSION['nomeServidor'] ?? 'Desconhecido';
            $baixadoEm = now();

            // Atualiza status, quem enviou e o momento
            DB::update("
                UPDATE respondente 
                SET status = 6, baixado_por = ?, baixado_em = ? 
                WHERE id_respondente = ?", 
                [$baixadoPor, $baixadoEm, $request->id]
            );

            return response()->json(['message' => 'Email enviado com sucesso e status atualizado.']);

        } catch (\Exception $e) {
            return response()->json(['error' => "Erro ao enviar e-mail: {$mail->ErrorInfo}"]);
        }
    }

    public function enviarComPhpMailerRegular(Request $request)
    {
        require base_path('vendor/autoload.php');

        // Verifica se já foi enviado
        $respondente = DB::table('respondente')->where('id_respondente', $request->id)->first();
        if (!$respondente) {
            return response()->json(['error' => 'Respondente não encontrado.']);
        }

        if ($respondente->status == 3) {
            return response()->json(['message' => 'Email já foi enviado anteriormente.']);
        }

        $mail = new \PHPMailer\PHPMailer\PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = 'mail.amttdetra.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'questionario@amttdetra.com';
            $mail->Password = 'Paran@2022';
            $mail->SMTPSecure = 'ssl';
            $mail->Port = 465;

            $mail->setFrom('questionario@amttdetra.com', 'CONECTAPG');
            $mail->addAddress($request->email, $request->nome);

            $mail->isHTML(true);
            $mail->CharSet = 'UTF-8';
            $mail->Subject = 'Cadastro Efetuado';

            $body = \View::make('emails.regular', ['nome' => $request->nome])->render();
            $mail->Body = $body;

            $mail->send();

            if (!isset($_SESSION)) session_start();
            $baixadoPor = $_SESSION['nomeServidor'] ?? 'Desconhecido';
            $baixadoEm = now();

            DB::update("
                UPDATE respondente 
                SET status = 3, baixado_por = ?, baixado_em = ? 
                WHERE id_respondente = ?", 
                [$baixadoPor, $baixadoEm, $request->id]
            );

            return response()->json(['message' => 'Email enviado com sucesso e status atualizado.']);

        } catch (\Exception $e) {
            return response()->json(['error' => "Erro ao enviar e-mail: {$mail->ErrorInfo}"]);
        }
    }


    //
    // public function fotoPorId($id)
    // {
    //     $aluno = DB::table('respondente')
    //         ->where('fk_questionario', 21)
    //         ->where('id_respondente', $id)
    //         ->first();
//
    //     if (!$aluno) {
    //         return response()->json(['erro' => 'Aluno não encontrado'], 404);
    //     }
//
    //     $data = \Carbon\Carbon::parse($aluno->respondido_em)->format('d-m-Y'); // formato com traço
    //     $cpf = preg_replace('/\D/', '', $aluno->cpf);
//
    //     // Faz a requisição externa
    //     $url = "https://amttdetra.com/questionario/questionario_ajax2.php?case=buscarFotos&cpf={$cpf}&id={$aluno->id_respondente}&data={$data}";
    //     $response = Http::get($url);
//
    //     if ($response->successful()) {
    //         $fotos = $response->json();
    //         $aluno->foto = $fotos['fotoPessoa'] ?? null;
    //         $aluno->identidade = $fotos['fotoDocumento'] ?? null;
    //     } else {
    //         $aluno->foto = null;
    //         $aluno->identidade = null;
    //     }
//
    //     return response()->json($aluno);
    // }
//

    public function fotoPorId($id)
    {
        $aluno = DB::table('respondente')
            ->where('fk_questionario', 21)
            ->where('id_respondente', $id)
            ->first();

        if (!$aluno) {
            return response()->json(['erro' => 'Aluno não encontrado'], 404);
        }

        $data = \Carbon\Carbon::parse($aluno->respondido_em)->format('d-m-Y');
        $cpf = preg_replace('/\D/', '', $aluno->cpf);

        $basePath = base_path("../../questionario/uploads/{$data}/");

        $fotoPessoa = $this->buscarArquivo($basePath, "{$cpf}_fotoPessoa_{$aluno->id_respondente}", $data);
        $fotoDocumento = $this->buscarArquivo($basePath, "{$cpf}_fotoDocumento_{$aluno->id_respondente}", $data);

        $senha = 'minhaSenhaFixa123'; // 🔒 senha fixa

        return response()->json([
            'foto' => $fotoPessoa ? route('imagem.protegida', ['data' => $data, 'nome' => basename($fotoPessoa), 'senha' => $senha]) : null,
            'identidade' => $fotoDocumento ? route('imagem.protegida', ['data' => $data, 'nome' => basename($fotoDocumento), 'senha' => $senha]) : null,
        ]);
    }

    private function buscarArquivo($path, $nomeBase, $data)
    {
        $extensoes = ['jpg', 'jpeg', 'png', 'webp', 'bmp'];

        foreach ($extensoes as $ext) {
            $arquivo = $path . $nomeBase . '.' . $ext;

            if (file_exists($arquivo)) {
                return "questionario/uploads/{$data}/{$nomeBase}.{$ext}";
            }
        }

        return null;
    }

    public function serveImagemProtegida($data, $nome)
    {
        $senhaRecebida = request()->query('senha');
        $senhaCorreta = 'minhaSenhaFixa123'; // 🔒 mesma senha da fotoPorId()

        if ($senhaRecebida !== $senhaCorreta) {
            return response()->json(['erro' => 'Acesso negado: senha incorreta'], 403);
        }

        $basePath = base_path("../../questionario/uploads/{$data}/");
        $caminhoArquivo = realpath($basePath . '/' . $nome);

        // Segurança: verifica se o arquivo está dentro do diretório permitido
        if (!$caminhoArquivo || strpos($caminhoArquivo, realpath($basePath)) !== 0 || !file_exists($caminhoArquivo)) {
            return response()->json(['erro' => 'Arquivo não encontrado'], 404);
        }

        $mimeType = mime_content_type($caminhoArquivo);
        return response()->file($caminhoArquivo, ['Content-Type' => $mimeType]);
    }


    /* 
    public function fotoPorId($id)
    {
        $aluno = DB::table('respondente')
            ->where('fk_questionario', 21)
            ->where('id_respondente', $id)
            ->first();

        if (!$aluno) {
            return response()->json(['erro' => 'Aluno não encontrado'], 404);
        }

        $data = \Carbon\Carbon::parse($aluno->respondido_em)->format('d-m-Y');
        $cpf = preg_replace('/\D/', '', $aluno->cpf);

        $url = "https://amttdetra.com/questionario/questionario_ajax2.php?case=buscarFotos&cpf={$cpf}&id={$aluno->id_respondente}&data={$data}";

        try {
            $response = Http::timeout(60) // espera até 60 segundos
                ->retry(2, 2000)          // tenta no máximo 2 vezes, com 2 segundos entre as tentativas
                ->get($url);

            if ($response->successful()) {
                $fotos = $response->json();
                $aluno->foto = $fotos['fotoPessoa'] ?? null;
                $aluno->identidade = $fotos['fotoDocumento'] ?? null;
            } else {
                $aluno->foto = null;
                $aluno->identidade = null;
            }
        } catch (\Exception $e) {
            $aluno->foto = null;
            $aluno->identidade = null;
        }

        return response()->json($aluno);
    }
    */

    public function historico()
    {
        $historico = DB::table('respondente')
            ->where('fk_questionario', 21)
            ->whereIn('cpf', function ($query) {
                $query->select('cpf')
                    ->from('respondente')
                    ->where('fk_questionario', 21)
                    ->groupBy('cpf')
                    ->havingRaw('COUNT(*) > 1');
            })
            ->orderBy('cpf')
            ->orderBy('respondido_em')
            ->get();

        return response()->json($historico);
    }

    public function tabelaExcel(Request $request)
    {
        $dataInicial = $request->input('dataInicial');
        $tipo = $request->input('tipo');

        // Fallback se dataInicial for inválida ou não enviada
        if (!$dataInicial || !strtotime($dataInicial)) {
            $dataEncontrada = DB::table('respondente')
                ->where('fk_questionario', 18)
                ->orderBy('respondido_em', 'asc')
                ->value(DB::raw('DATE(respondido_em)'));

            if (!$dataEncontrada) {
                return response()->json([
                    'dados' => []
                ]);
            }

            $dataInicial = $dataEncontrada;
        }

        // Query base
        $dados = DB::table('respondente')
            ->select(
                'id_respondente',
                DB::raw("DATE_FORMAT(respondido_em, '%d/%m/%Y %H:%i') AS respondido_em"),
                'nome',
                'email',
                'cpf',
                'respostas'
            )
            ->where('fk_questionario', 18)
            ->whereDate('respondido_em', '=', $dataInicial)
            ->get();

        return response()->json([
            'dados' => $dados
        ]);
    }



}
