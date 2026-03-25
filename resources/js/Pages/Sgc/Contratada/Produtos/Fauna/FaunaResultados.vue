<script setup>
import InputError from '@/Components/InputError.vue';
import NavButton from '@/Components/NavButton.vue';
import { ref, computed, watch, onMounted, nextTick } from 'vue';
import * as XLSX from 'xlsx';
import Highcharts from 'highcharts';

const props = defineProps({
    formResultados: { type: Object, required: true },
    resultadosRecords: { type: Object, default: () => ({}) },
    idCampanha: { type: [String, Number], required: true },
});

const emit = defineEmits(['update:resultadosRecords', 'prev', 'next']);



// ESTADOS PRINCIPAIS

const consideracoes = ref(props.formResultados.consideracoes ?? '');

const initial = props.resultadosRecords || {};
const resultados = ref({
    terrestre: Array.isArray(initial.terrestre) ? initial.terrestre : [],
    aquatica: Array.isArray(initial.aquatica) ? initial.aquatica : [],
    cavernicola: Array.isArray(initial.cavernicola) ? initial.cavernicola : [],
});

/* Abas */
const activeTipo = ref('terrestre');

const tipos = [
    { key: 'terrestre', label: 'Fauna Terrestre' },
    { key: 'aquatica', label: 'Fauna Aquática' },
    { key: 'cavernicola', label: 'Fauna Cavernícola' },
];

const labelTipoAtivo = computed(() => tipos.find(t => t.key === activeTipo.value)?.label || '');
const previewAtivo = computed(() => resultados.value[activeTipo.value] || []);

// GERADOR DE CORES — PASTEL E FRIO
function pastelColor() {
    // Escolhe um dos grupos de cor: azul, verde ou amarelo
    const grupo = Math.floor(Math.random() * 3);

    let hue;

    if (grupo === 0) {
        // 🔵 Azul discreto — 190 a 230
        hue = 190 + Math.random() * 40;
    } else if (grupo === 1) {
        // 🟢 Verde suave — 90 a 140
        hue = 90 + Math.random() * 50;
    } else {
        // 🟡 Amarelo pastel — 40 a 60
        hue = 40 + Math.random() * 20;
    }

    const saturation = 35 + Math.random() * 20;   // 35–55% (mais suave)
    const lightness  = 65 + Math.random() * 10;   // 65–75% (leve, mas visível)

    return `hsl(${hue}, ${saturation}%, ${lightness}%)`;
}


// Mapa de cores fixas por classe/grupo
const classColorMap = {};

function colorByClass(classe) {
    const key = String(classe || '').trim().toLowerCase();
    if (!key) return '#cccccc';

    if (!classColorMap[key]) {
        classColorMap[key] = pastelColor();
    }
    return classColorMap[key];
}

// MAPEAMENTO DE CLASSES (Terrestre usa grupos)
function mapClasseToGrupo(classeRaw) {
    /* Aquática e Cavernícola → usar Classe pura */
    if (activeTipo.value !== 'terrestre') {
        return String(classeRaw || '').trim() || 'Não classificado';
    }

    /* Terrestre → mapeamento oficial */
    const c = String(classeRaw).trim().toLowerCase();
    if (c === 'aves') return 'Avifauna';
    if (c === 'mammalia') return 'Mastofauna';
    if (c === 'reptilia' || c === 'amphibia') return 'Herpetofauna';
    return 'Não classificado';
}

// AUXILIAR DE ESPÉCIES
function getEspecieFromLinha(linha) {
    return (
        linha['Especie'] ||
        linha['Espécie'] ||
        linha['Nome cientifico'] ||
        linha['Nome científico'] ||
        linha['Nome Cientifico'] ||
        ''
    );
}

// CÁLCULOS — RIQUEZA E ABUNDÂNCIA
const riquezaPorGrupo = computed(() => {
    const data = previewAtivo.value;
    const grupos = {};

    data.forEach(linha => {
        const grupo = mapClasseToGrupo(linha['Classe']);
        const especie = String(getEspecieFromLinha(linha)).trim();
        if (!especie) return;

        if (!grupos[grupo]) grupos[grupo] = new Set();
        grupos[grupo].add(especie);
    });

    return Object.entries(grupos).map(([grupo, set]) => ({
        grupo,
        valor: set.size,
    }));
});

