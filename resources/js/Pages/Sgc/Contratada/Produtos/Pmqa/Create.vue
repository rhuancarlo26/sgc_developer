<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import { Head, useForm } from "@inertiajs/vue3";
import { ref, watch } from "vue";
import Index from "./Configuracao/Ponto/Index.vue";
import IndexParametros from "./Configuracao/Parametro/Index.vue";
import IndexVincularParametro from "./Configuracao/VinculacaoPonto/Index.vue";
import NavbarContrato from "../../NavbarContrato.vue";
import InputLabel from "@/Components/InputLabel.vue";
import { usePage } from "@inertiajs/vue3";
import { computed } from "vue";
import InputError from "@/Components/InputError.vue";
import { IconDeviceFloppy } from "@tabler/icons-vue";

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
            props.contrato,
            props.produto.slug,
        ]),
        {
            preserveScroll: true,
            onSuccess: () => {
                subStep.value = 2;
            },
        },
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
</script>

<template>
    <Head :title="`${contratos.contratada.slice(0, 10)}...`" />
    <AuthenticatedLayout>
        <template #header>
            <div class="w-100 d-flex justify-content-between">
                <Breadcrumb
                    class="align-self-center"
                    :links="[
                        {
                            route: route(
                                'contratos.gestao.listagem',
                                contratos.tipo_contrato,
                            ),
                            label: `Gestão de Contratos`,
                        },
                        { route: '#', label: contratos.contratada },
                    ]"
                />
                <!-- <Link
                    class="btn btn-primary"
                    :href="
                        route('contratos.contratada.servicos.index', {
                            contrato: props.contratos.id,
                        })
                    "
                >
                    Voltar
                </Link> -->
            </div>
        </template>

        <NavbarContrato :tipo="contratos">
            <template #body>
                <div class="card">
                    <div class="card-body">
                        <h2 class="text-center mb-4">
                            CADASTRAR {{ produtoNome.toUpperCase() }}
                        </h2>
                        <ul class="nav nav-tabs mb-4">
                            <li class="nav-item">
                                <a
                                    class="nav-link"
                                    :class="{
                                        active: activeTab === 'apresentacao',
                                    }"
                                    @click.prevent="
                                        setActiveTab('apresentacao')
                                    "
                                    >Apresentacao</a
                                >
                            </li>
                            <li class="nav-item">
                                <a
                                    class="nav-link"
                                    :class="{
                                        active: activeTab === 'configuracao',
                                    }"
                                    @click.prevent="
                                        setActiveTab('configuracao')
                                    "
                                    >Configuração</a
                                >
                            </li>
                            <li class="nav-item">
                                <a
                                    class="nav-link"
                                    :class="{
                                        active: activeTab === 'resultados',
                                    }"
                                    @click.prevent="setActiveTab('resultados')"
                                    >Resultados</a
                                >
                            </li>
                            <li class="nav-item">
                                <a
                                    class="nav-link"
                                    :class="{ active: activeTab === 'anexos' }"
                                    @click.prevent="setActiveTab('anexos')"
                                    >Anexos</a
                                >
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div
                                v-if="activeTab === 'apresentacao'"
                                class="tab-pane fade"
                                :class="{
                                    'show active': activeTab === 'apresentacao',
                                }"
                            >
                                <!-- Subetapa 1/5 -->
                                <div v-if="subStep === 1">
                                    <h4 class="mb-3" style="text-align: center">
                                        APRESENTAÇÃO
                                    </h4>
                                    <form @submit.prevent="salvarApresentacao">
                                        <div class="row mb-4">
                                            <div class="col form-group">
                                                <InputLabel
                                                    value="Tema"
                                                    for="tema"
                                                />
                                                <v-select
                                                    :options="temas"
                                                    label="nome_tema"
                                                    :reduce="(t) => t.nome_tema"
                                                    v-model="form.tema"
                                                >
                                                    <template #no-options="{}">
                                                        Nenhum registro
                                                        encontrado.
                                                    </template>
                                                </v-select>
                                                <InputError
                                                    :message="form.errors.tema"
                                                />
                                            </div>
                                            <div class="col form-group">
                                                <InputLabel
                                                    value="Código de Empreendimento"
                                                    for="cod_emp"
                                                />
                                                <v-select
                                                    :options="empreendimentos"
                                                    label="nome"
                                                    v-model="form.cod_emp"
                                                >
                                                    <template #no-options="{}">
                                                        Nenhum registro
                                                        encontrado.
                                                    </template>
                                                </v-select>
                                                <InputError
                                                    :message="form.errors.tipo"
                                                />
                                            </div>
                                        </div>
                                        <div class="row mb-4">
                                            <div class="col form-group">
                                                <InputLabel
                                                    value="Especificação"
                                                    for="especificacao"
                                                />
                                                <textarea
                                                    name="especificacao"
                                                    id="especificacao"
                                                    class="form-control"
                                                    v-model="form.especificacao"
                                                    rows="5"
                                                ></textarea>
                                                <InputError
                                                    :message="
                                                        form.errors
                                                            .especificacao
                                                    "
                                                />
                                            </div>
                                        </div>
                                        <div class="row mb-4">
                                            <div class="col form-group">
                                                <InputLabel
                                                    value="Introdução"
                                                    for="introducao"
                                                />
                                                <textarea
                                                    name="introducao"
                                                    id="introducao"
                                                    class="form-control"
                                                    v-model="form.introducao"
                                                    rows="5"
                                                ></textarea>
                                                <InputError
                                                    :message="
                                                        form.errors.introducao
                                                    "
                                                />
                                            </div>
                                        </div>
                                        <div class="row mb-4">
                                            <div class="col form-group">
                                                <InputLabel
                                                    value="Justificativa"
                                                    for="justificativa"
                                                />
                                                <textarea
                                                    name="justificativa"
                                                    id="justificativa"
                                                    class="form-control"
                                                    v-model="form.justificativa"
                                                    rows="5"
                                                ></textarea>
                                                <InputError
                                                    :message="
                                                        form.errors
                                                            .justificativa
                                                    "
                                                />
                                            </div>
                                        </div>
                                        <div class="row mb-4">
                                            <div class="col form-group">
                                                <InputLabel
                                                    value="Objetivos"
                                                    for="objetivo"
                                                />
                                                <textarea
                                                    name="objetivo"
                                                    id="objetivo"
                                                    class="form-control"
                                                    v-model="form.objetivos"
                                                    rows="5"
                                                ></textarea>
                                                <InputError
                                                    :message="
                                                        form.errors.objetivos
                                                    "
                                                />
                                            </div>
                                        </div>
                                        <div class="row mb-4">
                                            <div class="col form-group">
                                                <InputLabel
                                                    value="Metodologia"
                                                    for="metodologia"
                                                />
                                                <textarea
                                                    name="metodologia"
                                                    id="metodologia"
                                                    class="form-control"
                                                    v-model="form.metodologia"
                                                    rows="5"
                                                ></textarea>
                                                <InputError
                                                    :message="
                                                        form.errors.metodologia
                                                    "
                                                />
                                            </div>
                                        </div>
                                        <div class="row mb-4">
                                            <div class="col form-group">
                                                <InputLabel
                                                    value="Público alvo"
                                                    for="publico_alvo"
                                                />
                                                <textarea
                                                    name="publico_alvo"
                                                    id="publico_alvo"
                                                    class="form-control"
                                                    v-model="form.publico_alvo"
                                                    rows="5"
                                                ></textarea>
                                                <InputError
                                                    :message="
                                                        form.errors.publico_alvo
                                                    "
                                                />
                                            </div>
                                        </div>
                                        <div
                                            class="mb-4 d-flex justify-content-end"
                                        >
                                            <button
                                                v-if="!isViewMode"
                                                type="submit"
                                                class="btn btn-success"
                                                :disabled="form.processing"
                                            >
                                                <IconDeviceFloppy
                                                    class="me-2"
                                                />
                                                Salvar
                                            </button>

                                            <button
                                                v-else
                                                type="button"
                                                class="btn btn-primary"
                                                @click="
                                                    setActiveTab('configuracao')
                                                "
                                            >
                                                Avançar
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div
                                v-if="activeTab === 'configuracao'"
                                class="tab-pane fade"
                                :class="{
                                    'show active': activeTab === 'configuracao',
                                }"
                            >
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
                                <div v-else class="alert alert-info mb-0">
                                    Selecione uma etapa de configuração.
                                </div>
                            </div>

                            <div
                                v-if="activeTab === 'resultados'"
                                class="tab-pane fade"
                                :class="{
                                    'show active': activeTab === 'resultados',
                                }"
                            >
                                teste
                            </div>
                            <div
                                v-if="activeTab === 'anexos'"
                                class="tab-pane fade"
                                :class="{
                                    'show active': activeTab === 'anexos',
                                }"
                            >
                                teste
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </NavbarContrato>
    </AuthenticatedLayout>
</template>

<style scoped></style>
