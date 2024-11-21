<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import Navbar from "../Navbar.vue";
import {Head, Link} from "@inertiajs/vue3";
import ModelSearchFormAllColumns from "@/Components/ModelSearchFormAllColumns.vue";
import Table from "@/Components/Table.vue";
import NavButton from "@/Components/NavButton.vue";
import {dateTimeFormat} from "@/Utils/DateTimeUtils.js";
import ModalFormRelatorio from "./ModalFormRelatorio.vue";
import {IconEye, IconFileTypePdf, IconPencil, IconTrash} from "@tabler/icons-vue";
import {ref} from "vue";
import ModalVisualizarRelatorio from "./ModalVisualizarRelatorio.vue";
import LinkConfirmation from "@/Components/LinkConfirmation.vue";

const props = defineProps({
    contrato: {type: Object},
    servico: {type: Object},
    relatorios: {type: Object},
    resultados: {type: Array}

});

const modalFormRelatorio = ref({});
const modalVisualizarRelatorio = ref({});

const abrirModalRelatorio = (item) => {
    modalFormRelatorio.value.abrirModal(item);
}

const abrirVisualizarRelatorio = (item) => {
    modalVisualizarRelatorio.value.abrirModal(item);
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
            </div>
        </template>

        <Navbar :contrato="contrato" :servico="servico">
            <template #body>
                <ModelSearchFormAllColumns :columns="['nome_id', 'intensidade', 'lote.nome_id', 'lote.empresa']">
                    <template #action>
                        <NavButton @click="abrirModalRelatorio()" type-button="success"
                                   title="Novo relatório"/>
                    </template>
                </ModelSearchFormAllColumns>
                <Table
                    :columns="['ID relatório', 'Nome relatório', 'Nome resultado', 'Data relatório', 'Status', 'Ação']"
                    :records="relatorios" table-class="table-hover">
                    <template #body="{ item }">
                        <tr>
                            <td>{{ item.id }}</td>
                            <td>{{ item.nome_relatorio }}</td>
                            <td>{{ item.resultado?.nome }}</td>
                            <td>{{ dateTimeFormat(item.created_at) }}</td>
                            <td>
                                <span v-if="item.fk_status === 1"
                                      class="badge bg-primary text-white">Em confecção</span>
                                <span v-if="item.fk_status === 2" class="badge bg-warning text-white">Em análise</span>
                                <span v-if="item.fk_status === 3" class="badge bg-primary text-white">Aprovado</span>
                                <span v-if="item.fk_status === 4" class="badge bg-danger text-white">Pendente</span>
                            </td>
                            <td>
                                <NavButton @click="abrirVisualizarRelatorio(item)" class="btn-icon"
                                           type-button="info"
                                           :icon="IconEye"/>
                                <a target="_blank"
                                   :href="route('contratos.contratada.servicos.cont_ocorrencia.relatorio.gerar_pdf', { contrato: contrato.id, servico: servico.id, relatorio: item.id })"
                                   class="btn btn-icon btn-info me-1">
                                    <IconFileTypePdf/>
                                </a>
                                <NavButton @click="abrirModalRelatorio(item)" class="btn-icon"
                                           type-button="primary"
                                           :icon="IconPencil"/>
                                <LinkConfirmation v-slot="confirmation"
                                                  :options="{ text: 'A remoção da campanha será permanente.' }">
                                    <Link :onBefore="confirmation.show"
                                          :href="route('contratos.contratada.servicos.cont_ocorrencia.relatorio.delete', { contrato: contrato.id, servico: servico.id, relatorio: item.id })"
                                          as="button" method="delete" type="button" class="btn btn-icon btn-danger">
                                        <IconTrash/>
                                    </Link>
                                </LinkConfirmation>
                            </td>
                        </tr>
                    </template>
                </Table>
            </template>
        </Navbar>

        <ModalFormRelatorio :contrato="contrato" :servico="servico" :resultados="resultados" ref="modalFormRelatorio"/>
        <ModalVisualizarRelatorio :contrato="contrato" :servico="servico"
                                  ref="modalVisualizarRelatorio"/>

    </AuthenticatedLayout>
</template>
