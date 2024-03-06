<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import Navbar from "../Navbar.vue";
import { Head } from "@inertiajs/vue3";
import TabDadosContratuais from "./DadosContratuais/TabDadosContratuais.vue";
import { ref } from "vue";

const mapaTabDadosContratuais = ref();

const props = defineProps({
  contrato: Object
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
            <a @click="visualizarMapa()" href="#dadosContratuais" class="nav-link active" data-bs-toggle="tab"
              aria-selected="true" role="tab">Dados contratuais</a>
          </li>
          <li class="nav-item" role="presentation">
            <a href="#tabs-profile-5" class="nav-link" data-bs-toggle="tab" aria-selected="false" role="tab"
              tabindex="-1">Profile</a>
          </li>
          <li class="nav-item" role="presentation">
            <a href="#tabs-activity-5" class="nav-link" data-bs-toggle="tab" aria-selected="false" tabindex="-1"
              role="tab">Activity</a>
          </li>
          <li class="nav-item" role="presentation">
            <a href="#tabs-activity-5" class="nav-link" data-bs-toggle="tab" aria-selected="false" tabindex="-1"
              role="tab">Activity</a>
          </li>
          <li class="nav-item" role="presentation">
            <a href="#tabs-activity-5" class="nav-link" data-bs-toggle="tab" aria-selected="false" tabindex="-1"
              role="tab">Activity</a>
          </li>
          <li class="nav-item" role="presentation">
            <a href="#tabs-activity-5" class="nav-link" data-bs-toggle="tab" aria-selected="false" tabindex="-1"
              role="tab">Activity</a>
          </li>
        </ul>

        <div class="tab-content">
          <div class="tab-pane active show" id="dadosContratuais" role="tabpanel">
            <TabDadosContratuais :contrato="contrato" ref="mapaTabDadosContratuais" />
          </div>
          <div class="tab-pane" id="tabs-profile-5" role="tabpanel">
            <h4>Profile tab</h4>
            <div>Fringilla egestas nunc quis tellus diam rhoncus ultricies tristique enim at diam, sem nunc amet,
              pellentesque id egestas velit sed</div>
          </div>
          <div class="tab-pane" id="tabs-activity-5" role="tabpanel">
            <h4>Activity tab</h4>
            <div>Donec ac vitae diam amet vel leo egestas consequat rhoncus in luctus amet, facilisi sit mauris accumsan
              nibh habitant senectus</div>
          </div>
          <div class="tab-pane" id="tabs-activity-5" role="tabpanel">
            <h4>Activity tab</h4>
            <div>Donec ac vitae diam amet vel leo egestas consequat rhoncus in luctus amet, facilisi sit mauris accumsan
              nibh habitant senectus</div>
          </div>
          <div class="tab-pane" id="tabs-activity-5" role="tabpanel">
            <h4>Activity tab</h4>
            <div>Donec ac vitae diam amet vel leo egestas consequat rhoncus in luctus amet, facilisi sit mauris accumsan
              nibh habitant senectus</div>
          </div>
          <div class="tab-pane" id="tabs-activity-5" role="tabpanel">
            <h4>Activity tab</h4>
            <div>Donec ac vitae diam amet vel leo egestas consequat rhoncus in luctus amet, facilisi sit mauris accumsan
              nibh habitant senectus</div>
          </div>
        </div>
      </template>
    </Navbar>

  </AuthenticatedLayout>
</template>
