<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import NavbarContrato from '@/Pages/Sgc/Contratada/NavbarContrato.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import { computed, ref } from 'vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
  campanha: { type: Object, default: () => ({}) },
  contrato: [Number, String],
  produto: { type: String, default: 'espeleologia' },
  contratos: { type: Object, default: () => ({ contratada: 'Contratada', tipo_contrato: null }) },
  canApprove: { type: Boolean, default: false },
  analises: { type: Array, default: () => [] },
});

const form = useForm({
  observacoes: '',
});

const showRejectForm = ref(false);
const showAnalysisModal = ref(false);

const statusClass = computed(() => ({
  'bg-warning text-white': props.campanha.status === 'Em análise',
  'bg-success text-white': props.campanha.status === 'Aprovada',
  'bg-danger text-white': props.campanha.status === 'Reprovada' || props.campanha.status === 'Rejeitada',
  'bg-secondary text-white': props.campanha.status === 'Em elaboração',
}));

const anexos = computed(() => Array.isArray(props.campanha.anexos) ? props.campanha.anexos : []);
const resultados = computed(() => Array.isArray(props.campanha.resultados_anexos) ? props.campanha.resultados_anexos : []);

const aprovar = () => {
  form.post(route('sgc.contratada.produtos.espeleo.aprovarTudo', [props.contrato, props.produto, props.campanha.id]), {
    onFinish: () => form.reset(),
  });
};

const reprovar = () => {
  if (!form.observacoes || form.observacoes.trim().length < 10) {
    alert('A justificativa deve ter no minimo 10 caracteres.');
    return;
  }

  form.post(route('sgc.contratada.produtos.espeleo.reprovarTudo', [props.contrato, props.produto, props.campanha.id]), {
    onFinish: () => {
      form.reset();
      showRejectForm.value = false;
    },
  });
};
</script>

