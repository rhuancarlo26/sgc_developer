<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import NavbarContrato from '@/Pages/Sgc/Contratada/NavbarContrato.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import InputError from '@/Components/InputError.vue';
import { ref } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import MapLayer from './MapViewer.vue';
import ResultadosGeoserver from './ResultadosGeoserver.vue';

const props = defineProps({
  campanha: {
    type: Object,
    default: () => ({}),
  },
  contrato: [Number, String],
  produto: String,
  contratos: Object,
  canApprove: Boolean,
  coordenadas: [Object, String, Array],
});

const activeTab = ref('apresentacao');

const formAprovacao = useForm({
  status: '',
  observacoes: '',
});

const setActiveTab = (tab) => {
  activeTab.value = tab;
};

const aprovarCampanha = () => {
  formAprovacao.status = 'Aprovada';
  formAprovacao.observacoes = '';
  salvarAprovacao();
};

const rejeitarCampanha = () => {
  if (!formAprovacao.observacoes.trim()) {
    formAprovacao.setError('observacoes', 'Observações são obrigatórias para rejeição.');
    return;
  }
  formAprovacao.status = 'Rejeitada';
  salvarAprovacao();
};

const salvarAprovacao = () => {
  formAprovacao.post(route('sgc.contratada.produtos.espeleo.approve', [props.contrato, 'espeleologia', props.campanha.id]), {
    onSuccess: () => {
      formAprovacao.reset();
      router.get(route('sgc.contratada.produtos.index', [props.contrato, 'espeleologia']));
    },
  });
};

const formatAnexoLabel = (tipo) => {
  if (!tipo) return 'Nao informado';
  return tipo
    .replace(/_/g, ' ')
    .split(' ')
    .map((word) => word.charAt(0).toUpperCase() + word.slice(1))
    .join(' ');
};
</script>

