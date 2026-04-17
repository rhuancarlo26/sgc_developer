<script setup>
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import { ref } from "vue";

const props = defineProps({
    form: { type: Object },
    modulos: { type: Array },
    contratos: { type: Array }
});

const campanhas = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]

const selecionarArquivo = ({target}) => {
    props.form.arquivo = target.files?.[0]
}

</script>
<template>
    <div class="card">
        <div class="card-header">
            <h3 class="my-0">Informações Gerais</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <InputLabel for="modulo_id">
                        <span>Módulo <span class="text-danger">*</span></span>
                    </InputLabel>
                    <v-select v-model="form.modulo_id" :options="modulos" :reduce="option => option.id" label="nome" :disabled="[2, 4].includes(form.status)" />
                    <InputError :message="form.errors.modulo_id"/>
                </div>
                <div class="col-lg-4 mb-4">
                    <InputLabel for="mes_ano_referencia">
                        <span>Referência (Mês/Ano) <span class="text-danger">*</span></span>
                    </InputLabel>
                    <input type="text" id="mes_ano_referencia" class="form-control" v-model="form.mes_ano_referencia" maxlength="7" placeholder="MM/AAAA" :disabled="[2, 4].includes(form.status)"/>
                    <InputError :message="form.errors.mes_ano_referencia"/>
                </div>
                <div class="col-lg-4 mb-4">
                    <InputLabel for="campanha">
                        <span>Campanha <span class="text-danger">*</span></span>
                    </InputLabel>
                    <v-select v-model="form.campanha" :options="campanhas" :disabled="[2, 4].includes(form.status)"/>
                    <InputError :message="form.errors.campanha"/>
                </div>
                <div class="col-lg-4 mb-4">
                    <InputLabel for="contrato_id">
                        <span>Contrato <span class="text-danger">*</span></span>
                    </InputLabel>
                    <v-select v-model="form.contrato_id" :options="contratos" :reduce="option => option.id" label="numero_contrato" :disabled="[2, 4].includes(form.status)"/>
                    <InputError :message="form.errors.contrato_id"/>
                </div>
                <div class="col-lg-8 mb-4">
                    <InputLabel for="upload_arquivo">
                        <span>Upload da Planilha (.csv/.xlsx) <span class="text-danger">*</span></span>
                    </InputLabel>
                    <input type="file" id="upload_arquivo" @change="selecionarArquivo" class="form-control"
                        accept=".xlsx,.csv" :disabled="[2, 4].includes(form.status)"/>
                    <InputError :message="form.errors.arquivo"/>
                </div>
            </div>
        </div>
    </div>
</template>
