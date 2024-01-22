<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link } from "@inertiajs/vue3";
import Table from '@/Components/Table.vue';
import Breadcrumb from "@/Components/Breadcrumb.vue";
import ModelSearchForm from "@/Components/ModelSearchForm.vue";
import { IconDots } from "@tabler/icons-vue";
import { onMounted } from "vue";

defineProps({
  contratos: Object
})

onMounted(() => {
  console.log(window.location.search);
});

</script>

<template>
  <Head title="Gestão de Contratos" />

  <AuthenticatedLayout>

    <template #header>
      <div class="w-100 d-flex justify-content-between">
        <Breadcrumb class="align-self-center" :links="[
          { route: '#', label: 'Gestão de Contratos' }
        ]" />
        <div>
          <Link class="btn btn-success" :href="'#'">
          Importar Contrato
          </Link>
          <Link class="btn btn-success" :href="'#'">
          Mapa dos Contratos
          </Link>
        </div>
      </div>
    </template>

    <div class="card card-body space-y-3">

      <!-- Pesquisa-->
      <ModelSearchForm :search-columns="{
        'numero_contrato': 'N° do Contrato',
        'cnpj': 'CNPJ',
        'contratada': 'Contratada',
        'processo_sei': 'Processo SEI',
        'situacao': 'Situação'
      }" />

      <!-- Listagem-->
      <Table :columns="['UF', 'BR', 'N° do Contrato', 'CNPJ', 'Contratada', 'Processo SEI', 'Situação', 'Ação']"
        :records="contratos" table-class="table-hover" :excelRoute="'teste'">
        <template #body="{ item }">
          <tr class="cursor-pointer">
            <td></td>
            <td></td>
            <td>{{ item.numero_contrato }}</td>
            <td>{{ item.cnpj }}</td>
            <td>{{ item.contratada }}</td>
            <td>{{ item.processo_sei }}</td>
            <td>{{ item.situacao }}</td>
            <td>
              <span class="dropdown">
                <button class="btn dropdown-toggle align-text-top" data-bs-boundary="viewport" data-bs-toggle="dropdown"
                  aria-expanded="false">
                  <IconDots />
                </button>
                <div class="dropdown-menu dropdown-menu-end" style="">
                  <a class="dropdown-item" href="#">
                    Visualizar
                  </a>
                  <a class="dropdown-item" href="#">
                    Editar
                  </a>
                  <a class="dropdown-item" href="#">
                    Excluir
                  </a>
                </div>
              </span>

            </td>
          </tr>
        </template>
      </Table>
    </div>

  </AuthenticatedLayout>
</template>