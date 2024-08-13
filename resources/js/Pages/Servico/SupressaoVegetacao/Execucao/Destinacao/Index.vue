<script setup>
import {Head, Link} from "@inertiajs/vue3";
import {IconTrash, IconEye, IconPencil} from "@tabler/icons-vue";
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
    pilhas: {type: Array},
    data: {type: Object},
    contrato: {type: Object},
    servico: {type: Object},
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

    <Head title="Destinação"/>

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
                    :columns="['chave', 'dt_envio', 'uso_da_madeira', 'destinatario', 'pilhas.chave']">
                    <template #action>
                        <a v-if="data.data?.length" class="btn btn-success me-1" :href="route('contratos.contratada.servicos.supressao-vegetacao.execucao.destinacao.export', { servico: servico.id, _query: urlQueryParams })">
                            Exportar Excel
                        </a>
                        <NavButton @click="abrirModalCadastro()"
                           route-name="contratos.contratada.servicos.supressao-vegetacao.execucao.supressao.store"
                           :param="{ contrato: props.contrato.id, servico: props.servico.id }" type-button="success"
                           title="Cadastrar Destinação" />
                    </template>
                </ModelSearchFormAllColumns>

                <Table
                    :columns="['ID Código', 'Data do Envio', 'Pilhas', 'Destinatário', 'Uso da Madeira', 'Volume (m³)', 'Observação', 'Ação']"
                    :records="data" table-class="table-hover">
                    <template #body="{ item }">
                        <tr>
                            <td class="text-center">{{ item.chave ?? '-' }}</td>
                            <td class="text-center">{{ dateTimeFormat(item.dt_envio) ?? '-' }}</td>
                            <td class="text-center">{{ item.pilhas?.map((pilha) => pilha.chave).join(', ') ?? '-' }}</td>
                            <td class="text-center">{{ item.destinatario ?? '-' }}</td>
                            <td class="text-center">{{ item.uso_da_madeira ?? '-' }}</td>
                            <td class="text-center">{{ item.pilhas_sum_volume ?? '-' }}</td>
                            <td class="text-center">{{ item.observacao ?? '-' }}</td>
                            <td>
                                <div class="d-flex justify-content-center">
                                    <NavButton @click="abrirModalVisualizar(item)" type-button="info" class="btn-icon" :icon="IconEye" />
                                    <NavButton @click="abrirModalCadastro(item)" type-button="warning" class="btn-icon" :icon="IconPencil" />
                                    <LinkConfirmation v-slot="confirmation" :options="{ text: 'Você deseja remover o plano de supressão?' }">
                                        <Link :onBefore="confirmation.show"
                                              :href="route('contratos.contratada.servicos.supressao-vegetacao.execucao.destinacao.delete', item.id)"
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
            :pilhas="pilhas"
            :servico="servico"
        />
    </AuthenticatedLayout>

</template>
