<script setup>
import { ref, computed, watch, onMounted, onBeforeUnmount, nextTick } from 'vue';
import { defineProps, defineEmits } from 'vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import NavButton from '@/Components/NavButton.vue';
import Highcharts from 'highcharts';


const props = defineProps({
    resultadosTerrestre: Array,
    resultadosAquatica: Array,
    resultadosCavernicola: Array,
    consideracoes: String,

    canApprove: Boolean,
    statusCampanha: String,
    analiseForm: Object,
    currentAnalise: Object
});

const emit = defineEmits(['next','prev','aprovar','rejeitar']);

const subtab = ref('terrestre');

const tipos = [
    { key: 'terrestre', label: 'Fauna Terrestre' },
    { key: 'aquatica', label: 'Fauna Aquática' },
    { key: 'cavernicola', label: 'Fauna Cavernícola' },
];

const labelTipoAtivo = computed(() => tipos.find(t => t.key === subtab.value)?.label || '');

const resultadosPorTipo = computed(() => ({
    terrestre: props.resultadosTerrestre || [],
    aquatica: props.resultadosAquatica || [],
    cavernicola: props.resultadosCavernicola || [],
}));

const previewAtivo = computed(() => resultadosPorTipo.value[subtab.value] || []);


const temPlanilha = computed(() => {
    return (
        (props.resultadosTerrestre?.length || 0) ||
        (props.resultadosAquatica?.length || 0) ||
        (props.resultadosCavernicola?.length || 0)
    );
});

function pastelColor() {
    const grupo = Math.floor(Math.random() * 3);
    let hue;

    if (grupo === 0) {
        hue = 190 + Math.random() * 40;
    } else if (grupo === 1) {
        hue = 90 + Math.random() * 50;
    } else {
        hue = 40 + Math.random() * 20;
    }

    const saturation = 35 + Math.random() * 20;
    const lightness = 65 + Math.random() * 10;

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

function getValue(linha, keys) {
    for (const key of keys) {
        const value = linha?.[key];

        if (value !== undefined && value !== null && value !== '') {
            return value;
        }
    }

    return '';
}

function mapClasseToGrupo(classeRaw) {
    if (subtab.value !== 'terrestre') {
        return String(classeRaw || '').trim() || 'Não classificado';
    }

    const classe = String(classeRaw || '').trim().toLowerCase();

    if (classe === 'aves') return 'Avifauna';
    if (classe === 'mammalia') return 'Mastofauna';
    if (classe === 'reptilia' || classe === 'amphibia') return 'Herpetofauna';

    return 'Não classificado';
}

function getClasseFromLinha(linha) {
    return getValue(linha, ['classe', 'Classe']);
}

function getEspecieFromLinha(linha) {
    return getValue(linha, [
        'especie',
        'espécie',
        'Espécie',
        'Especie',
        'nome_cientifico',
        'Nome cientifico',
        'Nome científico',
        'Nome Cientifico',
    ]);
}

function getAbundanciaFromLinha(linha) {
    const value = getValue(linha, ['abundancia', 'Abundancia', 'Abundância']);
    const parsed = Number(String(value).replace(',', '.'));

    return Number.isFinite(parsed) ? parsed : 0;
}

const riquezaPorGrupo = computed(() => {
    const grupos = {};

    previewAtivo.value.forEach(linha => {
        const grupo = mapClasseToGrupo(getClasseFromLinha(linha));
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
    const grupos = {};

    previewAtivo.value.forEach(linha => {
        const grupo = mapClasseToGrupo(getClasseFromLinha(linha));

        if (!grupos[grupo]) grupos[grupo] = 0;

        grupos[grupo] += getAbundanciaFromLinha(linha);
    });

    return Object.entries(grupos)
        .map(([grupo, valor]) => ({ grupo, valor }))
        .filter(item => item.valor > 0)
        .sort((a, b) => b.valor - a.valor);
});

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

        if (!previewAtivo.value.length || !chartRiquezaRef.value || !chartAbundanciaRef.value) {
            return;
        }

        chartRiqueza = createChart(chartRiquezaRef.value, {
            chart: { type: 'pie' },
            title: { text: 'Riqueza' },
            subtitle: { text: labelTipoAtivo.value },
            tooltip: { pointFormat: '<b>{point.percentage:.1f}%</b> ({point.y} espécies)' },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    dataLabels: { enabled: true, format: '<b>{point.name}</b>: {point.percentage:.1f}%' },
                },
            },
            series: [{
                name: 'Riqueza',
                data: riquezaPorGrupo.value.map(r => ({
                    name: r.grupo,
                    y: r.valor,
                    color: colorByClass(r.grupo),
                })),
            }],
            credits: { enabled: false },
        });

        chartAbundancia = createChart(chartAbundanciaRef.value, {
            chart: { type: 'pie' },
            title: { text: 'Abundância' },
            subtitle: { text: labelTipoAtivo.value },
            tooltip: { pointFormat: '<b>{point.percentage:.1f}%</b> ({point.y} registros)' },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    dataLabels: { enabled: true, format: '<b>{point.name}</b>: {point.percentage:.1f}%' },
                },
            },
            series: [{
                name: 'Classes',
                data: abundanciaPorClasse.value.map(c => ({
                    name: c.grupo,
                    y: c.valor,
                    color: colorByClass(c.grupo),
                })),
            }],
            credits: { enabled: false },
        });
    });
}

