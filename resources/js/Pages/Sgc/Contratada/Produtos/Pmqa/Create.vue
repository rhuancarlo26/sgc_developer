<script setup>
import { router, useForm } from "@inertiajs/vue3";
import { ref, watch } from "vue";
import Index from "./Configuracao/Ponto/Index.vue";
import IndexParametros from "./Configuracao/Parametro/Index.vue";
import IndexVincularParametro from "./Configuracao/VinculacaoPonto/Index.vue";
import IndexResultado from "./Resultado/Index.vue";
import { usePage } from "@inertiajs/vue3";
import { computed } from "vue";
import ProdutoTabsLayout from "./ProdutoTabsLayout.vue";
import ApresentacaoForm from "./ApresentacaoForm.vue";

const props = defineProps({
    contrato: { type: [Object, String, Number], required: true },
    produto: { type: [Object, String], required: true },
    pontos: { type: [Array, Object], default: () => [] },
    contratos: { type: Object, required: true },
    pmqa: { type: Object, required: true },
    empreendimentos: { type: Object },
    parametros: { type: Array, default: () => [] },
    listas: { type: Object },
    vinculacoes: { type: Object },
    campanhas: { type: Object },
    campanha: { type: Object },
    pontosExecucao: { type: Object },
    canApprove: { type: Boolean, default: false },
});

const page = usePage();

const getDefaultTabAndStep = () => {
    const params = new URLSearchParams(window.location.search);
    const queryTab = params.get('tab') || page.props.tab;
    const querySubStep = params.get('subStep') || page.props.subStep;

    if (queryTab || querySubStep) {
        const tab = queryTab ?? (Number(querySubStep) >= 2 ? "configuracao" : "apresentacao");
        const step = Number(querySubStep) || (tab === "configuracao" ? 2 : 1);
        return { tab, step };
    }

    if (props.pmqa) {
        if (props.pmqa.status_apresentacao !== 'Aprovada') return { tab: 'apresentacao', step: 1 };
        if (props.pmqa.status_configuracao !== 'Aprovada') return { tab: 'configuracao', step: 2 };
        if (props.pmqa.status_execucao !== 'Aprovada') return { tab: 'execucao', step: 1 };
        if (props.pmqa.status_resultado !== 'Aprovada') return { tab: 'resultados', step: 1 };
        if (props.pmqa.status_relatorio !== 'Aprovada') return { tab: 'relatorios', step: 1 };
    }
    
    return { tab: 'apresentacao', step: 1 };
};

const defaultState = getDefaultTabAndStep();
const subStep = ref(defaultState.step);
const activeTab = ref(defaultState.tab);

const temas = [{ id: 1, nome_tema: "Recursos Hídricos" }];

const form = useForm({
    id: props.pmqa?.id ?? null,
    cod_emp: props.pmqa?.cod_emp ?? "",
    tipo: props.produto === "Eia" ? "Eia" : (props.pmqa?.subproduto ?? ""),
    tema: props.pmqa?.tema ?? "Recursos Hídricos",
    especificacao: props.pmqa?.especificacao ?? "",
    introducao: props.pmqa?.introducao ?? "",
    justificativa: props.pmqa?.justificativa ?? "",
    objetivos: props.pmqa?.objetivos ?? "",
    metodologia: props.pmqa?.metodologia ?? "",
    publico_alvo: props.pmqa?.publico_alvo ?? "",
});

const setActiveTab = (tabId) => {
    activeTab.value = tabId;

    if (tabId === "apresentacao") {
        subStep.value = 1;
    }

    if (tabId === "configuracao") {
        if (subStep.value < 2) subStep.value = 2;
    }
};

const salvarApresentacao = () => {
    form.patch(
        route("sgc.contratada.produtos.pmqa.update", [
            props.contratos,
            props.produto,
        ]),
        {
            preserveScroll: true,
            onSuccess: () => {
                subStep.value = 2;
            },
        },
    );
};

const produtoParam = computed(() =>
    typeof props.produto === "string" ? props.produto.toLowerCase() : (props.produto?.slug ?? "pmqa")
);

const pmqaEditavel = computed(() =>
    ["Em elaboração", "Rejeitada"].includes(props.pmqa?.status_aprovacao)
);

const submeterPmqa = () => {
    if (!confirm("Submeter PMQA para análise?")) return;

    router.post(
        route("sgc.contratada.produtos.pmqa.submeter", [
            props.contratos.id,
            produtoParam.value,
            props.pmqa.id,
        ])
    );
};

