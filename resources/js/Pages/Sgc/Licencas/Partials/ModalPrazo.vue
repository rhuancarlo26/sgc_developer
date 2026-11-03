<script setup>
import Modal from "@/Components/Modal.vue";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import BadgeSituacao from "./BadgeSituacao.vue";
import { formatarData, hoje, textoDias } from "./utils.js";
import { router, useForm } from "@inertiajs/vue3";
import { ref } from "vue";
import { IconDownload, IconTrash } from "@tabler/icons-vue";

const props = defineProps({
    prazo: Object,
    tiposRegistro: Array,
});

const modal = ref();

const form = useForm({
    tipo: 'cumprimento', data_registro: hoje(), numero_oficio: '', numero_sei: '', descricao: '', data_limite_nova: null, arquivo: null,
});

const arquivoInput = ref();

const abrir = () => {
    confirmandoId.value = null;
    form.reset();
    form.clearErrors();
    modal.value.getBsModal().show();
};

const registrar = () => {
    form.post(route('sgc.licencas.prazos.registros.store', props.prazo.id), {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            if (arquivoInput.value) {
                arquivoInput.value.value = '';
            }
        },
    });
};

// Confirmação em dois cliques (um modal de confirmação por cima deste não funciona bem com o Bootstrap)
const confirmandoId = ref(null);

const remover = (registro) => {
    if (confirmandoId.value !== registro.id) {
        confirmandoId.value = registro.id;
        return;
    }
    router.delete(route('sgc.licencas.prazos.registros.destroy', registro.id), {
        preserveScroll: true,
        onFinish: () => confirmandoId.value = null,
    });
};

const rotuloTipo = (tipo) => props.tiposRegistro.find(t => t.value === tipo)?.label ?? tipo;
const corTipo = { cumprimento: 'success', prorrogacao: 'azure', dispensa: 'secondary', observacao: 'info' };

defineExpose({ abrir });
</script>

