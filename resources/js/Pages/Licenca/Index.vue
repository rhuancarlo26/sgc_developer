<template>

    <Head title="Gestão de Licenças" />

    <AuthenticatedLayout>

        <template #header>
            <div class="w-100 d-flex justify-content-between">
                <Breadcrumb class="align-self-center" :links="[{ route: '#', label: 'Gestão de Licenças' }]" />
                <div class="container-buttons">
                    <NavLink route-name="licenca.create" title="Cadastrar licenças" class="btn btn-info me-2" />
                    <button type="button" class="btn btn-icon btn-info dropdown-toggle p-2" data-bs-boundary="viewport"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <IconSettings />
                    </button>
                    <div class="dropdown-menu dropdown-menu-end">
                        <NavLink route-name="licenca.arquivo" :param="1" title="Licenças arquivadas"
                            class="dropdown-item" />
                        <NavLink route-name="licenca.index" title="Licenças ativas" class="dropdown-item" />
                        <a class="dropdown-item" @click="abrirMapaGeral()">
                            Mapa das licenças
                        </a>
                    </div>
                </div>
            </div>
        </template>

        <div class="card card-body space-y-3">

            <!-- Pesquisa-->
            <ModelSearchForm :columns="[
                'tipo_licenca.sigla',
                'numero_licenca',
                'empreendimento',
                'data_emissao',
                'status',
                'vencimento',
                'processo_dnit'
            ]" />

            <!-- Listagem-->
            <Table
                :columns="['Modal', 'Tipo', 'Nº Licença', 'Empreendimento', 'Emissor', 'Data da emissão', 'Status', 'Vencimento', 'Processo DNIT', 'Açao']"
                :records="licencas" table-class="table-hover">
                <template #body="{ item }">
                    <tr>
                        <td class="w-8 text-center">
                            <IconCar v-if="item.modal == 1" />
                            <IconShip v-if="item.modal == 2" />
                            <IconTrain v-if="item.modal == 3" />
                        </td>
                        <td class="text-center">
                            {{ item.tipo?.sigla }}
                        </td>
                        <td>
                            {{ item.numero_licenca }}
                        </td>
                        <td>
                            {{ item.empreendimento }}
                        </td>
                        <td>
                            {{ item.emissor }}
                        </td>
                        <td class="text-center">
                            {{ dateTimeFormat(item.data_emissao) }}
                        </td>
                        <td class="text-center">
                            <a href="javascript:void(0)">
                                <span v-if="item.requerimentos.length" class="badge bg-primary-lt">
                                    Em Análise
                                </span>
                                <span v-else-if="dtAlerta(item.vencimento) <= 0" class="badge bg-danger-lt">
                                    Vencida
                                </span>
                                <span v-else-if="!item.vencimento !== '' && item.vencimento !== null"
                                    class="badge bg-green-lt">
                                    Vigente
                                </span>
                            </a>
                        </td>
                        <td class="text-center">
                            <a href="javascript:void(0)">
                                <span v-if="dtAlerta(item.vencimento) <= 0" class="badge bg-danger-lt">
                                    {{ dateTimeFormat(item.vencimento) }}
                                </span>
                                <span v-else-if="item.vencimento !== '' && item.vencimento !== null"
                                    class="badge bg-green-lt">
                                    {{ dateTimeFormat(item.vencimento) }}
                                </span>
                            </a>
                        </td>
                        <td>
                            {{ item.processo_dnit }}
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-icon btn-info dropdown-toggle p-2"
                                    data-bs-boundary="viewport" data-bs-toggle="dropdown" aria-expanded="false">
                                <IconDots/>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end" style="">
                                <NavLink route-name="licenca.create" :param="item.id" title="Editar"
                                         class="dropdown-item"/>
                                <NavLinkVoid title="Visualizar" @click="abrirModalVisualizar(item)"/>
                                <a v-if="item.documento?.id" class="dropdown-item" target="_blank"
                                   :href="route('licenca.documento.visualizar', item.documento.id)">
                                    Visualizar PDF
                                </a>
                                <NavLink route-name="licenca.condicionante.index" :param="item.id"
                                         title="Condicionante" class="dropdown-item"/>
                                <NavLinkVoid route-name="licenca.requerimento.index" title="Requerimento"
                                             @click="abrirModalRequerimento(item)"/>
                                <NavLinkVoid v-if="!item.arquivado" title="Arquivar licença"
                                             @click="gerenciarArquivo(item, index, true)"/>
                                <NavLinkVoid v-else title="Desarquivar"
                                             @click="gerenciarArquivo(item, index, false)" class="dropdown-item"/>
                            </div>
                        </td>
                    </tr>
                </template>
            </Table>
        </div>

        <ModalMapaGeral ref="refModalMapaGeral" />
        <ModalVisualizar ref="refModalVisualizar" />
        <ModalRequerimento ref="refModalRequerimento" />
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, router } from "@inertiajs/vue3";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import Table from "@/Components/Table.vue";
import { ref } from "vue";
import { IconCar, IconDots, IconSettings, IconShip, IconTrain } from "@tabler/icons-vue";
import { dateTimeFormat } from "@/Utils/DateTimeUtils.js";
import ModelSearchForm from "@/Components/ModelSearchFormAllColumns.vue";
import ModalMapaGeral from "@/Pages/Licenca/ModalMapaGeral.vue";
import ModalVisualizar from "@/Pages/Licenca/ModalVisualizar.vue";
import ModalRequerimento from "./Requerimento/ModalRequerimento.vue";
import NavLink from "@/Components/NavLink.vue";
import NavLinkVoid from "@/Components/NavLinkVoid.vue";

const props = defineProps({
    licencas: Object
})

const refModalMapaGeral = ref(null);
const refModalVisualizar = ref(null);
const refModalRequerimento = ref(null);

const abrirMapaGeral = () => {
    refModalMapaGeral.value.abrirModal();
}

const dtAlerta = (data) => {
    if (data) {
        const data_licenca = new Date(data);
        const hoje = new Date();
        const dia = 1000 * 60 * 60 * 24;

        const resultado = (data_licenca - hoje) / dia;

        return Math.round(resultado);
    }
}

const abrirModalVisualizar = (item) => {
    refModalVisualizar.value.abrirModal(item);
}

const abrirModalRequerimento = (item) => {
    refModalRequerimento.value.abrirModal(item);
}

const gerenciarArquivo = (licenca, index, status) => {
    router.patch(route('licenca.gerenciar-licenca', licenca), { id: licenca.id, arquivado: status }, {
        preserveScroll: true,
        onSuccess: () => {
            licencas.value.data.splice(index, 1)
        }
    })
}

</script>
