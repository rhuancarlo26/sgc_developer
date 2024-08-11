<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import Navbar from "../../Navbar.vue";
import {Head, Link, router, useForm} from "@inertiajs/vue3";
import ModelSearchFormAllColumns from "@/Components/ModelSearchFormAllColumns.vue";
import Table from "@/Components/Table.vue";
import NavButton from "@/Components/NavButton.vue";
import LinkConfirmation from "@/Components/LinkConfirmation.vue";
import {ref, watch} from "vue";
import {IconEye} from "@tabler/icons-vue";
import {IconPlus} from "@tabler/icons-vue";
import {IconPencil} from "@tabler/icons-vue";
import {IconTrash} from "@tabler/icons-vue";
import NavLink from "@/Components/NavLink.vue";
import {IconMap} from "@tabler/icons-vue";
import {dateTimeFormat} from "@/Utils/DateTimeUtils";
import Modal from "@/Components/Modal.vue";
import {usePage} from "@inertiajs/vue3";
import {IconDeviceFloppy} from "@tabler/icons-vue";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import {useToast} from 'vue-toastification';
import TabObservacao from "@/Pages/Servico/ContOcorr/Execucao/Ocorrencia/TabObservacao.vue";
import TabLocal from "@/Pages/Servico/ContOcorr/Execucao/Ocorrencia/TabLocal.vue";
import TabRegistro from "@/Pages/Servico/ContOcorr/Execucao/Ocorrencia/TabRegistro.vue";
import TabDescricao from "@/Pages/Servico/ContOcorr/Execucao/Ocorrencia/TabDescricao.vue";
import TabNorma from "@/Pages/Servico/ContOcorr/Execucao/Ocorrencia/TabNorma.vue";
import TabAcao from "@/Pages/Servico/ContOcorr/Execucao/Ocorrencia/TabAcao.vue";

const toast = useToast();

const modalFormVistoria = ref();
const ocorrencia = ref({});

const props = defineProps({
    contrato: {type: Object},
    servico: {type: Object},
    ocorrencias: {type: Array}
});

const form = useForm({
    id: null,
    id_ocorrencia: null,
    data_ocorrencia: null,
    nome_id: null,
    data_vistoria: null,
    corrigido: null,
    data_fim: null,
    intensidade_vistoria: null,
    tipo_vistoria: null,
    acordo_prazo: null,
    prazo_vistoria: null,
    obs_vistoria: null
});

const abrirModal = (item) => {
    ocorrencia.value = {};

    if (item) {
        ocorrencia.value = item;

        form.id_ocorrencia = ocorrencia.value.id
    }
    modalFormVistoria.value.getBsModal().show();
}

const changeFormCorrigido = () => {
    if (ocorrencia.value.tipo === 'ROA') {
        if (form.corrigido === 'Sim') {
            form.tipo_vistoria = ocorrencia.value.tipo;
            form.intensidade_vistoria = ocorrencia.value.intensidade;
        } else {
            form.intensidade_vistoria = null;
            form.tipo_vistoria = ocorrencia.value.tipo;
        }
    } else {
        if (form.corrigido === 'Sim') {
            form.tipo_vistoria = ocorrencia.value.tipo;
            form.intensidade_vistoria = ocorrencia.value.intensidade;
        } else {
            form.tipo_vistoria = ocorrencia.value.tipo;
        }
    }
}

const changeAcordoPrazo = () => {
    if (form.acordo_prazo === 'Sim') {
        form.prazo_vistoria = null;
    } else {
        form.prazo_vistoria = 'Indeterminado';
    }
}

const salvarVistoria = () => {
    const url = form.id ? 'update_vistoria' : 'store_vistoria';

    router.post(route('contratos.contratada.servicos.cont_ocorrencia.execucao.ocorrencia.' + url, {
        contrato: props.contrato.id,
        servico: props.servico.id
    }), {ocorrencia: ocorrencia.value, vistoria: form}, {
        onSuccess: () => {
            form.reset();

            ocorrencia.value.vistorias = props.ocorrencias.find(ocorr => ocorr.id === ocorrencia.value.id).vistorias;
        }
    });
}

const excluirVistoria = (vistoria_id) => {
    router.delete(route('contratos.contratada.servicos.cont_ocorrencia.execucao.ocorrencia.delete_vistoria', {
        contrato: props.contrato.id,
        servico: props.servico.id,
        vistoria: vistoria_id
    }), {
        onSuccess: () => {
            ocorrencia.value.vistorias = props.ocorrencias.find(ocorr => ocorr.id === ocorrencia.value.id).vistorias;
        }
    });
}

defineExpose({abrirModal});
</script>

<template>
    <Modal ref="modalFormVistoria" title="Cadastro de vistorias" modal-dialog-class="modal-xl">
        <template #body>

            <div class="card-header">
                <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a href="#vistoria" class="nav-link active" data-bs-toggle="tab" aria-selected="true"
                           role="tab">Vistorias</a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a href="#arquivo" class="nav-link" data-bs-toggle="tab" aria-selected="false" role="tab"
                           tabindex="-1">Arquivos</a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a href="#visualizar" class="nav-link" data-bs-toggle="tab" aria-selected="false" role="tab"
                           tabindex="-1">Visualizar</a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content">
                    <div class="tab-pane active show" id="vistoria" role="tabpanel">

                    </div>
                    <div class="tab-pane" id="arquivo" role="tabpanel">

                    </div>
                    <div class="tab-pane" id="visualizar" role="tabpanel">

                    </div>
                    <div class="tab-pane" id="norma" role="tabpanel">

                    </div>
                    <div class="tab-pane" id="registro" role="tabpanel">

                    </div>
                    <div class="tab-pane" id="observacao" role="tabpanel">

                    </div>
                </div>
            </div>
        </template>
        <template #footer>
        </template>
    </Modal>
</template>
