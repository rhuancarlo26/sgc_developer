<script setup>
import InputError from '@/Components/InputError.vue';
import NavButton from '@/Components/NavButton.vue';
import { ref, computed } from 'vue';
import * as XLSX from 'xlsx';

// Props
const props = defineProps({
    formResultados: {
        type: Object,
        required: true,
    },
    // Agora vamos tratar como um "objeto" de listas,
    // mas se vier vazio ou em outro formato, caímos no default.
    resultadosRecords: {
        type: Object,
        default: () => ({}),
    },
    idCampanha: {
        type: [String, Number],
        required: true,
    },
});

// Emits
const emit = defineEmits(['update:resultadosRecords', 'prev', 'next']);

// Estado
const consideracoes = ref(props.formResultados.consideracoes ?? '');

// Estrutura local para manter as 3 listas de preview
const initial = props.resultadosRecords || {};
const resultados = ref({
    terrestre: Array.isArray(initial.terrestre) ? initial.terrestre : [],
    aquatica: Array.isArray(initial.aquatica) ? initial.aquatica : [],
    cavernicola: Array.isArray(initial.cavernicola) ? initial.cavernicola : [],
});

// Arquivos locais (opcional, mais controle no componente)
const planilhaTerrestre = ref(null);
const planilhaAquatica = ref(null);
const planilhaCavernicola = ref(null);

// Aba ativa (sub-aba)
const activeTipo = ref('terrestre');

// Lista de tipos para iterar na view
const tipos = [
    { key: 'terrestre', label: 'Fauna Terrestre' },
    { key: 'aquatica', label: 'Fauna Aquática' },
    { key: 'cavernicola', label: 'Fauna Cavernícola' },
];

// Preview da aba ativa
const previewAtivo = computed(() => resultados.value[activeTipo.value] || []);

// Baixar modelo de exemplo (apenas instrução)
const downloadModelo = () => {
    alert(
        'Os modelos oficiais agora são os arquivos:\n' +
        '- Fauna Terrestre.xlsx\n' +
        '- Fauna Aquática.xlsx\n' +
        '- Fauna Cavernícola.xlsx\n\n' +
        'Eles já estão no sistema e devem ser baixados na área de Modelos.'
    );
};

// Função genérica para processar planilha de um tipo
const processarPlanilha = (tipo, event) => {
    const file = event.target.files[0];
    if (!file) return;

    // Guarda no formResultados para o backend
    if (tipo === 'terrestre') {
        planilhaTerrestre.value = file;
        props.formResultados.planilha_terrestre = file;
    } else if (tipo === 'aquatica') {
        planilhaAquatica.value = file;
        props.formResultados.planilha_aquatica = file;
    } else if (tipo === 'cavernicola') {
        planilhaCavernicola.value = file;
        props.formResultados.planilha_cavernicola = file;
    }

    const reader = new FileReader();
    reader.onload = (e) => {
        const data = new Uint8Array(e.target.result);
        const workbook = XLSX.read(data, { type: 'array' });
        const sheet = workbook.Sheets[workbook.SheetNames[0]];
        const json = XLSX.utils.sheet_to_json(sheet, { defval: '' });

        // Atualiza apenas a lista daquele tipo
        resultados.value[tipo] = json;

        // Emite objeto completo para o pai (mantém compatibilidade conceitual)
        emit('update:resultadosRecords', {
            terrestre: resultados.value.terrestre,
            aquatica: resultados.value.aquatica,
            cavernicola: resultados.value.cavernicola,
        });
    };
    reader.readAsArrayBuffer(file);
};

const excluirResultado = (tipo, index) => {
    resultados.value[tipo].splice(index, 1);
    emit('update:resultadosRecords', {
        terrestre: resultados.value.terrestre,
        aquatica: resultados.value.aquatica,
        cavernicola: resultados.value.cavernicola,
    });
};

const avancar = () => {
    props.formResultados.consideracoes = consideracoes.value;
    emit('next');
};
</script>

