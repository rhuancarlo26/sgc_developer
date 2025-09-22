<template>
  <div>
    <form @submit.prevent="$emit('salvar')">
      <div class="row">
      <div class="col-md-6 mb-3">
        <label>ID Campanha</label>
        <input v-model="localForm.id_campanha" class="form-control" readonly />
        <small v-if="errors.id_campanha" class="text-danger">{{ errors.id_campanha }}</small>
      </div>
        <div class="col-md-6 mb-3">
          <label>Empreendimento</label>
          <select v-model="localForm.cod_emp" @change="preencherCamposEmpreendimento" class="form-select" required>
            <option value="">Selecione um empreendimento</option>
            <option v-for="emp in empreendimentos" :key="emp.cod_emp" :value="emp.cod_emp">{{ emp.cod_emp }}</option>
          </select>
          <small v-if="errors.cod_emp" class="text-danger">{{ errors.cod_emp }}</small>
        </div>
        <div class="col-md-6 mb-3">
          <label>Subproduto</label>
          <input v-model="localForm.subproduto" class="form-control" readonly />
          <small v-if="errors.subproduto" class="text-danger">{{ errors.subproduto }}</small>
        </div>
        <div class="col-md-6 mb-3">
          <label>Subtrecho</label>
          <input v-model="localForm.subtrecho" class="form-control" readonly />
        </div>
        <div class="col-md-6 mb-3">
          <label>Segmento</label>
          <input v-model="localForm.segmento" class="form-control" readonly />
        </div>
        <div class="col-md-6 mb-3">
          <label>Extensão (km)</label>
          <input v-model="localForm.extensao" class="form-control" readonly />
        </div>
        <div class="col-md-6 mb-3">
          <label>Tipo de Intervenção</label>
          <input v-model="localForm.tipo_de_intervencao" class="form-control" readonly />
        </div>
        <div class="col-md-6 mb-3">
          <label>Bioma</label>
          <input v-model="localForm.bioma" class="form-control" readonly />
        </div>
        <div class="col-md-12 mb-3">
          <label>Descrição</label>
          <textarea v-model="localForm.descricao" class="form-control" readonly />
        </div>
      </div>
      <button type="submit" class="btn btn-primary">Salvar</button>
    </form>
  </div>
</template>

<script setup>
import { reactive, watch } from 'vue';

const props = defineProps(['campanha', 'empreendimentos', 'errors']);
const emit = defineEmits(['salvar', 'update-form']);

const localForm = reactive({ ...props.campanha });

watch(() => localForm, (newForm) => {
    emit('update-form', newForm);
}, { deep: true });

const preencherCamposEmpreendimento = () => {
    const emp = props.empreendimentos.find(e => e.cod_emp === localForm.cod_emp);
    if (emp) {
        localForm.subtrecho = emp.subtrecho || '';
        localForm.segmento = emp.segmento || '';
        localForm.extensao = emp.extensao || '';
        localForm.tipo_de_intervencao = emp.tipo_de_intervencao || '';
        localForm.descricao = emp.descricao || '';
        localForm.bioma = emp.bioma || '';
    } else {
        localForm.subtrecho = '';
        localForm.segmento = '';
        localForm.extensao = '';
        localForm.tipo_de_intervencao = '';
        localForm.descricao = '';
        localForm.bioma = '';
    }
};
</script>