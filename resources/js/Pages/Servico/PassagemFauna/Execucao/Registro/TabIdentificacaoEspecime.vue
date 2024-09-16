<script setup>
import InputLabel from "@/Components/InputLabel.vue";
import {computed} from "vue";
import InputError from "@/Components/InputError.vue";
import NavButton from "@/Components/NavButton.vue";

const emit = defineEmits(['salvar-registro'])

const props = defineProps({
    form: {type: Object}
})

const classes = computed(() => {
    if (props.form.id_grupo) {
        if (props.form.id_grupo == '1') {
            return [
                'Aves'
            ];
        } else if (props.form.id_grupo == '2') {
            return [
                'Anfíbios',
                'Répteis'
            ];
        } else if (props.form.id_grupo == '3') {
            return [
                'Mamíferos'
            ];
        }
    }
})

const salvarRegistro = () => {
    emit('salvar-registro');
}
</script>

<template>
    <div class="row mb-4">
        <div class="col">
            <InputLabel value="Classe" for="classe"/>
            <select name="id_grupo" id="id_grupo" class="form-select" v-model="form.classe">
                <option v-for="classe in classes" :key="classe" :value="classe">{{ classe }}</option>
            </select>
            <InputError :message="form.errors.classe"/>
        </div>
        <div class="col">
            <InputLabel value="Ordem" for="ordem"/>
            <input type="text" name="ordem" id="ordem" class="form-control" v-model="form.ordem">
            <InputError :message="form.errors.ordem"/>
        </div>
        <div class="col">
            <InputLabel value="Família" for="familia"/>
            <input type="text" name="familia" id="familia" class="form-control" v-model="form.familia">
            <InputError :message="form.errors.familia"/>
        </div>
    </div>
    <div class="row mb-4">
        <div class="col">
            <InputLabel value="Genero" for="genero"/>
            <input type="text" name="genero" id="genero" class="form-control" v-model="form.genero">
            <InputError :message="form.errors.genero"/>
        </div>
        <div class="col">
            <InputLabel value="Espécie" for="especie"/>
            <input type="text" name="especie" id="especie" class="form-control" v-model="form.especie">
            <InputError :message="form.errors.especie"/>
        </div>
        <div class="col">
            <InputLabel value="Nome comum" for="nome_comum"/>
            <input type="text" name="nome_comum" id="nome_comum" class="form-control" v-model="form.nome_comum">
            <InputError :message="form.errors.nome_comum"/>
        </div>
    </div>
    <div class="d-flex justify-content-end">
        <NavButton @click="salvarRegistro()" type-button="success" :title="form.id ? 'Alterar' : 'Salvar'"/>
    </div>
</template>

<style scoped>

</style>
