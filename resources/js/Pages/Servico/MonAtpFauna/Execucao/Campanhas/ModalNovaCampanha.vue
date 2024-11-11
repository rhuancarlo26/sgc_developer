<script setup>

import { onMounted, ref, watch } from "vue";
import { Link, router, useForm } from "@inertiajs/vue3";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import Modal from "@/Components/Modal.vue";
import { IconDots, IconTrash } from "@tabler/icons-vue";
import Table from "@/Components/Table.vue";
import axios from "axios";
import LinkConfirmation from "@/Components/LinkConfirmation.vue";
import { dateTimeFormat } from "../../../../../Utils/DateTimeUtils.js";

const props = defineProps({
    showAction: { type: Boolean, default: true },
    licencasVigente: { type: Array },
    configVinculacao: { type: Array },
})

const form = useForm({
    id: null,
    uf_inicial: null,
    km_inicial: null,
    latitude_inicial: null,
    longitude_inicial: null,
    uf_final: null,
    km_final: null,
    latitude_final: null,
    longitude_final: null,
    data_inicial: null,
    data_final: null,
    observacao: null,
});

const save = () => {
    form.transform((data) => ({
        ...data,
        fk_servico_licenca: props.licencasVigente[0].fk_servico_licenca,
    }));

    const onSuccess = () => {
        modalRef.value.getBsModal().hide();
        form.reset();
    }

    if (form.id !== null) {
        form.patch(route('contratos.contratada.servicos.mon_atp_fauna.execucao.campanhas.update'), {
            preserveState: true,
            onSuccess
        })
        return
    }

    form.post(route('contratos.contratada.servicos.mon_atp_fauna.execucao.campanhas.store'), {
        preserveState: true,
        onSuccess
    })
}

const tab = ref(null)
const modalRef = ref();
const abrirModal = (item = null) => {
    form.reset()
    Object.assign(form, item)
    modalRef.value.getBsModal().show();
    tab.value = 'campanhas';
}

const getAbio = async () => {
    const { data } = await axios.get(route('contratos.contratada.servicos.mon_atp_fauna.execucao.campanhas.get-abio', {
        campanha_id: form.id,
    }))
    return data
}

const getRets = async () => {
    // contratos.contratada.servicos.mon_atp_fauna.execucao.campanhas.get-rets-campanha

    const [dataCampanhas, dataVinculadas] = await Promise.all([
        axios.get(route('contratos.contratada.servicos.mon_atp_fauna.execucao.campanhas.get-rets-campanha', {
            campanha_id: form.id,
        })),
        axios.get(route('contratos.contratada.servicos.mon_atp_fauna.execucao.campanhas.get-rets', {
            campanha_id: form.id,
        }))
    ])

    return [dataCampanhas.data, dataVinculadas.data]
}

const abio = ref(null)
const rets = ref(null)
const retsVinculacao = ref([])

watch(tab, async (value) => {
    if (!form.id || !value) return;
    if (value === 'abio' && !abio.value) {
        abio.value = await getAbio();
    }
    if (value === 'rets' && !rets.value && !retsVinculacao.value.length) {
        [retsVinculacao.value, rets.value] = await getRets();
    }
}, { immediate: true });

const saveCampanhaAbio = (value) => {
    router.post(route('contratos.contratada.servicos.mon_atp_fauna.execucao.campanhas.vincular-abio'), {
        fk_config_vinculacao: value.id,
        fk_execucao_campanha: form.id
    }, {
        onSuccess: () => {
            abio.value = null;
            modalRef.value.getBsModal().hide();
        }
    });
}

const saveCampanhaRet = (value) => {
    router.post(route('contratos.contratada.servicos.mon_atp_fauna.execucao.campanhas.vincular-ret'), {
        fk_config_ret: value.id,
        fk_execucao_campanha: form.id
    }, {
        onSuccess: () => {
            rets.value = null;
            retsVinculacao.value = [];
            modalRef.value.getBsModal().hide();
        }
    });
}

onMounted(() => {
    modalRef.value.$el.addEventListener('hidden.bs.modal', () => {
        form.reset()
        tab.value = null;
        abio.value = null;
        rets.value = null;
        retsVinculacao.value = [];
    });
});

defineExpose({ abrirModal });

</script>

