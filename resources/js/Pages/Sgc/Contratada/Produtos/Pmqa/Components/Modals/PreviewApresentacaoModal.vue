<script setup>
import { ref, watch, onUnmounted } from 'vue'
import InputLabel from '@/Components/InputLabel.vue'
import { useForm, usePage } from '@inertiajs/vue3'
import { IconCircleCheck, IconX } from '@tabler/icons-vue'

const props = defineProps({
  contrato: [String, Number],
  produto: String,
})

const emit = defineEmits(['aprovado', 'reprovado'])

const aberto = ref(false)
const dados = ref(null)
const motivoReprovacao = ref('')
const mostrarMotivo = ref(false)

const abrirModal = (payload) => {
  dados.value = payload
  aberto.value = true
  motivoReprovacao.value = ''
  mostrarMotivo.value = false
}

const fechar = () => {
  aberto.value = false
}

const formAprovar = useForm({})
const formReprovar = useForm({ motivo: '' })

const aprovar = () => {
  formAprovar.patch(
    route('sgc.contratada.produtos.pmqa.aprovar', [
      props.contrato,
      props.produto,
      dados.value.id,
    ]),
    {
      preserveScroll: true,
      onSuccess: () => {
        dados.value.status_aprovacao = 'Em elaboracao'
        emit('aprovado', dados.value.id)
        fechar()
      },
    }
  )
}

const reprovar = () => {
  if (!mostrarMotivo.value) {
    mostrarMotivo.value = true
    return
  }

  formReprovar.motivo = motivoReprovacao.value
  formReprovar.patch(
    route('sgc.contratada.produtos.pmqa.reprovar', [
      props.contrato,
      props.produto,
      dados.value.id,
    ]),
    {
      preserveScroll: true,
      onSuccess: () => {
        dados.value.status_aprovacao = 'Reprovado'
        emit('reprovado', dados.value.id)
        fechar()
      },
    }
  )
}

watch(aberto, (valor) => {
  document.body.classList.toggle('modal-open', valor)
})

onUnmounted(() => {
  document.body.classList.remove('modal-open')
})

defineExpose({ abrirModal })
</script>

<template>
  <div v-if="aberto" class="modal-backdrop fade show"></div>

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

          <!-- Badge de status -->
          <span
            v-if="dados"
            class="badge ms-3"
            :class="{
              'bg-success': dados.status_aprovacao === 'Em elaboracao',
              'bg-danger':  dados.status_aprovacao === 'Reprovado',
              'bg-warning text-dark': dados.status_aprovacao === 'Em analise',
            }"
          >
            {{ dados.status_aprovacao ?? 'Rascunho' }}
          </span>

          <button class="btn-close ms-auto" @click="fechar"></button>
        </div>

        <div class="modal-body" v-if="dados">
          <div class="row mb-4">
            <div class="col-md-6">
              <InputLabel value="Tema" />
              <input class="form-control" :value="dados.tema ?? '—'" disabled />
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

          <!-- Campo de motivo de reprovação — aparece ao clicar em Reprovar -->
          <div v-if="mostrarMotivo" class="mt-3">
            <InputLabel value="Motivo da reprovação" />
            <textarea
              class="form-control border-danger"
              rows="3"
              v-model="motivoReprovacao"
              placeholder="Descreva o motivo da reprovação..."
            />
          </div>
        </div>

        <div class="modal-footer">
          <!-- Aprovar: só aparece se status for Em analise -->
          <button
            v-if="dados?.status_aprovacao === 'Em analise'"
            class="btn btn-success"
            :disabled="formAprovar.processing"
            @click="aprovar"
          >
            <IconCircleCheck class="me-2" />
            Aprovar
          </button>

          <!-- Reprovar: primeiro clique mostra o campo, segundo confirma -->
          <button
            v-if="dados?.status_aprovacao === 'Em analise'"
            class="btn btn-danger"
            :disabled="formReprovar.processing"
            @click="reprovar"
          >
            <IconX class="me-2" />
            {{ mostrarMotivo ? 'Confirmar reprovação' : 'Reprovar' }}
          </button>
        </div>

      </div>
    </div>
  </div>
</template>

<style scoped>
.modal { z-index: 1055; }
.modal-backdrop {
  position: fixed;
  inset: 0;
  background-color: rgba(0, 0, 0, 0.5);
  z-index: 1050;
}
</style>
