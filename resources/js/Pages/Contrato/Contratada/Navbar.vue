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
      <div class="col-2">
        <ul class="navbar-nav">
          <!-- Dashboard -->
          <navLink route-name="contratos.contratada.dados_gerais.index" :param="contrato.id" title="Dados Gerais"
            :icon="IconLayoutDashboard" />

          <!-- Contratos -->
          <NavDropdown prefix="contratos.*" title="Recuros" :icon="IconLayoutDashboard">
            <!-- Gestão Ambiental -->
            <NavDropdownLink route-name="contratos.gestao.listagem" param-name="tipo" route-param="1"
              active-on-route-prefix="contratos.gestao.*" title="RH" />

            <!-- Equipamentos -->
            <NavDropdownLink route-name="contratos.contratada.recurso.equipamento.index" param-name="contrato"
              :route-param="contrato.id" title="Equipamentos" />

            <!-- Regularização Ambiental -->
            <NavDropdownLink route-name="contratos.gestao.listagem" param-name="tipo" route-param="3"
              active-on-route-prefix="contratos.gestao.*" title="Veículos" />
          </NavDropdown>

          <navLink route-name="contratos.contratada.dados_gerais.index" :param="contrato.id" title="Serviços"
            :icon="IconLayoutDashboard" />

          <navLink route-name="contratos.contratada.dados_gerais.index" :param="contrato.id" title="Cronograma"
            :icon="IconLayoutDashboard" />
        </ul>
      </div>
      <div class="col">
        <slot name="body" />
      </div>
    </div>
  </div>
</template>