<!-- resources\js\Pages\Servico\MonAtpFauna\Execucao\Campanhas\Index.vue -->
<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import Navbar from "../../Navbar.vue";
import NavButton from "@/Components/NavButton.vue";
import NavLink from "@/Components/NavLink.vue";
import ModelSearchFormAllColumns from "@/Components/ModelSearchFormAllColumns.vue";
import Table from "@/Components/Table.vue";
import ModalNovaCampanha from "./ModalNovaCampanha.vue";
import ModalEditarCampanha from "./ModalEditarCampanha.vue";
import ModalVisualizarCampanha from "./ModalVisualizarCampanha.vue";
import ModalExcluirCampanha from "./ModalExcluirCampanha.vue";
import { ref } from "vue";
import { dateTimeFormat } from "@/Utils/DateTimeUtils";
import { IconDots } from "@tabler/icons-vue";

const props = defineProps({
    contrato: { type: Object },
    servico: { type: Object },
    vinculacoes: { type: Object },
    licencas: { type: Array }
});

const modalNovaCampanha = ref({});
const modalEditarCampanha = ref({});
const modalVisualizarCampanha = ref({});
const modalExcluirCampanha = ref({});

const abrirModalNovaCampanha = () => {
    modalNovaCampanha.value.abrirModal();
}

const abrirModalEditarCampanha = () => {
    modalEditarCampanha.value.abrirModal();
}

const abrirModalVisualizarCampanha = () => {
    modalVisualizarCampanha.value.abrirModal();
}

const abrirModalExcluirCampanha = () => {
    modalExcluirCampanha.value.abrirModal();
}
</script>

<template>

    <Head :title="`${contrato.contratada.slice(0, 10)}...`" />

    <AuthenticatedLayout>

        <template #header>
            <div class="w-100 d-flex justify-content-between">
                <Breadcrumb class="align-self-center" :links="[
        { route: route('contratos.gestao.listagem', contrato.tipo_id), label: `Gestão de Contratos` },
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
                        <NavButton @click="abrirModalNovaCampanha()" type-button="info" title="Nova Campanha" />
                    </template>
                </ModelSearchFormAllColumns>

                <Table
                    :columns="['Tipo', 'N° licença', 'Empreendimento', 'Emissor', 'Data de emissão', 'Vencimento', 'Responsável', 'Processo DNIT', 'Ação']"
                    :records="vinculacoes" table-class="table-hover">
                    <template #body="{ item }">
                        <tr>
                            <td>{{ item.licenca?.tipo?.nome }}</td>
                            <td>{{ item.licenca?.numero_licenca }}</td>
                            <td>{{ item.licenca?.empreendimento }}</td>
                            <td>{{ item.licenca?.emissor }}</td>
                            <td>{{ dateTimeFormat(item.licenca?.data_emissao) }}</td>
                            <td>{{ dateTimeFormat(item.licenca?.vencimento) }}</td>
                            <td>{{ item.licenca?.fiscal }}</td>
                            <td>{{ item.licenca?.processo_dnit }}</td>
                            <td>
                                <button type="button" class="btn btn-icon btn-info dropdown-toggle p-2"
                                    data-bs-boundary="viewport" data-bs-toggle="dropdown" aria-expanded="false">
                                    <IconDots />
                                </button>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <a href="#" class="dropdown-item" @click.prevent="abrirModalEditarCampanha">
                                        Editar
                                    </a>
                                    <a href="#" class="dropdown-item" @click.prevent="abrirModalVisualizarCampanha">
                                        Visualizar
                                    </a>
                                    <a href="#" class="dropdown-item" @click.prevent="abrirModalExcluirCampanha">
                                        Excluir
                                    </a>
                                </div>

                            </td>
                        </tr>
                    </template>
                </Table>
            </template>
        </Navbar>

        <ModalNovaCampanha ref="modalNovaCampanha" />
        <ModalEditarCampanha ref="modalEditarCampanha" />
        <ModalVisualizarCampanha ref="modalVisualizarCampanha" />
        <ModalExcluirCampanha ref="modalExcluirCampanha" />

    </AuthenticatedLayout>
</template>
