<script setup>
import { dateTimeFormat } from '@/Utils/DateTimeUtils';
import { IconEye } from '@tabler/icons-vue';

const props = defineProps({
  contrato: { type: Object },
  pmqa: { type: Object },
  campanha: { type: Object },
  ponto: { type: Object }
})
</script>
<template>
  <div v-if="ponto.coleta">
    <div class="row mb-4">
      <div class="col">
        <span><strong>Data da coleta: </strong>{{ dateTimeFormat(ponto?.coleta?.dt_coleta) }}</span><br><br>
        <span><strong>Realizado a coleta: </strong>{{ ponto?.coleta?.coleta ? 'Não' : 'Sim'}}</span>
      </div>
    </div>
    <div v-if="!ponto?.coleta?.coleta">
      <div class="row mb-4">
        <div class="col">
          <span><strong>Número da amostra: </strong>{{ ponto?.coleta?.numero_amostra }}</span>
        </div>
        <div class="col">
          <span><strong>Preservação da amostra: </strong>{{ ponto?.coleta?.preservacao_amostra }}</span>
        </div>
        <div class="col">
          <span><strong>Acondicionamento da amostra: </strong>{{ ponto?.coleta?.acondicionamento_amostra }}</span>
        </div>
      </div>
      <div class="row mb-4">
        <div class="col">
          <span><strong>Acondicionamento da amostra: </strong>{{ ponto?.coleta?.acondicionamento_amostra }}</span>
        </div>
        <div class="col">
          <span><strong>Transporte da amostra: </strong>{{ ponto?.coleta?.transporte_amostra }}</span>
        </div>
      </div>
      <div v-if="ponto?.coleta?.arquivos?.length" class="table-responsive mt-4">
        <table class="table table-hover non-hover">
          <thead>
            <tr>
              <th>Nome</th>
              <th>Açao</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="arquivo in pontos.coleta?.arquivos" :key="arquivo.id">
              <td>{{ arquivo.nome }}</td>
              <td>
                <a class="btn btn-icon btn-primary me-1" target="_blank"
                  :href="route('contratos.contratada.sgc.pmqa.execucao.coleta.show_arquivo', { contrato: props.contrato.id, pmqa: props.pmqa.id, campanha: props.campanha.id, arquivo: arquivo.id })">
                  <IconEye />
                </a>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    <div v-else>
      <div class="row mb-4">
        <div class="col">
          <span><strong>Justificativa: </strong>{{ ponto?.coleta?.observacao }}</span>
        </div>
      </div>
    </div>
  </div>
  <div v-else>
    <div class="row mb-4">
      <div class="col d-flex align-itens-center">
        <span><strong>Sem coleta</strong></span>
      </div>
    </div>
  </div>
</template>
