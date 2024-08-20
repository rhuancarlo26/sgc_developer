<script setup>

import {ref} from "vue";
import BarChart from "@/Components/BarChart.vue";

const props = defineProps({
    lotes: {type: Object}
});

const chartOptions = ref({
    responsive: true,
    plugins: {
        legend: {
            position: 'top',
        },
        title: {
            display: true,
            text: 'Ocorrência por lotes',
        },
    }
});
</script>

<template>
    <div class="d-flex justify-content-center">
        <BarChart :chart_data="lotes"
                  :chart_options="chartOptions"/>
    </div>
</template>

<style scoped>

</style>
