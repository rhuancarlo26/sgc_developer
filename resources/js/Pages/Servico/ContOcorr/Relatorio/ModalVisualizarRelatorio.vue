<script setup>
import Modal from "@/Components/Modal.vue";
import {onMounted, ref} from "vue";
import FolhaA4 from "./Components/FolhaA4.vue";
import RelatorioCapa from "./Components/RelatorioCapa.vue";
import RelatorioDadosBasicos from "./Components/RelatorioDadosBasicos.vue";
import RelatorioVeiculos from "./Components/RelatorioVeiculos.vue";
import RelatorioSobre from "./Components/RelatorioSobre.vue";
import RelatorioEmpreendimento from "./Components/RelatorioEmpreendimento.vue";
import RelatorioRoa from "./Components/RelatorioRoa.vue";
import RelatorioRNC from "./Components/RelatorioRNC.vue";
import RelatorioIntensidadeLocal from "./Components/RelatorioIntensidadeLocal.vue";
import RelatorioClassificacaoLote from "./Components/RelatorioClassificacaoLote.vue";
import RelatorioAca from "./Components/RelatorioAca.vue";
import axios from "axios";
import RelatorioOutraAnalise from "@/Pages/Servico/ContOcorr/Relatorio/Components/RelatorioOutraAnalise.vue";
import RelatorioFotografico from "@/Pages/Servico/ContOcorr/Relatorio/Components/RelatorioFotografico.vue";

const props = defineProps({
    contrato: {type: Object},
    servico: {type: Object}
});

const modalRelatorio = ref({});
const relatorio = ref({});
const pagina = ref(1);
const variaveis = ref({});

const abrirModal = (item) => {
    relatorio.value = item;

    getVariaveis();

    modalRelatorio.value.getBsModal().show();
}

const getVariaveis = () => {
    axios.get(route('contratos.contratada.servicos.cont_ocorrencia.relatorio.get_variaveis', {
        contrato: props.contrato.id,
        servico: props.servico.id,
        resultado: relatorio.value.id_resultado
    })).then((resp) => {
        variaveis.value = resp.data;
    })
}

defineExpose({abrirModal});
</script>

<template>
    <Modal ref="modalRelatorio" title="Visualizar relatório" modal-dialog-class="modal-xl">
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
                    <RelatorioCapa :contrato="contrato" :servico="servico" :relatorio="relatorio"/>
                </div>
                <div v-show="pagina === 2">
                    <RelatorioDadosBasicos :servico="servico"/>
                </div>
                <div v-show="pagina === 3">
                    <RelatorioVeiculos :servico="servico"/>
                </div>
                <div v-show="pagina === 4">
                    <RelatorioSobre :relatorio="relatorio"/>
                </div>
                <div v-show="pagina === 5">
                    <RelatorioEmpreendimento :servico="servico" :lotes="variaveis.lotes"/>
                </div>
                <div v-show="pagina === 6">
                    <RelatorioRoa :analise="variaveis.analise" :roasAtendidos="variaveis.roasAtendidos"
                                  :roasEmAberto="variaveis.roasEmAberto"/>
                </div>
                <div v-show="pagina === 7">
                    <RelatorioRNC :analise="variaveis.analise" :rncsAtendidos="variaveis.rncsAtendidos"
                                  :rncsEmAberto="variaveis.rncsEmAberto"/>
                </div>
                <div v-show="pagina === 8">
                    <RelatorioIntensidadeLocal :analise="variaveis.analise"/>
                </div>
                <div v-show="pagina === 9">
                    <RelatorioClassificacaoLote :analise="variaveis.analise"/>
                </div>
                <div v-show="pagina === 10">
                    <RelatorioAca :analise="variaveis.analise" :acas="variaveis.acas"/>
                </div>
                <div v-show="pagina === 11">
                    <RelatorioOutraAnalise :outrasAnalises="variaveis.outrasAnalises"/>
                </div>
                <div v-show="pagina === 12">
                    <RelatorioFotografico :ocorrencias="variaveis.ocorrencias"/>
                </div>
            </FolhaA4>
        </template>
        <template #footer>
        </template>
    </Modal>
</template>
