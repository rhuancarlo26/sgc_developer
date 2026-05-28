<script setup>
import InputError from '@/Components/InputError.vue';
import NavButton from '@/Components/NavButton.vue';
import { ref, computed, watch, onMounted, nextTick } from 'vue';
import * as XLSX from 'xlsx';
import Highcharts from 'highcharts';

const props = defineProps({
    form: { type: Object, required: true },
    resultadosRecords: { type: Object, default: () => ({}) },
    idCampanha: { type: [String, Number], required: true },
    disabled: { type: Boolean, default: false },
});

const emit = defineEmits(['update:resultadosRecords', 'prev', 'next']);

// ===================== ESTADOS PRINCIPAIS =====================

const consideracoes = ref(props.form.consideracoes ?? '');

const initial = props.resultadosRecords || {};
const resultados = ref({
    terrestre: Array.isArray(initial.terrestre) ? initial.terrestre : [],
    aquatica: Array.isArray(initial.aquatica) ? initial.aquatica : [],
    cavernicola: Array.isArray(initial.cavernicola) ? initial.cavernicola : [],
});

// Abas
const activeTipo = ref('terrestre');

const tipos = [
    { key: 'terrestre', label: 'Fauna Terrestre' },
    { key: 'aquatica', label: 'Fauna Aquática' },
    { key: 'cavernicola', label: 'Fauna Cavernícola' },
];

const labelTipoAtivo = computed(
    () => tipos.find(t => t.key === activeTipo.value)?.label || ''
);

const previewAtivo = computed(
    () => resultados.value[activeTipo.value] || []
);

const temPlanilha = computed(() => {
    return (
        (resultados.value.terrestre?.length || 0) ||
        (resultados.value.aquatica?.length || 0) ||
        (resultados.value.cavernicola?.length || 0)
    );
});

// Quando resultadosRecords vindo do pai mudar (ex: carregou do banco), sincroniza
watch(
    () => props.resultadosRecords,
    (val) => {
        const v = val || {};
        resultados.value.terrestre   = Array.isArray(v.terrestre)   ? v.terrestre   : [];
        resultados.value.aquatica    = Array.isArray(v.aquatica)    ? v.aquatica    : [];
        resultados.value.cavernicola = Array.isArray(v.cavernicola) ? v.cavernicola : [];
        emit('update:resultadosRecords', { ...resultados.value });
        updateCharts();
    },
    { deep: true, immediate: true }
);

// Sincronizar considerações com o form pai
watch(consideracoes, (val) => {
    if (!props.disabled) {
        props.form.consideracoes = val;
    }
});

// ===================== CORES / AGRUPAMENTO =====================

// Gerador de cores pastel
function pastelColor() {
    const grupo = Math.floor(Math.random() * 3);
    let hue;

    if (grupo === 0) {
        // Azul discreto
        hue = 190 + Math.random() * 40;
    } else if (grupo === 1) {
        // Verde suave
        hue = 90 + Math.random() * 50;
    } else {
        // Amarelo pastel
        hue = 40 + Math.random() * 20;
    }

    const saturation = 35 + Math.random() * 20;
    const lightness  = 65 + Math.random() * 10;

    return `hsl(${hue}, ${saturation}%, ${lightness}%)`;
}

const classColorMap = {};

function colorByClass(classe) {
    const key = String(classe || '').trim().toLowerCase();
    if (!key) return '#cccccc';

    if (!classColorMap[key]) {
        classColorMap[key] = pastelColor();
    }
    return classColorMap[key];
}

// Mapeamento de Classe -> Grupo (para TERRESTRE)
function mapClasseToGrupo(classeRaw) {
    // Aquática e Cavernícola → usa a Classe direto
    if (activeTipo.value !== 'terrestre') {
        return String(classeRaw || '').trim() || 'Não classificado';
    }

    const c = String(classeRaw || '').trim().toLowerCase();
    if (c === 'aves') return 'Avifauna';
    if (c === 'mammalia') return 'Mastofauna';
    if (c === 'reptilia' || c === 'amphibia') return 'Herpetofauna';
    return 'Não classificado';
}