onMounted(updateCharts);
onBeforeUnmount(destroyCharts);
watch(subtab, updateCharts);
watch(previewAtivo, updateCharts, { deep: true });


</script>

<template>
<div class="card">
<div class="card-body">

<h4 class="mb-3 text-center">RESULTADOS</h4>    

<!-- ANÁLISE NO TOPO -->
<div v-if="canApprove && statusCampanha === 'Em análise'" class="mb-4">

    <h5 class="text-center mb-3">ANÁLISE DA ETAPA</h5>

    <div class="mb-3">
        <label class="form-label">Observações (obrigatório para reprovação)</label>

        <textarea
            v-model="analiseForm.observacoes"
            class="form-control"
            rows="3"
            placeholder="Digite observações"
        ></textarea>

        <InputError :message="analiseForm.errors?.observacoes" />

        <p v-if="currentAnalise?.status" class="mt-2 text-sm text-gray-600">
            Status atual: {{ currentAnalise.status }} (Análise {{ currentAnalise.analise }})
        </p>

        <p v-if="currentAnalise?.observacoes" class="mt-2 text-sm text-gray-600">
            Observações anteriores: {{ currentAnalise.observacoes }}
        </p>
    </div>

    <div class="d-flex justify-content-end gap-2">
        <NavButton type="button" type-button="danger" title="Rejeitar" @click="$emit('rejeitar')" />
        <NavButton type="button" type-button="success" title="Aprovar" @click="$emit('aprovar')" />
    </div>

</div>

<!-- SUBTABS -->
<ul class="nav nav-tabs mb-4">
    <li class="nav-item">
        <a class="nav-link" :class="{active: subtab==='terrestre'}"
           @click.prevent="subtab='terrestre'">Fauna Terrestre</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" :class="{active: subtab==='aquatica'}"
           @click.prevent="subtab='aquatica'">Fauna Aquática</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" :class="{active: subtab==='cavernicola'}"
           @click.prevent="subtab='cavernicola'">Fauna Cavernícola</a>
    </li>
</ul>

<!-- NAV TOPO -->
<div v-if="temPlanilha" class="d-flex justify-content-between mb-3 sticky-top bg-white py-2">
    <NavButton type-button="secondary" title="Voltar" @click="$emit('prev')" />
    <NavButton type-button="primary" title="Avançar" @click="$emit('next')" />
</div>

<!-- GRÁFICOS -->
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


<!-- ====================== TERRESTRE ====================== -->
<div v-if="subtab==='terrestre'">
<h5 class="text-center mb-3">Fauna Terrestre</h5>

