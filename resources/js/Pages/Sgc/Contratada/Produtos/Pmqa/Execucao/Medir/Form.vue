<script setup>
import { Head, Link, router, useForm } from "@inertiajs/vue3";
import NavButton from "@/Components/NavButton.vue";
import { IconDeviceFloppy } from "@tabler/icons-vue";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import { onMounted, ref } from "vue";
import ProdutoTabsLayout from "../../ProdutoTabsLayout.vue";

const props = defineProps({
    contrato: { type: Object },
    produto:  { type: [String, Object] },
    pmqa:     { type: Object },
    campanha: { type: Object },
    ponto:    { type: Object },
    canApprove: { type: Boolean, default: false },
});

import { computed } from "vue";
const isReadonly = computed(() => props.canApprove || (!props.canApprove && props.pmqa?.status_execucao !== 'Em elaboração' && props.pmqa?.status_execucao !== 'Reprovada'));

const form = useForm({
    id:               null,
    campanha_ponto_id: props.ponto.id,
    medido:           false,
    observacao:       null,
    iqa:              null,
    parametros:       [],
    arquivo:          null,
});

onMounted(() => {
    if (props.ponto.medicao) {
        form.id          = props.ponto.medicao.id;
        form.medido      = props.ponto.medicao.medido;
        form.iqa         = props.ponto.medicao.iqa;
        form.observacao  = props.ponto.medicao.observacao;
        if (props.ponto.medicao.parametros.length) {
            props.ponto.medicao.parametros.forEach((parametro) => {
                form.parametros[parametro.parametro_id] = parseFloat(parametro.medicao);
            });
        }
    }
});

// ---------------------------------------------------------------------------
// Ações
// ---------------------------------------------------------------------------

const saveMedicao = () => {
    if (!props.ponto.ponto?.lista?.medir_iqa) form.iqa = null;

    if (form.id) {
        form.patch(route("contratos.contratada.sgc.pmqa.execucao.medir.update", {
            contrato: props.contrato.id,
            produto:  props.produto,
            pmqa:     props.pmqa.id,
            campanha: props.campanha.id,
            ponto:    props.ponto.id,
        }));
    } else {
        form.post(route("contratos.contratada.sgc.pmqa.execucao.medir.store", {
            contrato: props.contrato.id,
            produto:  props.produto,
            pmqa:     props.pmqa.id,
            campanha: props.campanha.id,
            ponto:    props.ponto.id,
        }));
    }
};

const saveArquivo = () => {
    form.id = props.ponto.medicao.id;
    form.post(route("contratos.contratada.servicos.pmqa.execucao.medir.store_arquivo", {
        contrato: props.contrato.id,
        servico:  props.servico.id,
        campanha: props.campanha.id,
        ponto:    props.ponto.id,
    }));
};

const voltar = () => {
    router.visit(route("contratos.contratada.sgc.pmqa.execucao.gerenciar", {
        contrato: props.contrato.id,
        produto:  props.produto,
        pmqa:     props.pmqa.id,
        campanha: props.campanha.id,
    }));
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

            <!-- Checkbox "não foi possível medir" -->
            <div class="row mb-4">
                <div class="col d-flex align-self-end">
                    <label class="form-check">
                        <input class="form-check-input" type="checkbox" v-model="form.medido" :disabled="isReadonly" />
                        <span class="form-check-label">Não foi possível realizar a medição</span>
                    </label>
                    <InputError :message="form.errors.medido" />
                </div>
            </div>

            <!-- Tabela de medições -->
            <div v-if="!form.medido" class="row">
                <div class="table-responsive">
                    <table class="table card-table table-bordered">
                        <thead>
                            <tr>
                                <th>Parâmetro</th>
                                <th>Unidade</th>
                                <th>Limite</th>
                                <th>Medição</th>
                            </tr>
                        </thead>
                        <tbody>

                            <!-- Linha do IQA — campo livre para preenchimento -->
                            <tr v-if="ponto.ponto?.lista?.medir_iqa">
                                <td><strong>IQA</strong></td>
                                <td>—</td>
                                <td>—</td>
                                <td>
                                    <input
                                        type="text"
                                        class="form-control"
                                        v-model="form.iqa" :disabled="isReadonly"
                                        placeholder="Informe o valor do IQA"
                                    />
                                </td>
                            </tr>

                            <!-- Parâmetros normais -->
                            <tr
                                v-for="vinculado in ponto.ponto?.lista?.parametros_vinculados"
                                :key="vinculado.id"
                            >
                                <td>{{ vinculado.parametro.parametro }}</td>
                                <td>{{ vinculado.parametro.unidade }}</td>
                                <td>
                                    {{
                                        vinculado.parametro.limite
                                            ? vinculado.parametro.classe_2
                                            : "Sem limite"
                                    }}
                                </td>
                                <td>
                                    <input
                                        type="text"
                                        class="form-control"
                                        v-model="form.parametros[vinculado.parametro.id]" :disabled="isReadonly"
                                    />
                                </td>
                            </tr>

                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Justificativa (quando não foi possível medir) -->
            <div v-else class="row">
                <div class="col">
                    <InputLabel value="Justificativa" for="observacao" />
                    <textarea
                        class="form-control"
                        name="observacao"
                        id="observacao"
                        rows="5"
                        v-model="form.observacao" :disabled="isReadonly"
                    ></textarea>
                    <InputError :message="form.errors.observacao" />
                </div>
            </div>

            <!-- Botões -->
            <div class="d-flex justify-content-between mt-4">
                <NavButton @click="voltar" type-button="secondary" title="Voltar" />
                <NavButton
                    v-if="!isReadonly"
                                        @click="saveMedicao()"
                    type-button="success"
                    :icon="IconDeviceFloppy"
                    :title="form.id ? 'Alterar' : 'Salvar'"
                />
            </div>

            <!-- Upload de arquivos (disponível após salvar a medição) -->
            <div v-if="ponto.medicao?.id && !form.medido">
                <hr />
                <div class="row">
                    <div class="col">
                        <div>
                            <InputLabel value="Arquivos" for="arquivo" />
                            <div class="row g-2">
                                <div class="col">
                                    <input
                                        @input="form.arquivo = $event.target.files[0]"
                                        type="file"
                                        class="form-control"
                                        name="arquivo"
                                        id="arquivo"
                                    />
                                </div>
                                <div class="col-auto">
                                    <NavButton v-if="!isReadonly"
                                        @click="saveArquivo()" type-button="success" title="Salvar" />
                                </div>
                            </div>
                            <InputError :message="form.errors.arquivo" />
                        </div>
                        <div v-if="ponto.medicao?.arquivos?.length" class="table-responsive mt-4">
                            <table class="table table-hover non-hover">
                                <thead>
                                    <tr>
                                        <th>Nome</th>
                                        <th>Ação</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="arquivo in ponto.medicao.arquivos" :key="arquivo.id">
                                        <td>{{ arquivo.nome }}</td>
                                        <td><!-- botões de ação --></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </template>
    </ProdutoTabsLayout>
</template>
