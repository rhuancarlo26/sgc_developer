<script setup>
import Modal from "@/Components/Modal.vue";
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import { ref } from "vue";
import { useForm } from '@inertiajs/vue3';
import axios from "axios";
import { useToast } from "vue-toastification";
import { IconDots } from "@tabler/icons-vue";
import { usePage } from "@inertiajs/vue3";
import Swal from 'sweetalert2';

const toast = useToast();
const contrato = ref({});
const servico = ref({});
const tipo = ref({});
const arquivo = ref(null);
const relatorio = ref(null);
const anexos = ref([]);

const modalConclusaoRelatorio = ref(null);

const form = useForm({
    conclusao: ''
});

const abrirModal = (rel, tipoItem, itemContrato, itemServico) => {
    relatorio.value = rel;
    form.chave = rel.chave;
    form.conclusao = rel.conclusao ?? null;
    form.fk_status = rel.fk_status;
    form.id = rel.id;
    form.id_resultado = rel.id_resultado;
    form.id_servico = rel.id_servico;
    form.nome = rel.nome_relatorio;
    form.sobre_relatorio = rel.sobre_relatorio;
    form.parecer_fiscal = rel.parecer_fiscal;
    tipo.value = tipoItem;
    contrato.value = itemContrato;
    servico.value = itemServico;
    getAnexo();
    modalConclusaoRelatorio.value.getBsModal().show();
}

const setAnexo = async (event) => {
    arquivo.value = event.target.files[0];

    let formData = new FormData();
    formData.append('arquivo', arquivo.value);

    axios.post(route('contratos.contratada.servicos.afugentamento.resgate.fauna.relatorios.anexo.create', { relatorio: form.id }), formData)
        .then(response => {
            toast.success('Anexo salvo com sucesso!');
            arquivo.value = null;
            getAnexo();
        }).catch(error => {
            console.error(error);
        });
}

const getAnexo = () => {
    axios.get(route('contratos.contratada.servicos.afugentamento.resgate.fauna.relatorios.anexo.get', { relatorio: form.id }))
        .then(response => {
            anexos.value = response.data;
        })
        .catch(error => {
            console.error(error);
        });
}

const salvar = () => {
    if (form.id && relatorio.value.conclusao) {
        form.patch(route('contratos.contratada.servicos.afugentamento.resgate.fauna.relatorios.update.conclusao', form.id), {
            preserveScroll: true,
            onSuccess: () => {
                toast.success('Conclusão atualizada com sucesso!');
                modalConclusaoRelatorio.value.getBsModal().hide();
            }
        });
        return;
    }

    form.post(route('contratos.contratada.servicos.afugentamento.resgate.fauna.relatorios.create.conclusao', form.id), {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Conclusão salva com sucesso!');
            modalConclusaoRelatorio.value.getBsModal().hide();
        }
    });
}

const destroy = (anexo) => {
    Swal.fire({
        title: "Excluir Anexo",
        text: "Deseja continuar?",
        icon: "warning",
        showCloseButton: true,
        showCancelButton: true,
        focusConfirm: false,
    }).then((r) => {
        if (r.isConfirmed) {
            axios.delete(route('contratos.contratada.servicos.afugentamento.resgate.fauna.relatorios.anexo.delete', { anexo: anexo }))
                .then(response => {
                    toast.success('Anexo excluído com sucesso!');
                    getAnexo();
                })
                .catch(error => {
                    console.error(error);
                });
        }
    })
}

const closeModal = () => {
    console.log('closeModal');

    modalConclusaoRelatorio.value.getBsModal().hide();
    form.clearErrors();
    form.reset();
};

defineExpose({ abrirModal });

</script>

<template>
    <form @submit.prevent="salvar">
        <Modal ref="modalConclusaoRelatorio" :title="'Conclusão do Relatório ' + form.nome"
            modal-dialog-class="modal-xl" modalClass="modal-blur">
            <template #body>
                <div class="d-flex col-md-12 justify-content-between mb-4">
                    <div class="col-md-12">
                        <InputLabel for="conclusao" value="Conclusão" />
                        <textarea id="conclusao" v-model="form.conclusao" class="form-control"></textarea>
                        <InputError :message="form.errors.conclusao" />
                    </div>
                </div>
                <button type="submit" class="btn btn-success mb-4">Salvar</button>
                <div class="d-flex col-md-12 justify-content-between">
                    <div class="col-md-12 mb-4">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <div class="custom-file-container" id="file" data-upload-id="myFirstImage">
                                        <InputLabel for="file" value="Buscar arquivo (.pdf)" />
                                        <input type="file" accept=".pdf" @change="setAnexo" id="file"
                                            class="form-control">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="d-flex col-md-12 justify-content-between mb-4">
                    <table id="table-resultado-analise"
                        class="table table-bordered table-striped table-hover js-basic-example dataTable"
                        style="width:100%;">
                        <thead>
                            <tr class="bg-light text-center">
                                <th>Documentos anexados</th>
                                <th>Data</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="anexos.length > 0" class="text-center" v-for="item in anexos" :key="item.id">
                                <td>{{ item.nome_arquivo }}</td>
                                <td>{{ item.created_at }}</td>
                                <td>
                                    <button class="btn dropdown-toggle align-text-top" data-bs-boundary="viewport"
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                        <IconDots />
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                        <li>
                                            <a class="dropdown-item"
                                                :href="usePage().props.app_url + '/storage/' + item.caminho_arquivo"
                                                target="_blank">
                                                Visualizar anexo
                                            </a>
                                        </li>

                                        <li>
                                            <a class="dropdown-item" @click="destroy(item.id)"
                                                href="javascript:void(0);">
                                                Deletar anexo
                                            </a>
                                        </li>
                                    </ul>
                                </td>
                            </tr>
                            <tr v-else>
                                <td colspan="3" class="text-center">Nenhum documento anexado</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </template>
            <template #footer>
                <button class="btn btn-danger" type="button" @click="closeModal">Cancelar</button>
            </template>
        </Modal>
    </form>
</template>
