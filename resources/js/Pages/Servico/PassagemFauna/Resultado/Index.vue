<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import Navbar from "../Navbar.vue";
import {Head, Link} from "@inertiajs/vue3";
import ModelSearchFormAllColumns from "@/Components/ModelSearchFormAllColumns.vue";
import Table from "@/Components/Table.vue";
import {dateTimeFormat} from "@/Utils/DateTimeUtils.js";
import NavButton from "@/Components/NavButton.vue";
import ModalFormResultado from "./ModalFormResultado.vue";
import {ref} from "vue";
import {IconFileTypePdf, IconPencil, IconTrash} from "@tabler/icons-vue";
import LinkConfirmation from "@/Components/LinkConfirmation.vue";
import NavLink from "@/Components/NavLink.vue";

const modalFormResultado = ref({});
const props = defineProps({
    contrato: {type: Object},
    servico: {type: Object},
    resultados: {type: Object},
    campanhas: {type: Array}
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
                        <NavButton @click="abrirModalFormResultado()" title="Novo Resultado" type-button="success"/>
                    </template>
                </ModelSearchFormAllColumns>
                <Table
                    :columns="['ID resultado', 'Nome do resultado', 'Campanhas', 'Data', 'Ação']"
                    :records="resultados" table-class="table-hover">
                    <template #body="{ item }">
                        <tr>
                            <td>{{ item.id }}</td>
                            <td>{{ item.nome }}</td>
                            <td>
                                <span v-for="campanha in item.campanhas" :key="campanha.id"
                                      class="badge bg-warning text-white m-1">
                                    {{ campanha.id }}
                                </span>
                            </td>
                            <td>{{ dateTimeFormat(item.created_at) }}</td>
                            <td>
                                <NavButton @click="abrirModalFormResultado(item)" class="btn-icon" :icon="IconPencil"
                                           type-button="primary"/>
                                <NavLink class="btn btn-icon btn-info me-1" :icon="IconFileTypePdf"
                                         route-name="contratos.contratada.servicos.passagem_fauna.resultado.resultado"
                                         :param="{contrato:contrato.id, servico:servico.id, resultado:item.id}"/>
                                <LinkConfirmation v-slot="confirmation"
                                                  :options="{ text: 'A remoção da campanha será permanente.' }">
                                    <Link :onBefore="confirmation.show"
                                          :href="route('contratos.contratada.servicos.passagem_fauna.resultado.delete', { contrato: contrato.id, servico: servico.id, resultado: item.id })"
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

        <ModalFormResultado :contrato="contrato" :servico="servico" :campanhas="campanhas" ref="modalFormResultado"/>

    </AuthenticatedLayout>
</template>
