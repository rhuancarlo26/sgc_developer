<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import Navbar from "../../Navbar.vue";
import {Head, Link} from "@inertiajs/vue3";
import ModelSearchFormAllColumns from "@/Components/ModelSearchFormAllColumns.vue";
import Table from "@/Components/Table.vue";
import NavButton from "@/Components/NavButton.vue";
import {ref} from "vue";
import {IconPencil} from "@tabler/icons-vue";
import {IconTrash} from "@tabler/icons-vue";
import LinkConfirmation from "@/Components/LinkConfirmation.vue";
import ModalParametros from "./ModalParametros.vue";

const modalParametros = ref({});

const props = defineProps({
    contrato: {type: Object},
    servico: {type: Object},
    parametros: {type: Array},
    listas: {type: Object},
    aprovacao: {type: Object}
});

const abrirModalParametros = () => {
    modalParametros.value.abrirModal();
}

const editarLista = (item) => {
    modalParametros.value.abrirModal(item);
}

const ap = (ap) => {
    if (!ap?.fk_status) {
        return true;
    }
    return ap?.fk_status === 2;
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
                <ModelSearchFormAllColumns :columns="['nome', 'parametros.nome']" v-if="ap(aprovacao)">
                    <template #action>
                        <NavButton @click="abrirModalParametros()" type-button="success" title="Novo parâmetro"/>
                    </template>
                </ModelSearchFormAllColumns>

                <Table :columns="['Nome', 'Parâmetros', 'Ação']" :records="listas" table-class="table-hover">
                    <template #body="{ item }">
                        <tr>
                            <td>{{ item.nome }}</td>
                            <td>
                                <p v-if="item.parametros">
                                    <span v-for="(record, i) in item.parametros" :key="parametro"
                                        class="badge bg-warning text-white m-1">
                                        {{ record.parametro }}
                                    </span>
                                </p>
                            </td>
                            <td v-if="ap(aprovacao)">
                                <div class="d-flex">
                                    <NavButton :icon="IconPencil" class="btn-icon" type-button="primary"
                                               @click="editarLista(item)"/>
                                    <LinkConfirmation v-slot="confirmation"
                                                      :options="{ text: 'A remoção de um ponto será permanente.' }">
                                        <Link :onBefore="confirmation.show"
                                              :href="route('contratos.contratada.servicos.pmqa.configuracao.parametro.destroy', { contrato: contrato.id, servico: servico.id, lista: item.id })"
                                              as="button" method="delete" type="button"
                                              class="btn btn-icon btn-danger">
                                            <IconTrash/>
                                        </Link>
                                    </LinkConfirmation>
                                </div>
                            </td>
                        </tr>
                    </template>
                </Table>
            </template>
        </Navbar>

        <ModalParametros :contrato="contrato" :servico="servico" :parametros="parametros" ref="modalParametros"/>

    </AuthenticatedLayout>
</template>
