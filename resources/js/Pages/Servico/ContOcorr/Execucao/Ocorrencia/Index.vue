<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import Navbar from "../../Navbar.vue";
import {Head, Link, router} from "@inertiajs/vue3";
import ModelSearchFormAllColumns from "@/Components/ModelSearchFormAllColumns.vue";
import Table from "@/Components/Table.vue";
import NavButton from "@/Components/NavButton.vue";
import LinkConfirmation from "@/Components/LinkConfirmation.vue";
import {ref} from "vue";
import {IconDots, IconEye} from "@tabler/icons-vue";
import {IconList} from "@tabler/icons-vue";
import {IconPencil} from "@tabler/icons-vue";
import {IconTrash} from "@tabler/icons-vue";
import NavLink from "@/Components/NavLink.vue";
import NavLinkVoid from "@/Components/NavLinkVoid.vue";
import {IconMap} from "@tabler/icons-vue";
import {dateTimeFormat} from "@/Utils/DateTimeUtils";
import ModalVisualizarOcorrencia from "../ModalOcorrencia/ModalVisualizarOcorrencia.vue";
import ModalVisualizarOcorrenciaHistorico from "../ModalOcorrencia/ModalVisualizarOcorrenciaHistorico.vue";
import ModalEnviarOcorrencia from "./ModalEnviarOcorrencia.vue";

const modalVisualizarOcorrencia = ref({});
const modalVisualizarOcorrenciaHistorico = ref({});
const modalEnviarOcorrencia = ref({});

const props = defineProps({
    contrato: {type: Object},
    servico: {type: Object},
    ocorrencias: {type: Object},
    ocorrencias_em_aberto: {type: Array}
});

const abrirModalOcorrencia = (item) => {
    modalVisualizarOcorrencia.value.abrirModal(item)
}
const abrirModalOcorrenciaHistorico = (item) => {
    modalVisualizarOcorrenciaHistorico.value.abrirModal(item)
}
const abrirModalEnviarOcorrencia = () => {
    modalEnviarOcorrencia.value.abrirModal()
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
                <ModelSearchFormAllColumns :columns="['nome_id', 'intensidade', 'lote.nome_id', 'lote.empresa']">
                    <template #action>
                        <NavButton @click="abrirModalEnviarOcorrencia()" type-button="primary"
                                   title="Enviar ocorrência"/>
                        <NavLink route-name="contratos.contratada.servicos.cont_ocorrencia.execucao.ocorrencia.create"
                                 :param="{ contrato: props.contrato.id, servico: props.servico.id }"
                                 class="btn btn-success"
                                 title="Nova ocorrência"/>
                    </template>
                </ModelSearchFormAllColumns>
                <Table
                    :columns="['ID Ocorrência', 'Intensidade Ocorrência', 'Data da Ocorrência', 'Ocorrênia anterior', 'Prazo de correção', 'Lote', 'Construtora', 'Status Aprovação', 'Envio', 'Ação']"
                    :records="ocorrencias" table-class="table-hover">
                    <template #body="{ item }">
                        <tr>
                            <td>{{ item.nome_id }}</td>
                            <td>{{ item.intensidade }}</td>
                            <td>{{ dateTimeFormat(item.data_ocorrencia) }}</td>
                            <td>-</td>
                            <td>-</td>
                            <td>{{ item.lote?.nome_id }}</td>
                            <td>{{ item.lote?.empresa }}</td>
                            <td>{{ item.status }}</td>
                            <td>{{ item.envio_empresa }}</td>
                            <td>
                                <button type="button" class="btn btn-icon btn-info dropdown-toggle p-2"
                                        data-bs-boundary="viewport"
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                    <IconDots/>
                                </button>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <a @click="abrirModalOcorrenciaHistorico(item)" class="dropdown-item"
                                       href="javascript:void(0)">
                                        Log de atividades
                                    </a>
                                    <a @click="abrirModalOcorrencia(item)" class="dropdown-item"
                                       href="javascript:void(0)">
                                        Visualizar
                                    </a>
                                    <NavLink title="Editar" class="dropdown-item"
                                             route-name="contratos.contratada.servicos.cont_ocorrencia.execucao.ocorrencia.create"
                                             :param="{ contrato: contrato.id, servico: servico.id, ocorrencia: item.id }"/>
                                    <NavLink
                                        route-name="contratos.contratada.servicos.cont_ocorrencia.execucao.ocorrencia.delete"
                                        :param="{ contrato: contrato.id, servico: servico.id, ocorrencia: item.id }"
                                        title="Excluir"
                                        class="dropdown-item"/>
                                    <NavLink
                                        route-name="contratos.contratada.servicos.cont_ocorrencia.execucao.ocorrencia.create_vistoria"
                                        :param="{ contrato: contrato.id, servico: servico.id, ocorrencia: item.id }"
                                        title="Vistoria"
                                        class="dropdown-item"/>
                                </div>
                            </td>
                        </tr>
                    </template>
                </Table>
            </template>
        </Navbar>

        <ModalVisualizarOcorrencia ref="modalVisualizarOcorrencia"/>
        <ModalVisualizarOcorrenciaHistorico ref="modalVisualizarOcorrenciaHistorico"/>
        <ModalEnviarOcorrencia :contrato="contrato" :servico="servico" :ocorrencias="ocorrencias_em_aberto"
                               ref="modalEnviarOcorrencia"/>
        <ModalFormVistoria :contrato="contrato" :servico="servico" :ocorrencias="ocorrencias.data"
                           ref="modalFormVistoria"/>

    </AuthenticatedLayout>
</template>
