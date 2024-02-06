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
                            <select name="tipo" id="tipo" class="form-control" v-model="form.tipo">
                                <option value="1">
                                    AA - Autorização Ambiental
                                </option>
                                <option value="2">
                                    AFF - Autorização Ambiental de Funcionamento
                                </option>
                                <option value="3">
                                    ASV - Autorização de Supressão de Vegetação
                                </option>
                                <option value="4">
                                    LAR - Licença Ambiental de Regularização
                                </option>
                                <option value="5">
                                    LAU - Licença Ambiental Única
                                </option>
                                <option value="6">
                                    LI - Licença de Instalação
                                </option>
                                <option value="7">
                                    LIO - Licença de Instalação e Operação
                                </option>
                                <option value="8">
                                    LP - Licença Prévia
                                </option>
                                <option value="9">
                                    LIP - Licença Prévia e de Instalação
                                </option>
                                <option value="10">
                                    LO - Licença de Operação
                                </option>
                                <option value="11">
                                    AAP - Autorização Ambiental Preliminar
                                </option>
                                <option value="12">
                                    ABio - Autorização de Captura, Coleta e Transporte de Material Biológico
                                </option>
                                <option value="13">
                                    ACCT - Alterada para ABio
                                </option>
                                <option value="14">
                                    DEC - DEC
                                </option>
                                <option value="15">
                                    LA - Licença Ambiental
                                </option>
                                <option value="16">
                                    LL - Licença LL
                                </option>
                                <option value="17">
                                    LS - Licença Simplificada
                                </option>
                                <option value="18">
                                    DL - Dispensa de Licenciamento
                                </option>
                                <option value="19">
                                    AAP - Autorização Ambiental Preliminar
                                </option>
                            </select>
                            <InputError :message="form.errors.tipo" />
                        </div>
                        
                        <!-- Renovação/Retificação: -->
                        <div class="col-4">
                            <InputLabel value="Renovação/Retificação:" for="status" />
                            <select name="status" id="status" class="form-control" v-model="form.status">
                                <option value="Renovada">
                                    Renovação
                                </option>
                                <option value="Retificada">
                                    Retificação
                                </option>
                            </select>
                            <InputError :message="form.errors.status" />
                        </div>
                        
                        <!-- Modal: -->
                        <div class="col-4">
                            <InputLabel value="Modal:" for="modal" />
                                <select name="modal" id="modal" class="form-control" v-model="form.modal">
                                    <option value="1">Rodoviario</option>
                                    <option value="2">Aquaviario</option>
                                    <option value="3">Ferroviario</option>
                                </select>
                            <InputError :message="form.errors.modal" />
                        </div>
                    </div>

                    <!-- ROW 2 -->
                    <div class="row mb-4">
                        <!-- N° Licença: -->
                        <div class="col-4">
                            <InputLabel value="N° Licença:" for="numero_licenca" />
                            <input type="text" id="numero_licenca" name="numero_licenca" class="form-control" v-model="form.numero_licenca" />
                            <InputError :message="form.errors.numero_licenca" />
                        </div>
                        
                        <!-- N° SEI: -->
                        <div class="col-4">
                            <InputLabel value="N° SEI:" for="numero_sei" />
                            <input type="text" id="numero_sei" name="numero_sei" class="form-control" v-model="form.numero_sei" />
                            <InputError :message="form.errors.numero_sei" />
                        </div>
                        
                        <!-- Processo DNIT: -->
                        <div class="col-4">
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
                            <input type="date" id="vencimento" name="vencimento" class="form-control" v-model="form.vencimento" />
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
                            <input type="text" id="extensao" name="extensao" class="form-control" v-model="form.extensao" />
                            <InputError :message="form.errors.extensao" />
                        </div>

                        <!-- Dias para Renovação: -->
                        <div class="col-3">
                            <InputLabel value="Dias para Renovação:" for="dias_renovacao" />
                            <input type="text" id="dias_renovacao" name="dias_renovacao" class="form-control" v-model="form.dias_renovacao" />
                            <InputError :message="form.errors.dias_renovacao" />
                        </div>

                        <!-- Para Requerimento: -->
                        <div class="col-3">
                            <InputLabel value="Para Requerimento:" for="requerimento" />
                            <input type="text" id="requerimento" name="requerimento" class="form-control" v-model="form.requerimento" />
                            <InputError :message="form.errors.requerimento" />
                        </div>

                        <!-- Para Renovação: -->
                        <div class="col-3">
                            <InputLabel value="Para Renovação:" for="renovacao" />
                            <input type="text" id="renovacao" name="renovacao" class="form-control" v-model="form.renovacao" />
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
                        <button type="button" class="btn btn-secondary me-2" @click="salvarContrato()" :disabled="form.processing">
                            <IconArrowLeft class="me-2"/> Voltar
                        </button>
                        <button type="button" class="btn btn-primary" @click="salvarContrato()" :disabled="form.processing">
                            <IconDeviceFloppy class="me-2"/> Salvar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
    import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
    import { Head, useForm } from "@inertiajs/vue3";
    import { IconDeviceFloppy, IconArrowLeft } from "@tabler/icons-vue";

    import InputLabel from "@/Components/InputLabel.vue";
    import InputError from "@/Components/InputError.vue";
    import Breadcrumb from "@/Components/Breadcrumb.vue";

    const props = defineProps({
        licencas: Object
    })

    const form = useForm({});
</script>

<style scoped>
    .container-buttons a, .container-buttons button{
        margin: 0px 5px;
    }
</style>