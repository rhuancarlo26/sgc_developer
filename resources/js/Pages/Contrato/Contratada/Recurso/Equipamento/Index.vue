<script setup>

import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import ModelSearchForm from "@/Components/ModelSearchForm.vue";
import Table from '@/Components/Table.vue';
import Navbar from "../../Navbar.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import { ref } from "vue";
import { IconDots } from "@tabler/icons-vue";
import ModalDetalheEquipamento from "./ModalDetalheEquipamento.vue";

defineProps({
  contrato: Object,
  equipamentos: Object
})

const refDetalhes = ref();

const excluirEquipamento = (equipamento_id) => {
  router.delete(route('contratos.contratada.recurso.equipamento.destroy_equipamento', equipamento_id));
}

const abrirModal = (equipamento) => {
  refDetalhes.value.abrirModal(equipamento);
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
          <Link class="btn btn-info me-2" :href="route('contratos.contratada.recurso.equipamento.create', contrato.id)">
          Cadastrar equipamentos
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
        <Table :columns="['Nome', 'Descrição', 'Ação']" :records="equipamentos" table-class="table-hover">
          <template #body="{ item }">
            <tr>
              <td>{{ item.nome }}</td>
              <td>{{ item.descricao }}</td>
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
                    :href="route('contratos.contratada.recurso.equipamento.create', { contrato: contrato.id, equipamento: item.id })">
                    Editar
                  </a>
                  <a @click="excluirEquipamento(item.id)" class="dropdown-item" href="javascript:void(0)">
                    Excluir
                  </a>
                </div>
              </td>
            </tr>
          </template>
        </Table>
      </template>
    </Navbar>

    <ModalDetalheEquipamento ref="refDetalhes" />

  </AuthenticatedLayout>
</template>
