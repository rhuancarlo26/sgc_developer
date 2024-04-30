<script setup>
import NavDropdown from '@/Components/NavDropdown.vue';
import NavDropdownLink from '@/Components/NavDropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import {
  IconLayoutDashboard
} from "@tabler/icons-vue";

const props = defineProps({
  contrato: Object
})
</script>

<template>
  <div class="card card-body space-y-3">
    <div class="d-flex">
      <div class="col">
        <ul class="navbar-nav">
          <!-- Dashboard -->
          <navLink route-name="sgc.contratada.dados_gerais.index" :param="contrato.id" title="Dados Gerais"
            :icon="IconLayoutDashboard" />
            
          <!-- TESTE -->
          <navLink route-name="sgc.contratada.dados_gerais.index" :param="contrato.id" title="Relatório"
            :icon="IconLayoutDashboard" />

          <navLink route-name="sgc.contratada.dados_gerais.index" :param="contrato.id" title="Cronograma Físico"
            :icon="IconLayoutDashboard" />

          <navLink route-name="sgc.contratada.dados_gerais.index" :param="contrato.id" title="Estudos"
            :icon="IconLayoutDashboard" />
        </ul>
      </div>
      <div class="col-11">
        <slot name="body" />
      </div>
    </div>
  </div>
</template>
