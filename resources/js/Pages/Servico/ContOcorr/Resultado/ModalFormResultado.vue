<script setup>
import Modal from "@/Components/Modal.vue";
import {useForm} from "@inertiajs/vue3";
import {ref} from "vue";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import {IconDeviceFloppy, IconTrash} from "@tabler/icons-vue";
import NavButton from "@/Components/NavButton.vue";

const props = defineProps({
    contrato: {type: Object},
    servico: {type: Object}
})

const modalFormResultado = ref();

const form = useForm({
    id: null,
    nome: null,
    periodo: null,
    mes: null,
    semestre: null,
    ano: null,
    dt_inicio: null,
    dt_final: null
});

const abrirModal = (item) => {
    item ? Object.assign(form, item) : form.reset();

    modalFormResultado.value.getBsModal().show();
}

const salvarResultado = () => {
    const url = form.id ? 'update' : 'store';
    form.post(route('contratos.contratada.servicos.cont_ocorrencia.resultado.' + url, {
        contrato: props.contrato.id,
        servico: props.servico.id
    }), {
        onSuccess: () => modalFormResultado.value.getBsModal().hide()
    })
}

defineExpose({abrirModal});
</script>

<template>
    <Modal ref="modalFormResultado" title="Cadastro resultado" modal-dialog-class="modal-xl">
        <template #body>
            <div class="row mb-4">
                <div class="col">
                    <InputLabel value="ID resultado" for="id"/>
                    <input type="text" class="form-control" name="id" id="id" :value="form.id" disabled>
                </div>
                <div class="col">
                    <InputLabel value="Nome do resultado" for="nome"/>
                    <input type="text" class="form-control" name="nome" id="nome" v-model="form.nome">
                    <InputError :message="form.errors.nome"/>
                </div>
            </div>
            <div class="row mb-4">
                <div :class="form.periodo ? 'col' : 'col-3'">
                    <InputLabel value="Período" for="periodo"/>
                    <select class="form-control form-select" name="periodo" id="periodo" v-model="form.periodo">
                        <option value="M">Mensal</option>
                        <option value="S">Semestral</option>
                        <option value="A">Anual</option>
                        <option value="P">Período</option>
                    </select>
                    <InputError :message="form.errors.periodo"/>
                </div>
                <div class="col" v-show="form.periodo === 'M'">
                    <InputLabel value="Mês" for="mes"/>
                    <input type="month" class="form-control" name="mes" id="mes" v-model="form.mes">
                    <InputError :message="form.errors.mes"/>
                </div>
                <div class="col" v-show="form.periodo === 'S'">
                    <InputLabel value="Escolha o semestre" for="semestre"/>
                    <select class="form-control form-select" name="semestre" id="semestre" v-model="form.semestre">
                        <option value="1">1° Semestre</option>
                        <option value="2">2° Semestre</option>
                    </select>
                    <InputError :message="form.errors.semestre"/>
                </div>
                <div class="col" v-show="form.periodo === 'S' || form.periodo === 'A'">
                    <InputLabel value="Ano" for="ano"/>
                    <input type="number" class="form-control" name="ano" id="ano" v-model="form.ano">
                    <InputError :message="form.errors.ano"/>
                </div>
                <div class="col" v-show="form.periodo === 'P'">
                    <InputLabel value="Data início" for="dt_inicio"/>
                    <input type="date" class="form-control" name="dt_inicio" id="dt_inicio" v-model="form.dt_inicio">
                    <InputError :message="form.errors.dt_inicio"/>
                </div>
                <div class="col" v-show="form.periodo === 'P'">
                    <InputLabel value="Data final" for="dt_final"/>
                    <input type="date" class="form-control" name="dt_final" id="dt_final" v-model="form.dt_final">
                    <InputError :message="form.errors.dt_final"/>
                </div>
            </div>
            <div class="row">
                <div class="col d-flex justify-content-end">
                    <NavButton @click="salvarResultado()" type-button="success" :icon="IconDeviceFloppy"
                               :title="form.id ? 'Alterar' : 'Salvar'"/>
                </div>
            </div>
        </template>
        <template #footer>
        </template>
    </Modal>
</template>
