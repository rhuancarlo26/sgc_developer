<script setup>
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import {computed} from "vue";
import NavButton from "@/Components/NavButton.vue";

const emit = defineEmits(['salvar-registro'])

const props = defineProps({
    form: {type: Object},
    campanhas: {type: Array},
    passagens: {type: Array}
})

const tipos = computed(() => {
    if (props.form.forma === 'Método') {
        return [
            'Busca Ativa',
            'Vestígios'
        ]
    } else if (props.form.forma === 'Armadilha') {
        return [
            'Câmera Trap',
            'Caixa de Areia'
        ]
    }
});

const changePassagem = () => {
    if (props.form.id_passagem) {
        const passagem = props.passagens.find(passagem => passagem.id === props.form.id_passagem);

        props.form.km = passagem.km
        props.form.latitude = passagem.latitude
        props.form.longitude = passagem.longitude
    }
}

const salvarRegistro = () => {
    emit('salvar-registro');
}
</script>

<template>
    <div class="row mb-4">
        <div class="col">
            <InputLabel value="ID registro" for="nome_id"/>
            <input type="text" name="nome_id" id="nome_id" class="form-control" v-model="form.nome_id" disabled/>
            <InputError :message="form.errors.nome_id"/>
        </div>
        <div class="col">
            <InputLabel value="Selecione a campanha" for="id_campanha"/>
            <select name="id_campanha" id="id_campanha" class="form-select" v-model="form.id_campanha">
                <option v-for="campanha in campanhas" :key="campanha.id" :value="campanha.id">{{ campanha.id }}</option>
            </select>
            <InputError :message="form.errors.id_campanha"/>
        </div>
        <div class="col">
            <InputLabel value="Grupo amostrado" for="id_grupo"/>
            <select name="id_grupo" id="id_grupo" class="form-select" v-model="form.id_grupo">
                <option value="1">Avifauna</option>
                <option value="2">Herpetofauna</option>
                <option value="3">Mastofauna</option>
            </select>
            <InputError :message="form.errors.id_grupo"/>
        </div>
    </div>
    <div class="row mb-4">
        <div class="col">
            <InputLabel value="Selecione a passagem de fauna" for="id_passagem"/>
            <select @change="changePassagem()" name="id_passagem" id="id_passagem" class="form-select"
                    v-model="form.id_passagem">
                <option v-for="passagem in passagens" :key="passagem.id" :value="passagem.id">{{ passagem.nome_id }}
                </option>
            </select>
            <InputError :message="form.errors.id_passagem"/>
        </div>
        <div class="col">
            <InputLabel value="Km" for="km"/>
            <input type="text" name="km" id="km" class="form-control" v-model="form.km" disabled/>
            <InputError :message="form.errors.km"/>
        </div>
        <div class="col">
            <InputLabel value="Selecione a forma" for="forma"/>
            <select name="forma" id="forma" class="form-select" v-model="form.forma">
                <option value="Método">Método</option>
                <option value="Armadilha">Armadilha</option>
            </select>
            <InputError :message="form.errors.forma"/>
        </div>
    </div>
    <div class="row mb-4">
        <div class="col">
            <InputLabel value="Latitude" for="latitude"/>
            <input type="text" name="latitude" id="latitude" class="form-control" v-model="form.latitude" disabled/>
            <InputError :message="form.errors.latitude"/>
        </div>
        <div class="col">
            <InputLabel value="longitude" for="longitude"/>
            <input type="text" name="longitude" id="longitude" class="form-control" v-model="form.longitude" disabled/>
            <InputError :message="form.errors.longitude"/>
        </div>
        <div class="col">
            <InputLabel value="Selecione o tipo" for="latitude"/>
            <select name="tipo" id="tipo" class="form-select" v-model="form.tipo">
                <option v-for="tipo in tipos" :key="tipo" :value="tipo">{{ tipo }}</option>
            </select>
            <InputError :message="form.errors.tipo"/>
        </div>
    </div>
    <div class="d-flex justify-content-end">
        <NavButton @click="salvarRegistro()" type-button="success" :title="form.id ? 'Alterar' : 'Salvar'"/>
    </div>
</template>

<style scoped>

</style>
