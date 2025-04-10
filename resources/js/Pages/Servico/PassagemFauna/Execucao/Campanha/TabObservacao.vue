<script setup>

import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import NavButton from "@/Components/NavButton.vue";
import {Link, useForm} from "@inertiajs/vue3";
import {IconDeviceFloppy} from "@tabler/icons-vue";

const props = defineProps({
    contrato: {type: Object},
    servico: {type: Object},
    campanha: {type: Object}
})

const form = useForm({
    id: null,
    id_campanha: null,
    obs: null,
    ...props.campanha
});

const salvarObs = () => {
    form.id_campanha = props.campanha.id;

    form.post(route('contratos.contratada.servicos.passagem_fauna.execucao.campanha.update', {
        contrato: props.contrato.id,
        servico: props.servico.id
    }));
}
</script>

<template>
    <div class="row mb-4">
        <div class="col">
            <InputLabel value="Observações" for="obs"/>
            <textarea name="obs" id="obs" class="form-control"
                      v-model="form.obs" rows="5"></textarea>
            <InputError :message="form.errors.obs"/>
        </div>
    </div>
    <div class="row">
        <div class="col d-flex justify-content-end">
            <NavButton @click="salvarObs()" type-button="success" :icon="IconDeviceFloppy"
                       :title="form.id ? 'Alterar' : 'Salvar'"/>
        </div>
    </div>
</template>

<style scoped>

</style>
