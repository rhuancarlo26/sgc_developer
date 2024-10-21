<script setup>
import Table from "@/Components/Table.vue";
import ModelSearchForm from "@/Components/ModelSearchForm.vue";
import { Head } from "@inertiajs/vue3";
import Navbar from "../Navbar.vue";
import { IconDots } from "@tabler/icons-vue";
// import ModalVisualizarLogAtividadesRNCFiscal from "../ModalVisualizarLogAtividadesRNCFiscal.vue";
import ModalVisualizarParecerRNCFiscal from "../ModalVisualizarParecerRNCFiscal.vue";
import ModalVisualizarRNCFiscal from "../ModalVisualizarRNCFiscal.vue";
import { ref } from "vue";

const props = defineProps({
    contrato: Object,
    rncs: Object
});

const dataDados = [
    {
        id_ocorrencia: 1,
        intensidade_ocorrencia: "Alta",
        data_ocorrencia: "2023-04-01",
        data_fim: "2023-04-15",
        ocorrencia_anterior: "Nenhuma",
        prazo_correcao: "2023-04-20",
        lote: "Lote 1",
        construtora: "Construtora Exemplo",
        status_aprovacao: "Pendente",
        status_atendimento: "Em andamento",
        envio: "2023-04-02",
        acao: "Revisão técnica"
    },
    {
        id_ocorrencia: 2,
        intensidade_ocorrencia: "Média",
        data_ocorrencia: "2023-04-05",
        data_fim: "2023-04-25",
        ocorrencia_anterior: "Vazamento de água",
        prazo_correcao: "2023-05-05",
        lote: "Lote 2",
        construtora: "Construtora ABC",
        status_aprovacao: "Aprovado",
        status_atendimento: "Concluído",
        envio: "2023-04-06",
        acao: "Reparo de encanamento"
    }
]

// const modalVisualizarLogAtividadesRNCFiscal = ref();
const modalVisualizarParecerRNCFiscal = ref();
const modalVisualizarRNCFiscal = ref();

// const abrirModalLogAtividades = (item) => {
//     modalVisualizarLogAtividadesRNCFiscal.value.abrirModal(item);
// }

const abrirModalParecerRNCFiscal = (item) => {
    modalVisualizarParecerRNCFiscal.value.abrirModal(item);
}

const abrirModalRNCFiscal = (item) => {
    modalVisualizarRNCFiscal.value.abrirModal(item);
}

</script>

<template>

    <Head :title="`${contrato.contratada.slice(0, 10)}...`" />

    <Navbar :contrato="contrato">
        <template #body>

            <!-- Pesquisa -->
            <ModelSearchForm :search-columns="{}" />

            <!-- Listagem -->
            <Table :columns="['ID Ocorrência', 'Intensidade Ocorrência', 'Data da Ocorrência', 'Data Fim', 'Ocorrência Anterior',
                'Prazo Correção', 'Lote', 'Construtora', 'Status Aprovação', 'Status Atendimento', 'Envio', 'Ação']"
                :records="{ data: dataDados, links: [] }" table-class="table-hover">
                <template #body="{ item }">
                    <tr>
                        <td>{{ item.id_ocorrencia }}</td>
                        <td>{{ item.intensidade_ocorrencia }}</td>
                        <td>{{ item.data_ocorrencia }}</td>
                        <td>{{ item.data_fim }}</td>
                        <td>{{ item.ocorrencia_anterior }}</td>
                        <td>{{ item.prazo_correcao }}</td>
                        <td>{{ item.lote }}</td>
                        <td>{{ item.construtora }}</td>
                        <td>{{ item.status_aprovacao }}</td>
                        <td>{{ item.status_atendimento }}</td>
                        <td>{{ item.envio }}</td>
                        <td class="text-center">
                            <button type="button" class="btn btn-icon btn-info dropdown-toggle p-2"
                                data-bs-boundary="viewport" data-bs-toggle="dropdown" aria-expanded="false">
                                <IconDots />
                            </button>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a @click="abrirModalLogAtividades(item)" class="dropdown-item"
                                    href="javascript:void(0)">
                                    Log de Atividades
                                </a>
                                <a @click="abrirModalRNCFiscal(item)" class="dropdown-item" href="javascript:void(0)">
                                    Visualizar RNC
                                </a>
                                <a @click="abrirModalParecerRNCFiscal(item)" class="dropdown-item"
                                    href="javascript:void(0)">
                                    Visualizar parecer
                                </a>
                            </div>
                        </td>
                    </tr>
                </template>
            </Table>
        </template>
    </Navbar>

    <!-- <ModalVisualizarLogAtividadesRNCFiscal ref="modalVisualizarLogAtividadesRNCFiscal" /> -->
    <ModalVisualizarParecerRNCFiscal ref="modalVisualizarParecerRNCFiscal" />
    <ModalVisualizarRNCFiscal ref="modalVisualizarRNCFiscal" />

</template>
