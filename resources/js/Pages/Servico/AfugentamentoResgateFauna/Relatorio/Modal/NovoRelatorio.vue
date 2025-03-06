<script setup>
import Modal from "@/Components/Modal.vue";
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import { ref } from "vue";
import { useForm } from '@inertiajs/vue3';
import { useToast } from "vue-toastification";

const toast = useToast();
const modalRelatorio = ref(null);
const contrato = ref({});
const servico = ref({});
const tipo = ref({});

const abrirModal = (itemContrato, itemServico) => {
    contrato.value = itemContrato;
    servico.value = itemServico;
    getResultados();
    modalRelatorio.value.getBsModal().show();
}

const updateModal = (rel, tipoItem, itemContrato, itemServico) => {
    form.chave = rel.chave;
    form.conclusao = rel.conclusao;
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
    getResultados();
    modalRelatorio.value.getBsModal().show();
}

const form = useForm({
    nome: '',
    id_resultado: null,
    sobre_relatorio: '',
});

const resultado = ref([]);

const getResultados = () => {
    axios.get(route('contratos.contratada.servicos.afugentamento.resgate.fauna.resultado.dados', { contrato: contrato.value.id, servico: servico.value.id }))
        .then(response => {
            resultado.value = response.data.resultado.data;
        })
        .catch(error => {
            console.error(error);
        });
};

const salvar = () => {
    if (form.id && tipo.value === 0) {
        form.patch(route('contratos.contratada.servicos.afugentamento.resgate.fauna.relatorios.update', form.id), {
            preserveScroll: true,
            onSuccess: () => {
                modalRelatorio.value.getBsModal().hide();
                form.reset();
                toast.success('Relatório atualizado com sucesso!');
            },
            onError: (errors) => {
                console.error(errors);
            }
        });
        return;
    }

    form.post(route('contratos.contratada.servicos.afugentamento.resgate.fauna.relatorios.create', { contrato: contrato.value.id, servico: servico.value.id }), {
        onSuccess: () => {
            modalRelatorio.value.getBsModal().hide();
            form.reset();
            toast.success('Relatório salvo com sucesso!');
        },
        onError: (errors) => {
            console.error(errors);
        }
    });
};

const closeModal = () => {
    modalRelatorio.value.getBsModal().hide();
    form.clearErrors();
    form.reset();
};

defineExpose({ abrirModal, updateModal });

</script>

<template>
    <form @submit.prevent="salvar">
        <Modal ref="modalRelatorio" title="Novo Relatório" modal-dialog-class="modal-xl" modalClass="modal-blur">
            <template #body>
                <div class="col-md-12 justify-content-between mb-4">
                    <InputLabel for="nome" value="Nome" />
                    <input v-model="form.nome" type="text" id="nome" name="nome" class="form-control" />
                    <InputError :message="form.errors.nome" />
                </div>

                <div class="col-md-12 justify-content-between mb-4">
                    <table id="table-resultado-analise"
                        class="table table-bordered table-striped table-hover js-basic-example dataTable"
                        style="width:100%;">
                        <thead>
                            <tr class="bg-light text-center">
                                <th rowspan="3">Nome do Resultado</th>
                                <th rowspan="3">Data Início</th>
                                <th rowspan="3">Data Final</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="text-center" v-for="item in resultado" :key="item.id">
                                <td class="text-start">
                                    <input type="radio" :id="item.id" :value="item.id" v-model="form.id_resultado">
                                    {{ item.nome }}
                                </td>
                                <td>{{ item.data_inicio_f }}</td>
                                <td>{{ item.data_final_f }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div>
                    <InputLabel for="sobre_relatorio" value="Sobre o Relatório" />
                    <textarea v-model="form.sobre_relatorio" type="text" id="sobre_relatorio" name="sobre_relatorio"
                        class="form-control">
                    </textarea>
                    <InputError :message="form.errors.sobre_relatorio" />
                </div>
            </template>
            <template #footer>
                <button class="btn btn-danger" type="button" @click="closeModal">Cancelar</button>
                <button type="submit" class="btn btn-success">Salvar</button>
            </template>
        </Modal>
    </form>
</template>