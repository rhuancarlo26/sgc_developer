<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import LinkConfirmation from "@/Components/LinkConfirmation.vue";
import InputError from "@/Components/InputError.vue";
import BadgeSituacao from "./Partials/BadgeSituacao.vue";
import ModalCondicionante from "./Partials/ModalCondicionante.vue";
import ModalPrazo from "./Partials/ModalPrazo.vue";
import { descreverRegra, formatarData, hoje, textoDias } from "./Partials/utils.js";
import { Head, Link, router, useForm } from "@inertiajs/vue3";
import { computed, nextTick, ref } from "vue";
import { IconArchive, IconDots, IconDownload, IconPencil, IconPlus, IconRefresh, IconTrash } from "@tabler/icons-vue";

const props = defineProps({
    licenca: Object,
    prazos: Array,
    tiposPrazo: Array,
    situacoesCondicionante: Array,
    tiposRegistro: Array,
});

const aba = ref('prazos');
const somentePendentes = ref(false);

const modalCondicionante = ref();
const modalPrazo = ref();
const prazoAbertoId = ref(null);

// Após cada recarga o prazo aberto é lido de novo das props, mantendo o modal atualizado
const prazoAberto = computed(() => props.prazos.find(p => p.id === prazoAbertoId.value) ?? null);

const abrirPrazo = async (prazo) => {
    prazoAbertoId.value = prazo.id;
    await nextTick();
    modalPrazo.value.abrir();
};

const prazosVisiveis = computed(() => somentePendentes.value
    ? props.prazos.filter(p => p.status === 'pendente')
    : props.prazos);

const situacaoCondicionante = (valor) => props.situacoesCondicionante.find(s => s.value === valor)?.label ?? valor;
const corCondicionante = { atendida: 'success', parcialmente_atendida: 'lime', em_atendimento: 'azure', nao_atendida: 'danger', informativa: 'secondary' };

const condicionantesEvento = computed(() => props.licenca.condicionantes.filter(c => c.tipo_prazo === 'apos_evento'));
const formEvento = useForm({ condicionante_id: '', data_evento: hoje() });
const lancarEvento = () => formEvento.post(route('sgc.licencas.prazos.evento', props.licenca.id), {
    preserveScroll: true,
    onSuccess: () => formEvento.reset('condicionante_id'),
});

const excluirCondicionante = (condicionante, confirmar) => confirmar(() =>
    router.delete(route('sgc.licencas.condicionantes.destroy', condicionante.id), { preserveScroll: true })
);

const excluirLicenca = (confirmar) => confirmar(() => router.delete(route('sgc.licencas.destroy', props.licenca.id)));

const ativa = computed(() => props.licenca.status === 'ativa');
</script>

