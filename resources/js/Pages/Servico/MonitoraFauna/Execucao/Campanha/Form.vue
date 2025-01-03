<script setup>

import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import Navbar from "../../Navbar.vue";
import NavButton from "@/Components/NavButton.vue";
import ModelSearchFormAllColumns from "@/Components/ModelSearchFormAllColumns.vue";
import Table from "@/Components/Table.vue";
import { IconTrash } from "@tabler/icons-vue";
import { ref } from "vue";
import { Head, Link, router, useForm } from "@inertiajs/vue3";
import LinkConfirmation from "@/Components/LinkConfirmation.vue";
import { dateTimeFormat } from "@/Utils/DateTimeUtils";
import ModalFormCampanha from "./ModalFormCampanha.vue";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";

const props = defineProps({
  contrato: { type: Object },
  servico: { type: Object },
  campanha: { type: Object },
  modulos: { type: Array },
  abios: { type: Array },
  rhs: { type: Array },
  grupo_faunisticos: { type: Array },
});

const form = useForm({
  id: null,
  id_servico: props.servico.id,
  data_campanha_inicial: null,
  data_campanha_final: null,
  periodo: null,
  obs: null,
  ...props.campanha
});

const form_abio = useForm({
  id: null,
  id_campanha: props.campanha.id,
  id_abio: null
});

const form_profissional = useForm({
  id: null,
  id_campanha: props.campanha.id,
  id_profissional: null,
  id_grupo_faunistico: null
});

const save = () => {
  form.post(route('contratos.contratada.servicos.monitora_fauna.execucao.campanha.store', { contrato: props.contrato.id, servico: props.servico.id }), {
    onSuccess: () => Object.assign(form, props.campanha)
  });
}

const saveABIO = () => {
  form_abio.post(route('contratos.contratada.servicos.monitora_fauna.execucao.campanha.store_abio', { contrato: props.contrato.id, servico: props.servico.id }), {
    onSuccess: () => Object.assign(form, props.campanha)
  });
}

const saveProfissional = () => {
  form_profissional.post(route('contratos.contratada.servicos.monitora_fauna.execucao.campanha.store_profissional', { contrato: props.contrato.id, servico: props.servico.id }), {
    onSuccess: () => Object.assign(form, props.campanha)
  });
}

const linkConfirmationRef = ref();
const excluirAbio = (id) => {
  linkConfirmationRef.value.show(() => {
    router.delete(route('contratos.contratada.servicos.monitora_fauna.execucao.campanha.delete_abio', { contrato: props.contrato.id, servico: props.servico.id, campanha_abio: id }), {
      onSuccess: () => Object.assign(form, props.campanha)
    })
  })
}

const excluirProfissional = (id) => {
  linkConfirmationRef.value.show(() => {
    router.delete(route('contratos.contratada.servicos.monitora_fauna.execucao.campanha.delete_profissional', { contrato: props.contrato.id, servico: props.servico.id, campanha_profissional: id }), {
      onSuccess: () => Object.assign(form, props.campanha)
    })
  })
}