// Pega nome da espécie (diferentes cabeçalhos possíveis)
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

// ===================== CÁLCULOS (RIQUEZA / ABUNDÂNCIA) =====================

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

// ===================== HIGHCHARTS =====================

const chartRiquezaRef = ref(null);
const chartAbundanciaRef = ref(null);

let chartRiqueza = null;
let chartAbundancia = null;

function createChart(container, options) {
    if (!container) return null;
    return Highcharts.chart({
        ...options,
        chart: { ...options.chart, renderTo: container },
    });
}

function destroyCharts() {
    chartRiqueza?.destroy();
    chartAbundancia?.destroy();
    chartRiqueza = null;
    chartAbundancia = null;
}

function updateCharts() {
    nextTick(() => {
        destroyCharts();

        const riqueza = riquezaPorGrupo.value;
        const abundanciaClasse = abundanciaPorClasse.value;

        if (!chartRiquezaRef.value || !chartAbundanciaRef.value) return;

        // Riqueza
        chartRiqueza = createChart(chartRiquezaRef.value, {
            chart: { type: 'pie' },
            title: { text: 'Riqueza' },
            subtitle: { text: labelTipoAtivo.value },
            tooltip: {
                pointFormat: '<b>{point.percentage:.1f}%</b> ({point.y} espécies)',
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    dataLabels: {
                        enabled: true,
                        format: '<b>{point.name}</b>: {point.percentage:.1f}%',
                    },
                },
            },
            series: [
                {
                    name: 'Riqueza',
                    data: riqueza.map(r => ({
                        name: r.grupo,
                        y: r.valor,
                        color: colorByClass(r.grupo),
                    })),
                },
            ],
            credits: { enabled: false },
        });

        // Abundância
        chartAbundancia = createChart(chartAbundanciaRef.value, {
            chart: { type: 'pie' },
            title: { text: 'Abundância' },
            subtitle: { text: labelTipoAtivo.value },
            tooltip: {
                pointFormat: '<b>{point.percentage:.1f}%</b> ({point.y} registros)',
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    dataLabels: {
                        enabled: true,
                        format: '<b>{point.name}</b>: {point.percentage:.1f}%',
                    },
                },
            },
            series: [
                {
                    name: 'Classes',
                    data: abundanciaClasse.map(c => ({
                        name: c.grupo,
                        y: c.valor,
                        color: colorByClass(c.grupo),
                    })),
                },
            ],
            credits: { enabled: false },
        });
    });
}

onMounted(updateCharts);
watch(previewAtivo, updateCharts, { deep: true });
watch(activeTipo, updateCharts);

// ===================== PROCESSAMENTO DE PLANILHAS =====================

const processarPlanilha = (tipo, event) => {
    if (props.disabled) return;

    const file = event.target.files[0];
    if (!file) return;

    // Mapeia o campo de file correto no form (igual ao de criar)
    const map = {
        terrestre: 'planilha_terrestre',
        aquatica: 'planilha_aquatica',
        cavernicola: 'planilha_cavernicola',
    };

    props.form[map[tipo]] = file;

    const reader = new FileReader();
    reader.onload = e => {
        const data = new Uint8Array(e.target.result);
        const workbook = XLSX.read(data, { type: 'array' });
        const sheet = workbook.Sheets[workbook.SheetNames[0]];
        const json = XLSX.utils.sheet_to_json(sheet, { defval: '' });

        // Aqui cada tipo usa SUAS PRÓPRIAS COLUNAS da planilha
        resultados.value[tipo] = json;

        emit('update:resultadosRecords', { ...resultados.value });
        updateCharts();
    };
    reader.readAsArrayBuffer(file);
};

