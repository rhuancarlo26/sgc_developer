<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import NavbarContrato from '@/Pages/Sgc/Contratada/NavbarContrato.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import InputError from '@/Components/InputError.vue';
import NavButton from '@/Components/NavButton.vue';
import { ref, computed } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import DadosGeraisVisualizar from './Componentes/DadosGeraisVisualizar.vue';
import ModulosAmostraisVisualizar from './Componentes/ModulosAmostraisVisualizar.vue';
import QueloniosCrocodilianosVisualizar from './Componentes/QueloniosCrocodilianosVisualizar.vue';
import FaunaCavernicolaVisualizar from './Componentes/FaunaCavernicolaVisualizar.vue';
import MetodologiaVisualizar from './Componentes/MetodologiaVisualizar.vue';
import FaunaResultadosVisualizar from './Componentes/FaunaResultadosVisualizar.vue';
import MapLayer from '../Espeleologia/MapViewer.vue';

const props = defineProps({
    campanha: {
        type: Object,
        default: () => ({}),
    },
    contrato: [Number, String],
    produto: String,
    contratos: Object,
    canApprove: Boolean,
    coordenadas: [Object, String, null],
    campanha_id: [Number, String, null],
});

const isAtropelamento = computed(() =>
    props.campanha?.familia?.toLowerCase().includes('atropelamento') ?? false
);

const totalSubSteps = computed(() => isAtropelamento.value ? 3 : 5);

const activeTab = ref('apresentacao');
const subStep = ref(1);
const formAprovacao = useForm({
    status: '',
    observacoes: '',
});

const setActiveTab = (tab) => {
    activeTab.value = tab;
    if (tab === 'apresentacao') {
        subStep.value = isAtropelamento.value ? 3 : 5;
    }
};

const prevSubStep = () => {
    if (subStep.value > 1) subStep.value -= 1;
};

const nextSubStepOrTab = () => {
    if (subStep.value < totalSubSteps.value) {
        subStep.value += 1;
    } else {
        setActiveTab('metodologia');
    }
};

const aprovarCampanha = () => {
    formAprovacao.status = 'Aprovada';
    formAprovacao.observacoes = '';
    salvarAprovacao();
};

const rejeitarCampanha = () => {
    if (!formAprovacao.observacoes.trim()) {
        formAprovacao.errors.observacoes = 'Observações são obrigatórias para rejeição.';
        return;
    }
    formAprovacao.status = 'Rejeitada';
    salvarAprovacao();
};

const salvarAprovacao = () => {
    formAprovacao.post(route('sgc.contratada.produtos.approve', [props.campanha.id]), {
        onSuccess: () => {
            formAprovacao.reset();
            alert('Campanha atualizada com sucesso!');
            router.get(route('sgc.contratada.produtos.index', [props.contrato, props.produto.toLowerCase()]));
        },
        onError: (errors) => {
            console.error('Erro ao salvar aprovação:', errors);
            alert('Erro ao salvar aprovação: ' + (errors.error || 'Verifique os dados e tente novamente.'));
        },
    });
};

const anexoLabels = {
    anuencia_proprietarios:  'Anuência dos Proprietários',
    registro_fotografico:    'Registro Fotográfico',
    dados_secundarios:       'Dados Secundários',
    art:                     'ART',
    ret:                     'RET',
    cr:                      'CR',
    ctf:                     'CTF',
    anuencia_colecoes:       'Anuência de Coleções',
    oficio_atividades_campo: 'Ofício de Atividades de Campo',
    rfaef:                   'Formulário de Registro de Atropelamentos (RFAEF)',
    cartas_anuencia:         'Cartas de Anuência',
};

const formatAnexoLabel = (tipo) =>
    anexoLabels[tipo] ?? tipo.replace(/_/g, ' ').split(' ')
        .map(w => w.charAt(0).toUpperCase() + w.slice(1))
        .join(' ');

const isImage = (url) => /\.(jpe?g|png|gif|webp)(\?.*)?$/i.test(url ?? '');
const isPdf  = (url) => /\.pdf(\?.*)?$/i.test(url ?? '');

