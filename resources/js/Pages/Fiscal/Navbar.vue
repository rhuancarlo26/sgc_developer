<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import NavLink from '@/Components/NavLink.vue';
import { IconLayoutDashboard } from "@tabler/icons-vue";
import NavDropdown from "@/Components/NavDropdown.vue";
import NavDropdownLink from "@/Components/NavDropdownLink.vue";

const props = defineProps({
    contrato: Object
})
</script>

<template>
    <AuthenticatedLayout>

        <template #header>
            <div class="w-100 d-flex justify-content-between">
                <Breadcrumb class="align-self-center" :links="[
                    { route: route('fiscal.index', { tipo: contrato.tipo_contrato }), label: '🡰 Fiscal', title: 'voltar' },
                    { route: '#', label: contrato.contratada }
                ]" />
            </div>
        </template>

        <div class="card card-body p-0 space-y-3">
            <header class="navbar-expand-md">
                <div class="collapse navbar-collapse" id="navbar-menu">
                    <div class="navbar">
                        <div class="container-xl">
                            <div class="row flex-fill align-items-center">
                                <div class="col">
                                    <ul class="navbar-nav">
                                        <NavLink route-name="fiscal.dados.servicos.index" :param="contrato.id"
                                            title="Serviços" :icon="IconLayoutDashboard" />

                                        <NavDropdown prefix="fiscal.dados.configuracoes*" title="Configurações"
                                            :icon="IconLayoutDashboard">
                                            <NavDropdownLink route-name="fiscal.configuracoes.afugentamento.index"
                                                :route-param="{ contrato: contrato.id }"
                                                active-on-route-prefix="fiscal.configuracoes.supressao.index"
                                                title="Fauna - Afugentamento e resgate de fauna" />
                                            <NavDropdownLink route-name="fiscal.configuracoes.supressao.index"
                                                :route-param="{ contrato: contrato.id }"
                                                active-on-route-prefix="fiscal.configuracoes.supressao.index"
                                                title="Flora - Supressão da vegetação" />
                                            <NavDropdownLink route-name="fiscal.configuracoes.pmqa.index"
                                                :route-param="{ contrato: contrato.id }"
                                                active-on-route-prefix="fiscal.configuracoes.pmqa.index"
                                                title="Programa de Monitoramento da Qualidade da Água" />
                                            <NavDropdownLink route-name="fiscal.configuracoes.ocorrencia.index"
                                                :route-param="{ contrato: contrato.id }"
                                                active-on-route-prefix="fiscal.configuracoes.ocorrencia.index"
                                                title="Supervisão ambiental - Controle de ocorrência" />
                                        </NavDropdown>

                                        <NavLink route-name="fiscal.rnc.index" :param="contrato.id" title="RNC"
                                            active-on-route-prefix="fiscal.rnc*" :icon="IconLayoutDashboard" />

                                        <NavLink route-name="contratos.contratada.dados_gerais.index"
                                            :param="contrato.id" title="Relatórios" :icon="IconLayoutDashboard" />

                                        <NavLink route-name="contratos.contratada.dados_gerais.index"
                                            :param="contrato.id" title="Acompanhamentos" :icon="IconLayoutDashboard" />

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
    </AuthenticatedLayout>
</template>
