<script setup>
import NavDropdown from '@/Components/NavDropdown.vue';
import NavDropdownLink from '@/Components/NavDropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import {
  IconTimeline,
  IconClipboardData,
  IconClipboardList,
  IconCalendar,
  IconPlane,
} from "@tabler/icons-vue";

const props = defineProps({
  tipo: {
    type: Object,
    default: () => ({ id: 2 }) // Define um valor padrão para evitar erros
  }
});
</script>

<template>
  <div class="card card-body space-y-3">
    <div class="d-flex">
      <div class="col-1">
        <ul class="navbar-nav">
          <!-- Relatório de Coordenação -->
          <NavLink
            :route-name="'sgc.contratada.relatorios.index'"
            :param="tipo.id"
            title="Relatório de Coordenação"
            :icon="IconClipboardList"
          />

          <!-- Cronograma Físico -->
          <NavLink
            :route-name="'sgc.contratada.cronograma.index'"
            :param="tipo.id"
            title="Cronograma Físico"
            :icon="IconCalendar"
          />

          <!-- Produtos -->
          <NavLink
            :route-name="'sgc.gestao.dashboard.index'"
            :param="2"
            title="Produtos"
            :icon="IconClipboardData"
          />

          <!-- Quantitativos -->
          <NavLink
            :route-name="'sgc.contratada.cronograma.index'"
            :param="tipo.id"
            title="Quantitativos"
            :icon="IconCalendar"
          />

          <!-- Ficha Contratual -->
          <NavLink
            :route-name="'sgc.contratada.ficha.index'"
            :param="tipo.id"
            title="Ficha Contratual"
            :icon="IconCalendar"
          />

        </ul>
      </div>
      <div class="col-11">
        <slot name="body" />
      </div>
    </div>
  </div>
</template>