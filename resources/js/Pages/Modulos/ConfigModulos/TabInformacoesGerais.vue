<script setup>
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import {IconDeviceFloppy} from "@tabler/icons-vue";

const props = defineProps({
    form: { type: Object },
});
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
                <input type="file" id="planilha_modelo" name="planilha_modelo" class="form-control"
                    accept=".xlsx,.csv"/>
                <InputError :message="form.errors.planilha_modelo"/>
                <small class="text-secondary">Envie uma planilha modelo. Se contiver dados de exemplo, os tipos serão detectados automaticamente.</small>
            </div>
        </div>
    </div>
</template>
