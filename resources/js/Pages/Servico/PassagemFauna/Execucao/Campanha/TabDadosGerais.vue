<script setup>

import {IconDeviceFloppy} from "@tabler/icons-vue";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import NavButton from "@/Components/NavButton.vue";
import {useForm} from "@inertiajs/vue3";

const props = defineProps({
    contrato: {type: Object},
    servico: {type: Object},
    campanha: {type: Object}
})

const form = useForm({
    id: null,
    periodo: null,
    data_inicial: null,
    data_final: null,
    ...props.campanha
});

const salvarDadosGerais = () => {
    form.post(route('contratos.contratada.servicos.passagem_fauna.execucao.campanha.store', {
        contrato: props.contrato.id,
        servico: props.servico.id
    }), {
        onSuccess: () => form.id = props.campanha.id
    });
}
</script>

<template>
    <div class="row mb-4">
        <div class="col">
            <InputLabel value="ID campanha" for="id"/>
            <input type="text" name="id" id="id" class="form-control" disabled>
            <InputError :message="form.errors.id"/>
        </div>
        <div class="col">
            <InputLabel value="Período" for="periodo"/>
            <div>
                <label class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="radios-inline" value="Seca"
                           v-model="form.periodo">
                    <span class="form-check-label">Seca</span>
                </label>
                <label class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="radios-inline" value="Chuva"
                           v-model="form.periodo">
                    <span class="form-check-label">Chuva</span>
                </label>
            </div>
            <InputError :message="form.errors.periodo"/>
        </div>
    </div>
    <div class="row mb-4">
        <div class="col">
            <InputLabel value="Data inicial" for="data_inicial"/>
            <input type="date" name="data_inicial" id="data_inicial" class="form-control" v-model="form.data_inicial">
            <InputError :message="form.errors.data_inicial"/>
        </div>
        <div class="col">
            <InputLabel value="Data final" for="data_final"/>
            <input type="date" name="data_final" id="data_final" class="form-control" v-model="form.data_final">
            <InputError :message="form.errors.data_final"/>
        </div>
    </div>
    <div class="row">
        <div class="col d-flex justify-content-end">
            <NavButton @click="salvarDadosGerais()" type-button="success" :icon="IconDeviceFloppy"
                       :title="form.id ? 'Alterar' : 'Salvar'"/>
        </div>
    </div>
</template>

<style scoped>

</style>
