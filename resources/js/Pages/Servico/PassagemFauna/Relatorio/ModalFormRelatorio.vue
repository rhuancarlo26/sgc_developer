<script setup>
import Modal from "@/Components/Modal.vue";
import {ref} from "vue";
import InputLabel from "@/Components/InputLabel.vue";
import {useForm} from "@inertiajs/vue3";
import InputError from "@/Components/InputError.vue";
import Table from "@/Components/Table.vue";
import NavButton from "@/Components/NavButton.vue";
import {dateTimeFormat} from "@/Utils/DateTimeUtils.js";

const modalFormRelatorio = ref();
const props = defineProps({
    contrato: {type: Object},
    servico: {type: Object},
    resultados: {type: Array}
});

const form = useForm({
    id: null,
    id_servico: null,
    id_resultado: null,
    fk_status: 1,
    nome_relatorio: null,
    sobre_relatorio: null
});

const abrirModal = (item) => {
    form.reset();

    if (item) {
        Object.assign(form, item)
    }

    modalFormRelatorio.value.getBsModal().show();
}

const salvar = () => {
    form.id_servico = props.servico.id;

    const url = form.id ? 'update' : 'store';

    form.post(route('contratos.contratada.servicos.passagem_fauna.relatorio.' + url, {
        contrato: props.contrato.id,
        servico: props.servico.id
    }), {
        onSuccess: () => modalFormRelatorio.value.getBsModal().hide()
    });
}

defineExpose({abrirModal});
</script>

<template>
    <Modal ref="modalFormRelatorio" title="Cadastrar relatório" modal-dialog-class="modal-xl">
        <template #body>
            <div class="row mb-4">
                <div class="col">
                    <InputLabel value="Nome do relatório" for="nome_relatorio"/>
                    <input type="text" name="nome_relatorio" id="nome_relatorio" class="form-control"
                           v-model="form.nome_relatorio">
                    <InputError :message="form.errors.nome_relatorio"/>
                </div>
            </div>
            <div class="row mb-4">
                <div class="col">
                    <Table
                        :columns="['Nome resultado', 'Campanhas', 'Data']"
                        :records="{data: resultados, links: []}" table-class="table-hover">
                        <template #body="{ item }">
                            <tr>
                                <td>
                                    <label class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="radios-inline"
                                               :value="item.id" v-model="form.id_resultado">
                                        <span class="form-check-label">{{ item.nome }}</span>
                                    </label>
                                </td>
                                <td>
                                    <span v-for="campanha in item.campanhas" :key="campanha.id"
                                          class="badge bg-warning text-white m-1">
                                    {{ campanha.id }}
                                </span>
                                </td>
                                <td>{{ dateTimeFormat(item.created_at) }}</td>
                            </tr>
                        </template>
                    </Table>
                </div>
            </div>
            <div class="row mb-4">
                <div class="col">
                    <InputLabel value="Sobre o relatório" for="sobre_relatorio"/>
                    <textarea class="form-control" name="sobre_relatorio" id="sobre_relatorio" rows="5"
                              v-model="form.sobre_relatorio"></textarea>
                    <InputError :message="form.errors.sobre_relatorio"/>
                </div>
            </div>
            <div class="d-flex justify-content-end">
                <NavButton @click="salvar()" type-button="success" :title="form.id ? 'Alterar' : 'Salvar'"/>
            </div>
        </template>
        <template #footer>
        </template>
    </Modal>
</template>
