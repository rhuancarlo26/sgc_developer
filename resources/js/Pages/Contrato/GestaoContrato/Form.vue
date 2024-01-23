<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import { useToast } from "vue-toastification";
import { IconClipboardPlus } from "@tabler/icons-vue";
import { IconClipboardX } from "@tabler/icons-vue";

defineProps({
  contratoTipos: {
    type: Array
  }
});

const toast = useToast();
const form = useForm({
  numero_contrato: null,
  contrato_tipo: null,
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
  snv: '123',
  preco_inicial: null,
  total_aditivo: null,
  total_reajuste: null,
  total: null
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
      if (!response.data.length) {
        toast.error("Servidor fora de serviço");
        return;
      }

      // Armazena retorno do contrato
      toast.success("Contrato encontrado!");
      console.log('contrato', response.data[0]);
      form.cnpj = response.data[0].NU_CNPJ_CPF;
      form.numero_contrato = response.data[0].NU_CON_FORMATADO;
      form.contratada = response.data[0].NO_EMPRESA;
      form.objeto = response.data[0].DS_OBJETO;
      form.processo_sei = response.data[0].NU_PROCESSO;
      form.data_inicio_vigencia = YYYYmmdd(response.data[0].DT_INICIO);
      form.data_termino_vigencia = YYYYmmdd(response.data[0].DT_TERMINO_VIGENCIA);
      form.edital = response.data[0].NU_EDITAL;
      form.tipo_licitacao = response.data[0].TIPO_LICITACAO;
      form.edital = response.data[0].NU_EDITAL;
      form.modalidade = response.data[0].MODALIDADE_LICITACAO;
      form.unidade_gestora = response.data[0].SG_UND_GESTORA;
      form.fiscal_contrato = response.data[0].NM_FISCAL;
      form.preco_inicial = response.data[0].Valor_Inicial;
      form.total_aditivo = response.data[0].Valor_Total_de_Aditivos;
      form.total_reajuste = response.data[0].Valor_Total_de_Reajuste;
      form.total = response.data[0].Valor_Inicial_Adit_Reajustes;
      form.situacao = response.data[0].DS_FAS_CONTRATO;
    })
    .catch(response => {
      return console.log(response);
    });
}

const salvarContrato = () => {
  console.log('form', form);
  form.transform((data) => Object.assign({}, data))

  // if (props.user.id) {

  //     form.patch(route('cadastros.usuarios.atualizar', props.user.id));
  //     return;
  // }

  form.post(route('contratos.gestao.store'));
}
</script>

