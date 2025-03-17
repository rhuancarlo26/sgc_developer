// resources/js/Pages/emp/Index.vue
<template>
  <div>
    <Head :title="'Empreendimentos: edição'" />
		<AuthenticatedLayout>
    <h1 class="text-xl font-bold mb-4">Lista de empes</h1>
    <table class="table table-bordered table-striped table-hover table-sm">
      <thead class="thead-dark">
        <tr class="bg-gray-200">
          <th class="border px-4 py-2" scope="row">ID</th>
          <th class="border px-4 py-2">cod_emp</th>
          <th class="border px-4 py-2">tipo_de_intervencao</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="emp in empreendimentos" :key="emp.id">
          <td class="border px-4 py-2 w-min">{{ emp.id }}</td>
          <td class="border px-4 py-2 relative">
            <span @click="abrirEdicao(emp, 'cod_emp')" class="cursor-pointer">{{ emp.cod_emp }}</span>
            <div v-if="campoEditando.id === emp.id && campoEditando.campo === 'cod_emp'" class="absolute bg-white shadow-lg p-2 border rounded">
              <input v-model="empreendimentoEdit.valor" class="border p-1" @keyup.enter="salvarEdicao" @blur="fecharEdicao" />
            </div>
          </td>
          <td class="border px-4 py-2 relative">
            <span @click="abrirEdicao(emp, 'tipo_de_intervencao')" class="cursor-pointer">{{ emp.tipo_de_intervencao }}</span>
            <div v-if="campoEditando.id === emp.id && campoEditando.campo === 'tipo_de_intervencao'" class="absolute bg-white shadow-lg p-2 border rounded">
              <input v-model="empreendimentoEdit.valor" class="border p-1" @keyup.enter="salvarEdicao" @blur="fecharEdicao" />
            </div>
          </td>
        </tr>
      </tbody>
    </table>
    </AuthenticatedLayout>
  </div>
</template>
<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import { Head } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

const props = defineProps({ empreendimentos: Array });
const campoEditando = ref({ id: null, campo: null });
const empreendimentoEdit = ref({ id: null, campo: '', valor: '' });

const abrirEdicao = (empreendimento, campo) => {
  empreendimentoEdit.value = { id: empreendimento.id, campo, valor: empreendimento[campo] };
  campoEditando.value = { id: empreendimento.id, campo };
};

const salvarEdicao = () => {
  router.put(`/empreendimentos/${empreendimentoEdit.value.id}`, { [empreendimentoEdit.value.campo]: empreendimentoEdit.value.valor }, {
    onSuccess: () => {
      campoEditando.value = { id: null, campo: null };
    },
  });
};

const fecharEdicao = () => {
  campoEditando.value = { id: null, campo: null };
};
</script>
<style>
.cursor-pointer {
  cursor: pointer;
}
</style>
