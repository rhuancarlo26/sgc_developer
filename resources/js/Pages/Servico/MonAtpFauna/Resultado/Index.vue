<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import Navbar from "../Navbar.vue";
import NavButton from "@/Components/NavButton.vue";
import ModelSearchFormAllColumns from "@/Components/ModelSearchFormAllColumns.vue";
import Table from "@/Components/Table.vue";
import ModalNovoResultado from "./ModalNovoResultado.vue";
import ModalAnalise from "./ModalAnalise.vue";
import {ref} from "vue";
import {dateTimeFormat} from "@/Utils/DateTimeUtils";
import {IconDots} from "@tabler/icons-vue";
import {Head} from "@inertiajs/vue3";

const props = defineProps({
    contrato: {type: Object},
    data: {type: Object},
    servico: {type: Object},
    campanhas: {type: Array},
});

const modalNovoResultado = ref({});
const modalAnalise = ref({});

const abrirModalNovoResultado = (item) => {
    modalNovoResultado.value.abrirModal(item);
}

const showActionsModal = ref(true);
const abrirModalAnalise = (item, show = true) => {
    showActionsModal.value = show;
    modalAnalise.value.abrirModal(item);
}

</script>
<template>

    <Head :title="`${contrato.contratada.slice(0, 10)}...`"/>

    <AuthenticatedLayout>

        <template #header>
            <div class="w-100 d-flex justify-content-between">
                <Breadcrumb class="align-self-center" :links="[
                    { route: route('contratos.gestao.listagem', contrato.tipo_contrato), label: `Gestão de Contratos` },
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
                <ModelSearchFormAllColumns :columns="[]">
                    <template #action>
                        <NavButton @click="abrirModalNovoResultado()" type-button="info" title="Novo Resultado"/>
                    </template>
                </ModelSearchFormAllColumns>

                <Table
                    :columns="['ID', 'Nome do resultado', 'Campanhas', 'Data', 'Ação']"
                    :records="data" table-class="table-hover">
                    <template #body="{ item }">
                        <tr>
                            <td>{{ item.id }}</td>
                            <td>{{ item.nome_resultado }}</td>
                            <td>
                                <span v-for="i in item.campanhas.split(',')" class="badge badge-warning m-1">
                                    {{i}}
                                  </span>
                            </td>
                            <td>{{ dateTimeFormat(item.created_at) }}</td>
                            <td>
                                <button type="button" class="btn btn-icon btn-info dropdown-toggle p-2"
                                        data-bs-boundary="viewport"
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                    <IconDots/>
                                </button>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <a href="#" class="dropdown-item" @click.prevent="abrirModalAnalise(item)">
                                        Analisar
                                    </a>
                                    <a href="#" class="dropdown-item" @click.prevent="abrirModalAnalise(item, false)">
                                        Visualizar
                                    </a>
                                    <a href="#" class="dropdown-item" @click.prevent="abrirModalNovoResultado(item)">
                                        Editar
                                    </a>
                                    <a href="#" class="dropdown-item" @click.prevent="abrirModalAnalise(item)">
                                        Excluir
                                    </a>
                                </div>
                            </td>
                        </tr>
                    </template>
                </Table>
            </template>
        </Navbar>

        <ModalNovoResultado ref="modalNovoResultado" :campanhas="campanhas" :servico="servico" />
        <ModalAnalise ref="modalAnalise" :showActionsModal="showActionsModal"/>

    </AuthenticatedLayout>
</template>
