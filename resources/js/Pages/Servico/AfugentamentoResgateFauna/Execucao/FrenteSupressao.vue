<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import Navbar from "../Navbar.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import ModelSearchFormAllColumns from "@/Components/ModelSearchFormAllColumns.vue";
import { ref } from "vue";
import { IconDots } from "@tabler/icons-vue";
import NovaFrenteSupressaoModal from "./Modal/NovaFrenteSupressaoModal.vue";
import { dateTimeFormat } from "@/Utils/DateTimeUtils";
import Swal from 'sweetalert2';
import VisualizarFrenteSupressaoModal from "./Modal/VisualizarFrenteSupressaoModal.vue";

const props = defineProps({
    contrato: { type: Object },
    servico: { type: Object },
    frenteSupressao: { type: Array }
});

const novaFrenteSupressaoModal = ref();
const itemParaEditar = ref(null);
const visualizarFrenteSupressaoModal = ref();

const abrirModalNovaFrenteSupressaoModal = (servico) => {
    novaFrenteSupressaoModal.value.abrirModal(servico);
}

const abrirModalEditarFrenteSupressao = (item) => {
    itemParaEditar.value = item;
    novaFrenteSupressaoModal.value.updateModal(item);
}

const abrirModalVisualizarFrenteSupressao = (item) => {
    visualizarFrenteSupressaoModal.value.abrirModal(item);
}

const destroy = (frente) => {
    Swal.fire({
        title: "Excluir Frente de Supressão",
        text: "Deseja continuar?",
        icon: "warning",
        showCloseButton: true,
        showCancelButton: true,
        focusConfirm: false,
    }).then((r) => {
        if (r.isConfirmed) {
            router.delete(route('contratos.contratada.servicos.afugentamento.resgate.fauna.execucao.frente.supressao.delete', { frente: frente.id }));
        }
    })
}

</script>

<template>

    <Head :title="`${contrato.contratada.slice(0, 10)}...`" />

    <AuthenticatedLayout>

        <template #header>
            <div class="w-100 d-flex justify-content-between">
                <Breadcrumb class="align-self-center" :links="[
                    { route: route('contratos.gestao.listagem', contrato.tipo_id), label: `Gestão de Contratos` },
                    { route: '#', label: contrato.contratada }
                ]
                    " />
                <div>
                    <Link class="btn btn-dark"
                        :href="route('contratos.contratada.servicos.index', { contrato: props.contrato.id })">
                    Voltar
                    </Link>
                    <a @click="abrirModalNovaFrenteSupressaoModal(servico)" class="btn ms-3" href="javascript:void(0)">
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

                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>BR</th>
                            <th>Uf Inicial</th>
                            <th>UF Final</th>
                            <th>KM Inicial</th>
                            <th>KM Final</th>
                            <th>Data Inicial</th>
                            <th>Data Final</th>
                            <th>Observações</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="item in frenteSupressao">
                            <td>{{ item.id }}</td>
                            <td>{{ item.rodovia?.rodovia }}</td>
                            <td>{{ item.uf_inicial?.uf }}</td>
                            <td>{{ item.uf_final?.uf }}</td>
                            <td>{{ item.km_inicial }}</td>
                            <td>{{ item.km_final }}</td>
                            <td>{{ dateTimeFormat(item.data_inicial) }}</td>
                            <td>{{ dateTimeFormat(item.data_final) }}</td>
                            <td>{{ item.obs }}</td>
                            <td>
                                <span class="dropdown">
                                    <button class="btn dropdown-toggle align-text-top" data-bs-boundary="viewport"
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                        <IconDots />
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                        <li><a class="dropdown-item" @click="abrirModalVisualizarFrenteSupressao(item)"
                                                href="javascript:void(0);">Visualizar</a></li>
                                        <li><a class="dropdown-item" @click="abrirModalEditarFrenteSupressao(item)"
                                                href="javascript:void(0);">Editar</a>
                                        </li>
                                        <li><a class="dropdown-item" @click="destroy(item)"
                                                href="javascript:void(0);">Excluir</a>
                                        </li>
                                    </ul>
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </template>
        </Navbar>
        <NovaFrenteSupressaoModal ref="novaFrenteSupressaoModal" :contrato="contrato" />
        <VisualizarFrenteSupressaoModal ref="visualizarFrenteSupressaoModal" />
    </AuthenticatedLayout>
</template>