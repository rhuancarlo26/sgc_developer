<script setup>
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import NavButton from "@/Components/NavButton.vue";

const emit = defineEmits(['salvar-registro']);

const props = defineProps({
    form: {type: Object},
    status_conservacoes: {type: Array}
});

const salvarRegistro = () => {
    emit('salvar-registro');
}
</script>

<template>
    <div class="row mb-4">
        <div class="col">
            <InputLabel value="Federal" for="id_status_conservacao_federal"/>
            <select name="id_status_conservacao_federal" id="id_status_conservacao_federal" class="form-select"
                    v-model="form.id_status_conservacao_federal">
                <option v-for="status in [0,6,8,9].map(index => status_conservacoes[index])" :key="status.id"
                        :value="status.id">
                    {{ status.sigla }} -
                    {{ status.nome }}
                </option>
            </select>
            <InputError :message="form.errors.id_status_conservacao_federal"/>
        </div>
        <div class="col">
            <InputLabel value="IUCN" for="id_status_conservacao_iucn"/>
            <select name="id_status_conservacao_iucn" id="id_status_conservacao_iucn" class="form-select"
                    v-model="form.id_status_conservacao_iucn">
                <option v-for="status in status_conservacoes.filter((_,index) => index != 0)" :key="status.id"
                        :value="status.id">{{ status.sigla }} -
                    {{ status.nome }}
                </option>
            </select>
            <InputError :message="form.errors.id_status_conservacao_iucn"/>
        </div>
    </div>
    <div class="d-flex justify-content-end">
        <NavButton @click="salvarRegistro()" type-button="success" :title="form.id ? 'Alterar' : 'Salvar'"/>
    </div>
</template>

<style scoped>

</style>
