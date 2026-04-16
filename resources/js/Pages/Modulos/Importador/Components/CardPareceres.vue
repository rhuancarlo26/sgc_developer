<script setup>
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import { ref } from "vue";

const props = defineProps({
    form: { type: Object },
});

</script>
<template>
    <div class="card">
        <div class="card-header">
            <h3 class="my-0">Pareceres</h3>
        </div>
        <div class="card-body">
            <div class="col-12 mb-4">
                <InputLabel for="parecer_tecnico">Parecer Técnico (Liberado para importação)</InputLabel>
                <textarea v-model="form.parecer_tecnico" id="parecer_tecnico" class="form-control" 
                    rows="5" :disabled="false">
                </textarea>
                <InputError :message="form.errors.parecer_tecnico"/>
            </div>

            <hr />

            <div class="col-12 mb-4">
                <InputLabel for="parecer_tecnico">Parecer de Análise</InputLabel>
                <textarea v-model="form.parecer_analise" id="parecer_analise" class="form-control" 
                    rows="5" disabled style="cursor: not-allowed">
                </textarea>
                <InputError :message="form.errors.parecer_analise"/>
            </div>
        </div>
    </div>
</template>