<template>
    <Head :title="licenca.rotulo" />

    <AuthenticatedLayout>
        <template #header>
            <div class="w-100 d-flex flex-wrap gap-2 justify-content-between">
                <Breadcrumb class="align-self-center" :links="[
                    { route: route('sgc.licencas.painel'), label: 'Gestão de Prazos' },
                    { route: route('sgc.licencas.index'), label: 'Licenças e autorizações' },
                    { route: '#', label: licenca.rotulo },
                ]" />
                <div class="d-flex gap-2">
                    <Link class="btn btn-info" :href="route('sgc.licencas.edit', licenca.id)">
                        <IconPencil class="me-1" /> Editar
                    </Link>
                    <LinkConfirmation v-slot="confirmacao" :options="{ text: 'A licença, suas condicionantes e prazos serão excluídos.', confirmButtonClass: 'btn-danger', confirmButtonText: 'Excluir' }">
                        <button type="button" class="btn btn-icon btn-info dropdown-toggle p-2" data-bs-toggle="dropdown" aria-expanded="false">
                            <IconDots />
                        </button>
                        <div class="dropdown-menu dropdown-menu-end">
                            <template v-if="ativa">
                                <Link class="dropdown-item" :href="route('sgc.licencas.create', { anterior: licenca.id, motivo: 'retificacao' })">
                                    <IconRefresh class="me-2" size="18" /> Registrar retificação
                                </Link>
                                <Link class="dropdown-item" :href="route('sgc.licencas.create', { anterior: licenca.id, motivo: 'renovacao' })">
                                    <IconRefresh class="me-2" size="18" /> Registrar renovação
                                </Link>
                            </template>
                            <Link v-if="licenca.status !== 'substituida'" class="dropdown-item" as="button" method="patch"
                                :href="route('sgc.licencas.arquivar', licenca.id)" preserve-scroll>
                                <IconArchive class="me-2" size="18" /> {{ licenca.status === 'arquivada' ? 'Reativar' : 'Arquivar' }}
                            </Link>
                            <a class="dropdown-item text-danger" href="javascript:void(0)" @click="excluirLicenca(confirmacao.show)">
                                <IconTrash class="me-2" size="18" /> Excluir
                            </a>
                        </div>
                    </LinkConfirmation>
                </div>
            </div>
        </template>

        <!-- Dados -->
        <div class="card mb-3">
            <div class="card-body">
                <div class="d-flex flex-wrap justify-content-between gap-2 mb-3">
                    <div>
                        <h2 class="mb-1">{{ licenca.rotulo }}</h2>
                        <div class="text-secondary">{{ licenca.tipo?.nome }} · {{ licenca.orgao_emissor }}<span v-if="licenca.uf"> · {{ licenca.uf }}</span></div>
                    </div>
                    <div class="text-end">
                        <BadgeSituacao :situacao="licenca.situacao" />
                    </div>
                </div>

                <div v-if="!ativa" class="alert alert-secondary py-2">
                    Esta licença está {{ licenca.status === 'arquivada' ? 'arquivada' : 'substituída' }}: seus prazos não são recalculados,
                    não aparecem no painel e não geram alertas.
                </div>

                <div class="datagrid">
                    <div class="datagrid-item">
                        <div class="datagrid-title">Emissão</div>
                        <div class="datagrid-content">{{ formatarData(licenca.data_emissao) }}</div>
                    </div>
                    <div class="datagrid-item">
                        <div class="datagrid-title">Validade</div>
                        <div class="datagrid-content">{{ formatarData(licenca.data_validade) || 'Sem validade' }}</div>
                    </div>
                    <div class="datagrid-item">
                        <div class="datagrid-title">Requerer renovação até</div>
                        <div class="datagrid-content">
                            {{ formatarData(licenca.prazo_renovacao) || '-' }}
                            <span v-if="licenca.antecedencia_renovacao_dias" class="text-secondary small">({{ licenca.antecedencia_renovacao_dias }} dias antes)</span>
                        </div>
                    </div>
                    <div class="datagrid-item">
                        <div class="datagrid-title">Processo SEI</div>
                        <div class="datagrid-content">{{ licenca.processo_sei || '-' }}</div>
                    </div>
                    <div class="datagrid-item">
                        <div class="datagrid-title">Nº SEI do documento</div>
                        <div class="datagrid-content">{{ licenca.sei_documento || '-' }}</div>
                    </div>
                    <div class="datagrid-item">
                        <div class="datagrid-title">Empreendimento</div>
                        <div class="datagrid-content">{{ licenca.empreendimento || '-' }}</div>
                    </div>
                    <div class="datagrid-item">
                        <div class="datagrid-title">Contrato</div>
                        <div class="datagrid-content">{{ licenca.contrato?.numero_contrato || '-' }}</div>
                    </div>
                    <div class="datagrid-item">
                        <div class="datagrid-title">Responsáveis</div>
                        <div class="datagrid-content">
                            <span v-if="!licenca.responsaveis.length" class="text-warning">Nenhum (alertas vão para o e-mail padrão)</span>
                            <div v-for="r in licenca.responsaveis" :key="r.id">{{ r.name }}</div>
                        </div>
                    </div>
                    <div class="datagrid-item" v-if="licenca.arquivo_nome">
                        <div class="datagrid-title">Documento</div>
                        <div class="datagrid-content">
                            <a :href="route('sgc.licencas.arquivo', licenca.id)"><IconDownload size="16" class="me-1" />{{ licenca.arquivo_nome }}</a>
                        </div>
                    </div>
                    <div class="datagrid-item" v-if="licenca.anterior || licenca.sucessoras.length">
                        <div class="datagrid-title">Histórico</div>
                        <div class="datagrid-content">
                            <div v-if="licenca.anterior">
                                Sucede <Link :href="route('sgc.licencas.show', licenca.anterior.id)">{{ licenca.anterior.rotulo }}</Link>
                                <span class="text-secondary"> ({{ licenca.motivo_vinculo === 'renovacao' ? 'renovação' : 'retificação' }})</span>
                            </div>
                            <div v-for="s in licenca.sucessoras" :key="s.id">
                                Sucedida por <Link :href="route('sgc.licencas.show', s.id)">{{ s.rotulo }}</Link>
                            </div>
                        </div>
                    </div>
                </div>
                <p v-if="licenca.observacoes" class="mt-3 mb-0" style="white-space: pre-line">{{ licenca.observacoes }}</p>
            </div>
        </div>

        <!-- Abas -->
        <div class="card">
            <div class="card-header">
                <ul class="nav nav-tabs card-header-tabs">
                    <li class="nav-item">
                        <a class="nav-link" :class="{ active: aba === 'prazos' }" href="javascript:void(0)" @click="aba = 'prazos'">
                            Prazos <span class="badge bg-secondary-lt ms-2">{{ prazos.length }}</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" :class="{ active: aba === 'condicionantes' }" href="javascript:void(0)" @click="aba = 'condicionantes'">
                            Condicionantes <span class="badge bg-secondary-lt ms-2">{{ licenca.condicionantes.length }}</span>
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Prazos -->
            <div v-show="aba === 'prazos'">
                <div class="card-body d-flex flex-wrap gap-3 justify-content-between align-items-end">
                    <label class="form-check form-switch mb-0">
                        <input class="form-check-input" type="checkbox" v-model="somentePendentes">
                        <span class="form-check-label">Somente pendentes</span>
                    </label>

                    <form v-if="ativa && condicionantesEvento.length" class="d-flex flex-wrap gap-2 align-items-end" @submit.prevent="lancarEvento">
                        <div>
                            <label class="form-label">Lançar evento (ex.: acidente ambiental)</label>
                            <select class="form-select" v-model="formEvento.condicionante_id">
                                <option value="" disabled>Condicionante</option>
                                <option v-for="c in condicionantesEvento" :key="c.id" :value="c.id">
                                    {{ c.numero }} - {{ c.titulo || descreverRegra(c) }}
                                </option>
                            </select>
                        </div>
                        <div>
                            <input type="date" class="form-control" v-model="formEvento.data_evento">
                        </div>
                        <button class="btn btn-outline-primary" :disabled="formEvento.processing || !formEvento.condicionante_id">Lançar</button>
                        <div class="w-100">
                            <InputError :message="formEvento.errors.data_evento || formEvento.errors.condicionante_id" />
                        </div>
                    </form>
                </div>
                <div class="table-responsive">
                    <table class="table card-table table-vcenter table-hover">
                        <thead>
                            <tr>
                                <th>Data-limite</th>
                                <th>Prazo</th>
                                <th>Situação</th>
                                <th>Cumprimento</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="!prazosVisiveis.length">
                                <td colspan="5" class="text-center text-secondary py-4">
                                    Nenhum prazo. Os prazos são gerados a partir da validade (renovação) e das condicionantes com prazo.
                                </td>
                            </tr>
                            <tr v-for="prazo in prazosVisiveis" :key="prazo.id" style="cursor: pointer" @click="abrirPrazo(prazo)">
                                <td class="text-nowrap">
                                    <div class="fw-bold">{{ formatarData(prazo.data_limite) }}</div>
                                    <div class="small text-secondary" v-if="prazo.prorrogado">original {{ formatarData(prazo.data_limite_original) }}</div>
                                    <div class="small" v-if="prazo.status === 'pendente'" :class="prazo.dias_restantes < 0 ? 'text-danger' : 'text-secondary'">
                                        {{ textoDias(prazo.dias_restantes) }}
                                    </div>
                                </td>
                                <td>
                                    {{ prazo.descricao }}
                                    <span v-if="prazo.prorrogado" class="badge bg-azure-lt ms-1">prorrogado</span>
                                </td>
                                <td><BadgeSituacao :situacao="prazo.situacao" /></td>
                                <td class="small">
                                    <template v-if="prazo.registros.length">
                                        {{ formatarData(prazo.registros[0].data) }}
                                        <span v-if="prazo.registros[0].numero_oficio"> · {{ prazo.registros[0].numero_oficio }}</span>
                                        <span v-if="prazo.registros.length > 1" class="text-secondary"> (+{{ prazo.registros.length - 1 }})</span>
                                    </template>
                                    <span v-else class="text-secondary">-</span>
                                </td>
                                <td class="text-end">
                                    <button type="button" class="btn btn-sm btn-outline-primary" @click.stop="abrirPrazo(prazo)">Registrar</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Condicionantes -->
            <div v-show="aba === 'condicionantes'">
                <div class="card-body text-end">
                    <button type="button" class="btn btn-info" @click="modalCondicionante.abrir()">
                        <IconPlus class="me-1" /> Nova condicionante
                    </button>
                </div>
                <LinkConfirmation v-slot="confirmacao" :options="{ text: 'A condicionante e todos os seus prazos e registros serão excluídos.', confirmButtonClass: 'btn-danger', confirmButtonText: 'Excluir' }">
                    <div class="table-responsive">
                        <table class="table card-table table-vcenter">
                            <thead>
                                <tr>
                                    <th>Nº</th>
                                    <th>Condicionante</th>
                                    <th>Prazo</th>
                                    <th>Situação</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="!licenca.condicionantes.length">
                                    <td colspan="5" class="text-center text-secondary py-4">Nenhuma condicionante cadastrada.</td>
                                </tr>
                                <tr v-for="c in licenca.condicionantes" :key="c.id">
                                    <td class="text-nowrap fw-bold">
                                        {{ c.numero }}
                                        <div class="small text-secondary fw-normal" v-if="c.grupo">{{ c.grupo === 'geral' ? 'Geral' : 'Específica' }}</div>
                                    </td>
                                    <td>
                                        <div class="fw-bold" v-if="c.titulo">{{ c.titulo }}</div>
                                        <div class="text-secondary small text-truncate" style="max-width: 520px" :title="c.descricao">{{ c.descricao }}</div>
                                    </td>
                                    <td class="small">
                                        {{ descreverRegra(c) }}
                                        <span v-if="c.relatorio_sigla" class="badge bg-info-lt ms-1">{{ c.relatorio_sigla }}</span>
                                        <div v-if="c.prazos_pendentes_count" class="text-secondary">{{ c.prazos_pendentes_count }} pendente(s)</div>
                                    </td>
                                    <td>
                                        <span class="badge text-nowrap" :class="`bg-${corCondicionante[c.situacao]}-lt`">{{ situacaoCondicionante(c.situacao) }}</span>
                                    </td>
                                    <td class="text-end text-nowrap">
                                        <button type="button" class="btn btn-sm btn-outline-primary me-1" title="Editar" @click="modalCondicionante.abrir(c)">
                                            <IconPencil size="16" />
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-danger" title="Excluir" @click="excluirCondicionante(c, confirmacao.show)">
                                            <IconTrash size="16" />
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </LinkConfirmation>
            </div>
        </div>

        <ModalCondicionante ref="modalCondicionante" :licenca-id="licenca.id" :tipos-prazo="tiposPrazo" :situacoes="situacoesCondicionante" />
        <ModalPrazo ref="modalPrazo" :prazo="prazoAberto" :tipos-registro="tiposRegistro" />
    </AuthenticatedLayout>
</template>
