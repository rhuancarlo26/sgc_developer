<script setup>

import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import ModelSearchForm from "@/Components/ModelSearchForm.vue";
import Table from '@/Components/Table.vue';
import Navbar from "../../Navbar.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import { ref } from "vue";
import { IconDots } from "@tabler/icons-vue";
import ModalDetalheVeiculo from "./ModalDetalheVeiculo.vue";

const refDetalhes = ref();

defineProps({
  contrato: Object,
  veiculos: Object
})

const abrirModal = (veiculo) => {
  refDetalhes.value.abrirModal(veiculo);
}

const excluirVeiculo = (veiculo_id) => {
  router.delete(route('contratos.contratada.recurso.veiculo.destroy_veiculo', veiculo_id));
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
          <Link class="btn btn-info me-2" :href="route('contratos.contratada.recurso.veiculo.create', contrato.id)">
          Cadastrar veiculos
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
        <Table :columns="['Código', 'Descrição', 'Ação']" :records="veiculos" table-class="table-hover">
          <template #body="{ item }">
            <tr>
              <td>{{ item.codigo?.codigo }}</td>
              <td>{{ item.codigo?.descricao }}</td>
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
                    :href="route('contratos.contratada.recurso.veiculo.create', { contrato: contrato.id, veiculo: item.id })">
                    Editar
                  </a>
                  <a @click="excluirVeiculo(item.id)" class="dropdown-item" href="javascript:void(0)">
                    Excluir
                  </a>
                </div>
              </td>
            </tr>
          </template>
        </Table>

      </template>
    </Navbar>

    <ModalDetalheVeiculo ref="refDetalhes" />
  </AuthenticatedLayout>
</template>
