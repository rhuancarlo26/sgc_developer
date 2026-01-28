<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import { Head, Link } from "@inertiajs/vue3";
import ModelSearchFormAllColumns from "@/Components/ModelSearchFormAllColumns.vue";
import Table from "@/Components/Table.vue";
import NavButton from "@/Components/NavButton.vue";
import { IconEye } from "@tabler/icons-vue";
import { IconRulerMeasure } from "@tabler/icons-vue";
import { IconChartHistogram } from "@tabler/icons-vue";
import ModalVisualizarPonto from "./ModalVisualizarPonto.vue";
import { ref } from "vue";
import { IconSquareCheck } from "@tabler/icons-vue";
import ProdutoTabsLayout from "../../ProdutoTabsLayout.vue";

const modalVisualizarPonto = ref({});

const props = defineProps({
    contrato: { type: Object },
    produto: { type: [String, Object]},
    pmqa: { type: Object },
    campanha: { type: Object },
    pontos: { type: Object },
});

const abrirModalVisualizarPonto = (item) => {
    modalVisualizarPonto.value.abrirModal(item);
};

const activeTab = ref("execucao");

</script>
<template>
    <ProdutoTabsLayout
        :contratos="contrato"
        :title="'PMQA - EIA'"
        :active-tab="activeTab"
    >
        <template #execucao>
            <Table
                :columns="[
                    'Ponto',
                    'classe',
                    'Tipo de ambiente',
                    'UF',
                    'Município',
                    'coleta',
                    'Medição',
                    'Ação',
                ]"
                :records="pontos"
                :axios-pagination="true"
                table-class="table-hover"
            >
                <template #body="{ item }">
                    <tr>
                        <td class="text-center">
                            {{ item.ponto?.nome_ponto_coleta }}
                        </td>
                        <td class="text-center">{{ item.ponto?.classe }}</td>
                        <td class="text-center">
                            {{ item.ponto?.tipo_ambiente }}
                        </td>
                        <td class="text-center">{{ item.ponto?.uf }}</td>
                        <td class="text-center">{{ item.ponto?.municipio }}</td>

                        <td>
                            <div
                                class="d-flex align-items-center justify-content-center text-success"
                            >
                                <NavButton
                                    v-if="item.coleta"
                                    :icon="IconSquareCheck"
                                    class="btn-icon text-success"
                                    type-button="default"
                                />
                            </div>
                        </td>

                        <td>
                            <div
                                class="d-flex align-items-center justify-content-center text-success"
                            >
                                <NavButton
                                    v-if="item.medicao"
                                    :icon="IconSquareCheck"
                                    class="btn-icon text-success"
                                    type-button="default"
                                />
                            </div>
                        </td>

                        <td class="text-center">
                            <NavButton
                                @click="abrirModalVisualizarPonto(item)"
                                :icon="IconEye"
                                class="btn-icon"
                                type-button="info"
                            />

                            <Link
                                class="btn btn-icon btn-primary me-1"
                                :href="
                                    route(
                                        'contratos.contratada.sgc.pmqa.execucao.coleta.create',
                                        {
                                            contrato: props.contrato.id,
                                            produto: props.produto.slug,
                                            pmqa: props.pmqa.id,
                                            campanha: props.campanha.id,
                                            ponto: item.id,
                                        },
                                    )
                                "
                            >
                                <IconRulerMeasure />
                            </Link>

                            <Link
                                v-if="item.ponto"
                                class="btn btn-icon btn-primary me-1"
                                :href="
                                    route(
                                        'contratos.contratada.sgc.pmqa.execucao.medir.create',
                                        {
                                            contrato: props.contrato.id,
                                            produto: props.produto.slug,
                                            pmqa: props.pmqa.id,
                                            campanha: props.campanha.id,
                                            ponto: item.id,
                                        },
                                    )
                                "
                            >
                                <IconChartHistogram />
                            </Link>

                            <NavButton
                                v-else
                                type-button="primary"
                                class="btn-icon"
                                disabled
                                :icon="IconChartHistogram"
                            />
                        </td>
                    </tr>
                </template>
            </Table>

            <ModalVisualizarPonto
                :campanha="campanha"
                :contrato="contrato"
                ref="modalVisualizarPonto"
            />
        </template>
    </ProdutoTabsLayout>
</template>
