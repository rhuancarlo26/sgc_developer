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

// Dados
const consideracoes = ref('');
const resultadosRecords = ref(props.resultadosRecords);

const downloadModelo = () => {
    const headers = [
        'ID Campanha', 'Módulo', 'Parcela', 'ID Armadilha', 'Grupo Amostrado', 'Data do Registro', 'Hora do Registro',
        'Categoria', 'Classe', 'Ordem', 'Família', 'Gênero', 'Espécie', 'Nome Comum', 'Sexo', 'Faixa Etária',
        'Qnt de Indivíduos', 'Num Marcação', 'Coletado', 'Num de Tombamento', 'Dados Biométricos', 'Comp total',
        'Cabeça', 'Cauda', 'Fêmur', 'Orelha', 'Peso', 'Status Conservação Federal', 'Status Conservação IUCN',
        'Espécies Bioindicadoras', 'Espécies Alvo de Monitoramento'
    ];
    const ws = XLSX.utils.json_to_sheet([{}], { header: headers });
    const wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, 'Resultados');
    
    const wbout = XLSX.write(wb, { bookType: 'xlsx', type: 'array' });
    const blob = new Blob([wbout], { type: 'application/octet-stream' });
    const url = window.URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = url;
    link.download = 'modelo_resultados.xlsx';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    window.URL.revokeObjectURL(url);
};

const processarPlanilha = (event) => {
    const file = event.target.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = (e) => {
        const data = new Uint8Array(e.target.result);
        const workbook = XLSX.read(data, { type: 'array', cellDates: false });
        const sheet = workbook.Sheets[workbook.SheetNames[0]];
        const json = XLSX.utils.sheet_to_json(sheet);

        const novosResultados = json.map(row => ({
            id: Date.now() + Math.random(),
            id_campanha: row['ID Campanha'] || null,
            modulo: row['Módulo'] || 0,
            parcela: row['Parcela'] || 0,
            id_armadilha: row['ID Armadilha'] || 0,
            grupo_amostrado: row['Grupo Amostrado'] || '',
            data_registro: row['Data do Registro'] || '',
            hora_registro: row['Hora do Registro'] || null,
            categoria: row['Categoria'] || null,
            classe: row['Classe'] || null,
            ordem: row['Ordem'] || null,
            familia: row['Família'] || null,
            genero: row['Gênero'] || null,
            especie: row['Espécie'] || '',
            nome_comum: row['Nome Comum'] || null,
            sexo: row['Sexo'] || null,
            faixa_etaria: row['Faixa Etária'] || null,
            qnt_individuos: row['Qnt de Indivíduos'] || 0,
            num_marcacao: row['Num Marcação'] || 0,
            coletado: row['Coletado'] || null,
            num_tombamento: row['Num de Tombamento'] || null,
            dados_biometricos: row['Dados Biométricos'] || null,
            comp_total: row['Comp total'] || 0,
            cabeca: row['Cabeça'] || 0,
            cauda: row['Cauda'] || 0,
            femur: row['Fêmur'] || 0,
            orelha: row['Orelha'] || 0,
            peso: row['Peso'] || 0,
            status_conservacao_federal: row['Status Conservação Federal'] || null,
            status_conservacao_iucn: row['Status Conservação IUCN'] || null,
            especies_bioindicadoras: row['Espécies Bioindicadoras'] || null,
            especies_alvo_monitoramento: row['Espécies Alvo de Monitoramento'] || null,
        }));

        resultadosRecords.value = novosResultados;
        emit('update:resultadosRecords', novosResultados);
        props.formResultados.planilha = file;
    };
    reader.readAsArrayBuffer(file);
};

const avancar = () => {
    props.formResultados.consideracoes = consideracoes.value;
    emit('next', { consideracoes: consideracoes.value });
};

const excluirResultado = (id) => {
    const novosResultados = resultadosRecords.value.filter(item => item.id !== id);
    resultadosRecords.value = novosResultados;
    emit('update:resultadosRecords', novosResultados);
};
</script>

