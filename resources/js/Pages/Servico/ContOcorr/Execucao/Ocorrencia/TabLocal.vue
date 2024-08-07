<script setup>
import { useForm } from "@inertiajs/vue3";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import NavButton from "@/Components/NavButton.vue";
import { IconDeviceFloppy } from "@tabler/icons-vue";
import { watch } from "vue";
import { computed } from "vue";

const props = defineProps({
  contrato: { type: Object },
  servico: { type: Object },
  ocorrencia: { type: Object },
  lotes: { type: Array },
  rodovias: { type: Array }
});

const form = useForm({
  id: null,
  id_ocorrencia: null,
  num_por_servico: null,
  rodovia: null,
  data_ocorrencia: null,
  km: null,
  estaca: null,
  lado: null,
  latitude: null,
  longitude: null,
  lote: null,
  indicio_responsabilidade: null,
  possivel_causa: null,
  descricao_causa: null,
  rnc_direto: null,
  intensidade: null,
  tipo: null,
  ...props.ocorrencia
});

watch(
  () => form.rnc_direto,
  (value) => {
    form.tipo = value ? 'RNC' : 'ROA';
  }
);

const brs = computed(() => {
  const ufsSet = new Set();
  const rodovias = [];

  props.servico.licencas_condicionantes.forEach(lc => {
    lc.licenca.segmentos.forEach(segmento => {
      rodovias.push(segmento.rodovia);
      ufsSet.add(segmento.uf_inicial, segmento.uf_final);
    });
  });

  const ufsFiltered = Array.from(ufsSet);

  const rodoviasFiltered = props.rodovias.filter(br =>
    rodovias.includes(br.rodovia) && ufsFiltered.map(uf => uf.id).includes(br.uf_id)
  );

  const uniqueRodovias = Array.from(new Set(rodoviasFiltered.map(item => item.id)))
    .map(id => rodoviasFiltered.find(item => item.id === id));

  return uniqueRodovias;
});

