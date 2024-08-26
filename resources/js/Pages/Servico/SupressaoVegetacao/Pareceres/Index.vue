<script setup>
import {Head, Link, router} from "@inertiajs/vue3";
import Table from "@/Components/Table.vue";
import Navbar from "../Components/Navbar.vue";
import NavButton from "@/Components/NavButton.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import {ref} from "vue";
import {IconEye} from "@tabler/icons-vue";
import ModalVisualizar from "./Components/ModalVisualizar.vue";

const props = defineProps({
    data: {type: Object},
    contrato: {type: Object},
    servico: {type: Object},
});

const modalVisualizarRef = ref();
const abrirModalVisualizar = (item = null) => {
    modalVisualizarRef.value.abrirModal(item);
}

</script>

<template>

    <Head title="Pareceres"/>

    <AuthenticatedLayout>

        <template #header>
            <div class="w-100 d-flex justify-content-between">
                <Breadcrumb class="align-self-center" :links="[
                    { route: route('contratos.gestao.listagem', contrato.tipo_contrato), label: `Gestão de Contratos` },
                    { route: '#', label: contrato.contratada }
                ]"/>
                <Link class="btn btn-dark"
                      :href="route('contratos.contratada.servicos.index', { contrato: contrato.id })">
                    Voltar
                </Link>
            </div>
        </template>

        <Navbar :contrato="contrato" :servico="servico">
            <template #body>
                <Table
                    :columns="['Tipo', 'Status', 'Data do parecer']"
                    :records="data" table-class="table-hover">
                    <template #body="{ item }">
                        <tr>
                            <td>{{item.tipo}}</td>
                            <td>
                                <span v-if="item.fk_status === 1" class="badge text-bg-primary">Em confecção</span>
                                <span v-if="item.fk_status === 2" class="badge text-bg-warning">Em análise</span>
                                <span v-if="item.fk_status === 3" class="badge text-bg-primary">Aprovado</span>
                                <span v-if="item.fk_status === 4" class="badge text-bg-danger">Pendente</span>
                            </td>
                            <td>{{item.data_parecer}}</td>
                            <td class="text-center">
                                <NavButton @click="abrirModalVisualizar(item)" type-button="info" class="btn-icon"
                                           :icon="IconEye"/>
                            </td>
                        </tr>
                    </template>
                </Table>
            </template>
        </Navbar>
        <ModalVisualizar ref="modalVisualizarRef" />
    </AuthenticatedLayout>

</template>
