<script setup>
import Modal from "@/Components/Modal.vue";
import {ref} from "vue";
import FolhaA4 from "@/Pages/Servico/PassagemFauna/Relatorio/Components/FolhaA4.vue";
import RelatorioCapa from "./Components/RelatorioCapa.vue";
import RelatorioDadosBasicos from "./Components/RelatorioDadosBasicos.vue";
import RelatorioVeiculos from "./Components/RelatorioVeiculos.vue";
import RelatorioCampanha from "./Components/RelatorioCampanha.vue";
import RelatorioPassagens from "./Components/RelatorioPassagens.vue";
import RelatorioResultados from "@/Pages/Servico/PassagemFauna/Relatorio/Components/RelatorioResultados.vue";
import RelatorioClasse from "@/Pages/Servico/PassagemFauna/Relatorio/Components/RelatorioClasse.vue";
import RelatorioGraficoCampanha from "@/Pages/Servico/PassagemFauna/Relatorio/Components/RelatorioGraficoCampanha.vue";
import RelatorioTipo from "@/Pages/Servico/PassagemFauna/Relatorio/Components/RelatorioTipo.vue";
import RelatorioPassagem from "@/Pages/Servico/PassagemFauna/Relatorio/Components/RelatorioPassagem.vue";
import RelatorioEspecie from "@/Pages/Servico/PassagemFauna/Relatorio/Components/RelatorioEspecie.vue";
import RelatorioOutraAnalise from "@/Pages/Servico/PassagemFauna/Relatorio/Components/RelatorioOutraAnalise.vue";

const relatorio = ref({});
const modalVisualizarRelatorio = ref({});
const pagina = ref(1);

const props = defineProps({
    contrato: {type: Object},
    servico: {type: Object}
});

const abrirModal = (item) => {
    relatorio.value = {};

    if (item) {
        relatorio.value = item;
    }

    modalVisualizarRelatorio.value.getBsModal().show();
}

defineExpose({abrirModal});
</script>

<template>
    <Modal ref="modalVisualizarRelatorio" title="Visualizar relatório" modal-dialog-class="modal-xl">
        <template #body>
            <div class="d-flex justify-content-center mb-4">
                <button @click="pagina -= 1" :disabled="pagina === 1" class="btn btn-primary me-1">Anterior</button>
                <span v-for="num in 12" :key="num">
          <button @click="pagina = num" :disabled="num === pagina" class="btn btn-primary me-1">{{ num }}</button>
        </span>
                <button @click="pagina += 1" :disabled="pagina === 12" class="btn btn-primary">Próximo</button>
            </div>
            <FolhaA4>
                <div v-show="pagina === 1">
                    <RelatorioCapa :contrato="contrato" :relatorio="relatorio"/>
                </div>
                <div v-show="pagina === 2">
                    <RelatorioDadosBasicos :servico="servico"/>
                </div>
                <div v-show="pagina === 3">
                    <RelatorioVeiculos :servico="servico"/>
                </div>
                <div v-show="pagina === 4">
                    <RelatorioCampanha :relatorio="relatorio"/>
                </div>
                <div v-show="pagina === 5">
                    <RelatorioPassagens :relatorio="relatorio" :servico="servico"/>
                </div>
                <div v-show="pagina === 6">
                    <RelatorioResultados :relatorio="relatorio" :servico="servico"/>
                </div>
                <div v-show="pagina === 7">
                    <RelatorioClasse :relatorio="relatorio" :servico="servico"/>
                </div>
                <div v-show="pagina === 8">
                    <RelatorioGraficoCampanha :relatorio="relatorio" :servico="servico"/>
                </div>
                <div v-show="pagina === 9">
                    <RelatorioTipo :relatorio="relatorio" :servico="servico"/>
                </div>
                <div v-show="pagina === 10">
                    <RelatorioPassagem :relatorio="relatorio" :servico="servico"/>
                </div>
                <div v-show="pagina === 11">
                    <RelatorioEspecie :relatorio="relatorio" :servico="servico"/>
                </div>
                <div v-show="pagina === 12">
                    <RelatorioOutraAnalise :relatorio="relatorio" :servico="servico"/>
                </div>
            </FolhaA4>
        </template>
        <template #footer>
        </template>
    </Modal>
</template>
