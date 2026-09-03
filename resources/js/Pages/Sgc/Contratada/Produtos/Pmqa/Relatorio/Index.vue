<script setup>
import { Head, Link, router } from "@inertiajs/vue3";
import ModelSearchFormAllColumns from "@/Components/ModelSearchFormAllColumns.vue";
import Table from "@/Components/Table.vue";
import NavButton from "@/Components/NavButton.vue";
import { ref } from "vue";
import { dateTimeFormat } from "@/Utils/DateTimeUtils";
import { IconDots } from "@tabler/icons-vue";
import ModalFormRelatorio from "./ModalFormRelatorio.vue";
import ModalVisualizarRelatorio from "./ModalVisualizarRelatorio.vue";
import ProdutoTabsLayout from "../ProdutoTabsLayout.vue";

const modalFormRelatorio = ref({});
const modalVisualizarRelatorio = ref({});

const props = defineProps({
    contrato: { type: Object },
    produto: { type: [String, Object] },
    pmqa: { type: Object },
    relatorios: { type: Object },
    resultados: { type: Array },
});

const abrirModalFormRelatorio = (item) => {
    modalFormRelatorio.value.abrirModal(item);
};

const abrirVisualizarRelatorio = (item) => {
    modalVisualizarRelatorio.value.abrirModal(item);
};

const excluirRelatorio = (item) => {
    router.delete(
        route("contratos.contratada.servicos.pmqa.relatorio.delete", {
            contrato: props.contrato.id,
            servico: props.servico.id,
            relatorio: item.id,
        }),
    );
};
const activeTab = ref("relatorio");
</script>
<template>
    <ProdutoTabsLayout
        :contratos="contrato"
        :title="'PMQA - EIA'"
        :pmqa="pmqa"
        :produto="produto"
        v-model:active-tab="activeTab"
    >
        <template #relatorio>
            <ModelSearchFormAllColumns :columns="['nome']">
                <template #action>
                    <NavButton
                        @click="abrirModalFormRelatorio()"
                        type-button="success"
                        title="Novo relatório"
                    />
                </template>
            </ModelSearchFormAllColumns>
            <!-- Listagem-->
            <Table
                :columns="['Nome do relatório', 'Data', 'Status', 'Ação']"
                :records="relatorios"
                table-class="table-hover"
            >
                <template #body="{ item }">
                    <tr>
                        <td class="text-center">{{ item.nome }}</td>
                        <td class="text-center">{{ dateTimeFormat(item.created_at) }}</td>
                        <td class="text-center">
                            <span
                                v-if="item.status_id === 4"
                                class="badge bg-azure-lt"
                            >
                                Em confecção
                            </span>
                            <span
                                v-else-if="item.status_id === 1"
                                class="badge bg-red-lt"
                            >
                                Em análise
                            </span>
                            <span
                                v-else-if="item.status_id === 3"
                                class="badge bg-blue-lt"
                            >
                                Aprovado
                            </span>
                            <span
                                v-else-if="item.status_id === 2"
                                class="badge bg-blue-lt"
                            >
                                Pendente
                            </span>
                        </td>
                        <td class="text-center">
                            <button
                                type="button"
                                class="btn btn-icon btn-info dropdown-toggle p-2"
                                data-bs-boundary="viewport"
                                data-bs-toggle="dropdown"
                                aria-expanded="false"
                            >
                                <IconDots />
                            </button>
                            <div
                                class="dropdown-menu dropdown-menu-end"
                                style=""
                            >
                                <a
                                    class="dropdown-item"
                                    href="javascript:void(0)"
                                >
                                    Conclusão
                                </a>
                                <a
                                    @click="abrirVisualizarRelatorio(item)"
                                    class="dropdown-item"
                                    href="javascript:void(0)"
                                >
                                    Visualizar relátorio
                                </a>
                                <a
                                    @click="abrirModalFormRelatorio(item)"
                                    class="dropdown-item"
                                    href="javascript:void(0)"
                                >
                                    Editar
                                </a>
                                <a
                                    @click="excluirRelatorio(item)"
                                    class="dropdown-item"
                                    href="javascript:void(0)"
                                >
                                    Excluir
                                </a>
                                <a
                                    class="dropdown-item"
                                    href="javascript:void(0)"
                                >
                                    Enviar para o fiscal
                                </a>
                                <a
                                    class="dropdown-item"
                                    target="_blank"
                                    :href="
                                        route(
                                            'contratos.contratada.relatorio.pmqa.relatorio.gerar_pdf',
                                            {
                                                contrato: contrato.id,
                                                produto: 'eia',
                                                pmqa: pmqa.id,
                                                relatorio: item.id,
                                            },
                                        )
                                    "
                                >
                                    Exportar relatório
                                </a>
                            </div>
                        </td>
                    </tr>
                </template>
            </Table>
        </template>
    </ProdutoTabsLayout>
    <ModalFormRelatorio
        :contrato="contrato"
        :pmqa="pmqa"
        :resultados="resultados"
        ref="modalFormRelatorio"
    />
    <ModalVisualizarRelatorio
        :contrato="contrato"
        :pmqa="pmqa"
        ref="modalVisualizarRelatorio"
    />
</template>
