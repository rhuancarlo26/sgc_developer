<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import Navbar from "../Navbar.vue";
import { Head } from "@inertiajs/vue3";
import TabDadosContratuais from "./DadosContratuais/TabDadosContratuais.vue";
import { ref } from "vue";
import TabIntroducao from "./Introducao/TabIntroducao.vue";
import TabLicenciamento from "./Licenciamento/TabLicenciamento.vue";
import TabHistorico from "./Historico/TabHistorico.vue";
import TabAnexo from "./Anexo/TabAnexo.vue";
import TabEmpreendimento from "./Empreendimento/TabEmpreendimento.vue";
import { IconCircleCheck, IconCircleX } from "@tabler/icons-vue";
import NavLinkVoid from "@/Components/NavLinkVoid.vue";

const mapaTabDadosContratuais = ref();

defineProps({
  contrato: Object,
  numero_licencas: Array,
  ufs: Array,
  rodovias: Array
})

const visualizarMapa = () => {
  mapaTabDadosContratuais.value.visualizarTrecho();
}

const activeTab = ref('dadosContratuais');

let switchTab = (tabName) => {
  activeTab.value = tabName;
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
      </div>
    </template>

    <Navbar :contrato="contrato">
      <template #body>
        <ul class="nav nav-tabs card-header-tabs nav-fill" data-bs-toggle="tabs" role="tablist">
          <li class="nav-item" role="presentation" @click="visualizarMapa">
            <NavLinkVoid title="Dados contratuais" :icon="IconCircleCheck" tabName="dadosContratuais"
              :iconClass="'text-success'" @switch-tab="switchTab"
              class="nav-link active d-flex justify-content-between" data-bs-toggle="tab" aria-selected="true"
              role="tab" />
          </li>
          <li class="nav-item" role="presentation">
            <NavLinkVoid title="Introdução" :icon="contrato.introducao ? IconCircleCheck : IconCircleX"
              :iconClass="contrato.introducao ? 'text-success' : 'text-danger'" tabName="introducao"
              @switch-tab="switchTab" class="nav-link active d-flex justify-content-between" data-bs-toggle="tab"
              aria-selected="true" role="tab" />
          </li>
<!--          <li class="nav-item" role="presentation">-->
<!--            <NavLinkVoid title="Empreendimento"-->
<!--              :icon="contrato.empreendimento_trechos.length ? IconCircleCheck : IconCircleX" tabName="empreendimento"-->
<!--              :iconClass="contrato.empreendimento_trechos.length ? 'text-success' : 'text-danger'" @switch-tab="switchTab"-->
<!--              class="nav-link active d-flex justify-content-between" data-bs-toggle="tab" aria-selected="true"-->
<!--              role="tab" />-->
<!--          </li>-->
          <li class="nav-item" role="presentation">
            <NavLinkVoid title="Licenciamento" :icon="contrato.licenciamentos.length ? IconCircleCheck : IconCircleX"
              :iconClass="contrato.licenciamentos.length ? 'text-success' : 'text-danger'" tabName="licenciamento"
              @switch-tab="switchTab" class="nav-link active d-flex justify-content-between" data-bs-toggle="tab"
              aria-selected="true" role="tab" />
          </li>
          <li class="nav-item" role="presentation">
            <NavLinkVoid title="Histórico" :icon="contrato.historico.length ? IconCircleCheck : IconCircleX"
              :iconClass="contrato.historico.length ? 'text-success' : 'text-danger'" tabName="historico"
              @switch-tab="switchTab" class="nav-link active d-flex justify-content-between" data-bs-toggle="tab"
              aria-selected="true" role="tab" />
          </li>
          <li class="nav-item" role="presentation">
            <NavLinkVoid title="Anexos" :icon="contrato.anexos.length ? IconCircleCheck : IconCircleX"
              :iconClass="contrato.anexos.length ? 'text-success' : 'text-danger'" tabName="anexo"
              @switch-tab="switchTab" class="nav-link active d-flex justify-content-between" data-bs-toggle="tab"
              aria-selected="true" role="tab" />
          </li>
        </ul>
        <div v-show="activeTab === 'dadosContratuais'">
          <TabDadosContratuais :contrato="contrato" ref="mapaTabDadosContratuais" />
        </div>
        <div v-show="activeTab === 'introducao'">
          <TabIntroducao :contrato="contrato" />
        </div>
<!--        <div v-show="activeTab === 'empreendimento'">-->
<!--          <TabEmpreendimento :contrato="contrato" :ufs="ufs" :rodovias="rodovias" />-->
<!--        </div>-->
        <div v-show="activeTab === 'licenciamento'">
          <TabLicenciamento :contrato="contrato" :numero_licencas="numero_licencas" />
        </div>
        <div v-show="activeTab === 'historico'">
          <TabHistorico :contrato="contrato" />
        </div>
        <div v-show="activeTab === 'anexo'">
          <TabAnexo :contrato="contrato" />
        </div>
      </template>
    </Navbar>

  </AuthenticatedLayout>
</template>
