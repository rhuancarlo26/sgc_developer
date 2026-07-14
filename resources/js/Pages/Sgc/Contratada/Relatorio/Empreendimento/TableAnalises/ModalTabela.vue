<script setup>
import { ref, onMounted } from 'vue';

const props = defineProps({
  contrato: Object,
  empreendimentos: Array,
  estudos: Array,
});

const tabelaItens = ref([]);
const showModal = ref(false); // Controla a visibilidade do modal
const modalData = ref({}); // Armazena os dados a serem exibidos no modal

// Tabela de Subprodutos do Estudo Ambiental 
const criarTabelaItens = (empreendimentos) => {
  empreendimentos.forEach(element => {
    props.estudos.forEach(estudo => {
      if (estudo.cod_emp === element.cod_emp) {
        const {
          cod_siac,
          produto,
          item_edital,
          subproduto,
          familia,
          qtd_ose,
          r_ose,
          data_de_inicio_previsto,
          data_de_entrega_previsto,
          versao_00_sei,
          versao_00_data_de_entrega,
          versao_00_sei_nt,
          versao_00_data_nt,
          versao_00_sei_oficio,
          versao_00_data_oficio,
          versao_01_sei,
          versao_01_data_de_entrega,
          versao_01_sei_nt,
          versao_01_data_nt,
          versao_01_sei_oficio,
          versao_01_data_oficio,
          versao_02_sei,
          versao_02_data_de_entrega,
          versao_02_sei_nt,
          versao_02_data_nt,
          versao_02_sei_oficio,
          versao_02_data_oficio,
          versao_03_sei,
          versao_03_data_de_entrega,
          versao_03_sei_nt,
          versao_03_data_nt,
          versao_03_sei_oficio,
          versao_03_data_oficio,
          versao_04_sei,
          versao_04_data_de_entrega,
          versao_04_sei_nt,
          versao_04_data_nt,
          versao_04_sei_oficio,
          versao_04_data_oficio,
          versao_aceita_sei,
          versao_aceita_data,
          req_ext_sei,
          req_ext_data,
          analise_ext_01_sei,
          analise_ext_01_data,
          versao_ext_01_sei,
          versao_ext_01_data,
          analise_ext_02_sei,
          analise_ext_02_data,
          versao_ext_02_sei,
          versao_ext_02_data,
          analise_ext_03_sei,
          analise_ext_03_data,
          versao_ext_03_sei,
          versao_ext_03_data,
          analise_ext_04_sei,
          analise_ext_04_data,
          versao_ext_04_sei,
          versao_ext_04_data,
          aut_ext_sei,
          aut_ext_data,
        } = estudo;

        tabelaItens.value.push({
          cod_siac,
          produto,
          item_edital,
          subproduto,
          familia,
          qtd_ose,
          r_ose,
          data_de_inicio_previsto: data_de_inicio_previsto ? new Date(data_de_inicio_previsto).toLocaleDateString() : null,
          data_de_entrega_previsto: data_de_entrega_previsto ? new Date(data_de_entrega_previsto).toLocaleDateString() : null,
          versao_00_sei,
          versao_00_data_de_entrega,
          versao_00_sei_nt,
          versao_00_data_nt,
          versao_00_sei_oficio,
          versao_00_data_oficio,
          versao_01_sei,
          versao_01_data_de_entrega,
          versao_01_sei_nt,
          versao_01_data_nt,
          versao_01_sei_oficio,
          versao_01_data_oficio,
          versao_02_sei,
          versao_02_data_de_entrega,
          versao_02_sei_nt,
          versao_02_data_nt,
          versao_02_sei_oficio,
          versao_02_data_oficio,
          versao_03_sei,
          versao_03_data_de_entrega,
          versao_03_sei_nt,
          versao_03_data_nt,
          versao_03_sei_oficio,
          versao_03_data_oficio,
          versao_04_sei,
          versao_04_data_de_entrega,
          versao_04_sei_nt,
          versao_04_data_nt,
          versao_04_sei_oficio,
          versao_04_data_oficio,
          versao_aceita_sei,
          versao_aceita_data,
          req_ext_sei,
          req_ext_data,
          analise_ext_01_sei,
          analise_ext_01_data,
          versao_ext_01_sei,
          versao_ext_01_data,
          analise_ext_02_sei,
          analise_ext_02_data,
          versao_ext_02_sei,
          versao_ext_02_data,
          analise_ext_03_sei,
          analise_ext_03_data,
          versao_ext_03_sei,
          versao_ext_03_data,
          analise_ext_04_sei,
          analise_ext_04_data,
          versao_ext_04_sei,
          versao_ext_04_data,
          aut_ext_sei,
          aut_ext_data,
        });
      }
    });
  });
};

