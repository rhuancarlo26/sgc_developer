<script setup>
import NavDropdownLink from '@/Components/NavDropdownLink.vue';
import NavDropdown from '@/Components/NavDropdown.vue';
import Navbar from '@/Pages/Contrato/Contratada/Navbar.vue';
import {IconLayoutDashboard} from '@tabler/icons-vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import NavLink from '@/Components/NavLink.vue';

const porps = defineProps({
    contrato: {type: Object},
    servico: {type: Object}
});
</script>
<template>
    <Navbar :contrato="contrato">
        <template #body>
            <div class="mb-4">
                <Breadcrumb
                    :links="[{ route: route('contratos.contratada.servicos.index', { contrato: contrato.id, servico: servico.id }), label: 'Serviços' }, { route: '#', label: servico?.tipo?.nome }]"/>
            </div>
            <div class="card card-body p-0 space-y-3">
                <header class="navbar-expand-md">
                    <div class="collapse navbar-collapse" id="navbar-menu">
                        <div class="navbar">
                            <div class="container-xl">
                                <div class="row flex-fill align-items-center">
                                    <div class="col">
                                        <ul class="navbar-nav">
                                            <NavDropdown prefix="contratos.contratada.servicos.pmqa.configuracao*"
                                                         title="Configurações"
                                                         :icon="IconLayoutDashboard">
                                                <NavDropdownLink
                                                    route-name="contratos.contratada.servicos.cont_ocorrencia.configuracao.empreendimento.index"
                                                    :route-param="{ contrato: contrato.id, servico: servico.id }"
                                                    title="Empreendimento"/>
                                                <NavDropdownLink
                                                    route-name="contratos.contratada.servicos.cont_ocorrencia.configuracao.lote_obra.index"
                                                    :route-param="{ contrato: contrato.id, servico: servico.id }"
                                                    title="Lote de obra"/>
                                            </NavDropdown>

                                            <NavDropdown
                                                prefix="contratos.contratada.servicos.cont_ocorrencia.execucao.ocorrencia*"
                                                title="Execução" :icon="IconLayoutDashboard">
                                                <NavDropdownLink
                                                    route-name="contratos.contratada.servicos.cont_ocorrencia.execucao.ocorrencia.index"
                                                    :route-param="{ contrato: contrato.id, servico: servico.id }"
                                                    title="Ocorrência"/>
                                                <NavDropdownLink
                                                    route-name="contratos.contratada.servicos.cont_ocorrencia.execucao.controle_rnc.index"
                                                    :route-param="{ contrato: contrato.id, servico: servico.id }"
                                                    title="Controle RNC"/>
                                                <NavDropdownLink
                                                    route-name="contratos.contratada.servicos.cont_ocorrencia.execucao.aca.index"
                                                    :route-param="{ contrato: contrato.id, servico: servico.id }"
                                                    title="ACA"/>
                                            </NavDropdown>

                                            <NavLink
                                                route-name="contratos.contratada.servicos.cont_ocorrencia.resultado.index"
                                                active-on-route-prefix="contratos.contratada.servicos.cont_ocorrencia.resultado*"
                                                :param="{ contrato: contrato.id, servico: servico.id }"
                                                title="Resultado"
                                                :icon="IconLayoutDashboard"/>

                                            <NavLink
                                                route-name="contratos.contratada.servicos.cont_ocorrencia.relatorio.index"
                                                active-on-route-prefix="contratos.contratada.servicos.cont_ocorrencia.relatorio*"
                                                :param="{ contrato: contrato.id, servico: servico.id }"
                                                title="Relatorio"
                                                :icon="IconLayoutDashboard"/>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </header>
                <div class="mt-2 card card-body">
                    <slot name="body"/>
                </div>
            </div>
        </template>
    </Navbar>
</template>
