<script setup>
import NavDropdownLink from '@/Components/NavDropdownLink.vue';
import NavDropdown from '@/Components/NavDropdown.vue';
import Navbar from '@/Pages/Contrato/Contratada/Navbar.vue';
import NavLink from '@/Components/NavLink.vue';
import { IconLayoutDashboard } from '@tabler/icons-vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';

const porps = defineProps({
  contrato: { type: Object },
  servico: { type: Object }
});
</script>
<template>
  <Navbar :contrato="contrato">
    <template #body>
      <div class="mb-4">
        <Breadcrumb
          :links="[{ route: route('contratos.contratada.servicos.index', { contrato: contrato.id, servico: servico.id }), label: 'Serviços' }, { route: '#', label: servico?.tipo?.nome }]" />
      </div>
      <div class="card card-body p-0 space-y-3">
        <header class="navbar-expand-md">
          <div class="collapse navbar-collapse" id="navbar-menu">
            <div class="navbar">
              <div class="container-xl">
                <div class="row flex-fill align-items-center">
                  <div class="col">
                    <ul class="navbar-nav">
                      <!-- Configurações -->
                      <NavDropdown prefix="contratos.contratada.servicos.monitora_fauna.configuracoes*"
                        title="Configurações" :icon="IconLayoutDashboard">
                        <!-- Vincular ABIO -->
                        <NavDropdownLink
                          route-name="contratos.contratada.servicos.monitora_fauna.configuracoes.vincular_abio.index"
                          active-on-route-prefix="contratos.contratada.servicos.monitora_fauna.configuracoes.vincular_abio*"
                          :route-param="{ contrato: contrato.id, servico: servico.id }" title="Vincular ABIO" />
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