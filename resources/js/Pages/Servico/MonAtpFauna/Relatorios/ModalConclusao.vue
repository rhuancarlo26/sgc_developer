<script setup>

import {onMounted, ref, watch} from "vue";
import {Link, router, useForm} from "@inertiajs/vue3";
import Modal from "@/Components/Modal.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import Table from "@/Components/Table.vue";

const props = defineProps({
    showAction: {type: Boolean, default: true},
    campanhas: {type: Array},
    servico: {type: Object},
})

const form = useForm({
    id: null,
    nome_resultado: null,
    fk_campanha: null,
});

const modalRef = ref();
const onSuccess = () => {
    modalRef.value.getBsModal().hide();
    form.reset();
}
const save = () => {
    form.transform((data) => ({
        ...data,
        fk_servico: props.servico.id
    }));

    if (form.id !== null) {
        form.patch(route('contratos.contratada.servicos.mon_atp_fauna.resultado.update'), {
            preserveState: true,
            onSuccess
        })
        return
    }

    form.post(route('contratos.contratada.servicos.mon_atp_fauna.resultado.store'), {
        preserveState: true,
        onSuccess
    })
}

const saveResultadoCampanha = () => {
    form.transform((data) => ({
        ...data,
        fk_resultado: form.id,
    }));
    form.post(route('contratos.contratada.servicos.mon_atp_fauna.resultado.store-campanha'), {
        preserveState: true,
        preserveScroll: true,
        onSuccess
    })
}

const resultadosCampanha = ref([])
const abrirModal = async (item = null) => {
    form.reset()
    resultadosCampanha.value = [];
    if (item !== null) {
        Object.assign(form, item)
    }
    modalRef.value.getBsModal().show();
}

defineExpose({abrirModal});

</script>

<template>
    <form @submit.prevent="save">
        <Modal ref="modalRef" title="Resultado" modal-dialog-class="modal-xl">
            <template #body>
                <div class="row row-gap-2">
                    <div class="col-lg-12">
                        <InputLabel>Conclusão</InputLabel>
                        <textarea v-model="form.conclusao" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="col-lg-12">
                        <button type="button" @click="saveConclusao" class="btn btn-success">
                            Salvar Conclusão
                        </button>
                    </div>
                    <div class="col-lg-12">
                        <InputLabel value="Buscar arquivo (.pdf)" for="arquivo_relatorio_anexo"/>
                        <input @input="saveRelatorioAnexo" id="arquivo_relatorio_anexo" type="file" class="form-control" accept=".pdf">
                        <InputError :message="form.errors.arquivo_relatorio_anexo"/>
                    </div>
                    <div class="col-lg-12">
                        <Table
                            :columns="['Documentos anexados', 'Data', 'Ação']"
                            :records="{ data: [], links: [] }"
                            table-class="table-hover">
                            <template #body="{ item, key }">
                                <tr>
                                    <td>{{item.nome_arquivo}}</td>
                                    <td>{{item.created_atF}}</td>
                                    <td></td>
                                </tr>
                            </template>
                        </Table>
                    </div>
                </div>
            </template>
            <template #footer>
                <button @click="modalRef.getBsModal().hide()" type="button" class="btn btn-secondary">Fechar</button>
                <button v-if="showAction" type="submit" class="btn btn-success">Salvar</button>
            </template>
        </Modal>
    </form>
</template>
