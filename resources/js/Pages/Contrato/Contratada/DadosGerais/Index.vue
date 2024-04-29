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
      </div>
    </template>

    <Navbar :contrato="contrato">

      <template #body>
        <ul class="nav nav-tabs card-header-tabs nav-fill" data-bs-toggle="tabs" role="tablist">
          <li class="nav-item" role="presentation">
            <a @click="visualizarMapa()" href="#dadosContratuais" class="nav-link active d-flex justify-content-between"
              data-bs-toggle="tab" aria-selected="true" role="tab">
              <span>
                Dados contratuais
              </span>
              <IconCircleCheck class="text-success" />
            </a>
          </li>
          <li class="nav-item" role="presentation">
            <a href="#introducao" class="nav-link d-flex justify-content-between" data-bs-toggle="tab"
              aria-selected="false" role="tab" tabindex="-1">
              <span>
                Introdução
              </span>
              <IconCircleCheck class="text-success" v-if="contrato.introducao" />
              <IconCircleX class="text-danger" v-else />
            </a>
          </li>
          <li class="nav-item" role="presentation">
            <a href="#empreendimento" class="nav-link d-flex justify-content-between" data-bs-toggle="tab"
              aria-selected="false" tabindex="-1" role="tab">
              <span>
                Empreendimento
              </span>
              <IconCircleCheck class="text-success" v-if="contrato.empreendimento_trechos.length" />
              <IconCircleX class="text-danger" v-else />
            </a>
          </li>
          <li class="nav-item" role="presentation">
            <a href="#licenciamento" class="nav-link d-flex justify-content-between" data-bs-toggle="tab"
              aria-selected="false" tabindex="-1" role="tab">
              <span>
                Licenciamento
              </span>
              <IconCircleCheck class="text-success" v-if="contrato.licenciamentos.length" />
              <IconCircleX class="text-danger" v-else />
            </a>
          </li>
          <li class="nav-item" role="presentation">
            <a href="#historico" class="nav-link d-flex justify-content-between" data-bs-toggle="tab"
              aria-selected="false" tabindex="-1" role="tab">
              <span>
                Histórico
              </span>
              <IconCircleCheck class="text-success" v-if="contrato.historico.length" />
              <IconCircleX class="text-danger" v-else />
            </a>
          </li>
          <li class="nav-item" role="presentation">
            <a href="#anexo" class="nav-link d-flex justify-content-between" data-bs-toggle="tab" aria-selected="false"
              tabindex="-1" role="tab">
              <span>
                Anexos
              </span>
              <IconCircleCheck class="text-success" v-if="contrato.anexos.length" />
              <IconCircleX class="text-danger" v-else />
            </a>
          </li>
        </ul>

        <div class="card-body" style="margin-top: 15px;">
          <div class="tab-content">
            <div class="tab-pane active show" id="dadosContratuais" role="tabpanel">
              <TabDadosContratuais :contrato="contrato" ref="mapaTabDadosContratuais" />
            </div>
            <div class="tab-pane" id="introducao" role="tabpanel">
              <TabIntroducao :contrato="contrato" />
            </div>
            <div class="tab-pane" id="empreendimento" role="tabpanel">
              <TabEmpreendimento :contrato="contrato" :ufs="ufs" :rodovias="rodovias" />
            </div>
            <div class="tab-pane" id="licenciamento" role="tabpanel">
              <TabLicenciamento :contrato="contrato" :numero_licencas="numero_licencas" />
            </div>
            <div class="tab-pane" id="historico" role="tabpanel">
              <TabHistorico :contrato="contrato" />
            </div>
            <div class="tab-pane" id="anexo" role="tabpanel">
              <TabAnexo :contrato="contrato" />
            </div>
          </div>
        </div>
      </template>
    </Navbar>

  </AuthenticatedLayout>
</template>
