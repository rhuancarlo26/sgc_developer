<template>

    <Head :title="`${contrato.contratada.slice(0, 10)}...`" />

    <Navbar :contrato="contrato">
        <template #body>

            <!-- Pesquisa-->
            <ModelSearchForm :search-columns="{}" />

            <!-- Listagem-->
            <Table :columns="['#', 'Tema', 'Serviço', 'Especificação', 'Licença', 'Status', 'Ação']" :records="servicos"
                table-class="table-hover">
                <template #body="{ item }">
                    <tr>
                        <td class="text-center">{{item.id}}</td>
                        <td>{{ item.tema?.nome_tema }}</td>
                        <td>{{ item.tipo?.nome }}</td>
                        <td>{{ item.especificacao }}</td>
                        <td>
                            <span @click="abrirModalLicenca(item)" v-if="item.condicionantes.length">
                                {{ `${item.condicionantes[0]?.licenca?.numero_licenca ?? ''} ` }}
                            </span>
                        </td>
                        <td class="text-center">
                            <span v-if="item.status_aprovacao === 2" class="badge bg-yellow-lt">
                                Em análise
                            </span>
                            <span v-else-if="item.status_aprovacao === 3" class="badge bg-blue-lt">
                                Aprovado
                            </span>
                            <span v-else-if="item.status_aprovacao === 4" class="badge bg-red-lt">
                                Pendente
                            </span>
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-icon btn-info dropdown-toggle p-2"
                                data-bs-boundary="viewport" data-bs-toggle="dropdown" aria-expanded="false">
                                <IconDots />
                            </button>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a @click="abrirModalServicoFiscal(item)" class="dropdown-item" href="javascript:void(0)">
                                    Visualizar serviço
                                </a>
                                <a @click="abrirModalParecerFiscal(item)" class="dropdown-item" href="javascript:void(0)">
                                    Parecer
                                </a>
                            </div>
                        </td>
                    </tr>
                </template>
            </Table>
        </template>
    </Navbar>

    <ModalVisualizarParecerFiscal ref="modalVisualizarParecerFiscal" />
    <ModalVisualizarServicoFiscal ref="modalVisualizarServicoFiscal" />

</template>

<script setup>
import Table from "@/Components/Table.vue";
import ModelSearchForm from "@/Components/ModelSearchForm.vue";
import { Head } from "@inertiajs/vue3";
import Navbar from "../Navbar.vue";
import { IconDots } from "@tabler/icons-vue";
import ModalVisualizarParecerFiscal from "./ModalVisualizarParecerFiscal.vue";
import ModalVisualizarServicoFiscal from "./ModalVisualizarServicoFiscal.vue";
import { ref } from "vue";

defineProps({
    contrato: Object,
    servicos: Object
});

const modalVisualizarParecerFiscal = ref();
const modalVisualizarServicoFiscal = ref();

const abrirModalParecerFiscal = (item) => {
    modalVisualizarParecerFiscal.value.abrirModal(item);
}

const abrirModalServicoFiscal = (item) => {
    modalVisualizarServicoFiscal.value.abrirModal(item);
}

</script>

