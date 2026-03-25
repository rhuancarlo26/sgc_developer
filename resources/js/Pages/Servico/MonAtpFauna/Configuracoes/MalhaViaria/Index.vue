<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import Navbar from "../../Navbar.vue";
import NavButton from "@/Components/NavButton.vue";
import NavLink from "@/Components/NavLink.vue";
import ModelSearchFormAllColumns from "@/Components/ModelSearchFormAllColumns.vue";
import Table from "@/Components/Table.vue";
import ModalVisualizarMalhaViaria from "./ModalVisualizarMalhaViaria.vue";
import { ref } from "vue";
import { dateTimeFormat } from "@/Utils/DateTimeUtils";
import { IconDots, IconLineDashed, IconMap } from "@tabler/icons-vue";
import { Head, router } from "@inertiajs/vue3";
import ModalAdicionarShapefile from "./ModalAdicionarShapefile.vue";
import LinkConfirmation from "@/Components/LinkConfirmation.vue";

const props = defineProps({
    contrato: { type: Object },
    servico: { type: Object },
    data: { type: Object },
});

const linkConfirmationRef = ref()
const modalEnviarFiscal = () => {
    linkConfirmationRef.value.show(() => {
        router.post(route('contratos.contratada.servicos.mon_atp_fauna.configuracoes.malha_viaria.send_fiscal'), {
            servico_id: props.servico.id,
        })
    })
}

const modalAdicionaShapeFileRef = ref();
const modalAdicionaShapeFile = (item) => {
    modalAdicionaShapeFileRef.value.abrirModal(item);
}

const modalVisualizarMalhaViariaRef = ref();
const abrirModalVisualizarMalhaViaria = (item) => {
    modalVisualizarMalhaViariaRef.value.abrirModal(item);
}

const openArquivoLicenca = (item) => {
    window.open('/licenca/arquivo/' + item.chave, '_blank');
}

</script>
<template>

    <Head title="Malha Viária" />


    <AuthenticatedLayout>

        <template #header>
            <div class="w-100 d-flex justify-content-between">
                <Breadcrumb class="align-self-center" :links="[
                    { route: route('contratos.gestao.listagem', contrato.tipo_contrato), label: `Gestão de Contratos` },
                    { route: '#', label: contrato.contratada }
                ]
                    " />
                <Link class="btn btn-dark"
                    :href="route('contratos.contratada.servicos.index', { contrato: props.contrato.id })">
                Voltar
                </Link>
            </div>
        </template>

        <Navbar :contrato="contrato" :servico="servico">
            <template #body>
                <ModelSearchFormAllColumns :columns="[]">
                    <template #action
                        v-if="!servico.parecer_atropelamento || ![1, 3].includes(servico.parecer_atropelamento.fk_status)">
                        <NavButton v-if="data.data?.length" @click="modalEnviarFiscal" type-button="info"
                            title="Submeter ao fiscal" />
                    </template>
                </ModelSearchFormAllColumns>
                <!-- Listagem-->
                <Table
                    :columns="['Tipo', 'N° licença', 'Empreendimento', 'KM Inicial', 'KM Final', 'Extensão', 'Shapefile', 'Ação']"
                    :records="data" table-class="table-hover">
                    <template #body="{ item }">
                        <tr>
                            <td>{{ item.sigla }}</td>
                            <td>{{ item.numero_licenca }}</td>
                            <td>{{ item.empreendimento }}</td>
                            <td>{{ item.km_inicio }}</td>
                            <td>{{ item.km_fim }}</td>
                            <td>{{ item.extensao }}</td>
                            <td class="text-center">
                                <div v-if="item.local_shape || item.local_shape_licenca">
                                    <NavButton @click="abrirModalMapa(item.local_shape_em_app)" type-button="info"
                                        class="btn-icon" :icon="IconMap" />
                                </div>
                                <IconLineDashed v-else color="red" :size="40" />
                            </td>
                            <td>
                                <button type="button" class="btn btn-icon btn-info dropdown-toggle p-2"
                                    data-bs-boundary="viewport" data-bs-toggle="dropdown" aria-expanded="false">
                                    <IconDots />
                                </button>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <a v-if="item.arquivo_licenca" target="_blank"
                                        :href="route('licenca.documento.visualizar', { licenca: item.id_licenca })"
                                        class="dropdown-item">
                                        Visualizar
                                    </a>
                                    <template
                                        v-if="!servico.parecer_atropelamento || ![1, 3].includes(servico.parecer_atropelamento.fk_status)">
                                        <a @click="modalAdicionaShapeFile(item)" href="javascript:void(0);"
                                            class="dropdown-item">
                                            Adicionar Shapefile
                                        </a>
                                        <a @click="abrirModalVisualizarMalhaViaria(item)" href="javascript:void(0);"
                                            class="dropdown-item">
                                            Visualizar malha viária
                                        </a>
                                    </template>
                                </div>
                            </td>
                        </tr>
                    </template>
                </Table>
            </template>
        </Navbar>

        <modalVisualizarMalhaViaria ref="modalVisualizarMalhaViariaRef" />
        <ModalAdicionarShapefile ref="modalAdicionaShapeFileRef" />
        <LinkConfirmation ref="linkConfirmationRef"
            :options="{ text: 'Deseja realmente enviar a configuração para o fiscal?' }" />

    </AuthenticatedLayout>
</template>