<template>
    <form @submit.prevent="save">
        <Modal ref="modalRef" title="Cadastro de Campanha" modal-dialog-class="modal-xl">
            <template #body>
                <div class="card">
                    <div class="card-header">
                        <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs">
                            <li class="nav-item">
                                <a href="#dados" @click="tab = 'campanhas'" :class="{ active: tab === 'campanhas' }"
                                    class="nav-link" data-bs-toggle="tab">CAMPANHAS</a>
                            </li>
                            <li v-if="form.id" class="nav-item">
                                <a href="#abio" @click="tab = 'abio'" :class="{ active: tab === 'abio' }"
                                    class="nav-link" data-bs-toggle="tab">ABIO</a>
                            </li>
                            <li v-if="form.id" class="nav-item">
                                <a href="#rets" @click="tab = 'rets'" :class="{ active: tab === 'rets' }"
                                    class="nav-link" data-bs-toggle="tab">RET's</a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            <div :class="[tab === 'campanhas' ? 'active show' : '']" class="tab-pane" id="dados">

                                <div class="row row-gap-2 mb-2">
                                    <div class="col-lg-2">
                                        <InputLabel value="ID campanha" for="campanha_id" />
                                        <input v-model="form.id" type="text" id="campanha_id" class="form-control"
                                            disabled />
                                        <InputError :message="form.errors.id" />
                                    </div>
                                    <div class="col-lg-2">
                                        <InputLabel value="Rodovia" for="campanha_rodovia" />
                                        <input :value="licencasVigente[0]?.rodovia" type="text" id="campanha_rodovia"
                                            class="form-control" disabled />
                                        <InputError :message="form.errors.dt_inicial" />
                                    </div>
                                </div>
                                <div class="row row-gap-2 mb-2">
                                    <div class="col-lg-3">
                                        <InputLabel value="UF inicial" for="uf_inicial" />
                                        <v-select :options="licencasVigente" v-model="form.uf_inicial"
                                            :get-option-label="(item) => `${item.uf} - ${item.nome_estado}`"
                                            :reduce="t => t.id_estados" :disabled="!showAction">
                                            <template #no-options="{}">
                                                Nenhum registro encontrado.
                                            </template>
                                        </v-select>
                                        <InputError :message="form.errors.uf_inicial" />
                                    </div>
                                    <div class="col-lg-3">
                                        <InputLabel value="KM inicial" for="km_inicial" />
                                        <input v-model="form.km_inicial" type="text" id="km_inicial"
                                            class="form-control" :disabled="!showAction" />
                                        <InputError :message="form.errors.km_inicial" />
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="d-flex">
                                            <InputLabel class="me-2" value="Latitude inicial" for="latitude_inicial" />
                                            <span class="position-relative top-0 small">(Graus decimais)</span>
                                        </div>
                                        <input v-model="form.latitude_inicial" type="text" id="latitude_inicial"
                                            class="form-control" :disabled="!showAction" />
                                        <InputError :message="form.errors.latitude_inicial" />
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="d-flex">
                                            <InputLabel class="me-2" value="Longitude inicial"
                                                for="longitude_inicial" />
                                            <span class="position-relative top-0 small">(Graus decimais)</span>
                                        </div>
                                        <input v-model="form.longitude_inicial" type="text" id="longitude_inicial"
                                            class="form-control" :disabled="!showAction" />
                                        <InputError :message="form.errors.longitude_inicial" />
                                    </div>
                                </div>
                                <div class="row row-gap-2 mb-2">
                                    <div class="col-lg-3">
                                        <InputLabel value="UF final" for="uf_final" />
                                        <v-select :options="licencasVigente" v-model="form.uf_final"
                                            :get-option-label="(item) => `${item.uf} - ${item.nome_estado}`"
                                            :reduce="t => t.id_estados" :disabled="!showAction">
                                            <template #no-options="{}">
                                                Nenhum registro encontrado.
                                            </template>
                                        </v-select>
                                        <InputError :message="form.errors.uf_final" />
                                    </div>
                                    <div class="col-lg-3">
                                        <InputLabel value="KM final" for="km_final" />
                                        <input v-model="form.km_final" type="text" id="km_final" class="form-control"
                                            :disabled="!showAction" />
                                        <InputError :message="form.errors.km_final" />
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="d-flex">
                                            <InputLabel class="me-2" value="Latitude final" for="latitude_final" />
                                            <span class="position-relative top-0 small">(Graus decimais)</span>
                                        </div>
                                        <input v-model="form.latitude_final" id="latitude_final" class="form-control"
                                            :disabled="!showAction" />
                                        <InputError :message="form.errors.latitude_final" />
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="d-flex">
                                            <InputLabel class="me-2" value="Longitude final" for="longitude_final" />
                                            <span class="position-relative top-0 small">(Graus decimais)</span>
                                        </div>
                                        <input v-model="form.longitude_final" type="text" id="longitude_final"
                                            class="form-control" :disabled="!showAction" />
                                        <InputError :message="form.errors.longitude_final" />
                                    </div>
                                </div>
                                <div class="row row-gap-2 mb-2">
                                    <div class="col-lg-4">
                                        <InputLabel value="Data inicial" for="data_inicial" />
                                        <input v-model="form.data_inicial" type="date" id="data_inicial"
                                            class="form-control" :disabled="!showAction" />
                                        <InputError :message="form.errors.data_inicial" />
                                    </div>
                                    <div class="col-lg-4">
                                        <InputLabel value="Data final" for="data_final" />
                                        <input v-model="form.data_final" type="date" id="data_final"
                                            class="form-control" :disabled="!showAction" />
                                        <InputError :message="form.errors.data_final" />
                                    </div>
                                    <div class="col-12">
                                        <InputLabel value="Observação" for="observacao" />
                                        <textarea v-model="form.observacao" class="form-control" id="observacao"
                                            rows="2" :disabled="!showAction"></textarea>
                                        <InputError :message="form.errors.observacao" />
                                    </div>
                                </div>
                            </div>
                            <div :class="[tab === 'abio' ? 'active show' : '']" class="tab-pane" id="abio">
                                <div class="row mb-2">
                                    <div class="col-lg-3 mb-2">
                                        <InputLabel value="ABIO vinculadas" for="uf_final" />
                                        <v-select v-if="showAction" @option:selected="saveCampanhaAbio"
                                            :options="configVinculacao" label="numero_licenca" :reduce="t => t.id"
                                            :disabled="!showAction">
                                            <template #no-options="{}">
                                                Nenhum registro encontrado.
                                            </template>
                                        </v-select>
                                    </div>
                                    <div class="col-lg-12">
                                        <Table
                                            :columns="['Nº Licença', 'Empreendimento', 'Emissor', 'Data de emissão', 'Vencimento', 'Responsável', 'Processo DNIT', ...[showAction ? 'Ação' : null]]"
                                            :records="{ data: abio, links: [] }" table-class="table-hover">
                                            <template #body="{ item }">
                                                <tr>
                                                    <td>{{ item.numero_licenca }}</td>
                                                    <td>{{ item.empreendimento }}</td>
                                                    <td>{{ item.emissor }}</td>
                                                    <td>{{ item.data_emissao }}</td>
                                                    <td>{{ item.vencimento }}</td>
                                                    <td>{{ item.fiscal }}</td>
                                                    <td>{{ item.processo_dnit }}</td>
                                                    <td v-if="showAction">
                                                        <LinkConfirmation v-slot="confirmation"
                                                            :options="{ text: 'Excluir registro?' }">
                                                            <Link :onBefore="(request) => confirmation.show({
                                                                ...request,
                                                                preserveState: true,
                                                                onSuccess: () => {
                                                                    modalRef.getBsModal().hide();
                                                                }
                                                            })" :href="route('contratos.contratada.servicos.mon_atp_fauna.execucao.campanhas.delete-abio', item.id)"
                                                                as="button" method="delete" type="button"
                                                                class="btn btn-icon btn-danger">
                                                            <IconTrash />
                                                            </Link>
                                                        </LinkConfirmation>
                                                    </td>
                                                </tr>
                                            </template>
                                        </Table>
                                    </div>
                                </div>
                            </div>
                            <div :class="[tab === 'rets' ? 'active show' : '']" class="tab-pane" id="rets">
                                <div class="row mb-2">
                                    <div class="col-lg-3 mb-2">
                                        <InputLabel value="RET's vinculadas" for="uf_final" />
                                        <v-select v-if="showAction" @option:selected="saveCampanhaRet"
                                            :options="retsVinculacao"
                                            :get-option-label="(item) => `${item.numero_licenca} - ${item.nome_arquivo}`"
                                            :reduce="t => t.id">
                                            <template #no-options="{}">
                                                Nenhum registro encontrado.
                                            </template>
                                        </v-select>
                                    </div>
                                    <div class="col-lg-12">
                                        <Table
                                            :columns="['Nome do Arquivo', 'Data de inclusão', ...[showAction ? 'Ação' : null]]"
                                            :records="{ data: rets, links: [] }" table-class="table-hover">
                                            <template #body="{ item }">
                                                <tr>
                                                    <td>{{ item.nome_arquivo }}</td>
                                                    <td>{{ dateTimeFormat(item.created_at) }}</td>
                                                    <td v-if="showAction">
                                                        <LinkConfirmation v-slot="confirmation"
                                                            :options="{ text: 'Excluir registro?' }">
                                                            <Link :onBefore="(request) => confirmation.show({
                                                                ...request,
                                                                preserveState: true,
                                                                onSuccess: () => {
                                                                    modalRef.getBsModal().hide();
                                                                }
                                                            })" :href="route('contratos.contratada.servicos.mon_atp_fauna.execucao.campanhas.delete-ret', item.id)"
                                                                as="button" method="delete" type="button"
                                                                class="btn btn-icon btn-danger">
                                                            <IconTrash />
                                                            </Link>
                                                        </LinkConfirmation>
                                                    </td>
                                                </tr>
                                            </template>
                                        </Table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
            <template #footer>
                <button @click="modalRef.getBsModal().hide()" type="button" class="btn btn-secondary">Fechar</button>
                <button v-if="showAction" type="submit" class="btn btn-success">Salvar</button>
            </template>
        </Modal>
    </form>
</template>
