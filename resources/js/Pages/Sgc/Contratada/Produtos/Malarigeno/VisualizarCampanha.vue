<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import NavbarContrato from '@/Pages/Sgc/Contratada/NavbarContrato.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import { computed, onMounted, ref, watch } from 'vue';
import * as XLSX from 'xlsx';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    campanha: { type: Object, default: () => ({}) },
    campanha_id: [Number, String],
    contrato: [Number, String],
    produto: { type: String, default: 'malarigeno' },
    contratos: { type: Object, default: () => ({ contratada: 'Contratada', tipo_contrato: null }) },
    canApprove: { type: Boolean, default: false },
});

const previewFoto = ref(null);
const showAnalysisModal = ref(false);
const planilhaColumns = ref([]);
const planilhaRows = ref([]);
const planilhaLoading = ref(false);
const planilhaError = ref(null);
const currentPage = ref(1);
const rowsPerPage = 10;

const statusClass = computed(() => ({
    'bg-warning text-white': props.campanha.status === 'Em análise',
    'bg-success text-white': props.campanha.status === 'Aprovada',
    'bg-danger text-white': props.campanha.status === 'Rejeitada',
    'bg-secondary text-white': props.campanha.status === 'Em elaboração',
}));

const statusDisplay = computed(() => {
    if (props.campanha.status === 'Rejeitada') {
        return 'Em revisão';
    }
    return props.campanha.status;
});

const canEdit = computed(() => {
    return ['Rejeitada', 'Em elaboração'].includes(props.campanha.status);
});

const fotos = computed(() => Array.isArray(props.campanha.fotos) ? props.campanha.fotos : []);
const anexos = computed(() => Array.isArray(props.campanha.anexos) ? props.campanha.anexos : []);
const totalPages = computed(() => Math.max(1, Math.ceil(planilhaRows.value.length / rowsPerPage)));
const paginatedRows = computed(() => {
    const start = (currentPage.value - 1) * rowsPerPage;
    return planilhaRows.value.slice(start, start + rowsPerPage);
});
const planilhaRangeLabel = computed(() => {
    if (!planilhaRows.value.length) {
        return '0 de 0';
    }

    const start = (currentPage.value - 1) * rowsPerPage + 1;
    const end = Math.min(currentPage.value * rowsPerPage, planilhaRows.value.length);
    return `${start}-${end} de ${planilhaRows.value.length}`;
});

