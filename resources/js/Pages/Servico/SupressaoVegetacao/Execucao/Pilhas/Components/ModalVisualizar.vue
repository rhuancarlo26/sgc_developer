<script setup>
import {ref} from "vue";
import Modal from "@/Components/Modal.vue";
import {dateTimeFormat} from "@/Utils/DateTimeUtils.js";
import Table from "@/Components/Table.vue";

const modalRef = ref();
const pilha = ref(null);
const abrirModal = (item) => {
    pilha.value = item;
    modalRef.value.getBsModal().show();
}

defineExpose({abrirModal});
</script>

<template>
    <Modal ref="modalRef" title="Visualizar Controle de Pilha" modal-dialog-class="modal-xl">
        <template #body>
            <div v-if="pilha">
                <p class="row">
                    <span class="col-6">ID Codigo: <b>{{ pilha.chave }}</b></span>
                    <span class="col-6">Data de Cadastro: <b>{{ dateTimeFormat(pilha.created_at) }}</b></span>
                </p>
                <p class="row">
                    <span class="col-6">Área Supressão: <b>{{ pilha.area_supressao.chave }}</b></span>
                    <span class="col-6">Numero da ASV: <b>{{ pilha.licenca.numero_licenca }}</b></span>
                </p>
                <p class="row">
                    <span class="col-6">Pátio de Estocagem: <b>{{ pilha.patio.chave }}</b></span>
                    <span class="col-6">Tipo de Produto: <b>{{ pilha.produto.nome }}</b></span>
                </p>
                <p class="row">
                    <span class="col-6">Tipo de Pilha: <b>{{ pilha.tipo_pilha_label }}</b></span>
                    <span class="col-6">Volume (m³): <b>{{ pilha.volume }}</b></span>
                </p>
                <p class="row">
                    <span class="col-6">Espécies Ameaçadas/protegidas: <b>{{ pilha.corte_especie?.nome }}</b></span>
                    <span class="col-6">Quantidade de Indivíduos: <b>{{ pilha.nu_individuo }}</b></span>
                </p>
                <p class="row">
                    <span class="col-3">Latitude: <b>{{ pilha.latitude }}</b></span>
                    <span class="col-3">Longitude: <b>{{ pilha.longitude }}</b></span>
                </p>
                <p class="row">
                    <span class="col-12">Observação: <b>{{ pilha.observacao }}</b></span>
                </p>
            </div>
        </template>
        <template #footer>
            <button @click="modalRef.getBsModal().hide()" type="button" class="btn btn-secondary">Fechar</button>
        </template>
    </Modal>
</template>
