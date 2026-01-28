<script setup>
import { ref, watch, onUnmounted } from 'vue'
import InputLabel from '@/Components/InputLabel.vue'

const aberto = ref(false)
const dados = ref(null)

const abrirModal = (payload) => {
  dados.value = payload
  aberto.value = true
}

const fechar = () => {
  aberto.value = false
}

watch(aberto, (valor) => {
  if (valor) {
    document.body.classList.add('modal-open')
  } else {
    document.body.classList.remove('modal-open')
  }
})

onUnmounted(() => {
  document.body.classList.remove('modal-open')
})

defineExpose({ abrirModal })
</script>

<template>
  <div
    v-if="aberto"
    class="modal-backdrop fade show"
  ></div>

  <div
    v-if="aberto"
    class="modal fade show d-block"
    tabindex="-1"
    @click.self="fechar"
  >
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
      <div class="modal-content">

        <div class="modal-header">
          <h5 class="modal-title">Visualização – Apresentação</h5>
          <button class="btn-close" @click="fechar"></button>
        </div>

        <div class="modal-body" v-if="dados">

          <div class="row mb-4">
            <div class="col-md-6">
              <InputLabel value="Tema" />
              <input class="form-control" :value="dados.tema ?? '—'" disabled/>
            </div>

            <div class="col-md-6">
              <InputLabel value="Código de Empreendimento" />
              <input class="form-control" :value="dados.cod_emp ?? '—'" disabled />
            </div>
          </div>

          <div class="mb-4">
            <InputLabel value="Especificação" />
            <textarea class="form-control" rows="4" :value="dados.especificacao ?? ''" disabled />
          </div>

          <div class="mb-4">
            <InputLabel value="Introdução" />
            <textarea class="form-control" rows="4" :value="dados.introducao ?? ''" disabled />
          </div>

          <div class="mb-4">
            <InputLabel value="Justificativa" />
            <textarea class="form-control" rows="4" :value="dados.justificativa ?? ''" disabled />
          </div>

          <div class="mb-4">
            <InputLabel value="Objetivos" />
            <textarea class="form-control" rows="4" :value="dados.objetivos ?? ''" disabled />
          </div>

          <div class="mb-4">
            <InputLabel value="Metodologia" />
            <textarea class="form-control" rows="4" :value="dados.metodologia ?? ''" disabled />
          </div>

          <div class="mb-3">
            <InputLabel value="Público-alvo" />
            <textarea class="form-control" rows="3" :value="dados.publico_alvo ?? ''" disabled />
          </div>
        </div>

        <div class="modal-footer">
          <button class="btn btn-secondary" @click="fechar">
            Fechar
          </button>
        </div>

      </div>
    </div>
  </div>
</template>

<style scoped>
.modal {
  z-index: 1055;
}

.modal-backdrop {
  position: fixed;
  inset: 0;
  background-color: rgba(0, 0, 0, 0.5);
  z-index: 1050;
}

body.modal-open {
  overflow: hidden;
}
</style>