// ABUNDÂNCIA = soma da coluna “Abundancia” por grupo
const abundanciaPorClasse = computed(() => {
    const data = previewAtivo.value;
    const grupos = {};

    data.forEach(linha => {
        const grupo = mapClasseToGrupo(linha['Classe']);
        const abund = Number(linha['Abundancia'] ?? linha['Abundância'] ?? 0);

        if (!grupos[grupo]) grupos[grupo] = 0;
        grupos[grupo] += abund;
    });

    return Object.entries(grupos)
        .map(([grupo, valor]) => ({ grupo, valor }))
        .sort((a, b) => b.valor - a.valor);
});


// HIGHCHARTS
const chartRiquezaRef = ref(null);
const chartAbundanciaRef = ref(null);

let chartRiqueza = null;
let chartAbundancia = null;

function createChart(container, options) {
    if (!container) return null;
    return Highcharts.chart({
        ...options,
        chart: { ...options.chart, renderTo: container }
    });
}

function destroyCharts() {
    chartRiqueza?.destroy();
    chartAbundancia?.destroy();
    chartRiqueza = null;
    chartAbundancia = null;
}

/* Renderização */
function updateCharts() {
    nextTick(() => {
        destroyCharts();

        const riqueza = riquezaPorGrupo.value;
        const abundanciaClasse = abundanciaPorClasse.value;

        if (!chartRiquezaRef.value || !chartAbundanciaRef.value) return;

        /* RIQUEZA */
        chartRiqueza = createChart(chartRiquezaRef.value, {
            chart: { type: 'pie' },
            title: { text: 'Riqueza' },
            subtitle: { text: labelTipoAtivo.value },
            tooltip: { pointFormat: '<b>{point.percentage:.1f}%</b> ({point.y} espécies)' },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    dataLabels: { enabled: true, format: '<b>{point.name}</b>: {point.percentage:.1f}%' }
                }
            },
            series: [{
                name: 'Riqueza',
                data: riqueza.map(r => ({
                    name: r.grupo,
                    y: r.valor,
                    color: colorByClass(r.grupo)
                }))
            }],
            credits: { enabled: false }
        });

        /* ABUNDÂNCIA */
        chartAbundancia = createChart(chartAbundanciaRef.value, {
            chart: { type: 'pie' },
            title: { text: 'Abundância' },
            subtitle: { text: labelTipoAtivo.value },
            tooltip: { pointFormat: '<b>{point.percentage:.1f}%</b> ({point.y} registros)' },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    dataLabels: { enabled: true, format: '<b>{point.name}</b>: {point.percentage:.1f}%' }
                }
            },
            series: [{
                name: 'Classes',
                data: abundanciaClasse.map(c => ({
                    name: c.grupo,
                    y: c.valor,
                    color: colorByClass(c.grupo)
                }))
            }],
            credits: { enabled: false }
        });
    });
}

onMounted(updateCharts);
watch(previewAtivo, updateCharts, { deep: true });
watch(activeTipo, updateCharts);


// PROCESSAMENTO DE PLANILHAS
const processarPlanilha = (tipo, event) => {
    const file = event.target.files[0];
    if (!file) return;

    // Adiciona ao formResultados (Laravel)
    const map = {
        terrestre: 'planilha_terrestre',
        aquatica: 'planilha_aquatica',
        cavernicola: 'planilha_cavernicola'
    };
    props.formResultados[map[tipo]] = file;

    // Lê a planilha
    const reader = new FileReader();
    reader.onload = e => {
        const data = new Uint8Array(e.target.result);
        const workbook = XLSX.read(data, { type: 'array' });
        const sheet = workbook.Sheets[workbook.SheetNames[0]];
        const json = XLSX.utils.sheet_to_json(sheet, { defval: '' });

        resultados.value[tipo] = json;

        emit('update:resultadosRecords', { ...resultados.value });
        updateCharts();
    };
    reader.readAsArrayBuffer(file);
};

/* Remover linha */
const excluirResultado = (tipo, index) => {
    resultados.value[tipo].splice(index, 1);
    emit('update:resultadosRecords', { ...resultados.value });
    updateCharts();
};

// NAVEGAÇÃO
const downloadModelo = () => {
    alert('Os modelos oficiais estão na área de Modelos dentro do sistema.');
};

const avancar = () => {
    props.formResultados.consideracoes = consideracoes.value;
    emit('next');
};

</script>

