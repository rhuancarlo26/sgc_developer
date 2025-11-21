<script setup>
import InputError from '@/Components/InputError.vue';
import NavButton from '@/Components/NavButton.vue';
import { ref } from 'vue';
import * as XLSX from 'xlsx';

// Props
const props = defineProps({
    formResultados: {
        type: Object,
        required: true,
    },
    resultadosRecords: {
        type: Array,
        default: () => [],
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
const resultadosRecords = ref(props.resultadosRecords);

// Baixar modelo de exemplo (apenas instrução)
const downloadModelo = () => {
    alert('Os modelos oficiais agora são os arquivos: Fauna Terrestre.xlsx, Fauna Aquática.xlsx e Fauna Cavernícola.xlsx.\n\nEles já estão no sistema e devem ser baixados na área de Modelos.');
};

// Processar planilha (somente leitura para preview)
const processarPlanilha = (event) => {
    const file = event.target.files[0];
    if (!file) return;

    props.formResultados.planilha = file; // ENVIA PRO BACK-END

    const reader = new FileReader();
    reader.onload = (e) => {
        const data = new Uint8Array(e.target.result);
        const workbook = XLSX.read(data, { type: 'array' });
        const sheet = workbook.Sheets[workbook.SheetNames[0]];
        const json = XLSX.utils.sheet_to_json(sheet, { defval: '' });

        resultadosRecords.value = json;
        emit('update:resultadosRecords', json);
    };
    reader.readAsArrayBuffer(file);
};

const avancar = () => {
    props.formResultados.consideracoes = consideracoes.value;
    emit('next');
};

const excluirResultado = (index) => {
    resultadosRecords.value.splice(index, 1);
    emit('update:resultadosRecords', resultadosRecords.value);
};
</script>

<template>
    <div>
        <h4 class="mb-3">RESULTADOS</h4>

        <!-- SELEÇÃO DO TIPO DA PLANILHA -->
        <div class="mb-3">
            <label class="form-label fw-bold">Tipo da Planilha</label>
            <select v-model="formResultados.tipo" class="form-select" required>
                <option value="">Selecione...</option>
                <option value="terrestre">Fauna Terrestre</option>
                <option value="aquatica">Fauna Aquática</option>
                <option value="cavernicola">Fauna Cavernícola</option>
            </select>
            <InputError :message="formResultados.errors?.tipo" />
        </div>

        <div class="mb-3">
            <button class="btn btn-secondary" @click="downloadModelo">
                Ver Modelos Disponíveis
            </button>
        </div>

        <!-- Upload -->
        <div class="mb-3">
            <label for="planilha" class="form-label fw-bold">Upload da Planilha Preenchida</label>
            <input
                type="file"
                class="form-control"
                id="planilha"
                accept=".xlsx,.xls"
                @change="processarPlanilha"
                required
            />
            <InputError :message="formResultados.errors?.planilha" />
        </div>

        <!-- Considerações -->
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

        <!-- Preview Dinâmico -->
        <div v-if="resultadosRecords.length" class="table-responsive">
            <table class="table table-bordered table-sm small">
                <thead>
                    <tr>
                        <th v-for="(value, key) in resultadosRecords[0]" :key="key">{{ key }}</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(linha, index) in resultadosRecords" :key="index">
                        <td v-for="(value, key) in linha" :key="key">{{ value }}</td>
                        <td>
                            <button class="btn btn-danger btn-sm" @click="excluirResultado(index)">Excluir</button>
                        </td>
                    </tr>
                </tbody>
            </table>
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
