<script setup>
import { useForm } from "@inertiajs/vue3";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import { useToast } from "vue-toastification";
import axios from "axios";
import { IconDeviceFloppy } from "@tabler/icons-vue";

const props = defineProps({
  tipo: { type: Object },
  contrato: { type: Object }
});

const toast = useToast();
const form = useForm({
  numero_contrato: null,
  tipo_id: null,
  cnpj: null,
  contratada: null,
  objeto: null,
  processo_sei: null,
  data_inicio_vigencia: null,
  data_termino_vigencia: null,
  situacao: null,
  edital: null,
  tipo_licitacao: null,
  modalidade: null,
  unidade_gestora: null,
  fiscal_contrato: null,
  snv: null,
  preco_inicial: null,
  total_aditivo: null,
  total_reajuste: null,
  total: null,
  ...props.contrato
});

const YYYYmmdd = (data) => {
  var data = new Date(data);
  // Obtém os componentes da data
  var ano = data.getFullYear();
  var mes = (data.getMonth() + 1).toString().padStart(2, '0'); // Mês começa do zero, então adicionamos 1
  var dia = data.getDate().toString().padStart(2, '0');

  // Formata a data como YYYY-mm-dd
  var dataFormatada = ano + '-' + mes + '-' + dia;

  return dataFormatada;
}

const getDadosContrato = () => {
  // Regex para permitir apenas numeros no nr_contrato
  const nr_contrato = form.numero_contrato?.replace(/\D/g, '');

  // Contrato não informado
  if (!nr_contrato) {
    toast.error('Não dados deste contrato na API');
    return;
  }

  // Tenta buscar dados do contrato
  axios.get(route('contratos.get_contrato', nr_contrato))
    .then(response => {
      // Contrato Não Localizado
      if (!response.data?.data?.length) {
        toast.error("Não foi possível achar o contrato");
        const contrato = form.numero_contrato;
        form.reset();
        form.numero_contrato = contrato
        return;
      }

      // Armazena retorno do contrato
      toast.success("Contrato encontrado!");
      form.cnpj = response.data.data[0].NU_CNPJ_CPF;
      form.numero_contrato = response.data.data[0].NU_CON_FORMATADO;
      form.contratada = response.data.data[0].NO_EMPRESA;
      form.objeto = response.data.data[0].DS_OBJETO;
      form.processo_sei = response.data.data[0].NU_PROCESSO;
      form.data_inicio_vigencia = YYYYmmdd(response.data.data[0].DT_INICIO);
      form.data_termino_vigencia = YYYYmmdd(response.data.data[0].DT_TERMINO_VIGENCIA);
      form.situacao = response.data.data[0].DS_FAS_CONTRATO;
      form.edital = response.data.data[0].NU_EDITAL;
      form.tipo_licitacao = response.data.data[0].TIPO_LICITACAO;
      form.edital = response.data.data[0].NU_EDITAL;
      form.modalidade = response.data.data[0].MODALIDADE_LICITACAO;
      form.unidade_gestora = response.data.data[0].SG_UND_GESTORA;
      form.fiscal_contrato = response.data.data[0].NM_FISCAL;
      form.preco_inicial = response.data.data[0].Valor_Inicial;
      form.total_aditivo = response.data.data[0].Valor_Total_de_Aditivos;
      form.total_reajuste = response.data.data[0].Valor_Total_de_Reajuste;
      form.total = response.data.data[0].Valor_Inicial_Adit_Reajustes;
    })
    .catch(response => {
      return console.log(response);
    });
}

