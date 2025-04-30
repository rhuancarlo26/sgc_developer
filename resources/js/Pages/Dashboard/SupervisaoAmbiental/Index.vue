<script setup>
import PieChart from '@/Components/PieChart.vue';
import BarChart from '@/Components/BarChart.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Map from '@/Components/MapPontos.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref, computed } from 'vue';




const mapaVisualizarTrecho = ref(null);
const activeTab = ref('resultados');
const activeTabGraph = ref('rnc');
const filtro = ref('local');

const props = defineProps({
    monitora_supervisao_ambientais: Object,
    getChartDataPieLocalExecOcorrencia: Object,
    getChartDataPieTipoRncDiretoExecOcorrencia: Object,
    getChartDataPieIntensidadeExecOcorrencia: Object,
    getChartDataBarExecOcorrencia: Object,
    getChartDataBarLoteIntensidadeExecOcorrencia: Object,
    lotes: Array,
    registros: Array,
});


const chartOptions = ref({

    responsive: true,
    plugins: {
        legend: { display: true, position: 'bottom', },
        tooltip: { enabled: true },
    },
    scales: {
        x: {
            grid: { display: false },
            ticks: {

                minRotation: 90,
                maxRotation: 90,
            },
        },
        y: {
            beginAtZero: true,
            grid: { drawBorder: false },
        },
    },
});



const chartOptionsPie = ref({
    responsive: true,
    plugins: {
        legend: {
            display: true,
            position: 'bottom',
        },
        tooltip: {
            enabled: true,
        },
        datalabels: {
            anchor: "end",
            align: "end",
            offset: -50,
            formatter: (value, context) => {
                const dataArr = context.chart.data.datasets[0].data;
                const total = dataArr.reduce((sum, val) => sum + val, 0);
                const percentage = ((value / total) * 100).toFixed(2);
                return `${value} (${percentage}%)`;
            },
            color: 'black',
        },
    },
});


const trechosVisualizacao = computed(() => {
    let geojson = [];

    props.monitora_supervisao_ambientais.forEach(monitora_supervisao_ambiental => {
        const longitude = Number(monitora_supervisao_ambiental.longitude);
        const latitude = Number(monitora_supervisao_ambiental.latitude);

        geojson.push([
            JSON.stringify({
                type: "Feature",
                geometry: {
                    type: "Point",
                    coordinates: [longitude, latitude]
                }
            }),
            modalPontoRegistro(monitora_supervisao_ambiental),
            monitora_supervisao_ambiental
        ]);
    });

    return geojson;
});


const filtroRoa = ref(false)
const filtroRnc = ref(false)
const filtroIntensidades = ref([])
const filtroSituacao = ref('')
const filtroVistoria = ref('')
const filtroLote = ref('')

const situacoes = ['aberta', 'Atendida']
const intensidades = ['Leve', 'Moderada', 'Grave']


const filteredRegistros = computed(() =>
    props.registros.filter(r => {
        // Tipo
        if (filtroRoa.value && r.tipo !== 'ROA') return false
        if (filtroRnc.value && r.tipo !== 'RNC') return false

        // Situação
        if (filtroSituacao.value && r.status !== filtroSituacao.value) return false

        // Lote
        if (filtroLote.value && r.lote_nome !== filtroLote.value) return false

        // Intensidade
        if (filtroIntensidades.value.length > 0
            && !filtroIntensidades.value.includes(r.intensidade)) {
            return false
        }

        // Vistoria
        if (filtroVistoria.value === 'com' && r.vistoria === 0) return false
        if (filtroVistoria.value === 'sem' && r.vistoria > 0) return false

        return true
    })
)


