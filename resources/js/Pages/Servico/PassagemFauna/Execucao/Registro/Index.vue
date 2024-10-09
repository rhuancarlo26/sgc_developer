<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import Navbar from "../../Navbar.vue";
import { Head, Link } from "@inertiajs/vue3";
import ModelSearchFormAllColumns from "@/Components/ModelSearchFormAllColumns.vue";
import Table from "@/Components/Table.vue";
import NavLink from "@/Components/NavLink.vue";
import { dateTimeFormat } from "@/Utils/DateTimeUtils.js";
import NavButton from "@/Components/NavButton.vue";
import { IconEye, IconPencil, IconTrash } from "@tabler/icons-vue";
import LinkConfirmation from "@/Components/LinkConfirmation.vue";
import ModalVisualizarRegistro from "./ModalVisualizarRegistro.vue";
import { ref } from "vue";

const modalVisualizarRegistro = ref({});
const props = defineProps({
    contrato: { type: Object },
    servico: { type: Object },
    registros: { type: Object }
});
const grupo = ref({
    1: 'Avifauna',
    2: 'Herpetofauna',
    3: 'Mastofauna'
});

const abrirModalVisualizarRegistro = (item) => {
    modalVisualizarRegistro.value.abrirModal(item);
}

</script>
<template>

    <Head :title="`${contrato.contratada.slice(0, 10)}...`" />

    <AuthenticatedLayout>

        <template #header>
            <div class="w-100 d-flex justify-content-between">
                <Breadcrumb class="align-self-center" :links="[
                    { route: route('contratos.gestao.listagem', contrato.tipo_contrato), label: `Gestão de Contratos` },
                    { route: '#', label: contrato.contratada }
                ]
                    " />
                <Link class="btn btn-dark"
                    :href="route('contratos.contratada.servicos.index', { contrato: props.contrato.id })">
                Voltar
                </Link>
            </div>
        </template>

        <Navbar :contrato="contrato" :servico="servico">
            <template #body>
                <ModelSearchFormAllColumns :columns="[]">
                    <template #action>
                        <NavLink title="Novo registro" class="btn btn-success"
                            route-name="contratos.contratada.servicos.passagem_fauna.execucao.registro.create"
                            :param="{ contrato: contrato.id, servico: servico.id }" />
                    </template>
                </ModelSearchFormAllColumns>
                <Table
                    :columns="['Nome do registro', 'ID da campanha', 'Grupo amostrado', 'Espécie', 'Data registro', 'Data cadastro', 'Ação']"
                    :records="registros" table-class="table-hover">
                    <template #body="{ item }">
                        <tr>
                            <td>{{ item.id }}</td>
                            <td>{{ item.id_campanha }}</td>
                            <td>{{ grupo[item.id_grupo] }}</td>
                            <td>{{ item.especie }}</td>
                            <td>{{ dateTimeFormat(item.data_registro) }}</td>
                            <td>{{ dateTimeFormat(item.created_at) }}</td>
                            <td>
                                <NavButton @click="abrirModalVisualizarRegistro(item)" class="btn-icon" :icon="IconEye"
                                    type-button="info" />
                                <NavLink class="btn btn-icon btn-primary me-1"
                                    route-name="contratos.contratada.servicos.passagem_fauna.execucao.registro.create"
                                    :param="{ contrato: contrato.id, servico: servico.id, registro: item.id }"
                                    :icon="IconPencil" />
                                <LinkConfirmation v-slot="confirmation"
                                    :options="{ text: 'A remoção da campanha será permanente.' }">
                                    <Link :onBefore="confirmation.show"
                                        :href="route('contratos.contratada.servicos.passagem_fauna.execucao.registro.delete', { contrato: contrato.id, servico: servico.id, registro: item.id })"
                                        as="button" method="delete" type="button" class="btn btn-icon btn-danger">
                                    <IconTrash />
                                    </Link>
                                </LinkConfirmation>
                            </td>
                        </tr>
                    </template>
                </Table>
            </template>
        </Navbar>

        <ModalVisualizarRegistro ref="modalVisualizarRegistro" />

    </AuthenticatedLayout>
</template>
