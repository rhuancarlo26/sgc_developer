<script setup>
import {Head, Link, router} from "@inertiajs/vue3";
import {IconMap, IconLineDashed, IconTrash, IconEye, IconPencil} from "@tabler/icons-vue";
import Table from "@/Components/Table.vue";
import Navbar from "../../Components/Navbar.vue";
import NavButton from "@/Components/NavButton.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import LinkConfirmation from "@/Components/LinkConfirmation.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import {computed, ref} from "vue";
import {dateTimeFormat} from "@/Utils/DateTimeUtils.js";
import ModelSearchFormAllColumns from "@/Components/ModelSearchFormAllColumns.vue";
import ModalCadastro from "./Components/ModalCadastro.vue";
import ModalVisualizar from "./Components/ModalVisualizar.vue";
import ModalMapa from "./Components/ModalMapa.vue";

const props = defineProps({
    data: {type: Object},
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

const modalVisualizarRef = ref();
const abrirModalVisualizar = (item) => {
    modalVisualizarRef.value.abrirModal(item);
}

const urlQueryParams = computed(() => {
    const params = new URLSearchParams(window.location.search);
    const result = {};
    for (const [key, value] of params.entries()) {
        result[key] = value;
    }
    return result;
})

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
                <ModelSearchFormAllColumns
                    :columns="['chave', 'dt_inicial', 'dt_final', 'area_em_app', 'area_fora_app', 'area_total', 'bioma.nome', 'estagioSucessional.nome', 'licenca.numero_licenca']">
                    <template #action>
                        <a v-if="data.data?.length" class="btn btn-success me-1" :href="route('contratos.contratada.servicos.supressao-vegetacao.execucao.supressao.export', { servico: servico.id, _query: urlQueryParams })">
                            Exportar Excel
                        </a>
                        <NavButton @click="abrirModalCadastro"
                           route-name="contratos.contratada.servicos.supressao-vegetacao.execucao.supressao.store"
                           :param="{ contrato: props.contrato.id, servico: props.servico.id }" type-button="success"
                           title="Cadastrar Área de Supressão" />
                    </template>
                </ModelSearchFormAllColumns>

                <Table
                    :columns="['ID Código', 'Data Inicial', 'Data Final', 'Nº ASV', 'Bioma', 'Shapefile', 'Área em APP', 'Área Fora APP', 'Área Total', 'Ação']"
                    :records="data" table-class="table-hover">
                    <template #body="{ item }">
                        <tr>
                            <td class="text-center">{{ item.chave ?? '-' }}</td>
                            <td class="text-center">{{ item.dt_inicial ? dateTimeFormat(item.dt_inicial) : '-' }}</td>
                            <td class="text-center">{{ item.dt_final ? dateTimeFormat(item.dt_final) : '-'  }}</td>
                            <td class="text-center">{{ item.licenca?.numero_licenca ?? '-' }}</td>
                            <td class="text-center">{{ item.bioma?.nome ?? '-' }}</td>
                            <td class="text-center">
                                <div v-if="item.shapefile !== null">
                                    <NavButton @click="abrirModalMapa(item.shapefile)" type-button="info" class="btn-icon" :icon="IconMap"/>
                                </div>
                                <IconLineDashed v-else color="red" :size="40" />
                            </td>
                            <td class="text-center">{{ item.area_em_app ?? '-' }}</td>
                            <td class="text-center">{{ item.area_fora_app ?? '-' }}</td>
                            <td class="text-center">{{ item.area_total ?? '-' }}</td>
                            <td>
                                <div class="d-flex justify-content-center">
                                    <NavButton @click="abrirModalVisualizar(item)" type-button="info" class="btn-icon" :icon="IconEye" />
                                    <NavButton @click="abrirModalCadastro(item)" type-button="warning" class="btn-icon" :icon="IconPencil" />
                                    <LinkConfirmation v-slot="confirmation" :options="{ text: 'Você deseja remover o plano de supressão?' }">
                                        <Link :onBefore="confirmation.show"
                                              :href="route('contratos.contratada.servicos.supressao-vegetacao.execucao.supressao.delete', item.id)"
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
        <ModalCadastro ref="modalCadastroRef" :servico="servico" :estagios="estagios" :biomas="biomas" :licencas="licencas" />
    </AuthenticatedLayout>

</template>
