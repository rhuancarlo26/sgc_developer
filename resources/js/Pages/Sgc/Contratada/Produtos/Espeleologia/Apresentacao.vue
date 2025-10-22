<template>
  <div>
    <form>
      <div class="row mb-4">
        <div class="col-md-6 mb-3">
          <label>Subproduto</label>
          <input v-model="campanha.subproduto" class="form-control" readonly />
          <small v-if="errors.subproduto" class="text-danger">{{ errors.subproduto }}</small>
        </div>
        <div class="col-md-6 mb-3">
          <label>Empreendimento</label>
          <select
            v-model="campanha.cod_emp"
            @change="preencherCamposEmpreendimento"
            class="form-select"
            required
            :disabled="loading"
          >
            <option value="">Selecione um empreendimento</option>
            <option v-for="emp in empreendimentos" :key="emp.cod_emp" :value="emp.cod_emp">
              {{ emp.cod_emp }}
            </option>
          </select>
          <small v-if="errors.cod_emp" class="text-danger">{{ errors.cod_emp }}</small>
          <small v-if="loading" class="text-muted">Carregando dados...</small>
          <small v-if="noMapData" class="text-danger">Nenhuma coordenada disponível para o empreendimento.</small>
        </div>
      </div>

      <!-- Seção de Traçado -->
      <div class="row mb-5 align-items-stretch">
        <div class="col-md-7 mb-3">
          <div v-if="mapVisible && selectedGeoJson" class="map-container">
            <MapSgc
              ref="mapaVisualizarTrecho"
              height="400px"
              width="100%"
              :geojson="selectedGeoJson"
            />
          </div>
          <div v-else class="text-muted text-center p-4 map-placeholder">
            {{ noMapData ? 'Nenhuma coordenada disponível para o empreendimento.' : 'Selecione um empreendimento para visualizar o traçado.' }}
          </div>
        </div>
        <div class="col-md-5 mb-3">
          <div class="card info-card">
            <div class="card-body">
              <h5 class="card-title">Informações do Empreendimento</h5>
              <div class="table-container">
                <table class="table table-bordered">
                  <tbody>
                    <tr>
                      <th scope="row">BR</th>
                      <td>{{ campanha.cod_emp || 'N/A' }}</td>
                    </tr>
                    <tr>
                      <th scope="row">Subtrecho</th>
                      <td>{{ campanha.subtrecho || 'N/A' }}</td>
                    </tr>
                    <tr>
                      <th scope="row">Segmento</th>
                      <td>{{ campanha.segmento || 'N/A' }}</td>
                    </tr>
                    <tr>
                      <th scope="row">Extensão (km)</th>
                      <td>{{ campanha.extensao || 'N/A' }}</td>
                    </tr>
                    <tr>
                      <th scope="row">Tipo de Intervenção</th>
                      <td>{{ campanha.tipo_de_intervencao || 'N/A' }}</td>
                    </tr>
                    <tr>
                      <th scope="row">Bioma</th>
                      <td>{{ campanha.bioma || 'N/A' }}</td>
                    </tr>
                    <tr>
                      <th scope="row">Descrição</th>
                      <td>{{ campanha.descricao || 'N/A' }}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Seção de Equipe -->
      <div class="card section-card mb-5">
        <div class="card-body">
          <h4>Equipe</h4>
          <div class="mb-3">
            <select v-model="selectedProfissional" class="form-select" @change="vincularProfissional">
              <option value="">Selecione um profissional</option>
              <option v-for="prof in profissionais" :key="prof.id" :value="prof">
                {{ prof.profissional }}
              </option>
            </select>
            <button @click.prevent="showModal = true" class="btn btn-secondary mt-2">Cadastrar Novo Profissional</button>
          </div>
          <table class="table">
            <thead>
              <tr>
                <th>Profissional</th>
                <th>Formação</th>
                <th>Ação</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="prof in profissionalRecords" :key="prof.id">
                <td>{{ prof.profissional }}</td>
                <td>{{ prof.formacao }}</td>
                <td><button @click="excluirProfissional(prof.id)" class="btn btn-danger btn-sm">Excluir</button></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Seção de Justificativas -->
      <div class="card section-card mb-5">
        <div class="card-body">
          <h4>Justificativas</h4>
          <div class="mb-3">
            <label>Código SEI-DNIT do TRE do Órgão Licenciador</label>
            <input v-model="codigoSei" @input="$emit('update:codigo-sei', $event.target.value)" class="form-control" />
          </div>
          <div v-for="(just, index) in justificativas" :key="index" class="mb-3">
            <div class="row">
              <div v-if="index === 0" class="col-md-12 mb-2">
                <label>Citação</label>
                <input
                  v-model="justificativas[index].titulo"
                  @input="$emit('update:justificativas', [...justificativas.slice(0, index), { ...just, titulo: $event.target.value }, ...justificativas.slice(index + 1)])"
                  class="form-control mb-2"
                  placeholder="Título da Citação"
                />
                <textarea
                  v-model="justificativas[index].justificativa"
                  @input="$emit('update:justificativas', [...justificativas.slice(0, index), { ...just, justificativa: $event.target.value }, ...justificativas.slice(index + 1)])"
                  class="form-control"
                  placeholder="Texto da Citação"
                ></textarea>
              </div>
              <div v-else class="col-md-12 mb-2">
                <label>Justificativa</label>
                <textarea
                  v-model="justificativas[index].justificativa"
                  @input="$emit('update:justificativas', [...justificativas.slice(0, index), { ...just, justificativa: $event.target.value }, ...justificativas.slice(index + 1)])"
                  class="form-control"
                  placeholder="Texto da Justificativa"
                ></textarea>
              </div>
              <div class="col-md-12 text-end">
                <button @click="excluirJustificativa(index)" class="btn btn-danger btn-sm" v-if="index > 0">Remover</button>
              </div>
            </div>
          </div>
          <button @click="adicionarJustificativa" class="btn btn-secondary mt-2">Adicionar Justificativa</button>
        </div>
      </div>

      <!-- Modal para Cadastrar Novo Profissional -->
      <div v-if="showModal" class="modal" tabindex="-1" style="display: block;">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">CADASTRAR PROFISSIONAL</h5>
              <button type="button" class="btn-close" @click="showModal = false"></button>
            </div>
            <div class="modal-body">
              <div class="row">
                <div class="col-md-6 mb-2">
                  <label class="form-label">Profissional</label>
                  <input v-model="novoProfissional.profissional" class="form-control" required />
                </div>
                <div class="col-md-6 mb-2">
                  <label class="form-label">Formação</label>
                  <input v-model="novoProfissional.formacao" class="form-control" />
                </div>
                <div class="col-md-6 mb-2">
                  <label class="form-label">Telefone</label>
                  <input v-model="novoProfissional.telefone" class="form-control" />
                </div>
                <div class="col-md-6 mb-2">
                  <label class="form-label">CPF</label>
                  <input v-model="novoProfissional.cpf" class="form-control" />
                </div>
                <div class="col-md-6 mb-2">
                  <label class="form-label">Email</label>
                  <input v-model="novoProfissional.email" class="form-control" />
                </div>
                <div class="col-md-6 mb-2">
                  <label class="form-label">Currículo Lattes</label>
                  <input v-model="novoProfissional.curriculum_lattes" class="form-control" />
                </div>
                <div class="col-md-6 mb-2">
                  <label class="form-label">Função</label>
                  <input v-model="novoProfissional.funcao" class="form-control" />
                </div>
                <div class="col-md-6 mb-2">
                  <label class="form-label">CTF</label>
                  <input v-model="novoProfissional.ctf" class="form-control" />
                </div>
                <div class="col-md-6 mb-2">
                  <label class="form-label">Validade</label>
                  <input v-model="novoProfissional.validade" type="date" class="form-control" />
                </div>
                <div class="col-md-6 mb-2">
                  <label class="form-label">Conselho de Classe</label>
                  <input v-model="novoProfissional.conselho_de_classe" class="form-control" />
                </div>
                <div class="col-md-6 mb-2">
                  <label class="form-label">Número de Registro</label>
                  <input v-model="novoProfissional.numero_de_registro" type="number" class="form-control" />
                </div>
                <div class="col-md-6 mb-2">
                  <label class="form-label">Status</label>
                  <input v-model="novoProfissional.status" class="form-control" />
                </div>
                <div class="col-md-12 mb-2">
                  <label class="form-label">Observação</label>
                  <textarea v-model="novoProfissional.observacao" class="form-control"></textarea>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" @click="showModal = false">Fechar</button>
              <button @click="salvarNovoProfissional" class="btn btn-primary">Salvar</button>
            </div>
          </div>
        </div>
      </div>
    </form>
  </div>
