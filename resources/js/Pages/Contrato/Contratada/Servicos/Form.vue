<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import Navbar from "../Navbar.vue";
import { IconDoorExit } from "@tabler/icons-vue";
import { IconDeviceFloppy } from "@tabler/icons-vue";
import { ref, watch } from "vue";
import TabDadosGerais from "./TabDadosGerais.vue";
import TabVincularRecursos from "./TabVincularRecursos.vue";
import TabVincularCondicionantes from "./TabVincularCondicionantes.vue";

const props = defineProps({
  contrato: Object,
  servico: Object,
  tipos: Array,
  temas: Array,
  licencasLi: Array,
  rhs: Array,
  veiculos: Array,
  equipamentos: Array
})

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
        <div class="container-buttons">
          <Link class="btn btn-info me-2" :href="route('contratos.contratada.servicos.index', contrato.id)">
          <IconDoorExit class="me-2" />
          Voltar
          </Link>
        </div>
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
            <template v-if="servico.id">
              <li class="nav-item" role="presentation">
                <a href="#vincularRecursos" class="nav-link" data-bs-toggle="tab" aria-selected="false" role="tab"
                  tabindex="-1">Vincular recursos</a>
              </li>
              <li class="nav-item" role="presentation">
                <a href="#vincularCondicionantes" class="nav-link" data-bs-toggle="tab" aria-selected="false" role="tab"
                  tabindex="-1">Vincular condicionantes</a>
              </li>
            </template>
          </ul>
        </div>
        <div class="card-body">
          <div class="tab-content">
            <div class="tab-pane active show" id="dadosGerais" role="tabpanel">
              <TabDadosGerais :contrato="contrato" :servico="servico" :temas="temas" :tipos="tipos" />
            </div>

            <div class="tab-pane" id="vincularRecursos" role="tabpanel">
              <TabVincularRecursos :servico="servico" :rhs="rhs" :veiculos="veiculos" :equipamentos="equipamentos" />
            </div>
            <div class="tab-pane" id="vincularCondicionantes" role="tabpanel">
              <TabVincularCondicionantes :servico="servico" :licencasLi="licencasLi" />
            </div>
          </div>
        </div>

      </template>
    </Navbar>

  </AuthenticatedLayout>
</template>
