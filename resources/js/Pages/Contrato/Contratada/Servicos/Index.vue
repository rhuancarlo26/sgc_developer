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
                <ModelSearchForm :search-columns="{}"/>

                <!-- Listagem-->
                <Table :columns="['', 'Tema', 'Serviço', 'Especificação', 'Licença', 'Status', 'Ação']"
                       :records="servicos"
                       table-class="table-hover">
                    <template #body="{ item }">
                        <tr>
                            <td>{{ item.id }}</td>
                            <td>{{ item.tema?.nome_tema }}</td>
                            <td>{{ item.tipo?.nome }}</td>
                            <td>{{ item.especificacao }}</td>
                            <td>
                                <span @click="abrirModalLicenca(item)" v-if="item.condicionantes.length">
                                    {{ `${item.condicionantes[0]?.licenca?.numero_licenca ?? ''}` }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span v-if="item.status_aprovacao === 1" class="badge bg-azure-lt">
                                    Em confecção
                                </span>
                                <span v-else-if="item.status_aprovacao === 2" class="badge bg-yellow-lt">
                                   Em análise
                                </span>
                                <span v-else-if="item.status_aprovacao === 3" class="badge bg-blue-lt">
                                   Aprovado
                                </span>
                                <span v-else-if="item.status_aprovacao === 4" class="badge bg-red-lt">
                                   Pendente
                                </span>
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-icon btn-info dropdown-toggle p-2"
                                        data-bs-boundary="viewport" data-bs-toggle="dropdown" aria-expanded="false">
                                    <IconDots/>
                                </button>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <a @click="abrirModalServico(item)" class="dropdown-item" href="javascript:void(0)">
                                        Visualizar
                                    </a>
                                    <a v-if="item.servico === 1 && item.status_aprovacao === 3" class="dropdown-item"
                                       :href="route('contratos.contratada.servicos.pmqa.configuracao.ponto.index', { contrato: contrato.id, servico: item.id })">
                                        Gerenciar
                                    </a>
                                    <a v-if="item.servico === 6  && item.status_aprovacao === 3" class="dropdown-item"
                                       :href="route('contratos.contratada.servicos.supressao-vegetacao.configuracao.vincular-asv.index', { contrato: contrato.id, servico: item.id })">
                                        Gerenciar
                                    </a>
                                    <a v-if="item.servico_tipo_id === 2" class="dropdown-item"
                                        :href="route('contratos.contratada.servicos.afugentamento.resgate.fauna.configuracao.vincular.asv.index', { contrato: contrato.id, servico: item.id })">
                                        Gerenciar
                                    </a>
                                    <a class="dropdown-item"
                                       :href="route('contratos.contratada.servicos.create', { contrato: contrato.id, servico: item.id })">
                                        Editar
                                    </a>
                                    <a @click="deleteServico(item.id)" class="dropdown-item" href="javascript:void(0)"
                                       v-if="item.status_aprovacao === 1">
                                        Excluir
                                    </a>
                                    <a @click="enviaFiscal(item.id)" class="dropdown-item" href="javascript:void(0)"
                                       v-if="item.status_aprovacao === 4">
                                        Parecer
                                    </a>
                                    <a @click="enviaFiscal(item.id)" class="dropdown-item" href="javascript:void(0)"
                                       v-if="item.status_aprovacao === 1 || item.status_aprovacao === 4">
                                        Enviar para o fiscal
                                    </a>
                                </div>
                            </td>
                        </tr>
                    </template>
                </Table>
            </template>
        </Navbar>

        <ModalVisualizarLicenca ref="modalVisualizarLicenca"/>
        <ModalVisualizarServico ref="modalVisualizarServico"/>

    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import Table from "@/Components/Table.vue";
import ModelSearchForm from "@/Components/ModelSearchForm.vue";
import {Head, Link, router} from "@inertiajs/vue3";
import Navbar from "../Navbar.vue";
import {IconDots} from "@tabler/icons-vue";
import ModalVisualizarLicenca from "./ModalVisualizarLicenca.vue";
import ModalVisualizarServico from "./ModalVisualizarServico.vue";
import {ref} from "vue";

defineProps({
    contrato: Object,
    servicos: Object
});

const modalVisualizarLicenca = ref();
const modalVisualizarServico = ref();

const abrirModalLicenca = (servico) => {
    modalVisualizarLicenca.value.abrirModal(servico);
}

const abrirModalServico = (servico) => {
    modalVisualizarServico.value.abrirModal(servico);
}

const deleteServico = (servico_id) => {
    router.delete(route('contratos.contratada.servicos.delete', servico_id));
}

const enviaFiscal = (servico_id) => {
    router.post(route('contratos.contratada.servicos.envia-fiscal', servico_id));
}
</script>