</template>

<script setup>
import { defineProps, defineEmits, ref, watch, onMounted } from 'vue';
import { router } from '@inertiajs/vue3';
import MapSgc from '@/Components/MapSgc.vue';
import { useToast } from 'vue-toastification';

const toast = useToast();

const props = defineProps(['campanha', 'empreendimentos', 'errors', 'profissionais', 'profissionalRecords', 'justificativas', 'codigoSei']);
const emit = defineEmits(['update-form', 'vincular-profissional', 'salvar-novo-profissional', 'update:codigo-sei', 'update:justificativas', 'excluir-profissional']);

const showModal = ref(false);
const selectedProfissional = ref(null);
const loading = ref(false);
const noMapData = ref(false);
const mapVisible = ref(false);
const mapaVisualizarTrecho = ref(null);
const selectedGeoJson = ref(null);

const novoProfissional = ref({
  profissional: '',
  formacao: '',
  telefone: '',
  cpf: '',
  email: '',
  curriculum_lattes: '',
  funcao: '',
  ctf: '',
  validade: '',
  conselho_de_classe: '',
  numero_de_registro: '',
  status: 'Ativo',
  observacao: '',
});

const codigoSei = ref(props.codigoSei || '');
const justificativas = ref(props.justificativas || [{ justificativa: '', tipo: 'citacao', titulo: '', codigo_sei: props.codigoSei || '' }]);

