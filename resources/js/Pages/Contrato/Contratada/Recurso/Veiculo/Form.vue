<script setup>

import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import ModelSearchForm from "@/Components/ModelSearchForm.vue";
import Table from '@/Components/Table.vue';
import Navbar from "../../Navbar.vue";
import { Head, Link, router, useForm } from "@inertiajs/vue3";
import { ref } from "vue";
import { IconDeviceFloppy, IconDoorExit, IconDots } from "@tabler/icons-vue";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import TabDadosGerais from "./TabDadosGerais.vue";
import TabDocumentos from "./TabDocumentos.vue";
import TabQuilometragem from "./TabQuilometragem.vue";

const props = defineProps({
  contrato: Object,
  veiculo: Object,
  codigos: Array
});

</script>

<template>

  <Head :title="`${contrato.contratada.slice(0, 10)}...`" />

  <AuthenticatedLayout>

    <template #header>
      <div class="w-100 d-flex justify-content-between">
        <Breadcrumb class="align-self-center" :links="[
          { route: route('contratos.contratada.recurso.veiculo.index', contrato.id), label: `Veiculos` },
          { route: '#', label: contrato.contratada }
        ]
          " />
        <Link class="btn btn-info" :href="route('contratos.contratada.recurso.veiculo.index', contrato.id)">
        <IconDoorExit class="me-2" />
        Voltar
        </Link>
      </div>
    </template>

    <Navbar :contrato="contrato">

      <template #body>

        <div class="card-header">
          <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs" role="tablist">
            <li class="nav-item" role="presentation">
              <a href="#dadosGerais" class="nav-link active" data-bs-toggle="tab" aria-selected="true" role="tab">Dados
                gerais</a>
            </li>
            <template v-if="veiculo.id">
              <li class="nav-item" role="presentation">
                <a href="#quilometragem" class="nav-link" data-bs-toggle="tab" aria-selected="false" role="tab"
                  tabindex="-1">Quilometragem</a>
              </li>
              <li class="nav-item" role="presentation">
                <a href="#fotosVeiculo" class="nav-link" data-bs-toggle="tab" aria-selected="false" role="tab"
                  tabindex="-1">Fotos do veiculo</a>
              </li>
            </template>
          </ul>
        </div>
        <div class="card-body">
          <div class="tab-content">
            <div class="tab-pane active show" id="dadosGerais" role="tabpanel">
              <TabDadosGerais :contrato="contrato" :codigos="codigos" :veiculo="veiculo" />
            </div>
            <div class="tab-pane" id="quilometragem" role="tabpanel">
              <TabQuilometragem :contrato="contrato" :codigos="codigos" :veiculo="veiculo" />
            </div>
            <div class="tab-pane" id="fotosVeiculo" role="tabpanel">
              <TabDocumentos :contrato="contrato" :codigos="codigos" :veiculo="veiculo" />
            </div>
          </div>
        </div>
      </template>
    </Navbar>

  </AuthenticatedLayout>
</template>
