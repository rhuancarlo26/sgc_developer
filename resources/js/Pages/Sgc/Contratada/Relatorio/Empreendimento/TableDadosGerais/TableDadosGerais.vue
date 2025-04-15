<script setup>
  import { ref, computed, onMounted } from 'vue';

  // Propriedades recebidas
  const props = defineProps({
    contrato: Object,
    empreendimentos: Array,
    empreendimentos2: Object,
    subprodutos: Object,
  });

  const hoje = new Date();

    // Função para formatar data para o formato brasileiro (dd/mm/aaaa)
    function formatarDataBrasileira(dataStr) {
    if (!dataStr) return 'Data não encontrada';
    const data = new Date(dataStr);
    if (isNaN(data)) return 'Data inválida';
    const dia = String(data.getDate()).padStart(2, '0');
    const mes = String(data.getMonth() + 1).padStart(2, '0'); // Mês começa em 0
    const ano = data.getFullYear();
    return `${dia}-${mes}-${ano}`;
  }

  function calcularDias(dataInicio, dataFinal) {
    const diffTime = dataFinal - dataInicio;
    return Math.ceil(diffTime / (1000 * 60 * 60 * 24)); 
  }

  function converterParaData(dataStr) {
    const data = new Date(dataStr);
    return isNaN(data) ? null : data;
  }

  async function fetchDadosContrato(numeroContrato) {
    try {
      const response = await fetch(`https://servicos.dnit.gov.br/DPP/api/contrato/dnit/${numeroContrato}`);
      if (!response.ok) {
        throw new Error('Erro na requisição da API');
      }

      const data = await response.json();
      console.log('Dados da API:', data);

      if (data.data && data.data.length > 0) {
        return data.data[0];
      } else {
        console.warn('Nenhum dado encontrado na resposta da API.');
        return null;
      }
    } catch (error) {
      console.error('Erro ao buscar dados da API:', error);
      return null;
    }
  }

  function formatarContratoParaApi(contratoEstAmbiental) {
    const numeroContrato = contratoEstAmbiental.replace('/', '').padStart(11, '0');
    return numeroContrato;
  }

  // Saldo em dias do contrato
  const saldoEmDiasContrato = ref('-');

  onMounted(async () => {
    const empreendimento = props.empreendimentos2;
    if (empreendimento && empreendimento.contrato_est_ambiental) {
      const numeroContrato = formatarContratoParaApi(empreendimento.contrato_est_ambiental);
      const dadosContrato = await fetchDadosContrato(numeroContrato);

      if (dadosContrato && dadosContrato.DT_INICIO) {
        const dataInicio = converterParaData(dadosContrato.DT_INICIO);
        if (dataInicio) {
          saldoEmDiasContrato.value = calcularDias(dataInicio, hoje);
        } else {
          saldoEmDiasContrato.value = 'Data de início inválida';
        }
      }
    }
  });

  // Computed para dias decorridos desde a OSE
  const diasDecorridos = computed(() => {
    const empreendimento = props.empreendimentos2;

    if (empreendimento && empreendimento.ose_data) {
      const oseData = converterParaData(empreendimento.ose_data);
      if (oseData) {
        return calcularDias(oseData, hoje);
      } else {
        return 'Data OSE inválida';
      }
    }

    return 'Dados insuficientes';
  });

  // Data OSE formatada
  const oseData = computed(() => {
    const empreendimento = props.empreendimentos2;
    return empreendimento && empreendimento.ose_data ? formatarDataBrasileira(empreendimento.ose_data) : 'Data não encontrada';
  });

  // Dividir os contratos do projeto de engenharia
  const contratosProjeto = computed(() => {
    const contratos = props.empreendimentos2.contrato_projeto;
    return contratos ? contratos.match(/00\s\d{5}\/\d{4}/g) || [] : [];
  });

  // Dividir os processos do projeto
  const processosProjeto = computed(() => {
    const processos = props.empreendimentos2.processo_projeto;
    return processos ? processos.match(/\d{5}\.\d{6}\/\d{4}-\d{2}/g) || [] : [];
  });
  
</script>

