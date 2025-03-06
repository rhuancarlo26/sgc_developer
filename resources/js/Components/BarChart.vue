<script setup>
import {Bar} from 'vue-chartjs';
import ChartDataLabels from 'chartjs-plugin-datalabels';
import {Chart as ChartJS, Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale} from 'chart.js';
import annotationPlugin from 'chartjs-plugin-annotation';

const props = defineProps({
    chart_data: {type: Object, default: {}},
    chart_options: {type: Object, default: {}}
});

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale, annotationPlugin, ChartDataLabels)

</script>
<template>
    <Bar id="my-chart-id" :options="chart_options" :data="chart_data"/>
</template>