const preencherCamposEmpreendimento = () => {
  loading.value = true;
  noMapData.value = false;
  mapVisible.value = false;
  selectedGeoJson.value = null;

  console.log('Empreendimentos disponíveis:', props.empreendimentos);
  console.log('Cod_emp selecionado:', props.campanha.cod_emp);
  const emp = props.empreendimentos.find(e => e.cod_emp === props.campanha.cod_emp);
  console.log('Empreendimento selecionado:', emp);

  if (emp) {
    const formData = {
      subtrecho: emp.subtrecho || '',
      segmento: emp.segmento || '',
      extensao: emp.extensao || '',
      tipo_de_intervencao: emp.tipo_de_intervencao || '',
      descricao: emp.descricao || '',
      bioma: emp.bioma || '',
      coordenadas: emp.coordenadas || null,
    };
    emit('update-form', formData);
    console.log('Form data emitido:', formData);

    if (emp.coordenadas) {
      try {
        JSON.parse(emp.coordenadas);
        selectedGeoJson.value = emp.coordenadas;
        console.log('Coordenadas selecionadas:', selectedGeoJson.value);
        mapVisible.value = true;
        setTimeout(() => {
          if (mapaVisualizarTrecho.value) {
            mapaVisualizarTrecho.value.renderMapa();
            mapaVisualizarTrecho.value.setGeoJson(selectedGeoJson.value);
          } else {
            console.error('Mapa ref não disponível');
            toast.error('Erro ao inicializar o mapa.');
          }
        }, 500);
      } catch (e) {
        console.error('Erro ao parsear GeoJSON:', e);
        noMapData.value = true;
        toast.error('GeoJSON inválido para o empreendimento.');
      }
    } else {
      console.log('Coordenadas ausentes para o empreendimento:', emp.cod_emp);
      noMapData.value = true;
    }
  } else {
    emit('update-form', {
      subtrecho: '',
      segmento: '',
      extensao: '',
      tipo_de_intervencao: '',
      descricao: '',
      bioma: '',
      coordenadas: null,
    });
    console.log('Nenhum empreendimento encontrado para cod_emp:', props.campanha.cod_emp);
    noMapData.value = true;
  }
  loading.value = false;
};

const vincularProfissional = () => {
  if (selectedProfissional.value) {
    emit('vincular-profissional', selectedProfissional.value);
    selectedProfissional.value = null;
  }
};

