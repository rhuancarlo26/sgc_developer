<script setup>
import Modal from "@/Components/Modal.vue";
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import { ref } from "vue";
import { useForm } from '@inertiajs/vue3';

const modalResultado = ref(null);
const servico = ref({});

const abrirModal = (itemServico) => {
    servico.value = itemServico;
    modalResultado.value.getBsModal().show();
}

const preencherCampos = () => {
    if (form.periodo === 'M') {
        form.mes = form.dt_inicio.slice(0, 7);
    } else if (form.periodo === 'S') {
        form.semestre = form.dt_inicio.slice(5, 7) === '01' ? '1' : '2';
        form.ano = form.dt_inicio.slice(0, 4);
    } else if (form.periodo === 'A') {
        form.ano = form.dt_inicio.slice(0, 4);
    } else if (form.periodo === 'P') {
        form.dt_inicio = form.dt_inicio;
        form.dt_final = form.dt_final;
    }
}

const updateModal = (item) => {
    console.log(item);

    form.id = item.id;
    form.nome = item.nome;
    form.periodo = item.periodo;
    form.mes = '';
    form.ano = '';
    form.semestre = '';
    form.dt_inicio = item.dt_inicio;
    form.dt_final = item.dt_final;

    preencherCampos();
    modalResultado.value.getBsModal().show();
}

const form = useForm({
    id: null,
    nome: '',
    periodo: '',
    mes: '',
    ano: '',
    semestre: '',
    dt_inicio: '',
    dt_final: '',
});

const salvar = () => {
    if (form.id) {
        form.patch(route('contratos.contratada.servicos.afugentamento.resgate.fauna.resultado.update', form.id), {
            preserveScroll: true,
            onSuccess: () => {
                modalResultado.value.getBsModal().hide();
                form.reset();
            }
        });
        return;
    }

    form.post(route('contratos.contratada.servicos.afugentamento.resgate.fauna.resultado.create', servico.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            modalResultado.value.getBsModal().hide();
            form.reset();
        }
    });
}

defineExpose({ abrirModal, updateModal });
</script>

<template>
    <form @submit.prevent="salvar">
        <Modal ref="modalResultado" title="Novo Resultado" modal-dialog-class="modal-xl"  modalClass="modal-blur">
            <template #body>
                <div class="d-flex col-md-12 justify-content-between mb-4">
                    <div class="col-md-3">
                        <InputLabel for="id" value="ID" />
                        <input type="text" id="id" v-model="form.id" class="form-control" disabled>
                        <InputError :message="form.errors.id" />
                    </div>
                    <div class="col-md-8">
                        <InputLabel for="nome" value="Nome" />
                        <input type="text" id="nome" v-model="form.nome" class="form-control">
                        <InputError :message="form.errors.nome" />
                    </div>
                </div>

                <div class="col-md-12 mb-4">
                    <InputLabel for="periodo" value="Período" />
                    <select class="form-control col-md-6" v-model="form.periodo" id="periodo">
                        <option value="" :disabled="!form.periodo">Selecione uma opção</option>
                        <option value="M">Mensal</option>
                        <option value="S">Semestral</option>
                        <option value="A">Anual</option>
                        <option value="P">Período</option>
                    </select>
                    <InputError :message="form.errors.periodo" />
                </div>

                <div class="col-md-12" v-if="form.periodo == 'M'">
                    <InputLabel for="mes" value="Mês" />
                    <input type="month" id="mes" v-model="form.mes" class="form-control">
                    <InputError :message="form.errors.mes" />
                </div>

                <div class="col-12" v-if="form.periodo == 'S'">
                    <InputLabel for="semestre" value="Escolha o semestre" />
                    <select class="form-control col-md-6" v-model="form.semestre" id="semestre">
                        <option value="1">1º Semestre</option>
                        <option value="2">2º Semestre</option>
                    </select>
                    <InputError :message="form.errors.semestre" />
                </div>

                <div class="col-12" v-if="form.periodo == 'S' || form.periodo == 'A'">
                    <InputLabel for="ano" value="Ano" />
                    <input id="ano" v-model="form.ano" class="form-control">
                    <InputError :message="form.errors.ano" />
                </div>

                <div class="col-12" v-if="form.periodo == 'P'">
                    <InputLabel for="dt_inicio" value="Data Início" />
                    <input type="date" id="dt_inicio" v-model="form.dt_inicio" class="form-control">
                    <InputError :message="form.errors.dt_inicio" />

                    <InputLabel for="dt_final" value="Data Final" />
                    <input type="date" id="dt_final" v-model="form.dt_final" class="form-control">
                    <InputError :message="form.errors.dt_final" />
                </div>
            </template>
            <template #footer>
                <button type="submit" class="btn btn-success">Salvar</button>
            </template>
        </Modal>
    </form>
</template>