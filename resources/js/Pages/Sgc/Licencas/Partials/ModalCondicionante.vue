<script setup>
import Modal from "@/Components/Modal.vue";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import { MESES } from "./utils.js";
import { useForm } from "@inertiajs/vue3";
import { computed, ref } from "vue";

const props = defineProps({
    licencaId: { type: Number, required: true },
    tiposPrazo: Array,
    situacoes: Array,
});

const modal = ref();
const editandoId = ref(null);

const vazio = () => ({
    numero: '', grupo: '', titulo: '', descricao: '', tipo_prazo: 'sem_prazo', prazo_dias: null, data_fixa: null,
    dia_recorrencia: null, mes_recorrencia: null, relatorio_sigla: '', periodo_ano_anterior: false,
    situacao: 'em_atendimento', situacao_descricao: '', ordem: 0,
});

const form = useForm(vazio());

const usaDias = computed(() => ['dias_apos_emissao', 'dias_antes_vencimento', 'apos_evento'].includes(form.tipo_prazo));

// Modelos das condicionantes recorrentes das AOs do Ibama (PI nº 1/2020)
const modelos = {
    RCOI: { titulo: 'Relatório de Comunicação de Obras e Intervenções', tipo_prazo: 'anual', dia_recorrencia: 31, mes_recorrencia: 5, relatorio_sigla: 'RCOI', periodo_ano_anterior: false },
    RARA: { titulo: 'Relatório Anual de Regularização Ambiental', tipo_prazo: 'anual', dia_recorrencia: 30, mes_recorrencia: 6, relatorio_sigla: 'RARA', periodo_ano_anterior: true },
};

const aplicarModelo = (sigla) => Object.assign(form, modelos[sigla]);

const abrir = (condicionante = null) => {
    form.clearErrors();
    editandoId.value = condicionante?.id ?? null;
    form.defaults(condicionante
        ? Object.fromEntries(Object.keys(vazio()).map(k => [k, condicionante[k] ?? vazio()[k]]))
        : vazio());
    form.reset();
    modal.value.getBsModal().show();
};

const salvar = () => {
    const opcoes = { preserveScroll: true, onSuccess: () => modal.value.getBsModal().hide() };

    editandoId.value
        ? form.put(route('sgc.licencas.condicionantes.update', editandoId.value), opcoes)
        : form.post(route('sgc.licencas.condicionantes.store', props.licencaId), opcoes);
};

defineExpose({ abrir });
</script>

<template>
    <Modal ref="modal" :title="editandoId ? 'Editar condicionante' : 'Nova condicionante'" modal-dialog-class="modal-xl">
        <template #body>
            <form id="form-condicionante" @submit.prevent="salvar" class="row g-3">
                <div class="col-12" v-if="!editandoId">
                    <span class="text-secondary me-2">Modelos:</span>
                    <button type="button" class="btn btn-sm btn-outline-info me-2" @click="aplicarModelo('RCOI')">RCOI (até 31/05)</button>
                    <button type="button" class="btn btn-sm btn-outline-info" @click="aplicarModelo('RARA')">RARA (até 30/06, ano anterior)</button>
                </div>

                <div class="col-4 col-md-2">
                    <InputLabel value="Número *" />
                    <input class="form-control" v-model="form.numero" placeholder="2.11">
                    <InputError :message="form.errors.numero" />
                </div>
                <div class="col-8 col-md-3">
                    <InputLabel value="Grupo" />
                    <select class="form-select" v-model="form.grupo">
                        <option value="">-</option>
                        <option value="geral">Condição geral</option>
                        <option value="especifica">Condição específica</option>
                    </select>
                </div>
                <div class="col-12 col-md-7">
                    <InputLabel value="Título resumido" />
                    <input class="form-control" v-model="form.titulo" placeholder="Ex.: Relatório Anual de Regularização Ambiental">
                    <InputError :message="form.errors.titulo" />
                </div>
                <div class="col-12">
                    <InputLabel value="Texto da condicionante *" />
                    <textarea class="form-control" rows="3" v-model="form.descricao"></textarea>
                    <InputError :message="form.errors.descricao" />
                </div>

                <!-- Regra de prazo -->
                <div class="col-12 col-md-4">
                    <InputLabel value="Tipo de prazo *" />
                    <select class="form-select" v-model="form.tipo_prazo">
                        <option v-for="tipo in tiposPrazo" :key="tipo.value" :value="tipo.value">{{ tipo.label }}</option>
                    </select>
                    <InputError :message="form.errors.tipo_prazo" />
                </div>
                <div class="col-6 col-md-2" v-if="usaDias">
                    <InputLabel value="Dias *" />
                    <input type="number" min="0" class="form-control" v-model="form.prazo_dias">
                    <InputError :message="form.errors.prazo_dias" />
                </div>
                <div class="col-6 col-md-3" v-if="form.tipo_prazo === 'data_fixa'">
                    <InputLabel value="Data *" />
                    <input type="date" class="form-control" v-model="form.data_fixa">
                    <InputError :message="form.errors.data_fixa" />
                </div>
                <template v-if="form.tipo_prazo === 'anual'">
                    <div class="col-4 col-md-1">
                        <InputLabel value="Dia *" />
                        <input type="number" min="1" max="31" class="form-control" v-model="form.dia_recorrencia">
                        <InputError :message="form.errors.dia_recorrencia" />
                    </div>
                    <div class="col-8 col-md-2">
                        <InputLabel value="Mês *" />
                        <select class="form-select" v-model="form.mes_recorrencia">
                            <option v-for="(mes, i) in MESES" :key="mes" :value="i + 1">{{ mes }}</option>
                        </select>
                        <InputError :message="form.errors.mes_recorrencia" />
                    </div>
                    <div class="col-12 col-md-3 d-flex align-items-end">
                        <label class="form-check mb-2">
                            <input type="checkbox" class="form-check-input" v-model="form.periodo_ano_anterior">
                            <span class="form-check-label">Refere-se ao ano anterior (jan–dez)</span>
                        </label>
                    </div>
                </template>
                <div class="col-6 col-md-2">
                    <InputLabel value="Relatório periódico" />
                    <input class="form-control" v-model="form.relatorio_sigla" list="sgc-licencas-relatorios" placeholder="RARA, RCOI…">
                    <datalist id="sgc-licencas-relatorios">
                        <option value="RCOI" /><option value="RARA" /><option value="RCO" /><option value="RFAR" />
                    </datalist>
                    <InputError :message="form.errors.relatorio_sigla" />
                </div>

                <!-- Situação -->
                <div class="col-12 col-md-4">
                    <InputLabel value="Situação *" />
                    <select class="form-select" v-model="form.situacao">
                        <option v-for="s in situacoes" :key="s.value" :value="s.value">{{ s.label }}</option>
                    </select>
                    <InputError :message="form.errors.situacao" />
                </div>
                <div class="col-6 col-md-2">
                    <InputLabel value="Ordem" />
                    <input type="number" min="0" class="form-control" v-model="form.ordem">
                </div>
                <div class="col-12">
                    <InputLabel value="Descrição da situação (texto do RARA)" />
                    <textarea class="form-control" rows="3" v-model="form.situacao_descricao"></textarea>
                    <InputError :message="form.errors.situacao_descricao" />
                </div>
            </form>
        </template>
        <template #footer>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" form="form-condicionante" class="btn btn-primary" :disabled="form.processing">Salvar</button>
        </template>
    </Modal>
</template>
