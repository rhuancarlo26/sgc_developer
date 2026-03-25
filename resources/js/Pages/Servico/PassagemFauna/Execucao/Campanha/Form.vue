<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import Navbar from "../../Navbar.vue";
import {Head, Link} from "@inertiajs/vue3";
import TabDadosGerais from "./TabDadosGerais.vue";
import TabVincularABIO from "./TabVincularABIO.vue";
import TabVincularRH from "./TabVincularRH.vue";
import TabObservacao from "./TabObservacao.vue";

const props = defineProps({
    contrato: {type: Object},
    servico: {type: Object},
    campanha: {type: Object},
    abios: {type: Array}
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
                      :href="route('contratos.contratada.servicos.passagem_fauna.execucao.campanha.index', { contrato: contrato.id, servico: servico.id })">
                    Voltar
                </Link>
            </div>
        </template>

        <Navbar :contrato="contrato" :servico="servico">
            <template #body>
                <div class="card">
                    <div class="card-header">
                        <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <a href="#tab-dados-gerais" class="nav-link active" data-bs-toggle="tab"
                                   aria-selected="true"
                                   role="tab">Dados gerais</a>
                            </li>
                            <template v-if="campanha.id">
                                <li class="nav-item" role="presentation">
                                    <a href="#tab-vincular-abio" class="nav-link" data-bs-toggle="tab"
                                       aria-selected="false"
                                       tabindex="-1"
                                       role="tab">Vincular ABIO</a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a href="#tab-vincular-rh" class="nav-link" data-bs-toggle="tab"
                                       aria-selected="false"
                                       tabindex="-1"
                                       role="tab">Vincular profissionais</a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a href="#tab-observacao" class="nav-link" data-bs-toggle="tab"
                                       aria-selected="false"
                                       tabindex="-1"
                                       role="tab">Observações</a>
                                </li>
                            </template>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            <div class="tab-pane active show" id="tab-dados-gerais" role="tabpanel">
                                <TabDadosGerais :contrato="contrato" :servico="servico" :campanha="campanha"/>
                            </div>
                            <template v-if="campanha.id">
                                <div class="tab-pane" id="tab-vincular-abio" role="tabpanel">
                                    <TabVincularABIO :contrato="contrato" :servico="servico" :campanha="campanha"
                                                     :abios="abios"/>
                                </div>
                                <div class="tab-pane" id="tab-vincular-rh" role="tabpanel">
                                    <TabVincularRH :contrato="contrato" :servico="servico" :campanha="campanha"/>
                                </div>
                                <div class="tab-pane" id="tab-observacao" role="tabpanel">
                                    <TabObservacao :contrato="contrato" :servico="servico" :campanha="campanha"/>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </template>
        </Navbar>

    </AuthenticatedLayout>
</template>