<template>
  <Head title="Gestão de Contratos" />

  <AuthenticatedLayout>

    <template #header>
      <div class="w-100 d-flex justify-content-between">
        <Breadcrumb :links="[
          { route: route('contratos.gestao.listagem'), label: 'Gestão de Contratos' },
          { route: '#', label: 'Formulário' }
        ]" />
      </div>
    </template>

    <div class="card card-body mb-4">
      <h2>Dados Básicos</h2>
      <hr>
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
          <InputLabel value="Tipo de Contrato" for="contrato_tipo" />
          <select name="contrato_tipo" id="contrato_tipo" class="form-control" v-model="form.contrato_tipo">
            <option v-for="tipo_contrato in contratoTipos" :key="tipo_contrato" :value="tipo_contrato">{{
              tipo_contrato.nome }}</option>
          </select>
          <InputError :message="form.errors.contrato_tipo" />
        </div>
      </div>
      <div class="row mb-4">
        <div class="col-4">
          <InputLabel value="CNPJ" for="cnpj" />
          <input type="text" id="cnpj" name="cnpj" class="form-control" v-model="form.cnpj" />
          <InputError :message="form.errors.cnpj" />
        </div>
        <div class="col">
          <InputLabel value="Empresa" for="contratada" />
          <input type="text" id="contratada" name="contratada" class="form-control" v-model="form.contratada" />
          <InputError :message="form.errors.contratada" />
        </div>
      </div>
      <div class="row mb-4">
        <div class="col">
          <InputLabel value="Objeto do Contrato" for="objeto" />
          <textarea name="objeto" id="objeto" class="form-control" rows="5" v-model="form.objeto"></textarea>
        </div>
      </div>
      <div class="row mb-4">
        <div class="col">
          <InputLabel value="Numero do Processo" for="processo_sei" />
          <input type="text" id="processo_sei" name="processo_sei" class="form-control" v-model="form.processo_sei" />
          <InputError :message="form.errors.processo_sei" />
        </div>
        <div class="col">
          <InputLabel value="Início da Vigência" for="data_inicio_vigencia" />
          <input type="date" id="data_inicio_vigencia" name="data_inicio_vigencia" class="form-control"
            v-model="form.data_inicio_vigencia" />
          <InputError :message="form.errors.data_inicio_vigencia" />
        </div>
        <div class="col">
          <InputLabel value="Término da Vigência" for="data_termino_vigencia" />
          <input type="date" id="data_termino_vigencia" name="data_termino_vigencia" class="form-control"
            v-model="form.data_termino_vigencia" />
          <InputError :message="form.errors.data_termino_vigencia" />
        </div>
        <div class="col">
          <InputLabel value="Situação" for="situacao" />
          <input type="text" id="situacao" name="situacao" class="form-control" v-model="form.situacao" />
          <InputError :message="form.errors.situacao" />
        </div>
      </div>
    </div>

    <div class="card card-body mb-4">
      <h2>Licitação</h2>
      <hr>
      <div class="row mb-4">
        <div class="col">
          <InputLabel value="Edital" for="edital" />
          <input type="text" id="edital" name="edital" class="form-control" v-model="form.edital" />
          <InputError :message="form.errors.edital" />
        </div>
        <div class="col">
          <InputLabel value="Tipo de Licitação" for="tipo_licitacao" />
          <input type="text" id="tipo_licitacao" name="tipo_licitacao" class="form-control"
            v-model="form.tipo_licitacao" />
          <InputError :message="form.errors.tipo_licitacao" />
        </div>
        <div class="col">
          <InputLabel value="Modalidade" for="modalidade" />
          <input type="text" id="modalidade" name="modalidade" class="form-control" v-model="form.modalidade" />
          <InputError :message="form.errors.modalidade" />
        </div>
      </div>
    </div>

    <div class="card card-body mb-4">
      <h2>Fiscalização</h2>
      <hr>
      <div class="row mb-4">
        <div class="col">
          <InputLabel value="Unidade Gestora" for="unidade_gestora" />
          <input type="text" id="unidade_gestora" name="unidade_gestora" class="form-control"
            v-model="form.unidade_gestora" />
          <InputError :message="form.errors.unidade_gestora" />
        </div>
        <div class="col">
          <InputLabel value="Fiscal do Contrato" for="fiscal_contrato" />
          <input type="text" id="fiscal_contrato" name="fiscal_contrato" class="form-control"
            v-model="form.fiscal_contrato" />
          <InputError :message="form.errors.fiscal_contrato" />
        </div>
      </div>
    </div>

    <div class="card card-body mb-4">
      <h2>Valores</h2>
      <hr>
      <div class="row mb-4">
        <div class="col">
          <InputLabel value="SNV" for="snv" />
          <input type="text" id="snv" name="snv" class="form-control" v-model="form.snv" />
          <InputError :message="form.errors.snv" />
        </div>
        <div class="col">
          <InputLabel value="Preço Inicial" for="preco_inicial" />
          <input type="text" id="preco_inicial" name="preco_inicial" class="form-control" v-model="form.preco_inicial" />
          <InputError :message="form.errors.preco_inicial" />
        </div>
        <div class="col">
          <InputLabel value="Preço Aditivos" for="total_aditivo" />
          <input type="text" id="total_aditivo" name="total_aditivo" class="form-control" v-model="form.total_aditivo" />
          <InputError :message="form.errors.total_aditivo" />
        </div>
        <div class="col">
          <InputLabel value="Preço Reajuste" for="total_reajuste" />
          <input type="text" id="total_reajuste" name="total_reajuste" class="form-control"
            v-model="form.total_reajuste" />
          <InputError :message="form.errors.total_reajuste" />
        </div>
      </div>
    </div>

    <div class="card card-body">
      <div class="align-self-end">
        <button class="btn btn-danger me-2">
          <IconClipboardX />
        </button>
        <button @click="salvarContrato()" class="btn btn-success">
          <IconClipboardPlus />
        </button>
      </div>
    </div>

  </AuthenticatedLayout>
</template>