<template>
    <div>
        <h4 class="mb-3">RESULTADOS</h4>

        <!-- Botão Modelos -->
        <div class="mb-3">
            <button class="btn btn-secondary" type="button" @click="downloadModelo">
                Ver Modelos Disponíveis
            </button>
        </div>

        <!-- Abas -->
        <ul class="nav nav-tabs mb-3">
            <li v-for="tipo in tipos" :key="tipo.key" class="nav-item">
                <button
                    type="button"
                    class="nav-link"
                    :class="{ active: activeTipo === tipo.key }"
                    @click="activeTipo = tipo.key"
                >
                    {{ tipo.label }}
                </button>
            </li>
        </ul>

        <!-- GRÁFICOS AGORA NO TOPO -->
        <div v-if="previewAtivo.length" class="mt-3 mb-4">
            <h5>Gráficos — {{ labelTipoAtivo }}</h5>

            <div class="row mt-3">
                <div class="col-md-6 mb-4">
                    <div ref="chartRiquezaRef" style="width:100%; height:400px;"></div>
                </div>

                <div class="col-md-6 mb-4">
                    <div ref="chartAbundanciaRef" style="width:100%; height:400px;"></div>
                </div>
            </div>
        </div>

        <!-- Conteúdo -->
        <div class="tab-content mb-4">

            <!-- TERRESTRE -->
            <div v-if="activeTipo === 'terrestre'">
                <h5>Planilha - Fauna Terrestre</h5>

                <div class="mb-3">
                    <input type="file" class="form-control"
                        accept=".xlsx,.xls"
                        @change="(e) => processarPlanilha('terrestre', e)" />
                    <InputError :message="formResultados.errors?.planilha_terrestre" />
                </div>

                <!-- Preview -->
                <div v-if="resultados.terrestre.length" class="table-responsive">
                    <table class="table table-bordered table-sm small">
                        <thead>
                        <tr>
                            <th v-for="(v, k) in resultados.terrestre[0]" :key="k">{{ k }}</th>
                            <th>Ações</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(linha, idx) in resultados.terrestre" :key="idx">
                            <td v-for="(v, k) in linha" :key="k">{{ v }}</td>
                            <td>
                                <button class="btn btn-danger btn-sm"
                                    @click="excluirResultado('terrestre', idx)">Excluir</button>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- AQUATICA -->
            <div v-if="activeTipo === 'aquatica'">
                <h5>Planilha - Fauna Aquática</h5>

                <div class="mb-3">
                    <input type="file" class="form-control"
                        accept=".xlsx,.xls"
                        @change="(e) => processarPlanilha('aquatica', e)" />
                    <InputError :message="formResultados.errors?.planilha_aquatica" />
                </div>

                <!-- Preview -->
                <div v-if="resultados.aquatica.length" class="table-responsive">
                    <table class="table table-bordered table-sm small">
                        <thead>
                        <tr>
                            <th v-for="(v, k) in resultados.aquatica[0]" :key="k">{{ k }}</th>
                            <th>Ações</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(linha, idx) in resultados.aquatica" :key="idx">
                            <td v-for="(v, k) in linha" :key="k">{{ v }}</td>
                            <td>
                                <button class="btn btn-danger btn-sm"
                                    @click="excluirResultado('aquatica', idx)">Excluir</button>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- CAVERNICOLA -->
            <div v-if="activeTipo === 'cavernicola'">
                <h5>Planilha - Fauna Cavernícola</h5>

                <div class="mb-3">
                    <input type="file" class="form-control"
                        accept=".xlsx,.xls"
                        @change="(e) => processarPlanilha('cavernicola', e)" />
                    <InputError :message="formResultados.errors?.planilha_cavernicola" />
                </div>

                <!-- Preview -->
                <div v-if="resultados.cavernicola.length" class="table-responsive">
                    <table class="table table-bordered table-sm small">
                        <thead>
                        <tr>
                            <th v-for="(v, k) in resultados.cavernicola[0]" :key="k">{{ k }}</th>
                            <th>Ações</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(linha, idx) in resultados.cavernicola" :key="idx">
                            <td v-for="(v, k) in linha" :key="k">{{ v }}</td>
                            <td>
                                <button class="btn btn-danger btn-sm"
                                    @click="excluirResultado('cavernicola', idx)">Excluir</button>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Considerações -->
        <div class="mb-3 mt-4">
            <label for="consideracoes" class="form-label fw-bold">Considerações</label>
            <textarea v-model="consideracoes" class="form-control" rows="4"></textarea>
        </div>

        <!-- Navegação -->
        <div class="d-flex justify-content-between">
            <NavButton type-button="secondary" title="Voltar" @click="$emit('prev')" />
            <NavButton type-button="primary" title="Avançar" @click="avancar" />
        </div>
    </div>
</template>

<style scoped>
.table-responsive {
    margin-bottom: 1rem;
}
</style>
