<script setup>
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import axios from "axios";
import Swal from "sweetalert2";
import { ref, watch } from "vue";

const props = defineProps({
    form: { type: Object },
    contratos: {
        type: Array,
        default: () => [],
    },
});

const loadPlanilhaModelo = ref(false)

watch(
    () => props.form.pmqa,
    (value) => {
        if (Number(value) !== 1) {
            props.form.contrato_id = null;
        }
    }
);

const importarPlanilhaModelo = async ({ target }) => {
    const arquivo = target.files?.[0]
    if (!arquivo) return

    if (!props.form.campos.length) {
        await processarCamposPlanilha(arquivo)
        return
    }

    const swal_ = await Swal.fire({
        title: "Existem campos pre cadastrados dentro desse módulo",
        text: "Ao importar uma nova planilha os campos já existentes serão perdidos. Deseja continuar?",
        icon: "warning",
        showCloseButton: true,
        showCancelButton: true,
        focusConfirm: false,
    })

    if (!swal_.isConfirmed) {
        document.getElementById('planilha_modelo').value = ''
        return
    }

    await processarCamposPlanilha(arquivo)
}

const processarCamposPlanilha = async (arquivo) => {
    props.form.planilha_modelo = arquivo

    const formData = new FormData()
    formData.append('arquivo', arquivo)

    delete props.form.errors.planilha_modelo

    loadPlanilhaModelo.value = true

    try {

        const resp = await axios.post(
            route('modulos.config-modulos.processar-campos-planilha'),
            formData,
            {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            }
        )

        const campos = resp.data.colunas ?? []

        props.form.campos = campos.map(campo => {
            return {
                nome_campo: campo,
                tipo: null,
                obrigatorio: false,
                regra: false,
                valor_min: null,
                valor_max: null,
                max_caracteres: null,
                valor_exemplo: null,
            }
        })

    } catch (err) {

        props.form.errors.planilha_modelo =
            err.response?.data?.message ??
            'Erro ao processar a planilha.'

        props.form.planilha_modelo = null

        const input = document.getElementById('planilha_modelo')

        if (input) {
            input.value = ''
        }

    } finally {

        loadPlanilhaModelo.value = false
    }
}
</script>
<template>
    <div class="card-header">
        <h3 class="my-0">Dados do Módulo</h3>
    </div>

    <div class="card-body">
        <div class="row g-4">
            <div :class="Number(form.pmqa) === 1 ? 'col-lg-8' : 'col-lg-11'">
                <InputLabel for="nome">
                    <span>Nome do Módulo <span class="text-danger">*</span></span>
                </InputLabel>

                <input type="text" id="nome" name="nome" class="form-control" v-model="form.nome" />

                <InputError :message="form.errors.nome" />
            </div>

            <div class="col-lg-1 d-flex align-items-end">
                <div>
                    <InputLabel value="PMQA" for="pmqa" />

                    <div class="form-check form-switch mt-2">
                        <input id="pmqa" class="form-check-input" type="checkbox" v-model="form.pmqa" :true-value="1"
                            :false-value="0" />

                        <label class="form-check-label" for="pmqa">
                            {{ Number(form.pmqa) === 1 ? 'Sim' : 'Não' }}
                        </label>
                    </div>

                    <InputError :message="form.errors.pmqa" />
                </div>
            </div>

            <div v-if="Number(form.pmqa) === 1" class="col-lg-3">
                <InputLabel for="contrato_id">
                    <span>Contrato <span class="text-danger">*</span></span>
                </InputLabel>

                <v-select id="contrato_id" v-model="form.contrato_id" :options="contratos" :reduce="option => option.id"
                    label="numero_contrato" placeholder="Selecione o contrato">
                    <template #option="{ numero_contrato, contratada }">
                        {{ numero_contrato }} - {{ contratada }}
                    </template>

                    <template #selected-option="{ numero_contrato, contratada }">
                        {{ numero_contrato }} - {{ contratada }}
                    </template>

                    <template #no-options>
                        Nenhum contrato encontrado.
                    </template>
                </v-select>

                <InputError :message="form.errors.contrato_id" />
            </div>

            <div class="col-12">
                <InputLabel value="Planilha Modelo" for="planilha_modelo" />

                <div class="d-flex gap-2 align-items-center">
                    <input type="file" id="planilha_modelo" @change="importarPlanilhaModelo" name="planilha_modelo"
                        class="form-control" accept=".xlsx,.csv" />

                    <div v-if="loadPlanilhaModelo" class="spinner-border text-primary" role="status"
                        style="border-width: 3px">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>

                <InputError :message="form.errors.planilha_modelo" />

                <small class="text-secondary">
                    Envie uma planilha modelo. Os títulos das colunas serão importados
                    automaticamente e as validações poderão ser configuradas manualmente.
                </small>
            </div>
        </div>
    </div>
</template>
