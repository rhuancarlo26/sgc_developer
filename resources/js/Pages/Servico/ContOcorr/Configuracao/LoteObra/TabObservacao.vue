<script setup>
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import NavButton from "@/Components/NavButton.vue";
import {useForm} from "@inertiajs/vue3";
import {IconDeviceFloppy} from "@tabler/icons-vue";

const props = defineProps({
    contrato: {type: Object},
    servico: {type: Object},
    lote: {type: Object}
});

const form = useForm({
    id: null,
    obs: null,
    ...props.lote
});

const salvarObra = () => {
    form.id = props.lote.id;
    form.rodovia = props.lote.rodovia;

    form.post(route('contratos.contratada.servicos.cont_ocorrencia.configuracao.lote_obra.update', {
        contrato: props.contrato.id,
        servico: props.servico.id
    }))
}

</script>
<template>
    <div class="row mb-4">
        <div class="col">
            <InputLabel value="Observações" for="obs"/>
            <textarea class="form-control" rows="5" v-model="form.obs"></textarea>
            <InputError :message="form.errors.obs"/>
        </div>
    </div>
    <div class="row">
        <div class="col d-flex justify-content-end">
            <NavButton @click="salvarObra()" type-button="success" :icon="IconDeviceFloppy"
                       :title="form.id ? 'Alterar' : 'Salvar'"/>
        </div>
    </div>
</template>