<template>
  <AuthenticatedLayout>
    <Breadcrumb
      :links="[
        { route: route('sgc.gestao.listagem', props.contratos.tipo_contrato), label: 'Gestao de Contratos' },
        { route: route('sgc.contratada.produtos.index', [props.contrato, props.produto]), label: props.contratos.contratada || 'Contratada' },
        { route: '#', label: `Analisar Campanha ${props.campanha.id_campanha || props.campanha.id}` },
      ]"
    />

    <NavbarContrato :tipo="{ id: props.contrato }">
      <template #body>
        <div class="card">
          <div class="card-body">
            <div v-if="props.analises && props.analises.length > 0" class="alert alert-info mb-3 d-flex justify-content-between align-items-center" style="cursor: pointer;" @click="showAnalysisModal = true">
              <span>
                <i class="bi bi-info-circle me-2"></i>
                {{ props.analises.length }} análise{{ props.analises.length !== 1 ? 's' : '' }} anterior{{ props.analises.length !== 1 ? 'es' : '' }}
              </span>
              <span class="badge bg-info">Clique para visualizar</span>
            </div>

            <div class="text-center mb-4">
              <h2 class="mb-2">ANÁLISE CAMPANHA ESPELEOLOGIA</h2>
              <p class="text-muted mb-3 fs-5">{{ props.campanha.subproduto || 'Subproduto não informado' }}</p>
              <div class="d-inline-block">
                <span class="badge fs-4 px-3 py-2 fw-bold text-white" :class="statusClass">{{ props.campanha.status || 'N/A' }}</span>
              </div>
            </div>

            <div class="row g-3 mb-4">
              <div class="col-md-3">
                <div class="info-box">
                  <span>Campanha</span>
                  <strong>{{ props.campanha.id_campanha || 'N/A' }}</strong>
                </div>
              </div>
              <div class="col-md-3">
                <div class="info-box">
                  <span>Empreendimento</span>
                  <strong>{{ props.campanha.cod_emp || 'N/A' }}</strong>
                </div>
              </div>
              <div class="col-md-3">
                <div class="info-box">
                  <span>Subtrecho</span>
                  <strong>{{ props.campanha.subtrecho || 'N/A' }}</strong>
                </div>
              </div>
              <div class="col-md-3">
                <div class="info-box">
                  <span>Segmento</span>
                  <strong>{{ props.campanha.segmento || 'N/A' }}</strong>
                </div>
              </div>
            </div>

            <section class="mb-4">
              <h4 class="section-title">Metodologia</h4>
              <div class="content-box">
                {{ props.campanha.metodologia || 'Não informada' }}
              </div>
            </section>

            <section class="mb-4">
              <h4 class="section-title">Resultados Anexos</h4>
              <div v-if="resultados.length" class="list-group">
                <a
                  v-for="resultado in resultados"
                  :key="resultado.id"
                  class="list-group-item list-group-item-action d-flex justify-content-between align-items-center"
                  :href="resultado.caminho"
                  target="_blank"
                  rel="noopener"
                >
                  <span>{{ resultado.nome_arquivo || 'Resultado' }}</span>
                  <span class="text-primary">Abrir</span>
                </a>
              </div>
              <div v-else class="empty-state">Nenhum resultado vinculado.</div>
            </section>

            <section class="mb-4">
              <h4 class="section-title">Anexos</h4>
              <div v-if="anexos.length" class="list-group">
                <a
                  v-for="anexo in anexos"
                  :key="anexo.id"
                  class="list-group-item list-group-item-action d-flex justify-content-between align-items-center"
                  :href="anexo.caminho"
                  target="_blank"
                  rel="noopener"
                >
                  <span>{{ anexo.nome_arquivo || 'Anexo' }}</span>
                  <span class="text-primary">Abrir</span>
                </a>
              </div>
              <div v-else class="empty-state">Nenhum anexo vinculado.</div>
            </section>

            <section v-if="props.canApprove" class="mt-4 pt-4 border-top">
              <h4 class="section-title">Decisão da Análise</h4>
              <div class="d-flex gap-2 mb-3">
                <button @click="aprovar" :disabled="form.processing" class="btn btn-success">
                  <i class="bi bi-check-circle"></i> Aprovar
                </button>
                <button @click="showRejectForm = !showRejectForm" :class="['btn', showRejectForm ? 'btn-danger' : 'btn-outline-danger']">
                  <i class="bi bi-x-circle"></i> Reprovar
                </button>
              </div>

              <div v-if="showRejectForm" class="alert alert-warning">
                <h6 class="alert-heading">JUSTIFICATIVA</h6>
                <textarea
                  v-model="form.observacoes"
                  class="form-control mb-2"
                  rows="4"
                  placeholder="Explique os motivos da reprovação (mínimo 10 caracteres)..."
                ></textarea>
                <div class="d-flex gap-2">
                  <button @click="reprovar" :disabled="form.processing" class="btn btn-danger">
                    Confirmar Reprovação
                  </button>
                  <button @click="showRejectForm = false" class="btn btn-secondary">
                    Cancelar
                  </button>
                </div>
              </div>
            </section>
          </div>
        </div>

        <div v-if="showAnalysisModal && props.analises && props.analises.length > 0" class="modal-backdrop-custom" @click.self="showAnalysisModal = false">
          <div class="preview-modal" style="max-width: 600px;">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <h5 class="mb-0">Histórico de Análises Anteriores</h5>
              <button type="button" class="btn-close" @click="showAnalysisModal = false"></button>
            </div>
            <div class="modal-analysis-content">
              <div v-for="analise in props.analises" :key="analise.id" class="card mb-2">
                <div class="card-body p-3">
                  <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                      <strong>Versão {{ analise.versao }}</strong> -
                      <span :class="['badge', analise.status === 'Aprovada' ? 'bg-success' : 'bg-danger']">
                        {{ analise.status }}
                      </span>
                    </div>
                    <small class="text-muted">{{ analise.created_at }}</small>
                  </div>
                  <p class="mb-0 mt-2">
                    <strong>Fiscal:</strong> {{ analise.fiscal?.name || 'N/A' }}
                  </p>
                  <p v-if="analise.observacoes" class="mb-0 mt-2">
                    <strong>Observações:</strong> {{ analise.observacoes }}
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </template>
    </NavbarContrato>
  </AuthenticatedLayout>
</template>

<style scoped>
.info-box {
  border: 1px solid #e2e4e6;
  border-radius: 6px;
  padding: 12px;
  min-height: 82px;
}

.info-box span {
  display: block;
  color: #6c757d;
  font-size: 0.85rem;
  margin-bottom: 6px;
}

.info-box strong {
  display: block;
  overflow-wrap: anywhere;
}

.section-title {
  font-size: 1.05rem;
  font-weight: 700;
  margin-bottom: 12px;
}

.content-box {
  border: 1px solid #e2e4e6;
  border-radius: 6px;
  padding: 1rem;
  background: #ffffff;
  white-space: pre-wrap;
}

.empty-state {
  border: 1px dashed #c9ced3;
  border-radius: 6px;
  color: #6c757d;
  padding: 16px;
  text-align: center;
}

.modal-backdrop-custom {
  position: fixed;
  inset: 0;
  background: rgba(17, 24, 39, 0.6);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1050;
  padding: 1rem;
}

.preview-modal {
  width: 100%;
  max-height: 85vh;
  overflow: hidden;
  background: #fff;
  border-radius: 10px;
  box-shadow: 0 20px 45px rgba(0, 0, 0, 0.25);
  padding: 1rem;
}

.modal-analysis-content {
  max-height: 60vh;
  overflow-y: auto;
}
</style>
