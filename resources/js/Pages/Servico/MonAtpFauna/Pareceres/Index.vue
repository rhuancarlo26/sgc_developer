<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import Navbar from "../Navbar.vue";
import Table from "@/Components/Table.vue";
// import { Head } from "@inertiajs/vue3";
import { IconDots } from "@tabler/icons-vue";
import ModalVisualizar from "./ModalVisualizar.vue";
import {ref} from "vue";

const props = defineProps({
  contrato: { type: Object },
  servico: { type: Object },
  data: { type: Object },
});

// const modalVincularABIO = ref({});
//
// const abrirModalVincularABIO = () => {
//   modalVincularABIO.value.abrirModal();
// }

const modalRef = ref()
const openParecer = (item) => {
    modalRef.value.abrirModal(item)
}
</script>
<template>

  <Head :title="`${contrato.contratada.slice(0, 10)}...`" />

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
<!--            <NavButton @click="abrirModalVincularABIO()" type-button="info" title="Vincular ABIO" />-->
          </template>
        </ModelSearchFormAllColumns>

        <!-- Listagem-->
        <Table
          :columns="['Tipo', 'Status', 'Data do parecer', 'Ação']"
          :records="data" table-class="table-hover">
          <template #body="{ item }">
            <tr>
              <td>{{ item.tipo }}</td>
              <td>
                  <span v-if="item.fk_status === 2" class="shadow-none badge badge-warning">Em análise</span>
                  <span v-if="item.fk_status === 3" class="shadow-none badge badge-primary">Aprovado</span>
                  <span v-if="item.fk_status === 4" class="shadow-none badge badge-danger">Pendente</span>
              </td>
              <td>{{ item.data_parecer }}</td>
              <td>
                <button type="button" class="btn btn-icon btn-info dropdown-toggle p-2" data-bs-boundary="viewport"
                  data-bs-toggle="dropdown" aria-expanded="false">
                  <IconDots />
                </button>
                <div class="dropdown-menu dropdown-menu-end">
                    <a @click="openParecer(item)" class="dropdown-item" href="#">
                        Visualizar
                    </a>
                </div>
              </td>
            </tr>
          </template>
        </Table>
      </template>
    </Navbar>

<!--    <ModalVincularABIO :contrato="contrato" :servico="servico" :licencas="licencas" ref="modalVincularABIO" />-->

    <ModalVisualizar ref="modalRef" />
  </AuthenticatedLayout>
</template>
