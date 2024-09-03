<script setup>
import {ref} from "vue";
import Modal from "@/Components/Modal.vue";
import {dateTimeFormat} from "@/Utils/DateTimeUtils.js";

const licenca = ref({});
const modalVisualizarASVRef = ref();
const abrirModal = (item) => {
    licenca.value = item;
    modalVisualizarASVRef.value.getBsModal().show();
}

defineExpose({abrirModal});
</script>

<template>
    <Modal ref="modalVisualizarASVRef" title="Visualizar ASV" modal-dialog-class="modal-xl">
        <template #body>
            <div v-if="licenca">
                <h3>Informações da Licença</h3>
                <p class="text-uppercase">
                    <span class="fw-bold">Número Licença:</span>
                    <span>{{ licenca.numero_licenca }}</span>
                </p>
                <p class="text-uppercase"><span class="fw-bold">Orgão Expeditor:</span> {{ licenca.emissor }} </p>
                <p class="text-uppercase">
                    <span class="fw-bold">Tipo da Licença:</span>
                    <span>{{ licenca.tipo_rel?.sigla }} - {{ licenca.numero_licenca }} </span>
                </p>
                <p class="text-uppercase"><span class="fw-bold">Rodovia:</span>
                    <span v-for="br in licenca.brs?.split(',')" :key="br"
                          class="badge bg-warning text-white m-1">
                                                        {{ br }}
                                                    </span>
                </p>
                <p class="text-uppercase"><span class="fw-bold">KM Inicial:</span>
                    <span v-if="licenca.segmentos">
                                    {{ Math.min(...licenca.segmentos?.map(segmento => segmento.km_inicio)) }}
                                    </span>
                </p>
                <p class="text-uppercase"><span class="fw-bold">KM Final:</span>
                    <span v-if="licenca.segmentos">
                                        {{ Math.max(...licenca.segmentos?.map(segmento => segmento.km_fim)) }}
                                    </span>
                </p>
                <p class="text-uppercase"><span class="fw-bold">Data Inicial:</span>
                    {{ dateTimeFormat(licenca.data_emissao) }} </p>
                <p class="text-uppercase"><span class="fw-bold">Vencimento:</span> {{ licenca.vencimento }}
                </p>
            </div>
        </template>
        <template #footer>
        </template>
    </Modal>
</template>
