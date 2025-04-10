<script setup>
import { Head } from "@inertiajs/vue3";
import { onMounted, ref } from "vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import Navbar from "../Navbar.vue";
import DocxModal from "./DocxModal.vue";
import NavLinkSgc from "@/Components/NavLinkSgc.vue";
import { Link, useForm, usePage } from '@inertiajs/vue3';
import { defineProps } from 'vue';
import { computed } from 'vue';
import { IconCheck, IconMessageDots, IconX, IconCircle } from "@tabler/icons-vue";
import { atualizarStatus, revisaoStatus, aprovadoStatus } from './AtualizarStatus/statusUpdate.js';
import { toggleAprovado } from './AtualizarStatus/aprovarItem.js';


const user = usePage().props.auth.user;
const itens = ref([]);
const docxModal = ref();
const selectedItemId = ref(null);



const props = defineProps({
  contrato: Object,
  dadosrelat: { type: Array },
  update_anexo: Object,
  comentarios: Object
});

const form = useForm({
  id: null,
  contrato_id: props.contrato.id,
  caminho: null,
  arquivo: null,
  versao: null,
  item_id: null,
  name: user.name,
  email: user.email,
  update_anexo: props.updated_at,
  relatorio_num: props.dadosrelat.length > 0 ? props.dadosrelat[0].relatorio_num : null 
});

const showDiv = computed(() => {
  return !user.roles.some(role => role.name === 'Fiscal');
});

const selecionarItem = (idItem) => {
  form.item_id = idItem;
};

const abrirDoc = (idItem) => {
  const contratoId = form.contrato_id;
  const itemVersion = props.update_anexo[idItem]?.versao; // Adicionar versão aqui
  selectedItemId.value = idItem;
  docxModal.value.abrirModal(idItem, contratoId, itemVersion); // Passar a versão
};


const getUpdatedAt = (idItem) => {
  return props.update_anexo[idItem]?.updated_at || 'Não Inserido';
};

const obterItens = async () => {
  try {
    const response = await fetch(route('sgc.relatorio_coordenacao.index'));
    const data = await response.json();

    if (data.length > 0) {
      form.item_id = data[0].id_item;
    }

    itens.value = data;
  } catch (error) {
    console.error('Erro ao obter os itens:', error);
  }
};
obterItens();

const salvarAnexo = () => {
  if (form.contrato_id) {
    form.post(route('sgc.contratada.store_anexo'), {
      onSuccess: () => {
        form.reset();
        document.getElementById('inputfile').value = null;
      }
    });
  } else {
    form.post(route('sgc.contratada.store_anexo'), {
      onSuccess: () => {
        form.reset();
        document.getElementById('inputfile').value = null;
      }
    });
  }
};

const enviarParaDnit = async () => {
  const contratoId = form.contrato_id;
  const itemId = form.item_id;
  const relatorioNum = form.relatorio_num;
  await atualizarStatus(contratoId, itemId, relatorioNum, itens.value);
  window.location.reload();

  // Enviar e-mail
  try {
    await axios.get(`/sgc/contratada/send-email/${contratoId}/Em%20An%C3%A1lise/${form.relatorio_num}`);
  } catch (error) {
    console.error('Erro ao enviar e-mail:', error);
  }
};

const enviarParaRevisao = async () => {
  const contratoId = form.contrato_id;
  const itemId = form.item_id;
  const relatorioNum = form.relatorio_num;
  await revisaoStatus(contratoId, itemId, relatorioNum, itens.value); 
  window.location.reload();

  // Enviar e-mail
  try {
    await axios.get(`/sgc/contratada/send-email/${contratoId}/Em%20Revis%C3%A3o/${form.relatorio_num}`);
  } catch (error) {
    console.error('Erro ao enviar e-mail:', error);
  }
};

