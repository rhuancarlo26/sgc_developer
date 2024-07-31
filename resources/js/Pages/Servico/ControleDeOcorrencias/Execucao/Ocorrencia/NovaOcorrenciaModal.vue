<!-- resources\js\Pages\Servico\ControleDeOcorrencias\Execucao\Ocorrencia\NovaOcorrenciaModal.vue -->
<template>
    <Modal ref="modalNovaOcorrencia" title="Nova Ocorrência" modal-dialog-class="modal-xl">
        <template #body>
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#local" role="tab">
                        Local
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#descricao" role="tab">
                        Descrição
                    </a>
                </li>
            </ul>
            <div class="tab-content">
                <!-- Formulário Local -->
                <div class="tab-pane fade show active" id="local" role="tabpanel">
                    <div class="row my-3">
                        <div class="col-md-3">
                            <InputLabel value="ID Ocorrência" />
                            <input v-model="form.id_ocorrencia" type="text" class="form-control" />
                        </div>
                        <div class="col-md-3">
                            <InputLabel value="BR" />
                            <select v-model="form.br" class="form-control">
                                <option value="040">040</option>
                                <option value="060">060</option>
                                <option value="070">070</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <InputLabel value="UF" />
                            <select v-model="form.uf" class="form-control">
                                <option value="Distrito Federal">Distrito Federal</option>
                                <option value="Goias">Goias</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <InputLabel value="Data da Ocorrência" />
                            <input v-model="form.data_ocorrencia" type="date" class="form-control" />
                        </div>
                    </div>
                    <div class="row my-3">
                        <div class="col-md-4">
                            <InputLabel value="KM" />
                            <input v-model="form.km" type="text" class="form-control" />
                        </div>
                        <div class="col-md-4">
                            <InputLabel value="Estaca" />
                            <input v-model="form.estaca" type="text" class="form-control" />
                        </div>
                        <div class="col-md-4">
                            <InputLabel value="Lado" />
                            <select v-model="form.lado" class="form-control">
                                <option value="Esquerdo">Esquerdo</option>
                                <option value="Direito">Direito</option>
                                <option value="Esquerdo / Direito">Esquerdo / Direito</option>
                            </select>
                        </div>
                    </div>
                    <div class="row my-3">
                        <div class="col-md-3">
                            <InputLabel value="Latitude" />
                            <input v-model="form.latitude" type="text" class="form-control" />
                        </div>
                        <div class="col-md-3">
                            <InputLabel value="Longitude" />
                            <input v-model="form.longitude" type="text" class="form-control" />
                        </div>
                        <div class="col-md-3">
                            <InputLabel value="Lote" />
                            <select v-model="form.lote" class="form-control">
                                <option value="L.01">L.01</option>
                                <option value="L.02">L.02</option>
                                <option value="L.03">L.03</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <InputLabel value="Nome Lote" />
                            <input v-model="form.nome_lote" type="text" class="form-control" />
                        </div>
                    </div>
                    <div class="row my-3">
                        <div class="col-md-6">
                            <InputLabel value="Empresa/Consórcio" />
                            <input v-model="form.empresa_consorcio" type="text" class="form-control" />
                        </div>
                        <div class="col-md-6">
                            <InputLabel value="Contrato de Obra" />
                            <input v-model="form.contrato_obra" type="text" class="form-control" />
                        </div>
                    </div>
                    <div class="row my-3">
                        <div class="col-md-12">
                            <InputLabel value="Indícios de Responsabilidade da Construtora" />
                            <div>
                                <label><input type="radio" v-model="form.indicios_responsabilidade" value="Sim" />
                                    Sim</label>
                                <label class="ms-3"><input type="radio" v-model="form.indicios_responsabilidade"
                                        value="Não" /> Não</label>
                            </div>
                        </div>
                    </div>
                    <div class="row my-3">
                        <div class="col-md-12">
                            <InputLabel value="RNC Direto" />
                            <div>
                                <label><input type="radio" v-model="form.rnc_direto" value="Sim" /> Sim</label>
                                <label class="ms-3"><input type="radio" v-model="form.rnc_direto" value="Não" />
                                    Não</label>
                            </div>
                        </div>
                    </div>
                    <div class="row my-3">
                        <div class="col-md-6">
                            <InputLabel value="Intensidade de Ocorrência" />
                            <select v-model="form.intensidade_ocorrencia" class="form-control">
                                <option value="Leve">Leve</option>
                                <option value="Moderada">Moderada</option>
                                <option value="Grave">Grave</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <InputLabel value="Tipo de Ocorrência" />
                            <input v-model="form.tipo_ocorrencia" type="text" class="form-control" />
                        </div>
                    </div>
                </div>
                <!-- Formulário Descrição -->
                <div class="tab-pane fade" id="descricao" role="tabpanel">
                    <div class="row my-3">
                        <div class="col-md-6">
                            <InputLabel value="Local da Ocorrência" />
                            <select v-model="form.local_ocorrencia" class="form-control">
                                <option value="App">App</option>
                                <option value="Faixa de Dominio">Faixa de Dominio</option>
                                <option value="Canteiro">Canteiro</option>
                                <option value="Bota-Fora">Bota-Fora</option>
                                <option value="Área Fonte">Área Fonte</option>
                                <option value="Área de Apoio">Área de Apoio</option>
                                <option value="Outros">Outros</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <InputLabel value="Classificação da Ocorrência" />
                            <select v-model="form.classificacao_ocorrencia" class="form-control">
                                <option value="Erosão">Erosão</option>
                                <option value="Queda de blocos">Queda de blocos</option>
                                <option value="Rolamento de Blocos">Rolamento de Blocos</option>
                                <option value="Acessos Irregulares">Acessos Irregulares</option>
                                <option value="Ocupação da Faixa de domínio">Ocupação da Faixa de domínio</option>
                                <option value="Assoreamento">Assoreamento</option>
                                <option value="Alagamento">Alagamento</option>
                                <option value="Contaminação/poluição">Contaminação/poluição</option>
                                <option value="Licenças/outorgas">Licenças/outorgas</option>
                                <option value="Outros">Outros</option>
                            </select>
                        </div>
                    </div>
                    <div class="row my-3">
                        <div class="col-md-12">
                            <InputLabel value="Descrição da Ocorrência" />
                            <textarea v-model="form.descricao_ocorrencia" class="form-control"></textarea>
                        </div>
                    </div>
                    <div class="row my-3">
                        <div class="col-md-3">
                            <InputLabel value="Área total da Ocorrência (m²)" />
                            <input v-model="form.area_total" type="text" class="form-control" />
                        </div>
                        <div class="col-md-3">
                            <InputLabel value="Prazo para Correção (dias)" />
                            <input v-model="form.prazo_correcao" type="text" class="form-control" />
                        </div>
                    </div>
                </div>
            </div>
        </template>
        <template #footer>
            <NavButton @click="criarOcorrencia" type-button="success" title="Criar Ocorrência" />
            <NavButton @click="fecharModal" type-button="secondary" title="Cancelar" />
        </template>
    </Modal>
