<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import Navbar from "../../Navbar.vue";
import {Head, Link} from "@inertiajs/vue3";
import ModelSearchFormAllColumns from "@/Components/ModelSearchFormAllColumns.vue";
import Table from "@/Components/Table.vue";
import NavButton from "@/Components/NavButton.vue";
import {ref} from "vue";
import {IconEye} from "@tabler/icons-vue";
import {IconPlus} from "@tabler/icons-vue";
import {IconPencil} from "@tabler/icons-vue";
import {IconDeviceFloppy} from "@tabler/icons-vue";
import NavLink from "@/Components/NavLink.vue";
import {IconMap} from "@tabler/icons-vue";
import TabLocal from "./TabLocal.vue";
import TabDescricao from "./TabDescricao.vue";
import TabAcao from "./TabAcao.vue";
import TabNorma from "./TabNorma.vue";
import TabRegistro from "./TabRegistro.vue";
import TabObservacao from "./TabObservacao.vue";

const props = defineProps({
    contrato: {type: Object},
    servico: {type: Object},
    ocorrencia: {type: Object},
    lotes: {type: Array},
    rodovias: {type: Array}
});

const voltarPagina = () => {
    window.history.back();
}

</script>
<template>

    <Head :title="`${contrato.contratada.slice(0, 10)}...`"/>

    <AuthenticatedLayout>

        <template #header>
            <div class="w-100 d-flex justify-content-between">
                <Breadcrumb class="align-self-center" :links="[
          { route: route('contratos.gestao.listagem', contrato.tipo_id), label: `Gestão de Contratos` },
          { route: '#', label: contrato.contratada }
        ]
          "/>
                <NavButton @click="voltarPagina()" type-button="dark"
                           title="Voltar"/>
            </div>
        </template>

        <Navbar :contrato="contrato" :servico="servico">
            <template #body>
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a href="#dados_local" class="nav-link active" data-bs-toggle="tab" aria-selected="true"
                               role="tab">Local</a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a href="#descricao" class="nav-link" data-bs-toggle="tab" aria-selected="false" role="tab"
                               tabindex="-1">Descrição</a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a href="#acao" class="nav-link" data-bs-toggle="tab" aria-selected="false" role="tab"
                               tabindex="-1">Ações</a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a href="#norma" class="nav-link" data-bs-toggle="tab" aria-selected="false" role="tab"
                               tabindex="-1">Norma / Fundamento legal</a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a href="#registro" class="nav-link" data-bs-toggle="tab" aria-selected="false" role="tab"
                               tabindex="-1">Registro fotográfico</a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a href="#observacao" class="nav-link" data-bs-toggle="tab" aria-selected="false" role="tab"
                               tabindex="-1">Observações</a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content">
                        <div class="tab-pane active show" id="dados_local" role="tabpanel">
                            <TabLocal :contrato="contrato" :servico="servico" :ocorrencia="ocorrencia" :lotes="lotes"
                                      :rodovias="rodovias"/>
                        </div>
                        <div class="tab-pane" id="descricao" role="tabpanel">
                            <TabDescricao :contrato="contrato" :servico="servico" :ocorrencia="ocorrencia"/>
                        </div>
                        <div class="tab-pane" id="acao" role="tabpanel">
                            <TabAcao :contrato="contrato" :servico="servico" :ocorrencia="ocorrencia"/>
                        </div>
                        <div class="tab-pane" id="norma" role="tabpanel">
                            <TabNorma :contrato="contrato" :servico="servico" :ocorrencia="ocorrencia"/>
                        </div>
                        <div class="tab-pane" id="registro" role="tabpanel">
                            <TabRegistro :contrato="contrato" :servico="servico" :ocorrencia="ocorrencia"/>
                        </div>
                        <div class="tab-pane" id="observacao" role="tabpanel">
                            <TabObservacao :contrato="contrato" :servico="servico" :ocorrencia="ocorrencia"/>
                        </div>
                    </div>
                </div>
            </template>
        </Navbar>

    </AuthenticatedLayout>
</template>
