<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import Navbar from "../../Navbar.vue";
import {Head, Link} from "@inertiajs/vue3";
import ModelSearchFormAllColumns from "@/Components/ModelSearchFormAllColumns.vue";
import Table from "@/Components/Table.vue";
import NavButton from "@/Components/NavButton.vue";
import LinkConfirmation from "@/Components/LinkConfirmation.vue";
import {ref} from "vue";
import {IconEye, IconTrash} from "@tabler/icons-vue";
import {dateTimeFormat} from "@/Utils/DateTimeUtils";
import ModalGerarACA from "./ModalGerarACA.vue";
import ModalVisualizarACA from "./ModelVisualizarACA.vue";
import ModalEnviarACA from "./ModalEnviarACA.vue";

const modalEnviarACA = ref({});
const modalGerarACA = ref({});
const modalVisualizarACA = ref({});

const props = defineProps({
    contrato: {type: Object},
    servico: {type: Object},
    acas: {type: Object},
    acas_nao_enviadas: {type: Array},
    lotes: {type: Array},
    ocorrencias_aprovadas: {type: Array}
});

const abrirModalGerarACA = () => {
    modalGerarACA.value.abrirModal();
}

const abrirModalVisualizarACA = (item) => {
    modalVisualizarACA.value.abrirModal(item);
}

const abrirModalEnviarACA = () => {
    modalEnviarACA.value.abrirModal();
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
                        <NavButton @click="abrirModalEnviarACA()" type-button="primary"
                                   title="Enviar ACA"/>
                        <NavButton @click="abrirModalGerarACA()" type-button="success"
                                   title="Gerar ACA"/>
                    </template>
                </ModelSearchFormAllColumns>
                <Table
                    :columns="['ID ACA', 'Data ACA', 'Relação de RNC\'s', 'Lote', 'Contrutora', 'Envio', 'Ação']"
                    :records="acas" table-class="table-hover">
                    <template #body="{ item }">
                        <tr>
                            <td>{{ item.nome_id }}</td>
                            <td>{{ dateTimeFormat(item.data_aca) }}</td>
                            <td>
                                <span v-for="rnc in item.rncs" :key="rnc.id"
                                      class="badge bg-warning text-white m-1">
                                    {{ rnc.nome_id }}
                                </span>
                            </td>
                            <td>{{ item.lote?.nome_id }}</td>
                            <td>{{ item.lote?.empresa }}</td>
                            <td>{{ item.enviado }}</td>
                            <td>
                                <NavButton @click="abrirModalVisualizarACA(item)" type-button="primary" class="btn-icon"
                                           :icon="IconEye"/>
                                <LinkConfirmation v-slot="confirmation"
                                                  :options="{ text: 'A remoção da campanha será permanente.' }">
                                    <Link :onBefore="confirmation.show"
                                          :href="route('contratos.contratada.servicos.cont_ocorrencia.execucao.controle_rnc.delete', { contrato: contrato.id, servico: servico.id, aca: item.id })"
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

        <ModalGerarACA :contrato="contrato" :servico="servico" :ocorrencias="ocorrencias_aprovadas" :lotes="lotes"
                       ref="modalGerarACA"/>
        <ModalVisualizarACA ref="modalVisualizarACA"/>
        <ModalEnviarACA :contrato="contrato" :servico="servico" :acas="acas_nao_enviadas" ref="modalEnviarACA"/>

    </AuthenticatedLayout>
</template>
