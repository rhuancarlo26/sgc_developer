<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import Navbar from "../../Navbar.vue";
import {Head, Link} from "@inertiajs/vue3";
import ModelSearchFormAllColumns from "@/Components/ModelSearchFormAllColumns.vue";
import Table from "@/Components/Table.vue";
import NavButton from "@/Components/NavButton.vue";
import ModalImportarPonto from "./ModalImportarPonto.vue";
import ModalVisualizarPonto from "./ModalVisualizarPonto.vue";
import {ref} from "vue";
import {IconEye} from "@tabler/icons-vue";
import {IconPencil} from "@tabler/icons-vue";
import {IconTrash} from "@tabler/icons-vue";
import NavLink from "@/Components/NavLink.vue";
import LinkConfirmation from "@/Components/LinkConfirmation.vue";

const modalImportarPonto = ref({});
const modalVisualizarPonto = ref({});

const props = defineProps({
    contrato: {type: Object},
    servico: {type: Object},
    pontos: {type: Object},
    aprovacao: {type: Object}
});

const abrirModalImportar = () => {
    modalImportarPonto.value.abrirModal();
}

const abrirModalVisualizar = (item) => {
    modalVisualizarPonto.value.abrirModal(item);
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
          { route: '#', label: contrato.contratada }        ]
          "/>
                <Link class="btn btn-dark"
                      :href="route('contratos.contratada.servicos.index', { contrato: props.contrato.id })">
                    Voltar
                </Link>
            </div>
        </template>

        <Navbar :contrato="contrato" :servico="servico">
            <template #body>
                <ModelSearchFormAllColumns
                    :columns="['id', 'nomepontocoleta', 'lat_x', 'long_y', 'classificacao', 'classe', 'tipoambiente', 'uf', 'municipio', 'baciahidrografica', 'km_rodovia', 'estaca']">
                    <template #action>
                        <a class="btn btn-info me-1" target="_blank" v-if="ap(aprovacao)"
                           :href="route('contratos.contratada.servicos.pmqa.configuracao.ponto.download_modelo')">Modelo</a>
                        <NavButton @click="abrirModalImportar()"
                                   route-name="contratos.contratada.servicos.pmqa.configuracao.ponto.importar"
                                   :param="{ contrato: props.contrato.id, servico: props.servico.id }"
                                   type-button="success" v-if="ap(aprovacao)"
                                   title="Importar"/>
                    </template>
                </ModelSearchFormAllColumns>

                <Table
                    :columns="['Cod. ponto', 'Pt. coleta', 'Latitude', 'Longitude', 'Classificação', 'Classe', 'Tipo de ambiente', 'UF', 'Municipio', 'Bacia hidrografica', 'Km rodovia', 'Estaca', 'Ação']"
                    :records="pontos" table-class="table-hover">
                    <template #body="{ item }">
                        <tr>
                            <td class="text-center">{{ item.id }}</td>
                            <td class="text-center">{{ item.nome_ponto_coleta }}</td>
                            <td class="text-center">{{ item.lat_x }}</td>
                            <td class="text-center">{{ item.long_y }}</td>
                            <td>{{ item.classificacao }}</td>
                            <td class="text-center">{{ item.classe }}</td>
                            <td class="text-center">{{ item.tipo_ambiente }}</td>
                            <td class="text-center">{{ item.UF }}</td>
                            <td>{{ item.municipio }}</td>
                            <td>{{ item.bacia_hidrografica }}</td>
                            <td class="text-center">{{ item.km_rodovia }}</td>
                            <td class="text-center">{{ item.estaca }}</td>
                            <td class="text-center">
                                <NavButton @click="abrirModalVisualizar(item)" type-button="info" class="btn-icon"
                                           :icon="IconEye"/>
                                <NavLink class="btn btn-icon btn-primary me-1" v-if="ap(aprovacao)"
                                         route-name="contratos.contratada.servicos.pmqa.configuracao.ponto.create"
                                         :param="{ contrato: contrato.id, servico: servico.id, ponto: item.id }"
                                         :icon="IconPencil"/>
                                <LinkConfirmation v-slot="confirmation" v-if="ap(aprovacao)"
                                                  :options="{ text: 'A remoção de um ponto será permanente.' }">
                                    <Link :onBefore="confirmation.show"
                                          :href="route('contratos.contratada.servicos.pmqa.configuracao.ponto.delete', { contrato: contrato.id, servico: servico.id, ponto: item.id })"
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

        <ModalImportarPonto :contrato="contrato" :servico="servico" ref="modalImportarPonto"/>
        <ModalVisualizarPonto ref="modalVisualizarPonto"/>
    </AuthenticatedLayout>
</template>
