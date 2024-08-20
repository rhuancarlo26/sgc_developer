<script setup>

import {ref} from "vue";
import BarChart from "@/Components/BarChart.vue";

const props = defineProps({
    locais: {type: Object}
});

const chartOptions = ref({
    indexAxis: 'y',
    responsive: true,
    plugins: {
        legend: {
            position: 'top',
        },
        title: {
            display: true,
            text: 'Ocorrência por local',
        },
    }
});
</script>

<template>
    <div class="d-flex justify-content-center">
        <BarChart :chart_data="locais"
                  :chart_options="chartOptions"/>
    </div>
</template>

<style scoped>

</style>
