<script setup>

import {ref} from "vue";
import BarChart from "@/Components/BarChart.vue";
import NavButton from "@/Components/NavButton.vue";
import {IconDeviceFloppy} from "@tabler/icons-vue";
import html2canvas from "html2canvas";

const props = defineProps({
    form: {type: Object},
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

const captureChart = () => {
    let chart = null;

    chart = document.getElementById('graf_reg_lote');

    html2canvas(chart, {
        useCORS: true,
        allowTaint: true
    }).then(canvas => {
        props.form.graf_reg_lote = canvas.toDataURL('image/png');

        salvar();
    });
}

const salvar = () => {
    props.form.form = 7;

    props.form.post(route('contratos.contratada.servicos.cont_ocorrencia.resultado.store_analise', {
        contrato: props.form.contrato_id,
        servico: props.form.servico_id,
        resultado: props.form.id_resultado
    }))
}
</script>

<template>
    <div name="graf_reg_lote" id="graf_reg_lote" class="d-flex justify-content-center mb-4">
        <BarChart :style="{ height: '400px', position: 'relative' }" :chart_data="lotes"
                  :chart_options="chartOptions"/>
    </div>
    <div class="row mb-4">
        <div class="col">
            <textarea class="form-control" v-model="form.ocorr_por_lote" rows="5"></textarea>
        </div>
    </div>
    <div class="row mb-4">
        <div class="col d-flex justify-content-end">
            <NavButton @click="captureChart()" type-button="success" :icon="IconDeviceFloppy"
                       title="Salvar"/>
        </div>
    </div>
</template>

<style scoped>

</style>
