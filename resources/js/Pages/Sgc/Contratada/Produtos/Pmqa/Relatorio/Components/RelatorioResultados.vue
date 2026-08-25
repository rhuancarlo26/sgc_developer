<script setup>
import {usePage} from '@inertiajs/vue3';
import {computed} from 'vue';
import BarChart from "@/Components/BarChart.vue";

const props = defineProps({
    relatorio: {type: Object}
})

const parametrosVinculados = computed(() => {
    let parametrosUnicos = new Array();

    parametrosUnicos = props.relatorio?.resultado?.campanhas?.flatMap(campanha => campanha.pontos?.flatMap(ponto => ponto.lista?.parametros).filter(parametro => parametro !== null && parametro !== undefined).reduce((acc, curr) => {
        if (curr && !acc.some(item => item.id === curr.id)) {
            acc.push(curr);
        }
        return acc;
    }, [])) ?? [];

    let parametrosCompletos = [];

    if (parametrosUnicos) {
        parametrosCompletos = parametrosUnicos.map(parametro => {
            let analise = props.relatorio.resultado?.analises?.find(analise => analise.parametro_id === parametro.id);
            if (analise) {
                return {
                    ...parametro,
                    analise,
                };
            }

            return parametro;
        });
    }

    return parametrosCompletos;
});

const chartDataIqa = computed(() => {
    const labels = [];
    const datasets = [];

    props.relatorio?.resultado?.campanhas?.forEach(campanha => {
        const data = [];

        campanha.campanha_pontos?.forEach(campanhaPonto => {
            const ponto = campanhaPonto.ponto;
            const medicao = campanhaPonto.medicao;

            if (ponto?.nome_ponto_coleta || ponto?.nomepontocoleta) {
                labels.push(ponto.nome_ponto_coleta ?? ponto.nomepontocoleta);
            }

            if (medicao?.iqa !== null && medicao?.iqa !== undefined) {
                data.push(Number(medicao.iqa));
            }
        });

        if (data.length) {
            datasets.push({
                label: campanha.nome_campanha ?? campanha.nome,
                backgroundColor: colorFromString(campanha.nome_campanha ?? campanha.nome),
                data,
            });
        }
    });

    return {
        labels: [...new Set(labels)],
        datasets,
    };
});

const chartDataParametro = (parametroId) => {
    const datasets = [];
    let maxSize = 0;

    props.relatorio?.resultado?.campanhas?.forEach(campanha => {
        campanha.campanha_pontos?.forEach(campanhaPonto => {
            const ponto = campanhaPonto.ponto;
            const medicoes = campanhaPonto.medicao?.parametros
                ?.filter(medicaoParametro => Number(medicaoParametro.parametro_id) === Number(parametroId))
                ?.map(medicaoParametro => Number(medicaoParametro.medicao)) ?? [];

            if (medicoes.length) {
                maxSize = Math.max(maxSize, medicoes.length);
                const label = ponto?.nome_ponto_coleta ?? ponto?.nomepontocoleta ?? `Ponto ${ponto?.id ?? ''}`;

                datasets.push({
                    label,
                    backgroundColor: colorFromString(label),
                    data: medicoes,
                });
            }
        });
    });

    return {
        labels: Array.from({length: maxSize}, (_, index) => index + 1),
        datasets,
    };
};

const hasChartData = (chartData) => chartData.datasets?.some(dataset => dataset.data?.length);

const baseChartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    layout: {
        padding: {
            top: 4,
            right: 8,
            bottom: 2,
            left: 4,
        },
    },
    plugins: {
        legend: {
            position: 'bottom',
            labels: {
                boxWidth: 7,
                font: {
                    size: 8,
                },
            },
        },
        tooltip: {
            enabled: false,
        },
    },
    scales: {
        x: {
            ticks: {
                font: {
                    size: 8,
                },
            },
        },
        y: {
            beginAtZero: true,
            ticks: {
                font: {
                    size: 8,
                },
            },
        },
    },
};

const colorFromString = (value) => {
    const hash = String(value)
        .split('')
        .reduce((currentHash, char) => ((currentHash << 5) - currentHash) + char.charCodeAt(0), 0);

    return `#${Math.abs(hash).toString(16).slice(-6).padStart(6, '0')}`;
};
</script>
<template>
    <div>
        <h4>Resultados</h4>
        <hr>
        <div class="mb-4" v-if="relatorio.resultado?.analise_iqa || hasChartData(chartDataIqa)">
            <h4>IQA</h4>
            <img
                 v-if="relatorio.resultado?.analise_iqa?.graf_analise_iqa"
                 class="relatorio-chart-img mb-2"
                 :src="usePage().props.app_url + '/storage/' + relatorio.resultado?.analise_iqa?.graf_analise_iqa"
                 alt="Gráfico">
            <div v-else-if="hasChartData(chartDataIqa)" class="relatorio-chart mb-2">
                <BarChart
                     :chart_data="chartDataIqa"
                     :chart_options="baseChartOptions"
                />
            </div>

            <div>
                <span v-if="relatorio.resultado?.analise_iqa?.analise_iqa">
                    <strong>Análise: </strong>{{ relatorio.resultado?.analise_iqa?.analise_iqa }}
                </span>
            </div>
        </div>

        <div class="mb-4" v-for="parametro in parametrosVinculados" :key="parametro.id">
            <hr>
            <h4>{{ parametro.parametro }}</h4>
            <img
                 v-if="parametro.analise?.graf_analise_parametro"
                 class="relatorio-chart-img mb-2"
                 :src="usePage().props.app_url + '/storage/' + parametro.analise?.graf_analise_parametro"
                 alt="Gráfico">
            <div v-else-if="hasChartData(chartDataParametro(parametro.id))" class="relatorio-chart mb-2">
                <BarChart
                     :chart_data="chartDataParametro(parametro.id)"
                     :chart_options="{
                         ...baseChartOptions,
                         scales: {
                             ...baseChartOptions.scales,
                             x: { ...baseChartOptions.scales.x, display: false },
                         },
                         plugins: {
                             ...baseChartOptions.plugins,
                             title: {
                                 display: true,
                                 text: `Gráfico de ${parametro.parametro}`,
                                 font: { size: 10 },
                                 padding: { bottom: 3 },
                             },
                         },
                     }"
                />
            </div>

            <div v-if="parametro.analise?.analise_parametro">
                <span><strong>Análise: </strong>{{ parametro.analise?.analise_parametro }}</span>
            </div>
        </div>
    </div>
</template>

<style scoped>
.relatorio-chart {
    width: 100%;
    max-width: 600px;
    height: 155px;
    margin: 0 auto 14px;
    position: relative;
}

.relatorio-chart :deep(canvas) {
    width: 100% !important;
    height: 100% !important;
}

.relatorio-chart-img {
    display: block;
    width: 100%;
    max-width: 600px;
    max-height: 155px;
    object-fit: contain;
    margin-left: auto;
    margin-right: auto;
}
</style>
