<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import { computed } from "vue";
import { IconDeviceFloppy } from "@tabler/icons-vue";

const props = defineProps({
    licenca: Object,
    anterior: Object,
    tipoInicial: Number,
    ufInicial: Number,
    tipos: Array,
    ufs: Array,
    usuarios: Array,
    contratos: Array,
    orgaos: Array,
    antecedenciaPadrao: Number,
});

const editando = computed(() => !!props.licenca?.id);
const responsaveisIniciais = (props.licenca?.responsaveis ?? [])
    .map(id => props.usuarios.find(u => u.id === id))
    .filter(Boolean);

const form = useForm({
    tipo_id: props.licenca?.tipo_id ?? props.tipoInicial ?? '',
    numero: props.licenca?.numero ?? '',
    orgao_emissor: props.licenca?.orgao_emissor ?? (props.tipoInicial ? 'Ibama' : ''),
    uf_id: props.licenca?.uf_id ?? props.ufInicial ?? '',
    empreendimento: props.licenca?.empreendimento ?? '',
    processo_sei: props.licenca?.processo_sei ?? '',
    sei_documento: props.licenca?.sei_documento ?? '',
    data_emissao: props.licenca?.data_emissao ?? '',
    data_validade: props.licenca?.data_validade ?? '',
    antecedencia_renovacao_dias: props.licenca?.antecedencia_renovacao_dias ?? props.antecedenciaPadrao,
    licenca_anterior_id: props.licenca?.licenca_anterior_id ?? null,
    motivo_vinculo: props.licenca?.motivo_vinculo ?? null,
    copiar_condicionantes: props.licenca?.copiar_condicionantes ?? false,
    contrato_id: props.licenca?.contrato_id ?? '',
    observacoes: props.licenca?.observacoes ?? '',
    arquivo: null,
    responsaveis: responsaveisIniciais,
});

const tipoSelecionado = computed(() => props.tipos.find(t => t.id === Number(form.tipo_id)));

const salvar = () => {
    form
        .transform(dados => ({ ...dados, responsaveis: dados.responsaveis.map(u => u.id) }))
        .post(editando.value ? route('sgc.licencas.update', props.licenca.id) : route('sgc.licencas.store'), {
            forceFormData: true,
        });
};

const titulo = computed(() => {
    if (editando.value) {
        return 'Editar licença';
    }
    if (props.anterior) {
        return form.motivo_vinculo === 'renovacao' ? 'Renovação' : 'Retificação';
    }
    return 'Nova licença';
});
</script>

