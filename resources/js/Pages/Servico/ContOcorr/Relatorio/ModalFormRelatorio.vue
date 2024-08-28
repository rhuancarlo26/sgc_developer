<script setup>
import Modal from "@/Components/Modal.vue";
import {useForm} from "@inertiajs/vue3";
import {ref} from "vue";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import {IconDeviceFloppy, IconTrash} from "@tabler/icons-vue";
import NavButton from "@/Components/NavButton.vue";
import {dateTimeFormat} from "@/Utils/DateTimeUtils.js";
import Table from "@/Components/Table.vue";

const props = defineProps({
    contrato: {type: Object},
    servico: {type: Object},
    resultados: {type: Array}
})

const modalFormRelatorio = ref();

const form = useForm({
    id: null,
    id_servico: props.servico.id,
    nome_relatorio: null,
    id_resultado: null,
    sobre_relatorio: null
});

const abrirModal = (item) => {
    form.reset();
    
    if (item) {
        Object.assign(form, item);
    }

    modalFormRelatorio.value.getBsModal().show();
}

const salvarRelatorio = () => {
    const url = form.id ? 'update' : 'store'
    form.post(route('contratos.contratada.servicos.cont_ocorrencia.relatorio.' + url, {
        contrato: props.contrato.id,
        servico: props.servico.id
    }), {
        onSuccess: () => modalFormRelatorio.value.getBsModal().hide()
    });
}


defineExpose({abrirModal});
</script>

<template>
    <Modal ref="modalFormRelatorio" title="Cadastro relatorio" modal-dialog-class="modal-xl">
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
                    <InputLabel value="Selecionar resultado" for="id_resultado"/>
                    <InputError :message="form.errors.id_resultado"/>
                    <div class="table-responsive">
                        <table class="table card-table table-bordered">
                            <thead>
                            <tr>
                                <th>Nome do resultado</th>
                                <th>Período</th>
                                <th>Data início</th>
                                <th>Data final</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="resultado in resultados" :key="resultado.id">
                                <td>
                                    <label class="form-check">
                                        <input class="form-check-input" type="radio" :name="'resultado-' + resultado.id"
                                               :id="'resultado-' + resultado.id" :value="resultado.id"
                                               v-model="form.id_resultado">
                                        <span class="form-check-label">{{ resultado.nome }}</span>
                                    </label>
                                </td>
                                <td>{{ resultado.periodo }}</td>
                                <td>{{ dateTimeFormat(resultado.dt_inicio) }}</td>
                                <td>{{ dateTimeFormat(resultado.dt_final) }}</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="row mb-4">
                <div class="col">
                    <InputLabel value="Sobre o relatório" for="sobre_relatorio"/>
                    <textarea name="" id="" class="form-control" rows="5" v-model="form.sobre_relatorio"/>
                    <InputError :message="form.errors.sobre_relatorio"/>
                </div>
            </div>
            <div class="row">
                <div class="col d-flex justify-content-end">
                    <NavButton @click="salvarRelatorio()" type-button="success" :icon="IconDeviceFloppy"
                               :title="form.id ? 'Alterar' : 'Salvar'"/>
                </div>
            </div>
        </template>
        <template #footer>
        </template>
    </Modal>
</template>
