<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import Navbar from "../../Navbar.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import NavButton from "@/Components/NavButton.vue";
import { IconDeviceFloppy } from "@tabler/icons-vue";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import { onMounted } from "vue";
import LinkConfirmation from "@/Components/LinkConfirmation.vue";
import { IconTrash } from "@tabler/icons-vue";
import { IconEye } from "@tabler/icons-vue";

const props = defineProps({
  contrato: { type: Object },
  servico: { type: Object },
  campanha: { type: Object },
  ponto: { type: Object }
});

const form = useForm({
  id: null,
  campanha_ponto_id: props.ponto.id,
  sem_coleta: false,
  justificativa: null,
  iqa: null,
  parametros: [],
  arquivo: null
});

onMounted(() => {
  if (props.ponto.medicao) {
    form.id = props.ponto.medicao.id;
    form.sem_coleta = props.ponto.medicao.sem_coleta;
    form.iqa = props.ponto.medicao.iqa;
    form.justificativa = props.ponto.medicao.justificativa;

    if (props.ponto.medicao.parametros.length) {
      props.ponto.medicao.parametros.forEach(parametro => {
        form.parametros[parametro.lista_parametro_id] = parametro.medicao;
      });
    }
  }
})

const saveMedicao = () => {
  if (props.ponto.ponto?.lista?.medir_iqa) {
    form.iqa = null;
  }

  if (form.id) {
    form.patch(route('contratos.contratada.servicos.pmqa.execucao.medir.update', { contrato: props.contrato.id, servico: props.servico.id, campanha: props.campanha.id, ponto: props.ponto.id }))
  } else {
    form.post(route('contratos.contratada.servicos.pmqa.execucao.medir.store', { contrato: props.contrato.id, servico: props.servico.id, campanha: props.campanha.id, ponto: props.ponto.id }))
  }
}

const saveArquivo = () => {
  form.id = props.ponto.medicao.id;

  form.post(route('contratos.contratada.servicos.pmqa.execucao.medir.store_arquivo', { contrato: props.contrato.id, servico: props.servico.id, campanha: props.campanha.id, ponto: props.ponto.id }))
}

</script>
<template>

  <Head :title="`${contrato.contratada.slice(0, 10)}...`" />

  <AuthenticatedLayout>

    <template #header>
      <div class="w-100 d-flex justify-content-between">
        <Breadcrumb class="align-self-center" :links="[
          { route: route('contratos.gestao.listagem', contrato.tipo_id), label: `Gestão de Contratos` },
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
          <div class="col d-flex align-self-end">
            <label class="form-check">
              <input class="form-check-input" type="checkbox" v-model="form.sem_coleta">
              <span class="form-check-label">Não foi possível realizar a coleta</span>
            </label>
            <InputError :message="form.errors.sem_coleta" />
          </div>
        </div>
        <div v-if="!form.sem_coleta" class="row">
          <div class="table-responsive">
            <table class="table card-table table-bordered">
              <thead>
                <tr>
                  <th>Parâmetro</th>
                  <th>Unidade</th>
                  <th>Limite</th>
                  <th>Medição</th>
                </tr>
              </thead>
              <tbody>
                <tr v-if="ponto.ponto?.lista?.medir_iqa">
                  <td colspan="3">IQA</td>
                  <td>
                    <input type="text" class="form-control" v-model="form.iqa">
                  </td>
                </tr>
                <tr v-for="vinculado in ponto.ponto?.lista?.parametros_vinculados" :key="vinculado.id">
                  <td>{{ vinculado.parametro.nome }}</td>
                  <td>{{ vinculado.parametro.unidade }}</td>
                  <td>{{ vinculado.parametro.limite ? vinculado.parametro.classe_2 : 'Sem limite' }}</td>
                  <td>
                    <input type="text" class="form-control" v-model="form.parametros[vinculado.id]">
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
        <div v-else class="row">
          <div class="col">
            <InputLabel value="Justificativa" for="transporte_amostra" />
            <textarea class="form-control" name="justificativa" id="justificativa" rows="5"
              v-model="form.justificativa"></textarea>
            <InputError :message="form.errors.justificativa" />
          </div>
        </div>
        <div class="d-flex justify-content-end mt-4">
          <NavButton @click="saveMedicao()" type-button="success" :icon="IconDeviceFloppy"
            :title="form.id ? 'Alterar' : 'Salvar'" />
        </div>
        <div v-if="ponto.medicao?.id && !form.sem_coleta">
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
              <div v-if="ponto.medicao?.arquivos?.length" class="table-responsive mt-4">
                <table class="table table-hover non-hover">
                  <thead>
                    <tr>
                      <th>Nome</th>
                      <th>Açao</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="arquivo in ponto.medicao.arquivos" :key="arquivo.id">
                      <td>{{ arquivo.nome }}</td>
                      <td>
                        <a class="btn btn-icon btn-primary me-1" target="_blank"
                          :href="route('contratos.contratada.servicos.pmqa.execucao.medir.show_arquivo', { contrato: props.contrato.id, servico: props.servico.id, campanha: props.campanha.id, arquivo: arquivo.id })">
                          <IconEye />
                        </a>
                        <LinkConfirmation v-slot="confirmation"
                          :options="{ text: 'A remoção da coleta será permanente.' }">
                          <Link :onBefore="confirmation.show"
                            :href="route('contratos.contratada.servicos.pmqa.execucao.medir.delete_arquivo', { contrato: props.contrato.id, servico: props.servico.id, campanha: props.campanha.id, arquivo: arquivo.id })"
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