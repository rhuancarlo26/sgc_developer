<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import { ref, watch, computed } from "vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import TabDadosBasicos from "./TabDadosBasicos.vue";
import TabSegmento from "./TabSegmento.vue";
import TabAnexo from "./TabAnexo.vue";

// PROPS
const props = defineProps({
    tipos: {
        type: Array
    },
    ufs: {
        type: Array
    },
    rodovias: {
        type: Array
    },
    licenca: {
        type: Object
    }
});

const mapContainer = ref();

const abaSegmento = () => {
    mapContainer.value.abaSegmento();
}

</script>
<template>

    <Head title="Cadastro de Licenças" />

    <AuthenticatedLayout>

        <template #header>
            <div class="w-100 d-flex justify-content-between">
                <Breadcrumb class="align-self-center" :links="[{ route: '#', label: 'Cadastro de Licenças' }]" />
            </div>
        </template>

        <div class="card mb-4">
            <!-- CARD-HEADER -->
            <div class="card-header">
                <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs" role="tablist">
                    <!-- TAB - DADOS BASICOS -->
                    <li class="nav-item" role="presentation">
                        <a href="#tab-dadosBasicos" class="nav-link active" data-bs-toggle="tab" aria-selected="true"
                            role="tab">
                            Dados Básicos
                        </a>
                    </li>
                    <!-- TAB - SEGMENTO -->
                    <li v-if="licenca.id" class="nav-item" role="presentation">
                        <a @click="abaSegmento()"  href="#tab-segmento" class="nav-link" data-bs-toggle="tab" aria-selected="false" role="tab">
                            Segmentos
                        </a>
                    </li>
                    <!-- TAB - ARQUIVOS -->
                    <li v-if="licenca.id" class="nav-item" role="presentation">
                        <a href="#tab-arquivos" class="nav-link" data-bs-toggle="tab" aria-selected="false" role="tab">
                            Arquivos
                        </a>
                    </li>
                </ul>
            </div>

            <!-- CARD-BODY -->
            <div class="card-body">
                <div class="tab-content">
                    <!-- TAB - DADOS BASICOS -->
                    <div id="tab-dadosBasicos" class="tab-pane active show" role="tabpanel">
                        <TabDadosBasicos :tipos="tipos" :licenca="licenca" />
                    </div>

                    <!-- TAB - SEGMENTO -->
                    <div id="tab-segmento" class="tab-pane" role="tabpanel">
                        <TabSegmento ref="mapContainer" :licenca="licenca" :ufs="ufs" :rodovias="rodovias" />
                    </div>

                    <!-- TAB - ARQUIVOS -->
                    <div id="tab-arquivos" class="tab-pane" role="tabpanel">
                        <TabAnexo :licenca="licenca" />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>



<style scoped>
.container-buttons a,
.container-buttons button {
    margin: 0px 5px;
}
</style>
