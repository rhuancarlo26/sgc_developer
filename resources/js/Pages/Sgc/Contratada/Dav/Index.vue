<script setup>
import Breadcrumb from '@/Components/Breadcrumb.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import Navbar from '../Navbar.vue';
import { ref, watch, watchEffect } from 'vue';
import FormDav from './FormDav.vue';
import FormProfissionais from './FormProfissionais.vue';

const props = defineProps({
  contrato: Object,
  empreendimentos: Object,
  subprodutos: Object,
  produtos: Object,
  dav: Object,
  profissionais: Object,
  consumos: Object
});

const reminders = ref([]);
const attributes = ref([]);
const modalVisualizarForm = ref();
const modalVisualizarFormCadFunc = ref();
const allDav = ref([]);

const consumo = ref({
  "94/2022": {
    'Diárias': 552,
    'Deslocamentos - Veículo Leve Tipo Hatch': 73,
    'Deslocamentos - Veículo Leve Tipo Pick Up': 26,
    'Deslocamentos - Barco de Alumínio 6m e Motor de Popa 30 Hp': 0,
    'Deslocamentos - Passagem Aérea - Lote B': 38,
  },
  "93/2022": {
    'Diárias': 4778,
    'Deslocamentos - Passagem aérea - Lote A': 730,
    'Deslocamentos - Veículo leve tipo hatch': 60,
    'Deslocamentos - Veículo leve tipo Pick up': 1125,
    'Deslocamentos - Barco de alumínio 6m e motor de popa 30 HP': 334
  },
  "95/2022": {
    'Diárias': 3021,
    'Deslocamentos - Passagem aérea - Lote C': 556,
    'Deslocamentos - Veículo leve tipo hatch': 420,
    'Deslocamentos - Veículo leve tipo Pick up': 174,
    'Deslocamentos - Barco de alumínio 6m e motor de popa 30 HP': 44
  }
});

const progressValues = ref(
  props.subprodutos
    .filter(qtd => {
      const numeroFormatado = props.contrato.numero_contrato.replace(/^0+\s*|(?<=\s)0+/g, '');

      return consumo.value[numeroFormatado] && qtd.produto === '14' && numeroFormatado === qtd.contrato;
    })
    .map(qtd => {
      const numeroFormatado = props.contrato.numero_contrato.replace(/^0+\s*|(?<=\s)0+/g, '');
      const total = parseInt(qtd.qtd_contrato);

      const utilizada = consumo.value[numeroFormatado]?.[qtd.descricao_revisada] || 0;

      const percentage = total > 0 ? ((utilizada / total) * 100).toFixed(2) : 0;

      return {
        label: qtd.descricao_revisada,
        percentage: parseFloat(percentage),
        total,
        utilizada
      };
    })
);

const selectedRange = ref({
  start: new Date(),
  end: new Date(new Date().setDate(new Date().getDate() + 7)),
});

const getCalendarColor = () => {
  const statuses = reminders.value.map((reminder) => reminder.status);

  if (statuses.includes('reprovado')) {
    return 'red';
  } else if (statuses.includes('pendente')) {
    return 'yellow';
  } else if (statuses.includes('aprovado')) {
    return 'green';
  }

  return 'blue'; // Cor padrão, caso não haja lembretes
};

const updateAttributes = () => {
  attributes.value = allDav.value.map((reminder) => {
    return {
      highlight: {
        start: { fillMode: 'outline', class: `highlight-start` },
        base: { fillMode: 'light', class: `highlight-base` },
        end: { fillMode: 'outline', class: `highlight-end` },
      },
      dates: {
        start: new Date(reminder.startDate),
        end: new Date(reminder.endDate),
      },
    };
  });
};

const updateReminders = () => {
  reminders.value = allDav.value.filter((reminder) => {
    const reminderStart = new Date(reminder.startDate);
    const reminderEnd = new Date(reminder.endDate);

    return (
      reminderStart >= selectedRange.value.start &&
      reminderEnd <= selectedRange.value.end
    );
  });
};

watch(
  () => selectedRange.value,
  () => {
    updateReminders();
  },
  { immediate: true }
);

watch(
  () => reminders.value,
  () => {
    updateAttributes();
  },
  { immediate: true }
);


const adjustDate = (dateStr) => {
  const date = new Date(dateStr + 'T00:00:00Z');
  date.setHours(24, 0, 0, 0);
  return date;
};

watchEffect(() => {
  allDav.value = props.dav.map(item => (
    {
      id: item.id,
      icon: '✈️',
      title: item.empreendimento,
      startDate: adjustDate(item.dataInicio),
      endDate: adjustDate(item.dataFinal),
      status: item.status
    }));
});

// Atualiza as páginas (VDatePicker)
const updatePages = (pages) => {
  for (const week of pages) {
    const viewWeeks = week.viewWeeks || [];
    if (viewWeeks.length > 0) {
      const allDays = viewWeeks.flatMap((viewWeek) => viewWeek.days || []);
      if (allDays.length > 0) {
        const newStart = new Date(allDays[0].date);
        const newEnd = new Date(allDays[allDays.length - 1].date);

        if (
          newStart.getTime() !== selectedRange.value.start.getTime() ||
          newEnd.getTime() !== selectedRange.value.end.getTime()
        ) {
          selectedRange.value = { start: newStart, end: newEnd };
        }
        break;
      }
    }
  }
};

const getProgressBarClass = (percentage) => {
  if (percentage <= 50) {
    return 'bg-success';
  } else if (percentage <= 75) {
    return 'bg-warning';
  } else {
    return 'bg-danger';
  }
}

