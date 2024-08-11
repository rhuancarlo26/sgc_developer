<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import Navbar from "../../Navbar.vue";
import {Head, Link, router, useForm} from "@inertiajs/vue3";
import ModelSearchFormAllColumns from "@/Components/ModelSearchFormAllColumns.vue";
import Table from "@/Components/Table.vue";
import NavButton from "@/Components/NavButton.vue";
import {ref} from "vue";
import {IconEye, IconTrash} from "@tabler/icons-vue";
import {IconPlus} from "@tabler/icons-vue";
import {IconPencil} from "@tabler/icons-vue";
import {IconDeviceFloppy} from "@tabler/icons-vue";
import NavLink from "@/Components/NavLink.vue";
import {IconMap} from "@tabler/icons-vue";
import TabLocal from "./TabLocal.vue";
import TabDescricao from "./TabDescricao.vue";
import TabAcao from "./TabAcao.vue";
import TabNorma from "./TabNorma.vue";
import TabRegistro from "./TabRegistro.vue";
import TabObservacao from "./TabObservacao.vue";
import {dateTimeFormat} from "@/Utils/DateTimeUtils.js";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import LinkConfirmation from "@/Components/LinkConfirmation.vue";
import ModalVisualizarVistoria from "./ModalVisualizarVistoria.vue";

const modalVisualizarVistoria = ref({});