</script>
<template>

  <Head title="Módulos Amostrais" />

  <AuthenticatedLayout>

    <template #header>
      <div class="w-100 d-flex justify-content-between">
        <Breadcrumb class="align-self-center" :links="[
          { route: route('contratos.gestao.listagem', contrato.tipo_contrato), label: `Gestão de Contratos` },
          { route: '#', label: contrato.contratada }
        ]
          " />
        <Link class="btn btn-dark"
          :href="route('contratos.contratada.servicos.monitora_fauna.execucao.campanha.index', { contrato: props.contrato.id, servico: props.servico.id })">
        Voltar
        </Link>
      </div>
    </template>

    <Navbar :contrato="contrato" :servico="servico">
      <template #body>
        <div>
          <h4>DADOS GERAIS</h4>
          <form @submit.prevent="save()">
            <div class="row mb-4">
              <div class="col">
                <InputLabel value="ID Campanha" />
                <input type="text" id="id" name="id" class="form-control" :value="form.id" disabled>
                <InputError :message="form.errors.id" />
              </div>
              <div class="col">
                <InputLabel value="Módulos Amostrais" />
                <div class="d-flex">
                  <input type="text" v-for="modulo in modulos" :key="modulo.id" :value="modulo.id" disabled
                    class="form-control px-1 me-1 text-center col" />
                </div>
                <InputError :message="form.errors.id" />
              </div>
            </div>
            <div class="row mb-4">
              <div class="col">
                <InputLabel value="Data Inicial" for="data_campanha_inicial" />
                <input type="date" id="data_campanha_inicial" name="data_campanha_inicial" class="form-control"
                  v-model="form.data_campanha_inicial">
                <InputError :message="form.errors.data_campanha_inicial" />
              </div>
              <div class="col">
                <InputLabel value="Data Final" for="data_campanha_final" />
                <input type="date" id="data_campanha_final" name="data_campanha_final" class="form-control"
                  v-model="form.data_campanha_final">
                <InputError :message="form.errors.data_campanha_final" />
              </div>
            </div>
            <div class="row mb-4">
              <div class="col">
                <InputLabel value="Período" for="periodo" />
                <label class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" id="periodo" name="periodo" value="Seca"
                    v-model="form.periodo">
                  <span class="form-check-label">Seca</span>
                </label>
                <label class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" id="periodo" name="periodo" value="Chuva"
                    v-model="form.periodo">
                  <span class="form-check-label">Chuva</span>
                </label>
              </div>
            </div>
            <div class="row mb-4">
              <div class="col">
                <InputLabel value="Observações" for="obs" />
                <textarea name="obs" id="obs" class="form-control" v-model="form.obs" rows="5"></textarea>
              </div>
            </div>
            <div class="row">
              <div class="col d-flex justify-content-end">
                <NavButton type="submit" type-button="success" title="Salvar" />
              </div>
            </div>
          </form>

          <h4>VINCULAR ABIO</h4>
          <form @submit.prevent="saveABIO()" class="mb-4">
            <div class="row mb-4">
              <div class="col">
                <InputLabel value="N° ABIO Vigente" for="abio" />
                <div class="row g-2">
                  <div class="col">
                    <v-select :options="[...abios.map(c => { return { ...c, label: `${c.licenca.numero_licenca}` } })]"
                      :reduce="a => a.id" v-model="form_abio.id_abio">
                      <template #no-options="{}">
                        Nenhum registro encontrado.
                      </template>
                    </v-select>
                  </div>
                  <div class="col-auto">
                    <NavButton type="submit" type-button="success" title="Salvar" />
                  </div>
                </div>
                <InputError :message="form_abio.errors.id_abio" />
              </div>
            </div>

            <Table :columns="['Abio\'s vigentes', 'Ação']" :records="{ data: campanha.campanha_abios, links: [] }">
              <template #body="{ item }">
                <tr>
                  <td>{{ item.abio?.licenca?.numero_licenca }}</td>
                  <td class="text-center">
                    <NavButton @click="excluirAbio(item.id)" type-button="danger" :icon="IconTrash" />
                  </td>
                </tr>
              </template>
            </Table>
          </form>


          <h4>VINCULAR PROFISSIONAIS</h4>
          <form @submit.prevent="saveProfissional()">
            <div class="row mb-4">
              <div class="col">
                <InputLabel value="Selecionar Profissional" for="abio" />
                <v-select :options="[...rhs.map(c => { return { ...c, label: `${c.rh.nome}` } })]" :reduce="a => a.id"
                  v-model="form_profissional.id_profissional">
                  <template #no-options="{}">
                    Nenhum registro encontrado.
                  </template>
                </v-select>
                <InputError :message="form_profissional.errors.id_profissional" />
              </div>
              <div class="col">
                <InputLabel value="Grupo Responsável" for="id_grupo_faunistico" />
                <div class="row g-2">
                  <div class="col">
                    <v-select :options="grupo_faunisticos" label="grupo_faunistico" :reduce="a => a.id"
                      v-model="form_profissional.id_grupo_faunistico">
                      <template #no-options="{}">
                        Nenhum registro encontrado.
                      </template>
                    </v-select>
                  </div>
                  <div class="col-auto">
                    <NavButton type="submit" type-button="success" title="Salvar" />
                  </div>
                </div>
                <InputError :message="form_profissional.errors.id_grupo_faunistico" />
              </div>
            </div>

            <Table :columns="['Equipe Técnica', 'Grupo Responsável', 'Ação']"
              :records="{ data: campanha.profiss_grupo, links: [] }">
              <template #body="{ item }">
                <tr>
                  <td>{{ item.rh?.rh?.nome }}</td>
                  <td>{{ item.grupo_faunistico.grupo_faunistico }}</td>
                  <td class="text-center">
                    <NavButton @click="excluirProfissional(item.id)" type-button="danger" :icon="IconTrash" />
                  </td>
                </tr>
              </template>
            </Table>
          </form>
        </div>
      </template>
    </Navbar>

    <LinkConfirmation ref="linkConfirmationRef" :options="{ text: 'Excluir registro?' }" />

  </AuthenticatedLayout>
</template>
