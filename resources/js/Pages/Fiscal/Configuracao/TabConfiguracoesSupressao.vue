<template>

    <Head :title="`${contrato.contratada.slice(0, 10)}...`" />

    <Navbar :contrato="contrato">
        <template #body>

            <!-- Pesquisa -->
            <ModelSearchForm :search-columns="{}" />

            <!-- Listagem -->
            <Table :columns="['#', 'Serviço', 'Parecer','Status Aprovação', 'Ação']" :records="servicos"
                table-class="table-hover">
                <template #body="{ item }">
                    <tr>
                        <td class="text-center">{{item.id}}</tD>
                        <td class="text-center">{{ item.tipo?.nome }}</td>
                        <td> {{item.parecer_supressao_vegetacao?.parecer}}</td>
                        <td class="text-center">
                            <span v-if="item.parecer_supressao_vegetacao?.fk_status === 1" class="badge bg-yellow-lt">
                                Em análise
                            </span>
                            <span v-else-if="item.parecer_supressao_vegetacao?.fk_status === 3" class="badge bg-blue-lt">
                                Aprovado
                            </span>
                            <span v-else-if="item.parecer_supressao_vegetacao?.fk_status === 2" class="badge bg-red-lt">
                                Pendente
                            </span>
                            <span v-else class="badge bg-red-lt">
                                Em confecção
                            </span>
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-icon btn-info dropdown-toggle p-2"
                                data-bs-boundary="viewport" data-bs-toggle="dropdown" aria-expanded="false">
                                <IconDots />
                            </button>
                            <div class="dropdown-menu dropdown-menu-end">
<!--                                <a @click="abrirModalServicoFiscal(item)" class="dropdown-item" href="javascript:void(0)">-->
<!--                                    Visualizar-->
<!--                                </a>-->
                                <a @click="abrirModalParecerFiscal(item)" class="dropdown-item" href="javascript:void(0)">
                                    Visualizar parecer
                                </a>
                            </div>
                        </td>
                    </tr>
                </template>
            </Table>
        </template>
    </Navbar>

    <ModalParecerSupressao ref="modalParecerSupressao" />
<!--    <ModalVisualizarServicoFiscal ref="modalVisualizarServicoFiscal" />-->

</template>

<script setup>
import Table from "@/Components/Table.vue";
import ModelSearchForm from "@/Components/ModelSearchForm.vue";
import { Head } from "@inertiajs/vue3";
import Navbar from "../Navbar.vue";
import { IconDots } from "@tabler/icons-vue";
import { ref } from "vue";

import ModalParecerSupressao from "./ModalParecerSupressao.vue";


defineProps({
    contrato: Object,
    servicos: Object
});

const modalParecerSupressao = ref();
const modalVisualizarServicoFiscal = ref();

const abrirModalParecerFiscal = (item) => {
    modalParecerSupressao.value.abrirModal(item);
}

</script>
