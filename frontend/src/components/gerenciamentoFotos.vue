<template>
  <div class="min-h-dvh bg-gray-100 p-6">
    <!-- Título -->
    <div
      class="relative bg-blue-400 text-white rounded-lg py-4 mb-6 shadow-lg px-4 flex items-center"
    >
      <!-- Ícone esquerdo -->
      <a href="https://amttdetra.com/painel.php" class="mr-4">
        <img
          src="/logomarca_negativa.png"
          alt="Carregando..."
          class="w-12 h-12 rounded-3xl transition-shadow duration-200 hover:shadow-xl"
        />
      </a>

      <!-- Título centralizado absoluto -->
      <h1 class="absolute left-1/2 transform -translate-x-1/2 text-3xl font-bold">
        Validação de Fotos
      </h1>

      <!-- Ícone direito -->
      <a
        href="https://amttdetra.com/questionario/interno"
        class="ml-auto text-3xl hover:underline"
      >
        📋
      </a>
    </div>

    <!-- Filtros e Histórico -->
    <div class="flex flex-nowrap justify-between items-center mb-6 gap-2">
      <!-- Filtros -->
      <div class="flex items-end gap-4 flex-1 m-0 p-0">
        <div
          @click="getStatus()"
          class="py-2 w-6 hover:text-blue-700"
          title="Resumo dos status"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
            stroke-width="1.5"
            stroke="currentColor"
            class="size-6"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 5.25h.008v.008H12v-.008Z"
            />
          </svg>
        </div>
        <div
          @click="abrirModalPesquisa()"
          class="py-2 w-6 hover:text-blue-700"
          title="Pesquisar Dado Específico"
        >
          <svg
            class="w-6 h-6 text-gray-800"
            aria-hidden="true"
            xmlns="http://www.w3.org/2000/svg"
            width="24"
            height="24"
            fill="none"
            viewBox="0 0 24 24"
          >
            <path
              stroke="currentColor"
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M10 11h2v5m-2 0h4m-2.592-8.5h.01M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"
            />
          </svg>
        </div>
        <div class="w-44">
          <label class="block text-sm font-medium text-gray-700 mb-1"
            >Benificiário:</label
          >
          <select
            v-model="beneficiarioSelecionado"
            class="w-full border border-gray-300 bg-white py-2 px-3 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 hover:border-blue-700"
          >
            <option selected value="todos_benificiarios">TODOS</option>
            <option value="MEIA-PASSAGEM">MEIA PASSAGEM</option>
            <option value="IDOSO-60">IDOSO 60-64</option>
            <option value="IDOSO-65">IDOSO 65+</option>

            <!--<option value="data">Data</option> -->
          </select>
        </div>
        <div class="w-44">
          <label class="block text-sm font-medium text-gray-700 mb-1">Status:</label>
          <select
            v-model="statusSelecionado"
            class="w-full border border-gray-300 bg-white py-2 px-3 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 hover:border-blue-700"
          >
            <option value="todos">Todos</option>
            <option value="pendente">Pendentes</option>
            <!--<option value="regular">Regulares</option> -->
            <option value="regular_com_email">Regulares C/ email</option>
            <option value="regular_sem_email">Regulares S/ email</option>
            <!--<option value="irregular">Irregulares</option> -->
            <option value="irregular_com_email">Irregulares C/ Email</option>
            <option value="irregular_sem_email">Irregulares S/ Email</option>
            <option value="bloqueado_com_email">Bloqueados C/ Email</option>
            <option value="bloqueado_sem_email">Bloqueados S/ Email</option>

            <!--<option value="data">Data</option> -->
          </select>
        </div>
        <div class="w-60">
          <label class="block text-sm font-medium text-gray-700 mb-1"
            >Campo de pesquisa:</label
          >
          <input
            v-model="filtroPesquisa"
            type="text"
            class="w-full rounded-xl border border-gray-300 bg-white py-2 px-3 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
            placeholder="Pesquise aqui..."
          />
        </div>
        <div class="flex items-end gap-4">
          <!-- Input -->
          <div class="w-44">
            <div class="flex items-center justify-between mb-1">
              <label class="text-sm text-gray-700">Selecionar Data 1:</label>
              <div class="flex gap-1">
                <button
                  type="button"
                  class="px-2 text-blue-600 hover:text-blue-800 font-semibold"
                  @click="mudarData('dataInicial', -1)"
                >
                  &lt;
                </button>
                <button
                  type="button"
                  class="px-2 text-blue-600 hover:text-blue-800 font-semibold"
                  @click="mudarData('dataInicial', 1)"
                >
                  &gt;
                </button>
              </div>
            </div>

            <input
              type="date"
              v-model="dataInicial"
              class="w-full text-center rounded-xl border border-gray-300 bg-white py-2 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
            />
          </div>

          <!-- Botão + -->
          <div
            @click="alternarDatas()"
            class="flex items-center justify-center cursor-pointer"
          >
            <span v-if="!mostraDataFinal" class="text-4xl font-bold">+</span>
            <span v-else class="text-4xl font-bold">-</span>
          </div>
        </div>

        <div v-if="mostraDataFinal" class="w-44">
          <div class="flex items-center justify-between mb-1">
            <label class="block text-sm font-medium text-gray-700"
              >Selecionar Data 2</label
            >
            <div class="flex gap-1">
              <button
                type="button"
                class="px-2 text-blue-600 hover:text-blue-800 font-semibold"
                @click="mudarData('dataFinal', -1)"
              >
                &lt;
              </button>
              <button
                type="button"
                class="px-2 text-blue-600 hover:text-blue-800 font-semibold"
                @click="mudarData('dataFinal', 1)"
              >
                &gt;
              </button>
            </div>
          </div>
          <input
            type="date"
            v-model="dataFinal"
            class="w-full text-center rounded-xl border border-gray-300 bg-white py-2 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
          />
        </div>

        <div class="w-48">
          <button
            @click="buscarDados()"
            class="flex bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-5 rounded-2xl shadow-lg transition duration-200"
          >
            <svg
              class="w-6 h-6 text-gray-800 dark:text-white mr-2"
              aria-hidden="true"
              xmlns="http://www.w3.org/2000/svg"
              width="24"
              height="24"
              fill="none"
              viewBox="0 0 24 24"
            >
              <path
                stroke="currentColor"
                stroke-linecap="round"
                stroke-width="2"
                d="m21 21-3.5-3.5M17 10a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z"
              />
            </svg>
            Buscar
          </button>
        </div>
      </div>
      <!--
      <button
        @click="carregarHistorico()"
        class="bg-purple-600 hover:bg-purple-700 text-white font-semibold py-2 px-2 rounded-2xl shadow-lg transition duration-200"
        style="align-self: center"
      >
        📜 Histórico
      </button>
       -->
    </div>

    <!-- INFORMAÇOES DA PUXADA -->
    <div class="flex items-center gap-2 mb-2">
      <!-- <svg
        @click="puxarDados()"
        xmlns="http://www.w3.org/2000/svg"
        fill="none"
        viewBox="0 0 24 24"
        stroke-width="2"
        stroke="currentColor"
        class="w-8 h-8 cursor-pointer text-blue-700 hover:text-blue-800 transition-colors"
      >
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99"
        />
      </svg> -->

      <h2 class="text-lg font-semibold text-gray-800">
        Quantidade de Resultados ({{ formatarData(dataInicial)
        }}<span v-if="dataFinal != null"> - {{ formatarData(dataFinal) }}</span
        >): {{ alunos.length }} /
        {{ alunosOrdenados.length }}
      </h2>
    </div>

    <!-- Tabela -->
    <transition name="slide-fade">
      <div
        v-if="!mostrarHistorico"
        class="overflow-x-auto bg-white shadow-xl border border-gray-200"
      >
        <table class="min-w-full table-auto border-collapse text-sm">
          <thead>
            <tr class="bg-blue-300 text-gray-800 uppercase tracking-wide">
              <!-- Coluna ID -->
              <th class="w-[5%] px-4 py-1 text-left border border-black">
                <div class="flex justify-between items-center">
                  <span>ID</span>
                  <OrdenacaoSetas
                    coluna="id"
                    v-model:colunaAtiva="colunaOrdenada"
                    v-model:direcao="direcaoOrdenacao"
                  />
                </div>
              </th>

              <!-- Coluna Nome -->
              <th class="w-[33%] px-4 py-1 text-left border border-black">
                <div class="flex justify-between items-center">
                  <span>Nome</span>
                  <OrdenacaoSetas
                    coluna="nome"
                    v-model:colunaAtiva="colunaOrdenada"
                    v-model:direcao="direcaoOrdenacao"
                  />
                </div>
              </th>

              <!-- Coluna CPF -->
              <th class="w-[10%] px-4 py-1 text-left border border-black">
                <div class="flex justify-between items-center">
                  <span>CPF</span>
                  <OrdenacaoSetas
                    coluna="cpf"
                    v-model:colunaAtiva="colunaOrdenada"
                    v-model:direcao="direcaoOrdenacao"
                  />
                </div>
              </th>

              <!-- Coluna Status -->
              <th class="w-[22%] px-4 py-1 text-left border border-black">
                <div class="flex justify-between items-center">
                  <span>Status</span>
                  <OrdenacaoSetas
                    coluna="status"
                    v-model:colunaAtiva="colunaOrdenada"
                    v-model:direcao="direcaoOrdenacao"
                  />
                </div>
              </th>

              <!-- Coluna Tipo -->
              <th class="w-[15%] px-4 py-1 text-left border border-black">
                <div class="flex justify-between items-center">
                  <span>Tipo</span>
                  <OrdenacaoSetas
                    coluna="tipo"
                    v-model:colunaAtiva="colunaOrdenada"
                    v-model:direcao="direcaoOrdenacao"
                  />
                </div>
              </th>

              <th class="w-[10%] px-4 py-1 text-left border border-black">
                <div class="flex justify-between items-center">
                  <span>Data</span>
                  <OrdenacaoSetas
                    coluna="respondido_em"
                    v-model:colunaAtiva="colunaOrdenada"
                    v-model:direcao="direcaoOrdenacao"
                  />
                </div>
              </th>

              <!-- Coluna Ações (sem ordenação) -->
              <th class="w-[10%] px-4 py-1 text-left border border-black">Ações</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-if="loadingTabela"
              v-for="n in 8"
              :key="'skeleton-' + n"
              class="bg-gray-100 animate-pulse"
            >
              <td class="border border-black px-4 py-2">
                <div class="h-4 bg-gray-300 rounded w-8"></div>
              </td>
              <td class="border border-black px-4 py-2">
                <div class="h-4 bg-gray-300 rounded w-32"></div>
              </td>
              <td class="border border-black px-4 py-2">
                <div class="h-4 bg-gray-300 rounded w-20"></div>
              </td>
              <td class="border border-black px-4 py-2">
                <div class="h-4 bg-gray-300 rounded w-24"></div>
              </td>
              <td class="border border-black px-4 py-2">
                <div class="h-4 bg-gray-300 rounded w-20"></div>
              </td>
              <td class="border border-black px-4 py-2">
                <div class="h-6 bg-gray-300 rounded w-16"></div>
              </td>
              <td class="border border-black px-4 py-2">
                <div class="h-6 bg-gray-300 rounded w-16"></div>
              </td>
            </tr>
            <tr
              v-else
              v-for="(aluno, index) in alunosPaginados"
              :key="aluno.id_respondente"
              :class="index % 2 === 0 ? 'bg-gray-100' : 'bg-gray-50'"
              class="hover:bg-blue-100 transition"
            >
              <td class="border border-black px-4 py-2" :class="aluno.cor">
                {{ aluno.id_respondente }}
              </td>
              <td class="border border-black px-4 py-2">{{ aluno.nome }}</td>
              <td class="border border-black px-4 py-2">{{ formatarCPF(aluno.cpf) }}</td>
              <td class="border border-black px-4 py-2">
                <span
                  :class="
                    aluno.status === 1 || aluno.status === 3
                      ? 'text-green-600'
                      : aluno.status === 0
                      ? 'text-yellow-500'
                      : 'text-red-500'
                  "
                >
                  {{
                    aluno.status === 1
                      ? "✅ Aprovado"
                      : aluno.status === 2
                      ? "❌ Negado"
                      : aluno.status === 3
                      ? "✅📧 Aprovado (e-mail enviado)"
                      : aluno.status === 4
                      ? "❌📧 Negado (e-mail enviado)"
                      : aluno.status === 5
                      ? "🚫 Bloqueado"
                      : aluno.status === 6
                      ? "🚫📧 Bloqueado (e-mail enviado)"
                      : "⏳ Pendente"
                  }}
                </span>
              </td>
              <td class="border border-black px-4 py-2">{{ aluno.tipo }}</td>
              <td class="border border-black px-4 py-2">{{ aluno.respondido_em }}</td>
              <td class="border border-black px-4 py-2">
                <button
                  @click="abrirModal(aluno)"
                  class="flex items-center gap-1 bg-blue-500 hover:bg-blue-500 text-white text-xs font-semibold py-1 px-3 rounded shadow"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="1.5"
                    stroke="currentColor"
                    class="size-4"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"
                    />
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"
                    />
                  </svg>
                  Visualizar
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </transition>
    <!-- coloque isso logo após o fechamento da tabela, fora do container branco -->
    <div class="flex items-center justify-between mt-2 text-sm text-gray-700 px-2">
      <!-- Quantidade de registros -->
      <div>
        Exibindo
        <span class="font-semibold">{{ indiceInicio + 1 }}</span>
        até
        <span class="font-semibold">{{ indiceFim }}</span>
        de
        <span class="font-semibold">{{ alunosOrdenados.length }}</span>
        registros
      </div>

      <!-- Navegação de páginas -->
      <div class="flex items-center gap-2">
        <!-- Botão anterior -->
        <button
          @click="paginaAtual--"
          :disabled="paginaAtual === 1"
          class="px-2 py-1 rounded border border-gray-300 text-gray-600 hover:bg-gray-100 disabled:opacity-50"
        >
          ◀️
        </button>

        <!-- Números das páginas -->
        <button
          v-for="pagina in paginasVisiveis"
          :key="pagina"
          @click="paginaAtual = pagina"
          :class="[
            'px-3 py-1 rounded border cursor-pointer',
            pagina === paginaAtual
              ? 'bg-blue-600 text-white border-blue-600'
              : 'bg-transparent text-gray-700 border-gray-300 hover:bg-gray-100',
          ]"
        >
          {{ pagina }}
        </button>

        <!-- Botão próximo -->
        <button
          @click="paginaAtual++"
          :disabled="paginaAtual === totalPaginas"
          class="px-2 py-1 rounded border border-gray-300 text-gray-600 hover:bg-gray-100 disabled:opacity-50"
        >
          ▶️
        </button>
      </div>
    </div>

    <transition name="slide-fade">
      <div
        v-if="mostrarHistorico"
        class="overflow-x-auto bg-white shadow-xl border border-gray-200"
      >
        <!-- Botão para voltar -->
        <div class="p-4">
          <button
            @click="voltarParaPrincipal"
            class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg shadow mb-4 transition"
          >
            ⬅️ Voltar para a tabela principal
          </button>
        </div>

        <!-- Tabela de Histórico (estrutura similar à principal) -->
        <table class="min-w-full table-auto border-collapse text-sm">
          <thead>
            <tr class="bg-purple-100 text-gray-700 uppercase tracking-wide">
              <th class="px-4 py-3 text-left border border-black">ID</th>
              <th class="px-4 py-3 text-left border border-black">Nome</th>
              <th class="px-4 py-3 text-left border border-black">Datas</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="(item, index) in historicoAgrupado"
              :key="index"
              :class="index % 2 === 0 ? 'bg-purple-50' : 'bg-white'"
              class="hover:bg-purple-100 transition"
            >
              <td class="border border-black px-4 py-2">{{ item.nome }}</td>
              <td class="border border-black px-4 py-2">{{ formatarCPF(item.cpf) }}</td>
              <td class="border border-black px-4 py-2">
                <button
                  @click="abrirModalHistorico(item)"
                  class="bg-purple-600 hover:bg-purple-700 text-white text-xs font-semibold py-1 px-3 rounded"
                >
                  Ver datas
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </transition>

    <!-- Modal -->
    <transition name="fade">
      <div
        v-if="mostrarModal"
        class="fixed inset-0 flex items-center justify-center backdrop-blur-md z-50"
        @click.self="fecharModal()"
      >
        <div
          class="bg-white rounded-2xl p-8 w-full max-w-4xl shadow-2xl relative animate-fade-in"
        >
          <button
            title="Fechar"
            @click="fecharModal()"
            class="absolute top-3 right-3 text-gray-400 hover:text-red-600 text-4xl font-bold"
          >
            &times;
          </button>

          <h2
            class="text-2xl font-semibold mb-6 text-gray-800 border-b pb-2 flex items-center gap-2"
          >
            🔍 Dados do(a) {{ alunoSelecionado.nome }}

            <!-- Ícone de bloqueio (exibido condicionalmente)  v-if="alunoSelecionado.status < 3"-->
            <svg
              v-if="alunoSelecionado.status < 3"
              @click="atualizarStatusAluno(alunoSelecionado.id_respondente, 5)"
              xmlns="http://www.w3.org/2000/svg"
              fill="none"
              viewBox="0 0 24 24"
              stroke-width="1.5"
              stroke="currentColor"
              class="w-6 h-6 text-red-500 hover:text-red-600 cursor-pointer"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M18.364 18.364A9 9 0 0 0 5.636 5.636m12.728 12.728A9 9 0 0 1 5.636 5.636m12.728 12.728L5.636 5.636"
              />
            </svg>

            <!-- Ícone de email -->
            <svg
              v-if="alunoSelecionado.status == 5"
              @click="enviarEmail(5)"
              class="w-6 h-6 text-red-800 dark:text-red-800 cursor-pointer"
              aria-hidden="true"
              xmlns="http://www.w3.org/2000/svg"
              width="24"
              height="24"
              fill="none"
              viewBox="0 0 24 24"
            >
              <path
                stroke="currentColor"
                stroke-linecap="round"
                stroke-width="2"
                d="m3.5 5.5 7.893 6.036a1 1 0 0 0 1.214 0L20.5 5.5M4 19h16a1 1 0 0 0 1-1V6a1 1 0 0 0-1-1H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1Z"
              />
            </svg>
          </h2>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm text-gray-700">
            <div>
              <p
                @click="copiarTexto(alunoSelecionado.id_respondente)"
                class="cursor-pointer hover:text-blue-600"
              >
                <strong>ID:</strong> {{ alunoSelecionado.id_respondente }}
              </p>
              <p
                @click="copiarTexto(alunoSelecionado.tipo)"
                class="cursor-pointer hover:text-blue-600"
              >
                <strong>Tipo:</strong> {{ alunoSelecionado.tipo }}
              </p>
              <p
                @click="copiarTexto(formatarCPF(alunoSelecionado.cpf))"
                class="cursor-pointer hover:text-blue-600"
              >
                <strong>CPF:</strong> {{ formatarCPF(alunoSelecionado.cpf) }}
              </p>
              <p
                @click="copiarTexto(alunoSelecionado.nome)"
                class="cursor-pointer hover:text-blue-600"
              >
                <strong>Nome:</strong> {{ alunoSelecionado.nome }}
              </p>
              <p
                @click="copiarTexto(alunoSelecionado.telefone)"
                class="cursor-pointer hover:text-blue-600"
              >
                <strong>Telefone:</strong> {{ alunoSelecionado.telefone }}
              </p>
              <p
                @click="copiarTexto(alunoSelecionado.email)"
                class="cursor-pointer hover:text-blue-600"
              >
                <strong>Email:</strong> {{ alunoSelecionado.email }}
              </p>
              <p
                v-if="alunoSelecionado.instituicao"
                @click="copiarTexto(alunoSelecionado.instituicao)"
                class="cursor-pointer hover:text-blue-600"
              >
                <strong>Instituição:</strong> {{ alunoSelecionado.instituicao }}
              </p>

              <p>
                <strong>Situação:</strong>
                {{
                  alunoSelecionado.status === 1
                    ? "✅ Aprovado"
                    : alunoSelecionado.status === 2
                    ? "❌ Reprovado"
                    : alunoSelecionado.status === 3
                    ? "✅📧 Aprovado (e-mail enviado)"
                    : alunoSelecionado.status === 4
                    ? "❌📧 Reprovado (e-mail enviado)"
                    : alunoSelecionado.status === 5
                    ? "🚫 Bloqueado"
                    : alunoSelecionado.status === 6
                    ? "🚫📧 Bloqueado (e-mail enviado )"
                    : alunoSelecionado.status === 0
                    ? "⏳ Pendente"
                    : "Status desconhecido"
                }}
              </p>
              <p v-if="alunoSelecionado.baixado_em" style="white-space: nowrap">
                <strong>Email enviado por:</strong> {{ alunoSelecionado.baixado_por }} -
                {{ formatarDateTime(alunoSelecionado.baixado_em) }}
              </p>
            </div>
            <div></div>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mt-2">
            <!-- Foto da Pessoa -->
            <div>
              <div class="flex items-center justify-between mb-2">
                <p class="font-medium text-gray-600">Foto da Pessoa</p>
                <div class="flex gap-2">
                  <!-- 🔽 Botão de download normal -->
                  <button
                    v-if="alunoSelecionado.foto != null"
                    @click="baixarImagem('foto', 'foto')"
                    title="Baixar Foto"
                    class="p-1 rounded-full border border-gray-400 text-gray-500 hover:text-gray-700 hover:border-gray-600 transition-colors"
                  >
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      fill="none"
                      viewBox="0 0 24 24"
                      stroke-width="1.5"
                      stroke="currentColor"
                      class="w-4 h-4"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3"
                      />
                    </svg>
                  </button>

                  <!-- 🟩 Botão de download comprimido (verde) -->
                  <button
                    v-if="alunoSelecionado.foto != null"
                    @click="baixarImagemComprimida('foto', 'foto')"
                    title="Baixar Foto Comprimida"
                    class="p-1 rounded-full border border-green-400 text-green-500 hover:text-green-700 hover:border-green-600 transition-colors"
                  >
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      fill="none"
                      viewBox="0 0 24 24"
                      stroke-width="1.5"
                      stroke="currentColor"
                      class="w-4 h-4"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3"
                      />
                    </svg>
                  </button>
                </div>
              </div>
              <div
                class="w-full h-80 overflow-auto border rounded-lg flex items-center justify-center bg-gray-50"
              >
                <!-- Carregando -->
                <div
                  v-if="loadingFoto"
                  class="w-full h-full flex items-center justify-center"
                >
                  <div role="status">
                    <svg
                      aria-hidden="true"
                      class="w-8 h-8 text-gray-200 animate-spin dark:text-gray-600 fill-blue-600"
                      viewBox="0 0 100 101"
                      fill="none"
                      xmlns="http://www.w3.org/2000/svg"
                    >
                      <path
                        d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                        fill="currentColor"
                      />
                      <path
                        d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                        fill="currentFill"
                      />
                    </svg>
                    <span class="sr-only">Loading...</span>
                  </div>
                </div>

                <!-- Foto carregada com sucesso -->
                <div
                  v-else-if="alunoSelecionado.foto"
                  class="w-full h-full flex items-center justify-center"
                >
                  <img
                    :src="alunoSelecionado.foto"
                    alt="Foto"
                    class="max-w-full max-h-full rounded-lg"
                    @load="fotoCarregada = true"
                  />
                </div>

                <!-- Foto não disponível -->
                <div
                  v-else
                  class="w-full h-full flex items-center justify-center bg-gray-100 text-gray-400 text-center rounded-lg"
                >
                  A FOTO ESTÁ NA PASTA COMPARTILHADA
                </div>
              </div>

              <div class="flex items-center gap-2 mt-3">
                <h1 class="font-bold">As fotos conferem ?</h1>
                <button
                  @click="atualizarStatusAluno(alunoSelecionado.id_respondente, 1)"
                  :disabled="
                    alunoSelecionado.status >= 3 && alunoSelecionado.status !== 5
                  "
                  :class="[
                    'flex items-center gap-2 font-semibold px-4 py-2 rounded-xl shadow',
                    alunoSelecionado.status >= 3 && alunoSelecionado.status !== 5
                      ? 'bg-gray-400 text-white pointer-events-none cursor-not-allowed'
                      : 'bg-green-600 hover:bg-green-700 text-white',
                  ]"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="1.5"
                    stroke="currentColor"
                    class="size-6"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"
                    />
                  </svg>

                  Sim
                </button>
                <button
                  @click="atualizarStatusAluno(alunoSelecionado.id_respondente, 2)"
                  :disabled="
                    alunoSelecionado.status >= 3 && alunoSelecionado.status !== 5
                  "
                  :class="[
                    'flex items-center gap-2 font-semibold px-4 py-2 rounded-xl shadow',
                    alunoSelecionado.status >= 3 && alunoSelecionado.status !== 5
                      ? 'bg-gray-400 text-white pointer-events-none cursor-not-allowed'
                      : 'bg-red-600 hover:bg-red-700 text-white',
                  ]"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="1.5"
                    stroke="currentColor"
                    class="size-6"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"
                    />
                  </svg>

                  Não
                </button>
              </div>
            </div>

            <!-- Foto Identidade -->
            <div>
              <div class="flex items-center justify-between mb-2">
                <p class="font-medium text-gray-600">Foto da Identidade</p>
                <div class="flex gap-2">
                  <!-- 🔽 Botão de download normal -->
                  <button
                    v-if="alunoSelecionado.identidade != null"
                    @click="baixarImagem('identidade', 'doc')"
                    title="Baixar Identidade"
                    class="p-1 rounded-full border border-gray-400 text-gray-500 hover:text-gray-700 hover:border-gray-600 transition-colors"
                  >
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      fill="none"
                      viewBox="0 0 24 24"
                      stroke-width="1.5"
                      stroke="currentColor"
                      class="w-4 h-4"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3"
                      />
                    </svg>
                  </button>

                  <!-- 🟩 Botão de download comprimido (verde) -->
                  <button
                    v-if="alunoSelecionado.identidade != null"
                    @click="baixarImagemComprimida('identidade', 'doc')"
                    title="Baixar Identidade Comprimida"
                    class="p-1 rounded-full border border-green-400 text-green-500 hover:text-green-700 hover:border-green-600 transition-colors"
                  >
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      fill="none"
                      viewBox="0 0 24 24"
                      stroke-width="1.5"
                      stroke="currentColor"
                      class="w-4 h-4"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3"
                      />
                    </svg>
                  </button>
                </div>
              </div>

              <div
                class="w-full h-80 overflow-auto border rounded-lg flex items-center justify-center bg-gray-50"
              >
                <!-- Carregando -->
                <div
                  v-if="loadingIdentidade"
                  class="w-full h-full flex items-center justify-center"
                >
                  <div role="status">
                    <svg
                      aria-hidden="true"
                      class="w-8 h-8 text-gray-200 animate-spin dark:text-gray-600 fill-blue-600"
                      viewBox="0 0 100 101"
                      fill="none"
                      xmlns="http://www.w3.org/2000/svg"
                    >
                      <path
                        d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                        fill="currentColor"
                      />
                      <path
                        d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                        fill="currentFill"
                      />
                    </svg>
                    <span class="sr-only">Loading...</span>
                  </div>
                </div>
                <!-- Identidade carregada com sucesso -->
                <div
                  v-else-if="alunoSelecionado.identidade"
                  class="w-full h-full flex items-center justify-center"
                >
                  <img
                    :src="alunoSelecionado.identidade"
                    alt="Identidade"
                    class="max-w-full max-h-full rounded-lg"
                    @load="identidadeCarregada = true"
                  />
                </div>

                <!-- Identidade não disponível -->
                <div
                  v-else
                  class="w-full h-full flex items-center justify-center bg-gray-100 text-gray-400 text-center rounded-lg"
                >
                  A FOTO ESTÁ NA PASTA COMPARTILHADA
                </div>
              </div>

              <div class="flex items-center gap-2 mt-3">
                <h1 class="font-bold">Enviar:</h1>
                <button
                  @click="enviarEmail(1)"
                  :disabled="alunoSelecionado.status !== 1"
                  :class="[
                    'flex items-center gap-2 font-semibold px-4 py-2 rounded-xl shadow',
                    alunoSelecionado.status !== 1
                      ? 'bg-gray-400 text-white pointer-events-none cursor-not-allowed'
                      : 'bg-green-500 hover:bg-green-600 text-white',
                  ]"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="1.5"
                    stroke="currentColor"
                    class="size-6"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"
                    />
                  </svg>

                  E-Mail Ok
                </button>
                <button
                  @click="enviarEmail(2)"
                  :disabled="alunoSelecionado.status !== 2"
                  :class="[
                    'flex items-center gap-2 font-semibold px-4 py-2 rounded-xl shadow',
                    alunoSelecionado.status !== 2
                      ? 'bg-gray-400 text-white pointer-events-none cursor-not-allowed'
                      : 'bg-red-500 hover:bg-red-600 text-white',
                  ]"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="1.5"
                    stroke="currentColor"
                    class="size-6"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"
                    />
                  </svg>
                </button>
                <button
                  @click="escreverMensagemIrregular()"
                  :disabled="alunoSelecionado.status !== 2"
                  :class="[
                    'flex items-center gap-2 font-semibold px-4 py-2 rounded-xl shadow',
                    alunoSelecionado.status !== 2
                      ? 'bg-gray-400 text-white pointer-events-none cursor-not-allowed'
                      : 'bg-red-500 hover:bg-red-600 text-white',
                  ]"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="1.5"
                    stroke="currentColor"
                    class="size-6"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10"
                    />
                  </svg>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </transition>
  </div>

  <!-- AAAAAAAAAA-->
  <transition name="fade">
    <div
      v-if="mostrarModalHistorico"
      class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-40 backdrop-blur-sm z-50"
      @click.self="mostrarModalHistorico = false"
    >
      <div class="bg-white rounded-2xl p-6 w-full max-w-2xl shadow-2xl">
        <button
          @click="mostrarModalHistorico = false"
          class="absolute top-3 right-3 text-gray-400 hover:text-red-600 text-2xl font-bold"
        >
          &times;
        </button>
        <h2 class="text-xl font-semibold mb-4 text-gray-800">
          🕓 Datas respondidas por {{ alunoHistoricoSelecionado.nome }}
        </h2>
        <ul class="list-disc pl-5 space-y-1 text-gray-700">
          <li v-for="(data, index) in alunoHistoricoSelecionado.datas" :key="index">
            {{ data }}
          </li>
        </ul>
      </div>
    </div>
    <!--<div>

    <button @click="confirmarTipo()">Baixar Excel</button>
    <JsonExcel
      ref="excelExport"
      :data="dados"
      :fields="campos"
      name="tabela-empresas.xlsx"
      type="xlsx"
      sheet-name="Empresas"
      style="display: none"
    />
  </div> -->
  </transition>
