<script setup>
import Modal from "@/Components/Modal.vue";
import {dateTimeFormat} from "@/Utils/DateTimeUtils";
import {router, useForm, usePage} from "@inertiajs/vue3";
import {ref} from "vue";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import {IconEye, IconList, IconTrash} from "@tabler/icons-vue";
import NavButton from "@/Components/NavButton.vue";
import {useToast} from 'vue-toastification';
import TabNorma from "@/Pages/Servico/ContOcorr/Execucao/Ocorrencia/TabNorma.vue";
import TabObservacao from "@/Pages/Servico/ContOcorr/Execucao/Ocorrencia/TabObservacao.vue";
import TabDescricao from "@/Pages/Servico/ContOcorr/Execucao/Ocorrencia/TabDescricao.vue";
import TabAcao from "@/Pages/Servico/ContOcorr/Execucao/Ocorrencia/TabAcao.vue";
import TabLocal from "@/Pages/Servico/ContOcorr/Execucao/Ocorrencia/TabLocal.vue";
import TabRegistro from "@/Pages/Servico/ContOcorr/Execucao/Ocorrencia/TabRegistro.vue";

const toast = useToast();

const props = defineProps({
    contrato: {type: Object},
    servico: {type: Object},
    ocorrencia: {type: Object}
})

const modalVistoriaArquivo = ref();
const vistoria = ref({});

const form_imagem = useForm({
    id_ocorrencia: null,
    id_vistoria: null,
    imagens: [],
    qtd_imagem: 0
});

const form_arquivo = useForm({
    id_ocorrencia: null,
    id_vistoria: null,
    arquivos: [],
    qtd_arquivo: 0
})

const abrirModal = (item) => {
    vistoria.value = {};

    if (item) {
        vistoria.value = item;
    }

    modalVistoriaArquivo.value.getBsModal().show();
}

const salvarImagem = () => {
    form_imagem.id_ocorrencia = props.ocorrencia.id;
    form_imagem.id_vistoria = vistoria.value.id;
    form_imagem.qtd_imagem = vistoria.value.imagens.length

    if ((form_imagem.imagens.length + form_imagem.qtd_imagem) > 5) {
        toast.info('A quantidade de imagens ultrapassa 5 quantidades');
        return false;
    }

    form_imagem.post(route('contratos.contratada.servicos.cont_ocorrencia.execucao.ocorrencia.store_vistoria_imagem', {
        contrato: props.contrato.id,
        servico: props.servico.id,
        ocorrencia: props.ocorrencia.id
    }), {
        onSuccess: () => {
            form_imagem.reset();

            vistoria.value.imagens = props.ocorrencia.vistorias.find(vist => vist.id === vistoria.value.id).imagens;
        }
    });
}

const salvarArquivo = () => {
    form_arquivo.id_ocorrencia = props.ocorrencia.id;
    form_arquivo.id_vistoria = vistoria.value.id;
    form_arquivo.qtd_arquivo = vistoria.value.arquivos.length

    if ((form_arquivo.arquivos.length + form_arquivo.qtd_arquivo) > 3) {
        toast.info('A quantidade de arquivos ultrapassa 3 quantidades');
        return false;
    }

    form_arquivo.post(route('contratos.contratada.servicos.cont_ocorrencia.execucao.ocorrencia.store_vistoria_arquivo', {
        contrato: props.contrato.id,
        servico: props.servico.id,
        ocorrencia: props.ocorrencia.id
    }), {
        onSuccess: () => {
            form_arquivo.reset();

            vistoria.value.arquivos = props.ocorrencia.vistorias.find(vist => vist.id === vistoria.value.id).arquivos;
        }
    });
}

const excluirImagem = (imagem_id) => {
    router.delete(route('contratos.contratada.servicos.cont_ocorrencia.execucao.ocorrencia.delete_vistoria_imagem', {
        contrato: props.contrato.id,
        servico: props.servico.id,
        ocorrencia: props.ocorrencia.id,
        vistoria: vistoria.value.id,
        imagem: imagem_id
    }), {
        onSuccess: () => {
            vistoria.value.imagens = props.ocorrencia.vistorias.find(vist => vist.id === vistoria.value.id).imagens;
        }
    })
}

