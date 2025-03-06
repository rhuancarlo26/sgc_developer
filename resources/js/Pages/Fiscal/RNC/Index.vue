<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import Navbar from "../Navbar.vue";
import { Head, Link } from "@inertiajs/vue3";
import ModelSearchFormAllColumns from "@/Components/ModelSearchFormAllColumns.vue";
import Table from "@/Components/Table.vue";
import NavButton from "@/Components/NavButton.vue";
import { dateTimeFormat } from "@/Utils/DateTimeUtils";
import { ref } from "vue";
import ModalVisualizarOcorrenciaHistorico from "../../Servico/ContOcorr/Execucao/ModalOcorrencia/ModalVisualizarOcorrenciaHistorico.vue";
import ModalVisualizarOcorrencia
  from "../../Servico/ContOcorr/Execucao/ModalOcorrencia/ModalVisualizarOcorrencia.vue";
import ModalEmitirParecer from "./ModalEmitirParecer.vue";
import { IconDots } from "@tabler/icons-vue";

const props = defineProps({
  contrato: { type: Object },
  rncs: { type: Object }
});

const modalVisualizarOcorrenciaHistorico = ref({});
const modalVisualizarOcorrencia = ref({});
const modalEmitirParecer = ref({});

const calcPrazoCorrecao = (item) => {
  const prazo = parseInt(item.prazo);

  if (Number.isInteger(prazo) && item.data_ocorrencia) {
    const dataOcorrencia = new Date(item.data_ocorrencia);
    dataOcorrencia.setDate(dataOcorrencia.getDate() + prazo);

    const hoje = new Date();

    if (dataOcorrencia > hoje) {
      const dia = dataOcorrencia.getDate().toString().padStart(2, '0');
      const mes = (dataOcorrencia.getMonth() + 1).toString().padStart(2, '0');
      const ano = dataOcorrencia.getFullYear();
      return `${dia}/${mes}/${ano}`;
    } else {
      return 'vencido';
    }
  }

  return '';
};

const abrirModalOcorrenciaHistorico = (item) => {
  modalVisualizarOcorrenciaHistorico.value.abrirModal(item)
}

const abrirModalOcorrencia = (item) => {
  modalVisualizarOcorrencia.value.abrirModal(item);
}

const abrirModalEmitirParecer = (item) => {
  modalEmitirParecer.value.abrirModal(item);
}

</script>
<template>
  <Navbar :contrato="contrato">
    <template #body>
      <ModelSearchFormAllColumns :columns="[]">
        <template #action>
        </template>
      </ModelSearchFormAllColumns>
      <Table
        :columns="['ID', 'Intensidade', 'Data', 'Data fim', 'Ocorrência anterior', 'Prazo correção', 'Lote', 'Contrutora', 'Status aprovação', 'Status atendimento', 'Envio', 'Ação']"
        :records="rncs" table-class="table-hover">
        <template #body="{ item }">
          <tr>
            <td>{{ item.nome_id }}</td>
            <td>{{ item.intensidade }}</td>
            <td>{{ dateTimeFormat(item.data_ocorrencia) }}</td>
            <td>{{ dateTimeFormat(item.vistorias[item.vistorias.length - 1]?.data_fim) }}</td>
            <td>
              <span v-if="item.rnc_direto">RNC direto</span>
              <span v-else>{{ item.ocorrencia_anterior?.nome_id }}</span>
            </td>
            <td>
              <span v-if="calcPrazoCorrecao(item) === 'vencido'" class="btn btn-danger">{{ calcPrazoCorrecao(item)
                }}</span>
              <span v-else>{{ calcPrazoCorrecao(item) }}</span>
            </td>
            <td>{{ item.lote?.nome_id }}</td>
            <td>{{ item.lote?.empresa }}</td>
            <td>{{ item.aprovado_rnc }}</td>
            <td>{{ item.status }}</td>
            <td>{{ item.envio_empresa }}</td>
            <td>
              <button type="button" class="btn btn-icon btn-info dropdown-toggle p-2" data-bs-boundary="viewport"
                data-bs-toggle="dropdown" aria-expanded="false">
                <IconDots />
              </button>
              <div class="dropdown-menu dropdown-menu-end">
                <a @click="abrirModalOcorrenciaHistorico(item)" class="dropdown-item" href="javascript:void(0)">
                  Log de atividades
                </a>
                <a @click="abrirModalOcorrencia(item)" class="dropdown-item" href="javascript:void(0)">
                  Visualizar
                </a>
                <a class="dropdown-item" @click="abrirModalEmitirParecer(item)" href="javascript:void(0);">
                  Emitir Parecer
                </a>
              </div>
            </td>
          </tr>
        </template>
      </Table>
    </template>
  </Navbar>

  <ModalVisualizarOcorrenciaHistorico ref="modalVisualizarOcorrenciaHistorico" />
  <ModalVisualizarOcorrencia ref="modalVisualizarOcorrencia" />
  <ModalEmitirParecer :contrato="contrato" ref="modalEmitirParecer" />
  <!-- </AuthenticatedLayout> -->
</template>
