<script setup>
import NavDropdownLink from '@/Components/NavDropdownLink.vue';
import NavDropdown from '@/Components/NavDropdown.vue';
import Navbar from '@/Pages/Contrato/Contratada/Navbar.vue';
import { IconLayoutDashboard } from '@tabler/icons-vue';

const porps = defineProps({
  contrato: { type: Object },
  servico: { type: Object }
});
</script>
<template>
  <Navbar :contrato="contrato">
    <template #body>
      <div class="card card-body p-0 space-y-3">
        <header class="navbar-expand-md">
          <div class="collapse navbar-collapse" id="navbar-menu">
            <div class="navbar">
              <div class="container-xl">
                <div class="row flex-fill align-items-center">
                  <div class="col">
                    <ul class="navbar-nav">
                      <NavDropdown prefix="contratos.contratada.servicos.pmqa.configuracao*" title="Configurações"
                        :icon="IconLayoutDashboard">

                        <NavDropdownLink route-name="contratos.contratada.servicos.pmqa.configuracao.ponto.index"
                          active-on-route-prefix="contratos.contratada.servicos.pmqa.configuracao.ponto*"
                          :route-param="{ contrato: contrato.id, servico: servico.id }" title="Pontos" />

                        <NavDropdownLink route-name="contratos.contratada.servicos.pmqa.configuracao.parametro.index"
                          active-on-route-prefix="contratos.contratada.servicos.pmqa.configuracao.parametro*"
                          :route-param="{ contrato: contrato.id, servico: servico.id }" title="Parâmetros" />
                      </NavDropdown>
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
  </Navbar>
</template>