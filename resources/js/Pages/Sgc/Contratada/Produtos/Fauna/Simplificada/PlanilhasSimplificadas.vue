<script setup>
import { ref } from 'vue';
import * as XLSX from 'xlsx';

const props = defineProps({
    form: { type: Object, required: true },
    modulos: { type: Array, default: () => [] },
    contrato: { type: [Number, String], required: true },
});

const tipos = [
    { key: 'terrestre', label: 'Fauna Terrestre' },
    { key: 'aquatica', label: 'Fauna Aquática' },
    { key: 'cavernicola', label: 'Fauna Cavernícola' },
];

const previews = ref({});
const erros = ref({});

const dados = (tipo) => props.form.planilhas[tipo];
const existente = (tipo) => dados(tipo)?.existente;

const limparPreview = (tipo) => {
    previews.value[tipo] = { columns: [], rows: [], error: null };
};

const selecionarArquivo = (tipo, event) => {
    const arquivo = event.target.files?.[0];
    limparPreview(tipo);
    dados(tipo).arquivo = arquivo || null;
    dados(tipo).remover = false;
    erros.value[tipo] = '';

    if (!arquivo) return;

    const reader = new FileReader();
    reader.onload = ({ target }) => {
        try {
            const workbook = XLSX.read(target.result, { type: 'array' });
            const sheet = workbook.Sheets[workbook.SheetNames[0]];
            const rows = XLSX.utils.sheet_to_json(sheet, { header: 1, raw: false });
            if (!rows.length) throw new Error('Planilha vazia');

            const columns = rows[0].map((value, index) => value || `Coluna ${index + 1}`);
            previews.value[tipo] = {
                columns,
                rows: rows.slice(1, 11).map((row) => columns.map((_, index) => row?.[index] ?? '')),
                error: null,
            };
        } catch {
            previews.value[tipo] = { columns: [], rows: [], error: 'Não foi possível ler esta planilha.' };
        }
    };
    reader.readAsArrayBuffer(arquivo);
};

const removerPlanilha = (tipo) => {
    dados(tipo).arquivo = null;
    dados(tipo).modulo_id = null;
    dados(tipo).remover = Boolean(existente(tipo));
    limparPreview(tipo);
};

const validarCampos = () => {
    const novosErros = {};

    tipos.forEach(({ key, label }) => {
        const planilha = dados(key);
        if (planilha.remover) return;

        if (planilha.arquivo && !planilha.modulo_id) {
            novosErros[key] = `Selecione o modelo para ${label}.`;
        }

        if (!planilha.arquivo && planilha.modulo_id && !existente(key)) {
            novosErros[key] = `Envie a planilha preenchida de ${label}.`;
        }
    });

    erros.value = novosErros;
    return Object.keys(novosErros).length > 0;
};

defineExpose({ validarCampos });
</script>

<template>
    <div class="card">
        <div class="card-header"><h3 class="my-0">Planilhas de Resultados</h3></div>
        <div class="card-body">
            <p class="text-muted">Envie somente as modalidades aplicáveis à campanha. Cada planilha utiliza o modelo configurado para ela.</p>

            <div v-for="tipo in tipos" :key="tipo.key" class="border rounded p-3 mb-3">
                <div class="d-flex justify-content-between align-items-center mb-3 gap-2">
                    <h5 class="mb-0">{{ tipo.label }}</h5>
                    <button v-if="dados(tipo.key).arquivo || existente(tipo.key)" type="button" class="btn btn-outline-danger btn-sm" @click="removerPlanilha(tipo.key)">Remover</button>
                </div>

                <div v-if="dados(tipo.key).remover" class="alert alert-warning mb-0">
                    Esta planilha será removida quando a campanha for salva.
                    <button type="button" class="btn btn-link p-0 ms-1" @click="dados(tipo.key).remover = false">Desfazer</button>
                </div>

                <template v-else>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Modelo de planilha</label>
                            <div class="input-group">
                                <select v-model="dados(tipo.key).modulo_id" class="form-select" :class="{ 'is-invalid': erros[tipo.key] }" @change="erros[tipo.key] = ''">
                                    <option :value="null">Selecione um modelo</option>
                                    <option v-for="modulo in modulos" :key="modulo.id" :value="modulo.id">{{ modulo.nome || 'Módulo sem nome' }}</option>
                                </select>
                                <a v-if="dados(tipo.key).modulo_id" :href="route('sgc.contratada.produtos.modulos.configuracoes.gerar-planilha-modelo', [contrato, 'fauna', dados(tipo.key).modulo_id])" class="btn btn-primary">Baixar modelo</a>
                                <button v-else type="button" class="btn btn-secondary" disabled>Baixar modelo</button>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Planilha preenchida</label>
                            <input type="file" class="form-control" accept=".xlsx,.xls,.csv" @change="selecionarArquivo(tipo.key, $event)">
                            <small v-if="dados(tipo.key).arquivo" class="text-success d-block mt-1">Novo arquivo: <strong>{{ dados(tipo.key).arquivo.name }}</strong></small>
                            <small v-else-if="existente(tipo.key)" class="text-muted d-block mt-1">Arquivo atual: <a :href="existente(tipo.key).url" target="_blank" rel="noopener">{{ existente(tipo.key).nome_arquivo }}</a></small>
                            <small v-if="erros[tipo.key]" class="text-danger d-block mt-1">{{ erros[tipo.key] }}</small>
                        </div>
                    </div>

                    <div v-if="previews[tipo.key]?.error" class="alert alert-danger mb-0">{{ previews[tipo.key].error }}</div>
                    <div v-else-if="previews[tipo.key]?.rows?.length" class="table-responsive preview-table">
                        <table class="table table-bordered table-sm mb-0">
                            <thead class="table-light"><tr><th v-for="(column, index) in previews[tipo.key].columns" :key="index">{{ column }}</th></tr></thead>
                            <tbody><tr v-for="(row, index) in previews[tipo.key].rows" :key="index"><td v-for="(cell, cellIndex) in row" :key="cellIndex">{{ cell }}</td></tr></tbody>
                        </table>
                    </div>
                </template>
            </div>
        </div>
    </div>
</template>

<style scoped>
.preview-table { max-height: 320px; overflow: auto; }
.preview-table table { min-width: 900px; }
</style>
