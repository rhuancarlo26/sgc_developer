<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import Table from '@/Components/Table.vue';
import Breadcrumb from "@/Components/Breadcrumb.vue";
import ModelSearchForm from "@/Components/ModelSearchForm.vue";
import Modal from "@/Components/Modal.vue";
import { ref } from "vue";
import Map from "@/Components/Map.vue";
import { onMounted } from "vue";
import axios from "axios";
import { computed } from "vue";
import { dateTimeFormat } from "@/Utils/DateTimeUtils";
import { IconSettings } from "@tabler/icons-vue";
import { IconDots } from "@tabler/icons-vue";
import LinkConfirmation from "@/Components/LinkConfirmation.vue";
import Navbar from "../Navbar.vue";

const props = defineProps({
  contrato: Object
})

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
            <a href="#tabs-home-5" class="nav-link active" data-bs-toggle="tab" aria-selected="true" role="tab">Dados
              Contratuais</a>
          </li>
          <li class="nav-item" role="presentation">
            <a href="#tabs-profile-5" class="nav-link" data-bs-toggle="tab" aria-selected="false" tabindex="-1"
              role="tab">Introdução</a>
          </li>
          <li class="nav-item" role="presentation">
            <a href="#tabs-activity-5" class="nav-link" data-bs-toggle="tab" aria-selected="false" tabindex="-1"
              role="tab">Empreendimento</a>
          </li>
          <li class="nav-item" role="presentation">
            <a href="#tabs-activity-5" class="nav-link" data-bs-toggle="tab" aria-selected="false" tabindex="-1"
              role="tab">Licenciamento</a>
          </li>
          <li class="nav-item" role="presentation">
            <a href="#tabs-activity-5" class="nav-link" data-bs-toggle="tab" aria-selected="false" tabindex="-1"
              role="tab">Hitórico</a>
          </li>
          <li class="nav-item" role="presentation">
            <a href="#tabs-activity-5" class="nav-link" data-bs-toggle="tab" aria-selected="false" tabindex="-1"
              role="tab">Anexos</a>
          </li>
        </ul>
      </template>
    </Navbar>

  </AuthenticatedLayout>
</template>
