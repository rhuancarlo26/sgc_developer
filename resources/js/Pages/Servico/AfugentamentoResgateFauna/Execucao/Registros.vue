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
import NovoRegistroModal from "./Modal/NovoRegistroModal.vue";

const props = defineProps({
    contrato: { type: Object },
    servico: { type: Object },
});

const novoRegistroModal = ref();

const abrirModalNovoRegistro = () => {
    novoRegistroModal.value.abrirModal();
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
                    <Link class="btn"
                        :href="route('contratos.contratada.servicos.index', { contrato: props.contrato.id })">
                    Voltar
                    </Link>
                    <a @click="abrirModalNovoRegistro()" class="btn ms-3" href="javascript:void(0)">
                        Novo Registro
                    </a>
                </div>
            </div>
        </template>

        <Navbar :contrato="contrato" :servico="servico">
            <template #body>
                <!-- Pesquisa-->
                <ModelSearchFormAllColumns :columns="[]">
                </ModelSearchFormAllColumns>

                <Table :columns="['Nome do Registro', 'Nº da frente de Supressão', 'BR', 'UF', 'KM',
                    'Grupo Amostrado', 'Espécie', 'Data Registro', 'Categoria', 'Ação']" :records="servico"
                    table-class="table-hover">
                    <template #body="{ item }">
                        <tr>
                            <td>Nome do Registro</td>
                            <td>Nº da frente de Supressão</td>
                            <td>BR</td>
                            <td>UF</td>
                            <td>KM</td>
                            <td>Grupo Amostrado</td>
                            <td>Especie</td>
                            <td>Data Registro</td>
                            <td>Categoria</td>
                            <td>acao</td>
                        </tr>
                    </template>
                </Table>
            </template>
        </Navbar>
        <NovoRegistroModal ref="novoRegistroModal" />
    </AuthenticatedLayout>
</template>
