<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import NavbarContrato from '@/Pages/Sgc/Contratada/NavbarContrato.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import { computed, onMounted, ref, watch } from 'vue';
import * as XLSX from 'xlsx';

const props = defineProps({
    campanha: { type: Object, default: () => ({}) },
    campanha_id: [Number, String],
    contrato: [Number, String],
    produto: { type: String, default: 'malarigeno' },
    contratos: { type: Object, default: () => ({ contratada: 'Contratada', tipo_contrato: null }) },
    canApprove: { type: Boolean, default: false },
});

const previewFoto = ref(null);
const planilhaColumns = ref([]);
const planilhaRows = ref([]);
const planilhaLoading = ref(false);
const planilhaError = ref(null);
const currentPage = ref(1);
const rowsPerPage = 10;

const statusClass = computed(() => ({
    'bg-warning text-dark': props.campanha.status === 'Em análise',
    'bg-success': props.campanha.status === 'Aprovada',
    'bg-danger': props.campanha.status === 'Rejeitada',
    'bg-secondary': props.campanha.status === 'Em elaboração',
}));

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
                    { route: '#', label: `Visualizar Campanha ${props.campanha.id || props.campanha_id}` },
                ]"
            />
        </template>

        <NavbarContrato :tipo="{ id: props.contrato }">
            <template #body>
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
                            <div>
                                <h2 class="mb-1">VISUALIZAR CAMPANHA {{ (props.produto || '').toUpperCase() }}</h2>
                                <div class="text-muted">{{ props.campanha.subproduto || 'Subproduto não informado' }}</div>
                            </div>
                            <span class="badge fs-6" :class="statusClass">{{ props.campanha.status || 'N/A' }}</span>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-3">
                                <div class="info-box">
                                    <span>Campanha</span>
                                    <strong>{{ props.campanha.id || 'N/A' }}</strong>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="info-box">
                                    <span>Planilha Modelo</span>
                                    <strong>{{ props.campanha.modulo?.nome || 'N/A' }}</strong>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="info-box">
                                    <span>Criada em</span>
                                    <strong>{{ props.campanha.created_at || 'N/A' }}</strong>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="info-box">
                                    <span>Atualizada em</span>
                                    <strong>{{ props.campanha.updated_at || 'N/A' }}</strong>
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
}

.sheet-table-wrapper th,
.sheet-table-wrapper td {
    max-width: 280px;
    overflow-wrap: anywhere;
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
</style>