const aprovarRelatorio = async () => {
  const contratoId = form.contrato_id;
  const itemId = form.item_id;
  const relatorioNum = form.relatorio_num;
  await aprovadoStatus(contratoId, itemId, relatorioNum, itens.value); 
  window.location.reload();
  
  // Enviar e-mail
  try {
    await axios.get(`/sgc/contratada/send-email/${contratoId}/Em%20Revis%C3%A3o/${form.relatorio_num}`);
  } catch (error) {
    console.error('Erro ao enviar e-mail:', error);
  }
};

const obterStatusRelatorio = (itemId, relatorioNum) => {
  const item = props.dadosrelat.find(i => i.id_item === itemId && i.relatorio_num === relatorioNum);
  return item ? item.status : 'Status Desconhecido';
};

const isDisabledEnviarParaDnit = () => {
  const status = obterStatusRelatorio(form.item_id, form.relatorio_num);
  return status === 'Análise DNIT' || status === 'Relatório Aprovado';
};

const isDisabledEnviarParaRevisao = () => {
  const status = obterStatusRelatorio(form.item_id, form.relatorio_num);
  return status === 'Revisão Contratada' || status === 'Relatório Aprovado' || status === 'Em Elaboração';
};

const isDisabledAprovarRelatorio = () => {
  const status = obterStatusRelatorio(form.item_id, form.relatorio_num);
  return status === 'Revisão Contratada' || status === 'Relatório Aprovado' || status === 'Em Elaboração';
};

const downloadFile = (itemId) => {
  const url = route('sgc.contratada.download_anexo', {
      contratoId: props.contrato.id,
      itemId: itemId, // Corrigir nome do parâmetro
      relatorioNum: form.relatorio_num
  });
  window.location.href = url;
};

const filteredRelatorios = ref([]);

const filtrarRelatorios = () => {
  const seen = new Set();
  filteredRelatorios.value = props.dadosrelat.filter(relatorio => {
    if (seen.has(relatorio.relatorio_num)) {
      return false;
    }
    seen.add(relatorio.relatorio_num);
    return true;
  });
};

const temComentarios = (itemId) => {
    return props.comentarios.some(comentario => comentario.item_id === itemId);
};

const aprovarItem = async (item, contratoId, relatorioNum) => {
    if (item.aprovado !== 1) {
        await toggleAprovado(item, contratoId, relatorioNum, 1); 
    }
};

const reprovarItem = async (item, contratoId, relatorioNum) => {
    if (item.aprovado !== 0) {
        await toggleAprovado(item, contratoId, relatorioNum, 0); 
    }
};

onMounted(() => {
  filtrarRelatorios();
});

</script>

