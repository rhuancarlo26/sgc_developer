<script setup>

import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import Navbar from "../../Navbar.vue";
import NavButton from "@/Components/NavButton.vue";
import ModelSearchFormAllColumns from "@/Components/ModelSearchFormAllColumns.vue";
import Table from "@/Components/Table.vue";

const props = defineProps({
  contrato: { type: Object },
  servico: { type: Object }
});

</script>
<template>

  <Head title="Vincular ABIO" />

  <AuthenticatedLayout>

    <template #header>
      <div class="w-100 d-flex justify-content-between">
        <Breadcrumb class="align-self-center" :links="[
          { route: route('contratos.gestao.listagem', contrato.tipo_contrato), label: `Gestão de Contratos` },
          { route: '#', label: contrato.contratada }
        ]
          " />
        <Link class="btn btn-dark"
          :href="route('contratos.contratada.servicos.index', { contrato: props.contrato.id })">
        Voltar
        </Link>
      </div>
    </template>

    <Navbar :contrato="contrato" :servico="servico">
      <template #body>
        <ModelSearchFormAllColumns :columns="[]">
          <template #action>
            <NavButton type-button="info" title="Vincular ABIO" />
          </template>
        </ModelSearchFormAllColumns>

        <Table
          :columns="['Tipo', 'N° licença', 'Empreendimento', 'Emissor', 'Data de emissão', 'Vencimento', 'Responsável', 'Processo DNIT', 'Ação']"
          :records="[]" table-class="table-hover">
          <template #body="{}">
            <tr>

            </tr>
          </template>
        </Table>
      </template>
    </Navbar>

  </AuthenticatedLayout>
</template>
