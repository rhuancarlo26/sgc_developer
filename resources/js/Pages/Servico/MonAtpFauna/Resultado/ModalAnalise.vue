<!-- resources\js\Pages\Servico\MonAtpFauna\Resultado\ModalAnalise.vue -->
<template>
    <div class="modal fade" ref="modalAnalise" tabindex="-1" aria-labelledby="modalAnaliseLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalAnaliseLabel">
                        Análise dos resultados programa de atropelamento de fauna
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <ul id="myTab" role="tablist" class="nav nav-tabs mb-3">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#tabela_registros_identificados"
                                role="tab">
                                Tabela com os registros identificados
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#frequencia_atropelamentos" role="tab">
                                Frequência de Atropelamentos por Classe
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#animais_atropelados_campanha" role="tab">
                                Número de Animais Atropelados por Campanha
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#animais_atropelados_km" role="tab">
                                Número de Animais Atropelados por Km
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#animais_atropelados_mes" role="tab">
                                Número de Animais Atropelados por Mês
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#animais_atropelados_especie" role="tab">
                                Número de Animais Atropelados por espécie
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#outras_analises" role="tab">
                                Outras análises
                            </a>
                        </li>
                    </ul>
                    <div id="myTabContent" class="tab-content">
                        <div class="tab-pane fade show active" id="tabela_registros_identificados" role="tabpanel">
                            <div class="row mb-2">
                                <div class="col-12">
                                    <table id="table-resultado" class="table table-hover non-hover"
                                        style="width: 100% !important;">
                                        <thead>
                                            <tr role="row">
                                                <th rowspan="2">Espécie</th>
                                                <th rowspan="2">Nome comum</th>
                                                <th rowspan="2" class="text-center">Nº Indivíduos</th>
                                                <th rowspan="2" class="text-center">Frequência (%)</th>
                                                <th colspan="2" class="text-center">Status Conservação</th>
                                            </tr>
                                            <tr role="row">
                                                <th class="text-center">IUCN</th>
                                                <th class="text-center">FEDERAL</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td></td>
                                                <td></td>
                                                <td class="text-center">0</td>
                                                <td class="text-center">0.00%
                                                </td>
                                                <td class="text-center"></td>
                                                <td class="text-center"></td>
                                            </tr>
                                            <tr>
                                                <td>1</td>
                                                <td>1</td>
                                                <td class="text-center">2</td>
                                                <td class="text-center">1.60%
                                                </td>
                                                <td class="text-center">CR - Criticamente em Perigo</td>
                                                <td class="text-center">NT - Quase Ameaçada</td>
                                            </tr>
                                            <tr>
                                                <td>123</td>
                                                <td>123</td>
                                                <td class="text-center">123</td>
                                                <td class="text-center">98.40%
                                                </td>
                                                <td class="text-center">EX - Extinto</td>
                                                <td class="text-center">Ameaçado (CR,EN e VU)</td>
                                            </tr>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th colspan="5">Número Total de Indivíduos: <b>125</b></th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-12">
                                    <label>Análise: </label>
                                    <textarea class="form-control"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="animais_atropelados_campanha" role="tabpanel">
                            <div class="row mb-2">
                                <div class="col-12">
                                    <div id="chartLine">
                                        <div class="statbox widget box">
                                            <div id="chart-campanha-at" class="widget-content widget-content-area pt-2">
                                                <div style="min-height: 320px;">
                                                    Grafico - 01
                                                </div>
                                                <div class="resize-triggers">
                                                    <div class="expand-trigger">
                                                        <div style="width: 1px; height: 1px;"></div>
                                                    </div>
                                                    <div class="contract-trigger"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-12">
                                    <label>Análise:</label>
                                    <textarea class="form-control"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="animais_atropelados_km" role="tabpanel">
                            <div class="row mb-2">
                                <div class="col-12">
                                    <div id="chartLine">
                                        <div class="statbox widget box">
                                            <div id="chart-km-at" class="widget-content widget-content-area pt-2">
                                                <div style="min-height: 320px;">
                                                    Grafico - 02
                                                </div>
                                                <div class="resize-triggers">
                                                    <div class="expand-trigger">
                                                        <div style="width: 1px; height: 1px;"></div>
                                                    </div>
                                                    <div class="contract-trigger"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-12">
                                    <label>Análise:</label>
                                    <textarea class="form-control"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="frequencia_atropelamentos" role="tabpanel">
                            <div class="row mb-2">
                                <div class="col-12">
                                    <div id="chartLine">
                                        <div class="statbox widget box">
                                            <div id="chart-classe-at" class="widget-content widget-content-area pt-2">
                                                <div style="min-height: 305px;">
                                                    Grafico - 03
                                                </div>
                                                <div class="resize-triggers">
                                                    <div class="expand-trigger">
                                                        <div style="width: 1px; height: 1px;"></div>
                                                    </div>
                                                    <div class="contract-trigger"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-12">
                                    <label>Análise:</label>
                                    <textarea class="form-control"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="animais_atropelados_especie" role="tabpanel">
                            <div class="row mb-2">
                                <div class="col-12">
                                    <div id="chartLine">
                                        <div class="statbox widget box">
                                            <div id="chart-especie-at" class="widget-content widget-content-area pt-2">
                                                <div style="min-height: 320px;">
                                                    Grafico - 04
                                                </div>
                                                <div class="resize-triggers">
                                                    <div class="expand-trigger">
                                                        <div style="width: 1181px; height: 329px;"></div>
                                                    </div>
                                                    <div class="contract-trigger"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-12">
                                    <label>Análise:</label>
                                    <textarea class="form-control"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="animais_atropelados_mes" role="tabpanel">
                            <div class="row mb-2">
                                <div class="col-12">
                                    <div id="chartLine">
                                        <div class="statbox widget box">
                                            <div id="chart-mes-at" class="widget-content widget-content-area pt-2">
                                                <div style="min-height: 320px;">
                                                    Grafico - 05
                                                </div>
                                                <div class="resize-triggers">
                                                    <div class="expand-trigger">
                                                        <div style="width: 1181px; height: 329px;"></div>
                                                    </div>
                                                    <div class="contract-trigger"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-12">
                                    <label>Análise:</label>
                                    <textarea class="form-control"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="outras_analises" role="tabpanel">
                            <div class="row mb-2">
                                <div class="col-12 mb-2">
                                    <label>Nome da Análise</label>
                                    <input type="text" name="nome" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3 error-placeholder">
                                        <div id="file" data-upload-id="myFirstImage" class="custom-file-container">
                                            <label>Buscar arquivo</label>
                                            <input type="file" id="arquivo-outra-analise" class="form-control-file">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12 mb-4">
                                    <label>Análise dos resultados:</label>
                                    <textarea class="form-control"></textarea>
                                </div>
                                <div class="col-12 mb-4">
                                    <button type="button" class="btn btn-success btn-lg">
                                        Incluir Análise
                                    </button>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-12">
                                    <table id="table-resultado" class="table table-hover non-hover"
                                        style="width: 100% !important;">
                                        <thead>
                                            <tr role="row">
                                                <th>Nome</th>
                                                <th class="text-center">Análise dos resultados</th>
                                                <th class="text-center">Visualizar Arquivo</th>
                                                <th>Ação</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>testetesteteste</td>
                                                <td>
                                                    <a href="javascript:void(0);">
                                                        Screenshot (1).png
                                                    </a>
                                                </td>
                                                <td class="text-center">
                                                    <div class="icon-container">
                                                        <a
                                                            href="https://servicos.dnit.gov.br/DPP/ecosistema//atropelamento-fauna/resultados/outra-analise/download-arquivo/3">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="16"
                                                                height="16" fill="currentColor" viewBox="0 0 16 16"
                                                                class="bi bi-download">
                                                                <path
                                                                    d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z">
                                                                </path>
                                                                <path
                                                                    d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3z">
                                                                </path>
                                                            </svg>
                                                        </a>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="btn-group">
                                                        <div class=" dropdown-menu-right">
                                                            <a href="#" role="button" id="dropdownMenuLink-1"
                                                                data-toggle="dropdown" aria-haspopup="true"
                                                                aria-expanded="true" class="dropdown-toggle">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                                    height="24" viewBox="0 0 24 24" fill="none"
                                                                    stroke="currentColor" stroke-width="2"
                                                                    stroke-linecap="round" stroke-linejoin="round"
                                                                    class="feather feather-more-horizontal">
                                                                    <circle cx="12" cy="12" r="1"></circle>
                                                                    <circle cx="19" cy="12" r="1"></circle>
                                                                    <circle cx="5" cy="12" r="1"></circle>
                                                                </svg>
                                                            </a>
                                                            <div aria-labelledby="dropdownMenuLink-1"
                                                                class="dropdown-menu">
                                                                <a href="javascript:void(0);" class="dropdown-item">
                                                                    Excluir
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Teste</td>
                                                <td>
                                                    <a href="javascript:void(0);">
                                                        Correções - Ecosistema - Rev. 03.11 (1).pptx
                                                    </a>
                                                </td>
                                                <td class="text-center">
                                                    <div class="icon-container">
                                                        <a
                                                            href="https://servicos.dnit.gov.br/DPP/ecosistema//atropelamento-fauna/resultados/outra-analise/download-arquivo/4">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="16"
                                                                height="16" fill="currentColor" viewBox="0 0 16 16"
                                                                class="bi bi-download">
                                                                <path
                                                                    d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z">
                                                                </path>
                                                                <path
                                                                    d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3z">
                                                                </path>
                                                            </svg>
                                                        </a>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="btn-group">
                                                        <div class=" dropdown-menu-right">
                                                            <a href="#" role="button" id="dropdownMenuLink-1"
                                                                data-toggle="dropdown" aria-haspopup="true"
                                                                aria-expanded="true" class="dropdown-toggle">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                                    height="24" viewBox="0 0 24 24" fill="none"
                                                                    stroke="currentColor" stroke-width="2"
                                                                    stroke-linecap="round" stroke-linejoin="round"
                                                                    class="feather feather-more-horizontal">
                                                                    <circle cx="12" cy="12" r="1"></circle>
                                                                    <circle cx="19" cy="12" r="1"></circle>
                                                                    <circle cx="5" cy="12" r="1"></circle>
                                                                </svg>
                                                            </a>
                                                            <div aria-labelledby="dropdownMenuLink-1"
                                                                class="dropdown-menu">
                                                                <a href="javascript:void(0);" class="dropdown-item">
                                                                    Excluir
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Teste</td>
                                                <td>
                                                    <a href="javascript:void(0);">
                                                        color.png
                                                    </a>
                                                </td>
                                                <td class="text-center">
                                                    <div class="icon-container">
                                                        <a href="javascript:void(0);">
                                                            <i class="far fa-eye"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="btn-group">
                                                        <div class=" dropdown-menu-right">
                                                            <a href="#" role="button" id="dropdownMenuLink-1"
                                                                data-toggle="dropdown" aria-haspopup="true"
                                                                aria-expanded="true" class="dropdown-toggle">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                                    height="24" viewBox="0 0 24 24" fill="none"
                                                                    stroke="currentColor" stroke-width="2"
                                                                    stroke-linecap="round" stroke-linejoin="round"
                                                                    class="feather feather-more-horizontal">
                                                                    <circle cx="12" cy="12" r="1"></circle>
                                                                    <circle cx="19" cy="12" r="1"></circle>
                                                                    <circle cx="5" cy="12" r="1"></circle>
                                                                </svg>
                                                            </a>
                                                            <div aria-labelledby="dropdownMenuLink-1"
                                                                class="dropdown-menu">
                                                                <a href="javascript:void(0);" class="dropdown-item">
                                                                    Excluir
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <NavButton @click="salvarAnalise" type-button="success" title="Salvar Analise" />
                <NavButton @click="fecharModal" type-button="secondary" title="Fechar" />
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from "vue";
import NavButton from "@/Components/NavButton.vue";

const modalAnalise = ref(null);
let modalInstance = null;

const abrirModal = () => {
    modalInstance.show();
};

const fecharModal = () => {
    modalInstance.hide();
};

const salvarAnalise = () => {
    console.log("salvarAnalise");
};

onMounted(() => {
    modalInstance = new bootstrap.Modal(modalAnalise.value);
});

onBeforeUnmount(() => {
    modalInstance.dispose();
});

defineExpose({ abrirModal, fecharModal });
</script>