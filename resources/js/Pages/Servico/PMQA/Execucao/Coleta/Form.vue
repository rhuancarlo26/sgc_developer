<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import Navbar from "../../Navbar.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import NavButton from "@/Components/NavButton.vue";
import { IconEye } from "@tabler/icons-vue";
import { IconTrash } from "@tabler/icons-vue";
import LinkConfirmation from "@/Components/LinkConfirmation.vue";
import { IconDeviceFloppy } from "@tabler/icons-vue";

const props = defineProps({
  contrato: { type: Object },
  servico: { type: Object },
  campanha: { type: Object },
  ponto: { type: Object }
});

const form = useForm({
  id: null,
  campanha_ponto_id: props.ponto.id,
  data_coleta: null,
  sem_coleta: false,
  numero_amostra: null,
  preservacao_amostra: null,
  acondicionamento_amostra: null,
  transporte_amostra: null,
  justificativa: null,
  arquivo: null,
  ...props.ponto.coleta
});

const saveColetaPonto = () => {
  if (form.id) {
    form.patch(route('contratos.contratada.servicos.pmqa.execucao.coleta.update', { contrato: props.contrato.id, servico: props.servico.id, campanha: props.campanha.id }), {
      onSuccess: () => modal.value.getBsModal().hide()
    })
  } else {
    form.post(route('contratos.contratada.servicos.pmqa.execucao.coleta.store', { contrato: props.contrato.id, servico: props.servico.id, campanha: props.campanha.id }));
  }
}

const saveArquivo = () => {
  form.id = props.ponto.coleta.id;

  form.post(route('contratos.contratada.servicos.pmqa.execucao.coleta.store_arquivo', { contrato: props.contrato.id, servico: props.servico.id, campanha: props.campanha.id }))
}

</script>
<template>

  <Head :title="`${contrato.contratada.slice(0, 10)}...`" />

  <AuthenticatedLayout>

    <template #header>
      <div class="w-100 d-flex justify-content-between">
        <Breadcrumb class="align-self-center" :links="[
          { route: route('contratos.gestao.listagem', contrato.tipo_contrato), label: `Gestão de Contratos` },
          { route: '#', label: contrato.contratada }
        ]
          " />
        <Link class="btn btn-dark"
          :href="route('contratos.contratada.servicos.pmqa.execucao.gerenciar', { contrato: props.contrato.id, servico: props.servico.id, campanha: props.campanha.id })">
        Voltar
        </Link>
      </div>
    </template>

    <Navbar :contrato="contrato" :servico="servico">
      <template #body>
        <div class="row mb-4">
          <div class="col form-group">
            <InputLabel value="Data da coleta" for="data_coleta" />
            <input type="date" class="form-control" name="data_coleta" id="data_coleta" v-model="form.data_coleta">
            <InputError :message="form.errors.data_coleta" />
          </div>
          <div class="col d-flex align-self-end justify-content-center form-group ">
            <label class="form-check">
              <input class="form-check-input" type="checkbox" v-model="form.sem_coleta">
              <span class="form-check-label">Não foi possível realizar a coleta</span>
            </label>
            <InputError :message="form.errors.sem_coleta" />
          </div>
        </div>
        <div v-if="form.sem_coleta === false">
          <div class="row mb-4">
            <div class="col form-group">
              <InputLabel value="Número da amostra" for="numero_amostra" />
              <input type="text" class="form-control" name="numero_amostra" id="numero_amostra"
                v-model="form.numero_amostra">
              <InputError :message="form.errors.numero_amostra" />
            </div>
            <div class="col form-group">
              <InputLabel value="Preservação da amostra" for="preservacao_amostra" />
              <input type="text" class="form-control" name="preservacao_amostra" id="preservacao_amostra"
                v-model="form.preservacao_amostra">
              <InputError :message="form.errors.preservacao_amostra" />
            </div>
          </div>
          <div class="row">
            <div class="col form-group">
              <InputLabel value="Acondicionamento da amostra" for="acondicionamento_amostra" />
              <input type="text" class="form-control" name="acondicionamento_amostra" id="acondicionamento_amostra"
                v-model="form.acondicionamento_amostra">
              <InputError :message="form.errors.acondicionamento_amostra" />
            </div>
            <div class="col form-group">
              <InputLabel value="Transporte da amostra" for="transporte_amostra" />
              <input type="text" class="form-control" name="transporte_amostra" id="transporte_amostra"
                v-model="form.transporte_amostra">
              <InputError :message="form.errors.transporte_amostra" />
            </div>
          </div>
        </div>
        <div v-else>
          <div class="row">
            <div class="col">
              <InputLabel value="Justificativa" for="transporte_amostra" />
              <textarea class="form-control" name="justificativa" id="justificativa" rows="5"
                v-model="form.justificativa"></textarea>
              <InputError :message="form.errors.justificativa" />
            </div>
          </div>
        </div>
        <div class="row mt-4">
          <div class="col d-flex justify-content-end">
            <NavButton @click="saveColetaPonto()" type-button="success" :icon="IconDeviceFloppy"
              :title="form.id ? 'Alterar' : 'Salvar'" />
          </div>
        </div>
        <div v-if="ponto.coleta?.id && !form.sem_coleta">
          <hr>
          <div class="row">
            <div class="col">
              <div>
                <InputLabel value="Arquivos" for="arquivo" />
                <div class="row g-2">
                  <div class="col">
                    <input @input="form.arquivo = $event.target.files[0]" type="file" class="form-control"
                      name="arquivo" id="arquivo">
                  </div>
                  <div class="col-auto">
                    <NavButton @click="saveArquivo()" type-button="success" title="Salvar" />
                  </div>
                </div>
                <InputError :message="form.errors.arquivo" />
              </div>
              <div v-if="ponto.coleta?.arquivos?.length" class="table-responsive mt-4">
                <table class="table table-hover non-hover">
                  <thead>
                    <tr>
                      <th>Nome</th>
                      <th>Açao</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="arquivo in ponto.coleta.arquivos" :key="arquivo.id">
                      <td>{{ arquivo.nome }}</td>
                      <td>
                        <a class="btn btn-icon btn-primary me-1" target="_blank"
                          :href="route('contratos.contratada.servicos.pmqa.execucao.coleta.show_arquivo', { contrato: props.contrato.id, servico: props.servico.id, campanha: props.campanha.id, arquivo: arquivo.id })">
                          <IconEye />
                        </a>
                        <LinkConfirmation v-slot="confirmation"
                          :options="{ text: 'A remoção da coleta será permanente.' }">
                          <Link :onBefore="confirmation.show"
                            :href="route('contratos.contratada.servicos.pmqa.execucao.coleta.delete_arquivo', { contrato: props.contrato.id, servico: props.servico.id, campanha: props.campanha.id, arquivo: arquivo.id })"
                            as="button" method="delete" type="button" class="btn btn-icon btn-danger">
                          <IconTrash />
                          </Link>
                        </LinkConfirmation>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </template>
    </Navbar>
  </AuthenticatedLayout>
</template>
