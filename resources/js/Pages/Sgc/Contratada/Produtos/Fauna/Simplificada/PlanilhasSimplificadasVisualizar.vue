<script setup>
import { computed, onMounted, ref, watch } from 'vue';
import * as XLSX from 'xlsx';

const props = defineProps({ planilhas: { type: Object, default: () => ({}) } });
const tipos = [{ key: 'terrestre', label: 'Fauna Terrestre' }, { key: 'aquatica', label: 'Fauna Aquática' }, { key: 'cavernicola', label: 'Fauna Cavernícola' }];
const ativo = ref('terrestre');
const dados = ref({ columns: [], rows: [], loading: false, error: null });
const planilhaAtiva = computed(() => props.planilhas?.[ativo.value] ?? null);

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
onMounted(carregar);
</script>

<template>
    <section>
        <h4 class="mb-3">Planilhas de Resultados</h4>
        <ul class="nav nav-tabs mb-3"><li v-for="tipo in tipos" :key="tipo.key" class="nav-item"><button type="button" class="nav-link" :class="{ active: ativo === tipo.key }" @click="ativo = tipo.key">{{ tipo.label }} <span v-if="planilhas?.[tipo.key]" class="badge bg-success text-white ms-1" title="Planilha enviada" aria-label="Planilha enviada">✓</span></button></li></ul>
        <div v-if="!planilhaAtiva" class="alert alert-light border mb-0">Nenhuma planilha enviada para esta modalidade.</div>
        <template v-else><div class="d-flex justify-content-between align-items-center border rounded p-3 mb-3"><div><strong>{{ planilhaAtiva.nome_arquivo }}</strong><div class="text-muted small">Modelo: {{ planilhaAtiva.modulo?.nome || 'Não informado' }}</div></div><a :href="planilhaAtiva.url" target="_blank" rel="noopener" class="btn btn-primary">Abrir planilha</a></div><div v-if="dados.loading" class="text-muted">Carregando prévia...</div><div v-else-if="dados.error" class="alert alert-danger">{{ dados.error }}</div><div v-else-if="dados.rows.length" class="table-responsive preview"><table class="table table-sm table-bordered"><thead class="table-light"><tr><th v-for="(column, i) in dados.columns" :key="i">{{ column }}</th></tr></thead><tbody><tr v-for="(row, i) in dados.rows" :key="i"><td v-for="(cell, j) in row" :key="j">{{ cell || '-' }}</td></tr></tbody></table></div><div v-else class="alert alert-light border mb-0">A planilha não possui linhas para exibir.</div></template>
    </section>
</template>

<style scoped>.preview { max-height: 520px; overflow: auto; }.preview table { min-width: 900px; }</style>
