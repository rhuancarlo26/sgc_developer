<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import Navbar from "../../Navbar.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import ModelSearchFormAllColumns from "@/Components/ModelSearchFormAllColumns.vue";
import Table from "@/Components/Table.vue";
import NavButton from "@/Components/NavButton.vue";
import { dateTimeFormat } from "@/Utils/DateTimeUtils.js";
import ModalImportarPassagem from "./ModalImportarPassagem.vue";
import { ref } from "vue";
import { IconEye, IconPencil, IconTrash } from "@tabler/icons-vue";
import ModalVisualizarPassagemFauna from "./ModalVisualizarPassagemFauna.vue";
import LinkConfirmation from "@/Components/LinkConfirmation.vue";
import ModalForm from "./ModalForm.vue";

const modalImportarPassagem = ref({});
const modalVisualizarPassagemFauna = ref({});
const modalForm = ref({});
const props = defineProps({
    contrato: { type: Object },
    servico: { type: Object },
    passagens: { type: Object }
});

const abrirModalImportarPassagem = () => {
    modalImportarPassagem.value.abrirModal();
}

const abrirModalVisualizarPassagemFauna = (item) => {
    modalVisualizarPassagemFauna.value.abrirModal(item)
}

const abrirModalForm = (item) => {
    modalForm.value.abrirModal(item)
}

const submeterFiscal = () => {
    router.post(route('contratos.contratada.servicos.passagem_fauna.configuracao.passagem_fauna.submeter_fiscal', {
        contrato: props.contrato.id,
        servico: props.servico.id
    }));
}

</script>
<template>

    <Head :title="`${contrato.contratada.slice(0, 10)}...`" />

    <AuthenticatedLayout>

        <template #header>
            <div class="w-100 d-flex justify-content-between">
                <Breadcrumb class="align-self-center" :links="[
                    { route: route('contratos.gestao.listagem', contrato.tipo_contrato), label: `Gestão de Contratos` },
                    { route: '#', label: contrato.contratada }
                ]
                    " />
                <Link class="btn btn-dark"
                    :href="route('contratos.contratada.servicos.index', { contrato: props.contrato.id })">
                Voltar
                </Link>
            </div>
        </template>

        <Navbar :contrato="contrato" :servico="servico">
            <template #body>
                <ModelSearchFormAllColumns :columns="[]">
                    <template #action
                        v-if="!servico.parecer_passagem_fauna || ![1, 3].includes(servico.parecer_passagem_fauna.fk_status)">
                        <NavButton @click="submeterFiscal()" type-button="primary" title="Submeter ao fiscal" />
                        <a class="btn btn-info me-1" target="_blank"
                            :href="route('contratos.contratada.servicos.passagem_fauna.configuracao.passagem_fauna.download_modelo', { contrato: contrato.id, servico: servico.id })">Modelo</a>
                        <NavButton @click="abrirModalImportarPassagem()" class="nav-item" type-button="success"
                            title="Importar" />
                    </template>
                </ModelSearchFormAllColumns>
                <Table
                    :columns="['ID passagem', 'Rodovia', 'KM', 'UF', 'Tipo', 'Classificação', 'Dimensões', 'Nome', 'Observações', 'Data cadastro', 'Ação']"
                    :records="passagens" table-class="table-hover">
                    <template #body="{ item }">
                        <tr>
                            <td>{{ item.nome_id }}</td>
                            <td>{{ item.rodovia }}</td>
                            <td>{{ item.km }}</td>
                            <td>{{ item.uf }}</td>
                            <td>{{ item.tipo_de_estrutura }}</td>
                            <td>{{ item.classificacao }}</td>
                            <td>{{ item.dimensoes }}</td>
                            <td>{{ item.nome }}</td>
                            <td>{{ item.observacao }}</td>
                            <td>{{ dateTimeFormat(item.created_at) }}</td>
                            <td>
                                <NavButton @click="abrirModalVisualizarPassagemFauna(item)" :icon="IconEye"
                                    class="btn-icon" type-button="info" />
                                <span
                                    v-if="!servico.parecer_passagem_fauna || ![1, 3].includes(servico.parecer_passagem_fauna.fk_status)">
                                    <NavButton @click="abrirModalForm(item)" :icon="IconPencil" class="btn-icon"
                                        type-button="primary" />
                                    <LinkConfirmation v-slot="confirmation"
                                        :options="{ text: 'A remoção da passagem de fauna será permanente.' }">
                                        <Link :onBefore="confirmation.show"
                                            :href="route('contratos.contratada.servicos.passagem_fauna.configuracao.passagem_fauna.delete', { contrato: contrato.id, servico: servico.id, passagem_fauna: item.id })"
                                            as="button" method="delete" type="button" class="btn btn-icon btn-danger">
                                        <IconTrash />
                                        </Link>
                                    </LinkConfirmation>
                                </span>
                            </td>
                        </tr>
                    </template>
                </Table>
            </template>
        </Navbar>

        <ModalImportarPassagem :contrato="contrato" :servico="servico" ref="modalImportarPassagem" />
        <ModalVisualizarPassagemFauna ref="modalVisualizarPassagemFauna" />
        <ModalForm :contrato="contrato" :servico="servico" ref="modalForm" />

    </AuthenticatedLayout>
</template>
