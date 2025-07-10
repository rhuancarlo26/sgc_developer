<script setup>
import { defineProps, defineEmits } from 'vue';
import InputLabel from '@/Components/InputLabel.vue';
import NavButton from '@/Components/NavButton.vue';

defineProps({
  formResultados: {
    type: Object,
    default: () => ({}),
  },
  consideracoes: {
    type: String,
    default: null,
  },
});

defineEmits(['next', 'prev']);
</script>

<template>
  <div class="card">
    <div class="card-body">
      <h4 class="mb-3" style="text-align: center;">RESULTADOS</h4>

      <!-- Dados do Último Resultado -->
      <div v-if="formResultados && Object.keys(formResultados).length" class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
        <div>
          <InputLabel value="ID CAMPANHA" for="id_campanha" />
          <input
            type="text"
            class="form-control"
            id="id_campanha"
            :value="formResultados.id_campanha || 'Não informado'"
            disabled
          />
        </div>
        <div>
          <InputLabel value="MÓDULO" for="modulo" />
          <input
            type="text"
            class="form-control"
            id="modulo"
            :value="formResultados.modulo || 'Não informado'"
            disabled
          />
        </div>
        <div>
          <InputLabel value="PARCELA" for="parcela" />
          <input
            type="text"
            class="form-control"
            id="parcela"
            :value="formResultados.parcela || 'Não informado'"
            disabled
          />
        </div>
        <div>
          <InputLabel value="ID ARMADILHA" for="id_armadilha" />
          <input
            type="text"
            class="form-control"
            id="id_armadilha"
            :value="formResultados.id_armadilha || 'Não informado'"
            disabled
          />
        </div>
        <div>
          <InputLabel value="GRUPO AMOSTRADO" for="grupo_amostrado" />
          <input
            type="text"
            class="form-control"
            id="grupo_amostrado"
            :value="formResultados.grupo_amostrado || 'Não informado'"
            disabled
          />
        </div>
        <div>
          <InputLabel value="DATA DO REGISTRO" for="data_registro" />
          <input
            type="text"
            class="form-control"
            id="data_registro"
            :value="formResultados.data_registro || 'Não informado'"
            disabled
          />
        </div>
        <div>
          <InputLabel value="HORA DO REGISTRO" for="hora_registro" />
          <input
            type="text"
            class="form-control"
            id="hora_registro"
            :value="formResultados.hora_registro || 'Não informado'"
            disabled
          />
        </div>
        <div>
          <InputLabel value="CATEGORIA" for="categoria" />
          <input
            type="text"
            class="form-control"
            id="categoria"
            :value="formResultados.categoria || 'Não informado'"
            disabled
          />
        </div>
        <div>
          <InputLabel value="CLASSE" for="classe" />
          <input
            type="text"
            class="form-control"
            id="classe"
            :value="formResultados.classe || 'Não informado'"
            disabled
          />
        </div>
        <div>
          <InputLabel value="ORDEM" for="ordem" />
          <input
            type="text"
            class="form-control"
            id="ordem"
            :value="formResultados.ordem || 'Não informado'"
            disabled
          />
        </div>
        <div>
          <InputLabel value="FAMÍLIA" for="familia" />
          <input
            type="text"
            class="form-control"
            id="familia"
            :value="formResultados.familia || 'Não informado'"
            disabled
          />
        </div>
        <div>
          <InputLabel value="GÊNERO" for="genero" />
          <input
            type="text"
            class="form-control"
            id="genero"
            :value="formResultados.genero || 'Não informado'"
            disabled
          />
        </div>
        <div>
          <InputLabel value="ESPÉCIE" for="especie" />
          <input
            type="text"
            class="form-control"
            id="especie"
            :value="formResultados.especie || 'Não informado'"
            disabled
          />
        </div>
        <div>
          <InputLabel value="NOME COMUM" for="nome_comum" />
          <input
            type="text"
            class="form-control"
            id="nome_comum"
            :value="formResultados.nome_comum || 'Não informado'"
            disabled
          />
        </div>
        <div>
          <InputLabel value="SEXO" for="sexo" />
          <input
            type="text"
            class="form-control"
            id="sexo"
            :value="formResultados.sexo || 'Não informado'"
            disabled
          />
        </div>
        <div>
          <InputLabel value="FAIXA ETÁRIA" for="faixa_etaria" />
          <input
            type="text"
            class="form-control"
            id="faixa_etaria"
            :value="formResultados.faixa_etaria || 'Não informado'"
            disabled
          />
        </div>
        <div>
          <InputLabel value="QUANTIDADE DE INDIVÍDUOS" for="qnt_individuos" />
          <input
            type="text"
            class="form-control"
            id="qnt_individuos"
            :value="formResultados.qnt_individuos || 'Não informado'"
            disabled
          />
        </div>
        <div>
          <InputLabel value="NÚMERO DE MARCAÇÃO" for="num_marcacao" />
          <input
            type="text"
            class="form-control"
            id="num_marcacao"
            :value="formResultados.num_marcacao || 'Não informado'"
            disabled
          />
        </div>
        <div>
          <InputLabel value="COLETADO" for="coletado" />
          <input
            type="text"
            class="form-control"
            id="coletado"
            :value="formResultados.coletado || 'Não informado'"
            disabled
          />
        </div>
        <div>
          <InputLabel value="NÚMERO DE TOMBAMENTO" for="num_tombamento" />
          <input
            type="text"
            class="form-control"
            id="num_tombamento"
            :value="formResultados.num_tombamento || 'Não informado'"
            disabled
          />
        </div>
        <div class="sm:col-span-2">
          <InputLabel value="DADOS BIOMÉTRICOS" for="dados_biometricos" />
          <textarea
            class="form-control"
            id="dados_biometricos"
            rows="4"
            :value="formResultados.dados_biometricos || 'Não informado'"
            disabled
          ></textarea>
        </div>
        <div>
          <InputLabel value="COMPRIMENTO TOTAL (cm)" for="comp_total" />
          <input
            type="text"
            class="form-control"
            id="comp_total"
            :value="formResultados.comp_total || 'Não informado'"
            disabled
          />
        </div>
        <div>
          <InputLabel value="CABEÇA (cm)" for="cabeca" />
          <input
            type="text"
            class="form-control"
            id="cabeca"
            :value="formResultados.cabeca || 'Não informado'"
            disabled
          />
        </div>
        <div>
          <InputLabel value="CAUDA (cm)" for="cauda" />
          <input
            type="text"
            class="form-control"
            id="cauda"
            :value="formResultados.cauda || 'Não informado'"
            disabled
          />
        </div>
        <div>
          <InputLabel value="FÊMUR (cm)" for="femur" />
          <input
            type="text"
            class="form-control"
            id="femur"
            :value="formResultados.femur || 'Não informado'"
            disabled
          />
        </div>
        <div>
          <InputLabel value="ORELHA (cm)" for="orelha" />
          <input
            type="text"
            class="form-control"
            id="orelha"
            :value="formResultados.orelha || 'Não informado'"
            disabled
          />
        </div>
        <div>
          <InputLabel value="PESO (g)" for="peso" />
          <input
            type="text"
            class="form-control"
            id="peso"
            :value="formResultados.peso || 'Não informado'"
            disabled
          />
        </div>
        <div>
          <InputLabel value="STATUS CONSERVAÇÃO FEDERAL" for="status_conservacao_federal" />
          <input
            type="text"
            class="form-control"
            id="status_conservacao_federal"
            :value="formResultados.status_conservacao_federal || 'Não informado'"
            disabled
          />
        </div>
        <div>
          <InputLabel value="STATUS CONSERVAÇÃO IUCN" for="status_conservacao_iucn" />
          <input
            type="text"
            class="form-control"
            id="status_conservacao_iucn"
            :value="formResultados.status_conservacao_iucn || 'Não informado'"
            disabled
          />
        </div>
        <div class="sm:col-span-2">
          <InputLabel value="CONSIDERAÇÕES" for="consideracoes" />
          <textarea
            class="form-control"
            id="consideracoes"
            rows="4"
            :value="consideracoes || 'Não informado'"
            disabled
          ></textarea>
        </div>
      </div>
      <div v-else class="alert alert-info text-center">
        Nenhum resultado disponível.
      </div>

      <!-- Navegação -->
      <div class="d-flex justify-content-between mt-4">
        <NavButton type="button" type-button="secondary" title="Voltar" @click="$emit('prev')" />
        <NavButton type="button" type-button="primary" title="Avançar" @click="$emit('next')" />
      </div>
    </div>
  </div>
</template>

<style scoped>
.card {
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  background-color: #fff;
  margin: 1.5rem;
}
.card-body {
  padding: 2rem;
}
.form-control:disabled {
  background-color: #f8f9fa;
  color: #495057;
  border: 1px solid #ced4da;
  border-radius: 4px;
  box-sizing: border-box;
  width: 100%;
  min-width: 200px;
  max-width: 100%;
  padding: 0.5rem 1rem;
  font-size: 1rem;
}
textarea.form-control:disabled {
  resize: none;
}
.grid {
  display: grid;
  gap: 1rem;
  grid-template-columns: repeat(1, 1fr);
}
@media (min-width: 640px) {
  .grid {
    grid-template-columns: repeat(2, 1fr);
  }
}
.alert-info {
  font-size: 1rem;
  padding: 1rem;
  border-radius: 6px;
  background-color: #e7f1ff;
  color: #084298;
}
</style>