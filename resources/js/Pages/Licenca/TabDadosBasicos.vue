<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import {useForm} from '@inertiajs/vue3';
import {IconArrowLeft, IconDeviceFloppy, IconSearch} from '@tabler/icons-vue';

const props = defineProps({
    tipos: Array,
    licenca: Object
});

const form = useForm({
    licenca_id: props.licenca.id,
    tipo: null,
    status: null,
    modal: null,
    numero_licenca: null,
    numero_sei: null,
    processo_dnit: null,
    data_emissao: null,
    vencimento: null,
    emissor: null,
    empreendimento: null,
    inicio_subtrecho: null,
    fim_subtrecho: null,
    soma_extensao: null,
    dias_renovacao: null,
    requerimento: null,
    renovacao: null,
    fiscal: null,
    obs_renovacao: null,
    data_publicacao: null,
    in_app: null,
    out_app: null,
    total_app: null,
    volume: null,
    sinaflor: null,
    ...props.licenca,
});

const somaExtensao = props.licenca?.segmentos.reduce((total, formSegmento) => {
    const extensao = parseFloat(formSegmento.extensao);

    if (!isNaN(extensao)) {
        total += extensao;
    }

    return total;
}, 0);

const salvarLicenca = () => {
    form.transform((data) => Object.assign({}, data))

    if (props.licenca.id) {
        form.patch(route('licenca.update', props.licenca.id));
    } else {
        form.post(route('licenca.store'));
    }
}

const atualizarDatasPrazos = () => {
    const dataVencimento = form.vencimento;
    const dias = parseInt(form.dias_renovacao);
    const dataRequerimento = dias && dataVencimento ? calcularDataPrazos(dataVencimento, dias) : '';
    const dataRenovacao = dias && dataVencimento ? calcularDataPrazos(dataRequerimento, 60) : '';

    form.requerimento = dataRequerimento
    form.renovacao = dataRenovacao
}

const calcularDataPrazos = (data, dias) => {
    const dataParam = new Date(data);
    const newDate = new Date(dataParam.getTime() - dias * 24 * 60 * 60 * 1000);
    const formatoData = newDate.toISOString().split('T')[0];

    return formatoData;
}

const calcularArea = () => {
    const inApp = parseInt(form.in_app);
    const outApp = parseInt(form.out_app);

    form.total_app = inApp + outApp;
}

