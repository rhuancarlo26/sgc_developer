<script setup>
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import { ref } from "vue";

import ModalHistorico from "./ModalHistorico.vue"

const props = defineProps({
    form: { type: Object },
});

const ModalHistoricoRef = ref(null)
const abrirHistorico = () => {
    ModalHistoricoRef.value.abrirModal()
}

</script>
<template>
    <div class="card">
        <div class="card-header justify-content-between">
            <h3 class="my-0">Pareceres</h3>
            <button @click="abrirHistorico" type="button" class="btn btn-primary">Histórico</button>
        </div>
        <div class="card-body">
            <div class="col-12 mb-4">
                <InputLabel for="parecer_tecnico">Parecer Técnico (Liberado para importação)</InputLabel>
                <textarea v-model="form.parecer_tecnico" id="parecer_tecnico" class="form-control" rows="5"
                    :disabled="[2, 4].includes(form.status)"
                    :style="[2, 4].includes(form.status) ? 'cursor: not-allowed' : ''">
                </textarea>
                <InputError :message="form.errors.parecer_tecnico"/>
            </div>

            <hr />

            <div class="col-12 mb-4">
                <InputLabel for="parecer_tecnico">Parecer de Análise</InputLabel>
                <textarea v-model="form.parecer_analise" id="parecer_analise" class="form-control" rows="5"
                    :disabled="!form.status || [1, 3, 4].includes(form.status)"
                    :style="!form.status || [1, 3, 4].includes(form.status) ? 'cursor: not-allowed' : ''">
                </textarea>
                <InputError :message="form.errors.parecer_analise"/>
            </div>
        </div>
    </div>

    <ModalHistorico ref="ModalHistoricoRef" />
</template>
