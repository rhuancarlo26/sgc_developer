<script setup>

import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import ModelSearchForm from "@/Components/ModelSearchForm.vue";
import Table from '@/Components/Table.vue';
import Navbar from "../../Navbar.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import { ref } from "vue";
import { IconDots } from "@tabler/icons-vue";
import ModalDetalheRh from "./ModalDetalheRh.vue";

const refDetalhes = ref();

defineProps({
  contrato: Object,
  rhs: Object
})

const abrirModal = (rh) => {
  refDetalhes.value.abrirModal(rh);
}

const excluirRh = (rh_id) => {
  router.delete(route('contratos.contratada.recurso.rh.destroy_rh', rh_id));
}

</script>

<template>

  <Head :title="`${contrato.contratada.slice(0, 10)}...`" />

  <AuthenticatedLayout>

    <template #header>
      <div class="w-100 d-flex justify-content-between">
        <Breadcrumb class="align-self-center" :links="[
    { route: route('contratos.gestao.listagem', contrato.tipo_id), label: `Gestão de Contratos` },
    { route: '#', label: contrato.contratada }
  ]
    " />
        <div class="container-buttons">
          <Link class="btn btn-info me-2" :href="route('contratos.contratada.recurso.rh.create', contrato.id)">
          Cadastrar rh
          </Link>
        </div>
      </div>
    </template>

    <Navbar :contrato="contrato">

      <template #body>
        <!-- Pesquisa-->
        <ModelSearchForm :search-columns="{
    'nome': 'Nome'
  }" />

        <!-- Listagem-->
        <Table :columns="['Nome', 'CPF', 'E-mail', 'Função', 'Formação', 'Status', 'Ação']" :records="rhs"
          table-class="table-hover">
          <template #body="{ item }">
            <tr>
              <td>{{ item.nome }}</td>
              <td>{{ item.cpf }}</td>
              <td>{{ item.email }}</td>
              <td>{{ item.funcao }}</td>
              <td>{{ item.formacao }}</td>
              <td>
                <span :class="item.status == 0 ? 'badge bg-red-lt' : 'badge bg-green-lt'"
                :title="item.status == 0 ? 'Item inativo' : 'Item ativo'">
                  {{ item.status == 0 ? 'Inativo' : 'Ativo'}} 
                </span>
              </td>
              <td>
                <button type="button" class="btn btn-icon dropdown-toggle p-2" data-bs-boundary="viewport"
                  data-bs-toggle="dropdown" aria-expanded="false">
                  <IconDots />
                </button>
                <div class="dropdown-menu dropdown-menu-end">
                  <a @click="abrirModal(item)" class="dropdown-item" href="javascript:void(0)">
                    Visualizar
                  </a>
                  <a class="dropdown-item"
                    :href="route('contratos.contratada.recurso.rh.create', { contrato: contrato.id, rh: item.id })">
                    Editar
                  </a>
                  <a @click="excluirRh(item.id)" class="dropdown-item" href="javascript:void(0)">
                    Excluir
                  </a>
                </div>
              </td>
            </tr>
          </template>
        </Table>

      </template>
    </Navbar>

    <ModalDetalheRh ref="refDetalhes" />

  </AuthenticatedLayout>
</template>
