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

const props = defineProps({
    data: {type: Object},
    contrato: {type: Object},
    servico: {type: Object},
    tipos: {type: Array},
    patios: {type: Array},
    produtos: {type: Array},
    licencas: {type: Array},
    areasSuprimidas: {type: Array},
});

const modalCadastroRef = ref();
const abrirModalCadastro = (item = null) => {
    modalCadastroRef.value.abrirModal(item);
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

    <Head title="Pilhas"/>

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
                    :columns="['chave', 'created_at', 'volume', 'licenca.numero_licenca', 'produto.nome', 'corteEspecie.nome', 'corteEspecie.qtd_corte', 'areaSupressao.chave']">
                    <template #action>
                        <a class="btn btn-success me-1" :href="route('contratos.contratada.servicos.supressao-vegetacao.execucao.supressao.export', { servico: servico.id, _query: urlQueryParams })">
                            Exportar Excel
                        </a>
                        <NavButton @click="abrirModalCadastro()"
                           route-name="contratos.contratada.servicos.supressao-vegetacao.execucao.supressao.store"
                           :param="{ contrato: props.contrato.id, servico: props.servico.id }" type-button="success"
                           title="Cadastrar Pilha" />
                    </template>
                </ModelSearchFormAllColumns>

                <Table
                    :columns="['ID Código', 'ID Area de Supressão', 'Data Cadastro', 'Nº ASV', 'Tipo de Pilha', 'Tipo de produto', 'Nome Científico', 'Volume', 'Ação']"
                    :records="data" table-class="table-hover">
                    <template #body="{ item }">
                        <tr>
                            <td class="text-center">{{ item.chave ?? '-' }}</td>
                            <td class="text-center">{{ item.area_supressao?.chave ?? '-' }}</td>
                            <td class="text-center">{{ dateTimeFormat(item.created_at) }}</td>
                            <td class="text-center">{{ item.licenca?.numero_licenca ?? '-' }}</td>
                            <td class="text-center">{{ item.tipo_pilha_label ?? '-' }}</td>
                            <td class="text-center">{{ item.produto?.nome ?? '-' }}</td>
                            <td class="text-center">{{ item.corte_especie?.nome ?? '-' }}</td>
                            <td class="text-center">{{ item.volume ?? '-' }}</td>
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
        <ModalVisualizar ref="modalVisualizarRef" />
        <ModalCadastro
            ref="modalCadastroRef"
            :patios="patios"
            :tipos="tipos"
            :servico="servico"
            :produtos="produtos"
            :licencas="licencas"
            :areasSuprimidas="areasSuprimidas"
        />
    </AuthenticatedLayout>

</template>
