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

const props = defineProps({
    contrato: { type: Object },
});

const form = useForm({
    id: null,
    rodovia: null,
    uf_inicial: null,
    km_inicial: null,
    latitude_inicial: null,
    longitude_inicial: null,
    uf_final: null,
    km_final: null,
    latitude_final: null,
    longitude_final: null,
    data_inicial: null,
    data_final: null,
    processing: false,
    errors: {}
});

const salvar = () => {
    if (form.id) {
        form.put(route('',));
    }
    form.post(route('contratos.contratada.servicos.afugentamento.resgate.fauna.execucao.frente.supressao.create', form));
}

const salvarImagens = () => {
    form.post(route('',));
}

defineExpose({ abrirModal });

</script>

<template>
    <Modal ref="modalDetalhes" title="Frente de Supressão" modal-dialog-class="modal-xl">
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
                            {{ contrato.ufs }}
                            {{ contrato.brs }}
                            {{ contrato }}
                            <div id="dadosGerais" class="accordion-collapse collapse show" data-bs-parent="#servico">
                                <div class="accordion-body pt-0 card-body space-y-5">
                                    <div class="d-flex">
                                        <div class="col-lg-4 me-2">
                                            <InputLabel for="id" value="ID Frente" />
                                            <input id="id" type="number" class="form-control" v-model="form.id"
                                                autofocus placeholder="ID Frente" autocomplete="id" disabled />
                                            <InputError class="mt-2" :message="form.errors.id" />
                                        </div>

                                        <div class="col-lg-4 me-2">
                                            <InputLabel for="" value="Seleciona a Rodovia" />
                                            <select class="form-select" aria-label="Default select example">
                                                <option selected>Selecione a Rodovia</option>
                                                <option v-for="rodovia in contrato.trechos" :value="rodovia.id">{{ rodovia.rodovia }}</option>
                                                <option value="2">Frente 2</option>
                                                <option value="3">Frente 3</option>
                                            </select>
                                            <!-- <InputError class="mt-2" :message="form.errors." /> -->
                                        </div>


                                    </div>

                                    <div class="d-flex">
                                        <div class="col-lg-2 me-2">
                                            <InputLabel for="" value="UF inicial" />
                                            <select class="form-select" aria-label="Default select example">
                                                <option selected>Selecione a UF inicial</option>
                                                <option value="1">{{ contrato.uf }}</option>
                                                <option value="2">Frente 2</option>
                                                <option value="3">Frente 3</option>
                                                <!-- <InputError class="mt-2" :message="form.errors." /> -->
                                            </select>
                                        </div>

                                        <div class="col-lg-3 me-2">
                                            <InputLabel for="" value="KM inicial" />
                                            <input id="" type="text" class="form-control" v-model="form.km" autofocus
                                                placeholder="KM inicial" autocomplete="km_inicial" />
                                            <InputError class="mt-2" :message="form.errors.km" />
                                        </div>

                                        <div class="col-lg-3 me-2">
                                            <InputLabel for="" value="Latitude inicial" />
                                            <input id="" type="text" class="form-control" v-model="form.km" autofocus
                                                placeholder="Latitude inicial" autocomplete="latitude_inicial" />
                                            <InputError class="mt-2" :message="form.errors.km" />
                                        </div>

                                        <div class="col-lg-3 me-2">
                                            <InputLabel for="" value="Longitude inicial" />
                                            <input id="" type="text" class="form-control" v-model="form.km" autofocus
                                                placeholder="Longitude inicial" autocomplete="longitude_final" />
                                            <InputError class="mt-2" :message="form.errors.km" />
                                        </div>
                                    </div>

                                    <div class="d-flex">
                                        <div class="col-lg-2 me-2">
                                            <InputLabel for="" value="UF Final" />
                                            <select class="form-select" aria-label="Default select example">
                                                <option selected>Selecione a UF final</option>
                                                <option value="1">Frente 1</option>
                                                <option value="2">Frente 2</option>
                                                <option value="3">Frente 3</option>
                                                <!-- <InputError class="mt-2" :message="form.errors." /> -->
                                            </select>
                                        </div>

                                        <div class="col-lg-3 me-2">
                                            <InputLabel for="" value="KM Final" />
                                            <input id="" type="text" class="form-control" v-model="form.km" autofocus
                                                placeholder="KM final" autocomplete="km_Final" />
                                            <InputError class="mt-2" :message="form.errors.km" />
                                        </div>

                                        <div class="col-lg-3 me-2">
                                            <InputLabel for="" value="Latitude Final" />
                                            <input id="" type="text" class="form-control" v-model="form.km" autofocus
                                                placeholder="Latitude final" autocomplete="latitude_Final" />
                                            <InputError class="mt-2" :message="form.errors.km" />
                                        </div>

                                        <div class="col-lg-3 me-2">
                                            <InputLabel for="" value="Longitude Final" />
                                            <input id="" type="text" class="form-control" v-model="form.km" autofocus
                                                placeholder="Longitude final" autocomplete="longitude_final" />
                                            <InputError class="mt-2" :message="form.errors.km" />
                                        </div>
                                    </div>

                                    <div class="d-flex">
                                        <div class="col-lg-4 me-2">
                                            <InputLabel for="" value="Data Inicial" />
                                            <input id="" type="date" class="form-control" v-model="form.data_inicial"
                                                autofocus placeholder="Data inicial" autocomplete="data_inicial" />
                                            <InputError class="mt-2" :message="form.errors.data_inicial" />
                                        </div>

                                        <div class="col-lg-4 me-2">
                                            <InputLabel for="" value="Data Final" />
                                            <input id="" type="date" class="form-control" v-model="form.data_final"
                                                autofocus placeholder="Data final" autocomplete="data_final" />
                                            <InputError class="mt-2" :message="form.errors.data_final" />
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
                    </div>
                </div>
            </div>
        </template>
    </Modal>
</template>