<script setup>
import {ref} from "vue";
import Modal from "@/Components/Modal.vue";
import {useForm} from "@inertiajs/vue3";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";

const props = defineProps({
    servico: {type: Object}
})

const form = useForm({
    chave: null,
    dt_inicial: null,
    dt_final: null,
    area_em_app: null,
    area_fora_app: null,
    local_shape_em_app: null,
    local_shape_fora_app: null,
    doc: null,
})

const modalRef = ref();
const abrirModal = () => {
    modalRef.value.getBsModal().show();
}

const save = () => {
    form
        .transform((data) => ({
            ...data,
            servico_id: props.servico.id,
        }))
        .post(route('contratos.contratada.servicos.supressao-vegetacao.configuracao.plano-supressao.store'), {
            preserveState: true,
            onSuccess: () => {
                modalRef.value.getBsModal().hide();
                form.reset();
            }
        })
}

defineExpose({abrirModal});
</script>

<template>
    <form @submit.prevent="save">
        <Modal ref="modalRef" title="Cadastro de plano de supressão" modal-dialog-class="modal-xl">
            <template #body>
                <div class="row row-gap-2">
                    <div class="col-lg-4">
                        <InputLabel value="Código" for="codigo"/>
                        <input v-model="form.chave" id="nome" class="form-control" disabled/>
                        <InputError :message="form.errors.chave"/>
                    </div>
                    <div class="col-lg-4">
                        <InputLabel value="Data inicial" for="dt_inicial"/>
                        <input v-model="form.dt_inicial" id="dt_inicial" type="date" class="form-control"/>
                        <InputError :message="form.errors.dt_inicial"/>
                    </div>
                    <div class="col-lg-4">
                        <InputLabel value="Data final" for="dt_inicial"/>
                        <input v-model="form.dt_final" id="dt_final" type="date" class="form-control"/>
                        <InputError :message="form.errors.dt_final"/>
                    </div>
                    <div class="col-12">
                        <InputLabel value="Área APP (há)" for="area_em_app"/>
                        <input v-model="form.area_em_app" id="area_em_app" type="number" step="0.1" class="form-control"/>
                        <InputError :message="form.errors.area_em_app"/>
                    </div>
                    <div class="col-12">
                        <InputLabel value="Shapefile em área de APP" for="local_shape_em_app"/>
                        <input @input="form.local_shape_em_app = $event.target.files[0]" id="local_shape_em_app"
                               type="file" class="form-control" accept=".zip">
                        <InputError :message="form.errors.local_shape_em_app"/>
                    </div>
                    <div class="col-12">
                        <InputLabel value="Área fora APP (há)" for="area_fora_app"/>
                        <input v-model="form.area_fora_app" id="area_fora_app" type="number" step="0.1" class="form-control"/>
                        <InputError :message="form.errors.area_fora_app"/>
                    </div>
                    <div class="col-12">
                        <InputLabel value="Shapefile em área fora de APP" for="local_shape_fora_app"/>
                        <input @input="form.local_shape_fora_app = $event.target.files[0]" id="local_shape_fora_app"
                               type="file" class="form-control" accept=".zip">
                        <InputError :message="form.errors.local_shape_fora_app"/>
                    </div>
                    <div class="col-12">
                        <InputLabel value="Arquivo de plano de supressão" for="doc"/>
                        <input @input="form.doc = $event.target.files[0]" id="doc" type="file" class="form-control"
                               accept=".pdf">
                        <InputError :message="form.errors.doc"/>
                    </div>
                </div>
            </template>
            <template #footer>
                <button @click="modalRef.getBsModal().hide()" type="button" class="btn btn-secondary">Fechar</button>
                <button type="submit" class="btn btn-success">Salvar</button>
            </template>
        </Modal>
    </form>
</template>
