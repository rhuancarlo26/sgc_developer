<script setup>

import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import Navbar from "../../Navbar.vue";
import TabDadosGerais from "./Tabs/TabDadosGerais.vue";
import TabIdentificacaoEspecime from "./Tabs/TabIdentificacaoEspecime.vue";
import TabDadosEspecime from "./Tabs/TabDadosEspecime.vue";
import TabStatusConservacao from "./Tabs/TabStatusConservacao.vue";
import { useForm } from "@inertiajs/vue3";
import NavButton from "@/Components/NavButton.vue";
import TabObservacao from "./Tabs/TabObservacao.vue";
import { watch } from "vue";
import { ref } from "vue";

const props = defineProps({
  contrato: { type: Object },
  servico: { type: Object },
  registro: { type: Object },
  campanhas: { type: Array },
  modulos: { type: Array },
  status_conservacao: { type: Array },
  grupo_faunisticos: { type: Array }
});

const form = useForm({
  id: null,
  id_servico: null,
  nome_id: null,
  id_campanha: null,
  id_modulo: null,
  parcela_modulo: null,
  id_armadilha: null,
  id_grupo: null,
  id_tipo: 0,
  classe: null,
  ordem: null,
  familia: null,
  genero: null,
  especie: null,
  nome_comum: null,
  sexo: null,
  faixa_etaria: null,
  marcacao: null,
  num_marcacao: null,
  data_registro: null,
  hora_registro: null,
  coletado: null,
  num_tombamento: null,
  dds_bio_anotados: null,
  num_individuos: null,
  status_conserv_federal: null,
  status_conserv_iucn: null,
  ...props.registro
});

const form_arquivo = useForm({
  id: null,
  registro_id: null,
  arquivo: null
});

const save = () => {
  const url = form.id ? 'update' : 'store';

  form.post(route('contratos.contratada.servicos.monitora_fauna.execucao.registro.' + url, { contrato: props.contrato.id, servico: props.servico.id }), {
    onSuccess: () => Object.assign(form, props.registro)
  });
}

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
        <div class="card mb-4">
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
                <TabDadosGerais :campanhas="campanhas" :modulos="modulos" :grupo_faunisticos="grupo_faunisticos"
                  :form="form" />
              </div>
              <div class="tab-pane" id="identificacoes_especime" role="tabpanel">
                <TabIdentificacaoEspecime :form="form" />
              </div>
              <div class="tab-pane" id="dados_especime" role="tabpanel">
                <TabDadosEspecime :form="form" />
              </div>
              <div class="tab-pane" id="status_conservacao" role="tabpanel">
                <TabStatusConservacao :status_conservacao="status_conservacao" :form="form" />
              </div>
              <div class="tab-pane" id="registro_fotografico" role="tabpanel">
                5
              </div>
              <div class="tab-pane" id="observacao" role="tabpanel">
                <TabObservacao :form="form" />
              </div>
            </div>
          </div>
        </div>
        <div class="d-flex justify-content-end">
          <NavButton @click="save()" type-button="success" :title="form.id ? 'Alterar' : 'Salvar'" />
        </div>
      </template>
    </Navbar>

  </AuthenticatedLayout>
</template>
