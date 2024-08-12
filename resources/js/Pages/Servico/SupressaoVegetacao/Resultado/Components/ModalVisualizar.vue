<script setup>
import {ref} from "vue";
import Modal from "@/Components/Modal.vue";
import {dateTimeFormat} from "@/Utils/DateTimeUtils.js";
import Table from "@/Components/Table.vue";

const modalRef = ref();
const supressao = ref(null);
const abrirModal = (item) => {
    supressao.value = item;
    modalRef.value.getBsModal().show();
}

defineExpose({abrirModal});
</script>

<template>
    <Modal ref="modalRef" title="Visualizar Área de Supressão" modal-dialog-class="modal-xl">
        <template #body>
            <div v-if="supressao">
                <p>ID Código: <span class="fw-bold">{{ supressao.chave }}</span></p>
                <div class="d-flex gap-4">
                    <p>Data Inicial: <span class="fw-bold">{{ dateTimeFormat(supressao.dt_inicial) }}</span></p>
                    <p>Data Final: <span class="fw-bold">{{ dateTimeFormat(supressao.dt_final) }}</span></p>
                </div>
                <p>Bioma: <span class="fw-bold">{{ supressao.bioma?.nome }}</span></p>
                <p>Fitofisionomia: <span class="fw-bold">{{ supressao.fitofisionomia }}</span></p>
                <p>Estágio Sucessional: <span class="fw-bold">{{ supressao.estagio_sucessional?.nome }}</span></p>
                <div class="d-flex gap-4">
                    <p>Area em APP: <span class="fw-bold">{{ supressao.area_em_app }}</span></p>
                    <p>Área fora APP: <span class="fw-bold">{{ supressao.area_fora_app }}</span></p>
                    <p>Área Total: <span class="fw-bold">{{ supressao.area_total }}</span></p>
                </div>
                <p>Nº ASV: <span class="fw-bold">{{ supressao.licenca.numero_licenca }}</span></p>
                <p>Observação: <span class="fw-bold">{{ supressao.observacao }}</span></p>

                <Table
                    :columns="['Nome científica', 'Nome popular', 'N° de Indivíduos', 'Compensação', 'Legislação']"
                    :records="{ data: supressao.corte_especies, links: [] }"
                    table-class="table-hover">
                    <template #body="{ item, key }">
                        <tr>
                            <td>{{ item.nome }}</td>
                            <td>{{ item.nome_popular }}</td>
                            <td>{{ item.qtd_corte }}</td>
                            <td>{{ item.compensacao }}</td>
                            <td>{{ item.legislacao }}</td>
                        </tr>
                    </template>
                </Table>

            </div>
        </template>
        <template #footer>
            <button @click="modalRef.getBsModal().hide()" type="button" class="btn btn-secondary">Fechar</button>
        </template>
    </Modal>
</template>
