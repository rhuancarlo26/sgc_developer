<template>
  <div>
    <Head :title="`Relatório ${historico[0].relatorio_num} - Versão ${historico[0].versao}`" />
    <AuthenticatedLayout>
      <template #header>
        <Breadcrumb
          :links="[
            { route: route('sgc.contratada.relatorios.index', { contrato: contrato.id }), label: `Relatórios` },
            { route: '#', label: `Relatório ${historico[0].relatorio_num} - Revisão ${historico[0].versao}` }
          ]"
        />
      </template>

      <div class="container mt-4">
        <h1>Relatório {{ historico[0].relatorio_num }} - Revisão {{ historico[0].versao }}</h1>
        
        <table class="table table-striped">
          <thead>
            <tr>
              <th>Item</th>
              <th>Período</th>
              <th>Status</th>
              <th>Ação</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in historico" :key="item.id">
              <td>{{ item.nome_topico }}</td>
              <td>{{ item.periodo }}</td>
              <td>{{ item.status }}</td>
              <td>
                <button @click="abrirDocxModal(item.id_item, item.versao)" class="btn btn-success">Abrir</button> 
              </td>
            </tr>
          </tbody>
        </table>

        <!-- Modal para abrir o documento -->
        <DocxHistorico ref="docxModal"
          href="javascript:void(0)"
          :itemId="selectedItemId"
          :comentarios="null"
          :contrato="contrato"
          :numRelatorio="historico[0].relatorio_num"
          
        />
      </div>
    </AuthenticatedLayout>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { defineProps } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import { Head } from '@inertiajs/vue3';
import DocxHistorico from './DocxHistorico.vue'; // Importe o componente DocxModal

const props = defineProps({
  historico: Array,
  contrato: Object
});

const showModal = ref(false);
const modalContent = ref('');
const docxModal = ref(); // Referência para o DocxModal
const selectedItemId = ref(null);

const abrirModal = (caminho) => {
  modalContent.value = caminho;
  showModal.value = true;
};

const fecharModal = () => {
  showModal.value = false;
  modalContent.value = '';
};

// const abrirDocxModal = (itemId) => {
//   selectedItemId.value = itemId;
//   docxModal.value.abrirModal(itemId, props.contrato.id); // Use o método do DocxModal
// };
const abrirDocxModal = (itemId, versao) => {
  selectedItemId.value = itemId;
  docxModal.value.abrirModal(itemId, props.contrato.id, versao); // Passando a versão
};

</script>

