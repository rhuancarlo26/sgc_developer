<script setup>
import Modal from "@/Components/Modal.vue";
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import { ref } from "vue";
import { useForm } from '@inertiajs/vue3';

const modalDetalhes = ref(null);

const abrirModal = () => {
    modalDetalhes.value.getBsModal().show();
}

const form = useForm({
    id: null,
    data_registro: null,
    hora_registro: null,
    km: '',
    latitude: '',
    longitude: '',
    ordem: '',
    familia: '',
    genero: '',
    especie: '',
    nome_comum: '',
    n_individuos: '',
});

const salvar = () => {
    if (Registros.id) {
        form.put(route('',));
    }
    form.post(route('',));
}

const salvarImagens = () => {
    form.post(route('',));
}

defineExpose({ abrirModal });

</script>

<template>
    <Modal ref="modalDetalhes" title="Registro" modal-dialog-class="modal-xl">
        <template #body>
            <div class="mb-4">
                <div class="card-body">
                    <div class="accordion" id="servico">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading-1">
                                <button class="accordion-button " type="button" data-bs-toggle="collapse"
                                    data-bs-target="#dadosGerais" aria-expanded="true">
                                    Dados Gerais
                                </button>
                            </h2>
                            <div id="dadosGerais" class="accordion-collapse collapse show" data-bs-parent="#servico">
                                <div class="accordion-body pt-0 card-body space-y-5">
                                    <div class="d-flex">
                                        <div class="col-lg-4 me-2">
                                            <InputLabel for="id" value="ID registro" />
                                            <input id="id" type="number" class="form-control" v-model="form.id"
                                                autofocus placeholder="ID do registro" autocomplete="id" disabled />
                                            <InputError class="mt-2" :message="form.errors.id" />
                                        </div>

                                        <div class="col-lg-4 me-2">
                                            <InputLabel for="" value="Selecionar Frente de Supressão" />
                                            <select class="form-select" aria-label="Default select example">
                                                <option selected>Selecione a frente de supressão</option>
                                                <option value="1">Frente 1</option>
                                                <option value="2">Frente 2</option>
                                                <option value="3">Frente 3</option>
                                            </select>
                                            <!-- <InputError class="mt-2" :message="form.errors." /> -->
                                        </div>

                                        <div class="col-lg-4">
                                            <InputLabel for="" value="Grupo amostrado" />
                                            <select class="form-select" aria-label="Default select example">
                                                <option selected>Selecione o grupo amostrado</option>
                                                <option value="1">Frente 1</option>
                                                <option value="2">Frente 2</option>
                                                <option value="3">Frente 3</option>
                                                <!-- <InputError class="mt-2" :message="form.errors." /> -->
                                            </select>
                                        </div>
                                    </div>

                                    <div class="d-flex">
                                        <div class="col-lg-4 me-2">
                                            <InputLabel for="" value="Data Registro" />
                                            <input id="" type="date" class="form-control" v-model="form.data_registro"
                                                autofocus placeholder="Data registro" autocomplete="data_registro" />
                                            <InputError class="mt-2" :message="form.errors.data_registro" />
                                        </div>

                                        <div class="col-lg-4 me-2">
                                            <InputLabel for="" value="Hora Registro" />
                                            <input id="" type="time" class="form-control" v-model="form.hora_registro"
                                                autofocus placeholder="Hora registro" autocomplete="hora_registro" />
                                            <InputError class="mt-2" :message="form.errors.hora_registro" />
                                        </div>

                                        <div class="col-lg-4">
                                            <InputLabel for="" value="UF" />
                                            <select class="form-select" aria-label="Default select example">
                                                <option selected>Selecione a UF</option>
                                                <option value="1">Frente 1</option>
                                                <option value="2">Frente 2</option>
                                                <option value="3">Frente 3</option>
                                                <!-- <InputError class="mt-2" :message="form.errors." /> -->
                                            </select>
                                        </div>
                                    </div>

                                    <div class="d-flex">
                                        <div class="col-lg-4 me-2">
                                            <InputLabel for="" value="KM" />
                                            <input id="" type="text" class="form-control" v-model="form.km" autofocus
                                                placeholder="KM" autocomplete="km" />
                                            <InputError class="mt-2" :message="form.errors.km" />
                                        </div>

                                        <div class="col-lg-4 me-2">
                                            <InputLabel for="" value="Latitude" />
                                            <input id="" type="text" class="form-control" v-model="form.latitude"
                                                autofocus placeholder="Latitude" autocomplete="latitude" />
                                            <InputError class="mt-2" :message="form.errors.latitude" />
                                        </div>

                                        <div class="col-lg-4 me-2">
                                            <InputLabel for="" value="Longitude" />
                                            <input id="" type="text" class="form-control" v-model="form.longitude"
                                                autofocus placeholder="Longitude" autocomplete="longitude" />
                                            <InputError class="mt-2" :message="form.errors.longitude" />
                                        </div>
                                    </div>
                                    <div class="mt-2">
                                        <a @click="" href="#" class="btn btn-danger me-2" aria-label="Button"
                                            :disabled="form.processing">
                                            Cancelar
                                        </a>
                                        <a @click="salvar()" href="#" class="btn btn-success" aria-label="Button"
                                            :disabled="form.processing">
                                            Salvar
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading-2">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#identificacaoEspecime" aria-expanded="false">
                                    Identificação do Espécime
                                </button>
                            </h2>
                            <div id="identificacaoEspecime" class="accordion-collapse collapse"
                                data-bs-parent="#servico">
                                <div class="accordion-body pt-0 card-body space-y-5">
                                    <div class="d-flex">
                                        <div class="col-lg-4 me-2">
                                            <InputLabel for="" value="Classe" />
                                            <select class="form-select" aria-label="Default select example">
                                                <option selected>Selecione a classe</option>
                                                <option value="1">Frente 1</option>
                                                <option value="2">Frente 2</option>
                                                <option value="3">Frente 3</option>
                                            </select>
                                            <!-- <InputError class="mt-2" :message="form.errors." /> -->
                                        </div>

                                        <div class="col-lg-4 me-2">
                                            <InputLabel for="" value="Ordem" />
                                            <input id="" type="text" class="form-control" v-model="form.ordem" autofocus
                                                placeholder="Ordem" autocomplete="ordem" />
                                            <InputError class="mt-2" :message="form.errors.ordem" />
                                        </div>

                                        <div class="col-lg-4 me-2">
                                            <InputLabel for="" value="Família" />
                                            <input id="" type="text" class="form-control" v-model="form.familia"
                                                autofocus placeholder="Familia" autocomplete="familia" />
                                            <InputError class="mt-2" :message="form.errors.familia" />
                                        </div>
                                    </div>

                                    <div class="d-flex">
                                        <div class="col-lg-4 me-2">
                                            <InputLabel for="" value="Gênero" />
                                            <input id="" type="text" class="form-control" v-model="form.genero"
                                                autofocus placeholder="Gênero" autocomplete="genero" />
                                            <InputError class="mt-2" :message="form.errors.genero" />
                                        </div>

                                        <div class="col-lg-4 me-2">
                                            <InputLabel for="" value="Espécie" />
                                            <input id="" type="text" class="form-control" v-model="form.especie"
                                                autofocus placeholder="Espécie" autocomplete="especie" />
                                            <InputError class="mt-2" :message="form.errors.especie" />
                                        </div>

                                        <div class="col-lg-4 me-2">
                                            <InputLabel for="" value="Nome comum" />
                                            <input id="" type="text" class="form-control" v-model="form.nome_comum"
                                                autofocus placeholder="Nome comum" autocomplete="nome_comum" />
                                            <InputError class="mt-2" :message="form.errors.nome_comum" />
                                        </div>
                                    </div>

                                    <div class="d-flex">
                                        <div class="col-lg-4 me-2">
                                            <InputLabel for="" value="Categoria" />
                                            <select class="form-select" aria-label="Default select example">
                                                <option selected>Selecione a Categoria</option>
                                                <option value="1">Frente 1</option>
                                                <option value="2">Frente 2</option>
                                                <option value="3">Frente 3</option>
                                            </select>
                                            <!-- <InputError class="mt-2" :message="form.errors." /> -->
                                        </div>
                                    </div>
                                    <div class="mt-2">
                                        <a @click="" href="#" class="btn btn-danger me-2" aria-label="Button"
                                            :disabled="form.processing">
                                            Cancelar
                                        </a>
                                        <a @click="salvar()" href="#" class="btn btn-success" aria-label="Button"
                                            :disabled="form.processing">
                                            Salvar
                                        </a>
                                    </div>

                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading-3">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#dadosEspecime" aria-expanded="false">
                                    Dados do Espécime
                                </button>
                            </h2>
                            <div id="dadosEspecime" class="accordion-collapse collapse" data-bs-parent="#servico">
                                <div class="accordion-body pt-0 card-body space-y-5">
                                    <div class="d-flex">
                                        <div class="col-lg-4 me-2">
                                            <InputLabel for="" value="Sexo" />
                                            <select class="form-select" aria-label="Default select example">
                                                <option selected>Selecione o Sexo</option>
                                                <option value="1">Frente 1</option>
                                                <option value="2">Frente 2</option>
                                                <option value="3">Frente 3</option>
                                            </select>
                                            <!-- <InputError class="mt-2" :message="form.errors." /> -->
                                        </div>

                                        <div class="col-lg-4 me-2">
                                            <InputLabel for="" value="Faixa etária" />
                                            <select class="form-select" aria-label="Default select example">
                                                <option selected>Selecione a Faixa etária</option>
                                                <option value="1">Frente 1</option>
                                                <option value="2">Frente 2</option>
                                                <option value="3">Frente 3</option>
                                            </select>
                                            <!-- <InputError class="mt-2" :message="form.errors." /> -->
                                        </div>

                                        <div class="col-lg-4 me-2">
                                            <InputLabel for="" value="Nº de indivíduos do registro" />
                                            <input id="" type="text" class="form-control" v-model="form.n_individuos"
                                                autofocus placeholder="Nº de indivíduos do registro" autocomplete="" />
                                            <InputError class="mt-2" :message="form.errors.n_individuos" />
                                        </div>
                                    </div>

                                    <div class="d-flex">
                                        <div class="col-lg-4 me-2">
                                            <InputLabel for="" value="Forma Registro" />
                                            <select class="form-select" aria-label="Default select example">
                                                <option selected>Selecione a forma registro</option>
                                                <option value="1">Frente 1</option>
                                                <option value="2">Frente 2</option>
                                                <option value="3">Frente 3</option>
                                            </select>
                                            <!-- <InputError class="mt-2" :message="form.errors." /> -->
                                        </div>

                                        <div class="col-lg-4 me-2">
                                            <InputLabel for="" value="Tipo de Registro" />
                                            <select class="form-select" aria-label="Default select example">
                                                <option selected>Selecione o Tipo de Registro</option>
                                                <option value="1">Frente 1</option>
                                                <option value="2">Frente 2</option>
                                                <option value="3">Frente 3</option>
                                            </select>
                                            <!-- <InputError class="mt-2" :message="form.errors." /> -->
                                        </div>

                                        <div class="col-lg-4 me-2">
                                            <InputLabel for="" value="Destinação" />
                                            <select class="form-select" aria-label="Default select example">
                                                <option selected>Selecione a Destinação</option>
                                                <option value="1">Frente 1</option>
                                                <option value="2">Frente 2</option>
                                                <option value="3">Frente 3</option>
                                            </select>
                                            <!-- <InputError class="mt-2" :message="form.errors." /> -->
                                        </div>
                                    </div>
                                    <div class="mt-2">
                                        <a @click="" href="#" class="btn btn-danger me-2" aria-label="Button"
                                            :disabled="form.processing">
                                            Cancelar
                                        </a>
                                        <a @click="salvar()" href="#" class="btn btn-success" aria-label="Button"
                                            :disabled="form.processing">
                                            Salvar
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading-4">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#statusConservacaoEspecime" aria-expanded="false">
                                    Status de Conservação do Espécime
                                </button>
                            </h2>
                            <div id="statusConservacaoEspecime" class="accordion-collapse collapse"
                                data-bs-parent="#servico">
                                <div class="accordion-body pt-0 card-body space-y-5">
                                    <div class="d-flex">
                                        <div class="col-lg-6 me-2">
                                            <InputLabel for="" value="Federal" />
                                            <select class="form-select" aria-label="Default select example">
                                                <option selected>Selecione a Federal</option>
                                                <option value="1">Frente 1</option>
                                                <option value="2">Frente 2</option>
                                                <option value="3">Frente 3</option>
                                            </select>
                                            <!-- <InputError class="mt-2" :message="form.errors." /> -->
                                        </div>

                                        <div class="col-lg-6 me-2">
                                            <InputLabel for="" value="IUCN" />
                                            <select class="form-select" aria-label="Default select example">
                                                <option selected>Selecione a IUCN</option>
                                                <option value="1">Frente 1</option>
                                                <option value="2">Frente 2</option>
                                                <option value="3">Frente 3</option>
                                            </select>
                                            <!-- <InputError class="mt-2" :message="form.errors." /> -->
                                        </div>
                                    </div>
                                    <div class="mt-2">
                                        <a @click="" href="#" class="btn btn-danger me-2" aria-label="Button"
                                            :disabled="form.processing">
                                            Cancelar
                                        </a>
                                        <a @click="salvar()" href="#" class="btn btn-success" aria-label="Button"
                                            :disabled="form.processing">
                                            Salvar
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading-4">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#registroFotografico" aria-expanded="false">
                                    Registro Fotográfico
                                </button>
                            </h2>
                            <div id="registroFotografico" class="accordion-collapse collapse" data-bs-parent="#servico">

                                <div class="accordion-body pt-0 card-body space-y-5">
                                    <div class="d-flex">
                                        <div class="col-lg-6 me-2">
                                            <div>
                                                <InputLabel for="" value="Buscar Arquivo (.jpg)" />
                                                <input @input="form.documento = $event.target.files[0]" type="file"
                                                    class="form-control">
                                            </div>

                                            <div class="mt-2">
                                                <a @click="salvarImagens()" href="#" class="btn btn-success"
                                                    aria-label="Button" :disabled="form.processing">
                                                    Enviar
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </Modal>
</template>