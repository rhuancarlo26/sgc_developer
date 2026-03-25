<script setup>

import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import NavButton from "@/Components/NavButton.vue";

const emit = defineEmits(['salvar-registro']);

const props = defineProps({
    form: {type: Object}
});

const salvarRegistro = () => {
    emit('salvar-registro');
}
</script>

<template>
    <div class="row mb-4">
        <div class="col">
            <InputLabel value="Sexo" for="sexo"/>
            <select name="sexo" id="sexo" class="form-select" v-model="form.sexo">
                <option value="Macho">Macho</option>
                <option value="Fêmea">Fêmea</option>
                <option value="Indefinido">Indefinido</option>
            </select>
            <InputError :message="form.errors.sexo"/>
        </div>
        <div class="col">
            <InputLabel value="Faixa etária" for="faixa_etaria"/>
            <select name="faixa_etaria" id="faixa_etaria" class="form-select" v-model="form.faixa_etaria">
                <option value="Jovem">Jovem</option>
                <option value="Adulto">Adulto</option>
                <option value="Indeterminado">Indeterminado</option>
            </select>
            <InputError :message="form.errors.faixa_etaria"/>
        </div>
        <div class="col">
            <InputLabel value="Nº de indivíduos do registro" for="n_individuos"/>
            <input type="text" name="n_individuos" id="n_individuos" class="form-control" v-model="form.n_individuos">
            <InputError :message="form.errors.n_individuos"/>
        </div>
    </div>
    <div class="row mb-4">
        <div class="col">
            <InputLabel value="Data do registro" for="data_registro"/>
            <input type="date" name="data_registro" id="data_registro" class="form-control"
                   v-model="form.data_registro">
            <InputError :message="form.errors.data_registro"/>
        </div>
        <div class="col">
            <InputLabel value="Hora do registro" for="hora_registro"/>
            <input type="time" name="hora_registro" id="hora_registro" class="form-control"
                   v-model="form.hora_registro">
            <InputError :message="form.errors.hora_registro"/>
        </div>
    </div>
    <div class="d-flex justify-content-end">
        <NavButton @click="salvarRegistro()" type-button="success" :title="form.id ? 'Alterar' : 'Salvar'"/>
    </div>
</template>

<style scoped>

</style>
