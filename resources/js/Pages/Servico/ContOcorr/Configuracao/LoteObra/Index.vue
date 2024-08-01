<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import Navbar from "../../Navbar.vue";
import { Head, Link } from "@inertiajs/vue3";
import ModelSearchFormAllColumns from "@/Components/ModelSearchFormAllColumns.vue";
import Table from "@/Components/Table.vue";
import NavButton from "@/Components/NavButton.vue";
import { ref } from "vue";
import { IconEye } from "@tabler/icons-vue";
import { IconPlus } from "@tabler/icons-vue";
import { IconPencil } from "@tabler/icons-vue";
import { IconTrash } from "@tabler/icons-vue";
import NavLink from "@/Components/NavLink.vue";
import { IconMap } from "@tabler/icons-vue";

const props = defineProps({
  contrato: { type: Object },
  servico: { type: Object }
});

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
            <NavButton route-name="contratos.contratada.servicos.pmqa.configuracao.ponto.importar"
              :param="{ contrato: props.contrato.id, servico: props.servico.id }" type-button="primary"
              title="Submeter ao fiscal" />
            <NavLink route-name="contratos.contratada.servicos.cont_ocorrencia.configuracao.lote_obra.create"
              :param="{ contrato: props.contrato.id, servico: props.servico.id }" class="btn btn-success"
              title="Novo lote" />
          </template>
        </ModelSearchFormAllColumns>

        <Table
          :columns="['Lote', 'Nome', 'BR', 'UF', 'KM inicial', 'KM final', 'Construtora/Consórcio', 'N° contrato', 'Situação', 'Supervisora de obra', 'Ação']"
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