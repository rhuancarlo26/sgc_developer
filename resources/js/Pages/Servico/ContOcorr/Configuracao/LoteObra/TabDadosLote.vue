<script setup>
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import NavButton from "@/Components/NavButton.vue";
import { useForm } from "@inertiajs/vue3";
import { useToast } from "vue-toastification";
import axios from "axios";
import { computed, onMounted } from "vue";
import { IconDeviceFloppy } from "@tabler/icons-vue";

const toast = useToast();
const props = defineProps({
  contrato: { type: Object },
  servico: { type: Object },
  lote: { type: Object },
  rodovias: { type: Array }
});

const form = useForm({
  id: null,
  temContrato: props.lote?.num_contrato ? true : false,
  id_servico: props.servico.id,
  num_contrato: null,
  nome_id: null,
  nome: null,
  rodovia: null,
  uf: null,
  empresa: null,
  situacao_contrato: null,
  obj_contrato: null,
  fiscal_contrato: null,
  km_inicial: null,
  km_final: null,
  estaca_inicial: null,
  estaca_final: null,
  lat_inicial: null,
  lat_final: null,
  long_inicial: null,
  long_final: null,
  ...props.lote
});

const getDadosContrato = () => {
  const nr_contrato = form.num_contrato?.replace(/\D/g, '');

  if (!nr_contrato) {
    toast.error('Número de contrato inválido!');
    return;
  }

  axios.get(route('contratos.get_contrato', nr_contrato))
    .then((response) => {
      toast.success('Contrato encontrado!');

      form.nome = response.data.data[0].NU_LOTE_LICITACAO;
      form.empresa = response.data.data[0].NO_EMPRESA;
      form.num_contrato = response.data.data[0].NU_CON_FORMATADO;
      form.obj_contrato = response.data.data[0].DS_OBJETO;
      form.fiscal_contrato = response.data.data[0].NM_FISCAL;
      form.situacao_contrato = response.data.data[0].DS_FAS_CONTRATO;

      form.rodovia = brs.value[0];
      form.uf = ufs.value[0];
    })
    .catch((response) => {
      return console.log(response);
    });
}

const salvarLote = () => {
  form.id = props.lote.id;

  const url = props.lote.id ? 'update' : 'store';
  form.post(route('contratos.contratada.servicos.cont_ocorrencia.configuracao.lote_obra.' + url, { contrato: props.contrato.id, servico: props.servico.id }))
}

const brs = computed(() => {
  const ufsSet = new Set();
  let ufs = [];
  let rodovias = [];

  props.servico.licencas_condicionantes.map(lc => {
    lc.licenca.segmentos.map(segmento => {
      rodovias.push(segmento.rodovia)
      ufs.push(segmento.uf_inicial, segmento.uf_final)
    })
  })

  ufs.forEach(uf => {
    const ufString = JSON.stringify(uf);
    ufsSet.add(ufString);
  })

  const ufsFiltered = Array.from(ufsSet).map(ufString => JSON.parse(ufString));

  const rodoviasFiltered = props.rodovias.filter(br => rodovias.includes(br.rodovia) && ufsFiltered.map(uf => uf.id).includes(br.uf_id));

  return Array.from(new Set(rodoviasFiltered.map(item => item.id)))
    .map(id => {
      return rodoviasFiltered.find(item => item.id === id);
    });
})


