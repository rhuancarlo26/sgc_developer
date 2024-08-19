<script setup>
import {Head, Link, useForm} from "@inertiajs/vue3";
import {IconMap, IconLineDashed, IconTrash, IconEye, IconPencil} from "@tabler/icons-vue";
import Table from "@/Components/Table.vue";
import Navbar from "../../Components/Navbar.vue";
import NavButton from "@/Components/NavButton.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import LinkConfirmation from "@/Components/LinkConfirmation.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import {ref} from "vue";
import {dateTimeFormat} from "@/Utils/DateTimeUtils.js";
import ModalMapa from "./Components/ModalMapa.vue";
import ModalCadastro from "./Components/ModalCadastro.vue";
import ModalObservacao from "./Components/ModalObservacao.vue";
import ModalVisualizar from "./Components/ModalVisualizar.vue";

const props = defineProps({
    data: {type: Object},
    contrato: {type: Object},
    servico: {type: Object},
    tipos: {type: Array},
    licencas: {type: Array},
    aprovacao: {type: Object}
});

const modalCadastroRef = ref();
const abrirModalCadastro = (item = null) => {
    modalCadastroRef.value.abrirModal(item);
}

const modalMapaRef = ref();
const abrirModalMapa = (geojson) => {
    modalMapaRef.value.abrirModal(geojson);
}

const modalObservacaoRef = ref();
const abrirModalObservacao = (observacao) => {
    modalObservacaoRef.value.abrirModal(observacao);
}

const modalVisualizarRef = ref();
const abrirModalVisualizar = (item) => {
    modalVisualizarRef.value.abrirModal(item);
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
    form.post(route('contratos.contratada.servicos.supressao.configuracao.envia-fiscal', {
        contrato: props.contrato.id,
        servico: props.servico.id
    }));
}

</script>

<template>

    <Head title="Plano de supressão"/>

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
                    <NavButton type-button="primary" title="Enviar ao fiscal" v-if="ap(aprovacao)"
                               @click="enviaFiscal(aprovacao)"/>
                    <NavButton @click="abrirModalCadastro(null)"
                       route-name="contratos.contratada.servicos.pmqa.configuracao.ponto.importar"
                       :param="{ contrato: props.contrato.id, servico: props.servico.id }" type-button="success"
                       title="Cadastrar pátio de estocagem" v-if="ap(aprovacao)"/>
                </div>
                <Table
                    :columns="['ID Código', 'Data do Cadastro', 'Nº ASV', 'Tipo de Pátio', 'Shapefile Pátio', 'Observação', 'Ação']"
                    :records="data" table-class="table-hover">
                    <template #body="{ item }">
                        <tr>
                            <td class="text-center">{{ item.chave ?? '-' }}</td>
                            <td class="text-center">{{ item.created_at ? dateTimeFormat(item.created_at) : '-' }}</td>
                            <td class="text-center">{{ item.licenca?.numero_licenca ?? '-' }}</td>
                            <td class="text-center">{{ item.tipo?.nome ?? '-' }}</td>
                            <td class="text-center">
                                <div v-if="item.shapefile !== null">
                                    <NavButton @click="abrirModalMapa(item.shapefile)" type-button="info" class="btn-icon" :icon="IconMap"/>
                                </div>
                                <IconLineDashed v-else color="red" :size="40" />
                            </td>
                            <td class="text-center">
                                <div v-if="item.observacao">
                                    <NavButton @click="abrirModalObservacao(item.observacao)" type-button="info" class="btn-icon" :icon="IconEye"/>
                                </div>
                                <span v-else>-</span>
                            </td>
                            <td>
                                <div class="d-flex justify-content-center">
                                    <NavButton @click="abrirModalVisualizar(item)" type-button="info" class="btn-icon" :icon="IconEye" />
                                    <NavButton @click="abrirModalCadastro(item)" v-if="ap(aprovacao)" type-button="warning" class="btn-icon" :icon="IconPencil" />
                                    <LinkConfirmation v-slot="confirmation" v-if="ap(aprovacao)" :options="{ text: 'Você deseja remover o plano de supressão?' }">
                                        <Link :onBefore="confirmation.show"
                                              :href="route('contratos.contratada.servicos.supressao-vegetacao.configuracao.patio-estocagem.delete', item.id)"
                                              as="button" method="delete" type="button" class="btn btn-icon btn-danger">
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
        <ModalMapa ref="modalMapaRef" />
        <ModalVisualizar ref="modalVisualizarRef" />
        <ModalCadastro ref="modalCadastroRef" :servico="servico" :tipos="tipos" :licencas="licencas" />
        <ModalObservacao ref="modalObservacaoRef" />
    </AuthenticatedLayout>

</template>
