<script setup>
import PieChart from "@/Components/PieChart.vue";
import {ref} from "vue";

const props = defineProps({
    intensidades: {type: Object}
});

const chartOptions = ref({
    responsive: true,
    plugins: {
        legend: {
            position: 'top',
        },
        title: {
            display: true,
            text: 'RNC gerados',
        },
    }
});
</script>

<template>
    <div class="d-flex justify-content-center">
        <PieChart :style="{ height: '320px', position: 'relative' }" :chart_data="intensidades"
                  :chart_options="chartOptions"/>
    </div>
</template>

<style scoped>

</style>
