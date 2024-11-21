<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import Navbar from "../../Navbar.vue";
import { Head, Link } from "@inertiajs/vue3";
import ModelSearchFormAllColumns from "@/Components/ModelSearchFormAllColumns.vue";
import Table from "@/Components/Table.vue";
import NavButton from "@/Components/NavButton.vue";
import { ref } from "vue";
import { IconEye } from "@tabler/icons-vue";
import { IconPlus } from "@tabler/icons-vue";
import { IconPencil } from "@tabler/icons-vue";
import { IconDeviceFloppy } from "@tabler/icons-vue";
import NavLink from "@/Components/NavLink.vue";
import { IconMap } from "@tabler/icons-vue";
import TabDadosLote from "./TabDadosLote.vue";
import TabDadosObra from "./TabDadosObra.vue";
import TabObservacao from "./TabObservacao.vue";

const props = defineProps({
  contrato: { type: Object },
  servico: { type: Object },
  lote: { type: Object },
  rodovias: { type: Array }
});

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
          :href="route('contratos.contratada.servicos.cont_ocorrencia.configuracao.lote_obra.index', { contrato: contrato.id, servico: servico.id })">
        Voltar
        </Link>
      </div>
    </template>

    <Navbar :contrato="contrato" :servico="servico">
      <template #body>
        <div class="card-header">
          <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs" role="tablist">
            <li class="nav-item" role="presentation">
              <a href="#dados_lote" class="nav-link active" data-bs-toggle="tab" aria-selected="true" role="tab">Dados
                lote</a>
            </li>
            <li v-show="lote.id" class="nav-item" role="presentation">
              <a href="#dados_obra" class="nav-link" data-bs-toggle="tab" aria-selected="false" role="tab"
                tabindex="-1">Dados supervisora de obra</a>
            </li>
            <li v-show="lote.id" class="nav-item" role="presentation">
              <a href="#observacao" class="nav-link" data-bs-toggle="tab" aria-selected="false" role="tab"
                tabindex="-1">Observações</a>
            </li>
          </ul>
        </div>
        <div class="card-body">
          <div class="tab-content">
            <div class="tab-pane active show" id="dados_lote" role="tabpanel">
              <TabDadosLote :contrato="contrato" :servico="servico" :lote="lote" :rodovias="rodovias" />
            </div>
            <div v-show="lote.id" class="tab-pane" id="dados_obra" role="tabpanel">
              <TabDadosObra :contrato="contrato" :servico="servico" :lote="lote" />
            </div>
            <div v-show="lote.id" class="tab-pane" id="observacao" role="tabpanel">
              <TabObservacao :contrato="contrato" :servico="servico" :lote="lote" />
            </div>
          </div>
        </div>
      </template>
    </Navbar>

  </AuthenticatedLayout>
</template>
