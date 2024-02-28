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
                    <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs" role="tablist">
                        <!-- TAB - DADOS BASICOS -->
                        <li class="nav-item" role="presentation">
                            <a href="#tab-dadosBasicos" class="nav-link active" data-bs-toggle="tab" 
                            aria-selected="true" role="tab">
                                Dados Básicos
                            </a>
                        </li>
                        <!-- TAB - SEGMENTO -->
                        <li v-if="licenca.id" class="nav-item" role="presentation">
                            <a href="#tab-segmento" class="nav-link" data-bs-toggle="tab"
                            aria-selected="false" role="tab">
                                Segmento
                            </a>
                        </li>
                         <!-- TAB - ARQUIVOS -->
                        <li v-if="licenca.id" class="nav-item" role="presentation">
                            <a href="#tab-arquivos" class="nav-link" data-bs-toggle="tab"
                                aria-selected="false" role="tab">
                                Arquivos
                            </a>
                        </li>
                    </ul>
                </div>
                
                <!-- CARD-BODY -->
                <div class="card-body">
                    <div class="tab-content">
                        <!-- TAB - DADOS BASICOS -->
                        <div id="tab-dadosBasicos" class="tab-pane active show" role="tabpanel">
                            <!-- ROW 1 -->
                            <div class="row mb-4">
                                <!-- Tipo de Contrato: -->
                                <div class="col-4">
                                    <InputLabel value="Tipo de Contrato:" for="licenca_tipo_id" />
                                    <select name="licenca_tipo_id" id="licenca_tipo_id" class="form-select" v-model="form.licenca_tipo_id">
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
                                        <a class="btn btn-primary" :href="route('licenca.search', form.numero_licenca)">
                                            <IconSearch/>
                                        </a>
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
                                    <input type="text" id="soma_extensao" name="soma_extensao" class="form-control" v-model="form.soma_extensao" disabled readonly/>
                                    <InputError :message="form.errors.soma_extensao" />
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
                            <div class="row mb-4" v-if="form.licenca_tipo_id == 3">
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

                            <!-- AREA BOTÕES -->
                            <hr>
                            <div class="row mb-4">
                                <div class="d-flex justify-content-end">
                                    <a class="btn btn-secondary me-2" href="javascript:void(0);" @click="voltarPagina()">
                                        <IconArrowLeft class="me-2"/> Voltar
                                    </a>
                                    <a v-if="!licenca.id" class="btn btn-primary" @click="salvarLicenca()">
                                        <IconDeviceFloppy class="me-2"/> Salvar
                                    </a>
                                    <a v-else class="btn btn-primary" @click="salvarLicenca()">
                                        <IconDeviceFloppy class="me-2"/> Editar
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- TAB - SEGMENTO -->
                        <div id="tab-segmento" class="tab-pane" role="tabpanel">
                            <!-- AREA BOTÕES -->
                            <div class="row mb-4">
                                <div class="d-flex justify-content-end">
                                    <a id="botaoAdicionarSegmento" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#adicionarSegmento"
                                    @click="abrirSalvarSegmento()">
                                        <IconPlus class="me-2"/> Adicionar
                                    </a>
                                </div>
                            </div>

                            <hr>

                            <!-- TABELA LICENÇA SEGMENTO -->
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th class="text-center" scope="col" v-for="column in titleTableLicencaSegmento" :key="column">
                                            {{ column }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-if="licencaSegmento.length > 0" v-for="item in licencaSegmento">
                                        <!-- BR -->
                                        <td class="text-center">
                                            {{ item.rodovia }}
                                        </td>
                                        <!-- UF INICIAL -->
                                        <td class="text-center">
                                            {{item.uf_inicial}}
                                        </td>
                                        <!-- UF FINAL -->
                                        <td class="text-center">
                                            {{ item.uf_final }}
                                        </td>
                                        <!-- KM INICIAL -->
                                        <td class="text-center">
                                            {{ item.km_inicial }}
                                        </td>
                                        <!-- KM FINAL -->
                                        <td class="text-center">
                                            {{ item.km_final }}
                                        </td>
                                        <!-- EXTENSÃO -->
                                        <td class="text-center">
                                            {{ item.extensao }}
                                        </td>
                                        <!-- Ação -->
                                        <td class="text-center" @click.stop>
                                            <span class="dropdown">
                                                <button class="btn dropdown-toggle align-text-top" data-bs-boundary="viewport"
                                                    data-bs-toggle="dropdown" aria-expanded="false">
                                                    <IconDots />
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-end" style="">
                                                    <a class="dropdown-item" @click="abrirEditarSegmento(item)" href="javascript:void(0)"
                                                    data-bs-toggle="modal" data-bs-target="#adicionarSegmento">
                                                        Editar
                                                    </a>
                                                    <a class="dropdown-item" href="javascript:void(0)"  
                                                        data-bs-toggle="modal" data-bs-target="#confirmacaoExclusaoSegmento"
                                                        @click="dataSegmentoExcluir = item">
                                                        Excluir
                                                    </a>
                                                </div>
                                            </span>
                                        </td>
                                    </tr>
                                    <tr v-else>
                                        <td :colspan="titleTableLicencaSegmento.length" class="text-center py-3">
                                            Nenhum registro encontrado
                                        </td>
                                    </tr>
                                </tbody>
                            </table>


                            <!-- MODAL -->
                            <div class="modal fade" id="adicionarSegmento" data-bs-backdrop="static" data-bs-keyboard="false" 
                                tabindex="-1" aria-labelledby="adicionarSegmentoLabel" aria-hidden="true">
                                <div class="modal-dialog modal-xl">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h1 class="modal-title" id="adicionarSegmentoLabel">
                                                {{ 
                                                    editarLicencaSegmento ? 'Editar Licença' : 'Adicionar Licença'    
                                                }}
                                            </h1>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <!-- ROW 1 - MODAL -->
                                            <div class="row mb-4">
                                                <!-- BR: -->
                                                <div class="col-4">
                                                    <InputLabel value="BR:" for="rodovia_id" />
                                                    <select name="rodovia_id" id="rodovia_id" class="form-select" v-model="formSegmento.rodovia"
                                                        @change="resetarUfs()">
                                                        <option :value="null"> -- SELECIONE -- </option>
                                                        <option :value="br" v-for="br in sortedRodovias">
                                                            {{ br }}
                                                        </option>
                                                    </select>
                                                    <InputError :message="formSegmento.errors.tipo" />
                                                </div>

                                                <!-- UF Inicial: -->
                                                <div class="col-4">
                                                    <InputLabel value="UF Inicial:" for="uf_inicial" />
                                                    <select name="uf_inicial" id="uf_inicial" class="form-select" v-model="formSegmento.uf_inicial">
                                                        <option :value="null"> -- SELECIONE -- </option>
                                                        <option :value="uf.estado" v-for="uf in brs" v-show="uf && uf.rodovia.trim() == formSegmento.rodovia">
                                                            {{ uf.estado }}
                                                        </option>
                                                    </select>
                                                    <InputError :message="formSegmento.errors.uf_inicial" />
                                                </div>

                                                <!-- UF Final: -->
                                                <div class="col-4">
                                                    <InputLabel value="UF Final:" for="uf_final_id" />
                                                    <select name="uf_final_id" id="uf_final_id" class="form-select" v-model="formSegmento.uf_final">
                                                        <option :value="null"> -- SELECIONE -- </option>
                                                        <option :value="uf.estado" v-for="uf in brs" v-show="uf && uf.rodovia.trim() == formSegmento.rodovia">
                                                            {{ uf.estado }}
                                                        </option>
                                                    </select>
                                                    <InputError :message="formSegmento.errors.uf_final_id" />
                                                </div>
                                            </div>

                                            <!-- ROW 2 - MODAL -->
                                            <div class="row mb-4">
                                                <!-- KM Inicial: -->
                                                <div class="col-4">
                                                    <InputLabel value="KM Inicial:" for="km_inicial"/>
                                                    <input type="text" id="km_inicial" name="km_inicial" class="form-control" v-model="formSegmento.km_inicial"
                                                        @change="calcularExtensao()"
                                                        />
                                                    <InputError :message="formSegmento.errors.km_inicial" />
                                                </div>

                                                <!-- KM Final: -->
                                                <div class="col-4">
                                                    <InputLabel value="KM Final:" for="km_final"/>
                                                    <input type="text" id="km_final" name="km_final" class="form-control" v-model="formSegmento.km_final"
                                                        @change="calcularExtensao()"
                                                        />
                                                    <InputError :message="formSegmento.errors.km_final" />
                                                </div>

                                                <!-- Extensão: -->
                                                <div class="col-4">
                                                    <InputLabel value="Extensão:" for="extensao"/>
                                                    <input type="text" id="extensao" name="extensao" class="form-control" v-model="formSegmento.extensao" disabled readonly/>
                                                    <InputError :message="formSegmento.errors.extensao" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <a class="btn btn-secondary" href="javascript:void(0);" data-bs-dismiss="modal">
                                                <IconX class="me-2"/> Close
                                            </a>
                                            <a 
                                                v-if="!editarLicencaSegmento"
                                                class="btn btn-primary" 
                                                @click="salvarLicencaSegmento()" 
                                                href="javascript:void(0);" 
                                                data-bs-dismiss="modal"
                                                >
                                                <IconDeviceFloppy class="me-2"/> Salvar
                                            </a>
                                            <a 
                                                v-else    
                                                class="btn btn-primary" 
                                                @click="salvarLicencaSegmento()" 
                                                href="javascript:void(0);" 
                                                data-bs-dismiss="modal"
                                                >
                                                <IconDeviceFloppy class="me-2"/> Editar
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- MODAL EXCLUIR -->
                            <div class="modal fade" id="confirmacaoExclusaoSegmento" tabindex="-1" aria-labelledby="confirmacaoExclusaoSegmentoLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="confirmacaoExclusaoSegmentoLabel">Confirmação de Exclusão</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                                        </div>
                                        <div class="modal-body">
                                            Deseja realmente excluir o Segmento?
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                Cancelar
                                            </button>
                                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal" 
                                                @click="excluirSegmento()">
                                                Excluir
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- TAB - ARQUIVOS -->
                        <div id="tab-arquivos" class="tab-pane" role="tabpanel">
                            ARQUIVOS
                        </div>
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
    import { IconDeviceFloppy, IconArrowLeft, IconDots, IconSearch, IconPlus, IconX } from "@tabler/icons-vue";
    
    import InputLabel   from "@/Components/InputLabel.vue";
    import InputError   from "@/Components/InputError.vue";
    import Breadcrumb   from "@/Components/Breadcrumb.vue";


    // PROPS
    const props = defineProps({
        tipos: {
            type: Array
        },
        brs: {
            type: Array
        },
        licenca: {
            type: Array
        },
        licencaSegmento: {
            type: Array
        },
    });

    // VARIAVEIS
    const titleTableLicencaSegmento = ['BR', 'UF Inicial', 'UF Final', 'KM Inicial', 'KM Final', 'Extensão', 'Ação'];
    let arrayBrs = props.brs;
    let uniqueRodovias = [...new Set(arrayBrs.map(br => br.rodovia.trim()))];
    let sortedRodovias = uniqueRodovias.sort();
    let dataSegmentoExcluir = [];
    let editarLicencaSegmento = false;

    // FORM
    const form = useForm({
        licenca_tipo_id: null,
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
        observacoes: null,
        data_publicacao: null,

        // form.tipo == 3
        in_app: null,
        out_app: null,
        total_app: null,
        volume: null,
        sinaflor: null,

        ...props.licenca,
    });
    
    const formSegmento = useForm({
        licenca_id: form.id,
        rodovia: null,
        uf_inicial: null,
        uf_final: null,
        km_inicial: null,
        km_final: null,
        extensao: null,
    });
    
    // FUNCTIONS
    const voltarPagina = () => {
        window.history.back();
    }

    const abrirSalvarSegmento = () => {
        formSegmento.id         = null,
        formSegmento.licenca_id = form.id,
        formSegmento.rodovia    = null,
        formSegmento.uf_inicial = null,
        formSegmento.uf_final   = null,
        formSegmento.km_inicial = null,
        formSegmento.km_final   = null,
        formSegmento.extensao   = null,

        editarLicencaSegmento   = false;
    }

    const abrirEditarSegmento = (item) => {
        formSegmento.id         = item.id,
        formSegmento.licenca_id = item.licenca_id,
        formSegmento.rodovia    = item.rodovia;
        formSegmento.uf_inicial = item.uf_inicial;
        formSegmento.uf_final   = item.uf_final;
        formSegmento.km_inicial = item.km_inicial;
        formSegmento.km_final   = item.km_final;
        formSegmento.extensao   = item.extensao;

        editarLicencaSegmento   = true;
    }

    const excluirSegmento = () => {
        form.delete(route('licenca_segmento.delete', dataSegmentoExcluir.id));
        return;
    }

    const resetarUfs = () => {
        formSegmento.uf_inicial = null;
        formSegmento.uf_final = null;
    }

    const salvarLicenca = () => {
        form.transform((data) => Object.assign({}, data))

        if (props.licenca.id) {
            form.patch(route('licenca.update', props.licenca.id));
            return;
        }

        form.post(route('licenca.store'));
    }

    const salvarLicencaSegmento = () => {
        formSegmento.transform((data) => Object.assign({}, data))

        if(editarLicencaSegmento) {
            console.log("pastel");
            formSegmento.patch(route('licenca_segmento.update', formSegmento));
            return
        }

        console.log("adsokiodsaipsad")
        formSegmento.post(route('licenca_segmento.store'));
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

    const calcularExtensao = () => {
        const kmInicial = parseInt(formSegmento.km_inicial);
        const kmFinal = parseInt(formSegmento.km_final);

        if (kmFinal < kmInicial) {
            alert("O valor de Km Final não pode ser menor que Km Inicial. Por favor, corrija.");
        } else {
            formSegmento.extensao = kmFinal - kmInicial;
        }
    }

    // OTHERS
</script>

<style scoped>
    .container-buttons a, .container-buttons button{
        margin: 0px 5px;
    }
</style>
