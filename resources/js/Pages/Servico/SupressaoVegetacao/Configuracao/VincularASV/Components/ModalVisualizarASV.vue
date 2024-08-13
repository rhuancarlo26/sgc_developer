<script setup>
import { ref } from "vue";
import Modal from "@/Components/Modal.vue";
import {dateTimeFormat} from "@/Utils/DateTimeUtils.js";

const licenca = ref({});
const modalVisualizarASVRef = ref();
const abrirModal = (item) => {
    licenca.value = item;
    modalVisualizarASVRef.value.getBsModal().show();
}

defineExpose({ abrirModal });
</script>

<template>
    <Modal ref="modalVisualizarASVRef" title="Detalhes" modal-dialog-class="modal-xl">
        <template #body>
            <div class="row mb-4">
                <span class="col"><strong>Número: </strong>{{ licenca.numero_licenca ?? '-' }}</span>
                <span class="col"><strong>Data de emissão: </strong>{{ licenca.data_emissao ? dateTimeFormat(licenca.data_emissao) : '-' }}</span>
                <span class="col"><strong>Data da vencimento: </strong>{{ licenca.vencimento ? dateTimeFormat(licenca.vencimento) : '-' }}</span>
            </div>
            <div class="row mb-4">
                <span class="col"><strong>Número SEI: </strong>{{ licenca.numero_sei ?? '-'}}</span>
                <span class="col"><strong>Processo DNIT: </strong>{{ licenca.processo_dnit ?? '-'}}</span>
                <span class="col"><strong>Emissor: </strong>{{ licenca.emissor ?? '-'}}</span>
            </div>
            <div class="row mb-4">
                <span class="col"><strong>Início subtrecho: </strong>{{ licenca.inicio_subtrecho ?? '-'}}</span>
                <span class="col"><strong>Fim subtrecho: </strong>{{ licenca.fim_subtrecho ?? '-'}}</span>
                <span class="col"><strong>Extensão: </strong>{{ licenca.extensao ?? '-'}}</span>
            </div>
            <div class="row mb-4">
                <span class="col"><strong>Empreendimento: </strong>{{ licenca.empreendimento ?? '-'}}</span>
                <span class="col"><strong>Dias renovação: </strong>{{ licenca.dias_renovacao ?? '-'}}</span>
                <span class="col"><strong>Renovação: </strong>{{ licenca.renovacao ?? '-'}}</span>
            </div>
        </template>
        <template #footer>
        </template>
    </Modal>
</template>