<div v-if="resultadosTerrestre.length" class="table-responsive">
<table class="min-w-full bg-white border border-gray-300">
<thead>
<tr class="bg-gray-100">
    <th>ID</th>
    <th>Campanha</th>
    <th>Estação do Ano</th>
    <th>Data</th>
    <th>Horário</th>
    <th>Clima</th>
    <th>Temp (°C)</th>
    <th>Pluviosidade</th>
    <th>Município</th>
    <th>Unidade Amostral</th>
    <th>Ponto Amostral</th>
    <th>Latitude</th>
    <th>Longitude</th>
    <th>Metodologia</th>
    <th>Tipo Metodologia</th>
    <th>Fitofisionomia</th>
    <th>Habitat</th>
    <th>Características do Ponto</th>

    <!-- TAXONOMIA -->
    <th>Classe</th>
    <th>Ordem</th>
    <th>Família</th>
    <th>Gênero</th>
    <th>Espécie</th>
    <th>Nome Científico</th>
    <th>Nome Comum</th>

    <!-- Atributos -->
    <th>Abundância</th>
    <th>Sensibilidade</th>
    <th>Endemismo</th>
    <th>Observação</th>
    
    <!-- Status -->
    <th>IUCN</th>
    <th>MMA</th>
    <th>Salve</th>
    <th>Estado</th>

    <!-- Coleta -->
    <th>Registro Fotográfico</th>
    <th>Coletado</th>
    <th>Nº Tombo</th>
</tr>
</thead>

<tbody>
<tr v-for="r in resultadosTerrestre" :key="r.id">
    <td>{{ r.id }}</td>
    <td>{{ r.campanha }}</td>
    <td>{{ r.estacao_do_ano }}</td>
    <td>{{ r.data }}</td>
    <td>{{ r.horario }}</td>
    <td>{{ r.condicao_climatica }}</td>
    <td>{{ r.temperatura }}</td>
    <td>{{ r.pluviosidade }}</td>
    <td>{{ r.municipio }}</td>
    <td>{{ r.unidade_amostral }}</td>
    <td>{{ r.ponto_amostral }}</td>
    <td>{{ r.latitude }}</td>
    <td>{{ r.longitude }}</td>
    <td>{{ r.metodologia }}</td>
    <td>{{ r.tipo_metodologia }}</td>
    <td>{{ r.fitofisionomia }}</td>
    <td>{{ r.habitat }}</td>
    <td>{{ r.caracteristicas_ponto }}</td>

    <!-- TAXONOMIA -->
    <td>{{ r.classe }}</td>
    <td>{{ r.ordem }}</td>
    <td>{{ r.familia }}</td>
    <td>{{ r.genero }}</td>
    <td>{{ r.especie }}</td>
    <td>{{ r.nome_cientifico }}</td>
    <td>{{ r.nome_comum }}</td>

    <!-- ATRIBUTOS -->
    <td>{{ r.abundancia }}</td>
    <td>{{ r.sensibilidade }}</td>
    <td>{{ r.endemismo }}</td>
    <td>{{ r.observacao }}</td>

    <!-- STATUS -->
    <td>{{ r.iucn }}</td>
    <td>{{ r.mma }}</td>
    <td>{{ r.salve }}</td>
    <td>{{ r.estado }}</td>

    <!-- COLETA -->
    <td>{{ r.registro_fotografico }}</td>
    <td>{{ r.coletado }}</td>
    <td>{{ r.numero_tombo }}</td>
</tr>
</tbody>
</table>
</div>

<div v-else class="alert alert-info text-center">Nenhum registro terrestre.</div>
</div>

<!-- ====================== AQUÁTICA ====================== -->
<div v-if="subtab==='aquatica'">
<h5 class="text-center mb-3">Fauna Aquática</h5>

