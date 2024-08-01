<script setup>
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import NavButton from "@/Components/NavButton.vue";
import { useForm } from "@inertiajs/vue3";

const props = defineProps({
  contrato: { type: Object },
  servico: { type: Object }
});

const form = useForm({
  temContrato: null,
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
  long_final: null
});
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
          <input type="text" class="form-control" v-model="num_contrato">
        </div>
        <div class="col-auto">
          <NavButton route-name="contratos.contratada.servicos.pmqa.configuracao.ponto.importar"
            :param="{ contrato: props.contrato.id, servico: props.servico.id }" type-button="success" title="Buscar" />
        </div>
      </div>
    </div>
    <InputError :message="form.errors.num_contrato" />
  </div>
  <div class="row mb-4">
    <div class="col">
      <InputLabel value="ID lote" for="nome_id" />
      <input type="text" class="form-control" v-model="form.nome_id">
      <InputError :message="form.errors.nome_id" />
    </div>
    <div class="col">
      <InputLabel value="Nome lote" for="nome" />
      <input type="text" class="form-control" v-model="form.nome">
      <InputError :message="form.errors.nome" />
    </div>
    <div class="col">
      <InputLabel value="BR" for="rodovia" />
      <v-select :options="[]" label="rodovia" v-model="form.rodovia">
        <template #no-options="{}">
          Nenhum registro encontrado.
        </template>
      </v-select>
      <InputError :message="form.errors.rodovia" />
    </div>
    <div class="col">
      <InputLabel value="UF" for="uf" />
      <v-select :options="[]" label="uf" v-model="form.uf">
        <template #no-options="{}">
          Nenhum registro encontrado.
        </template>
      </v-select>
      <InputError :message="form.errors.uf" />
    </div>
  </div>
  <div class="row mb-4">
    <div class="col">
      <InputLabel value="Empresa/Consórcio" for="empresa" />
      <input type="text" class="form-control" v-model="form.empresa">
      <InputError :message="form.errors.empresa" />
    </div>
    <div class="col">
      <InputLabel value="N° do contrato" for="num_contrato" />
      <input type="text" class="form-control" v-model="form.num_contrato">
      <InputError :message="form.errors.num_contrato" />
    </div>
    <div class="col">
      <InputLabel value="Situação do contrato" for="situacao_contrato" />
      <v-select :options="[]" label="nome" v-model="form.situacao_contrato">
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
      <textarea class="form-control" rows="5" v-model="form.obj_contrato"></textarea>
      <InputError :message="form.errors.obj_contrato" />
    </div>
  </div>
  <div class="row mb-4">
    <div class="col">
      <InputLabel value="Fiscal do contrato" for="fiscal_contrato" />
      <input type="text" class="form-control" v-model="form.fiscal_contrato">
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
</template>