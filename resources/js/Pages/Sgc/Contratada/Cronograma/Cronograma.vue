<script setup>
import { ref, onMounted, computed } from "vue";
import { usePage } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Calendar from "@/Components/FullCalendar.vue";
import NavbarContrato from "../NavbarContrato.vue";
import axios from 'axios';
import { situacoes, obterSituacao, formatarData, fimExclusivo } from './situacao';

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

// Definir os props recebidos
defineProps({
  eventos: Array,
  contratoId: Number,
  contrato: Object,
});

onMounted(async () => {
  eventos.value = page.props.eventos || [];
  console.log("Eventos carregados:", eventos.value);

  try {
    const url = `/sgc/contratada/${page.props.contratoId}/cronograma/opcoes-evento`;
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
  let filtered = eventos.value.map(evento => {
    const situacao = obterSituacao(evento);
    return {
      ...evento,
      inicioPrevisto: evento.start,
      entregaPrevista: evento.end,
      end: fimExclusivo(evento.end),
      allDay: true,
      situacao,
      backgroundColor: situacoes[situacao].cor,
      borderColor: situacoes[situacao].cor,
      textColor: '#ffffff',
    };
  });

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
    filtered = filtered.filter((evento) => evento.situacao === filtroSituacao.value);
  }

  return filtered;
});

const handleEventClick = (eventInfo) => {
  selectedEvent.value = {
    ...eventInfo.event.extendedProps,
    title: eventInfo.event.title,
    start: eventInfo.event.extendedProps.inicioPrevisto,
    end: eventInfo.event.extendedProps.entregaPrevista,
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
    const url = `/sgc/contratada/${page.props.contratoId}/cronograma/evento-auxiliar`;
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
    <NavbarContrato :tipo="contrato" class="cronograma-layout">
      <template #body>
        <div class="card card-body cronograma-conteudo">
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
                <option v-for="(situacao, chave) in situacoes" :key="chave" :value="chave">
                  {{ situacao.label }}
                </option>
              </select>
            </div>
            <div class="filter-item filter-button">
              <button class="btn btn-outline-info" @click="openCreateModal">+ Novo Evento</button>
            </div>
          </div>
          <div class="situacao-legenda mb-3" aria-label="Legenda das situações">
            <div v-for="(situacao, chave) in situacoes" :key="chave" class="situacao-legenda-item" :title="situacao.descricao" tabindex="0" :aria-label="`${situacao.label}: ${situacao.descricao}`">
              <span class="situacao-cor" :style="{ backgroundColor: situacao.cor }" aria-hidden="true"></span>
              <div>
                <strong>{{ situacao.label }}</strong>
              </div>
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
                <p><strong>Data de Início Previsto:</strong> {{ formatarData(selectedEvent.start) }}</p>
                <p><strong>Data de Entrega Prevista:</strong> {{ formatarData(selectedEvent.end) }}</p>
                <p><strong>Versão 00 Data de Entrega:</strong> {{ formatarData(selectedEvent.versao_00_data_de_entrega) }}</p>
                <p><strong>Versão Aceita Data:</strong> {{ formatarData(selectedEvent.versao_aceita_data) }}</p>
                <p><strong>Requisição Externa Data:</strong> {{ formatarData(selectedEvent.req_ext_data) }}</p>
                <p><strong>Autorização Externa Data:</strong> {{ formatarData(selectedEvent.aut_ext_data) }}</p>
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
.cronograma-layout.card { background: transparent; border: 0; box-shadow: none; padding: 0; }
.cronograma-layout :deep(> .d-flex) { display: grid !important; grid-template-columns: 190px minmax(0, 1fr); gap: 20px; align-items: start; }
.cronograma-layout :deep(> .d-flex > .col-md-1) { width: auto; padding: 12px 8px; border: 1px solid #e2e8f0; border-radius: 8px; background: #fff; }
.cronograma-layout :deep(> .d-flex > .col-md-11) { width: auto; min-width: 0; }
.cronograma-layout :deep(.navbar-nav .nav-link) { padding: 12px 10px; border-radius: 6px; }
.cronograma-conteudo { min-width: 0; border-color: #e2e8f0; border-radius: 8px; padding: 20px; }
@media (max-width: 1199px) {
  .cronograma-layout :deep(> .d-flex) { grid-template-columns: 170px minmax(0, 1fr); gap: 16px; }
}
@media (max-width: 767px) {
  .cronograma-layout :deep(> .d-flex) { grid-template-columns: 1fr; }
  .cronograma-layout :deep(.navbar-nav) { flex-direction: row; flex-wrap: wrap; gap: 4px; }
  .cronograma-conteudo { padding: 16px; }
}
.situacao-legenda { display: flex; flex-wrap: wrap; gap: 10px 24px; padding: 10px 0; }
.situacao-legenda-item { display: flex; align-items: center; gap: 8px; color: #475569; cursor: help; font-size: 0.875rem; }
.situacao-legenda-item strong { font-weight: 500; }
.situacao-legenda-item:focus-visible { outline: 2px solid #93c5fd; outline-offset: 4px; border-radius: 3px; }
.situacao-cor { width: 11px; height: 11px; border-radius: 3px; flex-shrink: 0; }
.title-cronograma { font-size: 2rem; text-align: center; margin: 1.5rem 0; }
.filter-item { flex: 1 1 220px; min-width: 0; }
.filter-item .form-label { margin-bottom: 6px; color: #475569; }
.filter-item .filter-select { width: 100%; height: 38px; border-radius: 6px; border: 1px solid #cbd5e1; padding: 8px 32px 8px 10px; }
.filter-item .filter-select:focus { border-color: #93c5fd; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.12); }
.filter-button .btn { height: 38px; border-radius: 6px; white-space: nowrap; }
.custom-calendar { background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; box-shadow: 0 1px 4px rgba(15, 23, 42, 0.04); }
.custom-calendar :deep(.fc) { --fc-border-color: #e2e8f0; --fc-neutral-bg-color: #f8fafc; --fc-today-bg-color: #eff6ff; }
.custom-calendar :deep(.fc-multimonth) { border-radius: 6px; }
.custom-calendar :deep(.fc-multimonth-month) { background-color: #ffffff; }
.custom-calendar :deep(.fc-col-header-cell-cushion),
.custom-calendar :deep(.fc-daygrid-day-number) { color: #475569; text-decoration: none; }
.custom-calendar :deep(.fc-multimonth-title) { color: #334155; font-weight: 600; }
.cronograma-layout :deep(.navbar-nav .nav-item.active > .nav-link) { background-color: #eff6ff; color: #1d4ed8; border-radius: 6px; }
.modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); display: flex; justify-content: center; align-items: center; z-index: 1000; }
.modal-content { background: white; padding: 20px; border-radius: 10px; max-width: 500px; width: 90%; max-height: 80vh; overflow-y: auto; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); }
.modal-content h4 { margin-bottom: 15px; color: #007bff; }
.form-group { margin-bottom: 15px; }
.form-control, .form-select { width: 100%; padding: 8px; border-radius: 5px; border: 1px solid #ced4da; }

/* Button Inserir Evento */
.filter-container {
  display: flex;
  gap: 16px;
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
