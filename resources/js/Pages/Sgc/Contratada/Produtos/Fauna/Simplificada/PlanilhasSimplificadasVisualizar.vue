<script setup>
import { computed, nextTick, onBeforeUnmount, onMounted, ref, watch } from 'vue';
import * as XLSX from 'xlsx';
import Highcharts from 'highcharts';

const props = defineProps({ planilhas: { type: Object, default: () => ({}) } });
const tipos = [{ key: 'terrestre', label: 'Fauna Terrestre' }, { key: 'aquatica', label: 'Fauna Aquática' }, { key: 'cavernicola', label: 'Fauna Cavernícola' }];
const ativo = ref('terrestre');
const dados = ref({ columns: [], rows: [], loading: false, error: null });
const chartRiquezaRef = ref(null);
const chartAbundanciaRef = ref(null);
let chartRiqueza = null;
let chartAbundancia = null;

const planilhaAtiva = computed(() => props.planilhas?.[ativo.value] ?? null);
const registros = computed(() => dados.value.rows.map((row) => Object.fromEntries(dados.value.columns.map((column, index) => [column, row[index] ?? '']))));
const normalizar = (valor) => String(valor ?? '').normalize('NFD').replace(/[\u0300-\u036f]/g, '').trim().toLowerCase();
const valor = (linha, chaves) => {
    const entrada = Object.entries(linha).find(([chave]) => chaves.includes(normalizar(chave)));
    return entrada?.[1] ?? '';
};
const grupo = (classe) => {
    const valorClasse = String(classe || '').trim();
    if (ativo.value !== 'terrestre') return valorClasse || 'Não classificado';
    const chave = normalizar(valorClasse);
    if (chave === 'aves') return 'Avifauna';
    if (chave === 'mammalia') return 'Mastofauna';
    if (chave === 'reptilia' || chave === 'amphibia') return 'Herpetofauna';
    return valorClasse || 'Não classificado';
};
const estatisticas = computed(() => {
    const riqueza = {};
    const abundancia = {};
    registros.value.forEach((linha) => {
        const classe = grupo(valor(linha, ['classe']));
        const especie = String(valor(linha, ['especie', 'nome cientifico'])).trim();
        const bruto = String(valor(linha, ['abundancia'])).replace(',', '.');
        const quantidade = Number(bruto);
        if (especie) (riqueza[classe] ??= new Set()).add(especie);
        if (Number.isFinite(quantidade) && quantidade > 0) abundancia[classe] = (abundancia[classe] ?? 0) + quantidade;
    });
    return {
        riqueza: Object.entries(riqueza).map(([name, especies]) => ({ name, y: especies.size })).filter((item) => item.y > 0),
        abundancia: Object.entries(abundancia).map(([name, y]) => ({ name, y })).filter((item) => item.y > 0),
    };
});
const temDadosGraficos = computed(() => estatisticas.value.riqueza.length || estatisticas.value.abundancia.length);
const cores = ['#3b82f6', '#16a34a', '#f59e0b', '#8b5cf6', '#ec4899', '#06b6d4', '#ef4444'];
const serie = (itens) => itens.map((item, index) => ({ ...item, color: cores[index % cores.length] }));
const destruirGraficos = () => { chartRiqueza?.destroy(); chartAbundancia?.destroy(); chartRiqueza = null; chartAbundancia = null; };
const renderizarGraficos = () => nextTick(() => {
    destruirGraficos();
    if (!temDadosGraficos.value) return;
    const opcoes = (titulo, itens, sufixo) => ({ chart: { type: 'pie' }, title: { text: titulo }, subtitle: { text: tipos.find((tipo) => tipo.key === ativo.value)?.label }, tooltip: { pointFormat: `<b>{point.percentage:.1f}%</b> ({point.y} ${sufixo})` }, plotOptions: { pie: { allowPointSelect: true, dataLabels: { enabled: true, format: '<b>{point.name}</b>: {point.percentage:.1f}%' } } }, credits: { enabled: false }, series: [{ name: titulo, data: serie(itens) }] });
    if (chartRiquezaRef.value && estatisticas.value.riqueza.length) chartRiqueza = Highcharts.chart(chartRiquezaRef.value, opcoes('Riqueza', estatisticas.value.riqueza, 'espécies'));
    if (chartAbundanciaRef.value && estatisticas.value.abundancia.length) chartAbundancia = Highcharts.chart(chartAbundanciaRef.value, opcoes('Abundância', estatisticas.value.abundancia, 'registros'));
});

