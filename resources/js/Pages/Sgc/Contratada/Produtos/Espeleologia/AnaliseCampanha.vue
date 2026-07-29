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
});

const form = useForm({
  observacoes: '',
});

const showRejectForm = ref(false);

const statusClass = computed(() => ({
  'bg-warning text-white': props.campanha.status === 'Em análise',
  'bg-success text-white': props.campanha.status === 'Aprovada',
  'bg-danger text-white': props.campanha.status === 'Rejeitada',
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
            <div class="text-center mb-4">
              <h2 class="mb-2">ANALISE CAMPANHA ESPELEOLOGIA</h2>
              <p class="text-muted mb-3 fs-5">{{ props.campanha.subproduto || 'Subproduto nao informado' }}</p>
              <span class="badge fs-5 px-3 py-2 fw-bold" :class="statusClass">{{ props.campanha.status || 'N/A' }}</span>
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
                {{ props.campanha.metodologia || 'Nao informada' }}
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
              <h4 class="section-title">Decisao da Analise</h4>
              <div class="d-flex gap-2 mb-3">
                <button @click="aprovar" :disabled="form.processing" class="btn btn-success">
                  Aprovar
                </button>
                <button @click="showRejectForm = !showRejectForm" :class="['btn', showRejectForm ? 'btn-danger' : 'btn-outline-danger']">
                  Reprovar
                </button>
              </div>

              <div v-if="showRejectForm" class="alert alert-warning">
                <h6 class="alert-heading">Justificativa</h6>
                <textarea
                  v-model="form.observacoes"
                  class="form-control mb-2"
                  rows="4"
                  placeholder="Explique os motivos da reprovacao (minimo 10 caracteres)..."
                ></textarea>
                <div class="d-flex gap-2">
                  <button @click="reprovar" :disabled="form.processing" class="btn btn-danger">
                    Confirmar Reprovacao
                  </button>
                  <button @click="showRejectForm = false" class="btn btn-secondary">
                    Cancelar
                  </button>
                </div>
              </div>
            </section>
          </div>
        </div>
      </template>
    </NavbarContrato>
  </AuthenticatedLayout>
</template>

<style scoped>
.info-box {
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  padding: 0.75rem;
  background: #f8fafc;
}

.info-box span {
  display: block;
  font-size: 0.75rem;
  color: #6b7280;
  text-transform: uppercase;
}

.info-box strong {
  display: block;
  color: #111827;
}

.section-title {
  font-size: 1.1rem;
  font-weight: 700;
}

.content-box {
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  padding: 1rem;
  background: #ffffff;
  white-space: pre-wrap;
}

.empty-state {
  border: 1px dashed #d1d5db;
  border-radius: 8px;
  padding: 1rem;
  color: #6b7280;
}
</style>
