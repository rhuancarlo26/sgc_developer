<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import Navbar from "../Navbar.vue";
import { Head, Link } from "@inertiajs/vue3";
import ModelSearchFormAllColumns from "@/Components/ModelSearchFormAllColumns.vue";
import Table from "@/Components/Table.vue";
import NavButton from "@/Components/NavButton.vue";
import { ref } from "vue";
import { IconEye } from "@tabler/icons-vue";
import { IconPencil } from "@tabler/icons-vue";
import { IconTrash } from "@tabler/icons-vue";
import NavLink from "@/Components/NavLink.vue";
import LinkConfirmation from "@/Components/LinkConfirmation.vue";
import NovaFrenteSupressaoModal from "./Modal/NovaFrenteSupressaoModal.vue";

const props = defineProps({
    contrato: { type: Object },
    servico: { type: Object },
});

const novaFrenteSupressaoModal = ref();

const abrirModalNovaFrenteSupressaoModal = () => {
    novaFrenteSupressaoModal.value.abrirModal();
}

</script>

<template>

    <Head :title="`${contrato.contratada.slice(0, 10)}...`" />

    <AuthenticatedLayout>

        <template #header>
            <div class="w-100 d-flex justify-content-between">
                <Breadcrumb class="align-self-center" :links="[
                    { route: route('contratos.gestao.listagem', contrato.tipo_contrato), label: `Gestão de Contratos` },
                    { route: '#', label: contrato.contratada }
                ]
                    " />
                <div>
                    <Link class="btn btn-dark"
                        :href="route('contratos.contratada.servicos.index', { contrato: props.contrato.id })">
                    Voltar
                    </Link>
                    <a @click="abrirModalNovaFrenteSupressaoModal()" class="btn ms-3" href="javascript:void(0)">
                        Novo Frente de Supressão
                    </a>
                </div>
            </div>
        </template>

        <Navbar :contrato="contrato" :servico="servico">
            <template #body>
                <!-- Pesquisa-->
                <ModelSearchFormAllColumns :columns="[]">
                </ModelSearchFormAllColumns>

                <Table :columns="['ID', 'BR', 'UF Inicial', 'UF Final', 'KM Inicial',
                    'KM Final', 'Data Inicial', 'Data Final', 'Observações', 'Ação']" :records="servico"
                    table-class="table-hover">
                    <template #body="{ item }">
                        <tr>
                            <td>ID</td>
                            <td>BR</td>
                            <td>UF Inicial</td>
                            <td>UF Final</td>
                            <td>KM Inicial</td>
                            <td>KM Final</td>
                            <td>Data Inicial</td>
                            <td>Data Final</td>
                            <td>Observações</td>
                            <td>acao</td>
                        </tr>
                    </template>
                </Table>
            </template>
        </Navbar>
        <NovaFrenteSupressaoModal ref="novaFrenteSupressaoModal" :contrato="contrato" />
    </AuthenticatedLayout>
</template>
