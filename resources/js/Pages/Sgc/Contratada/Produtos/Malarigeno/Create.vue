<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import NavLink from '@/Components/NavLink.vue';
import {
    IconClipboardList,
    IconCalendar,
    IconNotes,
    IconDeviceAnalytics,
    IconPlane,
    IconLayoutDashboard
} from "@tabler/icons-vue";
import Breadcrumb from '@/Components/Breadcrumb.vue';
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import * as XLSX from 'xlsx';
import CardFotos from '../Modulos/Importador/Components/CardFotos.vue';
import CardAnexos from '../Modulos/Importador/Components/CardAnexos.vue';

const props = defineProps({
    contrato: [String, Number],
    produto: { type: String, default: 'Malarigeno' },
    contratos: { type: Object, default: () => ({ contratada: 'Contratada', tipo_contrato: null }) },
    modulos: { type: Array, default: () => [] },
    subproduto: { type: [String, Number], default: null },
});

const selectedFileName = ref(null);
const previewColumns = ref([]);
const previewRows = ref([]);
const fileError = ref(null);
const inputArquivoRef = ref(null);
const CardFotosRef = ref(null);
const CardAnexosRef = ref(null);

// default compact menu width (change this value in code to adjust)
const menuWidth = ref(200);

const form = useForm({
    contrato_id: props.contrato,
    subproduto: props.subproduto,
    modulo_id: null,
    arquivo: null,
    fotos: [],
    anexos: [],
    enviar_analise: false,
    status: null,
});

const salvarRascunho = () => {
    console.log('Malarigeno: salvarRascunho chamado', form);

    if (CardFotosRef.value?.validarCampos?.()) {
        console.log('Malarigeno: validação de fotos falhou');
        return;
    }

    if (CardAnexosRef.value?.validarCampos?.()) {
        console.log('Malarigeno: validação de anexos falhou');
        return;
    }

    form.enviar_analise = false;
    submitForm();
};

const enviarParaAnalise = () => {
    console.log('Malarigeno: enviarParaAnalise chamado', form);

    if (CardFotosRef.value?.validarCampos?.()) {
        console.log('Malarigeno: validação de fotos falhou');
        return;
    }

    if (CardAnexosRef.value?.validarCampos?.()) {
        console.log('Malarigeno: validação de anexos falhou');
        return;
    }

    form.enviar_analise = true;
    submitForm();
};

const submitForm = () => {
    const url = route('sgc.contratada.produtos.store', [props.contrato, props.produto.toLowerCase()]);
    console.log('Malarigeno: submitForm', { url, form });

    form.post(url, {
        preserveScroll: true,
        preserveState: true,
        forceFormData: true,
        onSuccess: () => {
            console.log('Malarigeno: form enviado com sucesso');
            form.arquivo = null;
            resetPreview();
            if (inputArquivoRef.value) {
                inputArquivoRef.value.value = '';
            }
        },
        onError: (errors) => {
            console.log('Malarigeno: erros do post', errors);
        },
    });
};

const resetPreview = () => {
    selectedFileName.value = null;
    previewColumns.value = [];
    previewRows.value = [];
    fileError.value = null;
};

const limparArquivo = () => {
    form.arquivo = null;
    resetPreview();

    if (inputArquivoRef.value) {
        inputArquivoRef.value.value = '';
    }
};

const handleFileChange = (event) => {
    const file = event.target.files?.[0];
    resetPreview();

    if (!file) {
        return;
    }

    form.arquivo = file;
    selectedFileName.value = file.name;

    const reader = new FileReader();

    reader.onload = (loadEvent) => {
        try {
            const data = loadEvent.target?.result;
            const workbook = XLSX.read(data, { type: 'array' });
            const sheet = workbook.Sheets[workbook.SheetNames[0]];
            const rows = XLSX.utils.sheet_to_json(sheet, { header: 1, raw: false });

            if (!rows || rows.length === 0) {
                fileError.value = 'A planilha está vazia ou não pôde ser lida.';
                return;
            }

            previewColumns.value = rows[0].map((value, index) => value || `Coluna ${index + 1}`);
            previewRows.value = rows.slice(1, 11).map((row) =>
                previewColumns.value.map((_, index) => row?.[index] ?? '')
            );

            if (previewRows.value.length === 0 && rows.length > 1) {
                previewRows.value = [rows[1].map((value) => value ?? '')];
            }
        } catch (error) {
            fileError.value = 'Erro ao ler a planilha. Tente um arquivo .xlsx, .xls ou .csv válido.';
        }
    };

    reader.onerror = () => {
        fileError.value = 'Não foi possível ler o arquivo selecionado.';
    };

    reader.readAsArrayBuffer(file);
};
</script>

