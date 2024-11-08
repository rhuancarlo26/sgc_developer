<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import Navbar from "../../Navbar.vue";
import NavButton from "@/Components/NavButton.vue";
import ModelSearchFormAllColumns from "@/Components/ModelSearchFormAllColumns.vue";
import Table from "@/Components/Table.vue";
import ModalNovoRegistro from "./ModalNovoRegistro.vue";
import ModalEditarRegistro from "./ModalEditarRegistro.vue";
import ModalExcluirRegistro from "./ModalExcluirRegistro.vue";
import ModalImportarRegistro from "./ModalImportarRegistro.vue";
import { computed, ref } from "vue";
import { IconDots } from "@tabler/icons-vue";
import { Head, Link, router } from "@inertiajs/vue3";
import LinkConfirmation from "@/Components/LinkConfirmation.vue";

const props = defineProps({
    contrato: { type: Object },
    servico: { type: Object },
    data: { type: Object },
    campanhas: { type: Array },
    licencasVigente: { type: Array },
    ufs: { type: Array },
});

const modalNovoRegistro = ref({});
const modalEditarRegistro = ref({});
const modalVisualizarRegistro = ref({});
const modalExcluirRegistro = ref({});
const modalImportarRegistroRef = ref({});

const showActionsModal = ref(true);

const abrirModalNovoRegistro = (item) => {
    showActionsModal.value = true;
    modalNovoRegistro.value.abrirModal(item);
}

const abrirModalVisualizarRegistro = (item) => {
    showActionsModal.value = false;
    modalNovoRegistro.value.abrirModal(item);
}

const abrirModalImportarRegistro = () => {
    modalImportarRegistroRef.value.abrirModal();
}

const linkConfirmationRef = ref()
const abrirModalExcluirRegistro = (id) => {
    linkConfirmationRef.value.show(() => {
        router.delete(route('contratos.contratada.servicos.mon_atp_fauna.execucao.registros.delete', id))
    })
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
                    <template #action>
                        <a v-if="data.data?.length" class="btn btn-success me-1"
                            :href="route('contratos.contratada.servicos.mon_atp_fauna.execucao.registros.export', { servico: servico.id, _query: urlQueryParams })">
                            Exportar Excel
                        </a>
                        <NavButton @click="abrirModalImportarRegistro()" type-button="info" title="Importar Registro" />
                        <NavButton @click="abrirModalNovoRegistro()" type-button="info" title="Novo Registro" />
                    </template>
                </ModelSearchFormAllColumns>

                <Table
                    :columns="['Nome de registro', 'Nº Campanha', 'BR', 'UF', 'KM', 'Grupo Amostrado', 'Espécie', 'Data Registro', 'Ação']"
                    :records="data" table-class="table-hover">
                    <template #body="{ item }">
                        <tr>
                            <td>{{ item.nome_registro }}</td>
                            <td>{{ item.fk_campanha }}</td>
                            <td>{{ item.rodovia }}</td>
                            <td>{{ item.nome_estado }}</td>
                            <td>{{ item.km }}</td>
                            <td>{{ item.nome_grupo_amostrado }}</td>
                            <td>{{ item.especie }}</td>
                            <td>{{ item.data_registroF }}</td>
                            <td>
                                <button type="button" class="btn btn-icon btn-info dropdown-toggle p-2"
                                    data-bs-boundary="viewport" data-bs-toggle="dropdown" aria-expanded="false">
                                    <IconDots />
                                </button>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <a href="#" class="dropdown-item" @click.prevent="abrirModalNovoRegistro(item)">
                                        Editar
                                    </a>
                                    <a href="#" class="dropdown-item"
                                        @click.prevent="abrirModalVisualizarRegistro(item)">
                                        Visualizar
                                    </a>
                                    <a href="#" class="dropdown-item"
                                        @click.prevent="abrirModalExcluirRegistro(item.id)">
                                        Excluir
                                    </a>
                                </div>
                            </td>
                        </tr>
                    </template>
                </Table>
            </template>
        </Navbar>

        <ModalNovoRegistro ref="modalNovoRegistro" :licencasVigente="licencasVigente" :campanhas="campanhas" :ufs="ufs"
            :servico="servico" :show-action="showActionsModal" />
        <ModalEditarRegistro ref="modalEditarRegistro" />
        <ModalExcluirRegistro ref="modalExcluirRegistro" />
        <ModalImportarRegistro :campanhas="campanhas" :servico="servico" ref="modalImportarRegistroRef" />
        <LinkConfirmation ref="linkConfirmationRef"
            :options="{ text: 'Você tem certeza que deseja excluir esta foto?' }" />

    </AuthenticatedLayout>
</template>
