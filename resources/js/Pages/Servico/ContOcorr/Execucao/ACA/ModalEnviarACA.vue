<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import Navbar from "../../Navbar.vue";
import {Head, Link, router, useForm} from "@inertiajs/vue3";
import ModelSearchFormAllColumns from "@/Components/ModelSearchFormAllColumns.vue";
import Table from "@/Components/Table.vue";
import NavButton from "@/Components/NavButton.vue";
import LinkConfirmation from "@/Components/LinkConfirmation.vue";
import {ref} from "vue";
import {IconEye} from "@tabler/icons-vue";
import {IconPlus} from "@tabler/icons-vue";
import {IconPencil} from "@tabler/icons-vue";
import {IconTrash} from "@tabler/icons-vue";
import NavLink from "@/Components/NavLink.vue";
import {IconMap} from "@tabler/icons-vue";
import {dateTimeFormat} from "@/Utils/DateTimeUtils";
import Modal from "@/Components/Modal.vue";
import {usePage} from "@inertiajs/vue3";
import {IconDeviceFloppy} from "@tabler/icons-vue";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";

const modalEnviarACA = ref();

const props = defineProps({
    contrato: {type: Object},
    servico: {type: Object},
    acas: {type: Array}
});

const form = useForm({
    acas: []
});

const abrirModal = () => {
    modalEnviarACA.value.getBsModal().show();
}

const enviarACA = () => {
    form.post(route('contratos.contratada.servicos.cont_ocorrencia.execucao.aca.enviar_aca', {
        contrato: props.contrato.id,
        servico: props.servico.id
    }), {
        onSuccess: () => modalEnviarACA.value.getBsModal().hide()
    });
}

defineExpose({abrirModal});
</script>

<template>
    <Modal ref="modalEnviarACA" title="Cadastro de Atestados de Conformidade Ambiental" modal-dialog-class="modal-xl">
        <template #body>
            <div class="row mb-4">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table card-table table-bordered table-hover">
                            <thead>
                            <tr>
                                <th></th>
                                <th>ID ACA</th>
                                <th>Data ACA</th>
                                <th>Relação de RNC's</th>
                                <th>Lote</th>
                                <th>Construtora</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="aca in acas" :key="aca.id">
                                <td>
                                    <label class="form-check">
                                        <input class="form-check-input" type="checkbox" :value="aca"
                                               v-model="form.acas">
                                        <span class="form-check-label"> {{ aca.id }} </span>
                                    </label>
                                </td>
                                <td>{{ aca.nome_id }}</td>
                                <td>{{ dateTimeFormat(aca.data_aca) }}</td>
                                <td>
                                    <span v-for="rnc in aca.rncs" :key="rnc.id"
                                          class="badge bg-warning text-white m-1">
                                    {{ rnc.nome_id }}
                                </span>
                                </td>
                                <td>{{ aca.lote?.nome_id }}</td>
                                <td>{{ aca.lote?.empresa }}</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col d-flex justify-content-end">
                    <NavButton @click="enviarACA()" type-button="success" :icon="IconDeviceFloppy"
                               title="Enviar ACA's"/>
                </div>
            </div>
        </template>
        <template #footer>
        </template>
    </Modal>
</template>
