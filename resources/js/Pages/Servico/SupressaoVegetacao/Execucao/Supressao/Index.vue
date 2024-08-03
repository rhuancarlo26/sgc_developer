<script setup>
import {Head, Link} from "@inertiajs/vue3";
import {IconMap, IconLineDashed, IconTrash, IconEye, IconPencil} from "@tabler/icons-vue";
import Table from "@/Components/Table.vue";
import Navbar from "../../Components/Navbar.vue";
import NavButton from "@/Components/NavButton.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import LinkConfirmation from "@/Components/LinkConfirmation.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import {ref} from "vue";
import {dateTimeFormat} from "@/Utils/DateTimeUtils.js";
import ModelSearchFormAllColumns from "@/Components/ModelSearchFormAllColumns.vue";
import ModalCadastro from "./Components/ModalCadastro.vue";

const props = defineProps({
    contrato: {type: Object},
    servico: {type: Object},
    estagios: {type: Array},
    biomas: {type: Array},
    licencas: {type: Array},
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
</script>

<template>

    <Head title="Plano de supressão"/>

    <AuthenticatedLayout>

        <template #header>
            <div class="w-100 d-flex justify-content-between">
                <Breadcrumb class="align-self-center" :links="[
                    { route: route('contratos.gestao.listagem', contrato.tipo_id), label: `Gestão de Contratos` },
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
                <ModelSearchFormAllColumns
                    :columns="['id', 'nomepontocoleta', 'lat_x', 'long_y', 'classificacao', 'classe', 'tipoambiente', 'uf', 'municipio', 'baciahidrografica', 'km_rodovia', 'estaca']">
                    <template #action>
                        <NavButton @click="abrirModalCadastro"
                           route-name="contratos.contratada.servicos.pmqa.configuracao.ponto.importar"
                           :param="{ contrato: props.contrato.id, servico: props.servico.id }" type-button="success"
                           title="Cadastrar Área de Supressão" />
                    </template>
                </ModelSearchFormAllColumns>

            </template>
        </Navbar>
        <ModalCadastro ref="modalCadastroRef" :servico="servico" :estagios="estagios" :biomas="biomas" :licencas="licencas" />
    </AuthenticatedLayout>

</template>
