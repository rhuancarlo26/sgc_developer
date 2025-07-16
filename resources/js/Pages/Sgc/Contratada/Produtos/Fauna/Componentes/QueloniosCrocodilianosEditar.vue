<script setup>
import { defineProps, defineEmits } from 'vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import NavButton from '@/Components/NavButton.vue';

defineProps({
  form: {
    type: Object,
    required: true,
  },
  pontoRecords: {
    type: Array,
    default: () => [],
  },
  subStep: {
    type: Number,
    default: 4,
  },
});

defineEmits(['next', 'prev']);

const addPonto = () => {
  form.pontos_quelo_crocod.push({
    ponto_de_coleta: '',
    nome_curso_hidrico: '',
    coordenadas: '',
    bacia: '',
    profundidade: null,
    largura: null,
    tipo_substrato: '',
  });
};

const removePonto = (index) => {
  form.pontos_quelo_crocod.splice(index, 1);
};
</script>

<template>
  <div class="card">
    <div class="card-body">
      <h4 class="mb-3" style="text-align: center;">QUELÔNIOS E CROCODILIANOS</h4>

      <div class="mb-4">
        <InputLabel value="NÃO SE APLICA" for="nao_se_aplica" />
        <input type="checkbox" v-model="form.nao_se_aplica" id="nao_se_aplica" />
        <InputError :message="form.errors.nao_se_aplica" />
      </div>

      <div v-if="!form.nao_se_aplica" class="mb-6">
        <div v-for="(ponto, index) in pontoRecords" :key="index" class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
          <div>
            <InputLabel value="PONTO DE COLETA" for="ponto_de_coleta" />
            <input v-model="form.pontos_quelo_crocod[index].ponto_de_coleta" type="text" class="form-control" :id="'ponto_de_coleta_' + index" />
            <InputError :message="form.errors[`pontos_quelo_crocod.${index}.ponto_de_coleta`]" />
          </div>
          <div>
            <InputLabel value="NOME DO CURSO HÍDRICO" for="nome_curso_hidrico" />
            <input v-model="form.pontos_quelo_crocod[index].nome_curso_hidrico" type="text" class="form-control" :id="'nome_curso_hidrico_' + index" />
            <InputError :message="form.errors[`pontos_quelo_crocod.${index}.nome_curso_hidrico`]" />
          </div>
          <div>
            <InputLabel value="COORDENADAS" for="coordenadas" />
            <input v-model="form.pontos_quelo_crocod[index].coordenadas" type="text" class="form-control" :id="'coordenadas_' + index" placeholder="Ex: -23.123, -46.456" />
            <InputError :message="form.errors[`pontos_quelo_crocod.${index}.coordenadas`]" />
          </div>
          <div>
            <InputLabel value="BACIA HIDROGRÁFICA" for="bacia" />
            <input v-model="form.pontos_quelo_crocod[index].bacia" type="text" class="form-control" :id="'bacia_' + index" />
            <InputError :message="form.errors[`pontos_quelo_crocod.${index}.bacia`]" />
          </div>
          <div>
            <InputLabel value="PROFUNDIDADE (m)" for="profundidade" />
            <input v-model="form.pontos_quelo_crocod[index].profundidade" type="number" step="any" class="form-control" :id="'profundidade_' + index" />
            <InputError :message="form.errors[`pontos_quelo_crocod.${index}.profundidade`]" />
          </div>
          <div>
            <InputLabel value="LARGURA (m)" for="largura" />
            <input v-model="form.pontos_quelo_crocod[index].largura" type="number" step="any" class="form-control" :id="'largura_' + index" />
            <InputError :message="form.errors[`pontos_quelo_crocod.${index}.largura`]" />
          </div>
          <div class="col-span-full sm:col-span-2">
            <InputLabel value="TIPO DE SUBSTRATO" for="tipo_substrato" />
            <textarea v-model="form.pontos_quelo_crocod[index].tipo_substrato" class="form-control" :id="'tipo_substrato_' + index" rows="5"></textarea>
            <InputError :message="form.errors[`pontos_quelo_crocod.${index}.tipo_substrato`]" />
          </div>
          <div class="col-span-full">
            <button type="button" class="btn btn-danger btn-sm" @click="removePonto(index)">Remover Ponto</button>
          </div>
        </div>
        <button type="button" class="btn btn-primary btn-sm mt-2" @click="addPonto">Adicionar Ponto</button>
      </div>
      <div v-else-if="!form.nao_se_aplica" class="alert alert-info text-center">
        Nenhum ponto de amostragem disponível. Clique em "Adicionar Ponto" para começar.
      </div>

      <div class="d-flex justify-content-between mt-4">
        <NavButton type="button" type-button="secondary" title="Voltar" @click="$emit('prev')" />
        <NavButton type="button" type-button="primary" title="Avançar" @click="$emit('next')" />
      </div>
      <h4 class="text-center mt-4 font-weight-bold text-muted">{{ subStep }}/5</h4>
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
.form-control {
  border: 1px solid #ced4da;
  border-radius: 4px;
  padding: 0.375rem 0.75rem;
  width: 100%;
}
textarea.form-control {
  resize: vertical;
  min-height: 100px;
}
.alert-info {
  font-size: 1rem;
  padding: 1rem;
  border-radius: 6px;
  background-color: #e7f1ff;
  color: #084298;
}
.text-muted {
  color: #6c757d !important;
}
.btn-sm {
  padding: 0.25rem 0.5rem;
  font-size: 0.875rem;
}
</style>