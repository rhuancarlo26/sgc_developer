<script setup>
import { Pie } from 'vue-chartjs';
import ChartDataLabels from 'chartjs-plugin-datalabels';
import { Chart as ChartJS, Title, Tooltip, Legend, ArcElement, CategoryScale, LinearScale } from 'chart.js';
import annotationPlugin from 'chartjs-plugin-annotation';

const props = defineProps({
  chart_data: { type: Object, default: () => ({ labels: [], datasets: [] }) },
  chart_options: { type: Object, default: () => ({}) }
});

ChartJS.register(Title, Tooltip, Legend, ArcElement, CategoryScale, LinearScale, annotationPlugin, ChartDataLabels);
</script>

<template>
  <Pie id="my-pie-chart-id" :options="chart_options" :data="chart_data" />
</template>
