<template>
    <Modal ref="modalCadastrarVistorias" title="Cadastro de Vistorias" modal-dialog-class="modal-xl">
        <template #body>
            <div class="tab-content">
                <div class="row my-3">
                    <div class="col-md-3">
                        <InputLabel value="ID Ocorrência" />
                        <input v-model="form.id_ocorrencia" type="text" class="form-control" readonly />
                    </div>
                    <div class="col-md-3">
                        <InputLabel value="Data da Ocorrência" />
                        <input v-model="form.data_ocorrencia" type="date" class="form-control" />
                    </div>
                    <div class="col-md-3">
                        <InputLabel value="ID Vistoria" />
                        <input v-model="form.id_vistoria" type="text" class="form-control" />
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
            <NavButton @click="cadastrarVistoria" type-button="success" title="Cadastrar Vistoria" />

            <hr>

            <table class="table table-bordered table-striped table-hover js-basic-example">
                <thead>
                    <tr class="text-center">
                        <th>ID Vistoria</th>
                        <th>Data da Vistoria</th>
                        <th>Adicionar Imagens e Arquivos de Acordo de Prazo</th>
                        <th>Ação</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="text-center">
                        <td>ROA.21.230-PA.(Vis.1)</td>
                        <td>24/11/2023</td>
                        <td>
                            <span class="icon-container">
                                <a href="javascript:void(0);"><i class="fa fa-image"></i></a>
                            </span>
                        </td>
                        <td>
                            <div class="btn-group">
                                <div class="dropdown-menu-right">
                                    <a href="#" role="button" id="dropdownMenuLink-1" data-toggle="dropdown"
                                       aria-haspopup="true" aria-expanded="true" class="dropdown-toggle">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                             viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                             stroke-linecap="round" stroke-linejoin="round" class="feather feather-more-horizontal">
                                            <circle cx="12" cy="12" r="1"></circle>
                                            <circle cx="19" cy="12" r="1"></circle>
                                            <circle cx="5" cy="12" r="1"></circle>
                                        </svg>
                                    </a>
                                    <div aria-labelledby="dropdownMenuLink-1" class="dropdown-menu">
                                        <a href="javascript:void(0);" class="dropdown-item">Visualizar</a>
                                        <a href="javascript:void(0);" class="dropdown-item">Editar</a>
                                        <a href="javascript:void(0);" class="dropdown-item">Excluir</a>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </template>
        <template #footer>
            <NavButton @click="fecharModal" type-button="secondary" title="Fechar" />
        </template>
    </Modal>
</template>

<script setup>
import InputLabel from "@/Components/InputLabel.vue";
import Modal from "@/Components/Modal.vue";
import NavButton from "@/Components/NavButton.vue";
import { useForm } from "@inertiajs/vue3";
import { ref, watch } from "vue";

const props = defineProps({
    rnc: { type: Object, default: () => null }
})

const modalCadastrarVistorias = ref();

const form = useForm({
    id_ocorrencia: props.rnc?.nome_id || '',
    data_ocorrencia: props.rnc?.data_ocorrenciaF || '',
    id_vistoria: '',
    data_vistoria: '',
    ocorrencia_corrigida: '',
    descricao_ocorrencia: ''
});

// Watch para atualizar o valor de id_ocorrencia se rnc mudar
watch(() => props.rnc?.nome_id, (newValue) => {
    form.id_ocorrencia = newValue || '';
});

const abrirModal = () => {
    modalCadastrarVistorias.value.getBsModal().show();
}

const fecharModal = () => {
    modalCadastrarVistorias.value.getBsModal().hide();
}

const cadastrarVistoria = () => {
    // form.post(route('contratos.contratada.servicos.controledeocorrencias.ocorrencia.store', { contrato: props.contrato.id, servico: props.servico.id }), {
    //     onSuccess: () => fecharModal()
    // });
    console.log("cadastrarVistoria");
}

defineExpose({ abrirModal, fecharModal });
</script>

<style scoped>
.modal-xl {
    max-width: 80%;
}
</style>
