<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import Table from "@/Components/Table.vue";
import ModelSearchForm from "@/Components/ModelSearchForm.vue";
import { Head, Link } from "@inertiajs/vue3";
import Navbar from "../Navbar.vue";
import { IconDots } from "@tabler/icons-vue";
import ModalVisualizarLicenca from "./ModalVisualizarLicenca.vue";
import { ref } from "vue";

defineProps({
    contrato: Object,
    servicos: Object
});

const modalVisualizarLicenca = ref();

const abrirModalLicenca = (servico) => {
    modalVisualizarLicenca.value.abrirModal(servico);
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
    ]
        " />
                <div class="container-buttons">
                    <Link class="btn btn-info me-2" :href="route('contratos.contratada.servicos.create', contrato.id)">
                    Cadastrar serviço
                    </Link>
                </div>
            </div>
        </template>

        <Navbar :contrato="contrato">
            <template #body>


                <!-- Pesquisa-->
                <ModelSearchForm :search-columns="{}" />

                <!-- Listagem-->
                <Table :columns="['Tema', 'Serviço', 'Especificação', 'Licença', 'Status', 'Ação']" :records="servicos"
                    table-class="table-hover">
                    <template #body="{ item }">
                        <tr>
                            <td>{{ item.tema?.nome }}</td>
                            <td>{{ item.tipo?.nome }}</td>
                            <td>{{ item.especificacao }}</td>
                            <td>
                                <a @click="abrirModalLicenca(item)" href="javascript:void(0)">
                                    TESTE
                                </a>
                            </td>
                            <td>
                                <span v-if="item.servico_status_id === 1" class="btn btn-info">
                                    {{ item.status?.nome }}
                                </span>
                                <span v-else-if="item.servico_status_id === 2" class="btn btn-warning">
                                    {{ item.status?.nome }}
                                </span>
                                <span v-else-if="item.servico_status_id === 3" class="btn btn-primary">
                                    {{ item.status?.nome }}
                                </span>
                                <span v-else class="btn btn-danger">
                                    {{ item.status?.nome }}
                                </span>
                            </td>
                            <td>
                                <button type="button" class="btn btn-icon btn-info dropdown-toggle p-2"
                                    data-bs-boundary="viewport" data-bs-toggle="dropdown" aria-expanded="false">
                                    <IconDots />
                                </button>
                                <div class="dropdown-menu dropdown-menu-end">

                                </div>
                            </td>
                        </tr>
                    </template>
                </Table>
            </template>
        </Navbar>

        <ModalVisualizarLicenca ref="modalVisualizarLicenca" />

    </AuthenticatedLayout>
</template>