// Tabela Subprodutos: contar as versões DNIT
const contarVersoesDnit = (item) => {
  let count = 0;
  if (item.versao_00_sei) count++;
  if (item.versao_01_data_de_entrega) count++;
  if (item.versao_02_sei) count++;
  if (item.versao_03_sei) count++;
  if (item.versao_04_sei) count++;
  return count;
};

// Tabela Subprodutos: contar as versões externas
const contarVersoesExterno = (item) => {
  let count = 0;
  if (item.req_ext_sei) { 
    if (item.versao_ext_01_sei) count++;
    if (item.versao_ext_02_sei) count++;
    if (item.versao_ext_03_sei) count++;
    if (item.versao_ext_04_sei) count++;
  }
  return count;
};

// Função para abrir o modal com os dados de uma linha específica
const abrirModal = (item) => {
  modalData.value = item; 
  showModal.value = true; 
};

// Tabela Subprodutos: tempo de análise DNIT
const calcularTempoAnaliseDnit = (item) => {
  if (!item.versao_00_data_de_entrega) {
    return "";
  }

  const dataDeEntrega = new Date(item.versao_00_data_de_entrega);
  
  const datasNt = [
    item.versao_04_data_nt,
    item.versao_03_data_nt,
    item.versao_02_data_nt,
    item.versao_01_data_nt,
    item.versao_00_data_nt
  ].filter(Boolean);

  if (datasNt.length === 0) {
    return "";
  }

  const ultimaDataNt = new Date(datasNt[0]); 

  const diffTime = Math.abs(ultimaDataNt - dataDeEntrega);
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

  return `${diffDays} dias`;
};

// Tabela Subprodutos: tempo de análise Externo
const calcularTempoAnaliseExterno = (item) => {
  if (!item.req_ext_data) {
    return "";
  }

  const dataDeRequisicaoExterna = new Date(item.req_ext_data);

  const datasExternas = [
    item.aut_ext_data,
    item.analise_ext_04_data,
    item.analise_ext_03_data,
    item.analise_ext_02_data,
    item.analise_ext_01_data
  ].filter(Boolean); 

  if (datasExternas.length === 0) {
    return "";
  }

  const ultimaDataExterna = new Date(datasExternas[0]); 

  const diffTime = Math.abs(ultimaDataExterna - dataDeRequisicaoExterna);
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

  return `${diffDays} dias`;
};

onMounted(() => {
  criarTabelaItens(props.empreendimentos);
});
</script>