const salvarNovoProfissional = () => {
  emit('salvar-novo-profissional', { ...novoProfissional.value });
  novoProfissional.value = {
    profissional: '',
    formacao: '',
    telefone: '',
    cpf: '',
    email: '',
    curriculum_lattes: '',
    funcao: '',
    ctf: '',
    validade: '',
    conselho_de_classe: '',
    numero_de_registro: '',
    status: 'Ativo',
    observacao: '',
  };
  showModal.value = false;
};


const adicionarJustificativa = () => {
  justificativas.value.push({ justificativa: '', tipo: 'justificativa', titulo: '', codigo_sei: '' });
};

const excluirJustificativa = (index) => {
  justificativas.value.splice(index, 1);
};

const excluirProfissional = (id) => {
  emit('excluir-profissional', id);
};

watch(() => props.codigoSei, (newVal) => {
  codigoSei.value = newVal || '';
});

watch(() => props.justificativas, (newVal) => {
  justificativas.value = newVal || [{ justificativa: '', tipo: 'citacao', titulo: '', codigo_sei: codigoSei.value }];
}, { deep: true });

onMounted(() => {
  console.log('Props recebidas em Apresentacao.vue:', props);
  if (props.campanha.cod_emp) {
    preencherCamposEmpreendimento();
  }
});
</script>

<style scoped>
.map-container, .map-placeholder {
  height: 400px;
  border: 1px solid #dee2e6;
  border-radius: 6px;
  overflow: hidden;
}

.info-card {
  height: 400px;
  border: none;
  border-radius: 6px;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
  display: flex;
  flex-direction: column;
  max-width: 100%;
  overflow: hidden;
}

.card-body {
  flex: 1;
  padding: 1rem;
}

.card-title {
  font-size: 1.25rem;
  font-weight: 600;
  text-align: center;
  margin-bottom: 1rem;
  color: #343a40;
}

.table-container {
  flex: 1;
  overflow-y: auto;
  max-width: 100%;
}

.table {
  table-layout: fixed;
  width: 100%;
  margin-bottom: 0;
}

.table th {
  width: 40%;
  font-weight: 500;
  text-align: left;
  vertical-align: middle;
  padding: 0.5rem;
  border: 1px solid #dee2e6;
  background-color: #f8f9fa;
}

.table td {
  width: 60%;
  vertical-align: middle;
  padding: 0.5rem;
  border: 1px solid #dee2e6;
  overflow-wrap: break-word;
  word-break: break-all;
  max-width: 100%;
}

.section-card {
  background-color: #ffffff;
  border: 1px solid #dee2e6;
  border-radius: 6px;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
  padding: 1.5rem;
  margin-top: 2rem;
}

.align-items-stretch {
  display: flex;
  align-items: stretch;
}

/* Estilização do Modal */
.modal {
  background-color: rgba(0, 0, 0, 0.4);
}

.modal-dialog {
  max-width: 1100px;
}

.modal-content {
  border-radius: 6px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  background-color: #fff;
}

.modal-header {
  background-color: #467cb7;
  padding: 0.75rem 1rem;
  border-bottom: none;
}

.modal-header {
  background-color: #467cb7;
  padding: 0.75rem 1rem;
  border-bottom: none;
  display: flex;
  justify-content: center; 
  align-items: center; 
}

.modal-title {
  font-size: 1.25rem;
  font-weight: 600;
  color: #fff;
}

.btn-close {
  background: none;
  color: #fff;
  opacity: 0.7;
}

.btn-close:hover {
  opacity: 1;
}

.modal-body {
  padding: 1rem;
}

.form-label {
  font-size: 0.9rem;
  color: #343a40;
}

.form-control {
  border-radius: 4px;
  border: 1px solid #ced4da;
}

.form-control:focus {
  border-color: #007bff;
  box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.2);
}

.modal-footer {
  padding: 0.75rem 1rem;
  border-top: none;
}

.btn-secondary, .btn-primary {
  border-radius: 4px;
  padding: 0.4rem 1rem;
}

@media (max-width: 767.98px) {
  .align-items-stretch {
    flex-direction: column;
  }
  .map-container, .map-placeholder, .info-card {
    height: 300px;
  }
  .section-card {
    padding: 1rem;
    margin-top: 1.5rem;
  }
  .table th, .table td {
    padding: 0.4rem;
  }
  .modal-dialog {
    margin: 0.5rem;
  }
}
</style>