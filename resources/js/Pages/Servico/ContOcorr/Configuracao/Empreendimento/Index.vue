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
import ModalVisualizarShapefile from "./ModalVisualizarShapefile.vue";
import ModalvisualizarEmpreendimento from "./ModalvisualizarEmpreendimento.vue";
import ModalFormShapefile from "./ModalFormShapefile.vue";

const modalvisualizarEmpreendimento = ref({});
const modalVisualizarShapefile = ref({});
const modalFormShapefile = ref({});

const props = defineProps({
  contrato: { type: Object },
  servico: { type: Object },
  empreendimentos: { type: Object }
});

const abrirEmpreendimento = (item) => {
  modalvisualizarEmpreendimento.value.abrirModal(item);
}

const abrirShapefile = (item) => {
  modalVisualizarShapefile.value.abrirModal(item);
}

const abrirFormShapefile = (item) => {
  modalFormShapefile.value.abrirModal(item);
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
          </template>
        </ModelSearchFormAllColumns>

        <Table
          :columns="['UF inicial', 'UF final', 'BR', 'Empreendimento', 'KM inicial', 'KM final', 'Extensão', 'N° licença', 'Shapefile', 'Ação']"
          :records="empreendimentos" table-class="table-hover">
          <template #body="{ item }">
            <tr>
              <td class="w-8">
                <template v-if="item.licenca?.iniciais">
                  <span v-for="uf in item.licenca?.iniciais.split(',')" :key="uf"
                    class="badge bg-warning text-white m-1">
                    {{ uf }}
                  </span>
                </template>
              </td>
              <td class="w-8">
                <template v-if="item.licenca?.finais">
                  <span v-for="uf in item.licenca?.finais.split(',')" :key="uf" class="badge bg-warning text-white m-1">
                    {{ uf }}
                  </span>
                </template>
              </td>
              <td class="w-8">
                <template v-if="item.licenca?.brs">
                  <span v-for="br in item.licenca?.brs.split(',')" :key="br" class="badge bg-warning text-white m-1">
                    {{ br }}
                  </span>
                </template>
              </td>
              <td>{{ item.licenca?.empreendimento }}</td>
              <td>
                <template v-if="item.licenca?.segmentos.length">
                  {{ Math.min(...item.licenca?.segmentos.map(segmento => segmento.km_inicial)) }}
                </template>
              </td>
              <td>
                <template v-if="item.licenca?.segmentos.length">
                  {{ Math.max(...item.licenca?.segmentos.map(segmento => segmento.km_final)) }}
                </template>
              </td>
              <td>{{ item.licenca?.extensao }}</td>
              <td>{{ item.licenca?.numero_licenca }}</td>
              <td>
                <button v-if="item.licenca?.shapefile" @click="abrirShapefile(item)" class="btn btn-icon btn-primary">
                  <IconMap />
                </button>
                <span v-else>-</span>
              </td>
              <td>
                <NavButton v-if="!item.licenca?.shapefile" :icon="IconMap" class="btn-icon" type-button="success"
                  @click="abrirFormShapefile(item)" />
                <NavButton :icon="IconEye" class="btn-icon" type-button="primary" @click="abrirEmpreendimento(item)" />
              </td>
            </tr>
          </template>
        </Table>
      </template>
    </Navbar>

    <ModalvisualizarEmpreendimento ref="modalvisualizarEmpreendimento" />
    <ModalVisualizarShapefile ref="modalVisualizarShapefile" />
    <ModalFormShapefile :contrato="contrato" :servico="servico" ref="modalFormShapefile" />

  </AuthenticatedLayout>
</template>