<template>
    <div>
        <h4 class="mb-3">RESULTADOS</h4>

        <!-- Botão para instrução de modelos -->
        <div class="mb-3">
            <button class="btn btn-secondary" type="button" @click="downloadModelo">
                Ver Modelos Disponíveis
            </button>
        </div>

        <!-- SUB-ABAS: TERRESTRE / AQUÁTICA / CAVERNÍCOLA -->
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

        <!-- CONTEÚDO DA SUB-ABA ATIVA -->
        <div class="tab-content mb-4">
            <!-- TERRESTRE -->
            <div
                v-if="activeTipo === 'terrestre'"
                class="tab-pane fade show active"
            >
                <h5 class="mb-2">Planilha - Fauna Terrestre</h5>

                <div class="mb-3">
                    <label for="planilha_terrestre" class="form-label fw-bold">
                        Upload da Planilha Preenchida (Terrestre)
                    </label>
                    <input
                        type="file"
                        class="form-control"
                        id="planilha_terrestre"
                        accept=".xlsx,.xls"
                        @change="(e) => processarPlanilha('terrestre', e)"
                    />
                    <!-- Por enquanto, erro genérico; depois você pode criar
                         validações específicas tipo 'planilha_terrestre' -->
                    <InputError :message="formResultados.errors?.planilha_terrestre || formResultados.errors?.planilha" />
                </div>

                <!-- Preview Terrestre -->
                <div v-if="resultados.terrestre.length" class="table-responsive">
                    <table class="table table-bordered table-sm small">
                        <thead>
                            <tr>
                                <th v-for="(value, key) in resultados.terrestre[0]" :key="key">
                                    {{ key }}
                                </th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(linha, index) in resultados.terrestre" :key="index">
                                <td v-for="(value, key) in linha" :key="key">{{ value }}</td>
                                <td>
                                    <button
                                        class="btn btn-danger btn-sm"
                                        type="button"
                                        @click="excluirResultado('terrestre', index)"
                                    >
                                        Excluir
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div v-else class="alert alert-info">
                    Nenhum dado carregado para Fauna Terrestre.
                </div>
            </div>

            <!-- AQUÁTICA -->
            <div
                v-else-if="activeTipo === 'aquatica'"
                class="tab-pane fade show active"
            >
                <h5 class="mb-2">Planilha - Fauna Aquática</h5>

                <div class="mb-3">
                    <label for="planilha_aquatica" class="form-label fw-bold">
                        Upload da Planilha Preenchida (Aquática)
                    </label>
                    <input
                        type="file"
                        class="form-control"
                        id="planilha_aquatica"
                        accept=".xlsx,.xls"
                        @change="(e) => processarPlanilha('aquatica', e)"
                    />
                    <InputError :message="formResultados.errors?.planilha_aquatica || formResultados.errors?.planilha" />
                </div>

                <!-- Preview Aquática -->
                <div v-if="resultados.aquatica.length" class="table-responsive">
                    <table class="table table-bordered table-sm small">
                        <thead>
                            <tr>
                                <th v-for="(value, key) in resultados.aquatica[0]" :key="key">
                                    {{ key }}
                                </th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(linha, index) in resultados.aquatica" :key="index">
                                <td v-for="(value, key) in linha" :key="key">{{ value }}</td>
                                <td>
                                    <button
                                        class="btn btn-danger btn-sm"
                                        type="button"
                                        @click="excluirResultado('aquatica', index)"
                                    >
                                        Excluir
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div v-else class="alert alert-info">
                    Nenhum dado carregado para Fauna Aquática.
                </div>
            </div>

            <!-- CAVERNÍCOLA -->
            <div
                v-else-if="activeTipo === 'cavernicola'"
                class="tab-pane fade show active"
            >
                <h5 class="mb-2">Planilha - Fauna Cavernícola</h5>

                <div class="mb-3">
                    <label for="planilha_cavernicola" class="form-label fw-bold">
                        Upload da Planilha Preenchida (Cavernícola)
                    </label>
                    <input
                        type="file"
                        class="form-control"
                        id="planilha_cavernicola"
                        accept=".xlsx,.xls"
                        @change="(e) => processarPlanilha('cavernicola', e)"
                    />
                    <InputError :message="formResultados.errors?.planilha_cavernicola || formResultados.errors?.planilha" />
                </div>

                <!-- Preview Cavernícola -->
                <div v-if="resultados.cavernicola.length" class="table-responsive">
                    <table class="table table-bordered table-sm small">
                        <thead>
                            <tr>
                                <th v-for="(value, key) in resultados.cavernicola[0]" :key="key">
                                    {{ key }}
                                </th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(linha, index) in resultados.cavernicola" :key="index">
                                <td v-for="(value, key) in linha" :key="key">{{ value }}</td>
                                <td>
                                    <button
                                        class="btn btn-danger btn-sm"
                                        type="button"
                                        @click="excluirResultado('cavernicola', index)"
                                    >
                                        Excluir
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div v-else class="alert alert-info">
                    Nenhum dado carregado para Fauna Cavernícola.
                </div>
            </div>
        </div>

        <!-- Considerações (única para a campanha toda) -->
        <div class="mb-3">
            <label for="consideracoes" class="form-label fw-bold">Considerações</label>
            <textarea
                v-model="consideracoes"
                class="form-control"
                id="consideracoes"
                rows="4"
                placeholder="Digite suas considerações..."
            ></textarea>
        </div>

        <!-- Navegação -->
        <div class="d-flex justify-content-between mt-4">
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
