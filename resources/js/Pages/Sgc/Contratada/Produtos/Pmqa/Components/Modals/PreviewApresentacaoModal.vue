<script setup>
import { computed, ref, watch, onUnmounted } from 'vue'
import InputLabel from '@/Components/InputLabel.vue'
import { useForm } from '@inertiajs/vue3'
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

const resumo = computed(() => dados.value?.vinculacoesResumo ?? {})
const configuracao = computed(() => dados.value?.configuracao ?? {})
const listas = computed(() => configuracao.value?.listas ?? [])
const pontosSemLista = computed(() => configuracao.value?.pontos_sem_lista ?? [])
const mostraConfiguracao = computed(() => {
  return (resumo.value.total_listas ?? 0) > 0 ||
    (resumo.value.total_pontos ?? 0) > 0 ||
    (resumo.value.total_pontos_vinculados ?? 0) > 0 ||
    listas.value.length > 0 ||
    pontosSemLista.value.length > 0
})
const tituloModal = computed(() =>
  mostraConfiguracao.value
    ? 'Visualização - Configuração PMQA'
    : 'Visualização - Apresentação'
)

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
        dados.value.status_aprovacao = 'Em elaboração'
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
        dados.value.status_aprovacao = 'Rejeitada'
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
          <h5 class="modal-title">{{ tituloModal }}</h5>

          <!-- Badge de status -->
          <span
            v-if="dados"
            class="badge ms-3"
            :class="{
              'bg-success': dados.status_aprovacao === 'Em elaboração',
              'bg-danger':  dados.status_aprovacao === 'Rejeitada',
              'bg-warning text-dark': dados.status_aprovacao === 'Em análise',
            }"
          >
            {{ dados.status_aprovacao ?? 'Rascunho' }}
          </span>

          <button class="btn-close ms-auto" @click="fechar"></button>
        </div>

        <div class="modal-body" v-if="dados">
          <template v-if="!mostraConfiguracao">
            <div class="row mb-4">
              <div class="col-md-6">
                <InputLabel value="Tema" />
                <input class="form-control" :value="dados.tema ?? '-'" disabled />
              </div>
              <div class="col-md-6">
                <InputLabel value="Código de Empreendimento" />
                <input class="form-control" :value="dados.cod_emp ?? '-'" disabled />
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
          </template>

          <template v-else>
          <div class="row g-3 mb-4">
            <div class="col-md-4">
              <div class="border rounded p-3 h-100">
                <div class="text-muted small">Listas de parâmetros</div>
                <div class="fs-3 fw-bold">{{ resumo.total_listas ?? listas.length }}</div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="border rounded p-3 h-100">
                <div class="text-muted small">Pontos cadastrados</div>
                <div class="fs-3 fw-bold">{{ resumo.total_pontos ?? 0 }}</div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="border rounded p-3 h-100">
                <div class="text-muted small">Pontos vinculados</div>
                <div class="fs-3 fw-bold">{{ resumo.total_pontos_vinculados ?? 0 }}</div>
              </div>
            </div>
          </div>

          <div v-if="!listas.length" class="alert alert-warning">
            Nenhuma lista de parâmetros foi configurada para este PMQA.
          </div>

          <div v-for="lista in listas" :key="lista.id" class="mb-4 border rounded">
            <div class="d-flex align-items-center justify-content-between border-bottom p-3">
              <div>
                <h6 class="mb-1">{{ lista.nome }}</h6>
                <span class="text-muted small">
                  {{ lista.pontos?.length ?? 0 }} ponto(s) vinculado(s)
                </span>
              </div>
              <span class="badge" :class="lista.medir_iqa ? 'bg-info' : 'bg-secondary'">
                {{ lista.medir_iqa ? 'Mede IQA' : 'Não mede IQA' }}
              </span>
            </div>

            <div class="p-3 border-bottom">
              <div class="text-muted small mb-2">Parâmetros</div>
              <div v-if="lista.parametros?.length" class="d-flex flex-wrap gap-2">
                <span
                  v-for="parametro in lista.parametros"
                  :key="parametro.id"
                  class="badge bg-light text-dark border"
                >
                  {{ parametro.nome }}
                </span>
              </div>
              <span v-else class="text-muted">Nenhum parâmetro vinculado.</span>
            </div>

            <div class="table-responsive">
              <table class="table table-bordered mb-0">
                <thead>
                  <tr>
                    <th class="text-center">Ponto</th>
                    <th>Nome</th>
                    <th class="text-center">Classe</th>
                    <th class="text-center">Ambiente</th>
                    <th class="text-center">UF</th>
                    <th>Município</th>
                    <th>Bacia hidrográfica</th>
                    <th class="text-center">Km</th>
                    <th class="text-center">Estaca</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="ponto in lista.pontos" :key="ponto.id">
                    <td class="text-center">{{ ponto.id }}</td>
                    <td>{{ ponto.nome_ponto_coleta ?? '-' }}</td>
                    <td class="text-center">{{ ponto.classe ?? '-' }}</td>
                    <td class="text-center">{{ ponto.tipo_ambiente ?? '-' }}</td>
                    <td class="text-center">{{ ponto.uf ?? '-' }}</td>
                    <td>{{ ponto.municipio ?? '-' }}</td>
                    <td>{{ ponto.bacia_hidrografica ?? '-' }}</td>
                    <td class="text-center">{{ ponto.km_rodovia ?? '-' }}</td>
                    <td class="text-center">{{ ponto.estaca ?? '-' }}</td>
                  </tr>
                  <tr v-if="!lista.pontos?.length">
                    <td colspan="9" class="text-center text-muted">
                      Nenhum ponto vinculado a esta lista.
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <div v-if="pontosSemLista.length" class="mt-4">
            <h6>Pontos sem lista vinculada</h6>
            <div class="table-responsive">
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th class="text-center">Ponto</th>
                    <th>Nome</th>
                    <th class="text-center">UF</th>
                    <th>Município</th>
                    <th class="text-center">Km</th>
                    <th class="text-center">Estaca</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="ponto in pontosSemLista" :key="ponto.id">
                    <td class="text-center">{{ ponto.id }}</td>
                    <td>{{ ponto.nome_ponto_coleta ?? '-' }}</td>
                    <td class="text-center">{{ ponto.uf ?? '-' }}</td>
                    <td>{{ ponto.municipio ?? '-' }}</td>
                    <td class="text-center">{{ ponto.km_rodovia ?? '-' }}</td>
                    <td class="text-center">{{ ponto.estaca ?? '-' }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
          </template>

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
          <!-- Aprovar: só aparece se status for Em análise -->
          <button
            v-if="dados?.status_aprovacao === 'Em análise'"
            class="btn btn-success"
            :disabled="formAprovar.processing"
            @click="aprovar"
          >
            <IconCircleCheck class="me-2" />
            Aprovar
          </button>

          <!-- Reprovar: primeiro clique mostra o campo, segundo confirma -->
          <button
            v-if="dados?.status_aprovacao === 'Em análise'"
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
