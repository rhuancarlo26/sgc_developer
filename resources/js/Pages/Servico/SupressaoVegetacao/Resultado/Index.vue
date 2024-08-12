<script setup>
import {Head, Link} from "@inertiajs/vue3";
import { IconTrash, IconEye, IconPencil} from "@tabler/icons-vue";
import Table from "@/Components/Table.vue";
import Navbar from "../Components/Navbar.vue";
import NavButton from "@/Components/NavButton.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import LinkConfirmation from "@/Components/LinkConfirmation.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import {computed, ref} from "vue";
import {dateTimeFormat} from "@/Utils/DateTimeUtils.js";
import ModalCadastro from "./Components/ModalCadastro.vue";
import ModalVisualizar from "./Components/ModalVisualizar.vue";

const props = defineProps({
    data: {type: Object},
    servico: {type: Object},
    contrato: {type: Object},
    periodos: {type: Array},
});

const modalCadastroRef = ref();
const abrirModalCadastro = (item = null) => {
    modalCadastroRef.value.abrirModal(item);
}

const modalVisualizarRef = ref();
const abrirModalVisualizar = (item) => {
    modalVisualizarRef.value.abrirModal(item);
}

</script>

<template>

    <Head title="Resultado"/>

    <AuthenticatedLayout>

        <template #header>
            <div class="w-100 d-flex justify-content-between">
                <Breadcrumb class="align-self-center" :links="[
                    { route: route('contratos.gestao.listagem', contrato.tipo_id), label: `Gestão de Contratos` },
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
                <div class="ms-auto mb-4">
                    <NavButton
                        @click="abrirModalCadastro" route-name="contratos.contratada.servicos.pmqa.configuracao.ponto.importar"
                        :param="{ contrato: props.contrato.id, servico: props.servico.id }" type-button="success"
                        title="Incluir resultado"
                    />
                </div>
                <Table
                    :columns="['ID Código', 'Período de Análise', 'Data Início', 'Data Final', 'Ação']"
                    :records="data" table-class="table-hover">
                    <template #body="{ item }">
                        <tr>
                            <td class="text-center">{{ item.chave }}</td>
                            <td class="text-center">{{ periodos.find(p => p.id === item.periodo).label }}</td>
                            <td class="text-center">{{ dateTimeFormat(item.dt_inicio) }}</td>
                            <td class="text-center">{{ dateTimeFormat(item.dt_final) }}</td>
                            <td>
                                <div class="d-flex justify-content-center">
                                    <NavButton @click="abrirModalVisualizar(item)" type-button="info" class="btn-icon"
                                               :icon="IconEye"/>
                                    <NavButton @click="abrirModalCadastro(item)" type-button="warning" class="btn-icon"
                                               :icon="IconPencil"/>
                                    <LinkConfirmation v-slot="confirmation"
                                                      :options="{ text: 'Você deseja remover o plano de supressão?' }">
                                        <Link :onBefore="confirmation.show"
                                              :href="route('contratos.contratada.servicos.supressao-vegetacao.execucao.supressao.delete', item.id)"
                                              as="button" method="delete" type="button" class="btn btn-icon btn-danger">
                                            <IconTrash/>
                                        </Link>
                                    </LinkConfirmation>
                                </div>
                            </td>
                        </tr>
                    </template>
                </Table>
            </template>
        </Navbar>
        <ModalVisualizar ref="modalVisualizarRef"/>
        <ModalCadastro ref="modalCadastroRef" :servico="servico" :periodos="periodos" />
    </AuthenticatedLayout>

</template>
