<script setup>
import NavDropdown from '@/Components/NavDropdown.vue';
import NavDropdownLink from '@/Components/NavDropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import {
  IconTimeline, IconClipboardData, IconClipboardList
} from "@tabler/icons-vue";

const props = defineProps({
  tipo: {
    type: Object,
    default: () => ({ id: 2 })  // Define um valor padrão para evitar erros
  }
})
</script>

<template>
  <div class="card card-body space-y-3">
    <div class="d-flex">
      <div class="col-1">
        <ul class="navbar-nav">

          <!-- Contratos -->
          <navLink route-name="sgc.gestao.listagem"
          :param="2" title="Contratos" :icon="IconClipboardList" />

          <!-- <navLink route-name="sgc.contratada.relatorios.index"
          :param="tipo.id" title="Relatorio de coordenação" /> -->
            
          <navLink route-name="sgc.gestao.dashboard.index" :param="2" title="Painel Gerencial"
            :icon="IconTimeline" />
        </ul>
      </div>
      <div class="col-11">
        <slot name="body" />
      </div>
    </div>
  </div>
</template>
