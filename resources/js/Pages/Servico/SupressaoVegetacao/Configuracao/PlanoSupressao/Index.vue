<script setup>
import {Head, Link, useForm} from "@inertiajs/vue3";
import {IconEye, IconFile, IconTrash} from "@tabler/icons-vue";
import Table from "@/Components/Table.vue";
import Navbar from "../../Components/Navbar.vue";
import NavButton from "@/Components/NavButton.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import LinkConfirmation from "@/Components/LinkConfirmation.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import {ref, watch} from "vue";
import {dateTimeFormat} from "@/Utils/DateTimeUtils.js";
import {useToast} from "vue-toastification";
import ModalIncluirPlano from "./Components/ModalIncluirPlano.vue";
import ModalMapa from "./Components/ModalMapa.vue";

const props = defineProps({
    data: {type: Object},
    contrato: {type: Object},
    servico: {type: Object},
});

const modalIncluirPlanoRef = ref();
const abrirModalIncluirPlano = () => {
    modalIncluirPlanoRef.value.abrirModal();
}

const modalMapaRef = ref();
const abrirModalMapa = (geojson) => {
    modalMapaRef.value.abrirModal(geojson);
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

                <div class="ms-auto">
                    <NavButton @click="abrirModalIncluirPlano"
                       route-name="contratos.contratada.servicos.pmqa.configuracao.ponto.importar"
                       :param="{ contrato: props.contrato.id, servico: props.servico.id }" type-button="success"
                       title="Incluir plano" />
                </div>
                <Table
                    :columns="['Código', 'Data inicial', 'Data final', 'Área APP(ha)', 'Shapefile em área de APP', 'Área fora APP(ha)', 'Shapefile em área fora de APP', 'Ação']"
                    :records="data" table-class="table-hover">
                    <template #body="{ item }">
                        <tr>
                            <td class="text-center">{{ item.chave ?? '-' }}</td>
                            <td class="text-center">{{ item.dt_inicial ? dateTimeFormat(item.dt_inicial) : '-' }}</td>
                            <td class="text-center">{{ item.dt_final ? dateTimeFormat(item.dt_inicial) : '-' }}</td>
                            <td class="text-center">{{ item.area_em_app ?? '-' }}</td>
                            <td class="text-center">
                                <div v-if="item.local_shape_em_app !== null">
                                    <NavButton @click="abrirModalMapa(item.local_shape_em_app)" type-button="info" class="btn-icon" :icon="IconEye"/>
                                </div>
                            </td>
                            <td class="text-center">{{ item.area_fora_app ?? '-' }}</td>
                            <td class="text-center">
                                <div v-if="item.local_shape_fora_app !== null">
                                    <NavButton @click="abrirModalMapa(item.local_shape_fora_app)" type-button="info" class="btn-icon" :icon="IconEye"/>
                                </div>
                            </td>
                            <td class="text-center">acao</td>
<!--                            <td>-->
<!--                                <div class="d-flex justify-content-center">-->
<!--                                    <NavButton @click="abrirModalVisualizar(item)" type-button="info" class="btn-icon" :icon="IconEye"/>-->
<!--                                    <NavButton v-if="item.documento === null" type-button="primary" class="btn-icon" :icon="IconFile" disabled />-->
<!--                                    <a v-else class="btn btn-primary btn-icon me-1" :href="item.documento.caminho"><IconFile /></a>-->
<!--                                    <LinkConfirmation v-slot="confirmation" :options="{ text: 'Você deseja remover o vínculo?' }">-->
<!--                                        <Link :onBefore="confirmation.show"-->
<!--                                              :href="route('contratos.contratada.servicos.supressao-vegetacao.configuracao.vincular-asv.delete', { contrato: contrato.id, servico: servico.id, licenca: item.id })"-->
<!--                                              as="button" method="delete" type="button" class="btn btn-icon btn-danger">-->
<!--                                            <IconTrash/>-->
<!--                                        </Link>-->
<!--                                    </LinkConfirmation>-->
<!--                                </div>-->
<!--                            </td>-->
                        </tr>
                    </template>
                </Table>
            </template>
        </Navbar>
        <ModalIncluirPlano ref="modalIncluirPlanoRef" :servico="servico" />
        <ModalMapa ref="modalMapaRef" />
    </AuthenticatedLayout>

</template>