const loadPlanilha = async () => {
    if (!props.campanha.planilha_url) {
        planilhaColumns.value = [];
        planilhaRows.value = [];
        return;
    }

    planilhaLoading.value = true;
    planilhaError.value = null;
    currentPage.value = 1;

    try {
        const response = await fetch(props.campanha.planilha_url);

        if (!response.ok) {
            throw new Error('Nao foi possivel baixar a planilha.');
        }

        const buffer = await response.arrayBuffer();
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
    if (currentPage.value < totalPages.value) {
        currentPage.value += 1;
    }
};

const openPreview = (foto) => {
    previewFoto.value = foto;
};

const closePreview = () => {
    previewFoto.value = null;
};

watch(() => props.campanha.planilha_url, loadPlanilha);

onMounted(loadPlanilha);
</script>

<template>
    <AuthenticatedLayout>
        <template #header>
            <Breadcrumb
                :links="[
                    { route: route('sgc.gestao.listagem', props.contratos.tipo_contrato), label: 'Gestão de Contratos' },
                    { route: route('sgc.contratada.produtos.index', [props.contrato, props.produto]), label: props.contratos.contratada },
                    { route: '#', label: `Visualizar Campanha ${props.campanha.id_campanha || props.campanha.id || props.campanha_id}` },
                ]"
            />
        </template>

        <NavbarContrato :tipo="{ id: props.contrato }">
            <template #body>
                <div class="card">
                    <div class="card-body">
                        <div v-if="props.campanha.analises && props.campanha.analises.length > 0" class="alert alert-info mb-3 d-flex justify-content-between align-items-center" style="cursor: pointer;" @click="showAnalysisModal = true">
                            <span>
                                <i class="bi bi-info-circle me-2"></i>
                                {{ props.campanha.analises.length }} análise{{ props.campanha.analises.length !== 1 ? 's' : '' }} registrada{{ props.campanha.analises.length !== 1 ? 's' : '' }}
                            </span>
                            <span class="badge bg-info text-white">Clique para visualizar</span>
                        </div>

                        <div class="text-center mb-4">
                            <h2 class="mb-2">VISUALIZAR CAMPANHA MALARÍGENO</h2>
                            <p class="text-muted mb-3 fs-5">{{ props.campanha.subproduto || 'Subproduto não informado' }}</p>
                            <div class="d-inline-block">
                                <span class="badge fs-4 px-3 py-2 fw-bold text-white" :class="statusClass">{{ statusDisplay || 'N/A' }}</span>
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-2">
                                <div class="info-box">
                                    <span>Campanha</span>
                                    <strong>{{ props.campanha.id_campanha || 'N/A' }}</strong>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="info-box">
                                    <span>Empreendimento</span>
                                    <strong>{{ props.campanha.cod_emp || 'N/A' }}</strong>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="info-box">
                                    <span>SEI DNIT</span>
                                    <strong>{{ props.campanha.sei_dnit || 'N/A' }}</strong>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="info-box">
                                    <span>Planilha Modelo</span>
                                    <strong>{{ props.campanha.modulo?.nome || 'N/A' }}</strong>
                                </div>
                            </div>
                        </div>

                        <section class="mb-4">
                            <h4 class="section-title">Planilha Enviada</h4>
                            <div v-if="props.campanha.planilha_url" class="file-row">
                                <div>
                                    <strong>{{ props.campanha.planilha_nome || 'Planilha da campanha' }}</strong>
                                    <div class="text-muted small">{{ props.campanha.planilha_caminho }}</div>
                                </div>
                                <a class="btn btn-primary" :href="props.campanha.planilha_url" target="_blank" rel="noopener">
                                    Abrir
                                </a>
                            </div>
                            <div v-else class="empty-state">Nenhuma planilha enviada.</div>

                            <div v-if="props.campanha.planilha_url" class="sheet-preview mt-3">
                                <div v-if="planilhaLoading" class="empty-state">Carregando dados da planilha...</div>
                                <div v-else-if="planilhaError" class="empty-state text-danger">{{ planilhaError }}</div>
                                <div v-else-if="planilhaColumns.length && planilhaRows.length">
                                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
                                        <span class="text-muted small">Linhas {{ planilhaRangeLabel }}</span>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-outline-secondary btn-sm" :disabled="currentPage === 1" @click="previousPage">
                                                Anterior
                                            </button>
                                            <button type="button" class="btn btn-outline-secondary btn-sm" disabled>
                                                {{ currentPage }} / {{ totalPages }}
                                            </button>
                                            <button type="button" class="btn btn-outline-secondary btn-sm" :disabled="currentPage === totalPages" @click="nextPage">
                                                Proxima
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
                                                <tr v-for="(row, rowIndex) in paginatedRows" :key="rowIndex">
                                                    <td v-for="(cell, cellIndex) in row" :key="cellIndex">
                                                        {{ cell || '-' }}
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div v-else class="empty-state">A planilha nao possui linhas para exibir.</div>
                            </div>
                        </section>

                        <section class="mb-4">
                            <h4 class="section-title">Fotos</h4>
                            <div v-if="fotos.length" class="photo-grid">
                                <button
                                    v-for="foto in fotos"
                                    :key="foto.id"
                                    type="button"
                                    class="photo-card"
                                    @click="openPreview(foto)"
                                >
                                    <img v-if="foto.url" :src="foto.url" :alt="foto.nome_arquivo || 'Foto da campanha'" />
                                    <div v-else class="photo-placeholder">Sem imagem</div>
                                    <div class="photo-meta">
                                        <strong>{{ foto.nome_arquivo || 'Foto' }}</strong>
                                        <span v-if="foto.data_captura">{{ foto.data_captura }}</span>
                                        <span v-if="foto.latitude && foto.longitude">{{ foto.latitude }}, {{ foto.longitude }}</span>
                                        <span v-if="foto.descricao">{{ foto.descricao }}</span>
                                    </div>
                                </button>
                            </div>
                            <div v-else class="empty-state">Nenhuma foto vinculada.</div>
                        </section>

                        <section>
                            <h4 class="section-title">Anexos</h4>
                            <div v-if="anexos.length" class="list-group">
                                <a
                                    v-for="anexo in anexos"
                                    :key="anexo.id"
                                    class="list-group-item list-group-item-action d-flex justify-content-between align-items-center"
                                    :href="anexo.url"
                                    target="_blank"
                                    rel="noopener"
                                >
                                    <span>{{ anexo.nome_arquivo || 'Anexo' }}</span>
                                    <span class="text-primary">Abrir</span>
                                </a>
                            </div>
                            <div v-else class="empty-state">Nenhum anexo vinculado.</div>
                        </section>

                        <!-- Seção de Edição para Contratadas -->
                        <section v-if="canEdit" class="mt-4 pt-4 border-top">
                            <h4 class="section-title">Editar Campanha</h4>
                            <p class="text-muted mb-3">Esta campanha está em revisão. Você pode editar os dados e resubmeter para análise.</p>
                            <Link
                                :href="route('sgc.contratada.produtos.malarigeno.edit', [props.contrato, props.produto, props.campanha.id])"
                                class="btn btn-primary"
                            >
                                <i class="bi bi-pencil"></i> Editar Campanha
                            </Link>
                        </section>

                        <!-- Seção de Análise para Fiscais -->
                        <section v-if="props.canApprove" class="mt-4 pt-4 border-top">
                            <h4 class="section-title">Análise da Campanha</h4>
                            <p class="text-muted mb-3">Como fiscal, você pode analisar e aprovar ou reprovar esta campanha.</p>
                            <Link
                                :href="route('sgc.contratada.produtos.malarigeno.analise', [props.contrato, props.produto, props.campanha.id])"
                                class="btn btn-primary"
                            >
                                <i class="bi bi-check2-square"></i> Ir para Análise
                            </Link>
                        </section>
                    </div>
                </div>

                <!-- Modal de Análises -->
                <div v-if="showAnalysisModal && props.campanha.analises && props.campanha.analises.length > 0" class="modal-backdrop-custom" @click.self="showAnalysisModal = false">
                    <div class="preview-modal" style="max-width: 600px;">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">Histórico de Análises</h5>
                            <button type="button" class="btn-close" @click="showAnalysisModal = false"></button>
                        </div>
                        <div class="modal-analysis-content">
                            <div v-for="analise in props.campanha.analises" :key="analise.id" class="card mb-2">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <strong>Versão {{ analise.versao }}</strong>
                                            <!-- <span :class="['badge', analise.status === 'Aprovada' ? 'bg-success' : 'bg-danger']">
                                                {{ analise.status }}
                                            </span> -->
                                        </div>
                                        <small class="text-muted">{{ analise.created_at }}</small>
                                    </div>
                                    <p class="mb-2 small">
                                        <strong>Fiscal:</strong> {{ analise.fiscal?.name || 'N/A' }}
                                    </p>
                                    <div v-if="analise.observacoes" class="alert alert-info small mb-0">
                                        <strong>Observações:</strong>
                                        <p class="mb-0 mt-1">{{ analise.observacoes }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-if="previewFoto" class="modal-backdrop-custom" @click.self="closePreview">
                    <div class="preview-modal">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">{{ previewFoto.nome_arquivo || 'Foto da campanha' }}</h5>
                            <button type="button" class="btn-close" @click="closePreview"></button>
                        </div>
                        <img :src="previewFoto.url" :alt="previewFoto.nome_arquivo || 'Foto da campanha'" />
                        <p v-if="previewFoto.descricao" class="mt-3 mb-0">{{ previewFoto.descricao }}</p>
                    </div>
                </div>
            </template>
        </NavbarContrato>
    </AuthenticatedLayout>
</template>

<style scoped>
.info-box {
    border: 1px solid #e2e4e6;
    border-radius: 6px;
    padding: 12px;
    min-height: 82px;
}

.info-box span {
    display: block;
    color: #6c757d;
    font-size: 0.85rem;
    margin-bottom: 6px;
}

.info-box strong {
    display: block;
    overflow-wrap: anywhere;
}

.section-title {
    font-size: 1.05rem;
    font-weight: 700;
    margin-bottom: 12px;
}

.file-row {
    border: 1px solid #e2e4e6;
    border-radius: 6px;
    padding: 12px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
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

.photo-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    gap: 14px;
}

.photo-card {
    background: #fff;
    border: 1px solid #e2e4e6;
    border-radius: 6px;
    padding: 0;
    text-align: left;
    overflow: hidden;
}

.photo-card img,
.photo-placeholder {
    width: 100%;
    aspect-ratio: 4 / 3;
    object-fit: cover;
    background: #f1f3f5;
}

.photo-placeholder {
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6c757d;
}

.photo-meta {
    display: flex;
    flex-direction: column;
    gap: 4px;
    padding: 10px;
    font-size: 0.9rem;
}

.photo-meta span {
    color: #6c757d;
}

.modal-backdrop-custom {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.55);
    z-index: 1050;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
}

.preview-modal {
    background: #fff;
    border-radius: 6px;
    max-width: 900px;
    width: 100%;
    padding: 18px;
}

.preview-modal img {
    width: 100%;
    max-height: 70vh;
    object-fit: contain;
    background: #f8f9fa;
}

.modal-analysis-content {
    max-height: 60vh;
    overflow-y: auto;
}
</style>