const hasPontosImportados = computed(() => {
    if (Array.isArray(props.pontos)) return props.pontos.length > 0;
    if (props.pontos?.data) return props.pontos.data.length > 0;
    return false;
});

const hasListaParametros = computed(() => {
    if (Array.isArray(props.listas)) return props.listas.length > 0;
    if (props.listas?.data) return props.listas.data.length > 0;
});

const nextSubStepFromConfiguracao = () => {
    activeTab.value = "configuracao";

    if (subStep.value === 2) {
        if (!hasPontosImportados.value) {
            alert("Importe/cadastre ao menos 1 ponto antes de avançar.");
            return;
        }
        subStep.value = 3;
        return;
    }

    if (subStep.value === 3) {
        if (!hasListaParametros.value) {
            alert("Crei pelo menos uma lista para avançar.");
            return;
        }
        subStep.value = 4;
        return;
    }
    if (subStep.value < 5) subStep.value += 1;
};

const prevSubStepFromConfiguracao = () => {
    activeTab.value = "configuracao";
    if (subStep.value === 3) subStep.value = 2;
    else if (subStep.value > 1) subStep.value -= 1;
};

const isViewMode = computed(() => !!props.pmqa?.id);
const produtoNome = computed(() =>
    typeof props.produto === "string"
        ? props.produto
        : (props.produto?.slug ?? props.produto?.nome),
);

import { onMounted } from 'vue';

onMounted(() => {
    if (activeTab.value === 'execucao') {
        router.visit(route("contratos.contratada.sgc.pmqa.execucao.index", [props.contratos.id, produtoParam.value, props.pmqa.id]), { preserveState: false, preserveScroll: true });
    } else if (activeTab.value === 'resultados') {
        router.visit(route("contratos.contratada.sgc.pmqa.resultado.index", [props.contratos.id, produtoParam.value, props.pmqa.id]), { preserveState: false, preserveScroll: true });
    } else if (activeTab.value === 'relatorios') {
        router.visit(route("contratos.contratada.relatorio.pmqa.relatorio.index", [props.contratos.id, produtoParam.value, props.pmqa.id]), { preserveState: false, preserveScroll: true });
    }
});

</script>

<template>
    <ProdutoTabsLayout
        :contratos="contratos"
        :title="'PMQA - EIA'"
        :pmqa="pmqa"
        :produto="produto"
                :subproduto="subproduto"
        v-model:activeTab="activeTab"
    >
        <template #apresentacao>
            <ApresentacaoForm
                :pmqa="pmqa"
                :contratos="contratos"
                :produto="produto"
                :subproduto="subproduto"
                :temas="temas"
                :empreendimentos="empreendimentos"
                :canApprove="canApprove"
                @saved="activeTab = 'apresentacao'"
            />
        </template>

        <template #configuracao>
            <Index
                v-if="subStep === 2"
                :contrato="contratos" :contratos="contratos"
                :produto="produto"
                :subproduto="subproduto"
                :pmqa="pmqa"
                :pontos="pontos"
                :canApprove="canApprove"
                @next="nextSubStepFromConfiguracao"
                @prev="prevSubStepFromConfiguracao"
            />

            <IndexParametros
                v-else-if="subStep === 3"
                :contrato="contratos" :contratos="contratos"
                :produto="produto"
                :subproduto="subproduto"
                :pmqa="pmqa"
                :parametros="parametros"
                :listas="listas"
                :canApprove="canApprove"
                @next="nextSubStepFromConfiguracao"
                @prev="prevSubStepFromConfiguracao"
            />

            <IndexVincularParametro
                v-else-if="subStep === 4"
                :contrato="contratos" :contratos="contratos"
                :produto="produto"
                :subproduto="subproduto"
                :pontos="pontos"
                :pmqa="pmqa"
                :listas="listas"
                :vinculacoes="vinculacoes"
                :canApprove="canApprove"
                @next="nextSubStepFromConfiguracao"
                @prev="prevSubStepFromConfiguracao"
            />

        </template>

        <!-- <template #resultados>
            <IndexResultado
                :contrato="contratos" :contratos="contratos"
                :produto="produto"
                :subproduto="subproduto"
                :pontos="pontos"
                :pmqa="pmqa"
            />
        </template> -->
    </ProdutoTabsLayout>
</template>

<style scoped></style>
