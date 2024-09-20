<!-- resources\js\Pages\Servico\MonAtpFauna\Execucao\Registros\ModalEditarRegistro.vue -->
<template>
    <div class="modal fade" ref="modalEditarRegistro" tabindex="-1" aria-labelledby="modalEditarRegistroLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEditarRegistroLabel">Editar Registros</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <ul id="myTab" role="tablist" class="nav nav-tabs mb-3">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#registro_local" role="tab">
                                Local
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#registro_identificacao" role="tab">
                                Identificação do Espécime
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#registro_dados_especime" role="tab">
                                Dados do Espécime
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#registro_conservacao_especime" role="tab">
                                Status de conservação do espécime
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#registro_fotografico" role="tab">
                                Registro fotográfico
                            </a>
                        </li>
                    </ul>
                    <div id="myTabContent" class="tab-content">
                        <div class="tab-pane fade show active" id="registro_local" role="tabpanel">
                            <div class="row mb-2">
                                <div class="col-2">
                                    <label for="campanha_id">ID registro</label>
                                    <input type="text" id="campanha_id" disabled="disabled" class="form-control">
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-4">
                                    <label>Selecionar campanha</label>
                                    <select class="form-control">
                                        <option value="1"> 1</option>
                                        <option value="2"> 2</option>
                                        <option value="3"> 3</option>
                                    </select>
                                </div>
                                <div class="col-4">
                                    <label>Grupo amostrado</label>
                                    <select id="id_grupo" class="form-control">
                                        <option value="1">Avifauna</option>
                                        <option value="2">Herpetofauna</option>
                                        <option value="3">Mastofauna</option>
                                    </select>
                                </div>
                                <div class="col-4">
                                    <label>hora Registro</label>
                                    <input type="date" class="form-control">
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-4">
                                    <label for="tipo_ambiente">UF:</label>
                                    <select name="uf" id="uf" class="form-control">
                                        <option value="1">AC</option>
                                        <option value="2">AL</option>
                                        <option value="3">AM</option>
                                        <option value="4">AP</option>
                                        <option value="5">BA</option>
                                        <option value="6">CE</option>
                                        <option value="7">DF</option>
                                        <option value="8">ES</option>
                                        <option value="9">GO</option>
                                        <option value="10">MA</option>
                                        <option value="11">MG</option>
                                        <option value="12">MS</option>
                                        <option value="13">MT</option>
                                        <option value="14">PA</option>
                                        <option value="15">PB</option>
                                        <option value="16">PE</option>
                                        <option value="17">PI</option>
                                        <option value="18">PR</option>
                                        <option value="19">RJ</option>
                                        <option value="20">RN</option>
                                        <option value="21">RO</option>
                                        <option value="22">RR</option>
                                        <option value="23">RS</option>
                                        <option value="24">SC</option>
                                        <option value="25">SE</option>
                                        <option value="26">SP</option>
                                        <option value="27">TO</option>
                                    </select>
                                </div>
                                <div class="col-4">
                                    <label>KM</label>
                                    <input type="text" class="form-control">
                                </div>
                                <div class="col-4">
                                    <label>Hora Registro</label>
                                    <input type="time" class="form-control">
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-4">
                                    <label>Latitude</label>
                                    <input type="text" class="form-control">
                                </div>
                                <div class="col-4">
                                    <label>Longitude</label>
                                    <input type="text" class="form-control">
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-2">
                                    <label>Sentido</label>
                                    <br>
                                    <input type="radio" name="registro_sentido" value="C">
                                    Crescente
                                    <br>
                                    <input type="radio" name="registro_sentido" value="D">
                                    Decrescente
                                </div>
                                <div class="col-2">
                                    <label>Margem</label>
                                    <br>
                                    <input type="radio" name="registro_margem" value="E">
                                    Esquerda
                                    <br>
                                    <input type="radio" name="registro_margem" value="D">
                                    Direita
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="registro_identificacao" role="tabpanel">
                            <div class="row mb-2">
                                <div class="col-4">
                                    <label>Classe</label>
                                    <select class="form-control">
                                        <option value="">-- Selecione --</option>
                                    </select>
                                </div>
                                <div class="col-4">
                                    <label>Ordem</label>
                                    <input type="text" class="form-control">
                                </div>
                                <div class="col-4">
                                    <label>Família</label>
                                    <input type="text" class="form-control">
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-4">
                                    <label>Gênero</label>
                                    <input type="text" class="form-control">
                                </div>
                                <div class="col-4">
                                    <label>Espécie</label>
                                    <input type="text" class="form-control">
                                </div>
                                <div class="col-4">
                                    <label>Nome comum</label>
                                    <input type="text" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="registro_dados_especime" role="tabpanel">
                            <div class="row mb-2">
                                <div class="col-4">
                                    <label>Sexo</label>
                                    <select class="form-control">
                                        <option value="macho">Macho</option>
                                        <option value="femea">Fêmea</option>
                                        <option value="sem indentificacao">Sem indentificação</option>
                                    </select>
                                </div>
                                <div class="col-4">
                                    <label>Faixa etária</label>
                                    <select class="form-control">
                                        <option value="Jovem">Jovem</option>
                                        <option value="Adulto">Adulto</option>
                                        <option value="Inderteminado">Inderteminado</option>
                                    </select>
                                </div>
                                <div class="col-4">
                                    <label>Coletado</label>
                                    <select class="form-control">
                                        <option value="sim">Sim</option>
                                        <option value="nao">Não</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-4">
                                    <label>Carcaça removida:</label>
                                    <select class="form-control">
                                        <option value="sim">Sim</option>
                                        <option value="nao">Não</option>
                                    </select>
                                </div>
                                <div class="col-4">
                                    <label>Redução Biológica com Cal:</label>
                                    <select class="form-control">
                                        <option value="sim">Sim</option>
                                        <option value="nao">Não</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-4">
                                    <label>Nº de indivíduos do registro:</label>
                                    <input type="text" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="registro_conservacao_especime" role="tabpanel">
                            <div class="row mb-2">
                                <div class="col-4">
                                    <label>Estadual:</label>
                                    <select class="form-control">
                                        <option value="Ameaçado (CR,EN e VU)">Ameaçado (CR,EN e VU)</option>
                                        <option value="NT - Quase Ameaçada">NT - Quase Ameaçada</option>
                                        <option value="DD - Dados Insuficientes">DD - Dados Insuficientes</option>
                                        <option value="NE - Não Avaliado">NE - Não Avaliado</option>
                                    </select>
                                </div>
                                <div class="col-4">
                                    <label>Federal:</label>
                                    <select class="form-control">
                                        <option value="Ameaçado (CR,EN e VU)">Ameaçado (CR,EN e VU)</option>
                                        <option value="NT - Quase Ameaçada">NT - Quase Ameaçada</option>
                                        <option value="DD - Dados Insuficientes">DD - Dados Insuficientes</option>
                                        <option value="NE - Não Avaliado">NE - Não Avaliado</option>
                                    </select>
                                </div>
                                <div class="col-4">
                                    <label>IUCN:</label>
                                    <select class="form-control">
                                        <option value="EX - Extinto">EX - Extinto</option>
                                        <option value="EW - Extinto na Natureza">EW - Extinto na Natureza</option>
                                        <option value="CR - Criticamente em Perigo">CR - Criticamente em Perigo</option>
                                        <option value="EN - Em Perigo">EN - Em Perigo</option>
                                        <option value="VU - Vulnerável">VU - Vulnerável</option>
                                        <option value="NT - Quase Ameaçada">NT - Quase Ameaçada</option>
                                        <option value="LC - Pouco Preocupante">LC - Pouco Preocupante</option>
                                        <option value="DD - Dados Insuficientes">DD - Dados Insuficientes</option>
                                        <option value="NE - Não Avaliado">NE - Não Avaliado</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="registro_fotografico" role="tabpanel">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3 error-placeholder">
                                        <div data-upload-id="myFirstImage" class="custom-file-container">
                                            <label>Buscar Arquivo (.jpg/png)</label>
                                            <input type="file" accept="image/png, image/jpeg"
                                                id="arquivo_registro_fotografico" class="form-control-file">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="anexo_foto">Imagens do Espécime</label>
                                    <table
                                        class="table table-bordered table-striped table-hover js-basic-example dataTable">
                                        <thead>
                                            <tr>
                                                <th>Arquivo</th>
                                                <th class="col-md-1">Ação</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <NavButton @click="editarRegistro" type-button="success" title="Editar Registro" />
                    <NavButton @click="fecharModal" type-button="secondary" title="Fechar" />
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from "vue";
import NavButton from "@/Components/NavButton.vue";

const modalEditarRegistro = ref(null);
let modalInstance = null;

const abrirModal = () => {
    modalInstance.show();
};

const fecharModal = () => {
    modalInstance.hide();
};

const editarRegistro = () => {
    console.log("editarRegistro");
};

onMounted(() => {
    modalInstance = new bootstrap.Modal(modalEditarRegistro.value);
});

onBeforeUnmount(() => {
    modalInstance.dispose();
});

defineExpose({ abrirModal, fecharModal });
</script>