const props = defineProps({
    contrato: {type: Object},
    servico: {type: Object},
    ocorrencia: {type: Object}
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

const changeFormCorrigido = () => {
    if (props.ocorrencia.tipo === 'ROA') {
        if (form.corrigido === 'Sim') {
            form.tipo_vistoria = props.ocorrencia.tipo;
            form.intensidade_vistoria = props.ocorrencia.intensidade;
        } else {
            form.intensidade_vistoria = null;
            form.tipo_vistoria = props.ocorrencia.tipo;
        }
    } else {
        if (form.corrigido === 'Sim') {
            form.tipo_vistoria = props.ocorrencia.tipo;
            form.intensidade_vistoria = props.ocorrencia.intensidade;
        } else {
            form.tipo_vistoria = props.ocorrencia.tipo;
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
    }), {ocorrencia: props.ocorrencia, vistoria: form}, {
        onSuccess: () => {
            form.reset();
        }
    });
}

const abrirModalVisualizarVistoria = (item) => {
    modalVisualizarVistoria.value.abrirModal(item)
}

</script>
<template>

    <Head :title="`${contrato.contratada.slice(0, 10)}...`"/>

    <AuthenticatedLayout>

        <template #header>
            <div class="w-100 d-flex justify-content-between">
                <Breadcrumb class="align-self-center" :links="[
          { route: route('contratos.gestao.listagem', contrato.tipo_id), label: `Gestão de Contratos` },
          { route: '#', label: contrato.contratada }
        ]
          "/>
                <Link class="btn btn-dark"
                      :href="route('contratos.contratada.servicos.cont_ocorrencia.execucao.ocorrencia.index', { contrato: contrato.id, servico: servico.id })">
                    Voltar
                </Link>
            </div>
        </template>

        <Navbar :contrato="contrato" :servico="servico">
            <template #body>
                <div class="row mb-4">
                    <div class="col">
                        <InputLabel value="ID ocorrência" for="nome_id"/>
                        <input type="text" class="form-control" :value="ocorrencia.nome_id" disabled>
                        <InputError :message="form.errors.nome_id"/>
                    </div>
                    <div class="col">
                        <InputLabel value="Data da ocorrência" for="data_ocorrencia"/>
                        <input type="date" class="form-control" :value="ocorrencia.data_ocorrencia" disabled>
                        <InputError :message="form.errors.data_ocorrencia"/>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col">
                        <InputLabel value="ID vistoria" for="nome_id"/>
                        <input type="text" class="form-control" v-model="form.nome_id" disabled>
                        <InputError :message="form.errors.nome_id"/>
                    </div>
                    <div class="col">
                        <InputLabel value="Data da vistoria" for="data_vistoria"/>
                        <input type="date" class="form-control" v-model="form.data_vistoria">
                        <InputError :message="form.errors.data_vistoria"/>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col align-content-center">
                        <InputLabel value="Ocorrência corrigida" for="corrigido"/>
                        <div>
                            <label class="form-check form-check-inline">
                                <input @change="changeFormCorrigido()" class="form-check-input" type="radio"
                                       value="Sim"
                                       v-model="form.corrigido">
                                <span class="form-check-label">Sim</span>
                            </label>
                            <label class="form-check form-check-inline">
                                <input @change="changeFormCorrigido()" class="form-check-input" type="radio"
                                       value="Não"
                                       v-model="form.corrigido">
                                <span class="form-check-label">Não</span>
                            </label>
                        </div>
                        <InputError :message="form.errors.corrigido"/>
                    </div>
                    <div v-if="form.corrigido === 'Sim'" class="col">
                        <InputLabel value="Data fim" for="data_fim"/>
                        <input type="date" class="form-control" v-model="form.data_fim">
                        <InputError :message="form.errors.data_fim"/>
                    </div>
                </div>
                <div>
                    <div class="row mb-4">
                        <div class="col">
                            <InputLabel value="Intensidade de Ocorrência" for="intensidade_vistoria"/>
                            <select class="form-control form-select" v-model="form.intensidade_vistoria"
                                    :disabled="form.corrigido === 'Sim'">
                                <option value="Leve">Leve</option>
                                <option value="Moderada">Moderada</option>
                                <option value="Grave">Grave</option>
                            </select>
                            <InputError :message="form.errors.intensidade_vistoria"/>
                        </div>
                        <div class="col">
                            <InputLabel value="Tipo de Ocorrência" for="tipo_vistoria"/>
                            <input type="text" class="form-control" v-model="form.tipo_vistoria" disabled>
                            <InputError :message="form.errors.tipo_vistoria"/>
                        </div>
                    </div>
                    <div v-if="form.corrigido === 'Não' && ocorrencia.tipo === 'RNC'" class="row mb-4">
                        <div class="col align-content-center">
                            <InputLabel value="Realizado Acordo de Prazo" for="acordo_prazo"/>
                            <div>
                                <label class="form-check form-check-inline">
                                    <input @change="changeAcordoPrazo()" class="form-check-input" type="radio"
                                           value="Sim"
                                           v-model="form.acordo_prazo">
                                    <span class="form-check-label">Sim</span>
                                </label>
                                <label class="form-check form-check-inline">
                                    <input @change="changeAcordoPrazo()" class="form-check-input" type="radio"
                                           value="Não"
                                           v-model="form.acordo_prazo">
                                    <span class="form-check-label">Não</span>
                                </label>
                            </div>
                            <InputError :message="form.errors.acordo_prazo"/>
                        </div>
                        <div v-if="form.acordo_prazo !== null" class="col">
                            <InputLabel value="Prazo para Correção (dias)" for="prazo_vistoria"/>
                            <input type="text" class="form-control" v-model="form.prazo_vistoria"
                                   :disabled="form.acordo_prazo === 'Não'">
                            <InputError :message="form.errors.prazo_vistoria"/>
                        </div>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col">
                        <InputLabel value="Observações da Vistoria" for="obs_vistoria"/>
                        <textarea rows="6" class="form-control" v-model="form.obs_vistoria"></textarea>
                        <InputError :message="form.errors.obs_vistoria"/>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col d-flex justify-content-end">
                        <NavButton @click="salvarVistoria()" type-button="success" :icon="IconDeviceFloppy"
                                   :title="form.id ? 'Alterar' : 'Salvar'"/>
                    </div>
                </div>
                <div class="row col mb-4">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table card-table table-bordered table-hover">
                                <thead>
                                <tr>
                                    <th>ID vistoria</th>
                                    <th>Data da vistoria</th>
                                    <th>Adicionar arquivos</th>
                                    <th>Ação</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="vistoria in ocorrencia.vistorias" :key="vistoria.id">
                                    <td>{{ vistoria.nome_id }}</td>
                                    <td>{{ dateTimeFormat(vistoria.data_vistoria) }}</td>
                                    <td>
                                        <NavButton @click="abrirModalVisualizarVistoria(vistoria)" :icon="IconEye"
                                                   class="btn-icon"
                                                   type-button="primary"/>
                                    </td>
                                    <td>
                                        <NavButton @click="Object.assign(form, vistoria)" :icon="IconPencil"
                                                   class="btn-icon"
                                                   type-button="primary"/>
                                        <LinkConfirmation v-slot="confirmation"
                                                          :options="{ text: 'A remoção de um ponto será permanente.' }">
                                            <Link :onBefore="confirmation.show"
                                                  :href="route('contratos.contratada.servicos.cont_ocorrencia.execucao.ocorrencia.delete_vistoria', {contrato: props.contrato.id, servico: props.servico.id, vistoria: vistoria.id})"
                                                  as="button" method="delete" type="button"
                                                  class="btn btn-icon btn-danger">
                                                <IconTrash/>
                                            </Link>
                                        </LinkConfirmation>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </template>
        </Navbar>

        <ModalVisualizarVistoria ref="modalVisualizarVistoria"/>

    </AuthenticatedLayout>
</template>
