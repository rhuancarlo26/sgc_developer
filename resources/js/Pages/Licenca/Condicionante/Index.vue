<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link } from "@inertiajs/vue3";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import ModelSearchForm from "@/Components/ModelSearchForm.vue";
import Table from "@/Components/Table.vue";
import ModalNovaCondicionante from "./ModalNovaCondicionante.vue";
import ModalImportarCondicionante from "./ModalImportarCondicionante.vue";
import { ref } from "vue";
import {IconArrowLeft, IconDots, IconEdit, IconTrash} from "@tabler/icons-vue";
import LinkConfirmation from "@/Components/LinkConfirmation.vue";
import NavLinkVoid from "@/Components/NavLinkVoid.vue";
import NavButton from "@/Components/NavButton.vue";
import NavLink from "@/Components/NavLink.vue";

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
          <NavButton route-name="licenca.condicionante.store" title="Cadastrar condicionante" type-button="info"
                     @click="abrirModal()"/>
          <NavButton route-name="licenca.condicionante.store_importacao" title="Importar condicionante" type-button="info"
                       @click="abrirModalImport()"/>
          <a class="btn btn-secondary" :href="route('licenca.index')">
              <IconArrowLeft class="me-2"/> Voltar
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
      <Table :columns="['Número', 'Descrição', 'Prazo', 'Ação']" :records="condicionantes" table-class="table-hover">
        <template #body="{ item }">
          <tr class="cursor-pointer">
            <td>{{ item.numero_condicionante }}</td>
            <td>{{ item.descricao }}</td>
            <td>
              <span class="badge bg-warning text-white m-1" v-if="item.prazo">
                {{ item.prazo }}
              </span>
            </td>
            <td class="col-1">
              <NavButton route-name="licenca.condicionante.update" title="" type-button="info"
                         @click="editarCondicionante(item)" :icon="IconEdit" class="btn btn-lg"/>
              <NavLink route-name="licenca.condicionante.destroy" title="" :param="item.id"
                         @click="editarCondicionante(item)" :icon="IconTrash" class="btn btn-lg btn-danger"/>
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
