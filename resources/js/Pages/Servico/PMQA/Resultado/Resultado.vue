<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import Navbar from "../Navbar.vue";
import { Head, Link } from "@inertiajs/vue3";
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

const props = defineProps({
  contrato: { type: Object },
  servico: { type: Object },
  resultado: { type: Object },
  parametros: { type: Array }
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
        uniqueParametros[vinculado.parametro_id][vinculado.lista_parametro_id] = {
          nome_lista: ponto.lista.nome,
          medicao: vinculado.medicao.medicao
        }
      });
    });
  });

  return uniqueParametros;
});

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
                data-bs-toggle="tab" aria-selected="false" tabindex="-1" role="tab">{{ parametro.nome }}</a>
            </li>
          </ul>
        </div>
        <div class="card-body">
          <div class="tab-content">
            <div v-for="parametro, key, index in filtroParametros" :key="parametro.id" class="tab-pane"
              :class="index === 0 ? 'active show' : ''" :id="'tabs-parametro-' + parametro.id" role="tabpanel">
              <h4>{{ parametro.nome }}</h4>
              <pre>{{ parametro }}</pre>
            </div>
          </div>
        </div>
      </template>
    </Navbar>

  </AuthenticatedLayout>
</template>