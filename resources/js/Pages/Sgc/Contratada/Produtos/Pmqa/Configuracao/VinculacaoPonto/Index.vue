<script setup>
import { usePmqaPermissions } from "@/Pages/Sgc/Contratada/Produtos/Pmqa/Composables/usePmqaPermissions";
import Table from "@/Components/Table.vue";
import NavButton from "@/Components/NavButton.vue";
import { Head, Link, router, useForm } from "@inertiajs/vue3";
import ModalVincularPonto from "./ModalVincularPonto.vue";
import { ref } from "vue";
import LinkConfirmation from "@/Components/LinkConfirmation.vue";
import { IconTrash } from "@tabler/icons-vue";
import { IconEye } from "@tabler/icons-vue";
import { IconPencil } from "@tabler/icons-vue";
import ModalVisualizarPonto from "./ModalVisualizarPonto.vue";
import ModelSearchFormAllColumns from "@/Components/ModelSearchFormAllColumns.vue";
import { computed } from "vue";

const modalVincularPonto = ref(null);
const modalVisualizarPonto = ref(null);

const props = defineProps({
    listas: { type: [Array, Object], default: () => [] },
    pontos: { type: [Array, Object], default: () => [] },
    vinculacoes: { type: Object },
    contrato: { type: Object },
    pmqa: { type: Object },
    aprovacao: { type: Object },
    produto: { type: Object },
    canApprove: { type: Boolean, default: false },
});

const emit = defineEmits(['next', 'prev'])
const { podeGerenciar, reprovarFase } = usePmqaPermissions(props, "configuracao");

const abrirModalVincularPonto = (item = null) => {
    if (!modalVincularPonto.value?.abrirModal) return;
    modalVincularPonto.value.abrirModal(item);
};

const abrirModalVisualizarPonto = (item) => {
    modalVisualizarPonto.value.abrirModal(item);
};

const ap = (ap) => {
    if (!ap?.fk_status) {
        return true;
    }
    return ap?.fk_status === 2;
};

const form = useForm({
    id: null,
    fk_status: null,
});

const podeEnviarAoFiscal = computed(() => {
    const listas = props.vinculacoes?.data ?? props.vinculacoes ?? [];
    if (listas.length === 0) return false;

    return listas.some(lista => lista.pontos && lista.pontos.length > 0);
})

const aprovarFase = () => {
    if (!confirm("Confirmar a aprovação desta fase de Configuração?")) return;
    router.post(route('sgc.contratada.produtos.pmqa.aprovarFase', [props.contrato.id, typeof props.produto === 'string' ? props.produto.toLowerCase() : props.produto.slug, props.pmqa.id]), {
        fase: 'configuracao'
    }, { preserveScroll: true });
};

const enviarParaAnalise = () => {
    if (!confirm("Tem certeza que deseja enviar a Configuração para análise?")) return;
    router.post(route('sgc.contratada.produtos.pmqa.enviarAnaliseFase', [props.contrato.id, typeof props.produto === 'string' ? props.produto.toLowerCase() : props.produto.slug, props.pmqa.id]), {
        fase: 'configuracao'
    }, { preserveScroll: true });
};
</script>
<template #body>
    <ModelSearchFormAllColumns
        v-if="!canApprove && ap(aprovacao) && podeGerenciar"
        :columns="[
            'nome',
            'pontos?.nome_ponto_coleta',
        ]"
    >
        <template #action>
            <NavButton
                v-if="!canApprove && podeGerenciar"
                type-button="primary"
                title="Enviar ao fiscal"
                @click="enviarParaAnalise"
                :disable="!podeEnviarAoFiscal"
            />
            <NavButton
                @click="abrirModalVincularPonto()"
                type-button="success"
                title="Vincular"
                v-if="ap(aprovacao) && podeGerenciar"
            />
        </template>
    </ModelSearchFormAllColumns>
    <Table
        :columns="!canApprove && ap(aprovacao) ? ['Nome da lista', 'Qtd. pontos', 'Ação'] : ['Nome da lista', 'Qtd. pontos']"
        :records="vinculacoes"
        table-class="table-hover"
    >
        <template #body="{ item }">
            <tr>
                <td>{{ item.nome }}</td>
                <td class="text-center">{{ item.pontos.length }}</td>
                <td class="text-center">
                    <NavButton
                        :icon="IconEye"
                        class="btn-icon"
                        type-button="info"
                        @click="abrirModalVisualizarPonto(item)"
                    />
                    <NavButton
                        v-if="!canApprove && ap(aprovacao) && podeGerenciar"
                        :icon="IconPencil"
                        class="btn-icon"
                        type-button="primary"
                        @click="abrirModalVincularPonto(item)"
                    />
                    <LinkConfirmation
                        v-if="!canApprove && ap(aprovacao) && podeGerenciar"
                        v-slot="confirmation"
                        :options="{
                            text: 'A remoção de um ponto será permanente.',
                        }"
                    >
                        <Link
                            :onBefore="confirmation.show"
                            :href="
                                route(
                                    'contratos.contratada.sgc.pmqa.configuracao.vinculacao_ponto.destroy',
                                    {
                                        contrato: contrato.id,
                                        produto: produto.slug,
                                        pmqa: pmqa.id,
                                        lista: item.id,
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
    <div class="d-flex justify-content-between my-4 px-1">
        <NavButton
            type="button"
            type-button="secondary"
            title="Voltar"
            @click="$emit('prev')"
        />

        <div class="d-flex gap-2">
            <NavButton
                v-if="canApprove && podeGerenciar"
                type-button="danger"
                title="✖ Reprovar Configuração"
                @click="reprovarFase"
            />
            <NavButton
                v-if="canApprove && podeGerenciar"
                type-button="primary"
                title="✓ Aprovar Configuração"
                @click="aprovarFase"
            />
        </div>
    </div>

    <ModalVincularPonto
        ref="modalVincularPonto"
        :listas="listas?.data ?? []"
        :pontos="pontos"
        :contrato="contrato"
        :pmqa="pmqa"
        :produto="produto"
    />
    <ModalVisualizarPonto ref="modalVisualizarPonto" />
</template>
