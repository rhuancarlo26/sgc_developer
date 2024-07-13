<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import Navbar from "../Navbar.vue";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import NavButton from "@/Components/NavButton.vue";
import { onMounted, ref } from "vue";
import { IconEdit } from "@tabler/icons-vue";
import { IconFileTypePdf } from "@tabler/icons-vue";
import LinkConfirmation from "@/Components/LinkConfirmation.vue";
import { IconX } from "@tabler/icons-vue";
import { IconTrash } from "@tabler/icons-vue";
import { IconDeviceFloppy } from "@tabler/icons-vue";
import BarChart from "@/Components/BarChart.vue";
import DivTabelaParametro from "../Configuracao/Parametro/DivTabelaParametro.vue";
import DivTabelaMedirIqaVue from "../Configuracao/Parametro/DivTabelaMedirIqa.vue";

const props = defineProps({
  contrato: { type: Object },
  servico: { type: Object },
  resultado: { type: Object },
  parametros: { type: Array },
  uniqueParametros: { type: Object },
  chartDataIqa: { type: Object },
});

const form = useForm({
  resultado_id: props.resultado.id,
  analises: [],
});

const form_iqa = useForm({
  id: null,
  resultado_id: props.resultado.id,
  analise: null,
  ...props.resultado.analise_iqa
})

let form_outra_analise = useForm({
  id: null,
  resultado_id: props.resultado.id,
  nome: null,
  arquivo: null,
  analise: null
});

onMounted(() => {
  if (props.resultado.analises.length) {
    props.resultado.analises.forEach(analise => {
      form.analises[analise.parametro_id] = analise.analise;
    });
  }
});

const salvarAnalise = () => {
  if (props.resultado.analises.length) {
    form.patch(route('contratos.contratada.servicos.pmqa.resultado.update_analise', { contrato: props.contrato.id, servico: props.servico.id, resultado: props.resultado.id }))
  } else {
    form.post(route('contratos.contratada.servicos.pmqa.resultado.store_analise', { contrato: props.contrato.id, servico: props.servico.id, resultado: props.resultado.id }))
  }
}

const salvarAnaliseIqa = () => {
  if (form_iqa.id) {
    form_iqa.patch(route('contratos.contratada.servicos.pmqa.resultado.update_analise_iqa', { contrato: props.contrato.id, servico: props.servico.id, resultado: props.resultado.id }))
  } else {
    form_iqa.post(route('contratos.contratada.servicos.pmqa.resultado.store_analise_iqa', { contrato: props.contrato.id, servico: props.servico.id, resultado: props.resultado.id }))
  }
}

const salvarOutraAnalise = () => {
  if (form_outra_analise.id) {
    console.log('form', form_outra_analise);
    form_outra_analise.post(route('contratos.contratada.servicos.pmqa.resultado.update_outra_analise', { contrato: props.contrato.id, servico: props.servico.id, resultado: props.resultado.id }))
  } else {
    form_outra_analise.post(route('contratos.contratada.servicos.pmqa.resultado.store_outra_analise', { contrato: props.contrato.id, servico: props.servico.id, resultado: props.resultado.id }))
  }
}

