<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import { ref, watch, computed } from "vue";
import { IconDoorExit } from "@tabler/icons-vue";
import { nextTick } from "vue";
import TabDadosGerais from "./TabDadosGerais.vue";
import TabTrechos from "./TabTrechos.vue";

const props = defineProps({
    ufs: {
        type: Array
    },
    rodovias: {
        type: Array
    },
    tipos: {
        type: Array
    },
    tipo: {
        type: Object
    },
    situacoes: {
        type: Array
    },
    contrato: {
        type: Object,
        default: null
    }
});

const mapContainer = ref();

const abaTrecho = () => {
    mapContainer.value.abaTrecho();
}
</script>
<template>

    <Head title="Gestão de Contratos" />

    <AuthenticatedLayout>

        <template #header>
            <div class="w-100 d-flex justify-content-between align-self-center">
                <Breadcrumb :links="[
                    { route: route('contratos.gestao.listagem', tipo.id), label: 'Gestão de Contratos' },
                    { route: '#', label: 'Formulário' }
                ]" />
                <Link class="btn btn-info" :href="route('contratos.gestao.listagem', tipo.id)">
                <IconDoorExit class="me-2" />
                Voltar
                </Link>
            </div>
        </template>

        <div class="card mb-4">
            <div class="card">
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a href="#tabs-home-1" class="nav-link active" data-bs-toggle="tab" aria-selected="true"
                                role="tab">Dados
                                Básicos</a>
                        </li>
                        <li v-if="contrato.id" class="nav-item" role="presentation">
                            <a @click="abaTrecho()" href="#tabs-profile-1" class="nav-link" data-bs-toggle="tab"
                                aria-selected="false" role="tab" tabindex="-1">Trechos</a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content">
                        <div class="tab-pane active show" id="tabs-home-1" role="tabpanel">
                            <TabDadosGerais :contrato="contrato" :tipo="tipo" />
                        </div>

                        <div class="tab-pane" id="tabs-profile-1" role="tabpanel">
                            <TabTrechos :contrato="contrato" :ufs="ufs" :rodovias="rodovias" :tipo="tipo" :tipos="tipos"
                                ref="mapContainer" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