<template>
    <div>
        <h4 class="mb-3">RESULTADOS</h4>
        <div class="mb-3">
            <button class="btn btn-primary" @click="downloadModelo">Baixar Planilha Modelo</button>
        </div>
        <div class="mb-3">
            <label for="planilha" class="form-label">Upload da Planilha Preenchida</label>
            <input
                type="file"
                class="form-control"
                id="planilha"
                accept=".xlsx,.xls"
                @change="processarPlanilha"
            />
            <InputError :message="formResultados.errors.planilha" />
        </div>
        <div class="mb-3">
            <label for="consideracoes" class="form-label">Considerações</label>
            <textarea
                v-model="consideracoes"
                class="form-control"
                id="consideracoes"
                rows="4"
                placeholder="Digite suas considerações aqui..."
            ></textarea>
            <InputError :message="formResultados.errors.consideracoes" />
        </div>
        <div v-if="resultadosRecords.length" class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID Campanha</th>
                        <th>Módulo</th>
                        <th>Parcela</th>
                        <th>ID Armadilha</th>
                        <th>Grupo Amostrado</th>
                        <th>Data do Registro</th>
                        <th>Hora do Registro</th>
                        <th>Categoria</th>
                        <th>Classe</th>
                        <th>Ordem</th>
                        <th>Família</th>
                        <th>Gênero</th>
                        <th>Espécie</th>
                        <th>Nome Comum</th>
                        <th>Sexo</th>
                        <th>Faixa Etária</th>
                        <th>Qnt de Indivíduos</th>
                        <th>Num Marcação</th>
                        <th>Coletado</th>
                        <th>Num de Tombamento</th>
                        <th>Dados Biométricos</th>
                        <th>Comp total</th>
                        <th>Cabeça</th>
                        <th>Cauda</th>
                        <th>Fêmur</th>
                        <th>Orelha</th>
                        <th>Peso</th>
                        <th>Status Conservação Federal</th>
                        <th>Status Conservação IUCN</th>
                        <th>Espécies Bioindicadoras</th>
                        <th>Espécies Alvo de Monitoramento</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="resultado in resultadosRecords" :key="resultado.id">
                        <td>{{ resultado.id_campanha }}</td>
                        <td>{{ resultado.modulo }}</td>
                        <td>{{ resultado.parcela }}</td>
                        <td>{{ resultado.id_armadilha }}</td>
                        <td>{{ resultado.grupo_amostrado }}</td>
                        <td>{{ resultado.data_registro }}</td>
                        <td>{{ resultado.hora_registro }}</td>
                        <td>{{ resultado.categoria }}</td>
                        <td>{{ resultado.classe }}</td>
                        <td>{{ resultado.ordem }}</td>
                        <td>{{ resultado.familia }}</td>
                        <td>{{ resultado.genero }}</td>
                        <td>{{ resultado.especie }}</td>
                        <td>{{ resultado.nome_comum }}</td>
                        <td>{{ resultado.sexo }}</td>
                        <td>{{ resultado.faixa_etaria }}</td>
                        <td>{{ resultado.qnt_individuos }}</td>
                        <td>{{ resultado.num_marcacao }}</td>
                        <td>{{ resultado.coletado }}</td>
                        <td>{{ resultado.num_tombamento }}</td>
                        <td>{{ resultado.dados_biometricos }}</td>
                        <td>{{ resultado.comp_total }}</td>
                        <td>{{ resultado.cabeca }}</td>
                        <td>{{ resultado.cauda }}</td>
                        <td>{{ resultado.femur }}</td>
                        <td>{{ resultado.orelha }}</td>
                        <td>{{ resultado.peso }}</td>
                        <td>{{ resultado.status_conservacao_federal }}</td>
                        <td>{{ resultado.status_conservacao_iucn }}</td>
                        <td>{{ resultado.especies_bioindicadoras }}</td>
                        <td>{{ resultado.especies_alvo_monitoramento }}</td>
                        <td>
                            <button class="btn btn-danger btn-sm" @click="excluirResultado(resultado.id)">Excluir</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
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