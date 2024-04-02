<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import Navbar from "../Navbar.vue";
import TabServicos from "./Servicos/TabServicos.vue";

defineProps({
    contrato: Object,
    numero_licencas: Array
})

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
                <TabServicos/>
            </template>
        </Navbar>

    </AuthenticatedLayout>
</template>
