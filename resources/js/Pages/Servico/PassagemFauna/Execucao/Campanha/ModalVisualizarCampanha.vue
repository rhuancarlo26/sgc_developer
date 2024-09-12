<script setup>
import Modal from "@/Components/Modal.vue";
import {ref} from "vue";
import {dateTimeFormat} from "../../../../../Utils/DateTimeUtils.js";
import LinkConfirmation from "@/Components/LinkConfirmation.vue";
import {IconTrash} from "@tabler/icons-vue";
import {Link} from "@inertiajs/vue3";
import Table from "@/Components/Table.vue";

const props = defineProps({
    contrato: {type: Object},
    servico: {type: Object}
});

const modalVisualizarCampanha = ref();
const campanha = ref({});

const abrirModal = (item) => {
    campanha.value = item;

    modalVisualizarCampanha.value.getBsModal().show();
}


defineExpose({abrirModal});
</script>

<template>
    <Modal ref="modalVisualizarCampanha" :title="'Visualização da campanha ' + campanha.id"
           modal-dialog-class="modal-xl">
        <template #body>
            <div class="mb-4">
                <h2>Dados Gerais</h2>

                <div class="row mb-4">
                    <span class="col"><strong>Data inicial: </strong>{{ dateTimeFormat(campanha.data_inicial) }}</span>
                    <span class="col"><strong>Data final: </strong>{{ dateTimeFormat(campanha.data_final) }}</span>
                    <span class="col"><strong>Período: </strong>{{ campanha.periodo }}</span>
                </div>
            </div>
            <div class="mb-4">
                <h2>ABIO's vinculados</h2>

                <Table :columns="['Abio\'s ']"
                       :records="{data: campanha.abios?.map(abio => abio.abio?.licenca?.numero_licenca), links: []}"
                       table-class="table-hover">
                    <template #body="{ item }">
                        <tr>
                            <td>{{ item }}</td>
                        </tr>
                    </template>
                </Table>
            </div>
            <div class="mb-4">
                <h2>Profissionais vinculados</h2>

                <Table :columns="['Equipe técnica']" :records="{data: campanha.rhs, links: []}"
                       table-class="table-hover">
                    <template #body="{ item }">
                        <tr>
                            <td>{{ item.servico_rh?.rh?.nome }}</td>
                        </tr>
                    </template>
                </Table>
            </div>
            <div class="mb-4">
                <h2>RET's vinculadas</h2>

                <Table :columns="['Número Abio', 'RET']" :records="{data: campanha.rets, links: []}"
                       table-class="table-hover">
                    <template #body="{ item }">
                        <tr>
                            <td>{{ item.ret?.abio?.licenca?.numero_licenca }}</td>
                            <td>{{ item.ret?.nome }}</td>
                        </tr>
                    </template>
                </Table>
            </div>
            <div>
                <h2>Observações</h2>

                <span>{{ campanha.obs }}</span>
            </div>
        </template>
        <template #footer>
        </template>
    </Modal>
</template>
