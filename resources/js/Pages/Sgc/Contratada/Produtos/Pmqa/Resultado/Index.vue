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
import ProdutoTabsLayout from "../../ProdutoTabsLayout.vue";

const refModalResultado = ref({});

const props = defineProps({
    contrato: { type: Object },
    resultados: { type: Object },
    campanhas: { type: Array },
    pmqa: {type: Object },
    produto: {type:Object}
});
console.log(props.resultados)
const abrirModalResultado = (item) => {
    refModalResultado.value.abrirModal(item);
};
const activeTab = ref("resultados");
</script>
<template>
    <ProdutoTabsLayout
        :contratos="contrato"
        :title="'PMQA - EIA'"
        :active-tab="activeTab"
    >
        <template #resultados>
            <!-- Listagem-->
            <ModelSearchFormAllColumns :columns="['nome']">
                <template #action>
                    <NavButton
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
                            <NavLink
                                route-name="contratos.contratada.sgc.pmqa.resultado.resultado"
                                :param="{
                                    contrato: contrato.id,
                                    produto: produto,
                                    pmqa: pmqa.id,
                                    resultado: item.id,
                                }"
                                class="btn btn-icon btn-info me-1"
                                :icon="IconSettings"
                            />
                            <NavButton
                                @click="abrirModalResultado(item)"
                                :icon="IconPencil"
                                class="btn-icon"
                                type-button="primary"
                            />
                            <LinkConfirmation
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
