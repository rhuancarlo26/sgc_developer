<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import Navbar from "../Navbar.vue";
import NavButton from "@/Components/NavButton.vue";
import ModelSearchFormAllColumns from "@/Components/ModelSearchFormAllColumns.vue";
import Table from "@/Components/Table.vue";
import ModalNovoRelatorio from "./ModalNovoRelatorio.vue";
import {ref} from "vue";
import {IconDots} from "@tabler/icons-vue";
import {Head, router} from '@inertiajs/vue3';
import ModalConclusao from "./ModalConclusao.vue";
import ModalRelatorio from "./ModalRelatorio.vue";
import LinkConfirmation from "@/Components/LinkConfirmation.vue";

const props = defineProps({
    contrato: {type: Object},
    servico: {type: Object},
    data: {type: Object},
    resultados: {type: Array},
});

const modalNovoRelatorio = ref({});

const abrirModalNovoRelatorio = (item) => {
    modalNovoRelatorio.value.abrirModal(item);
}

const modalConclusaoRef = ref({});
const abrirModalConclusao = (item) => {
    modalConclusaoRef.value.abrirModal(item)
}

const modalRelatorioRef = ref();
const modalRelatorio = (item) => {
    modalRelatorioRef.value.abrirModal(item)
}

const linkConfirmationRef = ref()
const deleteRelatorio = (id) => {
    linkConfirmationRef.value.show(() => {
        router.delete(route('contratos.contratada.servicos.mon_atp_fauna.relatorios.delete', id))
    })
}

const linkConfirmationFiscalRef = ref()
const enviarRelatorioFiscal = (item) => {
    linkConfirmationFiscalRef.value.show(() => {
        router.post(route('contratos.contratada.servicos.mon_atp_fauna.relatorios.fiscal'), item)
    })
}

const  gerarRelatorio = (r) => {
    window.open(`${window.location.host}/atropelamento-fauna/relatorio/pdf/${r.id}/${r.fk_resultado}`);
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
                        <NavButton @click="abrirModalNovoRelatorio()" type-button="info" title="Novo Relatorio"/>
                    </template>
                </ModelSearchFormAllColumns>

                <Table
                    :columns="['Nome', 'Sobre o relatório', 'Status', 'Ação']"
                    :records="data" table-class="table-hover">
                    <template #body="{ item }">
                        <tr>
                            <td>{{ item.nome_relatorio }}</td>
                            <td>{{ item.sobre_relatorio }}</td>
                            <td>
                                <span v-if="item.fk_status === 1"
                                      class="shadow-none badge badge-primary">Em confecção</span>
                                <span v-if="item.fk_status === 2"
                                      class="shadow-none badge badge-warning">Em análise</span>
                                <span v-if="item.fk_status === 3"
                                      class="shadow-none badge badge-primary">Aprovado</span>
                                <span v-if="item.fk_status === 4"
                                      class="shadow-none badge badge-danger">Pendente</span>
                            </td>
                            <td>
                                <button type="button" class="btn btn-icon btn-info dropdown-toggle p-2"
                                        data-bs-boundary="viewport"
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                    <IconDots/>
                                </button>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <a class="dropdown-item" href="#" @click="abrirModalConclusao(item)"
                                       v-if="item.fk_status === 1 || item.fk_status === 4">
                                        Conclusão
                                    </a>
                                    <a class="dropdown-item" @click="modalRelatorio(item)" href="#" data-toggle="modal"
                                       data-target="#visualizarRelatorio">
                                        Visualizar relatório
                                    </a>
                                    <a class="dropdown-item" href="#" @click="abrirModalNovoRelatorio(item, item.fk_resultado)"
                                       v-if="item.fk_status === 1 || item.fk_status === 4">
                                        Editar
                                    </a>
                                    <a class="dropdown-item" href="#" @click="deleteRelatorio(item.id)"
                                       v-if="item.fk_status === 1 || item.fk_status === 4">
                                        Excluir
                                    </a>
                                    <a @click="enviarRelatorioFiscal(item)" class="dropdown-item" href="#"
                                       v-if="item.fk_status === 1 || item.fk_status === 4">
                                        Enviar para o fiscal
                                    </a>
                                    <a class="dropdown-item" @click="gerarRelatorio(item)" href="javascript:void(0);">
                                        Exportar relatório
                                    </a>
                                </div>
                            </td>
                        </tr>
                    </template>
                </Table>
            </template>
        </Navbar>

        <ModalNovoRelatorio ref="modalNovoRelatorio" :resultados="resultados" :servico="servico"/>
        <ModalConclusao ref="modalConclusaoRef"/>
        <LinkConfirmation ref="linkConfirmationRef" :options="{ text: 'Excluir registro?' }" />
        <LinkConfirmation ref="linkConfirmationFiscalRef" :options="{ text: 'Deseja realmente enviar esse relatório para o fiscal?' }" />
        <ModalRelatorio ref="modalRelatorioRef"/>


    </AuthenticatedLayout>
</template>