<template>
  <div class="container my-4">
    <!-- Tabela dos Subprodutos -->
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header">
            Tabela de Subprodutos
          </div>
          <div class="card-body">
            <table class="table table-bordered">
              <thead>
                <tr>
                  <th>Cod SIAC</th>
                  <th>Produto</th>
                  <th>Item Edital</th>
                  <th>Subproduto</th>
                  <th>Família</th>
                  <th>Qtd OSE</th>
                  <th>R$ OSE</th>
                  <th>Data de Início Previsto</th>
                  <th>Data de Entrega Previsto</th>
                  <th>Qtd de Versões DNIT</th>
                  <th>Qtd de Versões Externo</th>
                  <th>Tempo de Análise DNIT</th>
                  <th>Tempo de Análise Externo</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(item, index) in tabelaItens" :key="index" @click="abrirModal(item)">
                  <td>{{ item.cod_siac }}</td>
                  <td>{{ item.produto }}</td>
                  <td>{{ item.item_edital }} <i class="fa fa-eye" @click.stop="abrirModal(item)" style="cursor: pointer;"></i></td>
                  <td>{{ item.subproduto }}</td>
                  <td>{{ item.familia }}</td>
                  <td>{{ item.qtd_ose }}</td>
                  <td>{{ item.r_ose }}</td>
                  <td>{{ item.data_de_inicio_previsto }}</td>
                  <td>{{ item.data_de_entrega_previsto }}</td>
                  <td>{{ contarVersoesDnit(item) }}</td>
                  <td>{{ contarVersoesExterno(item) }}</td>
                  <td>{{ calcularTempoAnaliseDnit(item) }}</td>
                  <td>{{ calcularTempoAnaliseExterno(item) }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal -->
    <div v-if="showModal" class="modal fade show d-block" tabindex="-1">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Detalhes do Item</h5>
            <button type="button" class="btn-close" @click="showModal = false"></button>
          </div>
          <div class="modal-body">
            <table class="table table-bordered">
              <thead>
                <tr>
                  <th>Campo</th>
                  <th>Valor</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>Data de Início Previsto</td>
                  <td>{{ modalData.data_de_inicio_previsto }}</td>
                </tr>
                <tr>
                  <td>Data de Entrega Previsto</td>
                  <td>{{ modalData.data_de_entrega_previsto }}</td>
                </tr>
                <tr>
                  <td>Versão 00 SEI</td>
                  <td>{{ modalData.versao_00_sei }}</td>
                </tr>
                <tr>
                  <td>Versão 00 Data de Entrega</td>
                  <td>{{ modalData.versao_00_data_de_entrega }}</td>
                </tr>
                <tr>
                  <td>Versão 00 SEI NT</td>
                  <td>{{ modalData.versao_00_sei_nt }}</td>
                </tr>
                <tr>
                  <td>Versão 00 Data NT</td>
                  <td>{{ modalData.versao_00_data_nt }}</td>
                </tr>
                <tr>
                  <td>Versão 00 SEI Ofício</td>
                  <td>{{ modalData.versao_00_sei_oficio }}</td>
                </tr>
                <tr>
                  <td>Versão 00 Data Ofício</td>
                  <td>{{ modalData.versao_00_data_oficio }}</td>
                </tr>
                <tr>
                  <td>Versão 01 SEI</td>
                  <td>{{ modalData.versao_01_sei }}</td>
                </tr>
                <tr>
                  <td>Versão 01 Data de Entrega</td>
                  <td>{{ modalData.versao_01_data_de_entrega }}</td>
                </tr>
                <tr>
                  <td>Versão 01 SEI NT</td>
                  <td>{{ modalData.versao_01_sei_nt }}</td>
                </tr>
                <tr>
                  <td>Versão 01 Data NT</td>
                  <td>{{ modalData.versao_01_data_nt }}</td>
                </tr>
                <tr>
                  <td>Versão 01 SEI Ofício</td>
                  <td>{{ modalData.versao_01_sei_oficio }}</td>
                </tr>
                <tr>
                  <td>Versão 01 Data Ofício</td>
                  <td>{{ modalData.versao_01_data_oficio }}</td>
                </tr>
                <tr>
                  <td>Versão 02 SEI</td>
                  <td>{{ modalData.versao_02_sei }}</td>
                </tr>
                <tr>
                  <td>Versão 02 Data de Entrega</td>
                  <td>{{ modalData.versao_02_data_de_entrega }}</td>
                </tr>
                <tr>
                  <td>Versão 02 SEI NT</td>
                  <td>{{ modalData.versao_02_sei_nt }}</td>
                </tr>
                <tr>
                  <td>Versão 02 Data NT</td>
                  <td>{{ modalData.versao_02_data_nt }}</td>
                </tr>
                <tr>
                  <td>Versão 02 SEI Ofício</td>
                  <td>{{ modalData.versao_02_sei_oficio }}</td>
                </tr>
                <tr>
                  <td>Versão 02 Data Ofício</td>
                  <td>{{ modalData.versao_02_data_oficio }}</td>
                </tr>
                <tr>
                  <td>Versão 03 SEI</td>
                  <td>{{ modalData.versao_03_sei }}</td>
                </tr>
                <tr>
                  <td>Versão 03 Data de Entrega</td>
                  <td>{{ modalData.versao_03_data_de_entrega }}</td>
                </tr>
                <tr>
                  <td>Versão 03 SEI NT</td>
                  <td>{{ modalData.versao_03_sei_nt }}</td>
                </tr>
                <tr>
                  <td>Versão 03 Data NT</td>
                  <td>{{ modalData.versao_03_data_nt }}</td>
                </tr>
                <tr>
                  <td>Versão 03 SEI Ofício</td>
                  <td>{{ modalData.versao_03_sei_oficio }}</td>
                </tr>
                <tr>
                  <td>Versão 03 Data Ofício</td>
                  <td>{{ modalData.versao_03_data_oficio }}</td>
                </tr>
                <tr>
                  <td>Versão 04 SEI</td>
                  <td>{{ modalData.versao_04_sei }}</td>
                </tr>
                <tr>
                  <td>Versão 04 Data de Entrega</td>
                  <td>{{ modalData.versao_04_data_de_entrega }}</td>
                </tr>
                <tr>
                  <td>Versão 04 SEI NT</td>
                  <td>{{ modalData.versao_04_sei_nt }}</td>
                </tr>
                <tr>
                  <td>Versão 04 Data NT</td>
                  <td>{{ modalData.versao_04_data_nt }}</td>
                </tr>
                <tr>
                  <td>Versão 04 SEI Ofício</td>
                  <td>{{ modalData.versao_04_sei_oficio }}</td>
                </tr>
                <tr>
                  <td>Versão 04 Data Ofício</td>
                  <td>{{ modalData.versao_04_data_oficio }}</td>
                </tr>
                <tr>
                  <td>Versão Aceita SEI</td>
                  <td>{{ modalData.versao_aceita_sei }}</td>
                </tr>
                <tr>
                  <td>Versão Aceita Data</td>
                  <td>{{ modalData.versao_aceita_data }}</td>
                </tr>
                <tr>
                  <td>Req Ext SEI</td>
                  <td>{{ modalData.req_ext_sei }}</td>
                </tr>
                <tr>
                  <td>Req Ext Data</td>
                  <td>{{ modalData.req_ext_data }}</td>
                </tr>
                <tr>
                  <td>Análise Ext 01 SEI</td>
                  <td>{{ modalData.analise_ext_01_sei }}</td>
                </tr>
                <tr>
                  <td>Análise Ext 01 Data</td>
                  <td>{{ modalData.analise_ext_01_data }}</td>
                </tr>
                <tr>
                  <td>Versão Ext 01 SEI</td>
                  <td>{{ modalData.versao_ext_01_sei }}</td>
                </tr>
                <tr>
                  <td>Versão Ext 01 Data</td>
                  <td>{{ modalData.versao_ext_01_data }}</td>
                </tr>
                <tr>
                  <td>Análise Ext 02 SEI</td>
                  <td>{{ modalData.analise_ext_02_sei }}</td>
                </tr>
                <tr>
                  <td>Análise Ext 02 Data</td>
                  <td>{{ modalData.analise_ext_02_data }}</td>
                </tr>
                <tr>
                  <td>Versão Ext 02 SEI</td>
                  <td>{{ modalData.versao_ext_02_sei }}</td>
                </tr>
                <tr>
                  <td>Versão Ext 02 Data</td>
                  <td>{{ modalData.versao_ext_02_data }}</td>
                </tr>
                <tr>
                  <td>Análise Ext 03 SEI</td>
                  <td>{{ modalData.analise_ext_03_sei }}</td>
                </tr>
                <tr>
                  <td>Análise Ext 03 Data</td>
                  <td>{{ modalData.analise_ext_03_data }}</td>
                </tr>
                <tr>
                  <td>Versão Ext 03 SEI</td>
                  <td>{{ modalData.versao_ext_03_sei }}</td>
                </tr>
                <tr>
                  <td>Versão Ext 03 Data</td>
                  <td>{{ modalData.versao_ext_03_data }}</td>
                </tr>
                <tr>
                  <td>Análise Ext 04 SEI</td>
                  <td>{{ modalData.analise_ext_04_sei }}</td>
                </tr>
                <tr>
                  <td>Análise Ext 04 Data</td>
                  <td>{{ modalData.analise_ext_04_data }}</td>
                </tr>
                <tr>
                  <td>Versão Ext 04 SEI</td>
                  <td>{{ modalData.versao_ext_04_sei }}</td>
                </tr>
                <tr>
                  <td>Versão Ext 04 Data</td>
                  <td>{{ modalData.versao_ext_04_data }}</td>
                </tr>
                <tr>
                  <td>Aut Ext SEI</td>
                  <td>{{ modalData.aut_ext_sei }}</td>
                </tr>
                <tr>
                  <td>Aut Ext Data</td>
                  <td>{{ modalData.aut_ext_data }}</td>
                </tr>
              </tbody>
            </table>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" @click="showModal = false">Fechar</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.modal.fade.show.d-block {
  display: block;
  opacity: 1;
  transition: opacity 0.15s linear;
}
</style>
