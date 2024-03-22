<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import Navbar from "../Navbar.vue";
// import { Head } from "@inertiajs/vue3";
import TabDadosContratuais from "../DadosGerais/DadosContratuais/TabDadosContratuais.vue";
import TabServicos from "./Servicos/TabServicos.vue";
import { ref } from "vue";
import TabIntroducao from "../DadosGerais/Introducao/TabIntroducao.vue";
import TabLicenciamento from "../DadosGerais/Licenciamento/TabLicenciamento.vue";
import TabHistorico from "../DadosGerais/Historico/TabHistorico.vue";

const mapaTabDadosContratuais = ref();

defineProps({
    contrato: Object,
    numero_licencas: Array
})

const visualizarMapa = () => {
    mapaTabDadosContratuais.value.visualizarTrecho();
}

</script>

<template>

    <Head :title="`${contrato.contratada.slice(0, 10)}...`" />

    <AuthenticatedLayout>

        <template #header>
            <div class="w-100 d-flex justify-content-between">
                <Breadcrumb 
                    class="align-self-center" 
                    :links="[
                        { route: route('contratos.gestao.listagem', contrato.tipo_id), label: `Gestão de Contratos` },
                        { route: '#', label: contrato.contratada }
                    ]
                "/>
            </div>
        </template>

        <Navbar :contrato="contrato">

            <template #body>
                <ul class="nav nav-tabs card-header-tabs nav-fill" data-bs-toggle="tabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a @click="visualizarMapa()" href="#servicos" class="nav-link active"
                            data-bs-toggle="tab" aria-selected="true" role="tab">Serviços</a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a href="#introducao" class="nav-link" data-bs-toggle="tab" aria-selected="false" role="tab"
                            tabindex="-1">Execução</a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a href="#empreendimento" class="nav-link" data-bs-toggle="tab" aria-selected="false"
                            tabindex="-1" role="tab">Resultado</a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a href="#licenciamento" class="nav-link" data-bs-toggle="tab" aria-selected="false"
                            tabindex="-1" role="tab">Relatórios</a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a href="#historico" class="nav-link" data-bs-toggle="tab" aria-selected="false" tabindex="-1"
                            role="tab">Pareceres</a>
                    </li>
                </ul>

                <div class="card-body" style="margin-top: 15px;">
                    <div class="tab-content">
                        <div class="tab-pane active show" id="servicos" role="tabpanel">
                            <TabServicos :contrato="contrato" />
                        </div>
                        <div class="tab-pane" id="introducao" role="tabpanel">
                            <TabIntroducao :contrato="contrato" />
                        </div>
                        <div class="tab-pane" id="empreendimento" role="tabpanel">
                            <h4>Activity tab</h4>
                            <div>Donec ac vitae diam amet vel leo egestas consequat rhoncus in luctus amet, facilisi sit
                                mauris accumsan
                                nibh habitant senectus</div>
                        </div>
                        <div class="tab-pane" id="licenciamento" role="tabpanel">
                            <TabLicenciamento :contrato="contrato" :numero_licencas="numero_licencas" />
                        </div>
                        <div class="tab-pane" id="historico" role="tabpanel">
                            <TabHistorico :contrato="contrato" />
                        </div>
                        <div class="tab-pane" id="tabs-activity-5" role="tabpanel">
                            <h4>Activity tab</h4>
                            <div>Donec ac vitae diam amet vel leo egestas consequat rhoncus in luctus amet, facilisi sit
                                mauris accumsan
                                nibh habitant senectus</div>
                        </div>
                    </div>
                </div>
            </template>
        </Navbar>

    </AuthenticatedLayout>
</template>
