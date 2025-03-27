<script setup>

import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import Navbar from "../../Navbar.vue";
import ModelSearchFormAllColumns from "@/Components/ModelSearchFormAllColumns.vue";
import Table from "@/Components/Table.vue";
import { IconDots } from "@tabler/icons-vue";
import { Head, Link, router } from "@inertiajs/vue3";
import LinkConfirmation from "@/Components/LinkConfirmation.vue";
import { ref } from "vue";
import { dateTimeFormat } from "@/Utils/DateTimeUtils";

const props = defineProps({
  contrato: { type: Object },
  servico: { type: Object },
  registros: { type: Object },
});

const linkConfirmationRef = ref();
const excluir = (id) => {
  linkConfirmationRef.value.show(() => {
    router.delete(route('contratos.contratada.servicos.monitora_fauna.execucao.registro.delete', { contrato: props.contrato.id, servico: props.servico.id, registro: id }))
  })
}

</script>
<template>

  <Head title="Registros" />

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
            <Link class="btn btn-primary"
              :href="route('contratos.contratada.servicos.monitora_fauna.execucao.registro.create', { contrato: contrato.id, servico: servico.id })">
            Novo Registro
            </Link>
          </template>
        </ModelSearchFormAllColumns>

        <Table
          :columns="['Nome Registro', 'N° Campanha', 'Nome Módulo', 'Parcela', 'Nome Armadilha', 'Grupo Amostrado', 'Espécie', 'Data Registro', 'Data Cadastro', 'Ação']"
          :records="registros" table-class="table-hover">
          <template #body="{ item }">
            <tr>
              <td>{{ item.nome_id }}</td>
              <td>{{ item.id_campanha }}</td>
              <td>{{ item.id_modulo }}</td>
              <td>{{ item.parcela_modulo }}</td>
              <td>{{ item.armadilha?.nome_id }}</td>
              <td>{{ item.grupo_faunistico?.grupo_faunistico }}</td>
              <td>{{ item.especie }}</td>
              <td>{{ dateTimeFormat(item.data_registro) }}</td>
              <td>{{ dateTimeFormat(item.created_at) }}</td>
              <td>
                <button type="button" class="btn btn-icon btn-info dropdown-toggle p-2" data-bs-boundary="viewport"
                  data-bs-toggle="dropdown" aria-expanded="false">
                  <IconDots />
                </button>
                <div class="dropdown-menu dropdown-menu-end">
                  <a class="dropdown-item"
                    :href="route('contratos.contratada.servicos.monitora_fauna.execucao.registro.create', { contrato: contrato.id, servico: servico.id, registro: item.id })">Editar</a>
                  <a @click="excluir(item.id)" class="dropdown-item" href="javascript:void(0)">Excluir</a>
                </div>
              </td>
            </tr>
          </template>
        </Table>
      </template>
    </Navbar>

    <LinkConfirmation ref="linkConfirmationRef" :options="{ text: 'Excluir registro?' }" />

  </AuthenticatedLayout>
</template>
