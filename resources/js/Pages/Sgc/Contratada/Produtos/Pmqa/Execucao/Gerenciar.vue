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
import { ref, computed } from "vue";
import { IconSquareCheck } from "@tabler/icons-vue";
import ProdutoTabsLayout from "../ProdutoTabsLayout.vue";

const modalVisualizarPonto = ref({});

const props = defineProps({
    contrato: { type: Object },
    produto: { type: [String, Object]},
    pmqa: { type: Object },
    campanha: { type: Object },
    pontos: { type: Object },
    canApprove: { type: Boolean, default: false },
});

const podeGerenciarExecucao = computed(() => {
    return ['Em elaboração', 'Reprovada'].includes(props.pmqa?.status_execucao);
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
        :pmqa="pmqa"
        :produto="produto"
        v-model:active-tab="activeTab"
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
                                class="btn-icon me-1"
                                type-button="info"
                            />

                            <Link
                                v-if="!canApprove && podeGerenciarExecucao"
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
                                v-if="item.ponto && !canApprove && podeGerenciarExecucao"
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
                                v-else-if="!item.ponto && !canApprove && podeGerenciarExecucao"
                                type-button="primary"
                                class="btn-icon"
                                disabled
                                :icon="IconChartHistogram"
                            />
                        </td>
                    </tr>
                </template>
            </Table>

            <div class="d-flex justify-content-between my-4 px-1">
                <Link
                    class="btn btn-secondary"
                    :href="route('contratos.contratada.sgc.pmqa.execucao.index', {
                        contrato: props.contrato.id,
                        produto: typeof props.produto === 'string' ? props.produto : props.produto.slug,
                        pmqa: props.pmqa.id
                    })"
                >
                    Voltar
                </Link>
            </div>

            <ModalVisualizarPonto
                :campanha="campanha"
                :contrato="contrato"
                ref="modalVisualizarPonto"
            />
        </template>
    </ProdutoTabsLayout>
</template>
