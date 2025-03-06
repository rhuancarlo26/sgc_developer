<script setup>

import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import Navbar from "../../Navbar.vue";
import NavButton from "@/Components/NavButton.vue";
import ModelSearchFormAllColumns from "@/Components/ModelSearchFormAllColumns.vue";
import Table from "@/Components/Table.vue";
import { dateTimeFormat } from "@/Utils/DateTimeUtils";
import { IconDots } from "@tabler/icons-vue";
import ModalVincularABIO from "./ModalVincularABIO.vue";
import ModalAdicionarRet from "./ModalAdicionarRet.vue";
import LinkConfirmation from "@/Components/LinkConfirmation.vue";
import { ref } from "vue";
import { Head, Link, router } from '@inertiajs/vue3';

const props = defineProps({
  contrato: { type: Object },
  servico: { type: Object },
  vinculacoes: { type: Object },
  licencas: { type: Array }
});

const modalVincularABIORef = ref({});
const modalAdicionarRetRef = ref({});
const linkConfirmationRef = ref()

const abrirModalVincularABIO = () => {
  modalVincularABIORef.value.abrirModal();
}

const abrirModalAdicionarRet = (item) => {
  modalAdicionarRetRef.value.abrirModal(item);
}

const deleteVinculoAbio = (id) => {
  linkConfirmationRef.value.show(() => {
    router.delete(route('contratos.contratada.servicos.monitora_fauna.configuracoes.vincular_abio.destroy_abio', { contrato: props.contrato.id, servico: props.servico.id, abio: id }))
  })
}

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
            <NavButton @click="abrirModalVincularABIO()" type-button="info" title="Vincular ABIO" />
          </template>
        </ModelSearchFormAllColumns>

        <Table
          :columns="['Tipo', 'N° licença', 'Empreendimento', 'Emissor', 'Data de emissão', 'Vencimento', 'Responsável', 'Processo DNIT', 'Ação']"
          :records="vinculacoes" table-class="table-hover">
          <template #body="{ item }">
            <tr>
              <td>{{ item.licenca?.tipo_rel?.sigla }}</td>
              <td>{{ item.licenca?.numero_licenca }}</td>
              <td>{{ item.licenca?.empreendimento }}</td>
              <td>{{ item.licenca?.emissor }}</td>
              <td>{{ dateTimeFormat(item.licenca?.data_emissao) }}</td>
              <td>{{ dateTimeFormat(item.licenca?.vencimento) }}</td>
              <td>{{ item.licenca?.fiscal }}</td>
              <td>{{ item.licenca?.processo_dnit }}</td>
              <td>
                <button type="button" class="btn btn-icon btn-info dropdown-toggle p-2" data-bs-boundary="viewport"
                  data-bs-toggle="dropdown" aria-expanded="false">
                  <IconDots />
                </button>
                <div class="dropdown-menu dropdown-menu-end">
                  <a v-if="item.licenca?.arquivo_licenca" class="dropdown-item" target="_blank"
                    :href="route('licenca.documento.visualizar', item.id)">
                    Visualizar ABIO
                  </a>
                  <a v-if="item.rets?.length" target="_blank"
                    :href="route('contratos.contratada.servicos.monitora_fauna.configuracoes.vincular_abio.visualizar_abio_ret', { contrato: contrato.id, servico: servico.id, abio_ret: item.rets[0]?.id })"
                    class="dropdown-item">
                    Visualizar RET
                  </a>
                  <a @click="abrirModalAdicionarRet(item)" href="javascript:void(0);" class="dropdown-item">
                    Adicionar RET
                  </a>
                  <a @click="deleteVinculoAbio(item.id)" href="javascript:void(0);" class="dropdown-item">
                    Excluir registro
                  </a>
                </div>
              </td>
            </tr>
          </template>
        </Table>
      </template>
    </Navbar>

    <LinkConfirmation ref="linkConfirmationRef" :options="{ text: 'Excluir registro?' }" />
    <ModalVincularABIO :contrato="contrato" :servico="servico" :licencas="licencas" ref="modalVincularABIORef" />
    <ModalAdicionarRet :contrato="contrato" :servico="servico" ref="modalAdicionarRetRef" />

  </AuthenticatedLayout>
</template>
