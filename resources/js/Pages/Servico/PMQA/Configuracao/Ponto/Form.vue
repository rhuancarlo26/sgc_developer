<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Navbar from "../../Navbar.vue";
import { Head, useForm } from "@inertiajs/vue3";
import NavButton from "@/Components/NavButton.vue";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import { IconDoorExit } from "@tabler/icons-vue";
import { IconDeviceFloppy } from "@tabler/icons-vue";
import NavLink from "@/Components/NavLink.vue";

const props = defineProps({
  contrato: { type: Object },
  servico: { type: Object },
  ponto: { type: Object }
});

const form = useForm({
  id: null,
  nomepontocoleta: null,
  lat_x: null,
  long_y: null,
  classificacao: null,
  classe: null,
  tipoambiente: null,
  uf: null,
  municipio: null,
  baciahidrografica: null,
  km_rodovia: null,
  estaca: null,
  observacoes: null,
  ...props.ponto
});

const alterarPonto = () => {
  form.patch(route('contratos.contratada.servicos.pmqa.configuracao.ponto.update', { contrato: props.contrato.id, servico: props.servico.id }))
}

</script>
<template>

  <Head :title="`${contrato.contratada.slice(0, 10)}...`" />

  <AuthenticatedLayout>
    <Navbar :contrato="contrato" :servico="servico">
      <template #body>
        <form @submit.prevent="salvarServico()">
          <div class="row mb-4">
            <div class="col form-group">
              <InputLabel value="Nome" for="nomepontocoleta" />
              <input type="text" class="form-control" v-model="form.nomepontocoleta">
              <InputError :message="form.errors.nomepontocoleta" />
            </div>
            <div class="col form-group">
              <InputLabel value="Latitude" for="lat_x" />
              <input type="number" step="any" class="form-control" v-model="form.lat_x">
              <InputError :message="form.errors.lat_x" />
            </div>
            <div class="col form-group">
              <InputLabel value="Longitude" for="long_y" />
              <input type="number" step="any" class="form-control" v-model="form.long_y">
              <InputError :message="form.errors.long_y" />
            </div>
          </div>
          <div class="row mb-4">
            <div class="col form-group">
              <InputLabel value="Classificação" for="classificacao" />
              <input type="text" class="form-control" v-model="form.classificacao">
              <InputError :message="form.errors.classificacao" />
            </div>
            <div class="col form-group">
              <InputLabel value="Classe" for="classe" />
              <input type="number" class="form-control" v-model="form.classe">
              <InputError :message="form.errors.classe" />
            </div>
            <div class="col form-group">
              <InputLabel value="Tipo de ambiente" for="tipoambiente" />
              <input type="text" class="form-control" v-model="form.tipoambiente">
              <InputError :message="form.errors.tipoambiente" />
            </div>
          </div>
          <div class="row mb-4">
            <div class="col form-group">
              <InputLabel value="UF" for="uf" />
              <input type="text" class="form-control" v-model="form.uf">
              <InputError :message="form.errors.uf" />
            </div>
            <div class="col form-group">
              <InputLabel value="Municipio" for="municipio" />
              <input type="text" class="form-control" v-model="form.municipio">
              <InputError :message="form.errors.municipio" />
            </div>
            <div class="col form-group">
              <InputLabel value="Bacia hidrografica" for="baciahidrografica" />
              <input type="text" class="form-control" v-model="form.baciahidrografica">
              <InputError :message="form.errors.baciahidrografica" />
            </div>
          </div>
          <div class="row mb-4">
            <div class="col form-group">
              <InputLabel value="Km rodovia" for="km_rodovia" />
              <input type="number" class="form-control" v-model="form.km_rodovia">
              <InputError :message="form.errors.km_rodovia" />
            </div>
            <div class="col form-group">
              <InputLabel value="Estaca" for="estaca" />
              <input type="number" class="form-control" v-model="form.estaca">
              <InputError :message="form.errors.estaca" />
            </div>
            <div class="col form-group">
              <InputLabel value="Bacia hidrografica" for="baciahidrografica" />
              <input type="text" class="form-control" v-model="form.baciahidrografica">
              <InputError :message="form.errors.baciahidrografica" />
            </div>
          </div>
          <div class="row mb-4">
            <div class="col form-group">
              <InputLabel value="Observações" for="observacoes" />
              <textarea name="observacoes" id="observacoes" rows="5" class="form-control"
                v-model="form.observacoes"></textarea>
              <InputError :message="form.errors.observacoes" />
            </div>
          </div>
          <div class="row">
            <div class="d-flex justify-content-end col form-group">
              <NavLink class="btn btn-secondary me-1" title="Voltar" :icon="IconDoorExit"
                route-name="contratos.contratada.servicos.pmqa.configuracao.ponto.index"
                :param="{ contrato: contrato.id, servico: servico.id }" />
              <NavButton @click="alterarPonto()" type-button="success" :icon="IconDeviceFloppy" title="Alterar" />
            </div>
          </div>
        </form>
      </template>
    </Navbar>
  </AuthenticatedLayout>
</template>