<script setup>
import NavDropdownLink from '@/Components/NavDropdownLink.vue';
import NavDropdown from '@/Components/NavDropdown.vue';
import Navbar from '@/Pages/Contrato/Contratada/Navbar.vue';
import { IconLayoutDashboard } from '@tabler/icons-vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import NavLink from '@/Components/NavLink.vue';

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
                                            <!-- Configuração -->
                                            <NavDropdown prefix="contratos.contratada.servicos.controledeocorrencias.configuracoes*"
                                                title="Configuração" :icon="IconLayoutDashboard">
                                                <!-- Empreendimento -->
                                                <NavDropdownLink
                                                    route-name="contratos.contratada.servicos.controledeocorrencias.configuracoes.empreendimento.index"
                                                    active-on-route-prefix="contratos.contratada.servicos.controledeocorrencias.configuracoes.empreendimento*"
                                                    :route-param="{ contrato: contrato.id, servico: servico.id }"
                                                    title="Empreendimento" />
                                                <!-- Lote de Obra -->
                                                <NavDropdownLink
                                                    route-name="contratos.contratada.servicos.controledeocorrencias.configuracoes.lotedeobra.index"
                                                    active-on-route-prefix="contratos.contratada.servicos.controledeocorrencias.configuracoes.lotedeobra*"
                                                    :route-param="{ contrato: contrato.id, servico: servico.id }"
                                                    title="Lote de Obra" />
                                            </NavDropdown>
                                            
                                            <!-- Execução -->
                                            <NavDropdown prefix="contratos.contratada.servicos.controledeocorrencias.execucao*"
                                                title="Execução" :icon="IconLayoutDashboard">
                                                <!-- Ocorrência -->
                                                <NavDropdownLink
                                                    route-name="contratos.contratada.servicos.controledeocorrencias.execucao.ocorrencia.index"
                                                    active-on-route-prefix="contratos.contratada.servicos.controledeocorrencias.execucao.ocorrencia*"
                                                    :route-param="{ contrato: contrato.id, servico: servico.id }"
                                                    title="Ocorrência" />
                                                <!-- Controle RNC -->
                                                <NavDropdownLink
                                                    route-name="contratos.contratada.servicos.controledeocorrencias.execucao.controlernc.index"
                                                    active-on-route-prefix="contratos.contratada.servicos.controledeocorrencias.execucao.controlernc*"
                                                    :route-param="{ contrato: contrato.id, servico: servico.id }"
                                                    title="Controle RNC" />
                                                <!-- ACA -->
                                                <NavDropdownLink
                                                    route-name="contratos.contratada.servicos.controledeocorrencias.execucao.aca.index"
                                                    active-on-route-prefix="contratos.contratada.servicos.controledeocorrencias.execucao.aca*"
                                                    :route-param="{ contrato: contrato.id, servico: servico.id }"
                                                    title="ACA" />
                                            </NavDropdown>

                                            <!-- Resultado -->
                                            <NavLink route-name="contratos.contratada.servicos.controledeocorrencias.resultado.index"
                                                active-on-route-prefix="contratos.contratada.servicos.controledeocorrencias.resultado*"
                                                :param="{ contrato: contrato.id, servico: servico.id }" title="Resultado"
                                                :icon="IconLayoutDashboard" />

                                            <!-- Relatórios -->
                                            <NavLink route-name="contratos.contratada.servicos.controledeocorrencias.relatorios.index"
                                                active-on-route-prefix="contratos.contratada.servicos.controledeocorrencias.relatorios*"
                                                :param="{ contrato: contrato.id, servico: servico.id }" title="Relatórios"
                                                :icon="IconLayoutDashboard" />

                                            <!-- Pareceres -->
                                            <NavLink route-name="contratos.contratada.servicos.controledeocorrencias.pareceres.index"
                                                active-on-route-prefix="contratos.contratada.servicos.controledeocorrencias.pareceres*"
                                                :param="{ contrato: contrato.id, servico: servico.id }" title="Pareceres"
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