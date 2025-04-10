<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import Navbar from "../../Navbar.vue";
import { Head, Link } from "@inertiajs/vue3";
import ModelSearchFormAllColumns from "@/Components/ModelSearchFormAllColumns.vue";
import Table from "@/Components/Table.vue";
import NavButton from "@/Components/NavButton.vue";
import { IconPencil, IconTrash, IconPlus, IconFileDownload } from "@tabler/icons-vue";
import LinkConfirmation from "@/Components/LinkConfirmation.vue";
import { ref } from 'vue';
import GerarACAModal from './GerarACAModal.vue';
import VisualizarACAModal from './VisualizarACAModal.vue';
import { IconEye } from "@tabler/icons-vue";

const props = defineProps({
    contrato: { type: Object },
    servico: { type: Object },
    acas: { type: Array }
});

const selectedItem_visualizarACA = ref(null);

const modalRef_GerarACAModal = ref(null);
const modalRef_VisualizarACAModal = ref(null);

function gerarACAModal() {
    modalRef_GerarACAModal.value.abrirModal();
}

function visualizarACAModal(item) {
    selectedItem_visualizarACA.value = item
    modalRef_VisualizarACAModal.value.abrirModal();
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
                    <!-- Gerar ACA -->
                    <NavButton :icon="IconPlus" class="nav-item" type-button="success" title="Gerar ACA"
                        @click="gerarACAModal" />
                </div>

                <!-- Listagem -->
                <Table :columns="['ID ACA', 'Data ACA', 'Relação de RNCs', 'Lote', 'Construtora', 'Envio', 'Ação']"
                    :records="{ data: acas, links: [] }" table-class="table-hover">
                    <template #body="{ item: aca }">
                        <tr>
                            <td>{{ aca.id_aca }}</td>
                            <td>{{ aca.data_aca }}</td>
                            <td>{{ aca.relacao_rncs_aca }}</td>
                            <td>{{ aca.lote_aca }}</td>
                            <td>{{ aca.construtora_aca }}</td>
                            <td>{{ aca.envio_aca }}</td>
                            <td class="text-center container-btn-acao">
                                <div class="group-btn">
                                    <a class="btn btn-primary btn-sm" href="#"
                                        @click.prevent="visualizarACAModal(aca)" title="Visualizar ACA">
                                        <IconEye />
                                    </a>
                                </div>
                            </td>
                        </tr>
                    </template>
                </Table>
            </template>
        </Navbar>

        <!-- Modal Gerar ACA -->
        <GerarACAModal ref="modalRef" :contrato="props.contrato" :servico="props.servico" />
        <VisualizarACAModal ref="modalRef_VisualizarACAModal" :aca="selectedItem_visualizarACA" />
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