<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import Navbar from "../Navbar.vue";
import {Head} from "@inertiajs/vue3";
import TabRoaAtendido from "./TabRoaAtendido.vue";
import TabRoaAberto from "./TabRoaAberto.vue";
import TabRncAtendido from "./TabRncAtendido.vue";
import TabRncAberto from "./TabRncAberto.vue";
import TabOcorrenciaIntensidade from "./TabOcorrenciaIntensidade.vue";
import TabOcorrenciaLocal from "./TabOcorrenciaLocal.vue";
import TabOcorrenciaClassificacao from "./TabOcorrenciaClassificacao.vue";
import TabOcorrenciaLote from "./TabOcorrenciaLote.vue";
import TabAca from "./TabAca.vue";

const props = defineProps({
    contrato: {type: Object},
    servico: {type: Object},
    resultado: {type: Object},
    tabs: {type: Object}
});

</script>
<template>

    <Head :title="`${contrato.contratada.slice(0, 10)}...`"/>

    <AuthenticatedLayout>

        <template #header>
            <div class="w-100 d-flex justify-content-between">
                <Breadcrumb class="align-self-center" :links="[
          { route: route('contratos.gestao.listagem', contrato.tipo_contrato), label: `Gestão de Contratos` },
          { route: '#', label: contrato.contratada }
        ]
          "/>
            </div>
        </template>

        <Navbar :contrato="contrato" :servico="servico">
            <template #body>
                <div class="card">
                    <div class="card-header">
                        <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <a href="#roa_atendido" class="nav-link active" data-bs-toggle="tab"
                                   aria-selected="true"
                                   role="tab">Tabela com os ROA's atendidos</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a href="#roa_aberto" class="nav-link" data-bs-toggle="tab" aria-selected="false"
                                   tabindex="-1" role="tab">Tabela com os ROA's em aberto</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a href="#rnc_atendido" class="nav-link" data-bs-toggle="tab" aria-selected="false"
                                   tabindex="-1" role="tab">Tabela com os RNC's atendidos</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a href="#rnc_aberto" class="nav-link" data-bs-toggle="tab" aria-selected="false"
                                   tabindex="-1" role="tab">Tabela com os RNC's em aberto</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a href="#ocorrencia_intendidade" class="nav-link" data-bs-toggle="tab"
                                   aria-selected="false"
                                   tabindex="-1" role="tab">Frequência de ocorrências por intensidade</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a href="#ocorrencia_local" class="nav-link" data-bs-toggle="tab" aria-selected="false"
                                   tabindex="-1" role="tab">Número de ocorrências por local</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a href="#ocorrencia_classificacao" class="nav-link" data-bs-toggle="tab"
                                   aria-selected="false"
                                   tabindex="-1" role="tab">Número de ocorrências por classificação</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a href="#ocorrencia_lote" class="nav-link" data-bs-toggle="tab" aria-selected="false"
                                   tabindex="-1" role="tab">Número de ocorrência por lote</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a href="#aca_gerado" class="nav-link" data-bs-toggle="tab" aria-selected="false"
                                   tabindex="-1" role="tab">Tabela de ACA gerados</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a href="#outra_analise" class="nav-link" data-bs-toggle="tab" aria-selected="false"
                                   tabindex="-1" role="tab">Outras análises</a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            <div class="tab-pane active show" id="roa_atendido" role="tabpanel">
                                <TabRoaAtendido :roa_atendido="tabs['roa_atendido']"/>
                            </div>
                            <div class="tab-pane" id="roa_aberto" role="tabpanel">
                                <TabRoaAberto :roa_aberto="tabs['roa_aberto']"/>
                            </div>
                            <div class="tab-pane" id="rnc_atendido" role="tabpanel">
                                <TabRncAtendido :rnc_atendido="tabs['rnc_atendido']"/>
                            </div>
                            <div class="tab-pane" id="rnc_aberto" role="tabpanel">
                                <TabRncAberto :rnc_aberto="tabs['rnc_aberto']"/>
                            </div>
                            <div class="tab-pane" id="ocorrencia_intendidade" role="tabpanel">
                                <TabOcorrenciaIntensidade :intensidades="tabs['intensidades']"/>
                            </div>
                            <div class="tab-pane" id="ocorrencia_local" role="tabpanel">
                                <TabOcorrenciaLocal :locais="tabs['locais']"/>
                            </div>
                            <div class="tab-pane" id="ocorrencia_classificacao" role="tabpanel">
                                <TabOcorrenciaClassificacao :classificacoes="tabs['classificacoes']"/>
                            </div>
                            <div class="tab-pane" id="ocorrencia_lote" role="tabpanel">
                                <TabOcorrenciaLote :lotes="tabs['lotes']"/>
                            </div>
                            <div class="tab-pane" id="aca_gerado" role="tabpanel">
                                <TabAca :acas="tabs['acas']"/>
                            </div>
                            <div class="tab-pane" id="outra_analise" role="tabpanel">
                                10
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </Navbar>
    </AuthenticatedLayout>
</template>