<template>
  <AuthenticatedLayout>
    <template #header>
      <Breadcrumb
        :links="[
          { route: route('sgc.gestao.listagem', contratos.tipo_contrato), label: 'Gestão de Contratos' },
          { route: route('sgc.contratada.produtos.index', [contrato, 'espeleologia']), label: contratos.contratada },
          { route: '#', label: `Visualizar Campanha ${campanha.id_campanha || campanha.id}` },
        ]"
      />
    </template>

    <NavbarContrato :tipo="{ id: contrato }">
      <template #body>
        <div class="card">
          <div class="card-body">
            <h2 class="text-center mb-4">VISUALIZAR CAMPANHA: ESPELEOLOGIA</h2>
            <h4 class="mb-3">Status: {{ campanha.status }}</h4>

            <ul class="nav nav-tabs mb-4">
              <li class="nav-item">
                <a class="nav-link" :class="{ active: activeTab === 'apresentacao' }" @click.prevent="setActiveTab('apresentacao')">
                  Apresentação
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" :class="{ active: activeTab === 'metodologia' }" @click.prevent="setActiveTab('metodologia')">
                  Metodologias
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" :class="{ active: activeTab === 'resultados' }" @click.prevent="setActiveTab('resultados')">
                  Resultados
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" :class="{ active: activeTab === 'resultadosgeo' }" @click.prevent="setActiveTab('resultadosgeo')">
                  Resultados Geoserver
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" :class="{ active: activeTab === 'anexos' }" @click.prevent="setActiveTab('anexos')">
                  Anexos
                </a>
              </li>
            </ul>

            <div class="tab-content">
              <div v-if="activeTab === 'apresentacao'" class="tab-pane fade" :class="{ 'show active': activeTab === 'apresentacao' }">
                <h4 class="text-center mb-3" style="font-weight: bold;">APRESENTAÇÃO</h4>
                <div class="row mb-4">
                  <div class="col-md-8">
                    <MapLayer :emp_coordenadas="coordenadas" :campanha-id="campanha.id" />
                  </div>
                  <div class="col-md-4">
                    <div class="mb-3">
                      <label class="form-label">Empreendimento</label>
                      <input type="text" class="form-control" :value="campanha.cod_emp || 'Nao informado'" disabled />
                    </div>
                    <div class="mb-3">
                      <label class="form-label">Subproduto</label>
                      <input type="text" class="form-control" :value="campanha.subproduto || 'Nao informado'" disabled />
                    </div>
                    <div class="mb-3">
                      <label class="form-label">Subtrecho</label>
                      <input type="text" class="form-control" :value="campanha.subtrecho || 'Nao informado'" disabled />
                    </div>
                    <div class="mb-3">
                      <label class="form-label">Segmento</label>
                      <input type="text" class="form-control" :value="campanha.segmento || 'Nao informado'" disabled />
                    </div>
                  </div>
                </div>

                <div class="card mb-3">
                  <div class="card-body">
                    <h5 class="mb-3">Equipe Vinculada</h5>
                    <table class="table table-bordered" v-if="campanha.profissionais && campanha.profissionais.length">
                      <thead>
                        <tr>
                          <th>Profissional</th>
                          <th>Formação</th>
                          <th>Função</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr v-for="profissional in campanha.profissionais" :key="profissional.id">
                          <td>{{ profissional.profissional || 'Nao informado' }}</td>
                          <td>{{ profissional.formacao || 'Nao informado' }}</td>
                          <td>{{ profissional.funcao || 'Nao informado' }}</td>
                        </tr>
                      </tbody>
                    </table>
                    <div v-else class="alert alert-info mb-0">Nenhum profissional vinculado.</div>
                  </div>
                </div>

                <div class="card">
                  <div class="card-body">
                    <h5 class="mb-3">Justificativas</h5>
                    <div v-if="campanha.justificativas && campanha.justificativas.length">
                      <div v-for="item in campanha.justificativas" :key="item.id" class="border rounded p-3 mb-2">
                        <p class="mb-1"><strong>Tipo:</strong> {{ item.tipo || 'Nao informado' }}</p>
                        <p class="mb-1"><strong>Titulo:</strong> {{ item.titulo || 'Nao informado' }}</p>
                        <p class="mb-1"><strong>Codigo SEI:</strong> {{ item.codigo_sei || 'Nao informado' }}</p>
                        <p class="mb-0"><strong>Texto:</strong> {{ item.justificativa || 'Nao informado' }}</p>
                      </div>
                    </div>
                    <div v-else class="alert alert-info mb-0">Nenhuma justificativa registrada.</div>
                  </div>
                </div>
              </div>

              <div v-if="activeTab === 'metodologia'" class="tab-pane fade" :class="{ 'show active': activeTab === 'metodologia' }">
                <h4 class="mb-3 text-center">METODOLOGIA</h4>
                <div class="card">
                  <div class="card-body">
                    <p class="mb-0" style="white-space: pre-wrap;">{{ campanha.metodologia || 'Nenhuma metodologia registrada.' }}</p>
                  </div>
                </div>
              </div>

              <div v-if="activeTab === 'resultados'" class="tab-pane fade" :class="{ 'show active': activeTab === 'resultados' }">
                <h4 class="mb-3 text-center">RESULTADOS</h4>
                <table class="table table-bordered" v-if="campanha.resultados_anexos && campanha.resultados_anexos.length">
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>Tipo</th>
                      <th>Arquivo</th>
                      <th>Observação</th>
                      <th>Data</th>
                      <th>Ação</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="resultado in campanha.resultados_anexos" :key="resultado.id">
                      <td>{{ resultado.id }}</td>
                      <td>{{ formatAnexoLabel(resultado.tipo) }}</td>
                      <td>{{ resultado.nome_arquivo || 'Nao informado' }}</td>
                      <td>{{ resultado.comentario || 'Sem observacao' }}</td>
                      <td>{{ resultado.created_at || 'Nao informado' }}</td>
                      <td>
                        <a v-if="resultado.caminho" :href="resultado.caminho" target="_blank" class="btn btn-link p-0">Abrir</a>
                        <span v-else>Nenhum arquivo</span>
                      </td>
                    </tr>
                  </tbody>
                </table>
                <div v-else class="alert alert-info">Nenhum resultado anexado.</div>
              </div>

              <div v-if="activeTab === 'resultadosgeo'" class="tab-pane fade" :class="{ 'show active': activeTab === 'resultadosgeo' }">
                <ResultadosGeoserver
                  :empreendimentos="[]"
                  :errors="{}"
                  :campanha-id="campanha.id"
                  :contrato="Number(contrato)"
                  :resultados-anexos="campanha.resultados_anexos || []"
                  :subprodutos-espeleologia="[]"
                  :estudos-posteriores="[]"
                  @update-resultados-anexos="() => {}"
                />
              </div>

              <div v-if="activeTab === 'anexos'" class="tab-pane fade" :class="{ 'show active': activeTab === 'anexos' }">
                <h4 class="mb-3" style="text-align: center;">ANEXOS</h4>
                <div v-if="campanha.anexos && campanha.anexos.length > 0" class="overflow-x-auto mb-4">
                  <table class="table table-bordered">
                    <thead>
                      <tr>
                        <th>ID</th>
                        <th>Tipo de Anexo</th>
                        <th>Arquivo</th>
                        <th>Legenda</th>
                        <th>Data</th>
                        <th>Ação</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="anexo in campanha.anexos" :key="anexo.id">
                        <td>{{ anexo.id || 'Nao informado' }}</td>
                        <td>{{ formatAnexoLabel(anexo.tipo_anexo) }}</td>
                        <td>{{ anexo.nome_arquivo || 'Nao informado' }}</td>
                        <td>{{ anexo.legenda || 'Sem legenda' }}</td>
                        <td>{{ anexo.created_at || 'Nao informado' }}</td>
                        <td>
                          <a v-if="anexo.caminho" :href="anexo.caminho" target="_blank" class="btn btn-link p-0">Visualizar</a>
                          <span v-else>Nenhum arquivo</span>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
                <div v-else class="alert alert-info text-center">Nenhum anexo disponível.</div>

                <div v-if="canApprove && campanha.status === 'Em análise'" class="mt-4">
                  <h4>APROVAÇÃO</h4>
                  <form @submit.prevent="rejeitarCampanha">
                    <div class="mb-3">
                      <label for="observacoes" class="form-label">Observações (obrigatório para rejeição)</label>
                      <textarea id="observacoes" v-model="formAprovacao.observacoes" class="form-control" rows="3"></textarea>
                      <InputError :message="formAprovacao.errors.observacoes" class="mt-2" />
                    </div>
                    <div class="d-flex gap-2">
                      <button type="button" class="btn btn-success" @click="aprovarCampanha">Aprovar</button>
                      <button type="submit" class="btn btn-danger">Rejeitar</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
      </template>
    </NavbarContrato>
  </AuthenticatedLayout>
</template>
