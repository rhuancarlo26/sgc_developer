<script setup>
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import {IconDeviceFloppy} from "@tabler/icons-vue";
import axios from "axios";
import Swal from "sweetalert2";
import { ref } from "vue";

const props = defineProps({
    form: { type: Object },
    contrato: [String, Number],
    produto: String,
});

const loadPlanilhaModelo = ref(false)

const importarPlanilhaModelo = async ({target}) => {
    const arquivo = target.files?.[0]
    if (!arquivo) return

    if(!props.form.campos.length) {
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

    if(!swal_.isConfirmed) {
        document.getElementById('planilha_modelo').value = ''
        return
    }

    processarCamposPlanilha(arquivo)
}

const processarCamposPlanilha = async (arquivo) => {
    props.form.planilha_modelo = arquivo

    const headers = { 'Content-Type': 'multipart/form-data' }
    const formData = new FormData()
    formData.append('arquivo', props.form.planilha_modelo)

    delete props.form.errors.planilha_modelo

    loadPlanilhaModelo.value = true

    await axios.post(route('sgc.contratada.produtos.modulos.configuracoes.processar-campos-planilha', [
        props.contrato,
        props.produto,
    ]), formData, { headers })
        .then(resp => {
            let campos = resp.data 
            if(!Array.isArray(campos)) {
                campos = Object.values(campos)
            }

            props.form.campos = campos.map(campo => {
                return {
                    nome_campo: campo,
                    tipo: 'texto',
                    obrigatorio: null,
                    regra: null,
                    valor_min: null,
                    valor_max: null,
                    max_caracteres: null,
                    valor_exemplo: null,
                }
            })
        })
        .catch(err => {
            props.form.errors.planilha_modelo = err.response.data?.message
            props.form.planilha_modelo = null

            document.getElementById('planilha_modelo').value = ''
        })

    loadPlanilhaModelo.value = false
}

</script>
<template>
    <div class="card-header">
        <h3 class="my-0">Dados do Módulo</h3>
    </div>
    <div class="card-body">
        <div class="row gap-4">
            <div class="col-12">
                <InputLabel for="nome">
                    <span>Nome do Módulo <span class="text-danger">*</span></span>
                </InputLabel>
                <input type="text" id="nome" name="nome" class="form-control" v-model="form.nome"/>
                <InputError :message="form.errors.nome"/>
            </div>
            <div class="col-12">
                <InputLabel value="Planilha Modelo" for="planilha_modelo"/>
                <div class="d-flex gap-2 align-items-center">
                    <input type="file" id="planilha_modelo" @change="importarPlanilhaModelo" name="planilha_modelo" class="form-control"
                        accept=".xlsx,.csv"/>
                    <div v-if="loadPlanilhaModelo" class="spinner-border text-primary" role="status" style="border-width: 3px">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
                <InputError :message="form.errors.planilha_modelo"/>
                <small class="text-secondary">Envie uma planilha modelo. Se contiver dados de exemplo, os tipos serão detectados automaticamente.</small>
            </div>
        </div>
    </div>
</template>
