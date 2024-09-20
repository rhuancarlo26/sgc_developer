<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import Navbar from "../../Navbar.vue";
import { Head, Link } from "@inertiajs/vue3";
import ModelSearchFormAllColumns from "@/Components/ModelSearchFormAllColumns.vue";
import Table from "@/Components/Table.vue";
import NavButton from "@/Components/NavButton.vue";
import { IconFileDownload, IconClipboardList, IconEye, IconClipboardCheck, IconThumbUp, IconFilter } from "@tabler/icons-vue";
import { ref } from 'vue';
import LogAtividadesModal  from './LogAtividadesModal.vue';
import VisualizarRNCModal  from './VisualizarRNCModal.vue';
import CadastrarVistoriaModal  from './CadastrarVistoriaModal.vue';
import ParecerFiscalModal  from './ParecerFiscalModal.vue';

const props = defineProps({
    contrato: { type: Object },
    servico: { type: Object },
    rncs: { type: Array }
});

const selectedItem_logAtividades = ref(null);
const selectedItem_visualizarRNC = ref(null);
const selectedItem_cadastrarVistoria = ref(null);
const selectedItem_parecerFiscal = ref(null);

const modalRef_LogAtividadesModal = ref(null);
const modalRef_VisualizarRNCModal = ref(null);
const modalRef_CadastrarVistoriaModal = ref(null);
const modalRef_ParecerFiscalModal = ref(null);

function abrirLogAtividadesModal(item) {
    selectedItem_logAtividades.value = item;
    modalRef_LogAtividadesModal.value.abrirModal();
}

function abrirVisualizarRNCModal(item) {
    selectedItem_visualizarRNC.value = item
    modalRef_VisualizarRNCModal.value.abrirModal();
}

function abrirCadastrarVistoriaModal(item) {
    selectedItem_cadastrarVistoria.value = item
    modalRef_CadastrarVistoriaModal.value.abrirModal();
}

function abrirParecerFiscalModal(item) {
    selectedItem_parecerFiscal.value = item
    modalRef_ParecerFiscalModal.value.abrirModal();
}

function exportarExcel() {
    console.log("exportarExcel")
}

function todosRnc() {
    console.log("todosRnc")
}

function rncAtendidos() {
    console.log("rncAtendidos")
}

function rncAbertos() {
    console.log("rncAbertos")
}
</script>

<template>

    <Head :title="`${contrato.contratada.slice(0, 10)}...`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="w-100 d-flex justify-content-between">
                <Breadcrumb class="align-self-center" :links="[
        { route: route('contratos.gestao.listagem', contrato.tipo_id), label: `Gestão de Contratos` },
        { route: '#', label: contrato.contratada }
    ]" />
                <Link class="btn btn-dark"
                    :href="route('contratos.contratada.servicos.index', { contrato: props.contrato.id })">
                Voltar
                </Link>
            </div>
        </template>

        <Navbar :contrato="contrato" :servico="servico">
            <template #body>
                <!-- Container Pesquisa / Botões -->
                <div style="display: inline-flex; align-items: flex-start; justify-content: space-between;">
                    <!-- Pesquisa -->
                    <div style="display: block; width: 100%;">
                        <ModelSearchFormAllColumns :columns="['nome', 'parametro.nome']">
                            <template #action></template>
                        </ModelSearchFormAllColumns>
                    </div>
                    <!-- Exportar Excel -->
                    <NavButton :icon="IconFileDownload" class="nav-item" type-button="primary" title="Exportar Excel"
                        @click="exportarExcel" />
                    <!-- Filtro: Todos os RNC's -->
                    <NavButton :icon="IconFilter" class="nav-item" type-button="success" title="Todos os RNC's"
                        @click="todosRnc" />
                    <!-- Filtro: RNC's Atendidos -->
                    <NavButton :icon="IconFilter" class="nav-item" type-button="success" title="RNC's Atendidos"
                        @click="rncAtendidos" />
                    <!-- Filtro: RNC's Em Abertos -->
                    <NavButton :icon="IconFilter" class="nav-item" type-button="success" title="RNC's Em Abertos"
                        @click="rncAbertos" />
                </div>

                <!-- Listagem -->
                <Table :columns="[
                        'ID Ocorrência', 'Intensidade Ocorrência', 'Data da Ocorrência', 'Data Fim', 'Ocorrência Anterior',
                        'Prazo Correção', 'Lote', 'Construtora', 'Status Aprovação', 'Status Atendimento', 'Envio', 'Ação'
                    ]" :records="{ data: rncs, links: [] }" table-class="table-hover">

                    <template #body="{ item: rnc }">
                        <tr>
                            <td>{{ rnc.nome_id }}</td>
                            <td>{{ rnc.intensidade }}</td>
                            <td>{{ rnc.data_ocorrenciaF }}</td>
                            <td>{{ rnc.data_fimF }}</td>
                            <td>{{ rnc.ocorrencia_anterior }}</td>
                            <td>{{ rnc.dias_restantes }}</td>
                            <td>{{ rnc.nome_lote }}</td>
                            <td>{{ rnc.empresa }}</td>
                            <td>{{ rnc.status }}</td>
                            <td>{{ rnc.status_atendimento }}</td>
                            <td>{{ rnc.envio_empresa }}</td>
                            <td class="text-center container-btn-acao">
                                <div class="group-btn">
                                    <a class="btn btn-dark btn-sm" href="#" @click.prevent="abrirLogAtividadesModal(rnc)"
                                        title="Log de Atividades">
                                        <IconClipboardList />
                                    </a>
                                    <a class="btn btn-primary btn-sm" href="#" @click.prevent="abrirVisualizarRNCModal(rnc)"
                                        title="Visualizar RNC">
                                        <IconEye />
                                    </a>
                                </div>
                                <div class="group-btn">
                                    <a class="btn btn-warning btn-sm" href="#" @click.prevent="abrirParecerFiscalModal(rnc)"
                                        title="Parecer Fiscal">
                                        <IconThumbUp />
                                    </a>
                                    <a class="btn btn-success btn-sm" href="#" @click.prevent="abrirCadastrarVistoriaModal(rnc)"
                                        title="Cadastrar Vistoria">
                                        <IconClipboardCheck />
                                    </a>
                                </div>
                            </td>
                        </tr>
                    </template>
                </Table>
            </template>
        </Navbar>
        <!-- MODAIS -->
        <LogAtividadesModal ref="modalRef_LogAtividadesModal" :rnc="selectedItem_logAtividades" />
        <VisualizarRNCModal ref="modalRef_VisualizarRNCModal" :rnc="selectedItem_visualizarRNC" />
        <CadastrarVistoriaModal ref="modalRef_CadastrarVistoriaModal" :rnc="selectedItem_cadastrarVistoria" />
        <ParecerFiscalModal ref="modalRef_ParecerFiscalModal" :rnc="selectedItem_parecerFiscal" />
    </AuthenticatedLayout>
</template>

<style scoped>
.group-btn {
    display: flex;
    flex-direction: row;
    align-items: center;
    justify-content: center;
    margin-top: 5px;
    margin-bottom: 5px;
}
.group-btn a {
    margin: 5px;
    width: 40px;
    height: 40px;
    border-radius: 7px;
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>
