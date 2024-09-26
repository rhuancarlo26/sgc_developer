<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import Navbar from "../Navbar.vue";
import {Head, Link, router} from "@inertiajs/vue3";
import ModelSearchFormAllColumns from "@/Components/ModelSearchFormAllColumns.vue";
import Table from "@/Components/Table.vue";
import NavButton from "@/Components/NavButton.vue";
import ModalFormRelatorio from "./ModalFormRelatorio.vue";
import {ref} from "vue";
import {dateTimeFormat} from "@/Utils/DateTimeUtils.js";
import {IconDots} from "@tabler/icons-vue";
import ModalVisualizarRelatorio from "./ModalVisualizarRelatorio.vue";

const modalFormRelatorio = ref({});
const modalVisualizarRelatorio = ref({});

const props = defineProps({
    contrato: {type: Object},
    servico: {type: Object},
    relatorios: {type: Object},
    resultados: {type: Object}
});

const status = ref({
    1: {label: 'Em confecção', class: 'bg-primary'},
    2: {label: 'Em análise', class: 'bg-warning'},
    3: {label: 'Aprovado', class: 'bg-primary'},
    4: {label: 'Pendente', class: 'bg-danger'}
});

const abrirModalFormRelatorio = (item) => {
    modalFormRelatorio.value.abrirModal(item);
}

const abrirModalVisualizarRelatorio = (item) => {
    modalVisualizarRelatorio.value.abrirModal(item);
}

const excluirRelatorio = (item) => {
    router.delete(route('contratos.contratada.servicos.passagem_fauna.relatorio.delete', {
        contrato: props.contrato.id, servico: props.servico.id, relatorio: item.id
    }));
}

</script>
<template>

    <Head :title="`${contrato.contratada.slice(0, 10)}...`"/>

    <AuthenticatedLayout>

        <template #header>
            <div class="w-100 d-flex justify-content-between">
                <Breadcrumb class="align-self-center" :links="[
          { route: route('contratos.gestao.listagem', contrato.tipo_contrato), label: `Gestão de Contratos` },
          { route: '#', label: contrato.contratada }
        ]
          "/>
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
                        <NavButton @click="abrirModalFormRelatorio()" title="Novo Relatório" type-button="success"/>
                    </template>
                </ModelSearchFormAllColumns>
                <Table
                    :columns="['ID', 'Nome relatório', 'Nome resultado', 'Data', 'Status', 'Ação']"
                    :records="relatorios" table-class="table-hover">
                    <template #body="{ item }">
                        <tr>
                            <td>{{ item.id }}</td>
                            <td>{{ item.nome_relatorio }}</td>
                            <td>{{ item.resultado?.nome }}</td>
                            <td>{{ dateTimeFormat(item.created_at) }}</td>
                            <td>
                                <span v-if="status[item.fk_status]"
                                      :class="['badge', 'text-white', status[item.fk_status].class]">{{
                                        status[item.fk_status].label
                                    }}</span>
                            </td>
                            <td>
                                <button type="button" class="btn btn-icon btn-info dropdown-toggle p-2"
                                        data-bs-boundary="viewport"
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                    <IconDots/>
                                </button>
                                <div class="dropdown-menu dropdown-menu-end" style="">
                                    <a @click="abrirModalVisualizarRelatorio(item)" class="dropdown-item"
                                       href="javascript:void(0)">
                                        Visualizar relátorio
                                    </a>
                                    <a @click="abrirModalFormRelatorio(item)" class="dropdown-item"
                                       href="javascript:void(0)">
                                        Editar
                                    </a>
                                    <a @click="excluirRelatorio(item)" class="dropdown-item" href="javascript:void(0)">
                                        Excluir
                                    </a>
                                    <a class="dropdown-item" href="javascript:void(0)">
                                        Enviar para o fiscal
                                    </a>
                                    <a class="dropdown-item" target="_blank"
                                       :href="route('contratos.contratada.servicos.passagem_fauna.relatorio.gerar_pdf', {contrato: contrato.id, servico: servico.id, relatorio: item.id})">
                                        Exportar relatório
                                    </a>
                                </div>
                            </td>
                        </tr>
                    </template>
                </Table>
            </template>
        </Navbar>

        <ModalFormRelatorio :contrato="contrato" :servico="servico" :resultados="resultados" ref="modalFormRelatorio"/>
        <ModalVisualizarRelatorio :contrato="contrato" :servico="servico" ref="modalVisualizarRelatorio"/>

    </AuthenticatedLayout>
</template>
