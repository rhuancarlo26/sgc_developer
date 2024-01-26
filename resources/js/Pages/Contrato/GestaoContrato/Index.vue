<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import Table from '@/Components/Table.vue';
import Breadcrumb from "@/Components/Breadcrumb.vue";
import ModelSearchForm from "@/Components/ModelSearchForm.vue";
import { IconDots } from "@tabler/icons-vue";

const props = defineProps({
  contratos: Object
})

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
          <Link class="btn btn-success me-2" :href="route('contratos.gestao.create')">
          Importar Contrato
          </Link>
          <button type="button" class="btn btn-success">Mapa dos Contratos</button>

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
        :records="contratos" table-class="table-hover" :excelRoute="route('contratos.gestao.excel_export')">
        <template #body="{ item }">
          <tr class="cursor-pointer" @click="router.get(route('contratos.gestao.create', item.id))">
            <td class="w-8"><span v-for="uf in item.ufs" :key="uf" class="badge bg-warning text-white m-1">{{ uf }}</span>
            </td>
            <td class="w-8"><span v-for="br in item.brs" :key="br" class="badge bg-warning text-white m-1">{{ br }}</span>
            </td>
            <td>{{ item.numero_contrato }}</td>
            <td>{{ item.cnpj }}</td>
            <td>{{ item.contratada }}</td>
            <td>{{ item.processo_sei }}</td>
            <td>{{ item.situacao?.nome }}</td>
            <td @click.stop>
              <span class="dropdown">
                <button class="btn dropdown-toggle align-text-top" data-bs-boundary="viewport" data-bs-toggle="dropdown"
                  aria-expanded="false">
                  <IconDots />
                </button>
                <div class="dropdown-menu dropdown-menu-end" style="">
                  <a class="dropdown-item" href="#">
                    Visualizar
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