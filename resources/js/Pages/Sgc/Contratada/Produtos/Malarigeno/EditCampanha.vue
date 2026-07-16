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
import { ref, onMounted } from 'vue';
import { useForm } from '@inertiajs/vue3';
import * as XLSX from 'xlsx';
import CardFotos from '../Modulos/Importador/Components/CardFotos.vue';
import CardAnexos from '../Modulos/Importador/Components/CardAnexos.vue';

const props = defineProps({
    campanha: Object,
    modulos: Array,
    contrato: [String, Number],
    produto: String,
    contratos: { type: Object, default: () => ({ contratada: 'Contratada', tipo_contrato: null }) },
});

const menuWidth = ref(200);
const inputArquivoRef = ref(null);
const CardFotosRef = ref(null);
const CardAnexosRef = ref(null);
const selectedFileName = ref(props.campanha?.planilha_nome);

const fotosOriginais = (props.campanha?.fotos || []).map(f => f.id);
const anexosOriginais = (props.campanha?.anexos || []).map(a => a.id);

const form = useForm({
    subproduto: props.campanha?.subproduto || '',
    modulo_id: props.campanha?.modulo_id || null,
    arquivo: null,
    fotos: props.campanha?.fotos || [],
    anexos: props.campanha?.anexos || [],
});

const planilhaColumns = ref([]);
const planilhaRows = ref([]);
const planilhaLoading = ref(false);
const planilhaError = ref(null);
const currentPage = ref(1);
const rowsPerPage = 10;

const totalPages = () => Math.max(1, Math.ceil(planilhaRows.value.length / rowsPerPage));
const paginatedRows = () => {
    const start = (currentPage.value - 1) * rowsPerPage;
    return planilhaRows.value.slice(start, start + rowsPerPage);
};
const planilhaRangeLabel = () => {
    if (!planilhaRows.value.length) {
        return '0 de 0';
    }

    const start = (currentPage.value - 1) * rowsPerPage + 1;
    const end = Math.min(currentPage.value * rowsPerPage, planilhaRows.value.length);
    return `${start}-${end} de ${planilhaRows.value.length}`;
};

const parseWorkbookBuffer = (buffer) => {
    const workbook = XLSX.read(buffer, { type: 'array' });
    const sheet = workbook.Sheets[workbook.SheetNames[0]];
    const rows = XLSX.utils.sheet_to_json(sheet, { header: 1, raw: false });

    if (!rows.length) {
        planilhaColumns.value = [];
        planilhaRows.value = [];
        planilhaError.value = 'A planilha está vazia.';
        return;
    }

    const maxColumns = rows.reduce((max, row) => Math.max(max, row?.length || 0), 0);
    const header = rows[0] || [];

    planilhaColumns.value = Array.from({ length: maxColumns }, (_, index) => header[index] || `Coluna ${index + 1}`);
    planilhaRows.value = rows.slice(1).map(row =>
        planilhaColumns.value.map((_, index) => row?.[index] ?? '')
    );
};

const loadPlanilha = async () => {
    planilhaColumns.value = [];
    planilhaRows.value = [];
    planilhaError.value = null;
    currentPage.value = 1;

    if (!form.arquivo && !props.campanha?.planilha_url) {
        return;
    }

    planilhaLoading.value = true;

    try {
        let buffer;

        if (form.arquivo) {
            buffer = await form.arquivo.arrayBuffer();
        } else {
            const response = await fetch(props.campanha.planilha_url);
            if (!response.ok) {
                throw new Error('Não foi possível baixar a planilha.');
            }
            buffer = await response.arrayBuffer();
        }

        parseWorkbookBuffer(buffer);
    } catch (error) {
        planilhaColumns.value = [];
        planilhaRows.value = [];
        planilhaError.value = error?.message || 'Erro ao carregar a planilha.';
    } finally {
        planilhaLoading.value = false;
    }
};

const previousPage = () => {
    if (currentPage.value > 1) {
        currentPage.value -= 1;
    }
};

const nextPage = () => {
    if (currentPage.value < totalPages()) {
        currentPage.value += 1;
    }
};