const excluirArquivo = (arquivo_id) => {
    router.delete(route('contratos.contratada.servicos.cont_ocorrencia.execucao.ocorrencia.delete_vistoria_arquivo', {
        contrato: props.contrato.id,
        servico: props.servico.id,
        ocorrencia: props.ocorrencia.id,
        vistoria: vistoria.value.id,
        arquivo: arquivo_id
    }), {
        onSuccess: () => {
            vistoria.value.arquivos = props.ocorrencia.vistorias.find(vist => vist.id === vistoria.value.id).arquivos;
        }
    })
}

defineExpose({abrirModal});
</script>

<template>
    <Modal ref="modalVistoriaArquivo" :title="'Adicionar arquivos na vistoria ' + vistoria.nome_id"
           modal-dialog-class="modal-xl">
        <template #body>

            <div class="card-header">
                <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a href="#imagem" class="nav-link active" data-bs-toggle="tab" aria-selected="true"
                           role="tab">Imagem</a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a href="#arquivo" class="nav-link" data-bs-toggle="tab" aria-selected="false" role="tab"
                           tabindex="-1">Arquivo</a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content">
                    <div class="tab-pane active show" id="imagem" role="tabpanel">
                        <div class="row mb-4">
                            <div class="col">
                                <InputLabel value="Imagem"/>
                                <div class="row g-2">
                                    <div class="col">
                                        <input type="file" @input="form_imagem.imagens = $event.target.files"
                                               class="form-control"
                                               multiple>
                                    </div>
                                    <div class="col-auto">
                                        <NavButton @click="salvarImagem()"
                                                   type-button="success" title="Salvar"/>
                                    </div>
                                </div>
                                <InputError :message="form_imagem.errors.imagens"/>
                            </div>
                        </div>
                        <div class="row">
                            <div class="table-responsive">
                                <table class="table card-table table-bordered">
                                    <thead>
                                    <tr>
                                        <th>Arquivo</th>
                                        <th>Ação</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr v-for="imagem in vistoria.imagens" :key="imagem.id">
                                        <td>{{ imagem.nome }}</td>
                                        <td>
                                            <a class="btn btn-icon btn-info me-1" title="Visualizar" target="_blank"
                                               :href="route('contratos.contratada.servicos.cont_ocorrencia.execucao.ocorrencia.visualizar_vistoria_imagem', {contrato: contrato.id, servico: servico.id, ocorrencia:ocorrencia.id, vistoria:vistoria.id, imagem:imagem.id})">
                                                <IconEye/>
                                            </a>
                                            <NavButton @click="excluirImagem(imagem.id)"
                                                       :icon="IconTrash"
                                                       class="btn-icon"
                                                       type-button="danger"/>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="arquivo" role="tabpanel">
                        <div class="row mb-4">
                            <div class="col">
                                <InputLabel value="Arquivo"/>
                                <div class="row g-2">
                                    <div class="col">
                                        <input type="file" @input="form_arquivo.arquivos = $event.target.files"
                                               class="form-control"
                                               multiple>
                                    </div>
                                    <div class="col-auto">
                                        <NavButton @click="salvarArquivo()"
                                                   type-button="success" title="Salvar"/>
                                    </div>
                                </div>
                                <InputError :message="form_arquivo.errors.arquivos"/>
                            </div>
                        </div>
                        <div class="row">
                            <div class="table-responsive">
                                <table class="table card-table table-bordered">
                                    <thead>
                                    <tr>
                                        <th>Arquivo</th>
                                        <th>Ação</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr v-for="arquivo in vistoria.arquivos" :key="arquivo.id">
                                        <td>{{ arquivo.nome }}</td>
                                        <td>
                                            <a class="btn btn-icon btn-info me-1" title="Visualizar" target="_blank"
                                               :href="route('contratos.contratada.servicos.cont_ocorrencia.execucao.ocorrencia.visualizar_vistoria_arquivo', {contrato: contrato.id, servico: servico.id, ocorrencia:ocorrencia.id, vistoria:vistoria.id, arquivo: arquivo.id})">
                                                <IconEye/>
                                            </a>
                                            <NavButton @click="excluirArquivo(arquivo.id)"
                                                       :icon="IconTrash"
                                                       class="btn-icon"
                                                       type-button="danger"/>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>
        <template #footer>
        </template>
    </Modal>
</template>
