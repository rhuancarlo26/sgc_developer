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
                                            <NavDropdown
                                                prefix="contratos.contratada.servicos.passagem_fauna.configuracao*"
                                                title="Configuração" :icon="IconLayoutDashboard">
                                                <NavDropdownLink
                                                    route-name="contratos.contratada.servicos.passagem_fauna.configuracao.vincular_abio.index"
                                                    active-on-route-prefix="contratos.contratada.servicos.passagem_fauna.configuracao.vincular_abio*"
                                                    :route-param="{ contrato: contrato.id, servico: servico.id }"
                                                    title="Vincular ABIO"/>

                                                <NavDropdownLink
                                                    route-name="contratos.contratada.servicos.passagem_fauna.configuracao.passagem_fauna.index"
                                                    active-on-route-prefix="contratos.contratada.servicos.passagem_fauna.configuracao.passagem_fauna*"
                                                    :route-param="{ contrato: contrato.id, servico: servico.id }"
                                                    title="Passagem fauna"/>
                                            </NavDropdown>
                                            <template
                                                v-if="servico.parecer_passagem_fauna || servico.parecer_passagem_fauna?.fk_status === 3">
                                                <NavDropdown
                                                    prefix="contratos.contratada.servicos.passagem_fauna.execucao*"
                                                    title="Execução" :icon="IconLayoutDashboard">
                                                    <NavDropdownLink
                                                        route-name="contratos.contratada.servicos.passagem_fauna.execucao.campanha.index"
                                                        active-on-route-prefix="contratos.contratada.servicos.passagem_fauna.execucao.campanha*"
                                                        :route-param="{ contrato: contrato.id, servico: servico.id }"
                                                        title="Campanha"/>
                                                    <NavDropdownLink
                                                        route-name="contratos.contratada.servicos.passagem_fauna.execucao.registro.index"
                                                        active-on-route-prefix="contratos.contratada.servicos.passagem_fauna.execucao.registro*"
                                                        :route-param="{ contrato: contrato.id, servico: servico.id }"
                                                        title="Registros"/>
                                                </NavDropdown>

                                                <NavLink
                                                    route-name="contratos.contratada.servicos.passagem_fauna.resultado.index"
                                                    active-on-route-prefix="contratos.contratada.servicos.passagem_fauna.resultado*"
                                                    :param="{ contrato: contrato.id, servico: servico.id }"
                                                    title="Resultado"
                                                    :icon="IconLayoutDashboard"/>

                                                <NavLink
                                                    route-name="contratos.contratada.servicos.passagem_fauna.relatorio.index"
                                                    active-on-route-prefix="contratos.contratada.servicos.passagem_fauna.relatorio*"
                                                    :param="{ contrato: contrato.id, servico: servico.id }"
                                                    title="Relatório"
                                                    :icon="IconLayoutDashboard"/>
                                            </template>
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