const limparArquivo = () => {
    form.arquivo = null;
    selectedFileName.value = props.campanha?.planilha_nome;
    if (inputArquivoRef.value) {
        inputArquivoRef.value.value = '';
    }
    loadPlanilha();
};

const handleFileChange = (event) => {
    const file = event.target.files?.[0];
    if (!file) return;

    form.arquivo = file;
    selectedFileName.value = file.name;
    loadPlanilha();
};

onMounted(loadPlanilha);

const salvar = () => {
    if (CardFotosRef.value?.validarCampos?.()) {
        console.log('Validação de fotos falhou');
        return;
    }

    if (CardAnexosRef.value?.validarCampos?.()) {
        console.log('Validação de anexos falhou');
        return;
    }

    submitForm();
};

const submitForm = () => {
    const url = route('sgc.contratada.produtos.malarigeno.update', [props.contrato, props.produto, props.campanha.id]);

    const fotosRemovidas = [];
    const fotosNovas = [];
    const fotosAtualizadas = [];

    form.fotos.forEach(foto => {
        if (foto.arquivo && foto.id) {
            // Foto existente com novo arquivo = deletar antiga + adicionar nova
            fotosRemovidas.push(foto.id);
            fotosNovas.push(foto);
        } else if (foto.arquivo && !foto.id) {
            // Foto nova (sem ID)
            fotosNovas.push(foto);
        } else if (foto.id && foto.nome_arquivo) {
            // Foto existente mantida sem mudanças
            fotosAtualizadas.push(foto.id);
        }
    });

    // IDs deletados = originais - atualizadas
    const fotos_remover = fotosOriginais.filter(id => !fotosAtualizadas.includes(id));

    const anexosRemovidos = [];
    const anexosNovos = [];
    const anexosAtualizados = [];

    form.anexos.forEach(anexo => {
        if (anexo.arquivo && anexo.id) {
            // Anexo existente com novo arquivo = deletar antigo + adicionar novo
            anexosRemovidos.push(anexo.id);
            anexosNovos.push(anexo);
        } else if (anexo.arquivo && !anexo.id) {
            // Anexo novo (sem ID)
            anexosNovos.push(anexo);
        } else if (anexo.id && anexo.nome_arquivo) {
            // Anexo existente mantido sem mudanças
            anexosAtualizados.push(anexo.id);
        }
    });

    // IDs deletados = originais - atualizados
    const anexos_remover = anexosOriginais.filter(id => !anexosAtualizados.includes(id));

    const fd = new FormData();

    fd.append('subproduto', form.subproduto);
    if (form.modulo_id) fd.append('modulo_id', form.modulo_id);
    if (form.arquivo) fd.append('arquivo', form.arquivo);

    fotos_remover.forEach((id, i) => {
        fd.append(`fotos_remover[${i}]`, id);
    });

    anexos_remover.forEach((id, i) => {
        fd.append(`anexos_remover[${i}]`, id);
    });

    fotosNovas.forEach((foto, i) => {
        if (foto.arquivo) fd.append(`novas_fotos[${i}]`, foto.arquivo);
    });

    anexosNovos.forEach((anexo, i) => {
        if (anexo.arquivo) fd.append(`novos_anexos[${i}]`, anexo.arquivo);
    });

    form.post(url, {
        data: fd,
        preserveScroll: true,
        preserveState: true,
        forceFormData: true,
        onSuccess: () => {
            console.log('Campanha atualizada com sucesso');
        },
        onError: (errors) => {
            console.log('Erros:', errors);
        },
    });
};

const voltar = () => {
    window.history.back();
};
</script>

