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
                                            <NavDropdownLink route-name="fiscal.configuracoes.passagem_fauna.index"
                                                :route-param="{ contrato: contrato.id }"
                                                active-on-route-prefix="fiscal.configuracoes.passagem_fauna*"
                                                title="Fauna - Passagem da Fauna" />
                                            <NavDropdownLink route-name="fiscal.configuracoes.atropelamento.index"
                                                :route-param="{ contrato: contrato.id }"
                                                active-on-route-prefix="fiscal.configuracoes.atropelamento*"
                                                title="Fauna - Atropelamento da Fauna" />
                                        </NavDropdown>

                                        <NavLink route-name="fiscal.rnc.index" :param="contrato.id" title="RNC"
                                            active-on-route-prefix="fiscal.rnc*" :icon="IconLayoutDashboard" />

                                        <NavDropdown prefix="fiscal.relatorio*" title="Relatórios"
                                            :icon="IconLayoutDashboard">

                                            <NavDropdownLink route-name="fiscal.relatorio.afugentamento.index"
                                                :route-param="{ contrato: contrato.id }"
                                                active-on-route-prefix="fiscal.relatorio.afugentamento*"
                                                title="Fauna - Afugentamento e resgate de fauna" />
                                            <NavDropdownLink route-name="fiscal.relatorio.pmqa.index"
                                                :route-param="{ contrato: contrato.id }"
                                                active-on-route-prefix="fiscal.relatorio.pmqa*"
                                                title="Programa de Monitoramento da Qualidade da Água" />
                                            <NavDropdownLink route-name="fiscal.relatorio.cont_ocorrencia.index"
                                                :route-param="{ contrato: contrato.id }"
                                                active-on-route-prefix="fiscal.relatorio.cont_ocorrencia*"
                                                title="Supervisão ambiental - Controle de ocorrência" />
                                            <NavDropdownLink route-name="fiscal.relatorio.supressao_vegetacao.index"
                                                :route-param="{ contrato: contrato.id }"
                                                active-on-route-prefix="fiscal.relatorio.supressao_vegetacao*"
                                                title="Flora - Supressão da vegetação" />
                                            <NavDropdownLink route-name="fiscal.relatorio.passagem_fauna.index"
                                                :route-param="{ contrato: contrato.id }"
                                                active-on-route-prefix="fiscal.relatorio.passagem_fauna*"
                                                title="Fauna - Passagem da Fauna" />
                                            <NavDropdownLink route-name="fiscal.relatorio.atropelamento.index"
                                                :route-param="{ contrato: contrato.id }"
                                                active-on-route-prefix="fiscal.relatorio.atropelamento*"
                                                title="Fauna - Atropelamento da Fauna" />
                                        </NavDropdown>

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
