<script setup>
import Modal from "@/Components/Modal.vue";
import {computed, ref} from "vue";
import {useForm} from "@inertiajs/vue3";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import NavButton from "@/Components/NavButton.vue";
import {IconDeviceFloppy, IconPlus} from "@tabler/icons-vue";

const props = defineProps({
    contrato: {type: Object},
    servico: {type: Object}
})

const modalForm = ref();
const form = useForm({
    id: null,
    rodovia: null,
    uf: null,
    km: null,
    tipo_de_estrutura: null,
    classificacao: null,
    nome: null,
    dimensoes: null,
    latitude: null,
    longitude: null,
    observacao: null
});

const abrirModal = (item) => {
    form.reset();

    if (item) {
        Object.assign(form, item)
    }

    modalForm.value.getBsModal().show();
}

const classificacoes = computed(() => {
    if (form.tipo_de_estrutura === 'OAC') {
        return [
            'BSCC - Bueiro Simples Celular de Concreto',
            'BDCC - Bueiro Duplo Celular de Concreto',
            'BTCC - Bueiro Triplo Celular de Concreto',
            'BSTC - Bueiro Simples Tubular de Concreto',
            'BDTC - Bueiro Duplo Tubular de Concreto',
            'BTTC - Bueiro Triplo Tubular de Concreto',
            'Bueiro Metálico'
        ];
    }

    return [
        'Ponte',
        'Passagem Aérea'
    ];
})

const salvarPassagem = () => {
    form.post(route('contratos.contratada.servicos.passagem_fauna.configuracao.passagem_fauna.update', {
        contrato: props.contrato.id,
        servico: props.servico.id
    }), {
        onSuccess: () => modalForm.value.getBsModal().hide()
    })
}

defineExpose({abrirModal});
</script>

<template>
    <Modal ref="modalForm" :title="'Passagem de fauna ' + form.nome_id"
           modal-dialog-class="modal-xl">
        <template #body>
            <div class="row mb-4">
                <div class="col">
                    <InputLabel value="ID passagem de fauna" for="nome_id"/>
                    <input type="text" name="nome_id" id="nome_id" class="form-control" v-model="form.nome_id" disabled>
                    <InputError :message="form.errors.nome_id"/>
                </div>
                <div class="col">
                    <InputLabel value="Data" for="created_at"/>
                    <input type="datetime" name="created_at" id="created_at" class="form-control"
                           v-model="form.created_at"
                           disabled>
                    <InputError :message="form.errors.created_at"/>
                </div>
            </div>
            <div class="row mb-4">
                <div class="col">
                    <InputLabel value="Rodovia" for="rodovia"/>
                    <input type="text" name="rodovia" id="rodovia" class="form-control" v-model="form.rodovia">
                    <InputError :message="form.errors.rodovia"/>
                </div>
                <div class="col">
                    <InputLabel value="UF" for="uf"/>
                    <input type="text" name="uf" id="uf" class="form-control"
                           v-model="form.uf">
                    <InputError :message="form.errors.uf"/>
                </div>
                <div class="col">
                    <InputLabel value="KM" for="km"/>
                    <input type="text" name="km" id="km" class="form-control"
                           v-model="form.km">
                    <InputError :message="form.errors.km"/>
                </div>
            </div>
            <div class="row mb-4">
                <div class="col">
                    <InputLabel value="Tipo de estrutura" for="tipo_de_estrutura"/>
                    <select name="tipo_de_estrutura" id="tipo_de_estrutura" class="form-select"
                            v-model="form.tipo_de_estrutura">
                        <option value="OAE">OAE</option>
                        <option value="OAC">OAC</option>
                    </select>
                    <InputError :message="form.errors.tipo_de_estrutura"/>
                </div>
                <div class="col">
                    <InputLabel value="Classificação" for="classificacao"/>
                    <select name="classificacao" id="classificacao" class="form-select"
                            v-model="form.classificacao">
                        <option v-for="classificacao in classificacoes" :key="classificacao" :value="classificacao">
                            {{ classificacao }}
                        </option>
                    </select>
                    <InputError :message="form.errors.classificacao"/>
                </div>
                <div class="col">
                    <InputLabel value="Nome" for="nome"/>
                    <input type="text" name="nome" id="nome" class="form-control"
                           v-model="form.nome">
                    <InputError :message="form.errors.nome"/>
                </div>
            </div>
            <div class="row mb-4">
                <div class="col">
                    <InputLabel value="Dimensões (m²)" for="dimensoes"/>
                    <input type="text" name="dimensoes" id="dimensoes" class="form-control"
                           v-model="form.dimensoes">
                    <InputError :message="form.errors.dimensoes"/>
                </div>
                <div class="col">
                    <InputLabel value="Latitude" for="latitude"/>
                    <input type="text" name="latitude" id="latitude" class="form-control"
                           v-model="form.latitude">
                    <InputError :message="form.errors.latitude"/>
                </div>
                <div class="col">
                    <InputLabel value="Longitude" for="longitude"/>
                    <input type="text" name="longitude" id="longitude" class="form-control"
                           v-model="form.longitude">
                    <InputError :message="form.errors.longitude"/>
                </div>
            </div>
            <div class="row mb-4">
                <div class="col">
                    <InputLabel value="Observações" for="observacao"/>
                    <textarea name="observacao" id="observacao" class="form-control"
                              v-model="form.observacao" rows="5"></textarea>
                </div>
            </div>
            <div class="row">
                <div class="col d-flex justify-content-end">
                    <NavButton @click="salvarPassagem()" type-button="success" :icon="IconDeviceFloppy"
                               :title="form.id ? 'Alterar' : 'Salvar'"/>
                </div>
            </div>
        </template>
        <template #footer>
        </template>
    </Modal>
</template>
