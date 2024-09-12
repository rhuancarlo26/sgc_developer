<script setup>
import InputLabel from "@/Components/InputLabel.vue";
import NavButton from "@/Components/NavButton.vue";

const emit = defineEmits(['salvar-registro'])

const props = defineProps({
    form: {type: Object}
})

const salvarRegistro = () => {
    emit('salvar-registro');
}
</script>

<template>
    <div class="row mb-4">
        <div class="col">
            <InputLabel value="Observações" for="obs"/>
            <textarea class="form-control" name="obs" id="obs" rows="5" v-model="form.obs"></textarea>
        </div>
    </div>
    <div class="d-flex justify-content-end">
        <NavButton @click="salvarRegistro()" type-button="success" :title="form.id ? 'Alterar' : 'Salvar'"/>
    </div>
</template>

<style scoped>

</style>