</script>
<template>
    <div class="row mb-4">
        <div class="col">
            <InputLabel value="Tipo de Contrato:" for="tipo_id"/>
            <v-select :options="tipos" label="nome" v-model="form.tipo">
                <template #no-options="{}">
                    Nenhum registro encontrado.
                </template>
            </v-select>
            <InputError :message="form.errors.tipo"/>
        </div>

        <div class="col">
            <InputLabel value="Renovação/Retificação:" for="status"/>
            <select name="status" id="status" class="form-select" v-model="form.status">
                <option value="Nova"> Nova Licença</option>
                <option value="Renovada"> Renovação</option>
                <option value="Retificada"> Retificação</option>
            </select>
            <InputError :message="form.errors.status"/>
        </div>

        <div class="col" v-if="form.status !== 'Nova'">
            <InputLabel value="Busca Licença:" for="numero_licenca"/>
            <div class="d-flex">
                <input type="text" id="numero_licenca" name="numero_licenca" class="form-control me-2"
                       v-model="form.numero_licenca"/>
                <a class="btn btn-icon btn-primary" :href="route('licenca.search', form.numero_licenca)">
                    <IconSearch/>
                </a>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col">
            <InputLabel value="Modal:" for="modal"/>
            <select name="modal" id="modal" class="form-select" v-model="form.modal">
                <option :value="null"> -- SELECIONE --</option>
                <option value="1">Rodoviario</option>
                <option value="2">Aquaviario</option>
                <option value="3">Ferroviario</option>
            </select>
            <InputError :message="form.errors.modal"/>
        </div>

        <div class="col">
            <InputLabel value="N° Licença:" for="numero_licenca"/>
            <input type="text" id="numero_licenca" name="numero_licenca" class="form-control"
                   v-model="form.numero_licenca"/>
            <InputError :message="form.errors.numero_licenca"/>
        </div>

        <div class="col">
            <InputLabel value="N° SEI:" for="numero_sei"/>
            <input type="text" id="numero_sei" name="numero_sei" class="form-control" v-model="form.numero_sei"/>
            <InputError :message="form.errors.numero_sei"/>
        </div>

        <div class="col">
            <InputLabel value="Processo DNIT:" for="processo_dnit"/>
            <input type="text" id="processo_dnit" name="processo_dnit" class="form-control"
                   v-model="form.processo_dnit"/>
            <InputError :message="form.errors.processo_dnit"/>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col">
            <InputLabel value="Data da Emissão:" for="data_emissao"/>
            <input type="date" id="data_emissao" name="data_emissao" class="form-control" v-model="form.data_emissao"/>
            <InputError :message="form.errors.data_emissao"/>
        </div>

        <div class="col">
            <InputLabel value="Vencimento:" for="vencimento"/>
            <input type="date" id="vencimento" name="vencimento" class="form-control" v-model="form.vencimento"
                   @change="atualizarDatasPrazos"/>
            <InputError :message="form.errors.vencimento"/>
        </div>

        <div class="col">
            <InputLabel value="Emissor/UF:" for="emissor"/>
            <input type="text" id="emissor" name="emissor" class="form-control" v-model="form.emissor"/>
            <InputError :message="form.errors.emissor"/>
        </div>

        <div class="col">
            <InputLabel value="Processo OEMA/IBAMA:" for="processo_oema_ibama"/>
            <input type="text" id="processo_oema_ibama" name="processo_oema_ibama" class="form-control"
                   v-model="form.processo_oema_ibama"/>
            <InputError :message="form.errors.processo_oema_ibama"/>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col">
            <InputLabel value="Empreendimento:" for="empreendimento"/>
            <input type="text" id="empreendimento" name="empreendimento" class="form-control"
                   v-model="form.empreendimento"/>
            <InputError :message="form.errors.empreendimento"/>
        </div>
        <div class="col">
            <InputLabel value="Início do Sub-Trecho (PNV):" for="inicio_subtrecho"/>
            <input type="text" id="inicio_subtrecho" name="inicio_subtrecho" class="form-control"
                   v-model="form.inicio_subtrecho"/>
            <InputError :message="form.errors.inicio_subtrecho"/>
        </div>

        <div class="col">
            <InputLabel value="Fim do Sub-Trecho (PNV):" for="fim_subtrecho"/>
            <input type="text" id="fim_subtrecho" name="fim_subtrecho" class="form-control"
                   v-model="form.fim_subtrecho"/>
            <InputError :message="form.errors.fim_subtrecho"/>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col">
            <InputLabel value="Extensão:" for="extensao"/>
            <input type="text" id="soma_extensao" name="soma_extensao" class="form-control" v-model="somaExtensao"
                   disabled
                   readonly/>
            <InputError :message="form.errors.soma_extensao"/>
        </div>

        <div class="col">
            <InputLabel value="Dias para Renovação:" for="dias_renovacao"/>
            <select name="dias_renovacao" id="dias_renovacao" class="form-select" v-model="form.dias_renovacao"
                    @change="atualizarDatasPrazos" :disabled="form.vencimento == null || form.vencimento == ''">
                <option :value="null"> -- SELECIONE --</option>
                <option value="30">30</option>
                <option value="60">60</option>
                <option value="90">90</option>
                <option value="120">120</option>
            </select>
            <InputError :message="form.errors.dias_renovacao"/>
        </div>

        <div class="col">
            <InputLabel value="Prazo Final Para Requerimento:" for="requerimento"/>
            <input type="date" id="requerimento" name="requerimento" class="form-control" v-model="form.requerimento"
                   disabled
                   readonly/>
            <InputError :message="form.errors.requerimento"/>
        </div>

        <div class="col">
            <InputLabel value="Para Renovação:" for="renovacao"/>
            <input type="date" id="renovacao" name="renovacao" class="form-control" v-model="form.renovacao" disabled
                   readonly/>
            <InputError :message="form.errors.renovacao"/>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col">
            <InputLabel value="Fiscal/Responsável:" for="fiscal"/>
            <input type="text" id="fiscal" name="fiscal" class="form-control" v-model="form.fiscal"/>
            <InputError :message="form.errors.fiscal"/>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col">
            <InputLabel value="Observação:" for="obs_renovacao"/>
            <textarea name="obs_renovacao" id="obs_renovacao" class="form-control" rows="5"
                      v-model="form.obs_renovacao"></textarea>
            <InputError :message="form.errors.obs_renovacao"/>
        </div>
    </div>

    <div class="row mb-4" v-if="form.tipo?.id == 3">
        <div class="col">
            <InputLabel value="Área em APP(ha):" for="in_app"/>
            <input type="number" id="in_app" name="in_app" class="form-control" v-model="form.in_app"/>
            <InputError :message="form.errors.in_app"/>
        </div>

        <div class="col">
            <InputLabel value="Área fora APP(ha):" for="out_app"/>
            <input type="number" id="out_app" name="out_app" class="form-control" v-model="form.out_app"
                   @change="calcularArea"/>
            <InputError :message="form.errors.out_app"/>
        </div>

        <div class="col">
            <InputLabel value="Área Total(ha):" for="total_app"/>
            <input type="number" id="total_app" name="total_app" class="form-control" v-model="form.total_app" disabled
                   readonly/>
            <InputError :message="form.errors.total_app"/>
        </div>

        <div class="col">
            <InputLabel value="Volume(m³):" for="volume"/>
            <input type="number" id="volume" name="volume" class="form-control" v-model="form.volume"/>
            <InputError :message="form.errors.volume"/>
        </div>

        <div class="col-3">
            <InputLabel value="Sinaflor:" for="sinaflor"/>
            <input type="text" id="sinaflor" name="sinaflor" class="form-control" v-model="form.sinaflor"/>
            <InputError :message="form.errors.sinaflor"/>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-3">
            <InputLabel value="Data da Publicação:" for="data_publicacao"/>
            <input type="date" id="data_publicacao" name="data_publicacao" class="form-control"
                   v-model="form.data_publicacao"/>
            <InputError :message="form.errors.data_publicacao"/>
        </div>
    </div>

    <div class="row mb-4">
        <div class="d-flex justify-content-end">
            <a class="btn btn-secondary me-2" :href="route('licenca.index')">
                <IconArrowLeft class="me-2"/>
                Voltar
            </a>
            <a class="btn btn-primary" @click="salvarLicenca()">
                <IconDeviceFloppy class="me-2"/>
                {{ licenca.id ? 'Editar' : 'Salvar' }}
            </a>
        </div>
    </div>
</template>
