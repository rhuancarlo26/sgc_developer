<script setup>
import {Head, Link} from "@inertiajs/vue3";
import {IconMap, IconLineDashed, IconTrash, IconFile} from "@tabler/icons-vue";
import Table from "@/Components/Table.vue";
import Navbar from "../../Components/Navbar.vue";
import NavButton from "@/Components/NavButton.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import LinkConfirmation from "@/Components/LinkConfirmation.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import {ref} from "vue";
import {dateTimeFormat} from "@/Utils/DateTimeUtils.js";
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

                <div class="ms-auto mb-4">
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
                                    <NavButton @click="abrirModalMapa(item.local_shape_em_app)" type-button="info" class="btn-icon" :icon="IconMap"/>
                                </div>
                                <IconLineDashed v-else color="red" :size="40" />
                            </td>
                            <td class="text-center">{{ item.area_fora_app ?? '-' }}</td>
                            <td class="text-center">
                                <div v-if="item.local_shape_fora_app !== null">
                                    <NavButton @click="abrirModalMapa(item.local_shape_fora_app)" type-button="info" class="btn-icon" :icon="IconMap"/>
                                </div>
                                <IconLineDashed v-else color="red" :size="40" />
                            </td>
                            <td>
                                <div class="d-flex justify-content-center">
                                    <NavButton v-if="item.arquivo === null" type-button="primary" class="btn-icon" :icon="IconFile" disabled />
                                    <a v-else target="_blank" class="btn btn-primary btn-icon me-1" :href="item.arquivo?.caminho"><IconFile /></a>
                                    <LinkConfirmation v-slot="confirmation" :options="{ text: 'Você deseja remover o plano de supressão?' }">
                                        <Link :onBefore="confirmation.show"
                                              :href="route('contratos.contratada.servicos.supressao-vegetacao.configuracao.plano-supressao.destroy', { plano: item.id })"
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
        <ModalIncluirPlano ref="modalIncluirPlanoRef" :servico="servico" />
        <ModalMapa ref="modalMapaRef" />
    </AuthenticatedLayout>

</template>
