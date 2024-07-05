<script setup>
import { dateTimeFormat } from '@/Utils/DateTimeUtils';

defineProps({
  ponto: { type: Object }
})
</script>
<template>
  <div class="row mb-4">
    <div class="col">
      <span><strong>Data da coleta: </strong>{{ dateTimeFormat(ponto.coleta?.data_coleta) }}</span>
    </div>
    <div class="col">
      <span><strong>Realizado a coleta?: </strong>{{ ponto.coleta?.sem_coleta ? 'Não' : 'Sim' }}</span>
    </div>
  </div>
  <div v-if="!ponto.coleta?.sem_coleta">
    <div class="row mb-4">
      <div class="col">
        <span><strong>Número da amostra: </strong>{{ ponto.coleta?.numero_amostra }}</span>
      </div>
      <div class="col">
        <span><strong>Preservação da amostra: </strong>{{ ponto.coleta?.preservacao_amostra }}</span>
      </div>
      <div class="col">
        <span><strong>Acondicionamento da amostra: </strong>{{ ponto.coleta?.acondicionamento_amostra }}</span>
      </div>
    </div>
    <div class="row mb-4">
      <div class="col">
        <span><strong>Acondicionamento da amostra: </strong>{{ ponto.coleta?.acondicionamento_amostra }}</span>
      </div>
      <div class="col">
        <span><strong>Transporte da amostra: </strong>{{ ponto.coleta?.transporte_amostra }}</span>
      </div>
    </div>
  </div>
  <div v-else>
    <div class="row mb-4">
      <div class="col">
        <span><strong>Justificativa: </strong>{{ ponto.coleta?.justificativa }}</span>
      </div>
    </div>
  </div>
</template>