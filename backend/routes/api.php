<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TesteController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::get('/check-session', function () {
    if (!isset($_SESSION)) {
        session_start();
    }
    if (isset($_SESSION['nomeServidor'])) {
        return response()->json(['logado' => true, 'session' => $_SESSION]);
    }
    return response()->json(['logado' => false, 'session' => $_SESSION]);
});

Route::middleware(['check.nome.servidor'])->group(function () {
    Route::get('/todos', [TesteController::class, 'todos']);
    Route::get('/pesquisar', [TesteController::class, 'buscarPorNomeOuCpf']);
    Route::get('/tabela-excel', [TesteController::class, 'tabelaExcel']);
    Route::get('/resumo-status', [TesteController::class, 'resumoStatus']);
    Route::get('/pegarParametros', [TesteController::class, 'pegarParametros']);
    Route::patch('/atualizar-status', [TesteController::class, 'atualizarStatus']);
    Route::patch('/enviar-email', [TesteController::class, 'enviarComPhpMailer']);
    Route::patch('/enviar-email-com-texto', [TesteController::class, 'enviarComTextoExtra']);
    Route::patch('/enviar-email-regular', [TesteController::class, 'enviarComPhpMailerRegular']);
    Route::patch('/enviar-email-bloqueado', [TesteController::class, 'enviarComPhpMailerBloqueado']);
    Route::get('/foto-por-id/{id}', [TesteController::class, 'fotoPorId']);

    Route::get('/imagem/{data}/{nome}', [TesteController::class, 'serveImagemProtegida'])
    ->name('imagem.protegida');
    

    //Route::get('/filtrar-por-data',[TesteController::class, 'filtrarPorData']);
    Route::get('/historico', [TesteController::class, 'historico']);
});