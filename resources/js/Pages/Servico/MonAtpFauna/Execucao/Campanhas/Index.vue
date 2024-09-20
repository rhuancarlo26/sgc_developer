<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import Navbar from "../../Navbar.vue";
import NavButton from "@/Components/NavButton.vue";
import ModelSearchFormAllColumns from "@/Components/ModelSearchFormAllColumns.vue";
import Table from "@/Components/Table.vue";
import ModalNovaCampanha from "./ModalNovaCampanha.vue";
import ModalEditarCampanha from "./ModalEditarCampanha.vue";
import ModalVisualizarCampanha from "./ModalVisualizarCampanha.vue";
import ModalExcluirCampanha from "./ModalExcluirCampanha.vue";
import { ref } from "vue";
import { IconDots } from "@tabler/icons-vue";
import {Head, router} from "@inertiajs/vue3";
import LinkConfirmation from "@/Components/LinkConfirmation.vue";

const props = defineProps({
    contrato: { type: Object },
    servico: { type: Object },
    data: { type: Object },
    licencasVigente: { type: Array },
    configVinculacao: { type: Array },
});

const modalNovaCampanha = ref({});
const modalEditarCampanha = ref({});
const modalVisualizarCampanha = ref({});
const modalExcluirCampanha = ref({});

const showActionsModal = ref(true);
const abrirModalNovaCampanha = (item) => {
    showActionsModal.value = true;
    modalNovaCampanha.value.abrirModal(item);
}

const abrirModalVisualizarCampanha = (item) => {
    showActionsModal.value = false;
    modalNovaCampanha.value.abrirModal(item);
}

const linkConfirmationRef = ref()
const abrirModalExcluirCampanha = (id) => {
    linkConfirmationRef.value.show(() => {
        router.delete(route('contratos.contratada.servicos.mon_atp_fauna.execucao.campanhas.delete', id))
    })
}
</script>

<template>

    <Head title="Campanhas" />

    <AuthenticatedLayout>

        <template #header>
            <div class="w-100 d-flex justify-content-between">
                <Breadcrumb class="align-self-center" :links="[
                    { route: route('contratos.gestao.listagem', contrato.tipo_contrato), label: `Gestão de Contratos` },
                    { route: '#', label: contrato.contratada }
                ]" />
                <Link class="btn btn-dark"
                    :href="route('contratos.contratada.servicos.index', { contrato: contrato.id })">
                Voltar
                </Link>
            </div>
        </template>

        <Navbar :contrato="contrato" :servico="servico">
            <template #body>
                <ModelSearchFormAllColumns :columns="[]">
                    <template #action>
                        <NavButton @click="abrirModalNovaCampanha()" type-button="info" title="Nova Campanha" />
                    </template>
                </ModelSearchFormAllColumns>

                <Table
                    :columns="['BR', 'UF incial', 'UF final', 'KM inicial', 'KM final', 'Data inical', 'Data final', 'Observação', 'Ação']"
                    :records="data" table-class="table-hover">
                    <template #body="{ item }">
                        <tr>
                            <td>{{ item.rodovia }}</td>
                            <td>{{ item.nome_uf_inicial }} - {{item.nome_estado_inicial}}</td>
                            <td>{{ item.nome_uf_final }} - {{item.nome_estado_final}}</td>
                            <td>{{ item.km_inicial }}</td>
                            <td>{{ item.km_final }}</td>
                            <td>{{ item.data_inicialF }}</td>
                            <td>{{ item.data_finalF }}</td>
                            <td>{{ item.observacao }}</td>
                            <td>
                                <button type="button" class="btn btn-icon btn-info dropdown-toggle p-2"
                                    data-bs-boundary="viewport" data-bs-toggle="dropdown" aria-expanded="false">
                                    <IconDots />
                                </button>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <a href="#" class="dropdown-item" @click.prevent="abrirModalNovaCampanha(item)">
                                        Editar
                                    </a>
                                    <a href="#" class="dropdown-item" @click.prevent="abrirModalVisualizarCampanha(item)">
                                        Visualizar
                                    </a>
                                    <a href="#" class="dropdown-item" @click.prevent="abrirModalExcluirCampanha(item.id)">
                                        Excluir
                                    </a>
                                </div>

                            </td>
                        </tr>
                    </template>
                </Table>
            </template>
        </Navbar>

        <ModalNovaCampanha ref="modalNovaCampanha" :licencasVigente="licencasVigente" :configVinculacao="configVinculacao" :show-action="showActionsModal" />
        <ModalEditarCampanha ref="modalEditarCampanha" />
        <ModalVisualizarCampanha ref="modalVisualizarCampanha" />
        <ModalExcluirCampanha ref="modalExcluirCampanha" />
        <LinkConfirmation ref="linkConfirmationRef" :options="{ text: 'Você tem certeza que deseja excluir esta campanha?' }" />

    </AuthenticatedLayout>
</template>
