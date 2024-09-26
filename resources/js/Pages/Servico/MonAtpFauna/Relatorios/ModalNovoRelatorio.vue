<script setup>

import {onMounted, ref, watch} from "vue";
import {Link, router, useForm} from "@inertiajs/vue3";
import Modal from "@/Components/Modal.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import LinkConfirmation from "@/Components/LinkConfirmation.vue";
import {IconTrash} from "@tabler/icons-vue";
import Table from "@/Components/Table.vue";
import {dateTimeFormat} from "../../../../Utils/DateTimeUtils.js";

const props = defineProps({
    resultados: {type: Array},
    servico: {type: Object},
})

const form = useForm({
    id: null,
    nome_relatorio: null,
    fk_resultado: null,
    sobre_relatorio: null,
});

const modalRef = ref();
const onSuccess = () => {
    modalRef.value.getBsModal().hide();
    form.reset();
}
const save = () => {
    form.transform((data) => ({
        ...data,
        fk_servico: props.servico.id
    }));

    if (form.id !== null) {
        form.patch(route('contratos.contratada.servicos.mon_atp_fauna.relatorios.update'), {
            preserveState: true,
            onSuccess
        })
        return
    }

    form.post(route('contratos.contratada.servicos.mon_atp_fauna.relatorios.store'), {
        preserveState: true,
        onSuccess
    })
}

const abrirModal = async (item = null) => {
    form.reset()
    if (item !== null) {
        Object.assign(form, item)
    }
    modalRef.value.getBsModal().show();
}

defineExpose({abrirModal});

</script>

<template>
    <form @submit.prevent="save">
        <Modal ref="modalRef" title="Resultado" modal-dialog-class="modal-xl">
            <template #body>
                <div class="row row-gap-2 mb-2">
                    <div class="col-lg-6">
                        <InputLabel value="Nome do relatório" for="nome_relatorio"/>
                        <input v-model="form.nome_relatorio" type="text" id="nome_relatorio" class="form-control"/>
                        <InputError :message="form.errors.nome_relatorio"/>
                    </div>
                    <div class="col-lg-12">
                        <Table
                            :columns="['Nome do Resultado', 'Campanhas', 'Data']"
                            :records="{ data: resultados, links: []}" table-class="table-hover">
                            <template #body="{ item }">
                                <tr>
                                    <td>
                                        <span class="d-flex align-items-center gap-2">
                                            <input type="radio" :id="item.id" :value="item.id" v-model="form.fk_resultado" >
                                            <label :for="item.id">{{ item.nome_resultado }}</label>
                                        </span>
                                    </td>
                                    <td class="d-flex flex-wrap">
                                      <span v-for="lista in item.campanhas.split(',')" class="badge badge-warning m-1">
                                        {{ lista }}
                                      </span>
                                    </td>
                                    <td>{{ dateTimeFormat(item.created_at) }}</td>
                                </tr>
                            </template>
                        </Table>
                    </div>
                    <div class="col-12">
                        <InputLabel>Sobre o Relatório:</InputLabel>
                        <textarea v-model="form.sobre_relatorio" class="form-control" rows="3"></textarea>
                    </div>
                </div>
            </template>
            <template #footer>
                <button @click="modalRef.getBsModal().hide()" type="button" class="btn btn-secondary">Fechar</button>
                <button type="submit" class="btn btn-success">Salvar</button>
            </template>
        </Modal>
    </form>
</template>