<template>
  <div>
    <Head :title="`${contrato.contratada.slice(0, 10)}...`" />
    <AuthenticatedLayout>
      <template #header>
        <div class="w-100 d-flex justify-content-between">
          <Breadcrumb
            class="align-self-center"
            :links="[
              { route: route('contratos.gestao.listagem', contrato.tipo_contrato), label: `Gestão de Contratos` },
              { route: '#', label: contrato.contratada }
            ]"
          />
        </div>
      </template>

      <Navbar :contrato="contrato">
        <template #body>
          <div class="container mt-4">
            <div class="row mb-3">
              <div class="col-12 text-center">
                <div class="titulo-relatorio">
                  {{ form.relatorio_num }}º RELATÓRIO DE COORDENAÇÃO
                </div>
              </div>
            </div>
            <div class="row mb-3">
              <div class="col-12 text-center">
                <div v-if="itens.length > 0" class="status-relatorio btn btn-light p-2">
                  STATUS DO RELATÓRIO - {{ obterStatusRelatorio(form.item_id, form.relatorio_num) }}
                </div>
              </div>
            </div>
            <div class="row mb-4">
              <div class="col-12 d-flex justify-content-end gap-2">
                <div class="button-wrapper">
                  <NavLinkSgc
                    route-name="sgc.relatorio_coordenacao.update_status"
                    class="btn btn-outline-warning"
                    title="Enviar para Análise"
                    @click="enviarParaDnit"
                    :disabled="isDisabledEnviarParaDnit()"
                  />
                </div>
                <div class="button-wrapper">
                  <NavLinkSgc
                    route-name="sgc.relatorio_coordenacao.revisao_status"
                    class="btn btn-outline-primary"
                    title="Enviar para Revisão"
                    @click="enviarParaRevisao"
                    :disabled="isDisabledEnviarParaRevisao()"
                  />
                </div>
                <div class="button-wrapper">
                  <NavLinkSgc 
                    route-name="sgc.relatorio_coordenacao.aprovado_status"
                    class="btn btn-outline-success"
                    title="Aprovar Relatório"
                    @click="aprovarRelatorio"
                    :disabled="isDisabledAprovarRelatorio()"
                  />
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-12">
                <table class="table table-striped">
                  <thead>
                    <tr style="background-color: #237D9E; text-align: left;">
                      <th scope="col">Item Relatório</th>
                      <th scope="col">Última Atualização</th>
                      <th v-if="showDiv" scope="col">Inserir Arquivo</th>
                      <th scope="col">Ações</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="item in dadosrelat" :key="item.id">
                      <td>
                          <span v-if="item.aprovado === 1" class="text-success">
                              <IconCheck size="16" />
                          </span>
                          <span v-else-if="item.aprovado === 0" class="text-danger">
                              <IconX size="16" />
                          </span>
                          <span v-else-if="item.aprovado === 2" class="text-info">
                              <IconCircle size="16" />
                          </span>
                          {{ item.nome_topico }}
                          <span v-if="temComentarios(item.id_item)" class="ms-2">
                              <IconMessageDots size="21" />
                          </span>
                      </td>

                      <td>{{ getUpdatedAt(item.id_item) }}</td>

                      <td v-if="showDiv" class="d-flex align-items-center mt-2">
                        <div class="input-wrapper">
                          <input @input="form.arquivo = $event.target.files[0]" id="inputfile" type="file" class="form-control mr-2" style="width: auto;" :disabled="item.status === 'Análise DNIT' || item.status === 'Relatório Aprovado' || item.aprovado === 1"/>
                        </div>
                        <div class="button-wrapper">
                          <button class="btn" @click="selecionarItem(item.id_item); salvarAnexo()" :disabled="item.status === 'Análise DNIT' || item.status === 'Relatório Aprovado' || item.aprovado === 1">
                            Salvar
                          </button>
                        </div>
                      </td>
                      <td class="align-middle">
                        <button v-if="item.id_item >= 17" @click="downloadFile(item.id_item)" class="bg-blue-500 text-black px-4 py-2 rounded">Anexo</button>
                        <button v-else @click="abrirDoc(item.id_item)" class="btn btn-success mr-2">Abrir</button>
                      </td>

                      <td class="align-middle">
                        <template v-if="user.roles.some(role => role.name === 'Fiscal') && item.status === 'Análise DNIT'">
                            <button @click="aprovarItem(item, props.contrato.id, form.relatorio_num)" 
                                    class="btn btn-sm btn-success me-1" 
                                    :disabled="item.aprovado === 1">
                                Aprovar
                            </button>
                            <button @click="reprovarItem(item, props.contrato.id, form.relatorio_num)" 
                                    class="btn btn-sm btn-danger" 
                                    :disabled="item.aprovado === 0">
                                Reprovar
                            </button>
                        </template>
                      </td>

                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </template>
      </Navbar>
      <DocxModal ref="docxModal"
        href="javascript:void(0)"
        :itemId="selectedItemId"
        :comentarios="comentarios"
        :contrato="contrato"
        :numRelatorio="form.relatorio_num"
      />
    </AuthenticatedLayout>
  </div>
</template>

<style scoped>
.titulo-relatorio {
  background-color: #fcfcfc;
  padding: 10px 20px;
  color: rgb(14, 34, 54);
  font-size: 1.3em;
  font-weight: bold;
  border-radius: 8px;
  margin-bottom: 10px;
}

.status-relatorio {
  background-color: #ffffff;
  padding: 10px;
  border: 1px solid #f1f1f1;
  border-radius: 9px;
  font-size: 1.0em;
}

.ms-2 {
    margin-left: 8px;
    color: #2808b9;
}
</style>

