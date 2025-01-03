<script setup>

import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import Navbar from "../../Navbar.vue";
import NavButton from "@/Components/NavButton.vue";
import ModelSearchFormAllColumns from "@/Components/ModelSearchFormAllColumns.vue";
import Table from "@/Components/Table.vue";
import { IconDots, IconMap, IconMinus } from "@tabler/icons-vue";
import { ref } from "vue";
import { Head, Link, router } from "@inertiajs/vue3";
import LinkConfirmation from "@/Components/LinkConfirmation.vue";
import { dateTimeFormat } from "@/Utils/DateTimeUtils";
import ModalFormCampanha from "./ModalFormCampanha.vue";

const props = defineProps({
  contrato: { type: Object },
  servico: { type: Object },
  campanhas: { type: Object },
  modulos: { type: Array }
});

const modalFormCampanhaRef = ref({});

const abrirModalFormCampanha = () => {
  modalFormCampanhaRef.value.abrirModal();
}

const linkConfirmationRef = ref();
const excluir = (id) => {
  linkConfirmationRef.value.show(() => {
    router.delete(route('contratos.contratada.servicos.monitora_fauna.execucao.campanha.delete', { contrato: props.contrato.id, servico: props.servico.id, campanha: id }), {
      onSuccess: () => Object.assign(form, props.campanha)
    })
  })
}

</script>
<template>

  <Head title="Módulos Amostrais" />

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
              :href="route('contratos.contratada.servicos.monitora_fauna.execucao.campanha.create', { contrato: contrato.id, servico: servico.id })">
            Nova Campanha
            </Link>
          </template>
        </ModelSearchFormAllColumns>

        <Table
          :columns="['ID Campanha', 'Módulo', 'N° ABIO', 'Período', 'Grupos Amostrados', 'Data Início', 'Data Final', 'Observações', 'Ação']"
          :records="campanhas" table-class="table-hover">
          <template #body="{ item }">
            <tr>
              <td>{{ item.id }}</td>
              <td>
                <p v-if="item.campanha_abios">
                  <span v-for="abio in item.campanha_abios" :key="abio" class="badge bg-warning text-white m-1">
                    {{ abio.abio.id }}
                  </span>
                </p>
              </td>
              <td>
                <p v-if="item.campanha_abios">
                  <span v-for="abio in item.campanha_abios" :key="abio.id" class="badge bg-warning text-white m-1">
                    {{ abio.abio?.licenca?.numero_licenca }}
                  </span>
                </p>
              </td>
              <td>{{ item.periodo }}</td>
              <td>
                <p v-if="item.profiss_grupo">
                  <span v-for="grupo in item.profiss_grupo" :key="grupo.id" class="badge bg-warning text-white m-1">
                    {{ grupo.grupo_faunistico?.grupo_faunistico }}
                  </span>
                </p>
              </td>
              <td>{{ dateTimeFormat(item.data_campanha_inicial) }}</td>
              <td>{{ dateTimeFormat(item.data_campanha_final) }}</td>
              <td>{{ item.obs }}</td>
              <td>
                <button type="button" class="btn btn-icon btn-info dropdown-toggle p-2" data-bs-boundary="viewport"
                  data-bs-toggle="dropdown" aria-expanded="false">
                  <IconDots />
                </button>
                <div class="dropdown-menu dropdown-menu-end">
                  <a class="dropdown-item"
                    :href="route('contratos.contratada.servicos.monitora_fauna.execucao.campanha.create', { contrato: contrato.id, servico: servico.id, campanha: item.id })">Editar</a>
                  <a @click="excluir(item.id)" class="dropdown-item" href="javascript:void(0)">Excluir</a>
                </div>
              </td>
            </tr>
          </template>
        </Table>
      </template>
    </Navbar>

    <ModalFormCampanha ref="modalFormCampanhaRef" :contrato="contrato" :servico="servico" :modulos="modulos" />
    <LinkConfirmation ref="linkConfirmationRef" :options="{ text: 'Excluir registro?' }" />

  </AuthenticatedLayout>
</template>