const salvarLocal = () => {
  const url = form.id ? 'update' : 'store';
  form.post(route('contratos.contratada.servicos.cont_ocorrencia.execucao.ocorrencia.' + url, { contrato: props.contrato.id, servico: props.servico.id }));
}
</script>
<template>
  <div class="row mb-4">
    <div class="col">
      <InputLabel value="ID Ocorrência" for="nome_id" />
      <input type="text" class="form-control"
        :value="`${form.tipo ?? '###'}.${ocorrencia.num_por_servico ?? '##'}.${form.rodovia?.uf?.uf ?? '##'}-${form.rodovia?.rodovia ?? '##'}`"
        disabled>
      <InputError :message="form.errors.nome_id" />
    </div>
    <div class="col">
      <InputLabel value="Segmento" for="rodovia" />
      <select class="form-control form-select" v-model="form.rodovia">
        <option v-for="rodovia in brs" :key="rodovia.id" :value="rodovia">{{ `${rodovia.rodovia} -
          ${rodovia.uf?.estado}` }}</option>
      </select>
      <InputError :message="form.errors.rodovia" />
    </div>
    <div class="col">
      <InputLabel value="Data da Ocorrência" for="data_ocorrencia" />
      <input type="date" class="form-control" v-model="form.data_ocorrencia">
      <InputError :message="form.errors.data_ocorrencia" />
    </div>
  </div>
  <div class="row mb-4">
    <div class="col">
      <InputLabel value="Km" for="km" />
      <input type="number" step="any" class="form-control" v-model="form.km">
      <InputError :message="form.errors.km" />
    </div>
    <div class="col">
      <InputLabel value="Estaca" for="estaca" />
      <input type="text" class="form-control" v-model="form.estaca">
      <InputError :message="form.errors.estaca" />
    </div>
    <div class="col">
      <InputLabel value="Lado" for="lado" />
      <select class="form-control form-select" v-model="form.lado">
        <option value="Direito">Direito</option>
        <option value="Esquerdo">Esquerdo</option>
        <option value="Direito / Esquerdo">Direito / Esquerdo</option>
      </select>
      <InputError :message="form.errors.lado" />
    </div>
  </div>
  <div class="row mb-4">
    <div class="col">
      <InputLabel value="Latitude" for="latitude" />
      <input type="number" step="any" class="form-control" v-model="form.latitude">
      <InputError :message="form.errors.latitude" />
    </div>
    <div class="col">
      <InputLabel value="Longitude" for="longitude" />
      <input type="number" step="any" class="form-control" v-model="form.longitude">
      <InputError :message="form.errors.longitude" />
    </div>
    <div class="col">
      <InputLabel value="Lote" for="lote" />
      <select class="form-control form-select" v-model="form.lote">
        <option v-for="lote in lotes" :key="lote.id" :value="lote">{{ lote.nome_id }}</option>
      </select>
      <InputError :message="form.errors.lote" />
    </div>
    <div class="col">
      <InputLabel value="Nome lote" for="nome" />
      <input type="text" class="form-control" :value="form.lote?.nome" disabled>
      <InputError :message="form.errors.nome" />
    </div>
  </div>
  <div class="row mb-4">
    <div class="col">
      <InputLabel value="Empresa/Consórcio" for="empresa" />
      <input type="text" class="form-control" :value="form.lote?.empresa" disabled>
      <InputError :message="form.errors.empresa" />
    </div>
    <div class="col">
      <InputLabel value="Contrato de Obra" for="num_contrato" />
      <input type="text" class="form-control" :value="form.lote?.num_contrato" disabled>
      <InputError :message="form.errors.num_contrato" />
    </div>
  </div>
  <div class="row mb-4">
    <div class="col align-content-center">
      <InputLabel value="Indícios de Responsabilidade da Construtora" for="indicio_responsabilidade" />
      <div>
        <label class="form-check form-check-inline">
          <input class="form-check-input" type="radio" :value="true" v-model="form.indicio_responsabilidade">
          <span class="form-check-label">Sim</span>
        </label>
        <label class="form-check form-check-inline">
          <input class="form-check-input" type="radio" :value="false" v-model="form.indicio_responsabilidade">
          <span class="form-check-label">Não</span>
        </label>
      </div>
      <InputError :message="form.errors.indicio_responsabilidade" />
    </div>
    <template v-if="form.indicio_responsabilidade === false">
      <div class="col align-content-center">
        <InputLabel value="Possível Causa" for="possivel_causa" />
        <select class="form-control form-select" v-model="form.possivel_causa">
          <option value="Terceiros">Terceiros</option>
          <option value="Caso Fortuito ou força maior">Caso Fortuito ou força maior</option>
          <option value="Outros">Outros</option>
        </select>
        <InputError :message="form.errors.possivel_causa" />
      </div>
      <div class="col">
        <InputLabel value="Descrição da Causa" for="descricao_causa" />
        <textarea class="form-control" v-model="form.descricao_causa" rows="5"></textarea>
        <InputError :message="form.errors.descricao_causa" />
      </div>
    </template>
  </div>
  <div class="row mb-4">
    <div class="col align-content-center">
      <InputLabel value="RNC Direto" for="rnc_direto" />
      <div>
        <label class="form-check form-check-inline">
          <input class="form-check-input" type="radio" :value="true" v-model="form.rnc_direto">
          <span class="form-check-label">Sim</span>
        </label>
        <label class="form-check form-check-inline">
          <input class="form-check-input" type="radio" :value="false" v-model="form.rnc_direto">
          <span class="form-check-label">Não</span>
        </label>
      </div>
      <InputError :message="form.errors.rnc_direto" />
    </div>
    <div class="col align-content-center">
      <InputLabel value="Intensidade de Ocorrência" for="intensidade" />
      <select class="form-control form-select" v-model="form.intensidade">
        <option value="Leve">Leve</option>
        <option value="Moderada">Moderada</option>
        <option value="Grave">Grave</option>
      </select>
      <InputError :message="form.errors.intensidade" />
    </div>
    <div class="col">
      <InputLabel value="Tipo de Ocorrência" for="tipo" />
      <input type="text" class="form-control" v-model="form.tipo" disabled>
      <InputError :message="form.errors.tipo" />
    </div>
  </div>
  <div class="row">
    <div class="col d-flex justify-content-end">
      <NavButton @click="salvarLocal()" type-button="success" :icon="IconDeviceFloppy"
        :title="form.id ? 'Alterar' : 'Salvar'" />
    </div>
  </div>
</template>