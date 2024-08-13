<script setup>
import {ref} from "vue";
import Modal from "@/Components/Modal.vue";
import {dateTimeFormat} from "@/Utils/DateTimeUtils.js";
import Table from "@/Components/Table.vue";

const modalRef = ref();
const destinacao = ref(null);
const abrirModal = (item) => {
    destinacao.value = item;
    modalRef.value.getBsModal().show();
}

defineExpose({abrirModal});
</script>

<template>
    <Modal ref="modalRef" title="Visualizar Controle de Pilha" modal-dialog-class="modal-xl">
        <template #body>
            <div v-if="destinacao">
                <p class="row">
                    <span class="col-6">ID Codigo: <b>{{ destinacao.chave }}</b></span>
                    <span class="col-6">Data de Cadastro: <b>{{ dateTimeFormat(destinacao.dt_envio) }}</b></span>
                </p>
                <div class="row mb-2">
                    <div class="col-12">
                        <Table :columns="['Pilhas', 'Tipo de Pilha', 'Espécie', 'Volume (m³)', 'Nº ASV']" :records="{ data: destinacao.pilhas, links: [] }">
                            <template #body="{item}">
                                <tr>
                                    <td>{{ item.chave }}</td>
                                    <td>{{ item.tipo_pilha_label ?? '-' }}</td>
                                    <td>{{ item.corte_especie?.nome ?? '-' }}</td>
                                    <td>{{ item.volume ?? '-' }}</td>
                                    <td>{{ item.licenca?.numero_licenca ?? '-' }}</td>
                                </tr>
                            </template>
                        </Table>
                    </div>
                </div>
                <p class="row">
                    <span class="col-12">Uso da Madeira: <b>{{destinacao.uso_da_madeira}}</b></span>
                </p>
                <p class="row">
                    <span class="col-12">Destinatário: <b>{{destinacao.destinatario}}</b></span>
                </p>
                <p class="row">
                    <span class="col-12">Observação: <b>{{destinacao.observacao}}</b></span>
                </p>
            </div>
        </template>
        <template #footer>
            <button @click="modalRef.getBsModal().hide()" type="button" class="btn btn-secondary">Fechar</button>
        </template>
    </Modal>
</template>