<div v-if="resultadosAquatica.length" class="table-responsive">
<table class="min-w-full bg-white border border-gray-300">
<thead>
<tr class="bg-gray-100">
    <th>ID</th>
    <th>Campanha</th>
    <th>Estação</th>
    <th>Data</th>
    <th>Horário</th>
    <th>Clima</th>
    <th>Temperatura</th>
    <th>Pluviosidade</th>
    <th>Município</th>
    <th>Unidade Amostral</th>
    <th>Ponto Amostral</th>
    <th>Latitude</th>
    <th>Longitude</th>

    <th>Metodologia</th>
    <th>Tipo Metodologia</th>
    <th>Fitofisionomia</th>

    <th>Habitat Preferencial</th>
    <th>Tipo de Ambiente</th>
    <th>Largura Médio Rio</th>
    <th>Profundidade Média</th>
    <th>Substrato</th>
    <th>Características da Água</th>
    <th>Características do Entorno</th>

    <!-- TAXONOMIA -->
    <th>Classe</th>
    <th>Ordem</th>
    <th>Família</th>
    <th>Gênero</th>
    <th>Espécie</th>
    <th>Nome Científico</th>
    <th>Nome Comum</th>

    <th>Abundância</th>
    <th>Sensibilidade</th>
    <th>Endemismo</th>
    <th>Observação</th>

    <th>IUCN</th>
    <th>MMA</th>
    <th>Salve</th>
    <th>Estado</th>

    <th>Registro Fotográfico</th>
    <th>Coletado</th>
    <th>Nº Tombo</th>
</tr>
</thead>

<tbody>
<tr v-for="r in resultadosAquatica" :key="r.id">
    <td>{{ r.id }}</td>
    <td>{{ r.campanha }}</td>
    <td>{{ r.estacao_do_ano }}</td>
    <td>{{ r.data }}</td>
    <td>{{ r.horario }}</td>
    <td>{{ r.condicao_climatica }}</td>
    <td>{{ r.temperatura }}</td>
    <td>{{ r.pluviosidade }}</td>
    <td>{{ r.municipio }}</td>
    <td>{{ r.unidade_amostral }}</td>
    <td>{{ r.ponto_amostral }}</td>
    <td>{{ r.latitude }}</td>
    <td>{{ r.longitude }}</td>

    <td>{{ r.metodologia }}</td>
    <td>{{ r.tipo_metodologia }}</td>
    <td>{{ r.fitofisionomia }}</td>

    <td>{{ r.habitat_preferencial }}</td>
    <td>{{ r.tipo_ambiente }}</td>
    <td>{{ r.largura_media_rio }}</td>
    <td>{{ r.profundidade_media }}</td>
    <td>{{ r.tipo_substrato }}</td>
    <td>{{ r.caracteristicas_agua }}</td>
    <td>{{ r.caracteristicas_entorno_ponto }}</td>

    <td>{{ r.classe }}</td>
    <td>{{ r.ordem }}</td>
    <td>{{ r.familia }}</td>
    <td>{{ r.genero }}</td>
    <td>{{ r.especie }}</td>
    <td>{{ r.nome_cientifico }}</td>
    <td>{{ r.nome_comum }}</td>

    <td>{{ r.abundancia }}</td>
    <td>{{ r.sensibilidade }}</td>
    <td>{{ r.endemismo }}</td>
    <td>{{ r.observacao }}</td>

    <td>{{ r.iucn }}</td>
    <td>{{ r.mma }}</td>
    <td>{{ r.salve }}</td>
    <td>{{ r.estado }}</td>

    <td>{{ r.registro_fotografico }}</td>
    <td>{{ r.coletado }}</td>
    <td>{{ r.numero_tombo }}</td>
</tr>
</tbody>
</table>
</div>

<div v-else class="alert alert-info text-center">Nenhum registro aquático.</div>
</div>

<!-- ====================== CAVERNÍCOLA ====================== -->
<div v-if="subtab==='cavernicola'">
<h5 class="text-center mb-3">Fauna Cavernícola</h5>