<template>
    <AuthenticatedLayout>
        <template #header>
            <Breadcrumb
                :links="[
                    { route: route('sgc.gestao.listagem', props.contratos.tipo_contrato), label: 'Gestão de Contratos' },
                    { route: route('sgc.contratada.produtos.index', [props.contrato, props.produto.toLowerCase()]), label: props.contratos.contratada },
                    { route: '#', label: `Cadastrar ${props.produto}` },
                ]"
            />
        </template>

        <div class="d-flex">
            <div class="me-3 menu-column" :style="{ width: menuWidth + 'px', minWidth: '120px', flex: '0 0 auto' }">
                <div class="card">
                    <div class="card-body p-2">
                        <ul class="navbar-nav mb-0">
                            <li>
                                <NavLink :route-name="'sgc.contratada.relatorios.index'" :param="props.contrato" title="Relatório de Coordenação" :icon="IconClipboardList" />
                            </li>
                            <li>
                                <NavLink :route-name="'sgc.contratada.produtos.index'" :param="[props.contrato, 'fauna']" title="Produtos" :icon="IconLayoutDashboard" />
                            </li>
                            <li>
                                <NavLink :route-name="'sgc.contratada.cronograma.index'" :param="props.contrato" title="Cronograma Físico" :icon="IconCalendar" />
                            </li>
                            <li>
                                <NavLink :route-name="'sgc.contratada.quantitativos.index'" :param="props.contrato" title="Quantitativos" :icon="IconDeviceAnalytics" />
                            </li>
                            <li>
                                <NavLink :route-name="'sgc.gestao.listagemDav'" :param="props.contrato" title="DAV" :icon="IconPlane" />
                            </li>
                            <li>
                                <NavLink :route-name="'sgc.contratada.ficha.index'" :param="props.contrato" title="Ficha Contratual" :icon="IconNotes" />
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="flex-fill content-column">
                <div class="card mb-3">
                    <div class="card-header">
                        <h3 class="my-0">Informações Gerais</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="moduloSelect" class="form-label fw-semibold">Planilha modelo <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <select id="moduloSelect" class="form-select" v-model="form.modulo_id">
                                        <option value="" disabled>Selecione uma planilha modelo</option>
                                        <option v-for="modulo in props.modulos" :key="modulo.id" :value="modulo.id">{{ modulo.nome || 'Módulo sem nome' }}</option>
                                    </select>
                                    <a v-if="form.modulo_id" :href="route('sgc.contratada.produtos.modulos.configuracoes.gerar-planilha-modelo', [props.contrato, props.produto.toLowerCase(), form.modulo_id])" class="btn btn-primary">Baixar</a>
                                    <button v-else type="button" class="btn btn-secondary" disabled>Baixar</button>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="upload_arquivo" class="form-label fw-semibold">Upload da Planilha (.csv/.xlsx) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input ref="inputArquivoRef" type="file" id="upload_arquivo" @change="handleFileChange" class="form-control" accept=".xlsx,.csv" />
                                    <button type="button" class="btn btn-primary" :disabled="!selectedFileName" @click="limparArquivo">Limpar</button>
                                </div>
                                <small v-if="selectedFileName" class="text-muted d-block mt-2 text-truncate d-inline-block" style="max-width: 100%;">
                                    Arquivo: <strong>{{ selectedFileName }}</strong>
                                </small>
                                <input type="hidden" name="subproduto" :value="form.subproduto" />
                                <small v-if="fileError" class="text-danger d-block mt-2">{{ fileError }}</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3 class="my-0">Dados da Planilha</h3>
                    </div>
                    <div class="card-body">
                        <div v-if="previewRows.length" class="table-responsive data-table-scroll">
                            <table class="table table-bordered table-sm mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th v-for="(column, index) in previewColumns" :key="index" class="text-nowrap">{{ column }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(row, rowIndex) in previewRows" :key="rowIndex">
                                        <td v-for="(cell, cellIndex) in row" :key="cellIndex">{{ cell }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div v-else class="text-muted">Nenhum dado de planilha carregado. Faça o upload para ver a pré-visualização.</div>
                    </div>
                </div>

                <form @submit.prevent="salvarRascunho" class="d-flex flex-column gap-4 mt-4">
                    <CardFotos ref="CardFotosRef" :form="form" />
                    <CardAnexos ref="CardAnexosRef" :form="form" />

                    <div class="card">
                        <div class="card-body d-flex justify-content-end gap-2">
                            <button type="button" class="btn btn-light" @click="salvarRascunho">
                                Salvar Rascunho
                            </button>
                            <button type="button" class="btn btn-primary" @click="enviarParaAnalise">
                                Enviar para Análise
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.card {
    max-width: 2650px;
    margin: 0 auto;
}

.content-column {
    min-width: 0;
}

.data-table-scroll {
    overflow-x: auto;
}

.data-table-scroll table {
    min-width: 1000px;
}

.text-muted {
    color: #6c757d;
}
</style>

