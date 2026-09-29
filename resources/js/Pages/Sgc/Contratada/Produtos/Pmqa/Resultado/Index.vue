<script setup>
import ModelSearchFormAllColumns from "@/Components/ModelSearchFormAllColumns.vue";
import Table from "@/Components/Table.vue";
import NavButton from "@/Components/NavButton.vue";
import NavLink from "@/Components/NavLink.vue";
import { ref } from "vue";
import { dateTimeFormat } from "@/Utils/DateTimeUtils";
import LinkConfirmation from "@/Components/LinkConfirmation.vue";
import { IconTrash } from "@tabler/icons-vue";
import { IconPencil } from "@tabler/icons-vue";
import { IconSettings } from "@tabler/icons-vue";
import ModalResultado from "./ModalResultado.vue";
import ProdutoTabsLayout from "../ProdutoTabsLayout.vue";
import { usePmqaPermissions } from '../Composables/usePmqaPermissions';
import { router } from "@inertiajs/vue3";
import { IconEye, IconListDetails } from "@tabler/icons-vue";
import { Link } from "@inertiajs/vue3";
import { computed } from "vue";

const refModalResultado = ref({});

const props = defineProps({
    contrato: { type: Object },
    resultados: { type: Object },
    campanhas: { type: Array },
    pmqa: {type: Object },
    produto: {type:Object},
    canApprove: { type: Boolean, default: false },
});

const { podeGerenciar: podeGerenciarResultado, reprovarFase } = usePmqaPermissions(props, 'resultado');

const abrirModalResultado = (item) => {
    refModalResultado.value.abrirModal(item);
};
const activeTab = ref("resultados");

const podeEnviarAoFiscal = computed(() => {
    const listaResultados = props.resultados?.data ?? props.resultados ?? [];
    return listaResultados.length > 0;
})

const enviarParaAnalise = () => {
    if (!confirm("Tem certeza que deseja enviar o Resultado para análise?")) return;
    router.post(route('sgc.contratada.produtos.pmqa.enviarAnaliseFase', [props.contrato.id, props.produto, props.pmqa.id]), {
        fase: 'resultado'
    }, { preserveScroll: true });
};

const aprovarFase = () => {
    if (!confirm("Confirmar a aprovação desta fase de Resultado?")) return;
    router.post(route('sgc.contratada.produtos.pmqa.aprovarFase', [props.contrato.id, props.produto, props.pmqa.id]), {
        fase: 'resultado'
    }, { preserveScroll: true });
};
</script>
<template>
    <ProdutoTabsLayout
        :contratos="contrato"
        :title="'PMQA - EIA'"
        :pmqa="pmqa"
        :produto="produto"
        v-model:active-tab="activeTab"
    >
        <template #resultados>
            <!-- Listagem-->
            <ModelSearchFormAllColumns :columns="['Nome do resultado', 'Campanhas', 'Data', 'Ação']">
                <template #action>
                    <NavButton
                        v-if="!canApprove && podeGerenciarResultado"
                        type-button="primary"
                        title="Enviar ao fiscal"
                        @click="enviarParaAnalise"
                        class="me-2"
                        :disabled="!podeEnviarAoFiscal"
                    />
                    <NavButton
                        v-if="canApprove && podeGerenciarResultado"
                        type-button="danger"
                        title="✖ Reprovar Resultados"
                        @click="reprovarFase"
                        class="me-2"
                    />
                    <NavButton
                        v-if="canApprove && podeGerenciarResultado"
                        type-button="success"
                        title="✓ Aprovar Resultados"
                        @click="aprovarFase"
                        class="me-2"
                    />
                    <NavButton
                        v-if="!canApprove && podeGerenciarResultado"
                        @click="abrirModalResultado()"
                        type-button="success"
                        title="Novo resultado"
                    />
                </template>
            </ModelSearchFormAllColumns>
            <Table
                :columns="['Nome do resultado', 'Campanhas', 'Data', 'Ação']"
                :records="resultados"
                table-class="table-hover"
            >
                <template #body="{ item }">
                    <tr>
                        <td>{{ item.nome }}</td>
                        <td>
                            <span
                                v-for="campanha in item.campanhas.map(
                                    (campanha) => campanha.nome_campanha,
                                )"
                                :key="campanha"
                                class="badge bg-warning text-white m-1"
                            >
                                {{ campanha }}
                            </span>
                        </td>
                        <td>{{ dateTimeFormat(item.created_at) }}</td>
                        <td>
                            <NavButton
                                @click="abrirModalResultado(item)"
                                :icon="(!canApprove && podeGerenciarResultado) ? IconPencil : IconEye"
                                class="btn-icon me-1"
                                :type-button="(!canApprove && podeGerenciarResultado) ? 'primary' : 'info'"
                            />
                            <Link
                                :href="route('contratos.contratada.sgc.pmqa.resultado.resultado', {
                                    contrato: contrato.id,
                                    produto: produto,
                                    pmqa: pmqa.id,
                                    resultado: item.id,
                                })"
                                class="btn btn-icon btn-info me-1"
                                :title="(!canApprove && podeGerenciarResultado) ? 'Gerenciar Detalhes' : 'Visualizar Detalhes'"
                            >
                                <IconSettings v-if="!canApprove && podeGerenciarResultado" />
                                <IconListDetails v-else />
                            </Link>
                            <LinkConfirmation v-if="!canApprove && podeGerenciarResultado"
                                v-slot="confirmation"
                                :options="{
                                    text: 'A remoção da campanha será permanente.',
                                }"
                            >
                                <Link
                                    :onBefore="confirmation.show"
                                    :href="
                                        route(
                                            'contratos.contratada.sgc.pmqa.resultado.delete',
                                            {
                                                contrato: contrato.id,
                                                produto: produto,
                                                pmqa: pmqa.id,
                                                resultado: item.id,
                                            },
                                        )
                                    "
                                    as="button"
                                    method="delete"
                                    type="button"
                                    class="btn btn-icon btn-danger"
                                >
                                    <IconTrash />
                                </Link>
                            </LinkConfirmation>
                        </td>
                    </tr>
                </template>
            </Table>
        </template>
    </ProdutoTabsLayout>
    <ModalResultado
        :contrato="contrato"
        :pmqa="pmqa"
        :produto="produto"
        :campanhas="campanhas"
        ref="refModalResultado"
    />
</template>