</template>

<script setup>
import InputLabel from "@/Components/InputLabel.vue";
import Modal from "@/Components/Modal.vue";
import NavButton from "@/Components/NavButton.vue";
import { useForm } from "@inertiajs/vue3";
import { ref } from "vue";

const props = defineProps({
    contrato: { type: Object },
    servico: { type: Object }
})

const modalNovaOcorrencia = ref();

const form = useForm({
    // Local
    id_ocorrencia: '',
    br: '',
    uf: '',
    data_ocorrencia: '',
    km: '',
    estaca: '',
    lado: '',
    latitude: '',
    longitude: '',
    lote: '',
    nome_lote: '',
    empresa_consorcio: '',
    contrato_obra: '',
    indicios_responsabilidade: '',
    rnc_direto: '',
    intensidade_ocorrencia: '',
    tipo_ocorrencia: '',
    nome_ocorrencia: '',  
    // DESCRIÇÃO
    local_ocorrencia: '',  
    classificacao_ocorrencia: '',
    descricao_ocorrencia: '',
    area_total: '',  
    prazo_correcao: '' 
});

const abrirModal = () => {
    modalNovaOcorrencia.value.getBsModal().show();
}

const fecharModal = () => {
    modalNovaOcorrencia.value.getBsModal().hide();
}

const criarOcorrencia = () => {
    form.post(route('contratos.contratada.servicos.controledeocorrencias.ocorrencia.store', { contrato: props.contrato.id, servico: props.servico.id }), {
        onSuccess: () => fecharModal()
    });
}

defineExpose({ abrirModal });
</script>

<style scoped>
.modal-xl {
    max-width: 80%;
}
</style>
