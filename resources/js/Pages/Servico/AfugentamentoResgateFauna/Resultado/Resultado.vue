<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import Navbar from "../Navbar.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import ModelSearchFormAllColumns from "@/Components/ModelSearchFormAllColumns.vue";
import Table from "@/Components/Table.vue";
import { ref } from "vue";
import { IconDots } from "@tabler/icons-vue";
import { dateTimeFormat } from "@/Utils/DateTimeUtils";
import Swal from 'sweetalert2';
import NovoResultado from "./Modal/NovoResultado.vue";
import VisualizarResultadoModal from "./Modal/VisualizarResultadoModal.vue";

const props = defineProps({
    contrato: { type: Object },
    servico: { type: Object },
    resultado: { type: Array },
});

const novoResultadoModal = ref();

const abrirModalNovoResultado = (servico) => {
    novoResultadoModal.value.abrirModal(servico);
}

const abrirModalEditarResultado = (item) => {
    novoResultadoModal.value.updateModal(item);
}

const visualizarResultadoModal = ref();

const abrirModalVisualizarResultado = (item) => {
    visualizarResultadoModal.value.abrirModal(item);
}

const destroy = (resultado) => {
    Swal.fire({
        title: "Excluir Resultado",
        text: "Deseja continuar?",
        icon: "warning",
        showCloseButton: true,
        showCancelButton: true,
        focusConfirm: false,
    }).then((r) => {
        if (r.isConfirmed) {
            router.delete(route('contratos.contratada.servicos.afugentamento.resgate.fauna.resultado.delete', { resultado: resultado.id }));
        }
    })
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

                <!-- Pesquisa-->
                <ModelSearchFormAllColumns class="w-100" :columns="[]">
                    <template #action>
                        <a @click="abrirModalNovoResultado(servico)" class="btn btn-success" href="javascript:void(0)">
                            Novo Resultado
                        </a>
                    </template>
                </ModelSearchFormAllColumns>

                <Table :columns="['ID Resultado', 'Nome Resultado', 'Data Início', 'Data Final', 'Ação']"
                    :records="resultado" table-class="table-hover">
                    <template #body="{ item }">
                        <tr>
                            <td>{{ item.id }}</td>
                            <td>{{ item.nome }}</td>
                            <td>{{ dateTimeFormat(item.dt_inicio) }}</td>
                            <td>{{ dateTimeFormat(item.dt_final) }}</td>
                            <td>
                                <button class="btn dropdown-toggle align-text-top" data-bs-boundary="viewport"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    <IconDots />
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                    <li>
                                        <a class="dropdown-item" @click="abrirModalVisualizarResultado(item)"
                                            href="javascript:void(0);">
                                            Analisar
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" @click="abrirModalVisualizarResultado(item)"
                                            href="javascript:void(0);">
                                            Visualizar
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" @click="abrirModalEditarResultado(item)"
                                            href="javascript:void(0);">
                                            Editar
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" @click="destroy(item)" href="javascript:void(0);">
                                            Excluir
                                        </a>
                                    </li>
                                </ul>
                            </td>
                        </tr>
                    </template>
                </Table>
            </template>
        </Navbar>
        <NovoResultado ref="novoResultadoModal" />
        <VisualizarResultadoModal ref="visualizarResultadoModal" />
    </AuthenticatedLayout>
</template>
