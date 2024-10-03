<script setup>
import {useForm} from "@inertiajs/vue3";
import Table from "@/Components/Table.vue";
import NavButton from "@/Components/NavButton.vue";
import {computed, ref} from "vue";
import {IconDeviceFloppy} from "@tabler/icons-vue";
import {dateTimeFormat} from "@/Utils/DateTimeUtils";
import Modal from "@/Components/Modal.vue";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";

const modalGerarACA = ref();

const props = defineProps({
    contrato: {type: Object},
    servico: {type: Object},
    lotes: {type: Array},
    ocorrencias: {type: Array}
});

const form = useForm({
    nome_id: null,
    data_aca: null,
    lote: null,
    id_lote: null,
    nome_lote: null,
    empresa: null,
    num_contrato: null,
    ocorrencias: []
});

const reset = () => {
    form.nome_id = null;
    form.data_aca = null;
    form.lote = null;
    form.id_lote = null;
    form.nome_lote = null;
    form.empresa = null;
    form.num_contrato = null;
    form.ocorrencias = [];
}

const abrirModal = () => {
    reset();

    modalGerarACA.value.getBsModal().show();
}

const salvarACA = () => {
    form.post(route('contratos.contratada.servicos.cont_ocorrencia.execucao.aca.store', {
        contrato: props.contrato.id,
        servico: props.servico.id
    }), {
        onSuccess: () => modalGerarACA.value.getBsModal().hide()
    });
}

const changeLote = () => {
    if (form.lote) {
        form.id_lote = form.lote?.id;
        form.nome_lote = form.lote?.nome;
        form.empresa = form.lote?.empresa;
        form.num_contrato = form.lote?.num_contrato;
    }
}

const filtroOcorrencia = computed(() => {
    return props.ocorrencias.filter(ocorrencia => ocorrencia.lote?.id == form.lote?.id);
})

defineExpose({abrirModal});
</script>

<template>
    <Modal ref="modalGerarACA" title="Cadastro de Atestados de Conformidade Ambiental" modal-dialog-class="modal-xl">
        <template #body>
            <div class="row mb-4">
                <div class="col">
                    <InputLabel value="ID ACA" for=""/>
                    <input type="text" class="form-control" v-model="form.nome_id" disabled>
                    <InputError message=""/>
                </div>
                <div class="col">
                    <InputLabel value="Data ACA" for=""/>
                    <input type="date" class="form-control" v-model="form.data_aca">
                    <InputError message=""/>
                </div>
            </div>
            <div class="row mb-4">
                <div class="col">
                    <InputLabel value="Lote"/>
                    <select @change="changeLote()" class="form-select" v-model="form.lote">
                        <option v-for="lote in lotes" :key="lote.id" :value="lote">{{ lote.nome_id }}</option>
                    </select>
                    <InputError message=""/>
                </div>
                <div class="col">
                    <InputLabel value="Nome lote" for=""/>
                    <input type="text" class="form-control" v-model="form.nome_lote" disabled>
                    <InputError message=""/>
                </div>
                <div class="col">
                    <InputLabel value="Empresa / Consórcio" for=""/>
                    <input type="text" class="form-control" v-model="form.empresa" disabled>
                    <InputError message=""/>
                </div>
                <div class="col">
                    <InputLabel value="Contrato" for=""/>
                    <input type="text" class="form-control" v-model="form.num_contrato" disabled>
                    <InputError message=""/>
                </div>
            </div>
            <div class="row mb-4">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table card-table table-bordered table-hover">
                            <thead>
                            <tr>
                                <th>ID ocorrência</th>
                                <th>Intensidade ocorrência</th>
                                <th>Tipo ocorrência</th>
                                <th>Data da ocorrência</th>
                                <th>Data fim</th>
                                <th>Ocorrência anterior</th>
                                <th>Prazo correção</th>
                                <th>Lote</th>
                                <th>Empresa / Consórcio</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="ocorrencia in filtroOcorrencia" :key="ocorrencia.id">
                                <td>
                                    <label class="form-check">
                                        <input class="form-check-input" type="checkbox" :value="ocorrencia"
                                               v-model="form.ocorrencias">
                                        <span class="form-check-label"> {{ ocorrencia.id }} </span>
                                    </label>
                                </td>
                                <td>{{ ocorrencia.nome_id }}</td>
                                <td>{{ ocorrencia.intensidade }}</td>
                                <td>{{ dateTimeFormat(ocorrencia.data_ocorrencia) }}</td>
                                <td>-</td>
                                <td>-</td>
                                <td>{{ ocorrencia.prazo }}</td>
                                <td>{{ ocorrencia.lote?.nome_id }}</td>
                                <td>{{ ocorrencia.lote?.empresa }}</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col d-flex justify-content-end">
                    <NavButton @click="salvarACA()" type-button="success" :icon="IconDeviceFloppy"
                               title="Salvar"/>
                </div>
            </div>
        </template>
        <template #footer>
        </template>
    </Modal>
</template>
