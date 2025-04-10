<script setup>
import { ref } from 'vue';
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import Navbar from "../../Navbar.vue";
import { Head, Link } from "@inertiajs/vue3";
import ModelSearchFormAllColumns from "@/Components/ModelSearchFormAllColumns.vue";
import Table from "@/Components/Table.vue";
import NavButton from "@/Components/NavButton.vue";
import { IconEye } from "@tabler/icons-vue";
import VisualizarLoteDeObraModal from './VisualizarLoteDeObraModal.vue';

const props = defineProps({
    contrato: { type: Object },
    servico: { type: Object },
    listas: { type: Array }
});

const modalRef = ref(null);
const selectedItem = ref(null);

function visualizarLoteDeObra(item) {
    selectedItem.value = item;
    modalRef.value.open();
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
                    ]" 
                />
                <Link class="btn btn-dark"
                    :href="route('contratos.contratada.servicos.index', { contrato: props.contrato.id })">
                Voltar
                </Link>
            </div>
        </template>

        <Navbar :contrato="contrato" :servico="servico">
            <template #body>
                <!-- Container Pesquisa / Botões -->
                <div style="display: inline-flex; align-items: flex-start; justify-content: space-between;">
                    <!-- Pesquisa -->
                    <div style="display: block; width: 100%;">
                        <ModelSearchFormAllColumns :columns="['nome', 'parametro.nome']">
                            <template #action></template>
                        </ModelSearchFormAllColumns>
                    </div>
                </div>

                <!-- Listagem -->
                <Table
                    :columns="['ID Lote', 'Nome do Lote', 'BR', 'UF', 'KM Inicial', 'KM Final', 'Construtora/Consórcio', 'Nº Contrato', 'Situação', 'Supervisora de Obra', 'Ação']"
                    :records="{ data: listas, links: [] }" table-class="table-hover">
                    <template #body="{ item }">
                        <tr>
                            <td class="text-center">{{ item.nome_id }}</td>
                            <td class="text-center">{{ item.nome }}</td>
                            <td class="text-center">{{ item.rodovia }}</td>
                            <td class="text-center">{{ item.uf }}</td>
                            <td class="text-center">{{ item.km_inicial }}</td>
                            <td class="text-center">{{ item.km_final }}</td>
                            <td class="text-center">{{ item.empresa }}</td>
                            <td class="text-center">{{ item.num_contrato }}</td>
                            <td class="text-center">{{ item.situacao_contrato }}</td>
                            <td class="text-center">{{ item.supervisora_obras }}</td>
                            <td class="text-center">
                                <NavButton :icon="IconEye" class="btn-icon" type-button="primary" @click="visualizarLoteDeObra(item)" />
                            </td>
                        </tr>
                    </template>
                </Table>
            </template>
        </Navbar>

        <!-- Modal Visualizar Lote de Obra -->
        <VisualizarLoteDeObraModal ref="modalRef" :loteDeObra="selectedItem" />
    </AuthenticatedLayout>
</template>

