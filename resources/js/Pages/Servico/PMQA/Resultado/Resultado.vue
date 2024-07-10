<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import Navbar from "../Navbar.vue";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import ModelSearchFormAllColumns from "@/Components/ModelSearchFormAllColumns.vue";
import Table from "@/Components/Table.vue";
import NavButton from "@/Components/NavButton.vue";
import NavLink from "@/Components/NavLink.vue";
import { onMounted, ref } from "vue";
import { dateTimeFormat } from "@/Utils/DateTimeUtils";
import LinkConfirmation from "@/Components/LinkConfirmation.vue";
import { IconTrash } from "@tabler/icons-vue";
import { IconPencil } from "@tabler/icons-vue";
import { IconSettings } from "@tabler/icons-vue";
import ModalResultado from "./ModalResultado.vue";
import { computed } from "vue";
import BarChart from "@/Components/BarChart.vue";
import { IconDeviceFloppy } from "@tabler/icons-vue";

const props = defineProps({
  contrato: { type: Object },
  servico: { type: Object },
  resultado: { type: Object },
  parametros: { type: Array }
});

const form = useForm({
  resultado_id: props.resultado.id,
  analises: []
});

onMounted(() => {
  if (props.resultado.analises.length) {
    props.resultado.analises.forEach(analise => {
      form.analises[analise.parametro_id] = analise.analise;
    });
  }
});

const filtroParametros = computed(() => {
  const parametrosIds = [...new Set(props.resultado.campanhas.map(campanha => campanha.pontos.map(ponto => ponto.lista.parametros)).flat(2).map(parametro => parametro.id))];

  let uniqueParametros = props.parametros.filter(parametro => parametrosIds.includes(parametro.id)).reduce((acc, item) => {
    acc[item.id] = item;
    return acc;
  }, {});

  props.resultado.campanhas.forEach(campanha => {
    campanha.pontos.forEach(ponto => {
      ponto.lista.parametros_vinculados.forEach(vinculado => {

      });
    });
  });
  console.log('parametros', uniqueParametros);
  return uniqueParametros;
});

const salvarAnalise = () => {
  if (props.resultado.analises.length) {
    form.patch(route('contratos.contratada.servicos.pmqa.resultado.update_analise', { contrato: props.contrato.id, servico: props.servico.id, resultado: props.resultado.id }))
  } else {
    form.post(route('contratos.contratada.servicos.pmqa.resultado.store_analise', { contrato: props.contrato.id, servico: props.servico.id, resultado: props.resultado.id }))
  }
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
          :href="route('contratos.contratada.servicos.pmqa.resultado.index', { contrato: props.contrato.id, servico: props.servico.id })">
        Voltar
        </Link>
      </div>
    </template>

    <Navbar :contrato="contrato" :servico="servico">
      <template #body>
        <div class="card-header">
          <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs" role="tablist">
            <li v-for="parametro, key, index in filtroParametros" :key="parametro.id" class="nav-item"
              role="presentation">
              <a :href="'#tabs-parametro-' + parametro.id" class="nav-link" :class="index === 0 ? 'active' : ''"
                data-bs-toggle="tab" aria-selected="false" tabindex="-1" role="tab">{{ `${parametro.nome}
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
            <div v-for="parametro, key, index in filtroParametros" :key="parametro.id" class="tab-pane"
              :class="index === 0 ? 'active show' : ''" :id="'tabs-parametro-' + parametro.id" role="tabpanel">
              <BarChart :chart_data="{
                labels: ['Medição'],
                datasets: [{
                  label: 'Data One',
                  backgroundColor: '#f87979',
                  data: [2]
                }, {
                  label: 'Data One',
                  backgroundColor: '#f87900',
                  data: [1]
                }]
              }" :chart_options="{ responsive: true }"></BarChart>
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
              <div>
                <div class="row mb-4">
                  <div class="col form-group">
                    <InputLabel value="Analise" for="analise" />
                    <textarea name="analise-iqa" id="analise-iqa" class="form-control" rows="6"></textarea>
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
            <div class="tab-pane" id="tabs-parametro-outra-analise" role="tabpanel">
              Outra analise
            </div>
          </div>
        </div>
      </template>
    </Navbar>

  </AuthenticatedLayout>
</template>