<template>
    <Head title="Cadastro de Licenças" />

    <AuthenticatedLayout>

        <template #header>
            <div class="w-100 d-flex justify-content-between">
                <Breadcrumb 
                    class="align-self-center" 
                    :links="[ { route: '#', label: 'Cadastro de Licenças' } ]" 
                    />
            </div>
        </template>

        <div class="card mb-4">
            <form @submit.prevent="salvarContrato()" :disabled="form.processing">
                <!-- CARD-HEADER -->
                <div class="card-header">
                    <h3 class="my-0">Dados Básicos</h3>
                </div>
                
                <!-- CARD-BODY -->
                <div class="card-body">
                    <!-- ROW 1 -->
                    <div class="row mb-4">
                        <!-- Tipo de Contrato: -->
                        <div class="col-4">
                            <InputLabel value="Tipo de Contrato:" for="tipo" />
                            <select name="tipo" id="tipo" class="form-select" v-model="form.tipo_id">
                                <option :value="null"> -- SELECIONE -- </option>
                                <option :value="tipo.id" v-for="tipo in tipos">
                                    {{tipo.sigla}} - {{tipo.nome}}
                                </option>
                            </select>
                            <InputError :message="form.errors.tipo" />
                        </div>
                        
                        <!-- Renovação/Retificação: -->
                        <div class="col-4">
                            <InputLabel value="Renovação/Retificação:" for="status" />
                            <select name="status" id="status" class="form-select" v-model="form.status">
                                <option :value="null" disabled> Nova Licença </option>
                                <option value="Renovada">
                                    Renovação
                                </option>
                                <option value="Retificada">
                                    Retificação
                                </option>
                            </select>
                            <InputError :message="form.errors.status" />
                        </div>
                        
                        <!-- Busca Licença: -->
                        <div class="col-4" v-if="form.status != null">
                            <InputLabel value="Busca Licença:" for="numero_licenca" />
                            <div class="d-flex">
                                <input type="text" id="numero_licenca" name="numero_licenca" class="form-control me-2" v-model="form.numero_licenca" />
                                <button type="button" class="btn btn-primary" @click="bucaLicenca()">
                                    <IconSearch/>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- ROW 2 -->
                    <div class="row mb-4">
                        <!-- Modal: -->
                        <div class="col-3">
                            <InputLabel value="Modal:" for="modal" />
                                <select name="modal" id="modal" class="form-select" v-model="form.modal">
                                    <option :value="null"> -- SELECIONE -- </option>
                                    <option value="1">Rodoviario</option>
                                    <option value="2">Aquaviario</option>
                                    <option value="3">Ferroviario</option>
                                </select>
                            <InputError :message="form.errors.modal" />
                        </div>

                        <!-- N° Licença: -->
                        <div class="col-3">
                            <InputLabel value="N° Licença:" for="numero_licenca" />
                            <input type="text" id="numero_licenca" name="numero_licenca" class="form-control" v-model="form.numero_licenca" />
                            <InputError :message="form.errors.numero_licenca" />
                        </div>
                        
                        <!-- N° SEI: -->
                        <div class="col-3">
                            <InputLabel value="N° SEI:" for="numero_sei" />
                            <input type="text" id="numero_sei" name="numero_sei" class="form-control" v-model="form.numero_sei" />
                            <InputError :message="form.errors.numero_sei" />
                        </div>
                        
                        <!-- Processo DNIT: -->
                        <div class="col-3">
                            <InputLabel value="Processo DNIT:" for="processo_dnit" />
                            <input type="text" id="processo_dnit" name="processo_dnit" class="form-control" v-model="form.processo_dnit" />
                            <InputError :message="form.errors.processo_dnit" />
                        </div>
                    </div>

                    <!-- ROW 3 -->
                    <div class="row mb-4">
                        <!-- Data da Emissão: -->
                        <div class="col-3">
                            <InputLabel value="Data da Emissão:" for="data_emissao"/>
                            <input type="date" id="data_emissao" name="data_emissao" class="form-control" v-model="form.data_emissao" />
                            <InputError :message="form.errors.data_emissao" />
                        </div>

                        <!-- Vencimento: -->
                        <div class="col-3">
                            <InputLabel value="Vencimento:" for="vencimento"/>
                            <input type="date" id="vencimento" name="vencimento" class="form-control" v-model="form.vencimento" 
                            @change="atualizarDatasPrazos"/>
                            <InputError :message="form.errors.vencimento" />
                        </div>

                        <!-- Emissor: -->
                        <div class="col-3">
                            <InputLabel value="Emissor:" for="emissor" />
                            <input type="text" id="emissor" name="emissor" class="form-control" v-model="form.emissor" />
                            <InputError :message="form.errors.emissor" />
                        </div>

                        <!-- Empreendimento: -->
                        <div class="col-3">
                            <InputLabel value="Empreendimento:" for="empreendimento" />
                            <input type="text" id="empreendimento" name="empreendimento" class="form-control" v-model="form.empreendimento" />
                            <InputError :message="form.errors.empreendimento" />
                        </div>
                    </div>

                    <!-- ROW 4 -->
                    <div class="row mb-4">
                        <!-- Início do Sub-Trecho (PNV): -->
                        <div class="col-6">
                            <InputLabel value="Início do Sub-Trecho (PNV):" for="inicio_subtrecho" />
                            <input type="text" id="inicio_subtrecho" name="inicio_subtrecho" class="form-control" v-model="form.inicio_subtrecho" />
                            <InputError :message="form.errors.inicio_subtrecho" />
                        </div>

                        <!-- Fim do Sub-Trecho (PNV): -->
                        <div class="col-6">
                            <InputLabel value="Fim do Sub-Trecho (PNV):" for="fim_subtrecho" />
                            <input type="text" id="fim_subtrecho" name="fim_subtrecho" class="form-control" v-model="form.fim_subtrecho" />
                            <InputError :message="form.errors.fim_subtrecho" />
                        </div>
                    </div>

                    <!-- ROW 5 -->
                    <div class="row mb-4">
                        <!-- Extensão: -->
                        <div class="col-3">
                            <InputLabel value="Extensão:" for="extensao" />
                            <input type="text" id="extensao" name="extensao" class="form-control" v-model="form.extensao" disabled readonly/>
                            <InputError :message="form.errors.extensao" />
                        </div>

                        <!-- Dias para Renovação: -->
                        <div class="col-3">
                            <InputLabel value="Dias para Renovação:" for="dias_renovacao" />
                                <select name="dias_renovacao" id="dias_renovacao" class="form-select" 
                                v-model="form.dias_renovacao" @change="atualizarDatasPrazos"
                                :disabled="form.vencimento == null || form.vencimento == ''">
                                    <option :value="null"> -- SELECIONE -- </option>
                                    <option value="30">30</option>
                                    <option value="60">60</option>
                                    <option value="90">90</option>
                                    <option value="120">120</option>
                                </select>
                            <InputError :message="form.errors.dias_renovacao" />
                        </div>

                        <!-- Prazo Final Para Requerimento: -->
                        <div class="col-3">
                            <InputLabel value="Prazo Final Para Requerimento:" for="requerimento"/>
                            <input type="date" id="requerimento" name="requerimento" class="form-control" v-model="form.requerimento" disabled readonly/>
                            <InputError :message="form.errors.requerimento" />
                        </div>

                        <!-- Para Renovação: -->
                        <div class="col-3">
                            <InputLabel value="Para Renovação:" for="renovacao"/>
                            <input type="date" id="renovacao" name="renovacao" class="form-control" v-model="form.renovacao" disabled readonly/>
                            <InputError :message="form.errors.renovacao" />
                        </div>
                    </div>

                    <!-- ROW 6 -->
                    <div class="row mb-4">
                        <!-- Fiscal/Responsável: -->
                        <div class="col-12">
                            <InputLabel value="Fiscal/Responsável:" for="fiscal" />
                            <input type="text" id="fiscal" name="fiscal" class="form-control" v-model="form.fiscal" />
                            <InputError :message="form.errors.fiscal" />
                        </div>
                    </div>

                    <!-- ROW 7 -->
                    <div class="row mb-4">
                        <!-- Observações: -->
                        <div class="col-12">
                            <InputLabel value="Observações:" for="observacoes" />
                            <textarea name="observacoes" id="observacoes" class="form-control" rows="5" v-model="form.observacoes"></textarea>
                            <InputError :message="form.errors.observacoes" />
                        </div>
                    </div>

                    <!-- ROW 8 -->
                    <div class="row mb-4" v-if="form.tipo_id == 3">
                        <!-- Área em APP(ha): -->
                        <div class="col-2">
                            <InputLabel value="Área em APP(ha):" for="in_app" />
                            <input type="text" id="in_app" name="in_app" class="form-control" v-model="form.in_app" />
                            <InputError :message="form.errors.in_app" />
                        </div>

                        <!-- Área fora APP(ha): -->
                        <div class="col-2">
                            <InputLabel value="Área fora APP(ha):" for="out_app" />
                            <input type="text" id="out_app" name="out_app" class="form-control" v-model="form.out_app" />
                            <InputError :message="form.errors.out_app" />
                        </div>

                        <!-- Área Total (ha): -->
                        <div class="col-2">
                            <InputLabel value="Área Total(ha):" for="total_app" />
                            <input type="text" id="total_app" name="total_app" class="form-control" v-model="form.total_app" />
                            <InputError :message="form.errors.total_app" />
                        </div>

                        <!-- Volume(m³): -->
                        <div class="col-2">
                            <InputLabel value="Volume(m³):" for="volume" />
                            <input type="text" id="volume" name="volume" class="form-control" v-model="form.volume" />
                            <InputError :message="form.errors.volume" />
                        </div>
     
                        <!-- Sinaflor: -->
                        <div class="col-4">
                            <InputLabel value="Sinaflor:" for="sinaflor" />
                            <input type="text" id="sinaflor" name="sinaflor" class="form-control" v-model="form.sinaflor" />
                            <InputError :message="form.errors.sinaflor" />
                        </div>
                    </div>

                    <!-- ROW # -->
                    <div class="row mb-4">
                        <!-- Data da Publicação: -->
                        <div class="col-3">
                            <InputLabel value="Data da Publicação:" for="data_publicacao"/>
                            <input type="date" id="data_publicacao" name="data_publicacao" class="form-control" v-model="form.data_publicacao" />
                            <InputError :message="form.errors.data_publicacao" />
                        </div>
                    </div>
                </div>

                <!-- CARD-FOOTER -->
                <div class="card-footer">
                    <div class="d-flex justify-content-end">
                        <button type="button" class="btn btn-secondary me-2" @click="salvarContrato()">
                            <IconArrowLeft class="me-2"/> Voltar
                        </button>
                        <button v-if="!licenca.id" type="button" class="btn btn-primary" @click="salvarLicenca()">
                            <IconDeviceFloppy class="me-2"/> Salvar
                        </button>
                        <button v-else type="button" class="btn btn-primary" @click="salvarLicenca()">
                            <IconDeviceFloppy class="me-2"/> Editar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
    // IMPORTS
    import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
    import { Head, useForm } from "@inertiajs/vue3";
    import { IconDeviceFloppy, IconArrowLeft } from "@tabler/icons-vue";

    import InputLabel from "@/Components/InputLabel.vue";
    import InputError from "@/Components/InputError.vue";
    import Breadcrumb from "@/Components/Breadcrumb.vue";
    import { IconSearch } from "@tabler/icons-vue";

    // PROPS
    const props = defineProps({
        tipos: {
            type: Array
        },
        licenca: {
            type: Array
        },
    });

    // FORM
    const form = useForm({
        // SEM REFERENCIAS POR ENQUANTO
        obs_renovacao: null,
        nome_file_shape: null,
        local_shape: null,
        arquivo_requerimento: null,
        geo_json: null,
        
        // NULOS NA TABELA
        file_shape: null,
        arquivo_licenca: null,


        
        tipo_id: null,
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
        extensao: null,
        dias_renovacao: null,
        requerimento: null,
        renovacao: null,
        fiscal: null,
        observacoes: null,
        data_publicacao: null,

        // form.tipo == 3
        in_app: null,
        out_app: null,
        total_app: null,
        volume: null,
        sinaflor: null,

        ...props.licenca
    });
    
    // FUNCTIONS
    const salvarLicenca = () => {
        form.transform((data) => Object.assign({}, data))

        if (props.licenca.id) {
            form.patch(route('licenca.update', props.licenca.id));

            return;
        }

        form.post(route('licenca.store'));
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

    // OTHERS
</script>

<style scoped>
    .container-buttons a, .container-buttons button{
        margin: 0px 5px;
    }
</style>
