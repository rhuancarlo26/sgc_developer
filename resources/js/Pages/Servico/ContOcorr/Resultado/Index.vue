<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import Navbar from "../Navbar.vue";
import {Head, Link} from "@inertiajs/vue3";
import ModelSearchFormAllColumns from "@/Components/ModelSearchFormAllColumns.vue";
import Table from "@/Components/Table.vue";
import {dateTimeFormat} from "@/Utils/DateTimeUtils.js";
import ModalFormResultado from "./ModalFormResultado.vue";
import {ref} from "vue";
import NavButton from "@/Components/NavButton.vue";
import {IconPencil, IconTrash, IconEye} from "@tabler/icons-vue";
import LinkConfirmation from "@/Components/LinkConfirmation.vue";

const modalFormResultado = ref({});

const props = defineProps({
    contrato: {type: Object},
    servico: {type: Object},
    resultados: {type: Object}
});

const abrirModalFormResultado = (item) => {
    modalFormResultado.value.abrirModal(item);
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
                        <NavButton @click="abrirModalFormResultado()" type-button="success"
                                   title="Novo resultado"/>
                    </template>
                </ModelSearchFormAllColumns>
                <Table
                    :columns="['ID resultado', 'Nome resultado', 'Período', 'Data início', 'Data final', 'Ação']"
                    :records="resultados" table-class="table-hover">
                    <template #body="{ item }">
                        <tr>
                            <td>{{ item.id }}</td>
                            <td>{{ item.nome }}</td>
                            <td>{{ item.periodo }}</td>
                            <td>{{ dateTimeFormat(item.dt_inicio) }}</td>
                            <td>{{ dateTimeFormat(item.dt_final) }}</td>
                            <td>
                                <a class="btn btn-icon btn-primary me-1"
                                   :href="route('contratos.contratada.servicos.cont_ocorrencia.resultado.resultado', { contrato: contrato.id, servico: servico.id, resultado: item.id })">
                                    <IconEye/>
                                </a>
                                <NavButton @click="abrirModalFormResultado(item)" class="btn-icon" type-button="primary"
                                           :icon="IconPencil"/>
                                <LinkConfirmation v-slot="confirmation"
                                                  :options="{ text: 'A remoção do resultado será permanente.' }">
                                    <Link :onBefore="confirmation.show"
                                          :href="route('contratos.contratada.servicos.cont_ocorrencia.resultado.delete', { contrato: contrato.id, servico: servico.id, resultado: item.id })"
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

        <ModalFormResultado :contrato="contrato" :servico="servico" ref="modalFormResultado"/>

    </AuthenticatedLayout>
</template>
