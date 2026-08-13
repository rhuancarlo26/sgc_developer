<script setup>
import { router, useForm } from "@inertiajs/vue3";
import { ref, watch } from "vue";
import Index from "./Configuracao/Ponto/Index.vue";
import IndexParametros from "./Configuracao/Parametro/Index.vue";
import IndexVincularParametro from "./Configuracao/VinculacaoPonto/Index.vue";
import IndexResultado from "./Resultado/Index.vue";
import { usePage } from "@inertiajs/vue3";
import { computed } from "vue";
import ProdutoTabsLayout from "../ProdutoTabsLayout.vue";
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
});

const page = usePage();

const subStep = ref(Number(page.props.subStep) || 1);
const activeTab = ref(
    page.props.tab ??
        (Number(page.props.subStep) >= 2 ? "configuracao" : "apresentacao"),
);

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

watch(activeTab, (tab) => {
    if (tab === "execucao") {
        router.visit(
            route("contratos.contratada.sgc.pmqa.execucao.index", [
                props.contratos.id,
                produtoParam.value,
                props.pmqa.id,
            ]),
            {
                preserveState: false,
                preserveScroll: true,
            },
        );
    }
}, { immediate: true });

</script>

<template>
    <ProdutoTabsLayout
        :contratos="contratos"
        :title="'PMQA - EIA'"
        :pmqa="pmqa"
        :produto="produto"
        v-model:activeTab="activeTab"
    >
        <template #apresentacao>
            <ApresentacaoForm
                :pmqa="pmqa"
                :contratos="contratos"
                :produto="produto"
                :temas="temas"
                :empreendimentos="empreendimentos"
                @saved="activeTab = 'apresentacao'"
            />
        </template>

        <template #configuracao>
            <Index
                v-if="subStep === 2"
                :contrato="contratos"
                :produto="produto"
                :pmqa="pmqa"
                :pontos="pontos"
                @next="nextSubStepFromConfiguracao"
                @prev="prevSubStepFromConfiguracao"
            />

            <IndexParametros
                v-else-if="subStep === 3"
                :contrato="contratos"
                :produto="produto"
                :pmqa="pmqa"
                :parametros="parametros"
                :listas="listas"
                @next="nextSubStepFromConfiguracao"
                @prev="prevSubStepFromConfiguracao"
            />

            <IndexVincularParametro
                v-else-if="subStep === 4"
                :contrato="contratos"
                :produto="produto"
                :pontos="pontos"
                :pmqa="pmqa"
                :listas="listas"
                :vinculacoes="vinculacoes"
                @next="nextSubStepFromConfiguracao"
                @prev="prevSubStepFromConfiguracao"
            />

            <div
                v-if="pmqaEditavel && subStep === 4"
                class="d-flex justify-content-end mt-4"
            >
                <button class="btn btn-success" @click="submeterPmqa">
                    Submeter para análise
                </button>
            </div>
        </template>

        <!-- <template #resultados>
            <IndexResultado
                :contrato="contratos"
                :produto="produto"
                :pontos="pontos"
                :pmqa="pmqa"
            />
        </template> -->
    </ProdutoTabsLayout>
</template>

<style scoped></style>
