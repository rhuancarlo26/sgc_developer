<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, router, usePage } from "@inertiajs/vue3";
import { IconCirclePlus, IconDots, IconEye, IconTrash, IconDownload } from '@tabler/icons-vue';
import Breadcrumb from "@/Components/Breadcrumb.vue";
import ModelSearchForm from "@/Components/ModelSearchFormAllColumns.vue";
import Table from '@/Components/Table.vue';
import NavLink from "@/Components/NavLink.vue";
import { dateTimeFormat } from '@/Utils/DateTimeUtils';
import { ref } from "vue";
import Swal from "sweetalert2";
import ModalCamposModulo from "./ModalCamposModulo.vue"

const props = defineProps({
    modulos: Object,
    tipos: Array,
    contrato: [String, Number],
    produto: String,
})

const page = usePage()

const ModalCamposModuloRef = ref(null)

const mostrarCampos = (modulo) => {
    ModalCamposModuloRef.value.abrirModal(modulo)
}

const excluir = (moduloId) => {
    Swal.fire({
        title: 'Tem certeza?',
        text: 'Essa ação não poderá ser desfeita!',
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: 'Sim, excluir',
        cancelButtonText: 'Cancelar'
    }).then(result => {

        if (result.isConfirmed) {
            console.log('opa')
            router.delete(route('sgc.contratada.produtos.modulos.configuracoes.delete', [
                props.contrato,
                props.produto,
                moduloId,
            ]))
        }
    })
}

</script>

<template>

    <Head title="Modulos" />

    <AuthenticatedLayout>

        <template #header>
            <div class="w-100 d-flex justify-content-between">
                <Breadcrumb class="align-self-center" :links="[
                    { route: route('sgc.contratada.produtos.modulos.configuracoes.index', [
                        props.contrato,
                        props.produto,
                    ]), label: `Módulos` }
                ]" />
                <NavLink
                    route-name="sgc.contratada.produtos.modulos.configuracoes.formulario"
                    :param="[props.contrato, props.produto]"
                    title="Novo Módulo"
                    :icon="IconCirclePlus"
                    class="btn btn-info me-2"
                />
            </div>
        </template>


        <div class="card card-body">
            <!-- Pesquisa -->
            <ModelSearchForm :columns="[
                'nome',
            ]" />

            <!-- Listagem-->
            <Table :columns="['Nome', 'Campos', 'Planilha Modelo Gerada', 'Criado em', 'Ações']" :records="modulos"
                table-class="table-hover">
                <template #body="{ item }">
                    <tr class="cursor-pointer">
                        <td class="text-center align-middle">{{ item.nome }}</td>
                        <td class="text-center align-middle">
                            <button class="btn btn-sm btn-secondary" @click="mostrarCampos(item)">
                                <IconEye />
                            </button>
                        </td>
        
                        <td class="text-center align-middle">
                            {{ item.nome_planilha_modelo }}
                            <a v-if="item.id && item.campos?.length"
                                :href="route('sgc.contratada.produtos.modulos.configuracoes.gerar-planilha-modelo', [
                                    props.contrato,
                                    props.produto,
                                    item.id,
                                ])"
                                class="btn btn-sm bg-gray-400 ms-2" target="_blank" title="Gerar Planilha Modelo">
                                <IconDownload class="me-1" />

                            </a>

                            <span v-else>-</span>
                        </td>
                        <td class="text-center align-middle">{{ dateTimeFormat(item.created_at) }}</td>
                        <td class="text-center">
                            <button type="button" class="btn btn-icon btn-info dropdown-toggle p-2"
                                data-bs-boundary="viewport" data-bs-toggle="dropdown" aria-expanded="false">
                                <IconDots />
                            </button>
                            <div class="dropdown-menu dropdown-menu-end">
                                <NavLink
                                    route-name="sgc.contratada.produtos.modulos.configuracoes.formulario"
                                    :param="[props.contrato, props.produto, item.id]"
                                    title="Editar"
                                    class="dropdown-item"
                                />

                                <button @click="excluir(item.id)" class="dropdown-item">
                                    Excluir
                                </button>
                            </div>
                        </td>
                    </tr>
                </template>
            </Table>
        </div>

        <ModalCamposModulo
            ref="ModalCamposModuloRef"
            :tipos="tipos"
            :contrato="props.contrato"
            :produto="props.produto"
        />

    </AuthenticatedLayout>
</template>
