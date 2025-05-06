<script setup>
import { ref, onMounted, computed } from "vue";
import { usePage } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Calendar from "@/Components/FullCalendar.vue";
import NavbarContrato from "../NavbarContrato.vue";
import axios from 'axios';

const page = usePage();
const eventos = ref([]);
const filtroItemEdital = ref([]);
const filtroCodEmp = ref("");
const filtroUf = ref("");
const filtroSituacao = ref("");
const showModal = ref(false);
const selectedEvent = ref(null);
const showCreateModal = ref(false);
const newEvent = ref({
  cod_emp: '',
  subproduto: '',
  data_de_inicio_previsto: '',
  data_de_entrega_previsto: '',
});
const opcoesEvento = ref({ empreendimentos: [], subprodutos: [] });

onMounted(async () => {
  eventos.value = page.props.eventos || [];
  console.log("Eventos carregados:", eventos.value);

  try {
    const url = `/sgc/contratada/${page.props.contrato}/cronograma/opcoes-evento`;
    console.log("URL da requisição:", window.location.origin + url);
    const response = await axios.get(url);
    opcoesEvento.value = response.data;
    console.log("Opções de evento carregadas:", opcoesEvento.value);
  } catch (error) {
    console.error("Erro ao carregar opções de evento:", error.response ? error.response.status : error.message);
  }
});

const itensEditaisUnicos = computed(() => {
  return [...new Set(eventos.value.map((evento) => evento.title))].sort();
});

const codEmpsUnicos = computed(() => {
  return [...new Set(eventos.value.map((evento) => evento.cod_emp))].sort();
});

const ufsUnicas = computed(() => {
  return [...new Set(eventos.value.map((evento) => evento.uf))].sort();
});

const eventosFiltrados = computed(() => {
  let filtered = eventos.value.map(evento => ({
    ...evento,
    backgroundColor: evento.source === 'auxiliar' ? '#28a745' : '#3788d8', // Forçar verde para auxiliar, azul para principal
  }));

  const hoje = new Date();
  hoje.setHours(0, 0, 0, 0);

  if (filtroItemEdital.value.length > 0) {
    filtered = filtered.filter((evento) => filtroItemEdital.value.includes(evento.title));
  }

  if (filtroCodEmp.value) {
    filtered = filtered.filter((evento) => evento.cod_emp === filtroCodEmp.value);
  }

  if (filtroUf.value) {
    filtered = filtered.filter((evento) => evento.uf === filtroUf.value);
  }

  if (filtroSituacao.value) {
    filtered = filtered.filter((evento) => {
      const dataEntregaPrevista = new Date(evento.end);
      dataEntregaPrevista.setHours(0, 0, 0, 0);
      const versao00Data = evento.versao_00_data_de_entrega ? new Date(evento.versao_00_data_de_entrega) : null;
      if (versao00Data) versao00Data.setHours(0, 0, 0, 0);

      if (filtroSituacao.value === "atrasada") {
        return (
          dataEntregaPrevista < hoje &&
          (!versao00Data || versao00Data === "")
        );
      } else if (filtroSituacao.value === "no_prazo") {
        return (
          versao00Data &&
          versao00Data <= dataEntregaPrevista
        );
      } else if (filtroSituacao.value === "entrega_atrasada") {
        return (
          versao00Data &&
          dataEntregaPrevista < versao00Data
        );
      } else if (filtroSituacao.value === "prevista") {
        return (
          dataEntregaPrevista > hoje
        );
      }
      return true;
    });
  }

  return filtered;
});

const handleEventClick = (eventInfo) => {
  selectedEvent.value = {
    ...eventInfo.event._def.extendedProps,
    title: eventInfo.event._def.title,
    start: eventInfo.event._instance.range.start,
    end: eventInfo.event._instance.range.end,
  };
  showModal.value = true;
};

const closeModal = () => {
  showModal.value = false;
  selectedEvent.value = null;
};

const openCreateModal = () => {
  showCreateModal.value = true;
};

const closeCreateModal = () => {
  showCreateModal.value = false;
  newEvent.value = { cod_emp: '', subproduto: '', data_de_inicio_previsto: '', data_de_entrega_previsto: '' };
};

