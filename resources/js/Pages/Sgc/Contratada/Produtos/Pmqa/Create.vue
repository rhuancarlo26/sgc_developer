<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import NavButton from "@/Components/NavButton.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import ModalImportarPontos from "./Fases/ModalPontos.vue";
import { ref } from "vue";
import { router } from "@inertiajs/vue3";
import ImportarPontos from "./Components/ImportarPontos.vue";
import ListaParametros from "./Components/ListaParametros.vue";
import NavbarContrato from "../../NavbarContrato.vue";

const props = defineProps({
    contrato: { type: Object },
    servico: { type: Object },
    pontos: { type: Object },
    produto: { type: Object },
    contratos: { type: Object },
    draftData: { type: Object },
    empreendimentos: { type: Object },
    parametros: { type: Object },
    listas: { type: Object }
});

const form = useForm({
    cod_emp: props.draftData?.cod_emp ?? "",
    familia:
        props.produto === "Eia" ? "Eia" : props.draftData?.subproduto ?? "",
});

const subStep = ref(1);

const modalImportarPonto = ref(null);
const activeTab = ref("apresentacao");

const abrirModalImportar = () => {
    modalImportarPonto.value.abrirModal();
};

const abrirModalVisualizar = (item) => {
    modalVisualizarPonto.value.abrirModal(item);
};

const setActiveTab = (tabId) => {
    activeTab.value = tabId;
};

// NOVO: Função que será chamada quando o evento do filho for recebido.
const atualizarListaDePontos = () => {
    console.log(
        "Evento 'importacaoConcluida' recebido! A atualizar a lista de pontos..."
    );
    router.reload({
        only: ["pontos"],
        onSuccess: () => {
            // Opcional: mostrar um feedback ao utilizador.
            // alert("A lista de pontos foi atualizada!");
        },
        onError: () => {
            alert("Ocorreu um erro ao atualizar a lista de pontos.");
        },
    });
};

const nextSubStep = () => {
    if (subStep.value === 3 && moduloRecords.value.length === 0) {
        alert("Adicione ao menos um módulo amostral antes de avançar.");
        return;
    }
    if (
        subStep.value === 4 &&
        !naoSeAplicaQuelonios.value &&
        pontoRecords.value.length === 0
    ) {
        alert(
            'Adicione ao menos um ponto de quelônio ou crocodiliano antes de avançar, ou marque "Não se aplica".'
        );
        return;
    }
    if (
        subStep.value === 5 &&
        !naoSeAplicaCavernicola.value &&
        pontoCavernicolaRecords.value.length === 0
    ) {
        alert(
            'Adicione pelo menos um ponto de fauna cavernícola antes de avançar, ou marque "Não se aplica".'
        );
        return;
    }
    if (subStep.value < 5) {
        subStep.value += 1;
    } else {
        setActiveTab("metodologia");
    }
};

const prevSubStep = () => {
    if (subStep.value > 1) {
        subStep.value -= 1;
    }
};
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
                                contratos.tipo_contrato
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
                            CADASTRAR {{ props.produto.toUpperCase() }}
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
                                    >Configuração</a
                                >
                            </li>
                            <li class="nav-item">
                                <a
                                    class="nav-link"
                                    :class="{
                                        active: activeTab === 'metodologia',
                                    }"
                                    @click.prevent="setActiveTab('metodologia')"
                                    >Metodologia</a
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
                                    <form @submit.prevent="nextSubStep">
                                        <div class="mb-3">
                                            <label
                                                for="cod_emp"
                                                class="form-label"
                                                >Empreendimento</label
                                            >
                                            <select
                                                v-model="form.cod_emp"
                                                class="form-select"
                                                id="cod_emp"
                                                required
                                            >
                                                <option value="">
                                                    Selecione um empreendimento
                                                </option>
                                                <option
                                                    v-for="emp in props.empreendimentos"
                                                    :key="emp"
                                                    :value="emp"
                                                >
                                                    {{ emp }}
                                                </option>
                                            </select>
                                            <InputError
                                                :message="form.errors.cod_emp"
                                            />
                                        </div>
                                        <div class="mb-3">
                                            <label
                                                for="familia"
                                                class="form-label"
                                                >Família</label
                                            >
                                            <input
                                                v-model="form.familia"
                                                type="text"
                                                class="form-control"
                                                id="familia"
                                                :disabled="
                                                    props.produto === 'Fauna'
                                                "
                                                required
                                            />
                                            <InputError
                                                :message="form.errors.familia"
                                            />
                                        </div>
                                        <div class="d-flex justify-content-end">
                                            <NavButton
                                                type="submit"
                                                type-button="primary"
                                                title="Avançar"
                                            />
                                        </div>
                                        <!-- <h4 class="text-center mt-4" style="font-weight: bold; color: #6c757d;">{{ subStep }}/5</h4> -->
                                    </form>
                                </div>
                                <ImportarPontos
                                    v-if="subStep === 2"
                                    :contrato="contrato"
                                    :pontos="pontos"
                                    :produto="produto"
                                    :contratos="contratos"
                                    :draftData="draftData"
                                    @next="nextSubStep"
                                    @prev="prevSubStep"
                                >
                                    <template #footer>
                                        <h4
                                            class="text-center mt-4"
                                            style="
                                                font-weight: bold;
                                                color: #6c757d;
                                            "
                                        >
                                            {{ subStep }}/5
                                        </h4>
                                    </template>
                                </ImportarPontos>
                                <ListaParametros
                                    v-if="subStep === 3"
                                    :draftData="draftData"
                                    :parametros="parametros"
                                    :contrato="contrato"
                                    :produto="produto"
                                    :listas="listas"
                                    @next="nextSubStep"
                                    @prev="prevSubStep"
                                >
                                </ListaParametros>
                                <!-- <QueloniosCrocodilian
                                    v-if="subStep === 4"
                                    v-model:naoSeAplica="naoSeAplicaQuelonios"
                                    :form-pontos-amostragem="formPontosAmostragem"
                                    :ponto-records="pontoRecords"
                                    @adicionar-ponto="adicionarPonto"
                                    @excluir-ponto="excluirPonto"
                                    @next="nextSubStep"
                                    @prev="prevSubStep"
                                >
                                    <template #footer>
                                        <h4 class="text-center mt-4" style="font-weight: bold; color: #6c757d;">{{ subStep }}/5</h4>
                                    </template>
                                </QueloniosCrocodilian> -->
                                <!-- <FaunaCavernic
                                    v-if="subStep === 5"
                                    v-model:naoSeAplica="naoSeAplicaCavernicola"
                                    :form-pontos-cavernicola="formPontosCavernicola"
                                    :ponto-cavernicola-records="pontoCavernicolaRecords"
                                    @adicionar-ponto-cavernicola="adicionarPontoCavernicola"
                                    @excluir-ponto-cavernicola="excluirPontoCavernicola"
                                    @next="nextSubStep"
                                    @prev="prevSubStep"
                                >
                                    <template #footer>
                                        <h4 class="text-center mt-4" style="font-weight: bold; color: #6c757d;">{{ subStep }}/5</h4>
                                    </template>
                                </FaunaCavernic> -->
                            </div>
                            <div
                                v-if="activeTab === 'metodologia'"
                                class="tab-pane fade"
                                :class="{
                                    'show active': activeTab === 'metodologia',
                                }"
                            >
                                teste
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

        <ModalImportarPontos
            :contrato="contrato"
            :produto="produto"
            :draftData="draftData"
            ref="modalImportarPonto"
            @importacaoConcluida="atualizarListaDePontos"
        />
    </AuthenticatedLayout>
</template>

<style scoped></style>
