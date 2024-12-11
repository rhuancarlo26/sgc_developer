<script setup>

import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import Navbar from "../../Navbar.vue";
import TabDadosGerais from "./Tabs/TabDadosGerais.vue";
import TabIdentificacaoEspecime from "./Tabs/TabIdentificacaoEspecime.vue";

const props = defineProps({
  contrato: { type: Object },
  servico: { type: Object },
  registros: { type: Object },
});

</script>
<template>

  <Head title="Registros" />

  <AuthenticatedLayout>

    <template #header>
      <div class="w-100 d-flex justify-content-between">
        <Breadcrumb class="align-self-center" :links="[
          { route: route('contratos.gestao.listagem', contrato.tipo_contrato), label: `Gestão de Contratos` },
          { route: '#', label: contrato.contratada }
        ]
          " />
        <Link class="btn btn-dark"
          :href="route('contratos.contratada.servicos.monitora_fauna.execucao.registro.index', { contrato: contrato.id, servico: servico.id })">
        Voltar
        </Link>
      </div>
    </template>

    <Navbar :contrato="contrato" :servico="servico">
      <template #body>
        <div class="card">
          <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs" role="tablist">
              <li class="nav-item" role="presentation">
                <a href="#dados_gerais" class="nav-link active" data-bs-toggle="tab" aria-selected="true"
                  role="tab">DADOS GERAIS</a>
              </li>
              <li class="nav-item" role="presentation">
                <a href="#identificacoes_especime" class="nav-link" data-bs-toggle="tab" aria-selected="false"
                  role="tab" tabindex="-1">IDENTIFICAÇÃO DO ESPÉCIME</a>
              </li>
              <li class="nav-item" role="presentation">
                <a href="#dados_especime" class="nav-link" data-bs-toggle="tab" aria-selected="false" role="tab"
                  tabindex="-1">DADOS DO ESPÉCIME</a>
              </li>
              <li class="nav-item" role="presentation">
                <a href="#status_conservacao" class="nav-link" data-bs-toggle="tab" aria-selected="false" role="tab"
                  tabindex="-1">STATUS DE CONSERVAÇÃO</a>
              </li>
              <li class="nav-item" role="presentation">
                <a href="#registro_fotografico" class="nav-link" data-bs-toggle="tab" aria-selected="false" role="tab"
                  tabindex="-1">REGISTRO FOTOGRÁFICOS</a>
              </li>
              <li class="nav-item" role="presentation">
                <a href="#observacao" class="nav-link" data-bs-toggle="tab" aria-selected="false" role="tab"
                  tabindex="-1">OBSERVAÇÕES</a>
              </li>
            </ul>
          </div>
          <div class="card-body">
            <div class="tab-content">
              <div class="tab-pane active show" id="dados_gerais" role="tabpanel">
                <TabDadosGerais :contrato="contrato" :servico="servico" />
              </div>
              <div class="tab-pane" id="identificacoes_especime" role="tabpanel">
                <TabIdentificacaoEspecime :contrato="contrato" :servico="servico" />
              </div>
              <div class="tab-pane" id="dados_especime" role="tabpanel">
                3
              </div>
              <div class="tab-pane" id="status_conservacao" role="tabpanel">
                4
              </div>
              <div class="tab-pane" id="registro_fotografico" role="tabpanel">
                5
              </div>
              <div class="tab-pane" id="observacao" role="tabpanel">
                6
              </div>
            </div>
          </div>
        </div>
      </template>
    </Navbar>

  </AuthenticatedLayout>
</template>
