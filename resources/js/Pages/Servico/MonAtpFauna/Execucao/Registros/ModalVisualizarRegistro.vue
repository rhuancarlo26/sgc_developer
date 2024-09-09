<!-- resources\js\Pages\Servico\MonAtpFauna\Execucao\Registros\ModalVisualizarRegistro.vue -->
<template>
    <div class="modal fade" ref="modalVisualizarRegistro" tabindex="-1" aria-labelledby="modalVisualizarRegistroLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalVisualizarRegistroLabel">
                        Visualizar Registro
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="col-12">
                        <h2>LOCAL</h2>
                    </div>
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <strong for="nome_id">Campanha: </strong> 
                            <label>1</label>
                        </div>
                        <div class="col-md-3 mb-3">
                            <strong for="id_campanha">Grupo amostrado: </strong>
                            <label>Avifauna</label>
                        </div>
                        <div class="col-md-3 mb-3">
                            <strong for="id_modulo">Registro: </strong> 
                            <label>28/08/2023</label>
                        </div>
                        <div class="col-md-3 mb-3">
                            <strong for="grupo_faunistico">UF: </strong> 
                            <label>Pará</label>
                        </div>
                    </div>
                    <hr>
                    <div class="col-12">
                        <h2>IDENTIFICAÇÃO DO ESPÉCIME</h2>
                    </div>
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <strong for="classe">Classe: </strong> 
                            <label>Aves</label>
                        </div>
                        <div class="col-md-3 mb-3">
                            <strong for="ordem">Ordem: </strong> 
                            <label></label>
                        </div>
                        <div class="col-md-3 mb-3">
                            <strong for="familia">Família: </strong> 
                            <label></label>
                        </div>
                        <div class="col-md-3 mb-3">
                            <strong for="genero">Gênero: </strong> 
                            <label></label>
                        </div>
                        <div class="col-md-3 mb-3">
                            <strong for="especie">Espécie: </strong> 
                            <label></label>
                        </div>
                        <div class="col-md-3 mb-3">
                            <strong for="nome_comum">Nome Comum: </strong> 
                            <label></label>
                        </div>
                    </div>
                    <hr>
                    <div class="col-12">
                        <h2>DADOS DO ESPÉCIME</h2>
                    </div>
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <strong for="sexo">Sexo: </strong> 
                            <label></label>
                        </div>
                        <div class="col-md-3 mb-3">
                            <strong for="faixa_etaria">Faixa Etária: </strong> 
                            <label></label>
                        </div>
                        <div class="col-md-3 mb-3">
                            <strong for="coletado">Coletado: </strong> 
                            <label></label>
                        </div>
                        <div class="col-md-3 mb-3">
                            <strong for="dds_bio_anotados">Carcaça removida: </strong>
                            <label></label>
                        </div>
                        <div class="col-md-3 mb-3">
                            <strong for="num_individuos">Redução biológica com Cal: </strong>
                            <label></label>
                        </div>
                        <div class="col-md-3 mb-3">
                            <strong for="num_individuos">Nº de indivíduos do registro: </strong>
                            <label></label>
                        </div>
                    </div>
                    <hr>
                    <div class="col-12">
                        <h2>STATUS DE CONSERVAÇÃO DO ESPÉCIME</h2>
                    </div>
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <strong for="status_conserv_federal">Estadual:</strong>
                            <label></label>
                        </div>
                        <div class="col-md-3 mb-3">
                            <strong for="status_conserv_federal">Federal:</strong>
                            <label></label>
                        </div>
                        <div class="col-md-3 mb-3">
                            <strong for="status_conserv_iucn">IUCN:</strong> 
                            <label></label>
                        </div>
                    </div>
                    <hr>
                    <div class="col-12">
                        <h2>IMAGENS DO ESPÉCIME</h2>
                    </div>
                    <div class="card mt-2 mb-4">
                        <table class="table table-bordered table-striped table-hover js-basic-example dataTable">
                            <thead>
                                <tr class="bg-light">
                                    <th class="col-md-4">Imagens do Espécime</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="d-flex flex-wrap">
                                        <div class="col-md-4">
                                            <img src="https://servicos.dnit.gov.br/DPP/ecosistema/atropelamento-fauna/arquivo/registro/fotografico/a74bed8e78b8f2f3c560c908c37a22eb" width="100%" alt="">
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <NavButton @click="fecharModal" type-button="secondary" title="Fechar" />
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from "vue";
import NavButton from "@/Components/NavButton.vue";

const modalVisualizarRegistro = ref(null);
let modalInstance = null;

const abrirModal = () => {
    modalInstance.show();
};

const fecharModal = () => {
    modalInstance.hide();
};

onMounted(() => {
    modalInstance = new bootstrap.Modal(modalVisualizarRegistro.value);
});

onBeforeUnmount(() => {
    modalInstance.dispose();
});

defineExpose({ abrirModal, fecharModal });
</script>