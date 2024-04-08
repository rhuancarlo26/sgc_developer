<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link } from "@inertiajs/vue3";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import ModelSearchForm from "@/Components/ModelSearchForm.vue";
import Table from "@/Components/Table.vue";
import ModalNovaCondicionante from "./ModalNovaCondicionante.vue";
import ModalImportarCondicionante from "./ModalImportarCondicionante.vue";
import { ref } from "vue";
import { IconDots, IconTrash } from "@tabler/icons-vue";
import LinkConfirmation from "@/Components/LinkConfirmation.vue";

const refNovaCondicionante = ref();
const refImportarCondicionante = ref();

const props = defineProps({
  licenca: Object,
  condicionantes: Object
})

const abrirModal = () => {
  refNovaCondicionante.value.abrirModal();
}
const abrirModalImport = () => {
  refImportarCondicionante.value.abrirModal();
}

const editarCondicionante = (item) => {
  refNovaCondicionante.value.editarCondicionante(item);
}

</script>

<template>
  <Head title="Gestão de condicionantes" />

  <AuthenticatedLayout>

    <template #header>
      <div class="w-100 d-flex justify-content-between">
        <Breadcrumb class="align-self-center"
          :links="[{ route: route('licenca.index'), label: 'Licenças' }, { route: '#', label: 'Condicionante' }]" />
        <div class="container-buttons">
          <button @click="abrirModal()" type="button" class="btn btn-success me-2">
            Cadastrar condicionante
          </button>
          <button @click="abrirModalImport()" type="button" class="btn btn-success me-2">
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
      <ModelSearchForm :search-columns="{
        'numero_condicionante': 'N° condicionante',
        'decricao': 'Descrição',
        'prazo': 'Prazo'
      }" />

      <!-- Listagem-->
      <Table :columns="['#', 'Número', 'Descrição', 'Prazo', 'Ação']" :records="condicionantes" table-class="table-hover">
        <template #body="{ item }">
          <tr class="cursor-pointer">
            <td>{{ item.id }}</td>
            <td>{{ item.numero_condicionante }}</td>
            <td>{{ item.descricao }}</td>
            <td>{{ item.prazo }}</td>
            <td>
              <span class="dropdown">
                <button class="btn dropdown-toggle align-text-top" data-bs-boundary="viewport" data-bs-toggle="dropdown"
                  aria-expanded="false">
                  <IconDots />
                </button>
                <div class="dropdown-menu dropdown-menu-end" style="">
                  <a @click="editarCondicionante(item)" class="dropdown-item" href="javascript:void(0)">
                    Editar
                  </a>
                  <a class="dropdown-item" :href="route('licenca.condicionante.destroy', item.id)">
                    Deletar
                  </a>
                </div>
              </span>
            </td>
          </tr>
        </template>
      </Table>
    </div>
  </AuthenticatedLayout>

  <ModalNovaCondicionante ref="refNovaCondicionante" :licenca="licenca" />
  <ModalImportarCondicionante ref="refImportarCondicionante" :licenca="licenca" />
</template>

<style scoped></style>