<div v-if="resultadosCavernicola.length" class="table-responsive">
<table class="min-w-full bg-white border border-gray-300">
<thead>
<tr class="bg-gray-100">
    <th>ID</th>
    <th>Caverna</th>
    <th>Campanha</th>
    <th>Estação</th>
    <th>Data</th>
    <th>Horário</th>
    <th>Clima</th>
    <th>Temp</th>
    <th>Pluviosidade</th>
    <th>Município</th>
    <th>Unidade Amostral</th>
    <th>Ponto Amostral</th>
    <th>Latitude</th>
    <th>Longitude</th>
    <th>Metodologia</th>
    <th>Tipo Metodologia</th>
    <th>Fitofisionomia</th>
    <th>Substrato</th>
    <th>Características Entorno</th>

    <!-- TAXONOMIA -->
    <th>Classe</th>
    <th>Ordem</th>
    <th>Família</th>
    <th>Gênero</th>
    <th>Especie</th>
    <th>Nome Científico</th>
    <th>Nome Comum</th>

    <!-- Atributos -->
    <th>Abundância</th>
    <th>Categoria Ecológica</th>
    <th>Sensibilidade</th>
    <th>Endemismo</th>
    <th>Observação</th>

    <th>Guano</th>
    <th>Presença de Água</th>
    <th>Conectividade Externa</th>
    <th>Perturbação Antropica</th>

    <th>IUCN</th>
    <th>MMA</th>
    <th>Salve</th>
    <th>Estado</th>

    <th>Registro Fotográfico</th>
    <th>Coletado</th>
    <th>Nº Tombo</th>
</tr>
</thead>

<tbody>
<tr v-for="r in resultadosCavernicola" :key="r.id">
    <td>{{ r.id }}</td>
    <td>{{ r.caverna }}</td>
    <td>{{ r.campanha }}</td>
    <td>{{ r.estacao_do_ano }}</td>
    <td>{{ r.data }}</td>
    <td>{{ r.horario }}</td>
    <td>{{ r.condicao_climatica }}</td>
    <td>{{ r.temperatura }}</td>
    <td>{{ r.pluviosidade }}</td>
    <td>{{ r.municipio }}</td>
    <td>{{ r.unidade_amostral }}</td>
    <td>{{ r.ponto_amostral }}</td>
    <td>{{ r.latitude }}</td>
    <td>{{ r.longitude }}</td>

    <td>{{ r.metodologia }}</td>
    <td>{{ r.tipo_metodologia }}</td>
    <td>{{ r.fitofisionomia }}</td>
    <td>{{ r.substrato_amostrado }}</td>
    <td>{{ r.caracteristicas_entorno_ponto }}</td>

    <td>{{ r.classe }}</td>
    <td>{{ r.ordem }}</td>
    <td>{{ r.familia }}</td>
    <td>{{ r.genero }}</td>
    <td>{{ r.especie }}</td>
    <td>{{ r.nome_cientifico }}</td>
    <td>{{ r.nome_comum }}</td>

    <td>{{ r.abundancia }}</td>
    <td>{{ r.categoria_ecologica }}</td>
    <td>{{ r.sensibilidade }}</td>
    <td>{{ r.endemismo }}</td>
    <td>{{ r.observacao }}</td>

    <td>{{ r.presenca_guano }}</td>
    <td>{{ r.presenca_agua }}</td>
    <td>{{ r.conectividade_externa }}</td>
    <td>{{ r.perturbacao_antropica }}</td>

    <td>{{ r.iucn }}</td>
    <td>{{ r.mma }}</td>
    <td>{{ r.salve }}</td>
    <td>{{ r.estado }}</td>

    <td>{{ r.registro_fotografico }}</td>
    <td>{{ r.coletado }}</td>
    <td>{{ r.numero_tombo }}</td>
</tr>
</tbody>
</table>
</div>

<div v-else class="alert alert-info text-center">Nenhum registro cavernícola.</div>
</div>

<!-- Considerações -->
<div class="mt-4">
    <InputLabel value="CONSIDERAÇÕES" />
    <textarea class="form-control" rows="4" :value="consideracoes || 'Não informado'" disabled></textarea>
</div>

<!-- Navegação -->
<div v-if="!temPlanilha" class="d-flex justify-content-between mt-4">
    <NavButton type-button="secondary" title="Voltar" @click="$emit('prev')" />
    <NavButton type-button="primary" title="Avançar" @click="$emit('next')" />
</div>

</div>
</div>
</template>

<style scoped>
.card { border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
.card-body { padding: 2rem; }
.nav-tabs .nav-link { cursor: pointer; }
table th, table td { padding: .5rem 1rem; border: 1px solid #ddd; }
tr:hover { background-color: #f9fafb; }
</style>
