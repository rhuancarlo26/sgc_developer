<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import Navbar from "../Navbar.vue";
import {Head, Link, router} from "@inertiajs/vue3";
import ModelSearchFormAllColumns from "@/Components/ModelSearchFormAllColumns.vue";
import Table from "@/Components/Table.vue";
import {ref} from "vue";
import {IconDots, IconDownload} from "@tabler/icons-vue";
import NovoRegistroModal from "./Modal/NovoRegistroModal.vue";
import {dateTimeFormat} from "@/Utils/DateTimeUtils";
import Swal from 'sweetalert2';
import VisualizarRegistroModal from "./Modal/VisualizarRegistroModal.vue";

const props = defineProps({
    contrato: {type: Object},
    servico: {type: Object},
    grupoAmostrado: {type: Array},
    frenteSupressao: {type: Array},
    formaRegistro: {type: Array},
    registros: {type: Array}
});

const novoRegistroModal = ref();

const abrirModalNovoRegistro = (contrato, servico) => {
    novoRegistroModal.value.abrirModal(contrato, servico);
}

const abrirModalEditarRegistro = (item) => {
    novoRegistroModal.value.updateModal(item);
}

const visualizarRegistroModal = ref();

const abrirModalVisualizarRegistro = (item) => {
    visualizarRegistroModal.value.abrirModal(item);
}

const destroy = (registro) => {
    Swal.fire({
        title: "Excluir Registro",
        text: "Deseja continuar?",
        icon: "warning",
        showCloseButton: true,
        showCancelButton: true,
        focusConfirm: false,
    }).then((r) => {
        if (r.isConfirmed) {
            router.delete(route('contratos.contratada.servicos.afugentamento.resgate.fauna.execucao.registros.delete', {registro: registro.id}));
        }
    })
}

const download = () => {
    router.get(route('contratos.contratada.servicos.afugentamento.resgate.fauna.execucao.registros.download', {servico: props.servico.id}));
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
                ]
                    "/>
                <div>
                    <Link class="btn"
                          :href="route('contratos.contratada.servicos.index', { contrato: props.contrato.id })">
                        Voltar
                    </Link>
                    <a @click="abrirModalNovoRegistro(contrato, servico)" class="btn ms-3" href="javascript:void(0)">
                        Novo Registro
                    </a>
                </div>
            </div>
        </template>

        <Navbar :contrato="contrato" :servico="servico">
            <template #body>
                <div class="d-flex justify-content-between">
                    <!-- Pesquisa-->
                    <ModelSearchFormAllColumns class="w-100" :columns="[]">
                    </ModelSearchFormAllColumns>

                    <!-- Download -->
                    <button type="button" class="btn col h-5" @click="download()">
                        <IconDownload/>
                    </button>
                </div>
                <Table :columns="['Nome do Registro', 'Nº da frente de Supressão', 'BR', 'UF', 'KM',
                    'Grupo Amostrado', 'Espécie', 'Data Registro', 'Categoria', 'Ação']" :records="registros"
                       table-class="table-hover">
                    <template #body="{ item }">
                        <tr>
                            <td>{{ item.nome_registro }}</td>
                            <td>{{ item.id_frente }}</td>
                            <td>{{ item.rodovia }}</td>
                            <td>{{ item.uf }}</td>
                            <td>{{ item.km }}</td>
                            <td>{{ item.nome_grupo_amostrado }}</td>
                            <td>{{ item.especie }}</td>
                            <td>{{ dateTimeFormat(item.data_registro) }}</td>
                            <td>{{ item.categoria }}</td>
                            <td>
                                <button class="btn dropdown-toggle align-text-top" data-bs-boundary="viewport"
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                    <IconDots/>
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                    <li><a class="dropdown-item" @click="abrirModalVisualizarRegistro(item)"
                                           href="javascript:void(0);">Visualizar</a></li>
                                    <li><a class="dropdown-item" @click="abrirModalEditarRegistro(item)"
                                           href="javascript:void(0);">Editar</a>
                                    </li>
                                    <li><a class="dropdown-item" @click="destroy(item)"
                                           href="javascript:void(0);">Excluir</a>
                                    </li>
                                </ul>
                            </td>
                        </tr>
                    </template>
                </Table>
            </template>
        </Navbar>
        <NovoRegistroModal ref="novoRegistroModal" :grupoAmostrado="grupoAmostrado" :frenteSupressao="frenteSupressao"
                           :formaRegistro="formaRegistro"/>

        <VisualizarRegistroModal ref="visualizarRegistroModal"/>
    </AuthenticatedLayout>
</template>
