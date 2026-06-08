<script setup>
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import { IconFileImport } from "@tabler/icons-vue";
import { computed, ref } from "vue";

const props = defineProps({
    form: { type: Object },
    modulos: { type: Array },
    contratos: { type: Array },
    temDadosPlanilha: { type: Boolean, default: false },
    contextoImportador: {
        type: Object,
        default: () => ({}),
    },
    campanhasDisponiveis: {
        type: Array,
        default: () => [],
    },
});

const emit = defineEmits(["importar-planilha"]);

const campanhas = computed(() => {
    return props.campanhasDisponiveis?.length
        ? props.campanhasDisponiveis
        : [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];
});

const veioDoServico = computed(() => {
    return !!props.contextoImportador?.origem_servico;
});

const inputArquivoRef = ref(null);
const inputArquivoKey = ref(0);

const selecionarArquivo = ({ target }) => {
    props.form.arquivo = target.files?.[0] ?? null;
};

const limparArquivo = () => {
    props.form.arquivo = null;

    if (inputArquivoRef.value) {
        inputArquivoRef.value.value = "";
    }

    inputArquivoKey.value++;
};

const importarPlanilha = () => {
    emit("importar-planilha");
};

defineExpose({
    limparArquivo,
});
</script>
<template>
    <div class="card">
        <div class="card-header">
            <h3 class="my-0">Informações Gerais</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <InputLabel for="modulo_id">
                        <span>Módulo <span class="text-danger">*</span></span>
                    </InputLabel>
                    <v-select v-model="form.modulo_id" :options="modulos" :reduce="option => option.id" label="nome"
                        :disabled="[2, 4].includes(form.status) || veioDoServico" />
                    <InputError :message="form.errors.modulo_id" />
                </div>
                <div class="col-lg-4 mb-4">
                    <InputLabel for="mes_ano_referencia">
                        <span>Referência (Mês/Ano) <span class="text-danger">*</span></span>
                    </InputLabel>
                    <input type="text" id="mes_ano_referencia" class="form-control" v-model="form.mes_ano_referencia"
                        maxlength="7" placeholder="MM/AAAA" :disabled="[2, 4].includes(form.status)" />
                    <InputError :message="form.errors.mes_ano_referencia" />
                </div>
                <div class="col-lg-4 mb-4">
                    <InputLabel for="campanha">
                        <span>Campanha <span class="text-danger">*</span></span>
                    </InputLabel>
                    <v-select v-model="form.campanha" :options="campanhas" :disabled="[2, 4].includes(form.status)" />
                    <InputError :message="form.errors.campanha" />
                </div>
                <div class="col-lg-4 mb-4">
                    <InputLabel for="contrato_id">
                        <span>Contrato <span class="text-danger">*</span></span>
                    </InputLabel>
                    <v-select v-model="form.contrato_id" :options="contratos" :reduce="option => option.id"
                        label="numero_contrato" :disabled="[2, 4].includes(form.status) || veioDoServico" />
                    <InputError :message="form.errors.contrato_id" />
                </div>
                <div class="col-lg-8 mb-4">
                    <InputLabel for="upload_arquivo">
                        <span>Upload da Planilha (.csv/.xlsx) <span class="text-danger">*</span></span>
                    </InputLabel>

                    <div class="input-group">
                        <input :key="inputArquivoKey" ref="inputArquivoRef" type="file" id="upload_arquivo"
                            @change="selecionarArquivo" class="form-control" accept=".xlsx,.csv"
                            :disabled="[2, 4].includes(form.status) || temDadosPlanilha" />

                        <button type="button" class="btn btn-primary"
                            :disabled="[2, 4].includes(form.status) || temDadosPlanilha || form.processing || !form.arquivo"
                            @click="importarPlanilha" title="Importar planilha">
                            <IconFileImport class="me-1" :size="18" />
                            Importar
                        </button>
                    </div>

                    <small v-if="temDadosPlanilha" class="text-warning d-block mt-1">
                        Já existem dados importados para esta planilha. Para importar novamente, exclua os dados atuais
                        no botão "Excluir dados da Planilha".
                    </small>

                    <InputError :message="form.errors.arquivo" />
                </div>
            </div>
        </div>
    </div>
</template>