<template>
    <Head :title="titulo" />

    <AuthenticatedLayout>
        <template #header>
            <div class="w-100 d-flex">
                <Breadcrumb class="align-self-center" :links="[
                    { route: route('sgc.licencas.painel'), label: 'Gestão de Prazos' },
                    { route: route('sgc.licencas.index'), label: 'Licenças e autorizações' },
                    { route: '#', label: titulo },
                ]" />
            </div>
        </template>

        <form @submit.prevent="salvar" class="card">
            <div class="card-body">
                <!-- Sucessão -->
                <div v-if="anterior" class="alert alert-info">
                    <div class="row g-2 align-items-end">
                        <div class="col-12 col-md-5">
                            Esta licença vai suceder <strong>{{ anterior.rotulo }}</strong>, que passará a "substituída".
                            Registros de prazos já lançados na anterior para as mesmas condicionantes serão trazidos para esta.
                        </div>
                        <div class="col-6 col-md-3">
                            <InputLabel value="Motivo" />
                            <select class="form-select" v-model="form.motivo_vinculo">
                                <option value="retificacao">Retificação</option>
                                <option value="renovacao">Renovação</option>
                            </select>
                            <InputError :message="form.errors.motivo_vinculo" />
                        </div>
                        <div class="col-6 col-md-4">
                            <label class="form-check">
                                <input type="checkbox" class="form-check-input" v-model="form.copiar_condicionantes">
                                <span class="form-check-label">Copiar condicionantes da anterior</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-12 col-md-5">
                        <InputLabel value="Tipo *" />
                        <select class="form-select" v-model="form.tipo_id">
                            <option value="" disabled>Selecione</option>
                            <option v-for="tipo in tipos" :key="tipo.id" :value="tipo.id">{{ tipo.sigla }} - {{ tipo.nome }}</option>
                        </select>
                        <small v-if="tipoSelecionado?.regularizacao" class="text-secondary">
                            Exibida no módulo de Regularização (relatórios periódicos por UF).
                        </small>
                        <InputError :message="form.errors.tipo_id" />
                    </div>
                    <div class="col-12 col-md-4">
                        <InputLabel value="Número *" />
                        <input class="form-control" v-model="form.numero" placeholder="Ex.: 21161207/2024">
                        <InputError :message="form.errors.numero" />
                    </div>
                    <div class="col-12 col-md-3">
                        <InputLabel value="Órgão emissor *" />
                        <input class="form-control" v-model="form.orgao_emissor" list="sgc-licencas-orgaos">
                        <datalist id="sgc-licencas-orgaos">
                            <option v-for="orgao in orgaos" :key="orgao" :value="orgao" />
                        </datalist>
                        <InputError :message="form.errors.orgao_emissor" />
                    </div>

                    <div class="col-6 col-md-2">
                        <InputLabel value="UF" />
                        <select class="form-select" v-model="form.uf_id">
                            <option value="">-</option>
                            <option v-for="uf in ufs" :key="uf.id" :value="uf.id">{{ uf.uf }}</option>
                        </select>
                        <InputError :message="form.errors.uf_id" />
                    </div>
                    <div class="col-12 col-md-6">
                        <InputLabel value="Empreendimento / abrangência" />
                        <input class="form-control" v-model="form.empreendimento" placeholder="Ex.: Rodovias federais pavimentadas de PE">
                        <InputError :message="form.errors.empreendimento" />
                    </div>
                    <div class="col-6 col-md-4">
                        <InputLabel value="Contrato vinculado" />
                        <select class="form-select" v-model="form.contrato_id">
                            <option value="">Nenhum</option>
                            <option v-for="contrato in contratos" :key="contrato.id" :value="contrato.id">{{ contrato.numero_contrato }}</option>
                        </select>
                        <InputError :message="form.errors.contrato_id" />
                    </div>

                    <div class="col-6 col-md-3">
                        <InputLabel value="Processo SEI" />
                        <input class="form-control" v-model="form.processo_sei">
                        <InputError :message="form.errors.processo_sei" />
                    </div>
                    <div class="col-6 col-md-3">
                        <InputLabel value="Nº SEI do documento" />
                        <input class="form-control" v-model="form.sei_documento">
                        <InputError :message="form.errors.sei_documento" />
                    </div>
                    <div class="col-6 col-md-2">
                        <InputLabel value="Emissão *" />
                        <input type="date" class="form-control" v-model="form.data_emissao">
                        <InputError :message="form.errors.data_emissao" />
                    </div>
                    <div class="col-6 col-md-2">
                        <InputLabel value="Validade" />
                        <input type="date" class="form-control" v-model="form.data_validade">
                        <InputError :message="form.errors.data_validade" />
                    </div>
                    <div class="col-6 col-md-2">
                        <InputLabel value="Renovar com (dias)" />
                        <input type="number" min="1" class="form-control" v-model="form.antecedencia_renovacao_dias"
                            title="Antecedência mínima para requerer a renovação antes do vencimento">
                        <InputError :message="form.errors.antecedencia_renovacao_dias" />
                    </div>

                    <div class="col-12 col-md-6">
                        <InputLabel value="Responsáveis (recebem os alertas por e-mail)" />
                        <Multiselect v-model="form.responsaveis" :options="usuarios" :multiple="true" track-by="id"
                            label="name" placeholder="Selecione os responsáveis" select-label="" deselect-label=""
                            selected-label="" :close-on-select="false">
                            <template #noResult>Nenhum usuário encontrado</template>
                        </Multiselect>
                        <InputError :message="form.errors.responsaveis" />
                    </div>
                    <div class="col-12 col-md-6">
                        <InputLabel value="Documento da licença (PDF)" />
                        <input type="file" class="form-control" @input="form.arquivo = $event.target.files[0]">
                        <small v-if="licenca?.arquivo_nome" class="text-secondary">Atual: {{ licenca.arquivo_nome }}</small>
                        <InputError :message="form.errors.arquivo" />
                    </div>

                    <div class="col-12">
                        <InputLabel value="Observações" />
                        <textarea class="form-control" rows="3" v-model="form.observacoes"></textarea>
                        <InputError :message="form.errors.observacoes" />
                    </div>
                </div>
            </div>
            <div class="card-footer d-flex justify-content-end gap-2">
                <Link :href="editando ? route('sgc.licencas.show', licenca.id) : route('sgc.licencas.index')" class="btn btn-secondary">
                    Cancelar
                </Link>
                <button class="btn btn-primary" :disabled="form.processing">
                    <IconDeviceFloppy class="me-1" /> Salvar
                </button>
            </div>
        </form>
    </AuthenticatedLayout>
</template>

<style scoped>
/* vue-multiselect com as cores do tema (claro/escuro) do Tabler */
:deep(.multiselect__tags),
:deep(.multiselect__input),
:deep(.multiselect__single),
:deep(.multiselect__content-wrapper) {
    background: var(--tblr-bg-forms);
    color: var(--tblr-body-color);
    border-color: var(--tblr-border-color);
}

:deep(.multiselect__placeholder) {
    color: var(--tblr-secondary);
}
</style>
