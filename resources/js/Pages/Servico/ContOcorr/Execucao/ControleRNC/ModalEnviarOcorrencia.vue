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

const modalEnviarOcorrencia = ref();

const props = defineProps({
    contrato: {type: Object},
    servico: {type: Object},
    ocorrencias: {type: Object}
});

const form = useForm({
    ocorrencias: []
});


const abrirModal = () => {
    modalEnviarOcorrencia.value.getBsModal().show();
}

const enviarOcorrencias = () => {
    form.post(route('contratos.contratada.servicos.cont_ocorrencia.execucao.controle_rnc.update', {
        contrato: props.contrato.id,
        servico: props.servico.id
    }), {
        onSuccess: () => modalEnviarOcorrencia.value.getBsModal().hide()
    });
}

defineExpose({abrirModal});
</script>

<template>
    <Modal ref="modalEnviarOcorrencia" title="Enviar ocorrência" modal-dialog-class="modal-xl">
        <template #body>
            <div class="row col mb-4">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table card-table table-bordered table-hover">
                            <thead>
                            <tr>
                                <th>ID ocorrência</th>
                                <th>Intensidade ocorrência</th>
                                <th>Tipo ocorrência</th>
                                <th>Data da ocorrência</th>
                                <th>Data fim</th>
                                <th>Ocorrência anterior</th>
                                <th>Prazo correção</th>
                                <th>Lote</th>
                                <th>Contrutora</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="item in ocorrencias" :key="item.id">
                                <td>
                                    <label class="form-check">
                                        <input class="form-check-input" type="checkbox" :value="item"
                                               v-model="form.ocorrencias">
                                        <span class="form-check-label"> {{ item.id }} </span>
                                    </label>
                                </td>
                                <td>{{ item.nome_id }}</td>
                                <td>{{ item.intensidade }}</td>
                                <td>{{ dateTimeFormat(item.data_ocorrencia) }}</td>
                                <td>-</td>
                                <td>-</td>
                                <td>{{ item.prazo }}</td>
                                <td>{{ item.lote?.nome_id }}</td>
                                <td>{{ item.lote?.empresa }}</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col d-flex justify-content-end">
                    <NavButton @click="enviarOcorrencias()" type-button="success" :icon="IconDeviceFloppy"
                               title="Enviar"/>
                </div>
            </div>
        </template>
        <template #footer>
        </template>
    </Modal>
</template>
