<script setup>
import {Head, Link} from "@inertiajs/vue3";
import Table from "@/Components/Table.vue";
import Navbar from "../Components/Navbar.vue";
import NavButton from "@/Components/NavButton.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import {computed, ref} from "vue";
import {IconDots} from "@tabler/icons-vue";
import ModalCadastro from "./Components/ModalCadastro.vue";
import ModalVisualizar from "./Components/ModalVisualizar.vue";
import ModalConclusao from "./Components/ModalConclusao.vue";
import ModalRelatorio from "./Components/ModalRelatorio.vue";

const props = defineProps({
    data: {type: Object},
    contrato: {type: Object},
    servico: {type: Object},
    resultados: {type: Array},
});

const modalCadastroRef = ref();
const abrirModalCadastro = (item = null) => {
    modalCadastroRef.value.abrirModal(item);
}

const modalConclusaoRef = ref();
const abrirModalConclusao = (item = null) => {
    modalConclusaoRef.value.abrirModal(item);
}

const modalRelatorioRef = ref();
const abrirModalRelatorio = (item = null) => {
    modalRelatorioRef.value.abrirModal(item);
}

const modalVisualizarRef = ref();
const abrirModalVisualizar = (item) => {
    modalVisualizarRef.value.abrirModal(item);
}

const urlQueryParams = computed(() => {
    const params = new URLSearchParams(window.location.search);
    const result = {};
    for (const [key, value] of params.entries()) {
        result[key] = value;
    }
    return result;
})

</script>

<template>

    <Head title="Destinação"/>

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
                <div class="ms-auto mb-4">

                    <NavButton @click="abrirModalCadastro(null)"
                               :param="{ contrato: props.contrato.id, servico: props.servico.id }" type-button="success"
                               title="Novo relatório" />
                </div>

                <Table
                    :columns="['Nome', 'Status', 'Sobre o relatório', 'Ações']"
                    :records="data" table-class="table-hover">
                    <template #body="{ item }">
                        <tr>
                            <td>{{item.nome_relatorio}}</td>
                            <td>
                                <span v-if="item.fk_status === 1" class="badge text-bg-primary">Em confecção</span>
                                <span v-if="item.fk_status === 2" class="badge text-bg-warning">Em análise</span>
                                <span v-if="item.fk_status === 3" class="badge text-bg-primary">Aprovado</span>
                                <span v-if="item.fk_status === 4" class="badge text-bg-danger">Pendente</span>
                            </td>
                            <td>{{item.sobre_relatorio}}</td>
                            <td class="text-center">
                                <button class="btn dropdown-toggle align-text-top" data-bs-boundary="viewport"
                                        data-bs-toggle="dropdown"
                                        aria-expanded="false">
                                    <IconDots/>
                                </button>
                                <div class="dropdown-menu dropdown-menu-end" style="">
                                    <a class="dropdown-item" href="javascript:void(0);" @click="abrirModalConclusao(item)"
                                       v-if="item.fk_status === 1 || item.fk_status === 4">
                                        Conclusão
                                    </a>
                                    <a class="dropdown-item" @click="abrirModalRelatorio(item)" href="javascript:void(0);" data-toggle="modal"
                                       data-target="#visualizarRelatorio">
                                        Visualizar relatório
                                    </a>
                                    <a class="dropdown-item" href="javascript:void(0);" @click="novoRelatorio(r, item.fk_resultado)"
                                       v-if="item.fk_status === 1 || item.fk_status === 4">
                                        Editar
                                    </a>
                                    <a class="dropdown-item" href="javascript:void(0);" @click="deleteRelatorio(item.id)"
                                       v-if="item.fk_status === 1 || item.fk_status === 4">
                                        Excluir
                                    </a>
                                    <a @click="enviarRelatorioFiscal(item)" v-if="item.fk_status === 1 || item.fk_status === 4"
                                       class="dropdown-item" href="#javascript:void(0);">
                                        Enviar para o fiscal
                                    </a>
                                    <a class=" dropdown-item" @click="gerarRelatorio(item)" href="javascript:void(0);">
                                        Exportar relatório
                                    </a>
                                </div>

                            </td>
                        </tr>
                    </template>
                </Table>
            </template>
        </Navbar>
        <ModalVisualizar ref="modalVisualizarRef"/>
        <ModalCadastro
            ref="modalCadastroRef"
            :servico="servico"
            :resultados="resultados"
            :relatorios="data.data"
        />
        <ModalConclusao
            ref="modalConclusaoRef"
        />
        <ModalRelatorio
            ref="modalRelatorioRef"
            :contrato="contrato"
        />
    </AuthenticatedLayout>

</template>