const salvarContrato = () => {
  form.tipo_id = props.tipo.id;

  form.transform((data) => Object.assign({}, data))

  if (props.contrato.id) {
    form.patch(route('contratos.gestao.atualizar', props.contrato.id));

    return;
  }

  form.post(route('contratos.gestao.store'));
}
</script>
<template>
  <form @submit.prevent="salvarContrato()" :disabled="form.processing">
    <div class="card-header">
      <h3 class="my-0">Dados Básicos</h3>
    </div>
    <div class="card-body">
      <div class="row mb-4">
        <div class="col">
          <InputLabel value="Contrato" for="contrato" class="form-label" />
          <div class="row g-2">
            <div class="col">
              <input type="text" id="contrato" name="contrato" class="form-control" v-model="form.numero_contrato" />
            </div>
            <div class="col-auto">
              <button @click="getDadosContrato()" type="button" class="btn btn-primary">
                Buscar
              </button>
            </div>
          </div>
        </div>
        <div class="col">
          <InputLabel value="Tipo de Contrato" for="tipo" />
          <input id="tipo" name="tipo" :value="tipo?.nome" class="form-control" disabled />
          <InputError :message="form.errors.tipo" />
        </div>
      </div>
      <div class="row mb-4">
        <div class="col-4">
          <InputLabel value="CNPJ" for="cnpj" />
          <input type="text" id="cnpj" name="cnpj" v-maska data-maska="##.###.###/####-##" class="form-control"
            v-model="form.cnpj" disabled />
          <InputError :message="form.errors.cnpj" />
        </div>
        <div class="col">
          <InputLabel value="Empresa" for="contratada" />
          <input type="text" id="contratada" name="contratada" class="form-control" v-model="form.contratada"
            disabled />
          <InputError :message="form.errors.contratada" />
        </div>
      </div>
      <div class="row mb-4">
        <div class="col">
          <InputLabel value="Objeto do Contrato" for="objeto" />
          <textarea name="objeto" id="objeto" class="form-control" rows="5" v-model="form.objeto" disabled></textarea>
          <InputError :message="form.errors.objeto" />
        </div>
      </div>
      <div class="row">
        <div class="col">
          <InputLabel value="Numero do Processo" for="processo_sei" />
          <input type="text" id="processo_sei" name="processo_sei" class="form-control" v-model="form.processo_sei"
            disabled />
          <InputError :message="form.errors.processo_sei" />
        </div>
        <div class="col">
          <InputLabel value="Início da Vigência" for="data_inicio_vigencia" />
          <input type="date" id="data_inicio_vigencia" name="data_inicio_vigencia" class="form-control"
            v-model="form.data_inicio_vigencia" disabled />
          <InputError :message="form.errors.data_inicio_vigencia" />
        </div>
        <div class="col">
          <InputLabel value="Término da Vigência" for="data_termino_vigencia" />
          <input type="date" id="data_termino_vigencia" name="data_termino_vigencia" class="form-control"
            v-model="form.data_termino_vigencia" disabled />
          <InputError :message="form.errors.data_termino_vigencia" />
        </div>
        <div class="col">
          <InputLabel value="Situação" for="situacao" />
          <input type="text" id="situacao" name="situacao" class="form-control" v-model="form.situacao" disabled />
          <InputError :message="form.errors.situacao" />
        </div>
      </div>
    </div>
    <div class="card-header">
      <h3 class="my-0">Licitação</h3>
    </div>
    <div class="card-body">
      <div class="row">
        <div class="col">
          <InputLabel value="Edital" for="edital" />
          <input type="text" id="edital" name="edital" class="form-control" v-model="form.edital" disabled />
          <InputError :message="form.errors.edital" />
        </div>
        <div class="col">
          <InputLabel value="Tipo de Licitação" for="tipo_licitacao" />
          <input type="text" id="tipo_licitacao" name="tipo_licitacao" class="form-control"
            v-model="form.tipo_licitacao" disabled />
          <InputError :message="form.errors.tipo_licitacao" />
        </div>
        <div class="col">
          <InputLabel value="Modalidade" for="modalidade" />
          <input type="text" id="modalidade" name="modalidade" class="form-control" v-model="form.modalidade"
            disabled />
          <InputError :message="form.errors.modalidade" />
        </div>
      </div>
    </div>
    <div class="card-header">
      <h3 class="my-0">Fiscalização</h3>
    </div>
    <div class="card-body">
      <div class="row">
        <div class="col">
          <InputLabel value="Unidade Gestora" for="unidade_gestora" />
          <input type="text" id="unidade_gestora" name="unidade_gestora" class="form-control"
            v-model="form.unidade_gestora" disabled />
          <InputError :message="form.errors.unidade_gestora" />
        </div>
        <div class="col">
          <InputLabel value="Fiscal do Contrato" for="fiscal_contrato" />
          <input type="text" id="fiscal_contrato" name="fiscal_contrato" class="form-control"
            v-model="form.fiscal_contrato" disabled />
          <InputError :message="form.errors.fiscal_contrato" />
        </div>
      </div>
    </div>
    <div class="card-header">
      <h3 class="my-0">Valores</h3>
    </div>
    <div class="card-body">
      <div class="row">
        <div class="col">
          <InputLabel value="SNV" for="snv" />
          <input type="text" id="snv" name="snv" class="form-control" v-model="form.snv" />
          <InputError :message="form.errors.snv" />
        </div>
        <div class="col">
          <InputLabel value="Preço Inicial" for="preco_inicial" />
          <input type="text" id="preco_inicial" name="preco_inicial" class="form-control" v-model="form.preco_inicial"
            disabled />
          <InputError :message="form.errors.preco_inicial" />
        </div>
        <div class="col">
          <InputLabel value="Preço Aditivos" for="total_aditivo" />
          <input type="text" id="total_aditivo" name="total_aditivo" class="form-control" v-model="form.total_aditivo"
            disabled />
          <InputError :message="form.errors.total_aditivo" />
        </div>
        <div class="col">
          <InputLabel value="Preço Reajuste" for="total_reajuste" />
          <input type="text" id="total_reajuste" name="total_reajuste" class="form-control"
            v-model="form.total_reajuste" disabled />
          <InputError :message="form.errors.total_reajuste" />
        </div>
      </div>
    </div>
    <div class="card-body">
      <div class="d-flex justify-content-end">
        <button @click="salvarContrato()" type="button" class="btn btn-success" :disabled="form.processing">
          <IconDeviceFloppy class="me-2" />
          {{ form.id ? 'Editar' : 'Salvar' }}
        </button>
      </div>
    </div>
  </form>
</template>