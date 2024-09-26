<script setup>

import BarChart from "@/Components/BarChart.vue";
import {ref} from "vue";
import InputLabel from "@/Components/InputLabel.vue";
import html2canvas from "html2canvas";
import NavButton from "@/Components/NavButton.vue";

const props = defineProps({
    contrato: {type: Object},
    servico: {type: Object},
    resultado: {type: Object},
    form: {type: Object},
    campanhas: {type: Object}
})

const chartOptions = ref({
    responsive: true,
    plugins: {
        legend: {
            position: 'top',
        },
        title: {
            display: true,
            text: 'Registros por classes',
        },
    }
});

const captureChart = () => {
    let chart = null;

    chart = document.getElementById('graf_reg_campanha');

    html2canvas(chart, {
        useCORS: true,
        allowTaint: true
    }).then(canvas => {
        props.form.graf_reg_campanha = canvas.toDataURL('image/png');

        salvar();
    });
}

const salvar = () => {
    props.form.id = props.resultado.analise?.id;
    props.form.form = 3;

    const url = props.form.id ? 'update_analise' : 'store_analise'

    props.form.post(route('contratos.contratada.servicos.passagem_fauna.resultado.' + url, {
        contrato: props.contrato.id,
        servico: props.servico.id,
        resultado: props.resultado.id
    }))
}
</script>

<template>
    <div name="graf_reg_campanha" id="graf_reg_campanha" class="d-flex justify-content-center mb-4">
        <BarChart :style="{ height: '400px', position: 'relative' }" :chart_data="campanhas"
                  :chart_options="chartOptions"/>
    </div>
    <div class="row mb-4">
        <div class="col">
            <InputLabel value="Análise" for="registros_campanha"/>
            <textarea name="registros_campanha" id="registros_campanha" class="form-control"
                      v-model="form.registros_campanha" rows="5"></textarea>
        </div>
    </div>
    <div class="d-flex justify-content-end">
        <NavButton @click="captureChart()" type-button="success" :title="form.id ? 'Alterar' : 'Salvar'"/>
    </div>
</template>

<style scoped>

</style>
