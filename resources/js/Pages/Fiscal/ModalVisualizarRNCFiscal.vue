<script setup>
import Modal from "@/Components/Modal.vue";
import { ref } from "vue";

const props = defineProps({
    contrato: Object
})

const parecer = ref(null);
const modalRNC = ref(null);

const abrirModal = (item) => {
    parecer.value = item;
    modalRNC.value.getBsModal().show();
}

defineExpose({ abrirModal });

</script>

<template>
    <Modal ref="modalRNC" title="Visualizar Ocorrência" modal-dialog-class="modal-xl">
        <template #body>
            <div class="accordion" id="accordion-example">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="heading-1">
                        <button class="accordion-button " type="button" data-bs-toggle="collapse"
                            data-bs-target="#dadosDaOcorrencia" aria-expanded="true">
                            Dados da Ocorrência
                        </button>
                    </h2>
                    <div id="dadosDaOcorrencia" class="accordion-collapse collapse show"
                        data-bs-parent="#accordion-example">
                        <div class="accordion-body pt-0">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row mb-4">
                                        <span class="col"><strong>Local: </strong></span>
                                    </div>
                                    <div class="row mb-4">
                                        <span class="col"><strong>BR: </strong></span>
                                        <span class="col"><strong>UF: </strong></span>
                                        <span class="col"><strong>Data da Ocorrência: </strong></span>
                                    </div>
                                    <div class="row mb-4">
                                        <span class="col"><strong>KM: </strong></span>
                                        <span class="col"><strong>Estaca: </strong></span>
                                        <span class="col"><strong>Lado: </strong></span>
                                    </div>
                                    <div class="row mb-4">
                                        <span class="col"><strong>Latitude: </strong></span>
                                        <span class="col"><strong>Longitude: </strong></span>
                                        <span class="col"><strong>Lote: </strong></span>
                                    </div>
                                    <div class="row mb-4">
                                        <span class="col"><strong>Empresa / Consórcio: </strong></span>
                                        <span class="col"><strong>Contrato de Obra: </strong></span>
                                        <span class="col"><strong>Indícios de Responsabilidade da Construtora:
                                            </strong></span>
                                    </div>
                                    <div class="row mb-4">
                                        <span class="col"><strong>RNC Direto: </strong></span>
                                        <span class="col"><strong>Intensidade de Ocorrência: </strong></span>
                                        <span class="col"><strong>Tipo de Ocorrência: </strong></span>
                                    </div>
                                    <div class="row mb-4">
                                        <span class="col"><strong>Status da Ocorrência: </strong></span>
                                    </div>
                                </div>

                                <div class="card-header">
                                    <h3 class="my-0">Descrição</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-4">
                                        <span class="col"><strong>Local da Ocorrência: </strong></span>
                                        <span class="col"><strong>Classificação da Ocorrência: </strong></span>
                                        <span class="col"><strong>Descrição da Ocorrência: </strong></span>
                                    </div>
                                    <div class="row mb-4">
                                        <span class="col"><strong>Área total da Ocorrência (m²):: </strong></span>
                                        <span class="col"><strong>Prazo para Correção (dias): </strong></span>
                                        <span class="col"><strong></strong></span>
                                    </div>
                                </div>

                                <div class="card-header">
                                    <h3 class="my-0">Sugestões de ações corretivas</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <span class="col"></span>
                                    </div>
                                </div>

                                <div class="card-header">
                                    <h3 class="my-0">Norma / Fundamento legal</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <span class="col"></span>
                                    </div>
                                </div>

                                <div class="card-header">
                                    <h3 class="my-0">Registro Fotográfico</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <img src="" alt="Descrição da Imagem" width="300" height="200" />
                                    </div>
                                </div>

                                <div class="card-header">
                                    <h3 class="my-0">Observações</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <span class="col"><strong>Arquivo:</strong>
                                            <textarea class="form-control" name="observacao" id="observacao"></textarea>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="heading-2">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#vistoriaRNC" aria-expanded="false">
                            Vistoria RNC
                        </button>
                    </h2>
                    <div id="vistoriaRNC" class="accordion-collapse collapse" data-bs-parent="#accordion-example">
                        <div class="accordion-body pt-0">
                            <div class="row mb-4">
                                <span class="col"><strong>ID Ocorrência: </strong></span>
                                <span class="col"><strong>Data da Ocorrência: </strong></span>
                                <span class="col"><strong>ID Vistoria: </strong></span>
                            </div>
                            <div class="row mb-4">
                                <span class="col"><strong>Data da Vistoria: </strong></span>
                                <span class="col"><strong>Ocorrência corrigida: </strong></span>
                                <span class="col"><strong>Data Fim: </strong></span>
                            </div>
                            <div class="row mb-4">
                                <span class="col"><strong>Intensidade de Ocorrência: </strong></span>
                                <span class="col"><strong>Tipo de Ocorrência: </strong></span>
                                <span class="col"><strong>Observações da Vistoria: </strong></span>
                            </div>

                            <div class="card-header">
                                <h3 class="my-0">Arquivo de acordo de prazo</h3>
                            </div>
                            <div class="card-body">
                                <div class="row mb-4">
                                    <a class="col" href="" target="_blank" download>
                                        Baixar Arquivo
                                    </a>
                                </div>
                            </div>

                            <div class="card-header">
                                <h3 class="my-0">Imagens da vistoria</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <img src="" alt="Descrição da Imagem" width="300" height="200" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <!-- Neste accordio será necessário um v-if para quando não ter nem aparecer -->
                    <h2 class="accordion-header" id="heading-3">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#vistoriaROA" aria-expanded="false">
                            Vistoria ROA
                        </button>
                    </h2>
                    <div id="vistoriaROA" class="accordion-collapse collapse" data-bs-parent="#accordion-example">
                        <div class="accordion-body pt-0">
                            <div class="row mb-4">
                                <span class="col"><strong>ID Ocorrência: </strong></span>
                                <span class="col"><strong>Data da Ocorrência: </strong></span>
                                <span class="col"><strong>ID Vistoria: </strong></span>
                            </div>
                            <div class="row mb-4">
                                <span class="col"><strong>Data da Vistoria: </strong></span>
                                <span class="col"><strong>Ocorrência corrigida: </strong></span>
                                <span class="col"><strong>Intensidade de Ocorrência: </strong></span>
                            </div>
                            <div class="row mb-4">
                                <span class="col"><strong>Tipo de Ocorrência: </strong></span>
                                <span class="col"><strong>Realizado Acordo de Prazo: </strong></span>
                                <span class="col"><strong>Prazo para Correção (dias): </strong></span>

                            </div>
                            <div class="row mb-4">
                                <span class="col"><strong>Observações da Vistoria: </strong></span>
                            </div>

                            <div class="card-header">
                                <h3 class="my-0">Arquivo de acordo de prazo</h3>
                            </div>
                            <div class="card-body">
                                <div class="row mb-4">
                                    <a class="col" href="" target="_blank" download>
                                        Baixar Arquivo
                                    </a>
                                </div>
                            </div>

                            <div class="card-header">
                                <h3 class="my-0">Imagens da vistoria</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <img src="" alt="Descrição da Imagem" width="300" height="200" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </Modal>
</template>
