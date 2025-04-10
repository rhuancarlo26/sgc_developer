<script setup>

import PieChart from "@/Components/PieChart.vue";
import {ref} from "vue";
import InputLabel from "@/Components/InputLabel.vue";
import NavButton from "@/Components/NavButton.vue";
import html2canvas from "html2canvas";

const props = defineProps({
    contrato: {type: Object},
    servico: {type: Object},
    resultado: {type: Object},
    form: {type: Object},
    tipos: {type: Object}
})

const chartOptions = ref({
    responsive: true,
    plugins: {
        legend: {
            position: 'top',
        },
        title: {
            display: true,
            text: 'Registros por Tipos',
        },
    }
});

const captureChart = () => {
    let chart = null;

    chart = document.getElementById('graf_reg_tipo');

    html2canvas(chart, {
        useCORS: true,
        allowTaint: true
    }).then(canvas => {
        props.form.graf_reg_tipo = canvas.toDataURL('image/png');

        salvar();
    });
}

const salvar = () => {
    props.form.id = props.resultado.analise?.id;
    props.form.form = 4;

    const url = props.form.id ? 'update_analise' : 'store_analise'

    props.form.post(route('contratos.contratada.servicos.passagem_fauna.resultado.' + url, {
        contrato: props.contrato.id,
        servico: props.servico.id,
        resultado: props.resultado.id
    }))
}
</script>

<template>
    <div name="graf_reg_tipo" id="graf_reg_tipo" class="d-flex justify-content-center mb-4">
        <PieChart :style="{ height: '400px', position: 'relative' }" :chart_data="tipos"
                  :chart_options="chartOptions"/>
    </div>
    <div class="row mb-4">
        <div class="col">
            <InputLabel value="Análise" for="registros_classe"/>
            <textarea class="form-control" name="registros_tipo" id="registros_tipo" v-model="form.registros_tipo"
                      rows="5"></textarea>
        </div>
    </div>
    <div class="d-flex justify-content-end">
        <NavButton @click="captureChart()" type-button="success" :title="'Salvar'"/>
    </div>
</template>

<style scoped>

</style>
