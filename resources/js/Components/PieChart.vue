<script setup>
import {Pie} from 'vue-chartjs';
import {Chart as ChartJS, Title, Tooltip, Legend, ArcElement, CategoryScale} from 'chart.js';

ChartJS.register(Title, Tooltip, Legend, ArcElement, CategoryScale);

const props = defineProps({
    chart_data: {type: Object, default: {}},
    chart_options: {type: Object, default: {}}
});

</script>
<template>
    <Pie id="my-chart-id" :options="chart_options"
         :data="chart_data"/>
</template>
