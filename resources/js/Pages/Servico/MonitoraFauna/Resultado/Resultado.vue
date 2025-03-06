<script setup>

import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import Navbar from "../Navbar.vue";
import NavButton from "@/Components/NavButton.vue";
import ModelSearchFormAllColumns from "@/Components/ModelSearchFormAllColumns.vue";
import Table from "@/Components/Table.vue";
import { IconDots, IconMap, IconMinus } from "@tabler/icons-vue";
import { ref } from "vue";
import { Head, Link, router, useForm } from "@inertiajs/vue3";
import LinkConfirmation from "@/Components/LinkConfirmation.vue";
import { dateTimeFormat } from "@/Utils/DateTimeUtils";
import ModalFormResultado from "./ModalFormResultado.vue";
import TabAvifauna from "./components/TabAvifauna.vue";
import TabHerpetofauna from "./components/TabHerpetofauna.vue";
import TabMastofauna from "./components/TabMastofauna.vue";
import TabIctiofauna from "./components/TabIctiofauna.vue";
import TabBentos from "./components/TabBentos.vue";


const props = defineProps({
  contrato: { type: Object },
  servico: { type: Object },
  grupo_faunisticos: { type: Array },
  dados_tabela: { type: Object },
  resultado: { type: Object },
  dados_campanha: { type: Object },
  dados_armadilha: { type: Object },
  dados_ordem: { type: Object },
  dados_familia: { type: Object },
  dados_coletor: { type: Object },
});

const form = useForm({});

</script>
<template>

  <Head title="Resultado" />

  <AuthenticatedLayout>

    <template #header>
      <div class="w-100 d-flex justify-content-between">
        <Breadcrumb class="align-self-center" :links="[
          { route: route('contratos.gestao.listagem', contrato.tipo_contrato), label: `Gestão de Contratos` },
          { route: '#', label: contrato.contratada }
        ]
          " />
        <Link class="btn btn-dark"
          :href="route('contratos.contratada.servicos.index', { contrato: props.contrato.id })">
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
                <a href="#tabs-avifauna" class="nav-link active" data-bs-toggle="tab" aria-selected="true"
                  role="tab">Avifauna</a>
              </li>
              <li class="nav-item" role="presentation">
                <a href="#tabs-herpetofauna" class="nav-link" data-bs-toggle="tab" aria-selected="false"
                  role="tab">Herpetofauna</a>
              </li>
              <li class="nav-item" role="presentation">
                <a href="#tabs-mastofauna" class="nav-link" data-bs-toggle="tab" aria-selected="false"
                  role="tab">Mastofauna</a>
              </li>
              <li class="nav-item" role="presentation">
                <a href="#tabs-ictiofauna" class="nav-link" data-bs-toggle="tab" aria-selected="false"
                  role="tab">Ictiofauna</a>
              </li>
              <li class="nav-item" role="presentation">
                <a href="#tabs-bentos" class="nav-link" data-bs-toggle="tab" aria-selected="false" role="tab">Bentos</a>
              </li>
            </ul>
          </div>
          <div class="card-body">
            <div class="tab-content">
              <div class="tab-pane active show" id="tabs-avifauna" role="tabpanel">
                <TabAvifauna :form="form" :dados_tabela="dados_tabela['Ictiofauna']"
                  :dados_campanha="dados_campanha['Ictiofauna']" :dados_armadilha="dados_armadilha['Ictiofauna']"
                  :dados_ordem="dados_ordem['Ictiofauna']" :dados_familia="dados_familia['Ictiofauna']"
                  :dados_coletor="dados_coletor['']" />
              </div>
              <div class="tab-pane" id="tabs-herpetofauna" role="tabpanel">
                <TabHerpetofauna :form="form" :contrato="contrato" :servico="servico"
                  :dados_tabela="dados_tabela['Herpetofauna']" :dados_campanha="dados_campanha['Herpetofauna']"
                  :dados_armadilha="dados_armadilha['Herpetofauna']" :dados_ordem="dados_ordem['Herpetofauna']"
                  :dados_familia="dados_familia['Herpetofauna']" :dados_coletor="dados_coletor['Herpetofauna']" />
              </div>
              <div class="tab-pane" id="tabs-mastofauna" role="tabpanel">
                <TabMastofauna :form="form" :dados_tabela="dados_tabela['Mastofauna']"
                  :dados_campanha="dados_campanha['Mastofauna']" :dados_armadilha="dados_armadilha['Mastofauna']"
                  :dados_ordem="dados_ordem['Mastofauna']" :dados_familia="dados_familia['Mastofauna']" />
              </div>
              <div class="tab-pane" id="tabs-ictiofauna" role="tabpanel">
                <TabIctiofauna :form="form" :dados_tabela="dados_tabela['Ictiofauna']"
                  :dados_campanha="dados_campanha['Ictiofauna']" :dados_armadilha="dados_armadilha['Ictiofauna']"
                  :dados_ordem="dados_ordem['Ictiofauna']" :dados_familia="dados_familia['Ictiofauna']" />
              </div>
              <div class="tab-pane" id="tabs-bentos" role="tabpanel">
                <TabBentos :form="form" :dados_tabela="dados_tabela['Bentos']"
                  :dados_campanha="dados_campanha['Bentos']" :dados_armadilha="dados_armadilha['Bentos']"
                  :dados_ordem="dados_ordem['Bentos']" :dados_familia="dados_familia['Bentos']" />
              </div>
            </div>
          </div>
        </div>
      </template>
    </Navbar>

  </AuthenticatedLayout>
</template>
