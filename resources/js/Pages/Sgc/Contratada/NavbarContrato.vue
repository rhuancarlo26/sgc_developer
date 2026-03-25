<script setup>
import NavExpandable from '@/Components/NavExpandable.vue';
import NavLink from '@/Components/NavLink.vue';
import {
  IconClipboardList,
  IconCalendar,
  IconNotes,
  IconDeviceAnalytics,
  IconPlane,
  IconLayoutDashboard
} from "@tabler/icons-vue";
import { computed, watch, onMounted } from 'vue';

const props = defineProps({
  tipo: {
    type: Object,
    required: true,
  }
});

const contratoId = computed(() => props.tipo?.id || null);

const handleClick = (routeName) => {
  console.log(`Clicado em ${routeName} - Contrato ID:`, contratoId.value);
};
</script>

<template>
  <div class="card card-body space-y-3">
    <div class="d-flex">
      <div class="col-1">
        <ul class="navbar-nav">
          <NavLink
            @click="handleClick('Relatórios')"
            :route-name="'sgc.contratada.relatorios.index'"
            :param="contratoId"
            title="Relatório de Coordenação"
            :icon="IconClipboardList"
          />

          <NavLink
            @click="handleClick('Produtos')"
            :route-name="'sgc.contratada.produtos.index'"
            :param="[contratoId, 'fauna']"
            title="Produtos"
            :icon="IconLayoutDashboard"
          />

          <NavLink
            @click="handleClick('Cronograma')"
            :route-name="'sgc.contratada.cronograma.index'"
            :param="contratoId"
            title="Cronograma Físico"
            :icon="IconCalendar"
          />

          <NavLink
            @click="handleClick('Quantitativos')"
            :route-name="'sgc.contratada.quantitativos.index'"
            :param="contratoId"
            title="Quantitativos"
            :icon="IconDeviceAnalytics"
          />

          <NavLink
            @click="handleClick('DAV')"
            :route-name="'sgc.gestao.listagemDav'"
            :param="contratoId"
            title="DAV"
            :icon="IconPlane"
          />

          <NavLink
            @click="handleClick('Ficha')"
            :route-name="'sgc.contratada.ficha.index'"
            :param="contratoId"
            title="Ficha Contratual"
            :icon="IconNotes"
          />
        </ul>
      </div>
      <div class="col-11">
        <slot name="body" />
      </div>
    </div>
  </div>
</template>


<style scoped>
.navbar-nav {
  padding: 0;
  margin: 0;
  list-style: none;
}

.nav-item {
  width: 100%;
}

.nav-link {
  display: flex;
  align-items: center;
  color: #182433;
  padding: 0.5rem 0rem;
  cursor: pointer;
}

.nav-sublist .nav-link {
  padding-left: 2rem;
}
</style>