<template>
    <AuthenticatedLayout>
        <template #header>
            <Breadcrumb
                :links="[
                    { route: route('sgc.gestao.listagem', props.contratos.tipo_contrato), label: 'Gestão de Contratos' },
                    { route: route('sgc.contratada.produtos.index', [props.contrato, props.produto.toLowerCase()]), label: props.contratos.contratada },
                    { route: '#', label: `Editar ${props.produto}` },
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
                                <label class="form-label fw-semibold">Subproduto <span class="text-danger">*</span></label>
                                <input
                                    v-model="form.subproduto"
                                    type="text"
                                    class="form-control"
                                    required
                                />
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="moduloSelect" class="form-label fw-semibold">Planilha modelo</label>
                                <select id="moduloSelect" class="form-select" v-model="form.modulo_id">
                                    <option :value="null">Selecione uma planilha modelo</option>
                                    <option v-for="modulo in props.modulos" :key="modulo.id" :value="modulo.id">
                                        {{ modulo.nome || 'Módulo sem nome' }}
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-header">
                        <h3 class="my-0">Planilha</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 mb-3">
                                <div v-if="props.campanha?.planilha_nome" class="alert alert-info">
                                    <strong>Arquivo atual:</strong> {{ props.campanha?.planilha_nome }}
                                    <a v-if="props.campanha?.planilha_url" :href="props.campanha?.planilha_url" target="_blank" class="btn btn-sm btn-outline-info ms-2">
                                        📥 Download
                                    </a>
                                </div>
                            </div>

                            <div class="col-12">
                                <label for="upload_arquivo" class="form-label fw-semibold">Substituir Planilha (.xlsx)</label>
                                <div class="input-group">
                                    <input
                                        ref="inputArquivoRef"
                                        type="file"
                                        id="upload_arquivo"
                                        @change="handleFileChange"
                                        class="form-control"
                                        accept=".xlsx,.xls"
                                    />
                                    <button type="button" class="btn btn-primary" :disabled="!selectedFileName || selectedFileName === props.campanha?.planilha_nome" @click="limparArquivo">
                                        Limpar
                                    </button>
                                </div>
                                <small v-if="selectedFileName && selectedFileName !== props.campanha?.planilha_nome" class="text-success d-block mt-2">
                                    Novo arquivo: <strong>{{ selectedFileName }}</strong>
                                </small>
                            </div>
                        </div>

                        <div class="sheet-preview mt-3">
                            <div v-if="planilhaLoading" class="empty-state">Carregando dados da planilha...</div>
                            <div v-else-if="planilhaError" class="empty-state text-danger">{{ planilhaError }}</div>
                            <div v-else-if="planilhaColumns.length && planilhaRows.length">
                                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
                                    <span class="text-muted small">Linhas {{ planilhaRangeLabel() }}</span>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-outline-secondary btn-sm" :disabled="currentPage === 1" @click="previousPage">
                                            Anterior
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary btn-sm" disabled>
                                            {{ currentPage }} / {{ totalPages() }}
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary btn-sm" :disabled="currentPage === totalPages()" @click="nextPage">
                                            Próxima
                                        </button>
                                    </div>
                                </div>

                                <div class="table-responsive sheet-table-wrapper">
                                    <table class="table table-sm table-bordered table-hover mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th v-for="(column, columnIndex) in planilhaColumns" :key="columnIndex" class="text-nowrap">
                                                    {{ column }}
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(row, rowIndex) in paginatedRows()" :key="rowIndex">
                                                <td v-for="(cell, cellIndex) in row" :key="cellIndex">
                                                    {{ cell || '-' }}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div v-else-if="form.arquivo || props.campanha?.planilha_url" class="empty-state">A planilha não possui linhas para exibir.</div>
                        </div>
                    </div>
                </div>

                <form @submit.prevent="salvar" class="d-flex flex-column gap-4">
                    <CardFotos ref="CardFotosRef" :form="form" />
                    <CardAnexos ref="CardAnexosRef" :form="form" />

                    <div class="card">
                        <div class="card-body d-flex justify-content-end gap-2">
                            <button type="button" class="btn btn-light" @click="voltar">
                                Cancelar
                            </button>
                            <button type="submit" class="btn btn-primary">
                                Salvar Alterações
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

.empty-state {
    border: 1px dashed #c9ced3;
    border-radius: 6px;
    color: #6c757d;
    padding: 16px;
    text-align: center;
}

.sheet-preview {
    border: 1px solid #e2e4e6;
    border-radius: 6px;
    padding: 12px;
}

.sheet-table-wrapper {
    max-height: 520px;
    overflow-x: auto;
}

.sheet-table-wrapper th,
.sheet-table-wrapper td {
    max-width: 320px;
    min-width: 120px;
    overflow-wrap: break-word;
    vertical-align: top;
}
</style>
