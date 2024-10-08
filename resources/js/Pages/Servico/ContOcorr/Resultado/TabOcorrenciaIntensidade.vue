<script setup>
import PieChart from "@/Components/PieChart.vue";
import { ref } from "vue";
import html2canvas from "html2canvas";
import { IconDeviceFloppy } from "@tabler/icons-vue";
import NavButton from "@/Components/NavButton.vue";

const props = defineProps({
    resultado: { type: Object },
    form: { type: Object },
    intensidades: { type: Object }
});

const chartOptions = ref({
    responsive: true,
    plugins: {
        tooltip: {
            enabled: false
        },
        datalabels: {
            display: true,
            color: 'white',
            font: {
                weight: 'bold',
            },
        },
        legend: {
            position: 'top',
        },
        title: {
            display: true,
            text: 'RNC gerados',
        },
    }
});

const captureChart = () => {
    let chart = null;

    chart = document.getElementById('graf_reg_intensidade');

    html2canvas(chart, {
        useCORS: true,
        allowTaint: true
    }).then(canvas => {
        props.form.graf_reg_intensidade = canvas.toDataURL('image/png');

        salvar();
    });
}

const salvar = () => {
    props.form.id = props.resultado.analise?.id;
    props.form.form = 5;

    props.form.post(route('contratos.contratada.servicos.cont_ocorrencia.resultado.store_analise', {
        contrato: props.form.contrato_id,
        servico: props.form.servico_id,
        resultado: props.form.id_resultado
    }))
}
</script>

<template>
    <div name="graf_reg_intensidade" id="graf_reg_intensidade" class="d-flex justify-content-center mb-4">
        <PieChart :style="{ height: '400px', position: 'relative' }" :chart_data="intensidades"
            :chart_options="chartOptions" />
    </div>
    <div class="row mb-4">
        <div class="col">
            <textarea class="form-control" v-model="form.ocorr_por_intensidade" rows="5"></textarea>
        </div>
    </div>
    <div class="row mb-4">
        <div class="col d-flex justify-content-end">
            <NavButton @click="captureChart()" type-button="success" :icon="IconDeviceFloppy" title="Salvar" />
        </div>
    </div>
</template>

<style scoped></style>