// Modal de preview de imagem
const previewUrl = ref(null);
const openPreview = (url) => { previewUrl.value = url; };
const closePreview = () => { previewUrl.value = null; };

const formatStatus = (status) => status === 'Rejeitada' ? 'Reprovada' : status;
</script>

<template>
    <AuthenticatedLayout>
        <template #header>
            <Breadcrumb
                :links="[
                    { route: route('sgc.gestao.listagem', contratos.tipo_contrato), label: 'Gestão de Contratos' },
                    { route: route('sgc.contratada.produtos.index', [contrato, produto.toLowerCase()]), label: contratos.contratada },
                    { route: '#', label: `Visualizar Campanha ${campanha.id_campanha || campanha.id}` },
                ]"
            />
        </template>
        <NavbarContrato :tipo="{ id: contrato }">
            <template #body>
                <div class="card">
                    <div class="card-body">
                        <h2 class="text-center mb-1">VISUALIZAR CAMPANHA {{ produto.toUpperCase() }}</h2>
                        <h5 class="text-center mb-3 text-muted">{{ campanha.familia }}</h5>
                        <h4 class="mb-3">Status: <span class="badge" :class="{
                            'bg-warning text-dark': campanha.status === 'Em análise',
                            'bg-success': campanha.status === 'Aprovada',
                            'bg-danger': campanha.status === 'Rejeitada',
                            'bg-secondary': campanha.status === 'Em elaboração',
                        }">{{ formatStatus(campanha.status) }}</span></h4>

                        <ul class="nav nav-tabs mb-4">
                            <li class="nav-item">
                                <a class="nav-link" :class="{ active: activeTab === 'apresentacao' }"
                                    @click.prevent="setActiveTab('apresentacao')">Apresentação</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" :class="{ active: activeTab === 'metodologia' }"
                                    @click.prevent="setActiveTab('metodologia')">Metodologia</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" :class="{ active: activeTab === 'resultados' }"
                                    @click.prevent="setActiveTab('resultados')">Resultados</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" :class="{ active: activeTab === 'anexos' }"
                                    @click.prevent="setActiveTab('anexos')">Anexos</a>
                            </li>
                        </ul>

                        <div class="tab-content">

                            <!-- ===================== APRESENTAÇÃO ===================== -->
                            <div v-if="activeTab === 'apresentacao'" class="tab-pane fade show active">

                                <!-- SubStep 1: Mapa + info básica -->
                                <div v-if="subStep === 1">
                                    <h4 class="text-center mb-3 fw-bold">APRESENTAÇÃO</h4>
                                    <div class="row">
                                        <div class="col-md-8">
                                            <MapLayer
                                                :emp_coordenadas="coordenadas"
                                                :campanhaId="isAtropelamento ? null : campanha_id"
                                                :somenteTrecho="isAtropelamento"
                                            />
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label">Empreendimento</label>
                                                <input type="text" class="form-control" :value="campanha.cod_emp" disabled />
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Subproduto</label>
                                                <input type="text" class="form-control" :value="campanha.familia || 'Fauna'" disabled />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-end mt-3">
                                        <button class="btn btn-primary" @click="subStep = 2">Avançar</button>
                                    </div>
                                    <p class="text-center mt-3 text-muted fw-bold">{{ subStep }}/{{ totalSubSteps }}</p>
                                </div>
                                <DadosGeraisVisualizar
                                    v-if="subStep === 2"
                                    :campanha="campanha"
                                    :abio-records="campanha.abios || []"
                                    :profissional-records="campanha.profissionais || []"
                                    :sub-step="subStep"
                                    @next="subStep = 3"
                                    @prev="prevSubStep"
                                />

                                <!-- SubStep 3 — Atropelamento: tabela de campanhas -->
                                <div v-if="subStep === 3 && isAtropelamento" class="mt-2">
                                    <h5 class="text-center mb-3 fw-bold">CAMPANHAS DE ATROPELAMENTO</h5>
                                    <div v-if="campanha.atropelamento_campanhas && campanha.atropelamento_campanhas.length > 0" class="table-responsive">
                                        <table class="table table-bordered table-hover table-sm">
                                            <thead class="table-dark">
                                                <tr>
                                                    <th>Rodovia</th>
                                                    <th>Data Inicial</th>
                                                    <th>Data Final</th>
                                                    <th>UF Ini</th>
                                                    <th>UF Fim</th>
                                                    <th>KM Inicial</th>
                                                    <th>KM Final</th>
                                                    <th>Observações</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="ac in campanha.atropelamento_campanhas" :key="ac.id">
                                                    <td>{{ ac.rodovia || '—' }}</td>
                                                    <td>{{ ac.data_inicial || '—' }}</td>
                                                    <td>{{ ac.data_final || '—' }}</td>
                                                    <td>{{ ac.uf_inicial || '—' }}</td>
                                                    <td>{{ ac.uf_final || '—' }}</td>
                                                    <td>{{ ac.km_inicial ?? '—' }}</td>
                                                    <td>{{ ac.km_final ?? '—' }}</td>
                                                    <td>{{ ac.obs || '—' }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div v-else class="alert alert-info text-center">Nenhuma campanha de atropelamento registrada.</div>
                                    <div class="d-flex justify-content-between mt-3">
                                        <button class="btn btn-outline-secondary" @click="prevSubStep">Voltar</button>
                                        <button class="btn btn-primary" @click="setActiveTab('metodologia')">Avançar</button>
                                    </div>
                                    <p class="text-center mt-3 text-muted fw-bold">{{ subStep }}/{{ totalSubSteps }}</p>
                                </div>

                                <!-- SubStep 3-5 — Fauna regular -->
                                <ModulosAmostraisVisualizar
                                    v-if="subStep === 3 && !isAtropelamento"
                                    :form-modulo-amostral="campanha.modulos_amostrais || []"
                                    :modulo-records="campanha.modulos_amostrais || []"
                                    :sub-step="subStep"
                                    @next="subStep = 4"
                                    @prev="prevSubStep"
                                />
                                <QueloniosCrocodilianosVisualizar
                                    v-if="subStep === 4 && !isAtropelamento"
                                    :formPontosAmostragem="campanha.pontos_quelo_crocod || []"
                                    :naoSeAplica="campanha.nao_se_aplica"
                                    :subStep="subStep"
                                    @next="subStep = 5"
                                    @prev="prevSubStep"
                                >
                                    <template #footer>
                                        <p class="text-center mt-3 text-muted fw-bold">{{ subStep }}/5</p>
                                    </template>
                                </QueloniosCrocodilianosVisualizar>
                                <FaunaCavernicolaVisualizar
                                    v-if="subStep === 5 && !isAtropelamento"
                                    :formPontosCavernicola="campanha.formPontosCavernicola || []"
                                    :naoSeAplica="campanha.nao_se_aplica"
                                    :subStep="subStep"
                                    @next="setActiveTab('metodologia')"
                                    @prev="prevSubStep"
                                >
                                    <template #footer>
                                        <p class="text-center mt-3 text-muted fw-bold">{{ subStep }}/5</p>
                                    </template>
                                </FaunaCavernicolaVisualizar>
                            </div>

                            <!-- ===================== METODOLOGIA ===================== -->
                            <div v-if="activeTab === 'metodologia'" class="tab-pane fade show active">
                                <MetodologiaVisualizar
                                    :formMetodologia="campanha.metodologias || []"
                                    @prev="setActiveTab('apresentacao')"
                                    @next="setActiveTab('resultados')"
                                />
                            </div>

                            <!-- ===================== RESULTADOS ===================== -->
                            <div v-if="activeTab === 'resultados'" class="tab-pane fade show active">
                                <!-- Fauna regular -->
                                <FaunaResultadosVisualizar
                                    v-if="!isAtropelamento"
                                    :resultadosTerrestre="campanha.resultadosTerrestre || []"
                                    :resultadosAquatica="campanha.resultadosAquatica || []"
                                    :resultadosCavernicola="campanha.resultadosCavernicola || []"
                                    :consideracoes="campanha.consideracoes"
                                    @prev="setActiveTab('metodologia')"
                                    @next="setActiveTab('anexos')"
                                />

                                <!-- Atropelamento: planilha + considerações -->
                                <div v-else>
                                    <h4 class="text-center mb-4 fw-bold">Resultados — Atropelamento de Fauna</h4>
                                    <div class="card p-4 mb-4">
                                        <div class="mb-4">
                                            <h6 class="fw-semibold mb-2">Planilha de Resultados</h6>
                                            <div v-if="campanha.planilha_atropelamento">
                                                <a :href="campanha.planilha_atropelamento" class="btn btn-outline-primary" download>
                                                    ⬇ Baixar Planilha Enviada
                                                </a>
                                            </div>
                                            <div v-else class="text-muted fst-italic">Nenhuma planilha enviada.</div>
                                        </div>
                                        <div>
                                            <h6 class="fw-semibold mb-2">Considerações</h6>
                                            <div v-if="campanha.consideracoes_atropelamento" class="form-control bg-light" style="min-height:80px; white-space:pre-wrap;">
                                                {{ campanha.consideracoes_atropelamento }}
                                            </div>
                                            <div v-else class="text-muted fst-italic">Sem considerações registradas.</div>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between mt-2">
                                        <button class="btn btn-outline-secondary px-4" @click="setActiveTab('metodologia')">Voltar</button>
                                        <button class="btn btn-primary px-4" @click="setActiveTab('anexos')">Avançar</button>
                                    </div>
                                </div>
                            </div>

                            <!-- ===================== ANEXOS ===================== -->
                            <div v-if="activeTab === 'anexos'" class="tab-pane fade show active">
                                <h4 class="text-center mb-4 fw-bold">ANEXOS</h4>

                                <div v-if="campanha.anexos && campanha.anexos.length > 0">
                                    <div class="row g-3 mb-4">
                                        <div
                                            v-for="anexo in campanha.anexos"
                                            :key="anexo.id"
                                            class="col-6 col-sm-4 col-md-3"
                                        >
                                            <div class="anexo-card h-100 d-flex flex-column">
                                                <!-- Miniatura de imagem -->
                                                <div
                                                    v-if="isImage(anexo.caminho)"
                                                    class="anexo-thumb-wrap"
                                                    @click="openPreview(anexo.caminho)"
                                                    title="Clique para ampliar"
                                                >
                                                    <img :src="anexo.caminho" :alt="anexo.nome_arquivo" class="anexo-thumb" />
                                                    <div class="anexo-thumb-overlay">🔍</div>
                                                </div>

                                                <!-- Ícone PDF -->
                                                <div v-else-if="isPdf(anexo.caminho)" class="anexo-thumb-wrap pdf-icon-wrap">
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" width="64" height="64">
                                                        <path fill="#F44336" d="M38 44H10c-2.2 0-4-1.8-4-4V8c0-2.2 1.8-4 4-4h20l12 12v24c0 2.2-1.8 4-4 4z"/>
                                                        <path fill="#FF8A80" d="M30 4l12 12H30z"/>
                                                        <text x="14" y="33" fill="#fff" font-size="10" font-family="sans-serif" font-weight="bold">PDF</text>
                                                    </svg>
                                                </div>

                                                <!-- Ícone genérico (xlsx etc.) -->
                                                <div v-else class="anexo-thumb-wrap file-icon-wrap">
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" width="64" height="64">
                                                        <path fill="#4CAF50" d="M38 44H10c-2.2 0-4-1.8-4-4V8c0-2.2 1.8-4 4-4h20l12 12v24c0 2.2-1.8 4-4 4z"/>
                                                        <path fill="#A5D6A7" d="M30 4l12 12H30z"/>
                                                        <text x="10" y="33" fill="#fff" font-size="9" font-family="sans-serif" font-weight="bold">FILE</text>
                                                    </svg>
                                                </div>

                                                <!-- Info + ação -->
                                                <div class="p-2 flex-grow-1 d-flex flex-column justify-content-between">
                                                    <div>
                                                        <div class="fw-semibold small" style="word-break:break-word;">
                                                            {{ formatAnexoLabel(anexo.tipo_anexo) }}
                                                        </div>
                                                        <div class="text-muted" style="font-size:0.7rem; word-break:break-all;">
                                                            {{ anexo.nome_arquivo }}
                                                        </div>
                                                    </div>
                                                    <a
                                                        v-if="anexo.caminho"
                                                        :href="anexo.caminho"
                                                        target="_blank"
                                                        class="btn btn-sm btn-outline-primary mt-2"
                                                    >
                                                        Visualizar
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div v-else class="alert alert-info text-center">
                                    Nenhum anexo disponível.
                                </div>

                                <!-- Aprovação (fiscal) -->
                                <div v-if="canApprove && campanha.status === 'Em análise'" class="mt-4 border-top pt-4">
                                    <h4>APROVAÇÃO</h4>
                                    <form @submit.prevent="rejeitarCampanha">
                                        <div class="mb-3">
                                            <label class="form-label">Observações (obrigatório para rejeição)</label>
                                            <textarea
                                                v-model="formAprovacao.observacoes"
                                                class="form-control"
                                                rows="4"
                                                placeholder="Digite observações (obrigatório para rejeição)"
                                            ></textarea>
                                            <InputError :message="formAprovacao.errors.observacoes" />
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <NavButton type="button" type-button="success" title="Aprovar" @click="aprovarCampanha" />
                                            <NavButton type="submit" type-button="danger" title="Rejeitar" />
                                        </div>
                                    </form>
                                </div>

                                <div class="d-flex justify-content-between mt-4">
                                    <NavButton type="button" type-button="secondary" title="Voltar" @click="setActiveTab('resultados')" />
                                    <NavButton
                                        type="button"
                                        type-button="primary"
                                        title="Voltar à Lista"
                                        @click="router.get(route('sgc.contratada.produtos.index', [contrato, produto.toLowerCase()]))"
                                    />
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- Modal de preview de imagem -->
                <div v-if="previewUrl" class="preview-backdrop" @click="closePreview">
                    <div class="preview-box" @click.stop>
                        <button class="btn-close preview-close" @click="closePreview"></button>
                        <img :src="previewUrl" class="preview-img" />
                    </div>
                </div>

            </template>
        </NavbarContrato>
    </AuthenticatedLayout>
</template>

<style scoped>
.card { border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,.1); }
.nav-tabs .nav-link { color: #6c757d; font-weight: 500; }
.nav-tabs .nav-link.active { color: #007bff; border-bottom: 2px solid #007bff; }
.tab-content { padding: 20px; }

/* Anexos grid */
.anexo-card {
    border: 1px solid #dee2e6;
    border-radius: 8px;
    overflow: hidden;
    background: #fff;
    transition: box-shadow .2s;
}
.anexo-card:hover { box-shadow: 0 4px 12px rgba(0,0,0,.12); }

.anexo-thumb-wrap {
    position: relative;
    width: 100%;
    height: 130px;
    background: #f8f9fa;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    overflow: hidden;
}
.anexo-thumb {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform .2s;
}
.anexo-thumb-wrap:hover .anexo-thumb { transform: scale(1.05); }

.anexo-thumb-overlay {
    position: absolute;
    inset: 0;
    background: rgba(0,0,0,.3);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    opacity: 0;
    transition: opacity .2s;
}
.anexo-thumb-wrap:hover .anexo-thumb-overlay { opacity: 1; }

.pdf-icon-wrap, .file-icon-wrap { cursor: default; }

/* Modal preview */
.preview-backdrop {
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,.7);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 99999;
}
.preview-box {
    position: relative;
    background: #fff;
    border-radius: 8px;
    padding: 12px;
    max-width: 90vw;
    max-height: 90vh;
}
.preview-img {
    max-width: 80vw;
    max-height: 80vh;
    object-fit: contain;
    display: block;
}
.preview-close {
    position: absolute;
    top: 8px;
    right: 8px;
    z-index: 1;
}
</style>
