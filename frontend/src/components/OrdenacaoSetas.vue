<template>
  <div
    class="flex flex-col items-end cursor-pointer leading-none text-[0px]"
    @click="trocarOrdenacao"
  >
    <!-- Seta Cima Cheia (ativo asc) -->
    <svg
      v-if="ativoAsc"
      class="w-5 h-5 text-black -mb-2"
      viewBox="0 0 24 24"
      fill="currentColor"
    >
      <path
        fill-rule="evenodd"
        d="M5.575 13.729C4.501 15.033 5.43 17 7.12 17h9.762c1.69 0 2.618-1.967
           1.544-3.271l-4.881-5.927a2 2 0 0 0-3.088 0l-4.88 5.927Z"
        clip-rule="evenodd"
      />
    </svg>
    <svg
      v-else
      class="w-5 h-5 text-black -mb-2"
      viewBox="0 0 24 24"
      fill="none"
      xmlns="http://www.w3.org/2000/svg"
    >
      <path
        stroke="currentColor"
        stroke-width="1.5"
        stroke-linecap="round"
        stroke-linejoin="round"
        d="M16.881 16H7.119a1 1 0 0 1-.772-1.636l4.881-5.927
           a1 1 0 0 1 1.544 0l4.88 5.927a1 1 0 0 1-.77 1.636Z"
      />
    </svg>

    <!-- Seta Baixo Cheia (ativo desc) -->
    <svg
      v-if="ativoDesc"
      class="w-5 h-5 text-black"
      viewBox="0 0 24 24"
      fill="currentColor"
    >
      <path
        fill-rule="evenodd"
        d="M18.425 10.271C19.499 8.967 18.57 7 16.88 7H7.12
           c-1.69 0-2.618 1.967-1.544 3.271l4.881 5.927a2 2
           0 0 0 3.088 0l4.88-5.927Z"
        clip-rule="evenodd"
      />
    </svg>
    <svg
      v-else
      class="w-5 h-5 text-black"
      viewBox="0 0 24 24"
      fill="none"
      xmlns="http://www.w3.org/2000/svg"
    >
      <path
        stroke="currentColor"
        stroke-width="1.5"
        stroke-linecap="round"
        stroke-linejoin="round"
        d="M7.119 8h9.762a1 1 0 0 1 .772 1.636l-4.881 5.927
           a1 1 0 0 1-1.544 0l-4.88-5.927A1 1 0 0 1 7.118 8Z"
      />
    </svg>
  </div>
</template>

<script>
export default {
  name: 'OrdenacaoSetas',
  props: {
    coluna:      { type: String, required: true },
    colunaAtiva: { type: String, default: null },
    direcao:     { type: String, default: null, validator: v => ['asc', 'desc', null].includes(v) },
  },
  computed: {
    ativoAsc() {
      return this.coluna === this.colunaAtiva && this.direcao === 'asc';
    },
    ativoDesc() {
      return this.coluna === this.colunaAtiva && this.direcao === 'desc';
    },
  },
  methods: {
    trocarOrdenacao() {
      let novaDirecao;
      if (this.coluna !== this.colunaAtiva) {
        novaDirecao = 'asc';
      } else {
        novaDirecao = this.direcao === 'asc' ? 'desc' : 'asc';
      }

      this.$emit('update:colunaAtiva', this.coluna);
      this.$emit('update:direcao', novaDirecao);
    }
  }
}
</script>
