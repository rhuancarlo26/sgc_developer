<template>
    <Modal ref="modalGerarACA" title="Gerar ACA" modal-dialog-class="modal-xl">
        <template #body>
            <div class="row my-3">
                <div class="col-md-6">
                    <InputLabel value="ID ACA" />
                    <input v-model="form.id_aca" type="text" class="form-control" />
                </div>
                <div class="col-md-6">
                    <InputLabel value="Data ACA" />
                    <input v-model="form.data_aca" type="date" class="form-control" />
                </div>
            </div>

            <div class="row my-3">
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
                <div class="col-md-3">
                    <InputLabel value="Empresa / Consórcio" />
                    <input v-model="form.empresa_consorcio" type="text" class="form-control" />
                </div>
                <div class="col-md-3">
                    <InputLabel value="Contrato" />
                    <input v-model="form.contrato" type="text" class="form-control" />
                </div>
            </div>

            <hr>

            <div class="row my-3">
                <div class="col-md-12">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID Ocorrência</th>
                                <th>Intensidade Ocorrência</th>
                                <th>Tipo Ocorrência</th>
                                <th>Data da Ocorrência</th>
                                <th>Data Fim</th>
                                <th>Ocorrência Anterior</th>
                                <th>Prazo Correção</th>
                                <th>Lote</th>
                                <th>Empresa/Consórcio</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(ocorrencia, index) in ocorrencias" :key="index">
                                <td>{{ ocorrencia.id_ocorrencia }}</td>
                                <td>{{ ocorrencia.intensidade_ocorrencia }}</td>
                                <td>{{ ocorrencia.tipo_ocorrencia }}</td>
                                <td>{{ ocorrencia.data_ocorrencia }}</td>
                                <td>{{ ocorrencia.data_fim }}</td>
                                <td>{{ ocorrencia.ocorrencia_anterior }}</td>
                                <td>{{ ocorrencia.prazo_correcao }}</td>
                                <td>{{ ocorrencia.lote }}</td>
                                <td>{{ ocorrencia.empresa_consorcio }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </template>
        <template #footer>
            <NavButton @click="gerarACA" type-button="success" title="Gerar ACA" />
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
    servico: { type: Object },
    ocorrencias: { type: Array, default: () => [] }
})

const modalGerarACA = ref();

const form = useForm({
    id_aca: '',
    data_aca: '',
    lote: '',
    nome_lote: '',
    empresa_consorcio: '',
    contrato: ''
});

const abrirModal = () => {
    modalGerarACA.value.getBsModal().show();
}

const fecharModal = () => {
    modalGerarACA.value.getBsModal().hide();
}

const gerarACA = () => {
    form.post(route('contratos.contratada.servicos.aca.store', { contrato: props.contrato.id, servico: props.servico.id }), {
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
