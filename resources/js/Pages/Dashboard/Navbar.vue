<script setup>
import NavLink from '@/Components/NavLink.vue';
import { IconLayoutDashboard } from '@tabler/icons-vue';

</script>
<template>
  <header class="navbar-expand-md mb-4">
    <div class="collapse navbar-collapse" id="navbar-menu">
      <div class="navbar">
        <div class="container-xl">
          <div class="row flex-fill align-items-center">
            <div class="col">
              <ul class="navbar-nav">
                <NavLink route-name="dashboard.pmqa" title="PMQA" :icon="IconLayoutDashboard" />
              </ul>
            </div>
            <div class="col">
              <ul class="navbar-nav">
                <NavLink route-name="dashboard.afugentamentoFauna" title="Afugentamento de fauna" :icon="IconLayoutDashboard" />
              </ul>
            </div>
            <div class="col">
              <ul class="navbar-nav">
                <NavLink route-name="dashboard.supervisaoAmbiental" title="Supervisao Ambiental" :icon="IconLayoutDashboard" />
              </ul>
            </div>
            <div class="col">
              <ul class="navbar-nav">
                <NavLink route-name="dashboard.supressaoVegetal" title="Supressao Vegetal" :icon="IconLayoutDashboard" />
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  </header>
</template>