<template>
  <div class="container-cards">
    <div class="card-columns">
      <!-- Quadro Estudos Ambientais -->
      <div class="card">
        <h3 class="card-title">ESTUDOS AMBIENTAIS</h3>
        <ul class="list-group">
                    
          <!-- Bloco 1: Informações sobre Competência e Contrato -->
          <ul class="list-group" style="border: 1px solid #ddd; border-radius: 5px; padding: 10px; flex: 1; margin: 5px;">
            <li class="list-group-item" style="border: none; padding: 5px 0;">
              <strong>Competência do Licenciamento:</strong> {{ empreendimentos2.competencia }}
            </li>
            <li class="list-group-item" style="border: none; padding: 5px 0;">
              <strong>Fase do Licenciamento Ambiental:</strong> {{ empreendimentos2.fase_do_licenciamento }}
            </li>
            <li class="list-group-item" style="border: none; padding: 5px 0;">
              <strong>Processo Licenciamento DNIT:</strong> {{ empreendimentos2.processo_licenciamento_dnit }}
            </li>
            <li class="list-group-item" style="border: none; padding: 5px 0;">
              <strong>Contrato/Empresa Estudos Ambientais:</strong> {{ empreendimentos2.contrato_est_ambiental }}
            </li>
            <li class="list-group-item" style="border: none; padding: 5px 0;" title="Nº SEI - FCA">
              <strong>FCA:</strong> SEI DNIT {{ empreendimentos2.fca_sei }} / {{ formatarDataBrasileira(empreendimentos2.fca_data) }}
            </li>
            <li class="list-group-item" style="border: none; padding: 5px 0;" title="Nº SEI - TRE">
              <strong>TRE:</strong> SEI DNIT {{ empreendimentos2.tre_sei_dnit }} / {{ formatarDataBrasileira(empreendimentos2.tre_data) }}
            </li>
            <li class="list-group-item" style="border: none; padding: 5px 0;">
              <strong>Plano de Trabalho:</strong> SEI + Data
            </li>
            <li class="list-group-item" style="border: none; padding: 5px 0;">
              <strong>OSE:</strong>  SEI DNIT {{ empreendimentos2.ose_sei }} / {{ oseData }}
            </li>
          </ul>
          
          <!-- Bloco 2: Informações sobre Origem da Demanda -->
          <ul class="list-group" style="border: 1px solid #ddd; border-radius: 5px; padding: 10px; flex: 1; margin: 5px;">
            <li class="list-group-item" style="border: none; padding: 5px 0;">
              <strong>Origem da Demanda:</strong> {{ empreendimentos2.origem }}
            </li>
            <li class="list-group-item" style="border: none; padding: 5px 0;">
              <strong>Origem da Demanda SEI:</strong> {{ empreendimentos2.origem_sei }}
            </li>
            <li class="list-group-item" style="border: none; padding: 5px 0;">
              <strong>Origem da Demanda DATA:</strong> {{ formatarDataBrasileira(empreendimentos2.origem_data) }}
            </li>
          </ul>
        </ul>  
      </div>

      <!-- Quadro EVTEA/Projeto -->
      <div class="card">
        <h3 class="card-title">EVTEA/PROJETO</h3>
        <ul class="list-group">
          <ul class="list-group" style="border: 1px solid #ddd; border-radius: 5px; padding: 10px; flex: 1; margin: 5px;">
            <li class="list-group-item" style="border: none; padding: 5px 0;">
              <strong>Contrato do Projeto de Engenharia:</strong>
              <ul class="contratos-list">
                <li v-for="(contrato, index) in contratosProjeto" :key="index" class="contrato-item">
                  {{ contrato }}
                </li>
              </ul>
            </li>

            <li class="list-group-item" style="border: none; padding: 5px 0;">
              <strong>Processo Projeto:</strong>
              <ul class="contratos-list">
                <li v-for="(processo, index) in processosProjeto" :key="index" class="contrato-item">
                  {{ processo }}
                </li>
              </ul>
            </li>

            <li class="list-group-item" style="border: none; padding: 5px 0;">
              <strong>Processo EVTEA:</strong> {{ empreendimentos2.processo_evtea }}
            </li>
          </ul>
        </ul>
      </div>
    </div>
  </div>
</template>

<style scoped>
  .container-cards {
    padding: 20px;
    background-color: white;
  }
  
  .card-columns {
    display: flex;
    gap: 20px;
    justify-content: space-between;
  }
  
  .card {
    flex: 1;
    background-color: #fdfdfd;
    border: 1px solid #5a595e;
    border-radius: 10px;
    padding: 0;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    overflow: hidden;
  }
  
  .card-title {
    font-size: 17px;
    text-align: center;
    font-weight: bold;
    padding: 15px;
    background-color: #dde1e4;
    color: rgb(10, 1, 1);
    margin: 0;
  }
  
  .list-group {
    padding-left: 0;
    margin: 0;
    list-style: none;
  }
  
  .list-group-item {
    font-size: 15.5px;
    padding: 10px;
    border-top: 0px solid #e9e6e6;
    text-align: center;
  }
  
  .list-group-item:first-of-type {
    border-top: none;
  }
  
  .list-group-item strong {
    font-weight: bold;
  }

  .contratos-list {
    padding-left: 0;
    list-style: none; 
    text-align: center;
    margin-top: 5px;
  }

  .contrato-item {
    font-size: 14px;
    margin-bottom: 3px;
  }
</style>
