<script setup>
import NavDropdownLink from '@/Components/NavDropdownLink.vue';
import NavDropdown from '@/Components/NavDropdown.vue';
import Navbar from '@/Pages/Contrato/Contratada/Navbar.vue';
import { IconLayoutDashboard } from '@tabler/icons-vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import NavLink from '@/Components/NavLink.vue';

defineProps({
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
                      <NavDropdown prefix="contratos.contratada.servicos.supressao-vegetacao.configuracao*" title="Configurações"
                        :icon="IconLayoutDashboard">

                        <NavDropdownLink route-name="contratos.contratada.servicos.supressao-vegetacao.configuracao.vincular-asv.index"
                          active-on-route-prefix="contratos.contratada.servicos.supressao-vegetacao.configuracao.vincular-asv*"
                          :route-param="{ contrato: contrato.id, servico: servico.id }" title="Vincular ASV" />

                        <NavDropdownLink route-name="contratos.contratada.servicos.supressao-vegetacao.configuracao.plano-supressao.index"
                          active-on-route-prefix="contratos.contratada.servicos.supressao-vegetacao.configuracao.plano-supressao*"
                          :route-param="{ contrato: contrato.id, servico: servico.id }" title="Plano de Supressão" />
                        <NavDropdownLink
                          route-name="contratos.contratada.servicos.supressao-vegetacao.configuracao.patio-estocagem.index"
                          active-on-route-prefix="contratos.contratada.servicos.supressao-vegetacao.configuracao.patio-estocagem*"
                          :route-param="{ contrato: contrato.id, servico: servico.id }" title="Patio estocagem" />
                      </NavDropdown>

                        <NavDropdown prefix="contratos.contratada.servicos.supressao-vegetacao.execucao*" title="Execução"
                                     :icon="IconLayoutDashboard">

                            <NavDropdownLink route-name="contratos.contratada.servicos.supressao-vegetacao.execucao.supressao.index"
                              active-on-route-prefix="contratos.contratada.servicos.supressao-vegetacao.execucao.supressao*"
                              :route-param="{ contrato: contrato.id, servico: servico.id }" title="Supressão" />

                            <NavDropdownLink route-name="contratos.contratada.servicos.supressao-vegetacao.execucao.pilhas.index"
                              active-on-route-prefix="contratos.contratada.servicos.supressao-vegetacao.execucao.pilhas*"
                              :route-param="{ contrato: contrato.id, servico: servico.id }" title="Pilhas" />

                            <NavDropdownLink route-name="contratos.contratada.servicos.supressao-vegetacao.execucao.destinacao.index"
                              active-on-route-prefix="contratos.contratada.servicos.supressao-vegetacao.execucao.destinacao*"
                              :route-param="{ contrato: contrato.id, servico: servico.id }" title="Destinação" />

                        </NavDropdown>

                      <NavLink route-name="contratos.contratada.servicos.pmqa.execucao.index"
                        active-on-route-prefix="contratos.contratada.servicos.pmqa.execucao*"
                        :param="{ contrato: contrato.id, servico: servico.id }" title="Execução"
                        :icon="IconLayoutDashboard" />

                      <NavLink route-name="contratos.contratada.servicos.pmqa.resultado.index"
                        active-on-route-prefix="contratos.contratada.servicos.pmqa.resultado*"
                        :param="{ contrato: contrato.id, servico: servico.id }" title="Resultado"
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
  </Navbar>
</template>
