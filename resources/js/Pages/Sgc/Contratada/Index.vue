<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import Navbar from "./Navbar.vue";

const props = defineProps({
  contrato: Object,
  comentarios: Object
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

              TESTE

            </div>
          </div>
        </div>


      </template>

    </Navbar>

  </AuthenticatedLayout>
</template>
