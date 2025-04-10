<script setup>
import { onMounted, reactive } from 'vue';
import { ref } from 'vue';
import Map from "@/Components/MapSgc.vue";
import { Chart } from "highcharts-vue";

const props = defineProps({
    contrato: Object,
    empreendimentos: Object,
    estudos: Object,
});

const coordenadas = props.empreendimentos.map(trecho => trecho.coordenadas);
const mapaVisualizarTrecho = ref();

const chartOptionsRadio = reactive({
    chart: {
        type: "pie",
        backgroundColor: "transparent",
        height: 300,
        legend: { enabled: true },
    },
    title: {
        text: "R$-OSE x Medido",
        align: "left",
        color: "white",
    },
    subtitle: {
        text: 'Fonte: <a target="_blank" href="https://www.google.com.br">DNIT</a>',
        align: "left",
    },
    xAxis: {
        categories: ["Info 1", "Info 2", "Info 3"],
    },
    yAxis: {
        min: 0,
        title: {
            text: "Km²",
        },
    },
    plotOptions: {
        series: {
            allowPointSelect: true,
            cursor: "pointer",
            dataLabels: {
                enabled: true,
                distance: 20,
            },
        },
        pie: {
            innerSize: '80%',
            startAngle: 20,
        }
    },
    series: [
        {
            data: [1, 4, 3],
        },
    ]
});

const updateChartOptions = (emp) => {
    emp.forEach(element => {
        let list = [];
        let somaOse = 0;
        let somaMedidas = 0;
        props.estudos.forEach(value => {
            if (value.cod_emp === element.cod_emp) {
                list.push(value.r_ose);
                somaOse += Number(value.r_ose);
                somaMedidas += (Number(value.medicao_40_qtd) + Number(value.medicao_60_qtd)) * Number(value.qtd_ose);
            }
        });
        chartOptionsRadio.xAxis.categories = list;
        chartOptionsRadio.series[0].data = [
            {
                name: 'R$ OSE',
                y: Number(somaOse.toFixed(0)),
                color: 'lightgray'
            },
            {
                name: 'Valor Medido',
                y: Number(somaMedidas.toFixed(0)),
                color: 'green'
            }
        ];
    });
};

const visualizarTrecho = () => {
    mapaVisualizarTrecho.value.renderMapa();
    setTimeout(() => {
        mapaVisualizarTrecho.value.setGeoJson(coordenadas, 'blue', 6, 'teste');
    }, 500);
};

onMounted(() => {
    visualizarTrecho();
    updateChartOptions(props.empreendimentos);
});

defineExpose({ visualizarTrecho });

</script>

<template>
    <div class="col-md-12">
        <div class="clearfix mb-4"></div>
        <div class="d-flex justify-content-between">
            <div class="card me-3" style="flex: 1;">
                <Map ref="mapaVisualizarTrecho" height="450px" width="100%"/>
            </div>
            <div class="card ms-3" style="flex: 1;">
                <div v-for="empreendimento in empreendimentos" :key="empreendimento.id">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item"><strong>BR/UF:</strong> {{ empreendimento.br }}/{{ empreendimento.uf }}</li>
                        <li class="list-group-item"><strong>Subtrecho:</strong> {{ empreendimento.subtrecho_ini }}</li>
                        <li class="list-group-item"><strong>Segmento:</strong> Faltante</li>
                        <li class="list-group-item"><strong>Extensão:</strong> {{ empreendimento.extensao }}</li>
                        <li class="list-group-item"><strong>Tipo de Intervenção:</strong> {{ empreendimento.tipo_de_intervencao }}</li>
                        <li class="list-group-item"><strong>Descrição:</strong> {{ empreendimento.descricao }}</li>
                        <li class="list-group-item"><strong>Bioma:</strong> {{ empreendimento.bioma }}</li>
                    </ul>
                </div>
            </div>
            <div v-for="empreendimento in empreendimentos" :key="empreendimento.id" class="card ms-3" style="flex: 1;">
                <h3 class="ms-3">LP</h3>
                <div class="progress mx-3" role="progressbar" aria-label="Progress LP" :aria-valuenow="empreendimento.lp_avanco != '#DIV/0!' ? Number(empreendimento.lp_avanco) * 100 : 0" aria-valuemin="0" aria-valuemax="100">
                    <div class="progress-bar progress-bar-striped progress-bar-animated" :style="{ width: (empreendimento.lp_avanco != '#DIV/0!' ? Number(empreendimento.lp_avanco) * 100 : 0) + '%' }">
                        {{ empreendimento.lp_avanco != '#DIV/0!' ? (Number(empreendimento.lp_avanco) * 100).toFixed(0) + ' %' : '' }}
                    </div>
                </div>
                <h3 class="ms-3">LI</h3>
                <div class="progress mx-3" role="progressbar" aria-label="Progress LI" :aria-valuenow="empreendimento.li_avanco != '#DIV/0!' ? Number(empreendimento.li_avanco) * 100 : 0" aria-valuemin="0" aria-valuemax="100">
                    <div class="progress-bar progress-bar-striped progress-bar-animated" :style="{ width: (empreendimento.li_avanco != '#DIV/0!' ? Number(empreendimento.li_avanco) * 100 : 0) + '%' }">
                        {{ empreendimento.li_avanco != '#DIV/0!' ? (Number(empreendimento.li_avanco) * 100).toFixed(0) + ' %' : '' }}
                    </div>
                </div>
                <Chart :options="chartOptionsRadio"></Chart>
            </div>
        </div>
        <br>
    </div>
</template>

<style>
.progress {
    height: 30px;
    width: 70%;
    margin: 10px 20px;
}

.progress-bar {
    font-size: 1rem;
}

h3 {
    margin: 0 20px;
}
</style>