const carregar = async () => {
    const planilha = planilhaAtiva.value;
    dados.value = { columns: [], rows: [], loading: Boolean(planilha?.url), error: null };
    if (!planilha?.url) return;
    try {
        const response = await fetch(planilha.url);
        if (!response.ok) throw new Error('Não foi possível baixar a planilha.');
        const workbook = XLSX.read(await response.arrayBuffer(), { type: 'array' });
        const rows = XLSX.utils.sheet_to_json(workbook.Sheets[workbook.SheetNames[0]], { header: 1, raw: false });
        if (!rows.length) throw new Error('A planilha está vazia.');
        const columns = rows[0].map((value, index) => value || `Coluna ${index + 1}`);
        dados.value = { columns, rows: rows.slice(1), loading: false, error: null };
    } catch (error) { dados.value = { columns: [], rows: [], loading: false, error: error.message || 'Erro ao carregar a planilha.' }; }
};

watch([ativo, () => props.planilhas], carregar, { deep: true });
watch([registros, ativo], renderizarGraficos, { deep: true });
onMounted(carregar);
onBeforeUnmount(destruirGraficos);
</script>

<template>
    <section>
        <h4 class="mb-3">Planilhas de Resultados</h4>
        <ul class="nav nav-tabs mb-3"><li v-for="tipo in tipos" :key="tipo.key" class="nav-item"><button type="button" class="nav-link" :class="{ active: ativo === tipo.key }" @click="ativo = tipo.key">{{ tipo.label }} <span v-if="planilhas?.[tipo.key]" class="badge bg-success text-white ms-1" title="Planilha enviada" aria-label="Planilha enviada">✓</span></button></li></ul>
        <div v-if="!planilhaAtiva" class="alert alert-light border mb-0">Nenhuma planilha enviada para esta modalidade.</div>
        <template v-else>
            <div class="d-flex justify-content-between align-items-center border rounded p-3 mb-3"><div><strong>{{ planilhaAtiva.nome_arquivo }}</strong><div class="text-muted small">Modelo: {{ planilhaAtiva.modulo?.nome || 'Não informado' }}</div></div><a :href="planilhaAtiva.url" target="_blank" rel="noopener" class="btn btn-primary">Abrir planilha</a></div>
            <div v-if="dados.loading" class="text-muted">Carregando prévia...</div><div v-else-if="dados.error" class="alert alert-danger">{{ dados.error }}</div>
            <template v-else-if="dados.rows.length">
                <div v-if="temDadosGraficos" class="mt-3 mb-4"><h5>Gráficos — {{ tipos.find((tipo) => tipo.key === ativo)?.label }}</h5><div class="row mt-3"><div class="col-lg-6 mb-3"><div ref="chartRiquezaRef" class="chart"></div></div><div class="col-lg-6 mb-3"><div ref="chartAbundanciaRef" class="chart"></div></div></div></div>
                <div v-else class="alert alert-light border">Sem dados de Classe, Espécie ou Abundância suficientes para gerar os gráficos.</div>
                <div class="table-responsive preview"><table class="table table-sm table-bordered"><thead class="table-light"><tr><th v-for="(column, i) in dados.columns" :key="i">{{ column }}</th></tr></thead><tbody><tr v-for="(row, i) in dados.rows" :key="i"><td v-for="(cell, j) in row" :key="j">{{ cell || '-' }}</td></tr></tbody></table></div>
            </template>
            <div v-else class="alert alert-light border mb-0">A planilha não possui linhas para exibir.</div>
        </template>
    </section>
</template>

<style scoped>.preview { max-height: 520px; overflow: auto; }.preview table { min-width: 900px; }.chart { width: 100%; height: 390px; }</style>