const saveNewEvent = async () => {
  try {
    const url = `/sgc/contratada/${page.props.contrato}/cronograma/evento-auxiliar`;
    console.log("Salvando evento com dados:", newEvent.value);
    await axios.post(url, newEvent.value);
    eventos.value.push({
      title: newEvent.value.subproduto,
      start: newEvent.value.data_de_inicio_previsto,
      end: newEvent.value.data_de_entrega_previsto,
      cod_emp: newEvent.value.cod_emp,
      source: 'auxiliar',
      backgroundColor: '#28a745', // Verde fixo para eventos auxiliares
    });
    closeCreateModal();
  } catch (error) {
    console.error("Erro ao salvar evento:", error.response ? error.response.data : error.message);
    alert("Erro ao salvar o evento. Verifique os dados e tente novamente.");
  }
};
</script>

<template>
  <AuthenticatedLayout>
    <NavbarContrato>
      <template #body>
        <div class="container mt-4">
          <h3 class="title-cronograma">CRONOGRAMA FÍSICO</h3>
          <div class="filter-container mb-3">
            <div class="filter-item">
              <label for="filtroCodEmp" class="form-label">Filtrar Empreendimento:</label>
              <select id="filtroCodEmp" v-model="filtroCodEmp" class="form-select filter-select">
                <option value="">Todos</option>
                <option v-for="cod in codEmpsUnicos" :key="cod" :value="cod">{{ cod }}</option>
              </select>
            </div>
            <div class="filter-item">
              <label for="filtroUf" class="form-label">Filtrar UF:</label>
              <select id="filtroUf" v-model="filtroUf" class="form-select filter-select">
                <option value="">Todos</option>
                <option v-for="uf in ufsUnicas" :key="uf" :value="uf">{{ uf }}</option>
              </select>
            </div>
            <div class="filter-item">
              <label for="filtroSituacao" class="form-label">Filtrar Situação:</label>
              <select id="filtroSituacao" v-model="filtroSituacao" class="form-select filter-select">
                <option value="">Todos</option>
                <option value="atrasada">Atrasada</option>
                <option value="no_prazo">Entrega no Prazo</option>
                <option value="entrega_atrasada">Entrega Atrasada</option>
                <option value="prevista">Prevista</option>
              </select>
            </div>
            <div class="filter-item">
              <button class="btn btn-outline-info" @click="openCreateModal">+ Novo Evento</button>
            </div>

          </div>
          <div class="custom-calendar p-2">
            <Calendar :events="eventosFiltrados" @event-click="handleEventClick" locale="pt-br" />
          </div>

          <div v-if="showModal" class="modal-overlay" @click="closeModal">
            <div class="modal-content" @click.stop>
              <h4>Detalhes do Evento</h4>
              <div v-if="selectedEvent">
                <p><strong>Código Empresa:</strong> {{ selectedEvent.cod_emp || 'N/A' }}</p>
                <p><strong>Contrato:</strong> {{ selectedEvent.contrato || 'N/A' }}</p>
                <p><strong>Empresa:</strong> {{ selectedEvent.empresa || 'N/A' }}</p>
                <p><strong>Etapa:</strong> {{ selectedEvent.etapa || 'N/A' }}</p>
                <p><strong>Item Edital:</strong> {{ selectedEvent.item_edital || 'N/A' }}</p>
                <p><strong>Subproduto:</strong> {{ selectedEvent.title || 'N/A' }}</p>
                <p><strong>Data de Início Previsto:</strong> {{ selectedEvent.start ? new Date(selectedEvent.start).toLocaleDateString('pt-BR') : 'N/A' }}</p>
                <p><strong>Data de Entrega Prevista:</strong> {{ selectedEvent.end ? new Date(selectedEvent.end).toLocaleDateString('pt-BR') : 'N/A' }}</p>
                <p><strong>Versão 00 Data de Entrega:</strong> {{ selectedEvent.versao_00_data_de_entrega ? new Date(selectedEvent.versao_00_data_de_entrega).toLocaleDateString('pt-BR') : 'N/A' }}</p>
                <p><strong>Versão Aceita Data:</strong> {{ selectedEvent.versao_aceita_data ? new Date(selectedEvent.versao_aceita_data).toLocaleDateString('pt-BR') : 'N/A' }}</p>
                <p><strong>Requisição Externa Data:</strong> {{ selectedEvent.req_ext_data ? new Date(selectedEvent.req_ext_data).toLocaleDateString('pt-BR') : 'N/A' }}</p>
                <p><strong>Autorização Externa Data:</strong> {{ selectedEvent.aut_ext_data ? new Date(selectedEvent.aut_ext_data).toLocaleDateString('pt-BR') : 'N/A' }}</p>
              </div>
              <button class="btn btn-secondary mt-3" @click="closeModal">Fechar</button>
            </div>
          </div>

          <div v-if="showCreateModal" class="modal-overlay" @click="closeCreateModal">
            <div class="modal-content" @click.stop>
              <h4>Inserir Novo Evento</h4>
              <div class="form-group">
                <label for="cod_emp">Empreendimento:</label>
                <select id="cod_emp" v-model="newEvent.cod_emp" class="form-select">
                  <option value="">Selecione um empreendimento</option>
                  <option v-for="emp in opcoesEvento.empreendimentos" :key="emp" :value="emp">
                    {{ emp }}
                  </option>
                </select>
              </div>
              <div class="form-group">
                <label for="subproduto">Subproduto:</label>
                <select id="subproduto" v-model="newEvent.subproduto" class="form-select">
                  <option value="">Selecione um subproduto</option>
                  <option v-for="sub in opcoesEvento.subprodutos" :key="sub" :value="sub">
                    {{ sub }}
                  </option>
                </select>
              </div>
              <div class="form-group">
                <label for="data_de_inicio_previsto">Data de Início Previsto:</label>
                <input id="data_de_inicio_previsto" v-model="newEvent.data_de_inicio_previsto" type="date" class="form-control" />
              </div>
              <div class="form-group">
                <label for="data_de_entrega_previsto">Data de Entrega Prevista:</label>
                <input id="data_de_entrega_previsto" v-model="newEvent.data_de_entrega_previsto" type="date" class="form-control" />
              </div>
              <div class="mt-3">
                <button class="btn btn-primary me-2" @click="saveNewEvent">Salvar</button>
                <button class="btn btn-secondary" @click="closeCreateModal">Cancelar</button>
              </div>
            </div>
          </div>
        </div>
      </template>
    </NavbarContrato>
  </AuthenticatedLayout>
