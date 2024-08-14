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

const modalVisualizarACA = ref();
const aca = ref({});

const abrirModal = (item) => {
    aca.value = item;

    modalVisualizarACA.value.getBsModal().show();
}

defineExpose({abrirModal});
</script>

<template>
    <Modal ref="modalVisualizarACA" title="Atestado de Conformidade Ambiental"
           modal-dialog-class="modal-xl">
        <template #body>
            <div class="row mb-4">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table card-table table-bordered table-hover">
                            <tbody>
                            <tr>
                                <th>ID ACA</th>
                                <td>{{ aca.nome_id }}</td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    {{
                                        `A ${aca.servico?.contrato?.contratada}, contrato ${aca.servico?.contrato?.numero_contrato}, cujo objeto é ${aca.servico?.contrato?.objeto}
              atesta que a empresa/consórcio ${aca.lote?.empresa}, contrato ${aca.lote?.num_contrato}, Lote
              ${aca.lote?.nome_id}, apresentou a seguinte relação de Registros de Não Conformidades (RNC) atendidos,
              conforme consta na tabela a seguir:`
                                    }}
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="row mb-4">
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
                                <th>Empresa / Consórcio</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="rnc in aca.rncs" :key="rnc.id">
                                <td>{{ rnc.nome_id }}</td>
                                <td>{{ rnc.nome_id }}</td>
                                <td>{{ rnc.intensidade }}</td>
                                <td>{{ dateTimeFormat(rnc.data_ocorrencia) }}</td>
                                <td>-</td>
                                <td>-</td>
                                <td>{{ rnc.prazo }}</td>
                                <td>{{ rnc.lote?.nome_id }}</td>
                                <td>{{ rnc.lote?.empresa }}</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col d-flex justify-content-end">
                    <span><strong>Data ACA: </strong>{{ dateTimeFormat(aca.data_aca) }}</span>
                </div>
            </div>
        </template>
        <template #footer>
        </template>
    </Modal>
</template>
