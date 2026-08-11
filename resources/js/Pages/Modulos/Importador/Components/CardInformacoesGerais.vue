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
    licencas: {
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

const podeAlterarPlanilha = computed(() => {
    return [1, 3, null, undefined].includes(
        props.form.status === null || props.form.status === undefined ? props.form.status : Number(props.form.status)
    );
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

const modulosFormatados = computed(() => {
    return props.modulos.map((modulo) => {
        const ehPmqa = modulo?.pmqa === true || Number(modulo?.pmqa) === 1;

        return {
            ...modulo,
            nome_formatado: ehPmqa
                ? `${modulo.nome} | ${modulo?.contrato?.numero_contrato ?? "Contrato não informado"}`
                : modulo.nome,
        };
    });
});

const pegarIdDaLicenca = (licenca) => {
    return Number(licenca?.id ?? licenca);
};

const licencasSelecionadas = computed(() => {
    const idsSelecionados = (props.form.licencas ?? [])
        .map((licenca) => pegarIdDaLicenca(licenca))
        .filter((id) => !Number.isNaN(id));

    return props.licencas.filter((licenca) => {
        return idsSelecionados.includes(Number(licenca.id));
    });
});

const colunasFiltroLicenca = "tipo_rel.sigla,numero_licenca,empreendimento,data_emissao,status,vencimento,processo_dnit";

const linkLicenca = (licenca) => {
    return route("licenca.index", {
        columns: colunasFiltroLicenca,
        value: licenca.numero_licenca,
    });
};

const formatarData = (data) => {
    if (!data) {
        return "-";
    }

    const partes = String(data).substring(0, 10).split("-");

    if (partes.length !== 3) {
        return data;
    }

    return `${partes[2]}/${partes[1]}/${partes[0]}`;
};

const textoTipoLicenca = (licenca) => {
    return licenca?.tipo_rel?.sigla
        ?? licenca?.tipo_sigla
        ?? licenca?.tipo
        ?? "-";
};

const textoStatusLicenca = (licenca) => {
    return licenca?.status_formatado
        ?? licenca?.status
        ?? "-";
};

const textoEmpreendimentoLicenca = (licenca) => {
    if (licenca?.empreendimento) {
        return licenca.empreendimento;
    }

    const trecho = [
        licenca?.inicio_subtrecho,
        licenca?.fim_subtrecho,
    ].filter(Boolean).join(" a ");

    return trecho || "-";
};

const textoEmissorLicenca = (licenca) => {
    return licenca?.emissor
        ?? licenca?.emissor_rel?.nome
        ?? licenca?.orgao_emissor?.nome
        ?? "-";
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
                    <v-select v-model="form.modulo_id" :options="modulosFormatados" :reduce="option => option.id"
                        label="nome_formatado" :disabled="[2, 4].includes(form.status) || veioDoServico" />
                    <InputError :message="form.errors.modulo_id" />
                </div>
                <div class="col-lg-2 mb-4">
                    <InputLabel for="mes_ano_referencia">
                        <span>Referência (Mês/Ano) <span class="text-danger">*</span></span>
                    </InputLabel>
                    <input type="text" id="mes_ano_referencia" class="form-control" v-model="form.mes_ano_referencia"
                        maxlength="7" placeholder="MM/AAAA" :disabled="[2, 4].includes(form.status)" />
                    <InputError :message="form.errors.mes_ano_referencia" />
                </div>
                <div class="col-lg-2 mb-4">
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
                <div class="col-lg-6 mb-4">
                    <InputLabel for="licencas">
                        <span>Licenciamento</span>
                    </InputLabel>

                    <v-select v-model="form.licencas" :options="licencas" :reduce="option => option.id"
                        label="numero_licenca" multiple placeholder="Selecione uma ou mais licenças"
                        :disabled="[2, 4].includes(form.status)">
                        <template #no-options>
                            Nenhuma licença encontrada.
                        </template>
                    </v-select>

                    <InputError :message="form.errors.licencas" />
                </div>

                <div class="col-lg-6 mb-4">
                    <InputLabel for="upload_arquivo">
                        <span>Upload da Planilha (.csv/.xlsx) <span class="text-danger">*</span></span>
                    </InputLabel>

                    <div class="input-group">
                        <input :key="inputArquivoKey" ref="inputArquivoRef" type="file" id="upload_arquivo"
                            @change="selecionarArquivo" class="form-control" accept=".xlsx,.csv"
                            :disabled="!podeAlterarPlanilha || temDadosPlanilha" />

                        <button type="button" class="btn btn-primary"
                            :disabled="!podeAlterarPlanilha || temDadosPlanilha || form.processing || !form.arquivo"
                            @click="importarPlanilha" title="Importar planilha">
                            <IconFileImport class="me-1" :size="18" />
                            Importar
                        </button>
                    </div>

                    <small v-if="temDadosPlanilha && podeAlterarPlanilha" class="text-warning d-block mt-1">
                        Já existem dados importados para esta planilha. Para importar novamente, exclua os dados atuais
                        no botão "Excluir dados da Planilha".
                    </small>

                    <small v-else-if="temDadosPlanilha" class="text-muted d-block mt-1">
                        A planilha fica bloqueada durante a análise fiscal e após aprovação.
                    </small>

                    <InputError :message="form.errors.arquivo" />
                </div>

                <h3 class="mt-2 mb-3">Tabela de Licenças Selecionadas</h3>

                <div v-if="licencasSelecionadas.length" class="col-12 mb-4">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover mb-0">
                            <thead>
                                <tr>
                                    <th class="text-center">Tipo</th>
                                    <th class="text-center">Nº Licença</th>
                                    <th class="text-center">Empreendimento</th>
                                    <th class="text-center">Emissor</th>
                                    <th class="text-center">Data da emissão</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Vencimento</th>
                                    <th class="text-center">Processo DNIT</th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr v-for="licenca in licencasSelecionadas" :key="licenca.id">
                                    <td class="text-center">
                                        {{ textoTipoLicenca(licenca) }}
                                    </td>

                                    <td class="text-center">
                                        <a :href="linkLicenca(licenca)" target="_blank"
                                            class="text-primary text-decoration-underline fw-bold"
                                            title="Abrir licença em nova aba">
                                            {{ licenca.numero_licenca }}
                                        </a>
                                    </td>

                                    <td>
                                        {{ textoEmpreendimentoLicenca(licenca) }}
                                    </td>

                                    <td>
                                        {{ textoEmissorLicenca(licenca) }}
                                    </td>

                                    <td class="text-center">
                                        {{ formatarData(licenca.data_emissao) }}
                                    </td>

                                    <td class="text-center">
                                        {{ textoStatusLicenca(licenca) }}
                                    </td>

                                    <td class="text-center">
                                        {{ formatarData(licenca.vencimento) }}
                                    </td>

                                    <td style="text-align: center;">
                                        {{ licenca.processo_dnit ?? '-' }}
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
