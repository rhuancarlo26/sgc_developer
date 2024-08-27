<script setup>
import Modal from "@/Components/Modal.vue";
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import { ref } from "vue";
import { useForm } from '@inertiajs/vue3';

const modalDetalhes = ref(null);
const servico = ref({});

const abrirModal = (itemServico) => {
    servico.value = itemServico;
    modalDetalhes.value.getBsModal().show();
}

const updateModal = (itemFrente) => {
    form.id = itemFrente.id;
    form.rodovia = itemFrente.rodovia;
    form.uf_inicial = itemFrente.uf_inicial;
    form.km_inicial = itemFrente.km_inicial;
    form.latitude_inicial = itemFrente.latitude_inicial;
    form.longitude_inicial = itemFrente.longitude_inicial;
    form.uf_final = itemFrente.uf_final;
    form.km_final = itemFrente.km_final;
    form.latitude_final = itemFrente.latitude_final;
    form.longitude_final = itemFrente.longitude_final;
    form.data_inicial = itemFrente.data_inicial;
    form.data_final = itemFrente.data_final;
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
    console.log(form);

    if (form.id) {
        form.patch(route('contratos.contratada.servicos.afugentamento.resgate.fauna.execucao.frente.supressao.update', form.id), {
            preserveState: true,
            onSuccess: () => {
                modalDetalhes.value.getBsModal().hide();
                form.reset();
            },
        });
    }

    form.post(route('contratos.contratada.servicos.afugentamento.resgate.fauna.execucao.frente.supressao.create', servico.value.id), {
        preserveState: true,
        onSuccess: () => {
            modalDetalhes.value.getBsModal().hide();
            form.reset();
        },
    });
}

const salvarImagens = () => {
    // form.post(route('',));
}

defineExpose({ abrirModal, updateModal });

</script>

<template>
    <form @submit.prevent="salvar">
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
                                <div id="dadosGerais" class="accordion-collapse collapse show"
                                    data-bs-parent="#servico">
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
                                                <select class="form-select" aria-label="Default select example"
                                                    v-model="form.rodovia">
                                                    <option selected>Selecione a Rodovia</option>
                                                    <option v-for="rodovia in contrato.trechos"
                                                        :value="rodovia.rodovia.id">
                                                        {{ rodovia.uf?.uf }} - {{ rodovia.rodovia?.rodovia }}
                                                    </option>
                                                </select>
                                                <InputError class="mt-2" :message="form.errors.rodovia" />
                                            </div>
                                        </div>

                                        <div class="d-flex">
                                            <div class="col-lg-2 me-2">
                                                <InputLabel for="" value="UF inicial" />
                                                <select class="form-select" aria-label="Default select example"
                                                    v-model="form.uf_inicial">
                                                    <option selected disabled>Selecione a UF inicial</option>
                                                    <option v-for="uf in contrato.trechos" :value="uf.uf.id">
                                                        {{ uf.uf?.uf }}
                                                    </option>
                                                    <InputError class="mt-2" :message="form.errors.uf_inicial" />
                                                </select>
                                            </div>

                                            <div class="col-lg-3 me-2">
                                                <InputLabel for="" value="KM inicial" />
                                                <input id="" type="text" class="form-control" v-model="form.km_inicial"
                                                    autofocus placeholder="KM inicial" autocomplete="km_inicial" />
                                                <InputError class="mt-2" :message="form.errors.km_inicial" />
                                            </div>

                                            <div class="col-lg-3 me-2">
                                                <InputLabel for="" value="Latitude inicial" />
                                                <input id="" type="text" class="form-control"
                                                    v-model="form.latitude_inicial" autofocus
                                                    placeholder="Latitude inicial" autocomplete="latitude_inicial" />
                                                <InputError class="mt-2" :message="form.errors.latitude_inicial" />
                                            </div>

                                            <div class="col-lg-3 me-2">
                                                <InputLabel for="" value="Longitude inicial" />
                                                <input id="" type="text" class="form-control"
                                                    v-model="form.longitude_inicial" autofocus
                                                    placeholder="Longitude inicial" autocomplete="longitude_final" />
                                                <InputError class="mt-2" :message="form.errors.longitude_inicial" />
                                            </div>
                                        </div>

                                        <div class="d-flex">
                                            <div class="col-lg-2 me-2">
                                                <InputLabel for="" value="UF Final" />
                                                <select class="form-select" aria-label="Default select example"
                                                    v-model="form.uf_final">
                                                    <option selected disabled>Selecione a UF final</option>
                                                    <option v-for="uf in contrato.trechos" :value="uf.uf.id">
                                                        {{ uf.uf?.uf }}
                                                    </option>
                                                    <InputError class="mt-2" :message="form.errors.uf_final" />
                                                </select>
                                            </div>

                                            <div class="col-lg-3 me-2">
                                                <InputLabel for="" value="KM Final" />
                                                <input id="" type="text" class="form-control" v-model="form.km_final"
                                                    autofocus placeholder="KM final" autocomplete="km_Final" />
                                                <InputError class="mt-2" :message="form.errors.km_final" />
                                            </div>

                                            <div class="col-lg-3 me-2">
                                                <InputLabel for="" value="Latitude Final" />
                                                <input id="" type="text" class="form-control"
                                                    v-model="form.latitude_final" autofocus placeholder="Latitude final"
                                                    autocomplete="latitude_Final" />
                                                <InputError class="mt-2" :message="form.errors.latitude_final" />
                                            </div>

                                            <div class="col-lg-3 me-2">
                                                <InputLabel for="" value="Longitude Final" />
                                                <input id="" type="text" class="form-control"
                                                    v-model="form.longitude_final" autofocus
                                                    placeholder="Longitude final" autocomplete="longitude_final" />
                                                <InputError class="mt-2" :message="form.errors.longitude_final" />
                                            </div>
                                        </div>

                                        <div class="d-flex">
                                            <div class="col-lg-4 me-2">
                                                <InputLabel for="" value="Data Inicial" />
                                                <input id="" type="date" class="form-control"
                                                    v-model="form.data_inicial" autofocus placeholder="Data inicial"
                                                    autocomplete="data_inicial" />
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
                                            <button type="submit" class="btn btn-success" aria-label="Button"
                                                :disabled="form.processing">
                                                Salvar
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </Modal>
    </form>
</template>