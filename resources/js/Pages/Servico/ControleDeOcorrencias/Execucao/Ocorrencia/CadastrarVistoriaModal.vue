<template>
    <Modal ref="modalCadastrarVistorias" title="Cadastro de Vistorias" modal-dialog-class="modal-xl">
        <template #body>
            <div class="tab-content">
                <div class="row my-3">
                    <div class="col-md-3">
                        <InputLabel value="ID Ocorrência" />
                        <input v-model="form.id_ocorrencia" type="text" class="form-control" />
                    </div>
                    <div class="col-md-3">
                        <InputLabel value="Data da Ocorrência" />
                        <input v-model="form.data_ocorrencia" type="date" class="form-control" />
                    </div>
                    <div class="col-md-3">
                        <InputLabel value="ID Vistoria" />
                        <input v-model="form.nome_ocorrencia" type="text" class="form-control" />
                    </div>
                    <div class="col-md-3">
                        <InputLabel value="Data da Vistoria" />
                        <input v-model="form.data_vistoria" type="date" class="form-control" />
                    </div>
                </div>
                <div class="row my-3">
                    <div class="col-md-12">
                        <InputLabel value="Ocorrência Corrigida:" />
                        <div>
                            <label>
                                <input type="radio" v-model="form.ocorrencia_corrigida" value="Sim" />
                                Sim
                            </label>
                            <label class="ms-3">
                                <input type="radio" v-model="form.ocorrencia_corrigida" value="Não" />
                                Não
                            </label>
                        </div>
                    </div>
                </div>
                <div class="row my-3">
                    <div class="col-md-12">
                        <InputLabel value="Observações da Vistoria" />
                        <textarea v-model="form.descricao_ocorrencia" class="form-control"></textarea>
                    </div>
                </div>
            </div>
        </template>
        <template #footer>
            <NavButton @click="cadastrarVistoria" type-button="success" title="Cadastrar Vistoria" />
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

const modalCadastrarVistorias = ref();

const form = useForm({
    id_ocorrencia: '',
    data_ocorrencia: '',
    nome_ocorrencia: '',
    data_vistoria: '',
    ocorrencia_corrigida: '',
    descricao_ocorrencia: ''
});

const abrirModal = () => {
    modalCadastrarVistorias.value.getBsModal().show();
}

const fecharModal = () => {
    modalCadastrarVistorias.value.getBsModal().hide();
}

const cadastrarVistoria = () => {
    // Substitua o comentário com a chamada para o backend, se necessário
    // form.post(route('contratos.contratada.servicos.controledeocorrencias.ocorrencia.store', { contrato: props.contrato.id, servico: props.servico.id }), {
    //     onSuccess: () => fecharModal()
    // });
    console.log("cadastrarVistoria")
}

defineExpose({ abrirModal });
</script>

<style scoped>
.modal-xl {
    max-width: 80%;
}
</style>
