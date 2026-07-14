<script setup>
import Modal from "@/Components/Modal.vue";
import {ref} from "vue";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import {router, useForm} from "@inertiajs/vue3";
import NavButton from "@/Components/NavButton.vue";
import {useToast} from "vue-toastification";
import {dateTimeFormat} from "@/Utils/DateTimeUtils.js";
import {IconEye, IconTrash} from "@tabler/icons-vue";
import Table from "@/Components/Table.vue";

const props = defineProps({
    contrato: {type: Object},
    servico: {type: Object}
});

const modalAdicionarRET = ref();
const abio = ref({});
const form = useForm({
    id_abio: null,
    arquivo: null,
});

const abrirModal = (item) => {
    abio.value = item;

    modalAdicionarRET.value.getBsModal().show();
}

const saveRET = () => {
    form.id_abio = abio.value.id;

    form.post(route('contratos.contratada.servicos.passagem_fauna.configuracao.vincular_abio.store_ret', {
        contrato: props.contrato.id,
        servico: props.servico.id
    }), {
        onSuccess: () => modalAdicionarRET.value.getBsModal().hide()
    })
}

const excluirAbioRet = (item) => {
    form.delete(route('contratos.contratada.servicos.passagem_fauna.configuracao.vincular_abio.delete_ret', {
        contrato: props.contrato.id,
        servico: props.servico.id,
        abio_ret: item.id
    }), {
        onSuccess: () => modalAdicionarRET.value.getBsModal().hide()
    });
}

defineExpose({abrirModal});
</script>

<template>
    <Modal ref="modalAdicionarRET" title="Adicionar RET" modal-dialog-class="modal-xl">
        <template #body>
            <div v-if="!abio.abio_ret">
                <div class="row mb-4">
                    <div class="col">
                        <InputLabel value="Buscar arquivo" for="arquivo"/>
                        <div class="row g-2">
                            <div class="col">
                                <input @input="form.arquivo = $event.target.files[0]" type="file"
                                       accept="application/pdf"
                                       name="arquivo" id="arquivo"
                                       class="form-control">
                            </div>
                            <div class="col-auto">
                                <NavButton @click="saveRET()" class="nav-item"
                                           type-button="success" title="Salvar"
                                />
                            </div>
                        </div>
                        <InputError :message="form.errors.arquivo"/>
                    </div>
                </div>
            </div>
            <div v-else>
                <Table :columns="['Arquivo', 'Data inclusão', 'Ação']"
                       :records="{ data: [abio.abio_ret], links: [] }" table-class="table-hover">
                    <template #body="{item}">
                        <tr class="cursor-pointer">
                            <td>{{ item.nome }}</td>
                            <td>{{ dateTimeFormat(item.created_at) }}</td>
                            <td>
                                <NavButton @click="excluirAbioRet(item)" :icon="IconTrash" type-button="danger"
                                           class="btn-icon"/>
                            </td>
                        </tr>
                    </template>
                </Table>
            </div>
        </template>
        <template #footer>
        </template>
    </Modal>
</template>
