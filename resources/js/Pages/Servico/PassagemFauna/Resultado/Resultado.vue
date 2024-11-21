<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import Navbar from "../Navbar.vue";
import {Head, Link, useForm} from "@inertiajs/vue3";
import TabRegistroIdentificado from "./Tabs/TabRegistroIdentificado.vue";
import TabRegsitroClasse from "./Tabs/TabRegsitroClasse.vue";
import TabRegistroCampanha from "./Tabs/TabRegistroCampanha.vue";
import TabRegistroTipo from "./Tabs/TabRegistroTipo.vue";
import TabRegistroPassagem from "./Tabs/TabRegistroPassagem.vue";
import TabRegistroEspecie from "./Tabs/TabRegistroEspecie.vue";
import TabRegistroOutraAnalise from "./Tabs/TabRegistroOutraAnalise.vue";

const props = defineProps({
    contrato: {type: Object},
    servico: {type: Object},
    resultado: {type: Object},
    chartDatas: {type: Object}
});

const form = useForm({
    id: null,
    id_resultado: null,
    form: null,
    registros_identificados: null,
    registros_classe: null,
    registros_campanha: null,
    registros_tipo: null,
    registros_periodo: null,
    registros_passagem: null,
    registros_km: null,
    registros_mes: null,
    registros_especie: null,
    formas_registros: null,
    graf_reg_classe: null,
    graf_reg_campanha: null,
    graf_reg_tipo: null,
    graf_reg_passagem: null,
    graf_reg_periodo: null,
    graf_formas_registros: null,
    graf_reg_km: null,
    graf_reg_mes: null,
    graf_reg_especie: null,
    ...props.resultado.analise
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
                <Link class="btn btn-dark"
                      :href="route('contratos.contratada.servicos.passagem_fauna.resultado.index', { contrato: contrato.id, servico:servico.id })">
                    Voltar
                </Link>
            </div>
        </template>

        <Navbar :contrato="contrato" :servico="servico">
            <template #body>
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a href="#registro-identificado" class="nav-link active" data-bs-toggle="tab"
                               aria-selected="true"
                               role="tab">Tabela com os registros identificados</a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a href="#registro-classe" class="nav-link" data-bs-toggle="tab" aria-selected="false"
                               role="tab"
                               tabindex="-1">Frequência de registros por classe</a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a href="#registro-campanha" class="nav-link" data-bs-toggle="tab" aria-selected="false"
                               role="tab"
                               tabindex="-1">Número de animais registrados por campanha</a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a href="#registro-tipo" class="nav-link" data-bs-toggle="tab" aria-selected="false"
                               role="tab"
                               tabindex="-1">Número de animais registrados por tipo</a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a href="#registro-passagem" class="nav-link" data-bs-toggle="tab" aria-selected="false"
                               role="tab"
                               tabindex="-1">Frequência de registros por passagem</a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a href="#registro-especie" class="nav-link" data-bs-toggle="tab" aria-selected="false"
                               role="tab"
                               tabindex="-1">Número de animais registrados por espécie</a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a href="#outra-analise" class="nav-link" data-bs-toggle="tab" aria-selected="false"
                               role="tab"
                               tabindex="-1">Outras análises</a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content">
                        <div class="tab-pane active show" id="registro-identificado" role="tabpanel">
                            <TabRegistroIdentificado :contrato="contrato" :servico="servico" :form="form"
                                                     :resultado="resultado"/>
                        </div>
                        <div class="tab-pane" id="registro-classe" role="tabpanel">
                            <TabRegsitroClasse :contrato="contrato" :servico="servico" :resultado="resultado"
                                               :form="form"
                                               :classes="chartDatas['classes']"/>
                        </div>
                        <div class="tab-pane" id="registro-campanha" role="tabpanel">
                            <TabRegistroCampanha :contrato="contrato" :servico="servico" :resultado="resultado"
                                                 :form="form" :campanhas="chartDatas['campanhas']"/>
                        </div>
                        <div class="tab-pane" id="registro-tipo" role="tabpanel">
                            <TabRegistroTipo :contrato="contrato" :servico="servico" :resultado="resultado"
                                             :form="form" :tipos="chartDatas['tipos']"/>
                        </div>
                        <div class="tab-pane" id="registro-passagem" role="tabpanel">
                            <TabRegistroPassagem :contrato="contrato" :servico="servico" :resultado="resultado"
                                                 :form="form" :passagens="chartDatas['passagens']"/>
                        </div>
                        <div class="tab-pane" id="registro-especie" role="tabpanel">
                            <TabRegistroEspecie :contrato="contrato" :servico="servico" :resultado="resultado"
                                                :form="form" :especies="chartDatas['especies']"/>
                        </div>
                        <div class="tab-pane" id="outra-analise" role="tabpanel">
                            <TabRegistroOutraAnalise :contrato="contrato" :servico="servico" :resultado="resultado"/>
                        </div>
                    </div>
                </div>
            </template>
        </Navbar>

    </AuthenticatedLayout>
</template>
