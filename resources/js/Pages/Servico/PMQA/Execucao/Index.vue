<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import Navbar from "../Navbar.vue";
import { Head, Link } from "@inertiajs/vue3";
import ModelSearchFormAllColumns from "@/Components/ModelSearchFormAllColumns.vue";
import Table from "@/Components/Table.vue";
import NavButton from "@/Components/NavButton.vue";

const props = defineProps({
  contrato: { type: Object },
  servico: { type: Object }
});

const abrirModalCampanha = () => {

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
      </div>
    </template>

    <Navbar :contrato="contrato" :servico="servico">
      <template #body>
        <!-- Pesquisa-->
        <ModelSearchFormAllColumns :columns="['nome', 'data_inicio', 'data_termino']">
          <template #action>
            <NavButton @click="abrirModalCampanha()" type-button="success" title="Nova campanha" />
          </template>
        </ModelSearchFormAllColumns>

        <!-- Listagem-->
        <Table :columns="['Nome da campanha', 'Data de início', 'Data de término', 'Pontos', 'Ação']" :records="[]"
          table-class="table-hover">
          <template #body="{}">
            <tr>

            </tr>
          </template>
        </Table>
      </template>
    </Navbar>

  </AuthenticatedLayout>
</template>