<!-- resources\js\Pages\Servico\ControleDeOcorrencias\Execucao\Ocorrencia\Index.vue -->
<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import Navbar from "../../Navbar.vue";
import { Head, Link } from "@inertiajs/vue3";
import ModelSearchFormAllColumns from "@/Components/ModelSearchFormAllColumns.vue";
import Table from "@/Components/Table.vue";
import NavButton from "@/Components/NavButton.vue";
import { IconPlus, IconFileDownload, IconClipboardList, IconEye, IconPencil, IconTrash, IconClipboardCheck } from "@tabler/icons-vue";
import { ref } from 'vue';
import NovaOcorrenciaModal from './NovaOcorrenciaModal.vue';
import LogAtividadesModal from './LogAtividadesModal.vue';
import VisualizarOcorrenciaModal from './VisualizarOcorrenciaModal.vue';
import EditarOcorrenciaModal from './EditarOcorrenciaModal.vue';
import CadastrarVistoriaModal from './CadastrarVistoriaModal.vue';
import ExcluirOcorrenciaModal from './ExcluirOcorrenciaModal.vue';

const props = defineProps({
    contrato: { type: Object },
    servico: { type: Object },
    ocorrencias: { type: Array }
});

const selectedItem_visualizarOcorrencia = ref(null);
const selectedItem_editarOcorrencia = ref(null);
const selectedItem_excluirOcorrencia = ref(null);

const modalRef_NovaOcorrenciaModal = ref(null);
const modalRef_LogAtividadesModal = ref(null);
const modalRef_VisualizarOcorrenciaModal = ref(null);
const modalRef_EditarOcorrenciaModal = ref(null);
const modalRef_CadastrarVistoriaModal = ref(null);
const modalRef_ExcluirOcorrenciaModal = ref(null);

function abrirNovaOcorrencia() {
    console.log("NovaOcorrenciaModal");
    modalRef_NovaOcorrenciaModal.value.abrirModal();
}

function abrirLogAtividadesModal(item) {
    console.log("LogAtividadesModal");
    modalRef_LogAtividadesModal.value.abrirModal();
}

function abrirVisualizarOcorrenciaModal(item) {
    selectedItem_visualizarOcorrencia.value = item;
    modalRef_VisualizarOcorrenciaModal.value.abrirModal();
}

function abrirEditarOcorrenciaModal(item) {
    selectedItem_editarOcorrencia.value = item;
    modalRef_EditarOcorrenciaModal.value.abrirModal();
}

function abrirCadastrarVistoriaModal(item) {
    console.log("CadastrarVistoriaModal");
    modalRef_CadastrarVistoriaModal.value.abrirModal();
}

function abrirExcluirOcorrenciaModal(item) {
    selectedItem_excluirOcorrencia.value = item;
    modalRef_ExcluirOcorrenciaModal.value.abrirModal();
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
                <Link class="btn btn-dark" :href="route('contratos.contratada.servicos.index', { contrato: props.contrato.id })">
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
                        @click="abrirNovaOcorrencia" />
                    <!-- Nova Ocorrência -->
                    <NavButton :icon="IconPlus" class="nav-item" type-button="success" title="Nova Ocorrência"
                        @click="abrirNovaOcorrencia" />
                </div>

                <!-- Listagem -->
                <Table :columns="[
                    'ID Ocorrência', 'Intensidade Ocorrência', 'Data da Ocorrência', 'Data Fim',
                    'Ocorrência Anterior', 'Prazo Correção', 'Lote', 'Construtora',
                    'Status Aprovação', 'Envio', 'Ação'
                ]" :records="{ data: ocorrencias, links: [] }" table-class="table-hover">
                    <template #body="{ item: ocorrencia }">
                        <tr>
                            <td>{{ ocorrencia.nome_id }}</td>
                            <td>{{ ocorrencia.intensidade }}</td>
                            <td>{{ ocorrencia.data_ocorrenciaF }}</td>
                            <td>{{ ocorrencia.data_fimF }}</td>
                            <td>{{ ocorrencia.ocorrencia_anterior }}</td>
                            <td>{{ ocorrencia.dias_restantes }}</td>
                            <td>{{ ocorrencia.nome_lote }}</td>
                            <td>{{ ocorrencia.empresa }}</td>
                            <td>{{ ocorrencia.status }}</td>
                            <td>{{ ocorrencia.envio_empresa }}</td>
                            <td class="text-center container-btn-acao">
                                <div class="group-btn">
                                    <a class="btn btn-dark btn-sm" href="#" @click.prevent="abrirLogAtividadesModal(ocorrencia)"
                                        title="Log de Atividades">
                                        <IconClipboardList />
                                    </a>
                                    <a class="btn btn-primary btn-sm" href="#" @click.prevent="abrirVisualizarOcorrenciaModal(ocorrencia)"
                                        title="Visualizar Ocorrencia">
                                        <IconEye />
                                    </a>
                                    <a class="btn btn-warning btn-sm" href="#" @click.prevent="abrirEditarOcorrenciaModal(ocorrencia)"
                                        title="Editar Ocorrencia">
                                        <IconPencil />
                                    </a>
                                </div>
                                <div class="group-btn">
                                    <a class="btn btn-success btn-sm" href="#" @click.prevent="abrirCadastrarVistoriaModal(ocorrencia)"
                                        title="Cadastrar Vistoria">
                                        <IconClipboardCheck />
                                    </a>
                                    <a class="btn btn-danger btn-sm" href="#" @click.prevent="abrirExcluirOcorrenciaModal(ocorrencia)"
                                        title="Excluir">
                                        <IconTrash />
                                    </a>
                                </div>
                            </td>
                        </tr>
                    </template>
                </Table>
            </template>
        </Navbar>

        <!-- MODAIS -->
        <NovaOcorrenciaModal ref="modalRef_NovaOcorrenciaModal" :contrato="props.contrato" :servico="props.servico" />
        <VisualizarOcorrenciaModal ref="modalRef_VisualizarOcorrenciaModal" :ocorrencia="selectedItem_visualizarOcorrencia" />
        <LogAtividadesModal ref="modalRef_LogAtividadesModal" :contrato="props.contrato" :servico="props.servico" />
        <EditarOcorrenciaModal ref="modalRef_EditarOcorrenciaModal" :ocorrencia="selectedItem_editarOcorrencia" />
        <CadastrarVistoriaModal ref="modalRef_CadastrarVistoriaModal" :contrato="props.contrato" :servico="props.servico" />
        <ExcluirOcorrenciaModal ref="modalRef_ExcluirOcorrenciaModal" :ocorrencia="selectedItem_excluirOcorrencia" />
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
