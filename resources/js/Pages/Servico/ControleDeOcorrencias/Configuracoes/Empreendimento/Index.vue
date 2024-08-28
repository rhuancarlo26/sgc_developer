<template>

    <Head :title="`${contrato.contratada.slice(0, 10)}...`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="w-100 d-flex justify-content-between">
                <Breadcrumb class="align-self-center" :links="[
        { route: route('contratos.gestao.listagem', contrato.tipo_id), label: `Gestão de Contratos` },
        { route: '#', label: contrato.contratada }
    ]" />
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
                    :columns="['UF Inicial', 'UF Final', 'BR', 'Empreendimento', 'KM Incial', 'KM Final', 'Extensão', 'Nº Licença', 'ShapeFile', 'Ação']"
                    :records="empreendimentos" table-class="table-hover">
                    <template #body="{ item }">
                        <tr>
                            <td class="w-8">
                                <p v-if="item.licenca?.iniciais">
                                    <span v-for="uf in item.licenca?.iniciais.split(',')" :key="uf"
                                        class="badge bg-warning text-white m-1">
                                        {{ uf }}
                                    </span>
                                </p>
                            </td>
                            <td class="w-8">
                                <p v-if="item.licenca?.finais">
                                    <span v-for="uf in item.licenca?.finais.split(',')" :key="uf"
                                        class="badge bg-warning text-white m-1">
                                        {{ uf }}
                                    </span>
                                </p>
                            </td>
                            <td class="w-8">
                                <p v-if="item.licenca?.brs">
                                    <span v-for="br in item.licenca?.brs.split(',')" :key="br"
                                        class="badge bg-warning text-white m-1">
                                        {{ br }}
                                    </span>
                                </p>
                            </td>
                            <td>{{ item.licenca?.empreendimento }}</td>
                            <td>{{ Math.min(...item.licenca?.segmentos.map(segmento => segmento.km_inicial)) }}</td>
                            <td>{{ Math.max(...item.licenca?.segmentos.map(segmento => segmento.km_final)) }}</td>
                            <td>{{ item.licenca?.extensao }}</td>
                            <td class="text-center">{{ item.licenca?.numero_licenca }}</td>
                            <td class="text-center"> - </td>
                            <td class="text-center">
                                <NavButton :icon="IconEye" class="btn-icon" type-button="primary"
                                    @click="visualizarEmpreendimento(item)" />
                            </td>
                        </tr>
                    </template>
                </Table>
            </template>
        </Navbar>

        <!-- Modal Visualizar Empreendimento -->
        <VisualizarEmpreendimentoModal ref="modalRef" :empreendimento="selectedItem" />
    </AuthenticatedLayout>
</template>

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
import VisualizarEmpreendimentoModal from './VisualizarEmpreendimentoModal.vue';

const props = defineProps({
    contrato: { type: Object },
    servico: { type: Object },
    empreendimentos: { type: Object }
    // listas: { type: Array }
});

const modalRef = ref(null);
const selectedItem = ref(null);

function visualizarEmpreendimento(item) {
    selectedItem.value = item;
    modalRef.value.open();
}
</script>