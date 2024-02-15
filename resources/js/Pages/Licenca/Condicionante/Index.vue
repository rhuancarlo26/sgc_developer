<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import ModelSearchForm from "@/Components/ModelSearchForm.vue";
import Table from "@/Components/Table.vue";

const props = defineProps({
  licencas: Object
})
</script>

<template>
  <Head title="Gestão de Licenças" />

  <AuthenticatedLayout>

    <template #header>
      <div class="w-100 d-flex justify-content-between">
        <Breadcrumb class="align-self-center"
          :links="[{ route: route('licenca.index'), label: 'Licenças' }, { route: '#', label: 'Condicionante' }]" />
        <div class="container-buttons">
          <button type="button" class="btn btn-success me-2">
            Cadastrar condicionante
          </button>
          <button type="button" class="btn btn-success me-2">
            Importar condicionante
          </button>
          <a class="btn btn-dark" :href="route('licenca.index')">
            Voltar
          </a>
        </div>
      </div>
    </template>

    <div class="card card-body space-y-3">
      <!-- Pesquisa-->
      <ModelSearchForm :search-columns="{}" />

      <!-- Listagem-->
      <Table :columns="['#', 'Número', 'Descrição', 'Prazo', 'Status', 'Ação']" table-class="table-hover">
        <template #body="{ }">
          <tr class="cursor-pointer">

          </tr>
        </template>
      </Table>
    </div>
  </AuthenticatedLayout>
</template>

<style scoped></style>