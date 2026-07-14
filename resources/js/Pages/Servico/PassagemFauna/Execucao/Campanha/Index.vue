<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import Navbar from "../../Navbar.vue";
import { Head, Link } from "@inertiajs/vue3";
import ModelSearchFormAllColumns from "@/Components/ModelSearchFormAllColumns.vue";
import Table from "@/Components/Table.vue";
import { dateTimeFormat } from "@/Utils/DateTimeUtils.js";
import NavLink from "@/Components/NavLink.vue";
import { IconEye, IconPencil, IconTrash } from "@tabler/icons-vue";
import ModalVisualizarCampanha from "./ModalVisualizarCampanha.vue";
import { ref } from "vue";
import NavButton from "@/Components/NavButton.vue";
import LinkConfirmation from "@/Components/LinkConfirmation.vue";

const modalVisualizarCampanha = ref({});
const props = defineProps({
    contrato: { type: Object },
    servico: { type: Object },
    campanhas: { type: Object }
});

const abrirModalVisualizarCampanha = (item) => {
    modalVisualizarCampanha.value.abrirModal(item);
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
                <ModelSearchFormAllColumns :columns="['id','periodo','data_inicial','data_final']">
                    <template #action>
                        <NavLink title="Nova campanha" class="btn btn-success"
                            route-name="contratos.contratada.servicos.passagem_fauna.execucao.campanha.create"
                            :param="{ contrato: contrato.id, servico: servico.id }" />
                    </template>
                </ModelSearchFormAllColumns>
                <Table class="text-center"
                    :columns="['N° Campanha', 'N° Licença', 'Período', 'Data início', 'Data final', 'Observações', 'Ação']"
                    :records="campanhas" table-class="table-hover align-middle">
                    <template #body="{ item }">
                        <tr>
                            <td>{{ item.id }}</td>
                            <td>
                                <span class="badge bg-warning text-white m-1"
                                    v-for="numero_licenca in item.abios.map(abio => abio.abio?.licenca?.numero_licenca)"
                                    :key="numero_licenca">{{ numero_licenca }}</span>
                            </td>
                            <td>{{ item.periodo }}</td>
                            <td>{{ dateTimeFormat(item.data_inicial) }}</td>
                            <td>{{ dateTimeFormat(item.data_final) }}</td>
                            <td>{{ item.obs }}</td>
                            <td class="text-center" style="white-space: nowrap;">
                                <div class="d-flex justify-content-center align-items-center gap-1">
                                    <NavButton @click="abrirModalVisualizarCampanha(item)"
                                        class="btn btn-icon btn-info p-1 d-flex align-items-center justify-content-center"
                                        :icon="IconEye" />

                                    <NavLink
                                        class="btn btn-icon btn-primary p-1 d-flex align-items-center justify-content-center"
                                        route-name="contratos.contratada.servicos.passagem_fauna.execucao.campanha.create"
                                        :param="{ contrato: contrato.id, servico: servico.id, campanha: item.id }"
                                        :icon="IconPencil" />

                                    <LinkConfirmation v-slot="confirmation"
                                        :options="{ text: 'A remoção da ABIO será permanente.' }">
                                        <Link :onBefore="confirmation.show" :href="route('contratos.contratada.servicos.passagem_fauna.execucao.campanha.delete', {
                                            contrato: contrato.id,
                                            servico: servico.id,
                                            campanha: item.id
                                        })" as="button" method="delete" type="button"
                                            class="btn btn-icon btn-danger p-1 d-flex align-items-center justify-content-center">
                                        <IconTrash />
                                        </Link>
                                    </LinkConfirmation>
                                </div>
                            </td>

                        </tr>
                    </template>
                </Table>
            </template>
        </Navbar>

        <ModalVisualizarCampanha :contrato="contrato" :servico="servico" ref="modalVisualizarCampanha" />

    </AuthenticatedLayout>
</template>
