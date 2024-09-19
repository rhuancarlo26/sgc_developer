<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import Navbar from "../Navbar.vue";
import { Head, Link } from "@inertiajs/vue3";
import Table from "@/Components/Table.vue";
import { ref } from "vue";
import { IconDots } from "@tabler/icons-vue";
import VisualizarParecer from "./Modal/VisualizarParecer.vue";

const props = defineProps({
    contrato: { type: Object },
    servico: { type: Object },
    pareceres: { type: Array },
});

const modalVisualizarParecer = ref();

const visualizarParecer = (item) => {
    modalVisualizarParecer.value.abrirModal(item);
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
                <div>
                    <Link class="btn"
                        :href="route('contratos.contratada.servicos.index', { contrato: props.contrato.id })">
                    Voltar
                    </Link>
                </div>
            </div>
        </template>

        <Navbar :contrato="contrato" :servico="servico">
            <template #body>

                <Table :columns="['Tipo', 'Status', 'Data do parecer', 'Ação']" :records="pareceres"
                    table-class="table-hover">
                    <template #body="{ item }">
                        <tr>
                            <td>{{ item.tipo }}</td>
                            <td>
                                <span v-if="item.fk_status === 1" class="shadow-none badge text-primary">
                                    Em confecção
                                </span>
                                <span v-if="item.fk_status === 2" class="shadow-none badge text-warning">
                                    Em análise
                                </span>
                                <span v-if="item.fk_status === 3" class="shadow-none badge text-info">
                                    Aprovado
                                </span>
                                <span v-if="item.fk_status === 4" class="shadow-none badge text-danger">
                                    Pendente
                                </span>
                            </td>
                            <td>{{ item.data_parecer }}</td>
                            <td>
                                <button class="btn dropdown-toggle align-text-top" data-bs-boundary="viewport"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    <IconDots />
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                    <li>
                                        <a class="dropdown-item" @click="visualizarParecer(item)"
                                            href="javascript:void(0);">
                                            Visualizar parecer
                                        </a>
                                    </li>
                                </ul>
                            </td>
                        </tr>
                    </template>
                </Table>

            </template>
        </Navbar>
        <VisualizarParecer ref="modalVisualizarParecer" />
    </AuthenticatedLayout>

</template>