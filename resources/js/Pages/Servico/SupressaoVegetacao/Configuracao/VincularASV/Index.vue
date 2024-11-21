<script setup>
import {Head, Link, useForm} from "@inertiajs/vue3";
import {IconEye, IconFile, IconTrash} from "@tabler/icons-vue";
import Table from "@/Components/Table.vue";
import Navbar from "../../Components/Navbar.vue";
import NavButton from "@/Components/NavButton.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import LinkConfirmation from "@/Components/LinkConfirmation.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import ModalVisualizarASV from "./Components/ModalVisualizarASV.vue";
import {ref, watch} from "vue";
import {dateTimeFormat} from "@/Utils/DateTimeUtils.js";
import {useToast} from "vue-toastification";

const props = defineProps({
    data: {type: Object},
    contrato: {type: Object},
    servico: {type: Object},
    licencas: {type: Object},
    aprovacao: {type: Object}
});

const form = useForm({
    licenca_id: null,
    servico_id: props.servico.id
})

const toast = useToast();

watch(() => form.licenca_id, (value) => {
    if (!value) return;
    form.post(route('contratos.contratada.servicos.supressao-vegetacao.configuracao.vincular-asv.vincular'), {
        preserveState: false,
        onError: (error) => {
            toast.error(error.licenca_id);
        },
        onSuccess: () => {
            toast.success('Licença vinculado com sucesso!');
        }
    });
});


const modalVisualizarASVRef = ref();

const abrirModalVisualizar = (item) => {
    modalVisualizarASVRef.value.abrirModal(item);
}

const ap = (ap) => {
    if (!ap?.fk_status) {
        return true;
    }
    return ap?.fk_status === 2;
}

</script>

<template>

    <Head title="Vincular ASV"/>

    <AuthenticatedLayout>

        <template #header>
            <div class="w-100 d-flex justify-content-between">
                <Breadcrumb class="align-self-center" :links="[
                    { route: route('contratos.gestao.listagem', contrato.tipo_contrato), label: `Gestão de Contratos` },
                    { route: '#', label: contrato.contratada }
                ]"/>
                <Link class="btn btn-dark"
                      :href="route('contratos.contratada.servicos.index', { contrato: contrato.id })">
                    Voltar
                </Link>
            </div>
        </template>

        <Navbar :contrato="contrato" :servico="servico">
            <template #body>

                <div class="row justify-content-between align-items-center" v-if="ap(aprovacao)">
                    <div class="col-5 mb-2">
                        <v-select :options="licencas" label="numero_licenca" v-model="form.licenca_id"
                                  :reduce="r => r.id">
                            <template v-slot:option="option">
                                {{ option.numero_licenca }} - {{ option.emissor }} - {{ option.tipo.sigla }}
                            </template>
                            <template #no-options="{}">
                                Nenhum registro encontrado.
                            </template>
                        </v-select>
                    </div>
                </div>

                <Table
                    :columns="['Número ASV', 'Data de emissão', 'Data da validade', 'Volume (m³)', 'Área em App', 'Área fora de App', 'Área total', 'Ação']"
                    :records="data" table-class="table-hover">
                    <template #body="{ item }">
                        <tr>
                            <td class="text-center">{{ item.numero_licenca ?? '-' }}</td>
                            <td class="text-center">{{
                                    item.data_emissao ? dateTimeFormat(item.data_emissao) : '-'
                                }}
                            </td>
                            <td class="text-center">{{ item.vencimento ? dateTimeFormat(item.vencimento) : '-' }}</td>
                            <td class="text-center">{{ item.volume ?? '-' }}</td>
                            <td class="text-center">{{ item.in_app ?? '-' }}</td>
                            <td class="text-center">{{ item.out_app ?? '-' }}</td>
                            <td class="text-center">{{ item.area_ha ?? '-' }}</td>
                            <td>
                                <div class="d-flex justify-content-center">
                                    <NavButton @click="abrirModalVisualizar(item)" type-button="info" class="btn-icon"
                                               :icon="IconEye"/>
                                    <NavButton v-if="item.documento === null" type-button="primary" class="btn-icon"
                                               :icon="IconFile" disabled/>
                                    <a v-else class="btn btn-primary btn-icon me-1" :href="item.documento?.caminho">
                                        <IconFile/>
                                    </a>
                                    <LinkConfirmation v-if="ap(aprovacao)" v-slot="confirmation"
                                                      :options="{ text: 'Você deseja remover o vínculo?' }">
                                        <Link :onBefore="confirmation.show"
                                              :href="route('contratos.contratada.servicos.supressao-vegetacao.configuracao.vincular-asv.delete', { contrato: contrato.id, servico: servico.id, licenca: item.id })"
                                              as="button" method="delete" type="button" class="btn btn-icon btn-danger">
                                            <IconTrash/>
                                        </Link>
                                    </LinkConfirmation>
                                </div>
                            </td>
                        </tr>
                    </template>
                </Table>
            </template>
        </Navbar>
        <ModalVisualizarASV ref="modalVisualizarASVRef"/>
    </AuthenticatedLayout>

</template>
