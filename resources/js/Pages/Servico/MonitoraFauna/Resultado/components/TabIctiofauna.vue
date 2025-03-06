<script setup>
import NavButton from "@/Components/NavButton.vue";
import { ref } from "vue";
import ModalRiqueza from "./ModalRiqueza.vue";
import ModalAbundancia from "./ModalAbundancia.vue";
import ModalColetor from "./ModalColetor.vue";
import ModalOutrasAnalises from "./ModalOutrasAnalises.vue";

const modalRiquezaRef = ref();
const modalAbundanciaRef = ref();
const modalColetorRef = ref();
const modalOutrasAnalisesRef = ref();

const props = defineProps({
  contrato: { type: Object },
  servico: { type: Object },
  dados_tabela: { type: Object },
  dados_campanha: { type: Object },
  dados_armadilha: { type: Object },
  dados_ordem: { type: Object },
  dados_familia: { type: Object }
})

const abrirModalRiqueza = () => {
  modalRiquezaRef.value.abrirModal();
}
const abrirModalAbundancia = () => {
  modalAbundanciaRef.value.abrirModal();
}
const abrirModalColetor = () => {
  modalColetorRef.value.abrirModal();
}
const abrirModalOutraAnalise = () => {
  modalOutrasAnalisesRef.value.abrirModal();
}


</script>
<template>
  <h2>Tabela com o registros das espécies do grupo Herpetofauna</h2>

  <div class="card mb-4">
    <div class="table-responsive">
      <table class="table card-table table-bordered">
        <thead>
          <tr>
            <th rowspan="2" class="text-center">Espécie</th>
            <th rowspan="2" class="text-center">Nome Comum</th>
            <th class="text-center">Módulos</th>
            <th rowspan="2" class="text-center">Abundância Absoluta</th>
            <th rowspan="2" class="text-center">Abundância Relativa da Espécie (%)</th>
            <th class="text-center">Status Conservação</th>
          </tr>
          <tr>
            <th class="text-center">IUCN</th>
            <th class="text-center">FEDERAL</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="data in dados_tabela" :key="data">
            <td class="text-center">{{ data.especie }}</td>
            <td class="text-center">{{ data.nome_comum }}</td>
            <td class="text-center">{{ data.modulos.join(', ') }}</td>
            <td class="text-center">{{ data.abundancia_absoluta }}</td>
            <td class="text-center">{{ parseFloat(data.abundancia_relativa).toFixed(2) }}%</td>
            <td class="text-center">{{ data.status_conservacao_iucn }} - {{ data.status_conservacao_federal }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
  <div class="mb-4">Abundância Absoluta Total: {{ dados_tabela ? Object.values(dados_tabela)?.reduce((acc, item) => acc
    +
    item.abundancia_absoluta,
    0) : 0 }}</div>
  <div class="text-center">
    <NavButton @click="abrirModalRiqueza()" title="Análise da Riqueza" type-button="outline-warning" />
    <NavButton @click="abrirModalAbundancia()" title="Análise da Abundância" type-button="outline-success" />
    <NavButton @click="abrirModalColetor()" title="Análise da Curva do Coletor" type-button="outline-info" />
    <NavButton @click="abrirModalOutraAnalise()" title="Outras Análises" type-button="outline-primary" />
  </div>

  <ModalRiqueza :contrato="contrato" :servico="servico" :dados_campanha="dados_campanha"
    :dados_armadilha="dados_armadilha" :dados_ordem="dados_ordem" :dados_familia="dados_familia"
    ref="modalRiquezaRef" />
  <ModalAbundancia :contrato="contrato" :servico="servico" :dados_campanha="dados_campanha"
    :dados_armadilha="dados_armadilha" :dados_ordem="dados_ordem" :dados_familia="dados_familia"
    ref="modalAbundanciaRef" />
  <ModalColetor :contrato="contrato" :servico="servico" ref="modalColetorRef" />
  <ModalOutrasAnalises :contrato="contrato" :servico="servico" ref="modalOutrasAnalisesRef" />

</template>