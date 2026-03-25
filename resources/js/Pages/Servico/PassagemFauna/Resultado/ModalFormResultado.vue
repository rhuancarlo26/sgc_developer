<script setup>
import Modal from "@/Components/Modal.vue";
import {ref} from "vue";
import {router, useForm} from "@inertiajs/vue3";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import NavButton from "@/Components/NavButton.vue";
import Table from "@/Components/Table.vue";
import {dateTimeFormat} from "@/Utils/DateTimeUtils.js";
import {IconTrash} from "@tabler/icons-vue";

const props = defineProps({
    contrato: {type: Object},
    servico: {type: Object},
    campanhas: {type: Array}
});

const modalFormResultado = ref();
const form = useForm({
    id: null,
    id_servico: null,
    nome: null,
    campanha: null,
    campanhas_selecionadas: []
});

const reset = () => {
    form.id = null;
    form.nome = null;
    form.campanha = null;
    form.campanhas_selecionadas = [];
}

const abrirModal = (item) => {
    reset();

    if (item) {
        form.id = item.id;
        form.nome = item.nome;

        form.campanhas_selecionadas = item.campanhas
    }

    modalFormResultado.value.getBsModal().show();
}

const adicionarCampanha = () => {
    const existe = form.campanhas_selecionadas.find(campanha => campanha.id === form.campanha.id);
    if (!existe) {
        form.campanhas_selecionadas.push(form.campanha);
        form.campanha = null;
    }
}

const removerCampanha = (id_campanha) => {
    form.campanhas_selecionadas.splice(form.campanhas_selecionadas.findIndex(obj => obj.id === id_campanha), 1);
}

const salvarResultado = () => {
    form.id_servico = props.servico.id;

    const url = form.id ? 'update' : 'store';

    form.post(route('contratos.contratada.servicos.passagem_fauna.resultado.' + url, {
        contrato: props.contrato.id,
        servico: props.servico.id
    }), {
        onSuccess: () => modalFormResultado.value.getBsModal().hide()
    });
}

defineExpose({abrirModal});
</script>

<template>
    <Modal ref="modalFormResultado" title="Cadastrar resultado" modal-dialog-class="modal-xl">
        <template #body>
            <div class="row mb-4">
                <div class="col">
                    <InputLabel value="ID resultado" for="id"/>
                    <input class="form-control" type="text" name="id" id="id" :value="form.id" disabled>
                    <InputError :message="form.errors.id"/>
                </div>
                <div class="col">
                    <InputLabel value="Data cadastro" for="created_at"/>
                    <input class="form-control" type="date" name="created_at" id="created_at"
                           :value="form.created_at ?? new Date().toISOString().substr(0, 10)"
                           disabled>
                    <InputError :message="form.errors.created_at"/>
                </div>
            </div>
            <div class="row mb-4">
                <div class="col">
                    <InputLabel value="Nome do resultado" for="nome"/>
                    <input class="form-control" type="text" name="nome" id="nome" v-model="form.nome">
                    <InputError :message="form.errors.nome"/>
                </div>
                <div class="col">
                    <InputLabel value="Selecionar campanha" for="id_campanha"/>
                    <div class="row g-2">
                        <div class="col">
                            <select class="form-select" name="id_campanha" id="id_campanha" v-model="form.campanha">
                                <option v-for="campanha in campanhas" :key="campanha.id" :value="campanha">
                                    {{ campanha.id }}
                                </option>
                            </select>
                        </div>
                        <div class="col-auto">
                            <NavButton @click="adicionarCampanha()" title="Adicionar" type-button="success"/>
                        </div>
                    </div>
                    <InputError :message="form.errors.id_campanha"/>
                </div>
            </div>
            <div class="row mb-4">
                <div class="col">
                    <InputError :message="form.errors.campanhas_selecionadas"/>
                    <Table :columns="['Campanha', 'Ação']" :records="{data: form.campanhas_selecionadas, links: []}"
                           table-class="table-hover">
                        <template #body="{item}">
                            <tr class="cursor-pointer">
                                <td>{{ item.id }}</td>
                                <td>
                                    <NavButton @click="removerCampanha(item.id)" class="btn-icon" :icon="IconTrash"
                                               type-button="danger"/>
                                </td>
                            </tr>
                        </template>
                    </Table>
                </div>
            </div>
            <div class="d-flex justify-content-end">
                <NavButton @click="salvarResultado()" type-button="success" :title="form.id ? 'Alterar' : 'Salvar'"/>
            </div>
        </template>
        <template #footer>
        </template>
    </Modal>
</template>