</template>

<script>
import Swal from "sweetalert2";
import OrdenacaoSetas from "./OrdenacaoSetas.vue";
import { useToast } from "vue-toastification";
import { JsonExcel } from "vue3-json-excel";

export default {
  components: {
    JsonExcel,
  },
  name: "TabelaCondutores",

  data() {
    return {
      mostrarModal: false,

      // Mnaipulação do dado principal
      alunoSelecionado: {},
      alunos: [],
      paginaAtual: 1,
      itensPorPagina: 50,

      statusSelecionado: "regular_sem_email",
      beneficiarioSelecionado: "todos_benificiarios",
      filtroPesquisa: "", // campo de pesquisa
      dadosStatus: [], // mostrar a situação total atual
      colunaOrdenada: "id", // começa ordenando por ID
      direcaoOrdenacao: "desc", // em descendente

      // Datas
      dataInicial: null,
      dataFinal: null,
      mostraDataFinal: false,
      //primeiraInicializacao: true, Não preciso mais pq to usando a pica de não ter data

      // Loadings
      loadingTabela: true,
      loadingFoto: true,
      loadingIdentidade: true,

      // Historico
      mostrarHistorico: false,
      historico: [],
      historicoAgrupado: [],
      alunoHistoricoSelecionado: null,
      mostrarModalHistorico: false,

      //tokenFoto: null, // <-- salva o token aqui

      //Tabela Excel da rafa (gambiarra do kct)
      dados: [],
      campos: {
        id_respondente: "ID",
        nome: "Nome",
        email: "E-mail",
        cpf: "CPF",
        respostas: "Respostas",
      },
    };
  },
  components: {
    OrdenacaoSetas,
  },

  methods: {
    async confirmarTipo() {
      const result = await Swal.fire({
        title: "Escolha o Tipo",
        text: "Como deseja gerar a tabela?",
        icon: "question",
        showCancelButton: true,
        confirmButtonText: "Crédito Direto",
        cancelButtonText: "Gerenciamento Total",
        reverseButtons: true,
      });

      if (result.isConfirmed) {
        this.montarTabelaEmpresas(1); // Crédito Direto
      } else if (result.dismiss === Swal.DismissReason.cancel) {
        this.montarTabelaEmpresas(2); // Gerenciamento Total
      }
    },

    montarTabelaEmpresas(tipo) {
      this.$axios
        .get("/tabela-excel", {
          params: {
            dataInicial: this.dataInicial,
            tipo: tipo,
          },
        })
        .then((response) => {
          this.dados = response.data.dados;

          // Espera um tick pra garantir que os dados foram atualizados
          this.$nextTick(() => {
            this.$refs.excelExport.generate();
          });
        })
        .catch((error) => {
          console.error("Erro:", error);
        });
    },
    abrirModal(aluno) {
      this.alunoSelecionado = {
        id_respondente: aluno.id_respondente,
        nome: aluno.nome,
        cpf: aluno.cpf,
        email: aluno.email,
        telefone: aluno.telefone,
        status: aluno.status,
        foto: null,
        identidade: null,
        baixado_por: aluno.baixado_por,
        baixado_em: aluno.baixado_em,
        tipo: aluno.tipo,
        instituicao: aluno.instituicao,
      };

      this.mostrarModal = true;

      this.loadingFoto = true;
      this.loadingIdentidade = true;

      const senha = "minhaSenhaFixa123"; // 🔐 sua senha fixa

      this.$axios
        .get(`/foto-por-id/${aluno.id_respondente}`)
        .then((response) => {
          // Evita sobrescrever se o modal já mudou de aluno
          if (this.alunoSelecionado.id_respondente !== aluno.id_respondente) {
            return;
          }

          const foto = response.data.foto;
          const identidade = response.data.identidade;

          this.alunoSelecionado.foto = foto ? `${foto}&senha=${senha}` : null;
          this.alunoSelecionado.identidade = identidade
            ? `${identidade}&senha=${senha}`
            : null;

          this.loadingFoto = false;
          this.loadingIdentidade = false;
        })

        .catch((error) => {
          // Evita mostrar erro se o modal já foi trocado
          if (this.alunoSelecionado.id_respondente !== aluno.id_respondente) {
            return;
          }

          //console.error("Erro ao buscar fotos do aluno:", error);
          //Swal.fire("Erro", "Não foi possível carregar as fotos do aluno.", "error");

          this.loadingFoto = false;
          this.loadingIdentidade = false;
        });
    },

    fecharModal() {
      this.mostrarModal = false;
      this.alunoSelecionado = {};
    },

    alternarDatas() {
      if (this.mostraDataFinal === true) {
        this.mostraDataFinal = false;
        this.dataFinal = null;
        this.puxarDados();
      } else {
        this.mostraDataFinal = true;
        this.dataFinal = null;
      }
    },

    formatarData(dateString) {
      if (!dateString) return "";

      const [year, month, day] = dateString.split("-");
      if (!year || !month || !day) return "";

      return `${day}/${month}/${year}`;
    },

    mudarData(dataTipo, dias) {
      if (!["dataInicial", "dataFinal"].includes(dataTipo)) return;

      // Pega a data atual no campo
      let dataAtualStr = this[dataTipo];

      // Se não existir data, usa hoje
      if (!dataAtualStr) {
        dataAtualStr = new Date().toISOString().substr(0, 10);
      }

      // Cria nova data sem alterar a original
      const partes = dataAtualStr.split("-");
      // Note: mês em JS é 0-indexado
      const dataAtual = new Date(partes[0], partes[1] - 1, partes[2]);

      // Ajusta os dias (dias pode ser positivo ou negativo)
      dataAtual.setDate(dataAtual.getDate() + dias);

      // Formata a data no padrão yyyy-mm-dd
      const ano = dataAtual.getFullYear();
      const mes = String(dataAtual.getMonth() + 1).padStart(2, "0");
      const dia = String(dataAtual.getDate()).padStart(2, "0");

      // Atualiza o estado
      this[dataTipo] = `${ano}-${mes}-${dia}`;
    },

    formatarDateLocal(date) {
      const day = String(date.getDate()).padStart(2, "0");
      const month = String(date.getMonth() + 1).padStart(2, "0"); // mês começa em 0
      const year = date.getFullYear();
      return `${year}-${month}-${day}`;
    },

    formatarDateTime(datetime) {
      const date = new Date(datetime);

      const day = String(date.getDate()).padStart(2, "0");
      const month = String(date.getMonth() + 1).padStart(2, "0"); // mês começa em 0
      const year = date.getFullYear();

      const hours = String(date.getHours()).padStart(2, "0");
      const minutes = String(date.getMinutes()).padStart(2, "0");

      return `${day}/${month}/${year} - ${hours}:${minutes}`;
    },
    formatarCPF(cpf) {
      const numeros = String(cpf).replace(/\D/g, "");

      if (numeros.length !== 11) {
        return cpf; // Retorna como está se não tiver 11 dígitos
      }

      const parte1 = numeros.slice(0, 3);
      const parte2 = numeros.slice(3, 6);
      const parte3 = numeros.slice(6, 9);
      const parte4 = numeros.slice(9, 11);

      return `${parte1}.${parte2}.${parte3}-${parte4}`;
    },
    getStatus() {
      this.$axios
        .get("/resumo-status")
        .then((response) => {
          const categorias = response.data.resumo;
          const totalGeral = response.data.total_registros;

          let texto = categorias
            .map((cat) => {
              // Título azul
              let bloco = `<h3 style="color:blue;">${cat.categoria_nome}</h3>`;

              // Lista de status
              bloco += cat.status
                .map((s) => `<b>${s.descricao}:</b> ${s.quantidade}`)
                .join("<br>");

              // Total da categoria
              bloco += `<br><b>Total da categoria:</b> ${cat.total_categoria}`;

              return bloco;
            })
            .join("<br><hr><br>");

          // Total geral mais embaixo
          texto += `<br><br><hr style="margin-top:20px;"><b>Total geral:</b> ${totalGeral}`;

          Swal.fire({
            title: "Resumo dos Status",
            html: texto,
            icon: "info",
            confirmButtonText: "Fechar",
            width: "600px",
          });
        })
        .catch((error) => {
          console.error("Erro ao buscar status:", error);
          Swal.fire("Erro", "Não foi possível buscar os dados.", "error");
        });
    },

    abrirModalPesquisa() {
      Swal.fire({
        title: "Pesquisar Respondente",
        input: "text",
        inputLabel: "Digite o nome ou CPF",
        showCancelButton: true,
        confirmButtonText: "Pesquisar",
        cancelButtonText: "Cancelar",
        inputValidator: (value) => {
          if (!value) return "Você precisa digitar algo!";
        },
      }).then((result) => {
        if (!result.isConfirmed) return;

        const termo = result.value;

        this.$axios
          .get("/pesquisar", {
            params: { termo },
          })
          .then((response) => {
            const dados = response.data;

            if (!dados.length) {
              Swal.fire("Nenhum resultado encontrado.", "", "info");
              return;
            }

            // Cria a tabela HTML
            const htmlTabela = this.gerarTabelaHtml(dados);

            // Mostra a tabela num novo Swal
            Swal.fire({
              title: `Encontrado(s): ${dados.length}`,
              html: htmlTabela,
              width: "90%",
              scrollbarPadding: false,
              customClass: {
                popup: "swal-tabela",
              },
            });
          })
          .catch(() => {
            Swal.fire("Erro", "Ocorreu um erro ao buscar os dados.", "error");
          });
      });
    },

    getDadoEspecifico(termo) {
      this.$axios
        .get("/pesquisar", {
          params: { termo },
        })
        .then((response) => {
          const dados = response.data;

          if (!dados.length) {
            Swal.fire("Nenhum resultado encontrado.", "", "info");
            return;
          }

          const htmlTabela = this.gerarTabelaHtml(dados);

          Swal.fire({
            title: `Encontrado(s): ${dados.length}`,
            html: htmlTabela,
            width: "90%",
            customClass: { popup: "swal-tabela" },
          });
        })
        .catch(() => {
          Swal.fire("Erro", "Erro ao buscar os dados.", "error");
        });
    },

    gerarTabelaHtml(dados) {
      return `
      <div style="overflow-x:auto; max-height:60vh">
        <table border="1" cellpadding="6" cellspacing="0" style="width:100%; border-collapse:collapse; font-size:14px">
          <thead style="background:#f2f2f2">
            <tr>
              <th>ID</th><th>Nome</th><th>CPF</th><th>Email</th>
              <th>Respondido em</th><th>Resposta em</th><th>Status</th>
            </tr>
          </thead>
          <tbody>
            ${dados
              .map(
                (d) => `
              <tr>
                <td>${d.id_respondente}</td>
                <td>${d.nome}</td>
                <td>${d.cpf}</td>
                <td>${d.email ?? "-"}</td>
                <td>${d.respondido_em ?? "-"}</td>
                <td>${d.baixado_em ?? "-"}</td>
                <td>${this.formatarStatus(d.status)}</td>
              </tr>
            `
              )
              .join("")}
          </tbody>
        </table>
      </div>
    `;
    },

    formatarStatus(status) {
      switch (status) {
        case 0:
          return "Pendente";
        case 1:
          return "OK";
        case 2:
          return "Irregular";
        case 3:
          return "OK + Email";
        case 4:
          return "Irregular + Email";
        case 5:
          return "Bloqueado";
        case 6:
          return "Bloqueado + Email";
        default:
          return "Desconhecido";
      }
    },

    puxarDados() {
      this.loadingTabela = true;
      this.$axios
        .get("/todos", {
          params: {
            dataInicial: this.dataInicial,
            dataFinal: this.dataFinal,
          },
        })
        .then((response) => {
          this.loadingTabela = false;
          this.alunos = response.data.dados;
          this.dataInicial = response.data.dataUsada; // Atualiza a data no front
          console.log(this.alunos);

          // Aplica cores fixas logo após receber os dados
          this.alunos = this.atribuirCoresFixas(response.data.dados);
        })
        .catch((error) => {
          console.error("Erro:", error);
        });
    },
    buscarDados() {
      this.puxarDados();
      this.statusSelecionado = "todos";
      this.filtroPesquisa = "";
      this.paginaAtual = 1;
    },

    atualizarStatusAluno(id, status) {
      this.$axios
        .patch("/atualizar-status", { id, status })
        .then(() => {
          this.alunoSelecionado.status = status;
          //this.puxarDados();

          Swal.fire({
            icon: "success",
            title: "Atualizado!",
            text: "Status do aluno atualizado com sucesso.",
            timer: 2000,
            showConfirmButton: false,
          });
        })
        .catch((error) => {
          console.error("Erro ao atualizar status:", error);

          Swal.fire({
            icon: "error",
            title: "Erro",
            text: "Falha ao atualizar status. Tente novamente.",
          });
        });
    },

    async escreverMensagemIrregular() {
      const { value: texto } = await Swal.fire({
        title: "Escreva a mensagem para o email irregular",
        input: "textarea",
        inputLabel: "Mensagem",
        inputPlaceholder:
          "Digite aqui a mensagem personalizada que aparecerá no e-mail...",
        inputAttributes: {
          "aria-label": "Digite sua mensagem personalizada",
        },
        showCancelButton: true,
        confirmButtonText: "Enviar",
        cancelButtonText: "Cancelar",
        reverseButtons: true,
        inputValidator: (value) => {
          if (!value) {
            return "Você precisa escrever alguma mensagem antes de enviar.";
          }
        },
      });

      if (texto) {
        this.enviarEmail(20, texto); // envia com texto extra
      }
    },

    enviarEmail(e, textoCustomizado = null) {
      const { id_respondente, nome, email } = this.alunoSelecionado;

      if (!email) {
        Swal.fire("Erro", "E-mail não encontrado.", "error");
        return;
      }

      let rota = "";
      let novoStatus = null;
      let mensagemConfirmacao = "";

      if (e === 1) {
        rota = "/enviar-email-regular";
        novoStatus = 3;
        mensagemConfirmacao = `Deseja enviar o e-mail de confirmação para ${nome}?`;
      } else if (e === 2) {
        rota = "/enviar-email";
        novoStatus = 4;
        mensagemConfirmacao = `Deseja enviar o e-mail de erro para ${nome}?`;
      } else if (e === 20) {
        rota = "/enviar-email-com-texto";
        novoStatus = 4;
        // Sem confirmação extra aqui
      } else if (e === 5) {
        rota = "/enviar-email-bloqueado";
        novoStatus = 6;
        mensagemConfirmacao = `Deseja enviar o e-mail de "bloqueio" para ${nome}?`;
      } else {
        Swal.fire("Erro", "Tipo de e-mail desconhecido.", "error");
        return;
      }

      // 🔁 Se for e === 20, envia direto
      if (e === 20) {
        const payload = {
          id: id_respondente,
          nome,
          email,
          texto_extra: textoCustomizado,
        };

        this.$axios
          .patch(rota, payload)
          .then(() => {
            this.alunoSelecionado.status = novoStatus;
            Swal.fire("Sucesso", "E-mail enviado com sucesso!", "success").then(() => {
              this.puxarDados();
            });
          })
          .catch((error) => {
            console.error(error);
            Swal.fire("Erro", "Não foi possível enviar o e-mail.", "error");
          });

        return; // 🧼 Encerra aqui
      }

      // 🔁 Para os outros casos, segue com a confirmação
      Swal.fire({
        title: "Confirmar envio",
        text: mensagemConfirmacao,
        icon: "question",
        showCancelButton: true,
        confirmButtonText: "Sim, enviar",
        cancelButtonText: "Cancelar",
      }).then((result) => {
        if (result.isConfirmed) {
          this.$axios
            .patch(rota, {
              id: id_respondente,
              nome,
              email,
            })
            .then(() => {
              this.alunoSelecionado.status = novoStatus;
              Swal.fire("Sucesso", "E-mail enviado com sucesso!", "success").then(() => {
                this.puxarDados();
              });
            })
            .catch((error) => {
              console.error(error);
              Swal.fire("Erro", "Não foi possível enviar o e-mail.", "error");
            });
        }
      });
    },

    baixarImagem(tipo, nomeFoto) {
      const dataUrl = this.alunoSelecionado[tipo];
      if (!dataUrl) return;

      // Extrair o tipo MIME (ex: image/jpeg)
      const mime = dataUrl.match(/data:(image\/[a-zA-Z]+);base64,/);
      const ext = mime ? mime[1].split("/")[1] : "jpg";

      // Limpar CPF para ficar só números (opcional)
      const cpfLimpo = this.alunoSelecionado.cpf.replace(/\D/g, "");

      // Montar nome do arquivo
      const nomeArquivo = `${cpfLimpo}_${nomeFoto}_${this.alunoSelecionado.id_respondente}.${ext}`;

      // Criar link com href data URI e forçar download
      const link = document.createElement("a");
      link.href = dataUrl;
      link.download = nomeArquivo;

      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
    },
    async baixarImagemComprimida(tipo, nomeFoto) {
      const dataUrl = this.alunoSelecionado[tipo];
      if (!dataUrl) return;

      const imagem = await this.carregarImagem(dataUrl);
      const base64Comprimido = this.comprimirImagem(imagem, 0.7); // 70% de qualidade

      // Extrair MIME
      const mime = base64Comprimido.match(/data:(image\/[a-zA-Z]+);base64,/);
      const ext = mime ? mime[1].split("/")[1] : "jpg";

      //const cpfLimpo = this.alunoSelecionado.cpf.replace(/\D/g, "");
      const nomeArquivo = `${nomeFoto}_${this.alunoSelecionado.id_respondente}.${ext}`;

      const link = document.createElement("a");
      link.href = base64Comprimido;
      link.download = nomeArquivo;
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
    },

    carregarImagem(base64) {
      return new Promise((resolve, reject) => {
        const img = new Image();
        img.onload = () => resolve(img);
        img.onerror = (e) => reject(e);
        img.src = base64;
      });
    },

    comprimirImagem(imagem, qualidade = 0.7) {
      const canvas = document.createElement("canvas");
      const maxLado = 2000; // 🔥 opcional: limitar resolução

      let { width, height } = imagem;

      // 🔧 (opcional) Reduz dimensões se forem muito grandes
      if (width > maxLado || height > maxLado) {
        const proporcao = Math.min(maxLado / width, maxLado / height);
        width = width * proporcao;
        height = height * proporcao;
      }

      canvas.width = width;
      canvas.height = height;

      const ctx = canvas.getContext("2d");
      ctx.drawImage(imagem, 0, 0, width, height);

      // 🔥 Salva como JPEG, que é mais eficiente que PNG
      return canvas.toDataURL("image/jpeg", qualidade);
    },
    copiarTexto(texto) {
      const toast = useToast();
      navigator.clipboard
        .writeText(texto)
        .then(() => {
          toast.info(`Texto Copiado: ${texto}`);
        })
        .catch(() => {
          toast.error("Erro ao copiar texto");
        });
    },

    async carregarHistorico() {
      try {
        const response = await this.$axios.get("/historico");
        const dados = response.data;

        // Agrupar por CPF (ou Nome + CPF, se necessário)
        const agrupado = {};

        dados.forEach((item) => {
          const chave = `${item.nome}-${item.cpf}`;
          if (!agrupado[chave]) {
            agrupado[chave] = {
              id_respondente: item.id_respondente,
              nome: item.nome,
              cpf: item.cpf,
              datas: [],
            };
          }
          agrupado[chave].datas.push(item.respondido_em);
        });

        // Transforma em array para exibição na tabela
        this.historicoAgrupado = Object.values(agrupado);

        // Exibe a tabela
        this.mostrarHistorico = true;
      } catch (error) {
        console.error("Erro ao carregar histórico:", error);
      }
    },

    abrirModalHistorico(item) {
      this.alunoHistoricoSelecionado = item;
      this.mostrarModalHistorico = true;
    },
    voltarParaPrincipal() {
      this.mostrarHistorico = false;
    },
    atribuirCoresFixas(alunos) {
      const cores = [
        "bg-blue-300",
        "bg-green-300",
        "bg-pink-200",
        "bg-yellow-200",
        "bg-sky-200",
        "bg-purple-300",
        "bg-orange-200",
        "bg-gray-300",
      ];

      const validos = alunos.filter((a) => ![2, 4, 5, 6].includes(a.status));
      const tamanhoGrupo = Math.ceil(validos.length / cores.length);

      let validIndex = 0;

      return alunos.map((aluno) => {
        if ([2, 4, 5, 6].includes(aluno.status)) {
          return { ...aluno, cor: "" }; // sem cor
        } else {
          const grupo = Math.floor(validIndex / tamanhoGrupo);
          validIndex++;
          return { ...aluno, cor: cores[grupo] || "" };
        }
      });
    },
  },
  mounted() {
    this.puxarDados();
  },
  computed: {
    alunosOrdenados() {
      let resultado = this.alunos;

      // 🔍 Filtro por status
      if (this.statusSelecionado !== "todos") {
        resultado = resultado.filter((aluno) => {
          const status = parseInt(aluno.status); // <-- força ser número

          const mapaStatus = {
            pendente: [0],
            //regular: [1, 3],
            regular_com_email: [3],
            regular_sem_email: [1],
            //irregular: [2, 4],
            irregular_com_email: [4],
            irregular_sem_email: [2],
            bloqueado_sem_email: [5],
            bloqueado_com_email: [6],
          };

          const filtros = mapaStatus[this.statusSelecionado];

          if (!filtros) return true; // Se não existir no mapa, não filtra

          return filtros.includes(status);
        });
      }

      // 🔍 Filtro por beneficiário (tipo)
      if (this.beneficiarioSelecionado !== "todos_benificiarios") {
        resultado = resultado.filter((aluno) => {
          return aluno.tipo === this.beneficiarioSelecionado;
        });
      }

      // 🔍 Filtro por texto (id, nome, cpf, respondido_em)
      if (this.filtroPesquisa && this.filtroPesquisa.trim() !== "") {
        const busca = this.filtroPesquisa.trim().toLowerCase();
        resultado = resultado.filter(
          (aluno) =>
            aluno.id_respondente?.toString().includes(busca) ||
            aluno.nome?.toLowerCase().includes(busca) ||
            aluno.cpf?.toString().includes(busca) ||
            aluno.respondido_em?.toString().includes(busca)
        );
      }

      // 🔃 Ordenação
      if (!this.direcaoOrdenacao || !this.colunaOrdenada) return resultado;

      return resultado.sort((a, b) => {
        let va = a[this.colunaOrdenada];
        let vb = b[this.colunaOrdenada];

        // 🛠️ Converte para número se for um campo numérico
        const camposNumericos = ["id", "id_respondente", "status"];
        if (camposNumericos.includes(this.colunaOrdenada)) {
          va = parseInt(va);
          vb = parseInt(vb);
        }

        if (va === vb) return 0;

        const ord = va > vb ? 1 : -1;
        return this.direcaoOrdenacao === "asc" ? ord : -ord;
      });
    },

    alunosPaginados() {
      const inicio = (this.paginaAtual - 1) * this.itensPorPagina;
      const fim = inicio + this.itensPorPagina;
      return this.alunosOrdenados.slice(inicio, fim);
    },

    indiceInicio() {
      return (this.paginaAtual - 1) * this.itensPorPagina;
    },
    indiceFim() {
      let fim = this.paginaAtual * this.itensPorPagina;
      if (fim > this.alunosOrdenados.length) {
        fim = this.alunosOrdenados.length;
      }
      return fim;
    },
    totalPaginas() {
      return Math.ceil(this.alunosOrdenados.length / this.itensPorPagina) || 1;
    },
    paginasVisiveis() {
      const total = this.totalPaginas;
      const atual = this.paginaAtual;
      let inicio = Math.max(1, atual - 2);
      let fim = inicio + 4;

      if (fim > total) {
        fim = total;
        inicio = Math.max(1, fim - 4);
      }

      const paginas = [];
      for (let i = inicio; i <= fim; i++) {
        paginas.push(i);
      }
      return paginas;
    },
  },
  watch: {
    filtroPesquisa() {
      this.paginaAtual = 1;
    },
    statusSelecionado() {
      this.paginaAtual = 1;
    },
  },
};
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

.slide-fade-enter-active {
  transition: all 0.3s ease-out;
}

.slide-fade-leave-active {
  transition: all 0.3s ease-in;
}

.slide-fade-enter-from,
.slide-fade-leave-to {
  transform: translateX(20px);
  opacity: 0;
}

button,
a,
[role="button"],
input[type="button"],
input[type="submit"],
input[type="reset"] {
  cursor: pointer;
}
</style>