<template>
    <Modal ref="modal" title="Prazo" modal-dialog-class="modal-xl">
        <template #body>
            <template v-if="prazo">
                <!-- Resumo -->
                <div class="d-flex flex-wrap justify-content-between gap-2 mb-3">
                    <div>
                        <h3 class="mb-1">{{ prazo.descricao }}</h3>
                        <div class="text-secondary" v-if="prazo.periodo_inicio">
                            Período de referência: {{ formatarData(prazo.periodo_inicio) }} a {{ formatarData(prazo.periodo_fim) }}
                        </div>
                        <div class="text-secondary" v-if="prazo.data_evento">Evento em {{ formatarData(prazo.data_evento) }}</div>
                    </div>
                    <div class="text-end">
                        <BadgeSituacao :situacao="prazo.situacao" />
                        <div class="fw-bold mt-1">Data-limite: {{ formatarData(prazo.data_limite) }}</div>
                        <div class="small text-secondary" v-if="prazo.prorrogado">Original: {{ formatarData(prazo.data_limite_original) }}</div>
                        <div class="small" v-if="prazo.status === 'pendente'" :class="prazo.dias_restantes < 0 ? 'text-danger' : 'text-secondary'">
                            {{ textoDias(prazo.dias_restantes) }}
                        </div>
                        <div class="small text-success" v-if="prazo.data_cumprimento">Cumprido em {{ formatarData(prazo.data_cumprimento) }}</div>
                    </div>
                </div>

                <!-- Histórico -->
                <h4>Histórico</h4>
                <div v-if="!prazo.registros?.length" class="text-secondary mb-3">Nenhum registro lançado.</div>
                <ul class="list-group mb-3" v-if="prazo.registros?.length">
                    <li class="list-group-item" v-for="registro in prazo.registros" :key="registro.id">
                        <div class="d-flex justify-content-between gap-2">
                            <div>
                                <span class="badge me-2" :class="`bg-${corTipo[registro.tipo]}-lt`">{{ rotuloTipo(registro.tipo) }}</span>
                                <strong>{{ formatarData(registro.data) }}</strong>
                                <span v-if="registro.tipo === 'prorrogacao'" class="ms-2">
                                    {{ formatarData(registro.data_limite_anterior) }} → {{ formatarData(registro.data_limite_nova) }}
                                </span>
                                <div class="small mt-1" v-if="registro.numero_oficio || registro.numero_sei">
                                    <span v-if="registro.numero_oficio">{{ registro.numero_oficio }}</span>
                                    <span v-if="registro.numero_sei" class="ms-2 text-secondary">SEI {{ registro.numero_sei }}</span>
                                </div>
                                <div class="small mt-1" v-if="registro.descricao" style="white-space: pre-line">{{ registro.descricao }}</div>
                                <div class="small text-secondary mt-1">{{ registro.usuario }} · {{ registro.criado_em }}</div>
                            </div>
                            <div class="d-flex gap-1 align-items-start">
                                <a v-if="registro.arquivo_nome" class="btn btn-sm btn-outline-secondary" :title="registro.arquivo_nome"
                                    :href="route('sgc.licencas.prazos.registros.arquivo', registro.id)">
                                    <IconDownload size="16" />
                                </a>
                                <button type="button" class="btn btn-sm"
                                    :class="confirmandoId === registro.id ? 'btn-danger' : 'btn-outline-danger'"
                                    :title="confirmandoId === registro.id ? 'Clique novamente para excluir' : 'Excluir'"
                                    @click="remover(registro)">
                                    <IconTrash size="16" />
                                    <span v-if="confirmandoId === registro.id" class="ms-1">Confirmar</span>
                                </button>
                            </div>
                        </div>
                    </li>
                </ul>

                <!-- Novo registro -->
                <h4>Novo registro</h4>
                <form id="form-registro" class="row g-3" @submit.prevent="registrar">
                    <div class="col-12 col-md-4">
                        <InputLabel value="Tipo *" />
                        <select class="form-select" v-model="form.tipo">
                            <option v-for="tipo in tiposRegistro" :key="tipo.value" :value="tipo.value">{{ tipo.label }}</option>
                        </select>
                        <InputError :message="form.errors.tipo" />
                    </div>
                    <div class="col-6 col-md-2">
                        <InputLabel :value="form.tipo === 'cumprimento' ? 'Data do protocolo *' : 'Data *'" />
                        <input type="date" class="form-control" v-model="form.data_registro">
                        <InputError :message="form.errors.data_registro" />
                    </div>
                    <div class="col-6 col-md-3" v-if="form.tipo === 'prorrogacao'">
                        <InputLabel value="Nova data-limite *" />
                        <input type="date" class="form-control" v-model="form.data_limite_nova">
                        <InputError :message="form.errors.data_limite_nova" />
                    </div>
                    <div class="col-12 col-md-5" :class="{ 'col-md-3': form.tipo === 'prorrogacao' }">
                        <InputLabel value="Nº do ofício" />
                        <input class="form-control" v-model="form.numero_oficio" placeholder="Ofício nº …/CRIAM/CGMAB/DPP/DNIT SEDE">
                        <InputError :message="form.errors.numero_oficio" />
                    </div>
                    <div class="col-6 col-md-3">
                        <InputLabel value="Nº SEI" />
                        <input class="form-control" v-model="form.numero_sei">
                        <InputError :message="form.errors.numero_sei" />
                    </div>
                    <div class="col-6 col-md-9">
                        <InputLabel value="Comprovante (arquivo)" />
                        <input ref="arquivoInput" type="file" class="form-control" @input="form.arquivo = $event.target.files[0]">
                        <InputError :message="form.errors.arquivo" />
                    </div>
                    <div class="col-12">
                        <InputLabel :value="['observacao', 'dispensa'].includes(form.tipo) ? 'Descrição *' : 'Descrição'" />
                        <textarea class="form-control" rows="2" v-model="form.descricao"></textarea>
                        <InputError :message="form.errors.descricao" />
                    </div>
                </form>
            </template>
        </template>
        <template #footer>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            <button type="submit" form="form-registro" class="btn btn-primary" :disabled="form.processing">Lançar registro</button>
        </template>
    </Modal>
</template>
