<script setup>
import FullCalendar from "@fullcalendar/vue3";
import dayGridPlugin from "@fullcalendar/daygrid";
import interactionPlugin from "@fullcalendar/interaction";
import multiMonthPlugin from "@fullcalendar/multimonth";
import ptBrLocale from "@fullcalendar/core/locales/pt-br";
import { ref, watchEffect } from "vue";

// Recebe os eventos como propriedade da view
const props = defineProps({
  events: {
    type: Array,
    default: () => [],
  },
});

const emit = defineEmits(['event-click']); // Removido 'add-event'

// Função para gerar cores com base no source e, opcionalmente, no title
const getEventColor = (event) => {
  // Se for evento auxiliar, retorna verde fixo
  if (event.extendedProps?.source === 'auxiliar') {
    return '#28a745'; // Verde fixo para eventos auxiliares
  }

  // Para eventos principais, mantém a lógica baseada no title
  const hash = event.title.split('').reduce((acc, char) => acc + char.charCodeAt(0), 0);
  const colors = [
    '#3d85c6', '#111db7', '#187589', '#6d7486', '#6d7486',
  ];
  return colors[hash % colors.length];
};

const calendarOptions = ref({
  plugins: [dayGridPlugin, interactionPlugin, multiMonthPlugin],
  locale: ptBrLocale,
  initialView: "multiMonthYear",
  headerToolbar: {
    left: "prev,next today",
    center: "title",
    right: "dayGridMonth,multiMonthYear",
  },
  events: [],
  eventDidMount: (info) => {
    const eventColor = getEventColor(info.event);
    info.el.style.backgroundColor = eventColor;
    info.el.style.borderColor = eventColor;

    // Exibe o título apenas no primeiro segmento do evento
    if (!info.isStart) {
      const titleElement = info.el.querySelector('.fc-event-title');
      if (titleElement) {
        titleElement.style.display = 'none'; // Oculta o título nos segmentos seguintes
      }
    }
  },
  editable: true,
  eventClick: handleEventClick,
  multiMonthMaxColumns: 3,
  multiMonthMinWidth: 250,
});

watchEffect(() => {
  console.log("Atualizando calendário com eventos do backend:", props.events);
  calendarOptions.value.events = [...props.events];
});

function handleEventClick(info) {
  emit('event-click', info);
}
</script>

<template>
  <FullCalendar :options="calendarOptions" />
</template>