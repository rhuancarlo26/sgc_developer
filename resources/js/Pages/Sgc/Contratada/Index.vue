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
import Navbar from "./Navbar.vue";

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
        
        <div class="card card-body space-y-3" style="width: 600px; height: 400px" >
          <div class="d-flex">
            <div class="col">


              
            </div>
          </div>
        </div>


      </template>
    </Navbar>

  </AuthenticatedLayout>
</template>