const modalPontoRegistro = (registro) => {
    return `
    <span><strong>ID:</strong> ${registro.id}</span><br>
    <span><strong>Número por Serviço:</strong> ${registro.num_por_servico}</span><br>
    <span><strong>Nome ID:</strong> ${registro.nome_id}</span><br>
    <span><strong>Rodovia (ID):</strong> ${registro.id_rodovia}</span><br>
    <span><strong>Data de Ocorrência:</strong> ${registro.data_ocorrencia}</span><br>
    <span><strong>KM:</strong> ${registro.km}</span><br>
    <span><strong>Estaca:</strong> ${registro.estaca}</span><br>
    <span><strong>Lado:</strong> ${registro.lado}</span><br>
    <span><strong>Latitude:</strong> ${registro.latitude}</span><br>
    <span><strong>Longitude:</strong> ${registro.longitude}</span><br>
    <span><strong>Zona:</strong> ${registro.zona || 'N/A'}</span><br>
    <span><strong>Lote (ID):</strong> ${registro.id_lote}</span><br>
    <span><strong>Indício de Responsabilidade:</strong> ${registro.indicio_responsabilidade}</span><br>
    <span><strong>Possível Causa:</strong> ${registro.possivel_causa || 'N/A'}</span><br>
    <span><strong>Descrição da Causa:</strong> ${registro.descricao_causa || 'N/A'}</span><br>
    <span><strong>Intensidade:</strong> ${registro.intensidade}</span><br>
    <span><strong>Tipo:</strong> ${registro.tipo}</span><br>
    <span><strong>RNC Direto:</strong> ${registro.rnc_direto}</span><br>
    <span><strong>Local:</strong> ${registro.local}</span><br>
    <span><strong>Classificação:</strong> ${registro.classificacao}</span><br>
    <span><strong>Descrição:</strong> ${registro.descricao}</span><br>
    <span><strong>Área Total:</strong> ${registro.area_total || 'N/A'}</span><br>
    <span><strong>Prazo (dias):</strong> ${registro.prazo}</span><br>
    <span><strong>Dias Restantes:</strong> ${registro.dias_restantes}</span><br>
    <span><strong>Ações:</strong> ${registro.acoes || 'N/A'}</span><br>
    <span><strong>Norma:</strong> ${registro.norma || 'N/A'}</span><br>
    <span><strong>Observações:</strong> ${registro.obs || 'N/A'}</span><br>
    <span><strong>Status:</strong> ${registro.status}</span><br>
    <span><strong>Aprovado RNC:</strong> ${registro.aprovado_rnc}</span><br>
    <span><strong>Parecer Fiscal:</strong> ${registro.parecer_fiscal || 'N/A'}</span><br>
    <span><strong>Envio Empresa:</strong> ${registro.envio_empresa}</span><br>
    <span><strong>Envio Fiscal:</strong> ${registro.envio_fiscal}</span><br>
    <span><strong>Criado em:</strong> ${new Date(registro.created_at).toLocaleString()}</span><br>
    <span><strong>Atualizado em:</strong> ${new Date(registro.updated_at).toLocaleString()}</span><br>
  `;
};


setTimeout(() => {
    mapaVisualizarTrecho.value.renderMapa()
    mapaVisualizarTrecho.value.setLinestrings(trechosVisualizacao.value, true);
}, 500);


</script>