</template>

<style scoped>
.title-cronograma { font-size: 2rem; text-align: center; margin: 1.5rem 0; }
.filter-container { display: flex; gap: 20px; flex-wrap: wrap; }
.filter-item { flex: 1; min-width: 250px; }
.filter-select { width: 100%; border-radius: 8px; border: 1px solid #007bff; padding: 8px; }
.custom-calendar { background-color: #f8f9fa; border-radius: 10px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); padding: 15px; }
.fc-multimonth-month { border: 1px solid #e0e0e0; border-radius: 5px; background-color: #ffffff; }
.fc-button { background-color: #007bff; border: none; border-radius: 5px; padding: 6px 12px; }
.fc-button:hover { background-color: #0056b3; }
.modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); display: flex; justify-content: center; align-items: center; z-index: 1000; }
.modal-content { background: white; padding: 20px; border-radius: 10px; max-width: 500px; width: 90%; max-height: 80vh; overflow-y: auto; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); }
.modal-content h4 { margin-bottom: 15px; color: #007bff; }
.form-group { margin-bottom: 15px; }
.form-control, .form-select { width: 100%; padding: 8px; border-radius: 5px; border: 1px solid #ced4da; }

/* Button Inserir Evento */
.filter-container {
  display: flex;
  gap: 20px;
  flex-wrap: wrap;
  justify-content: space-between; 
  align-items: flex-end; 
}

.filter-button {
  flex: 0 0 auto; 
  display: flex;
  align-items: flex-end;
}


</style>