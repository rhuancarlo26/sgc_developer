<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, router, usePage } from "@inertiajs/vue3";
import { IconCirclePlus, IconDots, IconEye, IconTrash, IconDownload } from '@tabler/icons-vue';
import Breadcrumb from "@/Components/Breadcrumb.vue";
import ModelSearchForm from "@/Components/ModelSearchFormAllColumns.vue";
import Table from '@/Components/Table.vue';
import NavLink from "@/Components/NavLink.vue";

const props = defineProps({
    modulos: Object,
})

</script>

<template>

  <Head title="Modulos" />

  <AuthenticatedLayout>

    <template #header>
        <div class="w-100 d-flex justify-content-between">
            <Breadcrumb class="align-self-center" :links="[
                { route: route('modulos.importador.index'), label: `Importador` }
            ]" />
            <NavLink route-name="modulos.importador.formulario" title="Nova Importação" :icon="IconCirclePlus"
                class="btn btn-info me-2"/>
        </div>
    </template>


    <div class="card card-body">
        <!-- Pesquisa -->
        <ModelSearchForm :columns="[
            'nome',
        ]" />

        <!-- Listagem-->
        Tabela de Importadores

    </div>

  </AuthenticatedLayout>
</template>