<template>

    <Head title="Dashboard Supervisao Ambiental" />
    <AuthenticatedLayout>
        <div class="card card-body mb-4">
            <h1 class="text-center mb-4">Programa de Supervisao Ambiental</h1>
        </div>
        <div class="row">
            <div class="col-lg-7 mb-4">
                <div class="card card-body">
                    <Map ref="mapaVisualizarTrecho" height="800px" :manual-render="true" />
                </div>
            </div>
            <div class="col-lg-5">
                <div class="card card-body mb-4">
                    <div class="row text-center">
                        <div class="col-4">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="option" id="resultados"
                                    @click="activeTab = 'resultados'" checked>
                                <label class="form-check-label" for="resultados">Resultados</label>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="option" id="registros"
                                    @click="activeTab = 'registros'">
                                <label class="form-check-label" for="registros">Registros</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div v-if="activeTab === 'resultados'" class="row mt-3">
                    <!-- Gráfico de pizza filtro local/tipo -->
                    <div class="col-md-6 mb-3">
                        <div class="card h-100">
                            <div class="card-header d-flex align-items-center">
                                <div class="form-check me-3">
                                    <input class="form-check-input" type="radio" name="filtro" id="filtroLocal"
                                        v-model="filtro" value="local" />
                                    <label class="form-check-label" for="filtroLocal">
                                        Local de Ocorrência
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="filtro" id="filtroTipo"
                                        v-model="filtro" value="tipo" />
                                    <label class="form-check-label" for="filtroTipo">
                                        Tipo de Ocorrência
                                    </label>
                                </div>
                            </div>

                            <div v-if="filtro === 'tipo'"
                                class="card-body d-flex justify-content-center align-items-center">
                                <PieChart :chart_data="props.getChartDataPieTipoRncDiretoExecOcorrencia"
                                    :chart_options="chartOptionsPie" />
                            </div>

                            <div v-if="filtro === 'local'"
                                class="card-body d-flex justify-content-center align-items-center">
                                <PieChart :chart_data="props.getChartDataPieLocalExecOcorrencia"
                                    :chart_options="chartOptionsPie" />
                            </div>
                        </div>
                    </div>

                    <!-- Gráfico de pizza intensidade -->
                    <div class="col-md-6 mb-3">
                        <div class="card h-100">
                            <div class="card-header">
                                Intensidade
                            </div>
                            <div class="card-body d-flex justify-content-center align-items-center">
                                <PieChart :chart_data="props.getChartDataPieIntensidadeExecOcorrencia"
                                    :chart_options="chartOptionsPie" />
                            </div>
                        </div>
                    </div>


                    <!-- Gráfico de barras RNC / histórico -->
                    <div class="row">
                        <div class="col mb-3">
                            <div class="card">
                                <div class="card-header d-flex justify-content-center">
                                    <div class="form-check me-3">
                                        <input class="form-check-input" type="radio" name="optionGraph" id="rnc"
                                            @click="activeTabGraph = 'rnc'" checked />
                                        <label class="form-check-label" for="rnc">
                                            RNC por lote de obra
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="optionGraph" id="historico"
                                            @click="activeTabGraph = 'historico'" />
                                        <label class="form-check-label" for="historico">
                                            Histórico
                                        </label>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div v-if="activeTabGraph === 'rnc'">
                                        <BarChart :chart_data="props.getChartDataBarLoteIntensidadeExecOcorrencia"
                                            :chart_options="chartOptions" />
                                    </div>
                                    <div v-if="activeTabGraph === 'historico'">
                                        <BarChart :chart_data="props.getChartDataBarExecOcorrencia"
                                            :chart_options="chartOptions" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <div v-if="activeTab === 'registros'" class="row mt-3">
                    <div class="card mb-4">
                        <div class="card-body">
                            <!-- FILTROS -->
                            <div class="row mb-4">
                                <!-- Tipo de Registro -->
                                <div class="col-md-4">
                                    <InputLabel value="Tipo de Registro" />
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="filtroRoa"
                                            v-model="filtroRoa" />
                                        <label class="form-check-label" for="filtroRoa">ROA</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="filtroRnc"
                                            v-model="filtroRnc" />
                                        <label class="form-check-label" for="filtroRnc">RNC</label>
                                    </div>
                                </div>

                                <!-- Intensidade -->
                                <div class="col-md-4">
                                    <InputLabel value="Intensidade" />
                                    <div v-for="int in intensidades" :key="int" class="form-check">
                                        <input class="form-check-input" type="checkbox" :id="`filtroInt-${int}`"
                                            :value="int" v-model="filtroIntensidades" />
                                        <label class="form-check-label" :for="`filtroInt-${int}`">{{ int }}</label>
                                    </div>
                                </div>

                                <!-- Situação -->
                                <div class="col-md-4">
                                    <InputLabel value="Situação" />
                                    <select class="form-select" v-model="filtroSituacao">
                                        <option value="">Todos</option>
                                        <option v-for="sit in situacoes" :key="sit" :value="sit">{{ sit }}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row mb-4">
                                <!-- Vistoria -->
                                <div class="col-md-6">
                                    <InputLabel value="Vistoria" />
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" id="vistTodos" value=""
                                            v-model="filtroVistoria" />
                                        <label class="form-check-label" for="vistTodos">Todos</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" id="vistCom" value="com"
                                            v-model="filtroVistoria" />
                                        <label class="form-check-label" for="vistCom">Com</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" id="vistSem" value="sem"
                                            v-model="filtroVistoria" />
                                        <label class="form-check-label" for="vistSem">Sem</label>
                                    </div>
                                </div>

                                <!-- Lote de Obra -->
                                <div class="col-md-6">
                                    <InputLabel value="Lote de Obra" />
                                    <select class="form-select" v-model="filtroLote">
                                        <option value="">Todos</option>
                                        <option v-for="l in lotes" :key="l" :value="l">{{ l }}</option>
                                    </select>
                                </div>
                            </div>

                            <!-- TABELA -->
                            <div class="table-scroll">
                                <table class="table table-bordered mb-0">
                                    <thead>
                                        <tr>
                                           
                                            <th>Descrição</th>
                                            <th>Tipo</th>
                                            <th>Intensidade</th>
                                            <th>Status</th>
                                            <th>Vistorias</th>
                                            <th>Data</th>
                                            <th>Lote</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="r in filteredRegistros" :key="r.id">
                                            <td>{{ r.descricao }}</td>
                                            <td>{{ r.tipo }}</td>
                                            <td>{{ r.intensidade }}</td>
                                            <td>{{ r.status }}</td>
                                            <td>{{ r.vistoria }}</td>
                                            <td>{{ r.data_ocorrencia }}</td>
                                            <td>{{ r.lote_nome }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.table-scroll {
    max-height: 900px;

    overflow-y: auto;
}

.table-scroll .table {
    margin-bottom: 0;
}
</style>