</script>
<template>
  <div class="row mb-4">
    <div class="col">
      <InputLabel value="Tem contrato de obra?" for="temContrato" />
      <div>
        <label class="form-check form-check-inline">
          <input class="form-check-input" type="radio" :value="true" v-model="form.temContrato">
          <span class="form-check-label">Sim</span>
        </label>
        <label class="form-check form-check-inline">
          <input class="form-check-input" type="radio" :value="false" v-model="form.temContrato">
          <span class="form-check-label">Não</span>
        </label>
      </div>
      <InputError :message="form.errors.temContrato" />
    </div>
    <div class="col" v-if="form.temContrato === true">
      <InputLabel value="Número do contrato" for="num_contrato" />
      <div class="row g-2">
        <div class="col">
          <input type="text" class="form-control" v-model="form.num_contrato">
        </div>
        <div class="col-auto">
          <NavButton @click="getDadosContrato()"
            route-name="contratos.contratada.servicos.pmqa.configuracao.ponto.importar"
            :param="{ contrato: props.contrato.id, servico: props.servico.id }" type-button="success" title="Buscar" />
        </div>
      </div>
    </div>
    <InputError :message="form.errors.num_contrato" />
  </div>
  <div class="row mb-4">
    <div class="col">
      <InputLabel value="ID lote" for="nome_id" />
      <input type="text" class="form-control" v-model="form.nome_id" disabled>
      <InputError :message="form.errors.nome_id" />
    </div>
    <div class="col">
      <InputLabel value="Nome lote" for="nome" />
      <input type="text" class="form-control" v-model="form.nome" :disabled="form.temContrato">
      <InputError :message="form.errors.nome" />
    </div>
    <div class="col">
      <InputLabel value="Trecho" for="rodovia" />
      <select class="form-control form-select" v-model="form.rodovia">
        <option v-for="rodovia in brs" :key="rodovia" :value="rodovia">{{ `${rodovia.rodovia} - (${rodovia.uf.estado})`
          }}</option>
      </select>
      <InputError :message="form.errors.rodovia" />
    </div>
  </div>
  <div class="row mb-4">
    <div class="col">
      <InputLabel value="Empresa/Consórcio" for="empresa" />
      <input type="text" class="form-control" v-model="form.empresa" :disabled="form.temContrato">
      <InputError :message="form.errors.empresa" />
    </div>
    <div class="col">
      <InputLabel value="N° do contrato" for="num_contrato" />
      <input type="text" class="form-control" v-model="form.num_contrato" :disabled="form.temContrato">
      <InputError :message="form.errors.num_contrato" />
    </div>
    <div class="col">
      <InputLabel value="Situação do contrato" for="situacao_contrato" />
      <v-select :options="['ATIVO', 'PARALISADO', 'CONCLUIDO', 'RESCINDIDO', 'SEM CONTRATO']"
        v-model="form.situacao_contrato" :disabled="form.temContrato">
        <template #no-options="{}">
          Nenhum registro encontrado.
        </template>
      </v-select>
      <InputError :message="form.errors.situacao_contrato" />
    </div>
  </div>
  <div class="row mb-4">
    <div class="col">
      <InputLabel value="Objeto do contrato" for="obj_contrato" />
      <textarea class="form-control" rows="5" v-model="form.obj_contrato" :disabled="form.temContrato"></textarea>
      <InputError :message="form.errors.obj_contrato" />
    </div>
  </div>
  <div class="row mb-4">
    <div class="col">
      <InputLabel value="Fiscal do contrato" for="fiscal_contrato" />
      <input type="text" class="form-control" v-model="form.fiscal_contrato" :disabled="form.temContrato">
      <InputError :message="form.errors.fiscal_contrato" />
    </div>
  </div>
  <div class="row mb-4">
    <div class="col">
      <InputLabel value="KM inicial" for="km_inicial" />
      <input type="text" class="form-control" v-model="form.km_inicial">
      <InputError :message="form.errors.km_inicial" />
    </div>
    <div class="col">
      <InputLabel value="KM final" for="km_final" />
      <input type="text" class="form-control" v-model="form.km_final">
      <InputError :message="form.errors.km_final" />
    </div>
    <div class="col">
      <InputLabel value="Estaca inicial" for="estaca_inicial" />
      <input type="text" class="form-control" v-model="form.estaca_inicial">
      <InputError :message="form.errors.estaca_inicial" />
    </div>
    <div class="col">
      <InputLabel value="Estaca final" for="estaca_final" />
      <input type="text" class="form-control" v-model="form.estaca_final">
      <InputError :message="form.errors.estaca_final" />
    </div>
  </div>
  <div class="row mb-4">
    <div class="col">
      <InputLabel value="Latitude inicial" for="lat_inicial" />
      <input type="text" class="form-control" v-model="form.lat_inicial">
      <InputError :message="form.errors.lat_inicial" />
    </div>
    <div class="col">
      <InputLabel value="Latitude final" for="lat_final" />
      <input type="text" class="form-control" v-model="form.lat_final">
      <InputError :message="form.errors.lat_final" />
    </div>
    <div class="col">
      <InputLabel value="Longitude inicial" for="long_inicial" />
      <input type="text" class="form-control" v-model="form.long_inicial">
      <InputError :message="form.errors.long_inicial" />
    </div>
    <div class="col">
      <InputLabel value="Longitude final" for="long_final" />
      <input type="text" class="form-control" v-model="form.long_final">
      <InputError :message="form.errors.long_final" />
    </div>
  </div>
  <div class="row">
    <div class="col d-flex justify-content-end">
      <NavButton @click="salvarLote()" type-button="success" :icon="IconDeviceFloppy"
        :title="form.id ? 'Alterar' : 'Salvar'" />
    </div>
  </div>
</template>