const horizontalLine = ref({
  plugins: {
    annotation: {
      annotations: {
        line1: {
          type: 'line',
          yMin: 20,
          yMax: 20,
          borderColor: '#667382',
          borderWidth: 2,
          label: {
            content: 'Péssimo',
            enabled: true,
            position: 'start'
          }
        },
        line2: {
          type: 'line',
          yMin: 36,
          yMax: 36,
          borderColor: '#d63939',
          borderWidth: 2,
          label: {
            content: 'Threshold',
            enabled: true,
            position: 'start'
          }
        },
        line3: {
          type: 'line',
          yMin: 51,
          yMax: 51,
          borderColor: '#f76707',
          borderWidth: 2,
          label: {
            content: 'Threshold',
            enabled: true,
            position: 'start'
          }
        },
        line4: {
          type: 'line',
          yMin: 79,
          yMax: 79,
          borderColor: '#2fb344',
          borderWidth: 2,
          label: {
            content: 'Threshold',
            enabled: true,
            position: 'start'
          }
        },
        line5: {
          type: 'line',
          yMin: 100,
          yMax: 100,
          borderColor: '#0054a6',
          borderWidth: 2,
          label: {
            content: 'Threshold',
            enabled: true,
            position: 'start'
          }
        },
      }
    }
  }
})

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
          :href="route('contratos.contratada.servicos.pmqa.resultado.index', { contrato: props.contrato.id, servico: props.servico.id })">
        Voltar
        </Link>
      </div>
    </template>

    <Navbar :contrato="contrato" :servico="servico">
      <template #body>
        <div class="card-header">
          <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs" role="tablist">
            <li v-for="parametro in uniqueParametros" :key="parametro.id" class="nav-item" role="presentation">
              <a :href="'#tabs-parametro-' + parametro.id" class="nav-link" data-bs-toggle="tab" aria-selected="false"
                tabindex="-1" role="tab">{{ `${parametro.nome}
                ${parametro.unidade ? ` - ${parametro.unidade}` : ''}` }}</a>
            </li>
            <li class="nav-item" role="presentation">
              <a href="#tabs-parametro-iqa" class="nav-link" data-bs-toggle="tab" aria-selected="false" tabindex="-1"
                role="tab">IQA</a>
            </li>
            <li class="nav-item" role="presentation">
              <a href="#tabs-parametro-outra-analise" class="nav-link" data-bs-toggle="tab" aria-selected="false"
                tabindex="-1" role="tab">Outras analises</a>
            </li>
          </ul>
        </div>
        <div class="card-body">
          <div class="tab-content">
            <div v-for="parametro in uniqueParametros" :key="parametro.id" class="tab-pane"
              :id="'tabs-parametro-' + parametro.id" role="tabpanel">
              <BarChart :style="{ height: '70px', position: 'relative' }" :chart_data="parametro.datasets"
                :chart_options="{ responsive: true }" />

              <div class="card mb-4">
                <DivTabelaParametro :parametro="parametro" />
              </div>

              <div>
                <div class="row mb-4">
                  <div class="col form-group">
                    <InputLabel value="Analise" for="analise" />
                    <textarea :name="'analise-' + parametro.id" :id="'analise-' + parametro.id" class="form-control"
                      rows="6" v-model="form.analises[parametro.id]"></textarea>
                    <InputError :message="form.errors.analise" />
                  </div>
                </div>
                <div class="row">
                  <div class="col form-group d-flex justify-content-end">
                    <NavButton @click="salvarAnalise()" type-button="success" :icon="IconDeviceFloppy" title="Salvar" />
                  </div>
                </div>
              </div>
            </div>
            <div class="tab-pane" id="tabs-parametro-iqa" role="tabpanel">
              <BarChart :style="{
                height: '70px',
                position: 'relative'
              }" :chart_data="chartDataIqa" :options="horizontalLine" :chart_options="{ responsive: true }" />

              <div class="card mb-4">
                <DivTabelaMedirIqaVue />
              </div>
              <div>
                <div class="row mb-4">
                  <div class="col form-group">
                    <InputLabel value="Analise" for="analise" />
                    <textarea name="analise-iqa" id="analise-iqa" class="form-control" rows="6"
                      v-model="form_iqa.analise"></textarea>
                    <InputError :message="form.errors.analise" />
                  </div>
                </div>
                <div class="row">
                  <div class="col form-group d-flex justify-content-end">
                    <NavButton @click="salvarAnaliseIqa()" type-button="success" :icon="IconDeviceFloppy"
                      title="Salvar" />
                  </div>
                </div>
              </div>
            </div>
            <div class="tab-pane" id="tabs-parametro-outra-analise" role="tabpanel">
              <div>
                <div class="row mb-4">
                  <div class="col form-group">
                    <InputLabel value="Nome da analise" for="nome" />
                    <input type="text" name="nome" id="nome" class="form-control" v-model="form_outra_analise.nome">
                    <InputError :message="form.errors.analise" />
                  </div>
                </div>
                <div class="row mb-4">
                  <div class="col form-group">
                    <InputLabel value="Buscar arquivo" for="arquivo" />
                    <input @input="form_outra_analise.arquivo = $event.target.files[0]" type="file" name="arquivo"
                      id="arquivo" class="form-control">
                    <InputError :message="form_outra_analise.errors.arquivo" />
                  </div>
                </div>
                <div class="row mb-4">
                  <div class="col form-group">
                    <InputLabel value="Analise dos resultados" for="analise" />
                    <textarea name="analise" id="analise" rows="6" class="form-control"
                      v-model="form_outra_analise.analise"></textarea>
                    <InputError :message="form_outra_analise.errors.analise" />
                  </div>
                </div>
                <div class="row mb-4">
                  <div class="col form-group d-flex justify-content-end">
                    <NavButton v-if="form_outra_analise.id" @click="form_outra_analise.reset()" type-button="danger"
                      :icon="IconX" />
                    <NavButton @click="salvarOutraAnalise()" type-button="success" :icon="IconDeviceFloppy"
                      :title="form_outra_analise ? 'Editar' : 'Salvar'" />
                  </div>
                </div>
                <div class="row">
                  <div class="col">
                    <div class="table-responsive">
                      <table class="table table-hover non-hover">
                        <thead>
                          <tr>
                            <th>Nome</th>
                            <th>Análise dos resultados</th>
                            <th>Ação</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr v-for="outraAnalise in resultado.outras_analises" :key="outraAnalise.id">
                            <td>{{ outraAnalise.nome }}</td>
                            <td>{{ outraAnalise.analise }}</td>
                            <td>
                              <a v-if="outraAnalise.caminho" class="btn btn-icon btn-primary me-1" target="_blank"
                                :href="route('contratos.contratada.servicos.pmqa.resultado.visualizar_outra_analise', { contrato: contrato.id, servico: servico.id, resultado: resultado.id, outra_analise: outraAnalise.id })">
                                <IconFileTypePdf />
                              </a>
                              <NavButton @click="Object.assing(form_outra_analise, outraAnalise)" route-name="#"
                                type-button="info" :icon="IconEdit" class="btn btn-icon" />
                              <LinkConfirmation v-slot="confirmation"
                                :options="{ text: 'A remoção da campanha será permanente.' }">
                                <Link :onBefore="confirmation.show"
                                  :href="route('contratos.contratada.servicos.pmqa.resultado.delete_outra_analise', { contrato: contrato.id, servico: servico.id, resultado: resultado.id, outra_analise: outraAnalise.id })"
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
            </div>
          </div>
        </div>
      </template>
    </Navbar>

  </AuthenticatedLayout>
</template>