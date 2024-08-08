<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import Navbar from "../../Navbar.vue";
import ModelSearchFormAllColumns from "@/Components/ModelSearchFormAllColumns.vue";
import Table from "@/Components/Table.vue";
import NavButton from "@/Components/NavButton.vue";
import {Head, Link, useForm} from "@inertiajs/vue3";
import ModalVincularPonto from "./ModalVincularPonto.vue";
import ModalVisualizarPonto from "./ModalVisualizarPonto.vue";
import {ref} from "vue";
import LinkConfirmation from "@/Components/LinkConfirmation.vue";
import {IconTrash} from "@tabler/icons-vue";
import {IconEye} from "@tabler/icons-vue";
import {IconPencil} from "@tabler/icons-vue";

const modalVincularPonto = ref({});
const modalVisualizarPonto = ref({});

const props = defineProps({
    vinculacoes: {type: Object},
    contrato: {type: Object},
    servico: {type: Object},
    listas: {type: Array},
    pontos: {type: Array},
    aprovacao: {type: Object}
});

const abrirModalVincularPonto = (item) => {
    modalVincularPonto.value.abrirModal(item);
}

const abrirModalVisualizarPonto = (item) => {
    modalVisualizarPonto.value.abrirModal(item);
}

const ap = (ap) => {
    if (!ap?.fk_status) {
        return true;
    }
    return ap?.fk_status === 2;
}

const form = useForm({
    id: null,
    fk_status: null
});
const enviaFiscal = (aprovacao) => {
    form.fk_status = 1;
    form.id = aprovacao?.id;
    form.post(route('contratos.contratada.servicos.pmqa.configuracao.envia-fiscal', {
        contrato: props.contrato.id,
        servico: props.servico.id
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
          { route: '#', label: contrato.contratada }]"/>
                <Link class="btn btn-dark"
                      :href="route('contratos.contratada.servicos.index', { contrato: props.contrato.id })">
                    Voltar
                </Link>
            </div>
        </template>

        <Navbar :contrato="contrato" :servico="servico">
            <template #body>
                <!-- Pesquisa-->
                <ModelSearchFormAllColumns :columns="['nome']">
                    <template #action>
                        <NavButton type-button="primary" title="Enviar ao fiscal" v-if="ap(aprovacao)"
                                   @click="enviaFiscal(aprovacao)"/>
                        <NavButton @click="abrirModalVincularPonto()" type-button="success" title="Vincular"
                                   v-if="ap(aprovacao)"/>
                    </template>
                </ModelSearchFormAllColumns>

                <!-- Listagem-->
                <Table :columns="['Nome da lista', 'Qtd. pontos', 'Ação']" :records="vinculacoes"
                       table-class="table-hover">
                    <template #body="{ item }">
                        <tr>
                            <td>{{ item.nome }}</td>
                            <td class="text-center">{{ item.pontos.length }}</td>
                            <td class="text-center">
                                <NavButton :icon="IconEye" class="btn-icon" type-button="info"
                                           @click="abrirModalVisualizarPonto(item)"/>
                                <NavButton :icon="IconPencil" class="btn-icon" type-button="primary"
                                           v-if="ap(aprovacao)"
                                           @click="abrirModalVincularPonto(item)"/>
                                <LinkConfirmation v-slot="confirmation"
                                                  :options="{ text: 'A remoção de um ponto será permanente.' }"
                                                  v-if="ap(aprovacao)">
                                    <Link :onBefore="confirmation.show"
                                          :href="route('contratos.contratada.servicos.pmqa.configuracao.vinculacao_ponto.destroy', { contrato: contrato.id, servico: servico.id, lista: item.id })"
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

        <ModalVincularPonto ref="modalVincularPonto" :listas="listas" :pontos="pontos" :contrato="contrato"
                            :servico="servico"/>
        <ModalVisualizarPonto ref="modalVisualizarPonto"/>

    </AuthenticatedLayout>
</template>
