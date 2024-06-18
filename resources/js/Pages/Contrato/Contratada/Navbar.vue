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
  <div class="card card-body p-0 space-y-3">
    <header class="navbar-expand-md">
      <div class="collapse navbar-collapse" id="navbar-menu">
        <div class="navbar">
          <div class="container-xl">
            <div class="row flex-fill align-items-center">
              <div class="col">
                <ul class="navbar-nav">

                  <NavLink route-name="contratos.contratada.dados_gerais.index" :param="contrato.id"
                    title="Dados Gerais" :icon="IconLayoutDashboard" />


                  <NavDropdown prefix="contratos.contratada.recurso*" title="Recuros" :icon="IconLayoutDashboard">

                    <NavDropdownLink route-name="contratos.contratada.recurso.rh.index" param-name="contrato"
                      active-on-route-prefix="contratos.contratada.recurso.rh.*" :route-param="contrato.id"
                      title="RH" />


                    <NavDropdownLink route-name="contratos.contratada.recurso.equipamento.index" param-name="contrato"
                      active-on-route-prefix="contratos.contratada.recurso.equipamento.*" :route-param="contrato.id"
                      title="Equipamentos" />


                    <NavDropdownLink route-name="contratos.contratada.recurso.veiculo.index" param-name="contrato"
                      active-on-route-prefix="contratos.contratada.recurso.veiculo.*" :route-param="contrato.id"
                      title="Veículos" />
                  </NavDropdown>

                  <NavLink route-name="contratos.contratada.servicos.index" :param="contrato.id"
                    active-on-route-prefix="contratos.contratada.servicos*" title="Serviços"
                    :icon="IconLayoutDashboard" />

                  <NavLink route-name="contratos.contratada.dados_gerais.index" :param="contrato.id" title="Cronograma"
                    :icon="IconLayoutDashboard" />
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>
    </header>
    <div class="mt-2 card card-body">
      <slot name="body" />
    </div>
  </div>
</template>
