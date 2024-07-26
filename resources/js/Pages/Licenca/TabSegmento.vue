<script setup>
import Table from '@/Components/Table.vue';
import { IconTrash } from '@tabler/icons-vue';
import { IconEdit } from '@tabler/icons-vue';
import axios from 'axios';
import { onMounted } from 'vue';
import { ref } from 'vue';
import ModalFormLicencaSegmento from './ModalFormLicencaSegmento.vue';
import { IconPlus } from '@tabler/icons-vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
  licenca: { type: Object },
  ufs: { type: Array },
  rodovias: { type: Array },
});

let licencaSegmentos = ref({});
const modalFormSegmento = ref({});

onMounted(() => {
  if (props.licenca.id) {
    getLicencaSegmentos();
  }
})

const getLicencaSegmentos = () => {
  axios.get(route('licenca_segmento.get', props.licenca.id))
    .then(response => {
      licencaSegmentos.value = response.data;
    })
    .catch(response => {
      console.log(response);
    });
}

const abrirFormSegmento = (item) => {
  modalFormSegmento.value.abrirModal(item);
}

const excluirSegmento = (segmento_id) => {
  router.delete(route('licenca_segmento.delete', segmento_id), {
    onSuccess: () => getLicencaSegmentos()
  });
}

</script>
<template>
  <div class="row mb-4">
    <div class="d-flex justify-content-end">
      <a id="botaoAdicionarSegmento" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#adicionarSegmento"
        @click="abrirFormSegmento()">
        <IconPlus class="me-2" />
        Adicionar
      </a>
    </div>
  </div>

  <hr>

  <Table :columns="['BR', 'UF Inicial', 'UF Final', 'KM Inicial', 'KM Final', 'Extensão', 'Ação']"
    :records="licencaSegmentos" :axios-pagination="true" table-class="table-hover">
    <template #body="{ item }">
      <tr>
        <td class="text-center">
          {{ item.rodovia }}
        </td>
        <td class="text-center">
          {{ item.uf_inicial?.uf }}
        </td>
        <td class="text-center">
          {{ item.uf_final?.uf }}
        </td>
        <td class="text-center">
          {{ item.km_inicio }}
        </td>
        <td class="text-center">
          {{ item.km_fim }}
        </td>
        <td class="text-center">
          {{ item.extensao_br }}
        </td>
        <td class="text-center">
          <a class="btn align-text-top btn-info m-1" title="Editar" @click="abrirFormSegmento(item)"
            href="javascript:void(0)">
            <IconEdit />
          </a>
          <a @click="excluirSegmento(item.id)" class="btn align-text-top btn-danger m-1" title="Excluir"
            href="javascript:void(0)">
            <IconTrash />
          </a>
        </td>
      </tr>
    </template>
  </Table>

  <ModalFormLicencaSegmento @atualizarsegmento="getLicencaSegmentos()" :licenca="licenca" :ufs="ufs"
    :rodovias="rodovias" ref="modalFormSegmento" />

</template>
