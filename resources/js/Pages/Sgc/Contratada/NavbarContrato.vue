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
import { computed, watch, onMounted } from 'vue';

const props = defineProps({
  tipo: {
    type: Object,
    required: true,
  }
});

const contratoId = computed(() => props.tipo?.id || null);

onMounted(() => {
  console.log('Props tipo (onMounted):', props.tipo);
  console.log('Contrato ID (onMounted):', contratoId.value);
});

watch(() => props.tipo, (newTipo) => {
  console.log('Props tipo (watch):', newTipo);
  console.log('Contrato ID (watch):', contratoId.value);
});

const handleClick = (routeName) => {
  console.log(`Clicado em ${routeName} - Contrato ID:`, contratoId.value);
};
</script>

<template>
  <div class="card card-body space-y-3">
    <div class="d-flex">
      <div class="col-1">
        <ul class="navbar-nav">
          <!-- Relatório de Coordenação -->
          <NavLink
            @click="handleClick('Relatórios')"
            :route-name="'sgc.contratada.relatorios.index'"
            :param="contratoId"
            title="Relatório de Coordenação"
            :icon="IconClipboardList"
          />

          <!-- Cronograma Físico -->
          <NavLink
            @click="handleClick('Cronograma')"
            :route-name="'sgc.contratada.cronograma.index'"
            :param="contratoId"
            title="Cronograma Físico"
            :icon="IconCalendar"
          />

          <!-- Produtos -->
          <!-- <NavLink
            @click="handleClick('Produtos')"
            :route-name="'sgc.gestao.dashboard.index'"
            :param="contratoId"
            title="Produtos"
            :icon="IconClipboardData"
          /> -->

          <!-- Quantitativos -->
          <NavLink
            @click="handleClick('Quantitativos')"
            :route-name="'sgc.contratada.quantitativos.index'"
            :param="contratoId"
            title="Quantitativos"
            :icon="IconCalendar"
          />

          <!-- Ficha Contratual -->
          <NavLink
            @click="handleClick('Ficha')"
            :route-name="'sgc.contratada.ficha.index'"
            :param="contratoId"
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