const abrirVisualizarFormDav = (tipo) => {
  if (tipo === 'formDav' && modalVisualizarForm.value) {
    modalVisualizarForm.value.abrirModal();
  } else if (tipo === 'cadFunc' && modalVisualizarFormCadFunc.value) {
    modalVisualizarFormCadFunc.value.abrirModal();
  } else {
    console.error("O modal especificado não está inicializado.");
  }
};

updateReminders();
</script>


<template>
  <Head :title="`${contrato.contratada.slice(0, 10)}...`" />
  <AuthenticatedLayout>
    <template #header>
      <div class="w-100 d-flex justify-content-between">
        <Breadcrumb
          class="align-self-center"
          :links="[
            { route: route('sgc.gestao.listagem', contrato.tipo_contrato), label: `Gestão de Contratos` },
            { route: '#', label: contrato.contratada }
          ]"
        />
        <div>
          <button type="button" class="btn btn-info me-3" @click="abrirVisualizarFormDav('cadFunc')">Cadastrar profissionais</button>
          <button type="button" class="btn btn-info" @click="abrirVisualizarFormDav('formDav')">Cadastrar DAV</button>
          <a class="btn btn-warning mx-3" :href="'/sgc/gestao/dav/historicos/1'">DAV's Reprovadas</a>
        </div>
      </div>
    </template>

    <Navbar :tipo="contrato">
      <template #body>
        <div class="d-flex align-items-stretch">
          <!-- Quadro de DAV (50%) -->
          <div class="card" style="flex: 1; margin-right: 20px;">
            <h3 class="m-3">DAV</h3>
            <div class="custom-vdatepicker p-2">
              <VDatePicker
                expanded
                @update:pages="updatePages"
                :attributes="attributes"
                :color="getCalendarColor()"
              >
                <template #footer>
                  <div class="reminders">
                    <h3>Dav</h3>
                    <div v-if="reminders.length === 0">
                      <p>Não há registro de viagem.</p>
                    </div>
                    <div
                      v-for="reminder in reminders"
                      :key="reminder.id"
                      class="reminder-item"
                    >
                      <span class="icon">{{ reminder.icon }}</span>
                      <div>
                        <p>{{ reminder.title }}</p>
                        <small><strong>Início:</strong> {{ reminder.startDate.toLocaleDateString('pt-BR') }}  </small>
                        <small><strong> Fim:</strong> {{ reminder.endDate.toLocaleDateString('pt-BR') }}</small>
                      </div>
                      <Link
                        class="btn btn-primary me-2 w-500"
                        :href="route('sgc.gestao.details', { contrato: contrato.id, id: reminder.id })"
                      >
                        Detalhes
                      </Link>
                    </div>
                  </div>
                </template>
              </VDatePicker>
              <div class="legend-container mt-3">
                <div class="legend-item">
                  <span class="circle circle-green"></span>
                  <span>Aprovado</span>
                </div>
                <div class="legend-item">
                  <span class="circle circle-yellow"></span>
                  <span>Pendente</span>
                </div>
                <div class="legend-item">
                  <span class="circle circle-red"></span>
                  <span>Reprovado</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Quadro de Barras de Progresso (50%) -->
          <div class="card" style="flex: 1;">
            <h3 class="m-4">Consumo</h3>
            <div class="p-5">

              <div v-for="(value, index) in progressValues" :key="index">
                <h4>{{ value.label }}</h4>
                <div class="d-flex align-items-center mb-3" style="flex: 1;">
                  <div class="progress w-75 ms-3" role="progressbar" aria-label="Success example" :aria-valuenow="value.percentage" aria-valuemin="0" aria-valuemax="100" style="height: 20px;">
                    <div
                      class="progress-bar"
                      :class="getProgressBarClass(value.percentage)"
                      :style="{ width: value.percentage + '%' }">
                      {{ value.utilizada }}
                    </div>
                  </div>
                  <p class="ms-3" style="margin-top: 5px;">Total: {{ value.total }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </template>
    </Navbar>
    <FormDav ref="modalVisualizarForm"
             :contrato="contrato"
             :subprodutos="subprodutos"
             :produtos="produtos"
             :empreendimentos="empreendimentos"
             :profissionais="profissionais"
             :consumos="consumos"/>
    <FormProfissionais ref="modalVisualizarFormCadFunc"
                       :contrato="contrato"
                       :profissionais="profissionais"/>
  </AuthenticatedLayout>
</template>

<style scoped>
.custom-vdatepicker {
  width: 100%;
  height: auto;
  font-size: 1.2rem;
}

.reminders {
  padding: 10px;
  border-radius: 10px;
  color: #0c0b0b;
}

.reminder-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 10px;
  margin-bottom: 10px;
  border-bottom: 1px solid #3a3a55;
}

.reminder-item:last-child {
  border-bottom: none;
}

.reminder-item .icon {
  font-size: 24px;
  margin-right: 10px;
}
.legend-container {
  display: flex;
  justify-content: center;
  gap: 20px;
  margin-top: 20px;
}

.legend-item {
  display: flex;
  align-items: center;
  gap: 10px;
  font-size: 14px;
  color: #333;
}

.circle {
  width: 16px;
  height: 16px;
  border-radius: 50%;
  display: inline-block;
}

.circle-green {
  background-color: green;
}

.circle-yellow {
  background-color: yellow;
}

.circle-red {
  background-color: red;
}
</style>