const excluirResultado = (tipo, index) => {
    if (props.disabled) return;

    resultados.value[tipo].splice(index, 1);
    emit('update:resultadosRecords', { ...resultados.value });
    updateCharts();
};

// ===================== NAVEGAÇÃO / BOTÕES =====================

const downloadModelo = () => {
    alert('Os modelos oficiais estão na área de Modelos dentro do sistema.');
};

const avancar = () => {
    props.form.consideracoes = consideracoes.value;
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

        <!-- NAVEGAÇÃO TOPO -->
        <div v-if="temPlanilha" class="d-flex justify-content-between mb-3 sticky-top bg-white py-2">
            <NavButton type-button="secondary" title="Voltar" @click="$emit('prev')" />
            <NavButton type-button="primary" title="Avançar" @click="avancar" />
        </div>

        <!-- GRÁFICOS -->
        <div v-if="previewAtivo.length" class="mt-3 mb-4">
            <h5>Gráficos — {{ labelTipoAtivo }}</h5>

            <div class="row mt-3">
                <div class="col-md-6 mb-4">
                    <div ref="chartRiquezaRef" style="width: 100%; height: 400px;"></div>
                </div>

                <div class="col-md-6 mb-4">
                    <div ref="chartAbundanciaRef" style="width: 100%; height: 400px;"></div>
                </div>
            </div>
        </div>

        <!-- CONTEÚDO DAS ABAS -->
        <div class="tab-content mb-4">
            <!-- TERRESTRE -->
            <div v-if="activeTipo === 'terrestre'">
                <h5>Planilha - Fauna Terrestre</h5>

                <div class="mb-3">
                    <input
                        type="file"
                        class="form-control"
                        accept=".xlsx,.xls"
                        @change="(e) => processarPlanilha('terrestre', e)"
                        :disabled="disabled"
                    />
                    <InputError :message="form.errors?.planilha_terrestre" />
                </div>

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
                                    <button
                                        class="btn btn-danger btn-sm"
                                        @click="excluirResultado('terrestre', idx)"
                                        :disabled="disabled"
                                    >
                                        Excluir
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- AQUÁTICA -->
            <div v-if="activeTipo === 'aquatica'">
                <h5>Planilha - Fauna Aquática</h5>

                <div class="mb-3">
                    <input
                        type="file"
                        class="form-control"
                        accept=".xlsx,.xls"
                        @change="(e) => processarPlanilha('aquatica', e)"
                        :disabled="disabled"
                    />
                    <InputError :message="form.errors?.planilha_aquatica" />
                </div>

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
                                    <button
                                        class="btn btn-danger btn-sm"
                                        @click="excluirResultado('aquatica', idx)"
                                        :disabled="disabled"
                                    >
                                        Excluir
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- CAVERNÍCOLA -->
            <div v-if="activeTipo === 'cavernicola'">
                <h5>Planilha - Fauna Cavernícola</h5>

                <div class="mb-3">
                    <input
                        type="file"
                        class="form-control"
                        accept=".xlsx,.xls"
                        @change="(e) => processarPlanilha('cavernicola', e)"
                        :disabled="disabled"
                    />
                    <InputError :message="form.errors?.planilha_cavernicola" />
                </div>

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
                                    <button
                                        class="btn btn-danger btn-sm"
                                        @click="excluirResultado('cavernicola', idx)"
                                        :disabled="disabled"
                                    >
                                        Excluir
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- CONSIDERAÇÕES -->
        <div class="mb-3 mt-4">
            <label for="consideracoes" class="form-label fw-bold">Considerações</label>
            <textarea
                v-model="consideracoes"
                class="form-control"
                rows="4"
                :disabled="disabled"
            ></textarea>
            <InputError :message="form.errors?.consideracoes" />
        </div>

        <!-- NAVEGAÇÃO -->
        <div v-if="!temPlanilha" class="d-flex justify-content-between">
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
