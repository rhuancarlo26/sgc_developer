<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import Navbar from "../../Navbar.vue";
import {Head, Link, useForm} from "@inertiajs/vue3";
import ModelSearchFormAllColumns from "@/Components/ModelSearchFormAllColumns.vue";
import Table from "@/Components/Table.vue";
import NavButton from "@/Components/NavButton.vue";
import {ref} from "vue";
import {IconDots} from "@tabler/icons-vue";
import NavLink from "@/Components/NavLink.vue";
import {dateTimeFormat} from "@/Utils/DateTimeUtils";
import ModalEnviarOcorrencia from "./ModalEnviarOcorrencia.vue";
import ModalVisualizarOcorrenciaHistorico
    from "../ModalOcorrencia/ModalVisualizarOcorrenciaHistorico.vue";
import ModalVisualizarOcorrencia
    from "../ModalOcorrencia/ModalVisualizarOcorrencia.vue";
import ModalParecerOcorrencia from "./ModalParecerOcorrencia.vue";

const modalEnviarOcorrencia = ref({});
const modalVisualizarOcorrencia = ref({});
const modalVisualizarOcorrenciaHistorico = ref({});
const modalParecerOcorrencia = ref({});

const props = defineProps({
    contrato: {type: Object},
    servico: {type: Object},
    ocorrencias: {type: Object},
    ocorrencias_fiscal: {type: Array}
});

const form = useForm({
    ocorrencias: [],
    url: 'contratos.contratada.servicos.cont_ocorrencia.execucao.controle_rnc.index'
});

const abrirModalEnviarOcorrencia = () => {
    modalEnviarOcorrencia.value.abrirModal();
}

const abrirModalOcorrencia = (item) => {
    modalVisualizarOcorrencia.value.abrirModal(item);
}

const abrirModalOcorrenciaHistorico = (item) => {
    modalVisualizarOcorrenciaHistorico.value.abrirModal(item);
}

const abrirModalParecerOcorrencia = (item) => {
    modalParecerOcorrencia.value.abrirModal(item);
}

const enviarOcorrencia = (item) => {
    form.ocorrencias = [
        item
    ];

    form.post(route('contratos.contratada.servicos.cont_ocorrencia.execucao.ocorrencia.enviar_ocorrencia', {
        contrato: props.contrato.id,
        servico: props.servico.id
    }), {
        onSuccess: () => form.reset()
    });
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
                <ModelSearchFormAllColumns :columns="['nome_id', 'intensidade', 'lote.nome_id', 'lote.empresa']">
                    <template #action>
                        <NavButton @click="abrirModalEnviarOcorrencia()" type-button="primary"
                                   title="Enviar ocorrência"/>
                    </template>
                </ModelSearchFormAllColumns>
                <Table
                    :columns="['ID Ocorrência', 'Intensidade Ocorrência', 'Data da Ocorrência', 'Data fim', 'Ocorrência anterior', 'Prazo correção', 'Lote', 'Construtora', 'Status aprovação', 'Status Atendimento', 'Envio', 'Ação']"
                    :records="ocorrencias" table-class="table-hover">
                    <template #body="{ item }">
                        <tr>
                            <td>{{ item.nome_id }}</td>
                            <td>{{ item.intensidade }}</td>
                            <td>{{ dateTimeFormat(item.data_ocorrencia) }}</td>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                            <td>{{ item.lote?.nome_id }}</td>
                            <td>{{ item.lote?.empresa }}</td>
                            <td>-</td>
                            <td>{{ item.status }}</td>
                            <td>{{ item.envio_empresa }}</td>
                            <td>
                                <button type="button" class="btn btn-icon btn-info dropdown-toggle p-2"
                                        data-bs-boundary="viewport"
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                    <IconDots/>
                                </button>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <a @click="abrirModalOcorrenciaHistorico(item)" class="dropdown-item"
                                       href="javascript:void(0)">
                                        Log de atividades
                                    </a>
                                    <a @click="abrirModalOcorrencia(item)" class="dropdown-item"
                                       href="javascript:void(0)">
                                        Visualizar
                                    </a>
                                    <NavLink title="Editar" class="dropdown-item"
                                             route-name="contratos.contratada.servicos.cont_ocorrencia.execucao.ocorrencia.create"
                                             :param="{ contrato: contrato.id, servico: servico.id, ocorrencia: item.id }"/>
                                    <NavLink
                                        route-name="contratos.contratada.servicos.cont_ocorrencia.execucao.ocorrencia.create_vistoria"
                                        :param="{ contrato: contrato.id, servico: servico.id, ocorrencia: item.id }"
                                        title="Vistoria"
                                        class="dropdown-item"/>
                                    <a @click="enviarOcorrencia(item)" class="dropdown-item"
                                       href="javascript:void(0)">
                                        Enviar ao fiscal
                                    </a>
                                    <a @click="abrirModalParecerOcorrencia(item)" class="dropdown-item"
                                       href="javascript:void(0)">
                                        Parecer fiscal
                                    </a>
                                </div>
                            </td>
                        </tr>
                    </template>
                </Table>
            </template>
        </Navbar>

        <ModalEnviarOcorrencia :contrato="contrato" :servico="servico" :ocorrencias="ocorrencias_fiscal"
                               ref="modalEnviarOcorrencia"/>
        <ModalVisualizarOcorrencia ref="modalVisualizarOcorrencia"/>
        <ModalVisualizarOcorrenciaHistorico ref="modalVisualizarOcorrenciaHistorico"/>
        <ModalParecerOcorrencia ref="modalParecerOcorrencia"/>

    </AuthenticatedLayout>
</template>
