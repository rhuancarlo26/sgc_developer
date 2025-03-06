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
import NovoRelatorio from "./Modal/NovoRelatorio.vue";
import ConclusaoRelatorio from "./Modal/ConclusaoRelatorio.vue";

const props = defineProps({
    contrato: { type: Object },
    servico: { type: Object },
    relatorio: { type: Array },
});

const novoRelatorioModal = ref();

const abrirModalNovoRelatorio = (contrato, servico) => {
    novoRelatorioModal.value.abrirModal(contrato, servico);
}

const abrirModalEditarRelatorio = (rel, tipo, contrato, servico) => {
    novoRelatorioModal.value.updateModal(rel, tipo, contrato, servico);
}

const conclusaoRelatorioModal = ref();

const abrirModalConclusaoRelatorio = (rel, tipo, contrato, servico) => {
    conclusaoRelatorioModal.value.abrirModal(rel, tipo, contrato, servico);
}

const destroy = (relatorio) => {
    Swal.fire({
        title: "Excluir Relatório",
        text: "Deseja continuar?",
        icon: "warning",
        showCloseButton: true,
        showCancelButton: true,
        focusConfirm: false,
    }).then((r) => {
        if (r.isConfirmed) {
            router.delete(route('contratos.contratada.servicos.afugentamento.resgate.fauna.relatorios.delete', { relatorio: relatorio }));
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
                        <a @click="abrirModalNovoRelatorio(contrato, servico)" class="btn btn-success"
                            href="javascript:void(0)">
                            Novo Relatório
                        </a>
                    </template>
                </ModelSearchFormAllColumns>

                <Table :columns="['ID Relatório', 'Nome Relatório', 'Data Relatório', 'Status', 'Ação']"
                    :records="relatorio" table-class="table-hover">
                    <template #body="{ item }">
                        <tr>
                            <td>{{ item.id }}</td>
                            <td>{{ item.nome_relatorio }}</td>
                            <td>{{ dateTimeFormat(item.created_at) }}</td>
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
                            <td>
                                <button class="btn dropdown-toggle align-text-top" data-bs-boundary="viewport"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    <IconDots />
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                    <li>
                                        <a class="dropdown-item"
                                            @click="abrirModalConclusaoRelatorio(item, 1, contrato, servico)"
                                            href="javascript:void(0);"
                                            v-if="item.fk_status === 1 || item.fk_status === 4">
                                            Conclusão
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" @click="visualizarRelatorio(i)"
                                            href="javascript:void(0);">
                                            Visualizar relatório
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item"
                                            @click="abrirModalEditarRelatorio(item, 0, contrato, servico)"
                                            href="javascript:void(0);"
                                            v-if="item.fk_status === 1 || item.fk_status === 4">
                                            Editar
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" @click="destroy(item.id)" href="javascript:void(0);"
                                            v-if="item.fk_status === 1 || item.fk_status === 4">
                                            Excluir
                                        </a>
                                    </li>

                                    <li>
                                        <a class="dropdown-item" href="javascript:void(0);"
                                            @click="enviarRelatorioFiscal(i)"
                                            v-if="item.fk_status === 1 || item.fk_status === 4">
                                            Enviar para o fiscal
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" @click="gerarRelatorio(item)"
                                            href="javascript:void(0);">
                                            Exportar relatório
                                        </a>
                                    </li>
                                </ul>
                            </td>
                        </tr>
                    </template>
                </Table>
            </template>
        </Navbar>
        <NovoRelatorio ref="novoRelatorioModal" />
        <ConclusaoRelatorio ref="conclusaoRelatorioModal" />
    </AuthenticatedLayout>
</template>