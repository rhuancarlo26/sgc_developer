<script setup>
import { ref } from "vue";
import Modal from "@/Components/Modal.vue";
import { useForm } from "@inertiajs/vue3";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";

const props = defineProps({
    servico: { type: Object }
})

const form = useForm({
    id: null,
    chave: null,
    dt_inicial: null,
    dt_final: null,
    area_em_app: null,
    area_fora_app: null,
    local_shape_em_app: null,
    local_shape_fora_app: null,
    servico_id: null,
    doc: null,
})

const modalRef = ref();
const dados = ref(null);
const abrirModal = (d = null) => {
    dados.value = d;
    if (d) {
        form.id = d.id;
        form.chave = d.chave;
        form.dt_inicial = d.dt_inicial;
        form.dt_final = d.dt_final;
        form.area_em_app = d.area_em_app;
        form.servico_id = d.servico_id;
        form.area_fora_app = d.area_fora_app;
        form.local_shape_em_app = null;
        form.local_shape_fora_app = null;
        form.doc = null;
    }
    modalRef.value.getBsModal().show();
}

const save = () => {
    form
        .transform((data) => ({
            ...data,
            servico_id: form.servico_id ?? props.servico.id,
        }))
        .submit(
            'post',
            route('contratos.contratada.servicos.supressao-vegetacao.configuracao.plano-supressao.store'),
            {
                preserveState: true,
                forceFormData: true,
                onSuccess: () => {
                    fecharModal();
                },
            }
        );
};

const inputShapeApp = ref();
const inputShapeForaApp = ref();
const inputDoc = ref();

const fecharModal = () => {
    modalRef.value.getBsModal().hide();
    form.reset();
    form.id = null;
    inputShapeApp.value.value = null;
    inputShapeForaApp.value.value = null;
    inputDoc.value.value = null;
    dados.value = null;
};

defineExpose({ abrirModal });
</script>

<template>
    <form @submit.prevent="save" enctype="multipart/form-data">
        <Modal ref="modalRef" title="Cadastro de plano de supressão" modal-dialog-class="modal-xl">
            <template #body>
                <div class="row row-gap-2">
                    <div class="col-lg-4">
                        <InputLabel value="Código" for="codigo" />
                        <input v-model="form.chave" id="nome" class="form-control" disabled />
                        <InputError :message="form.errors.chave" />
                    </div>
                    <div class="col-lg-4">
                        <InputLabel value="Data inicial" for="dt_inicial" />
                        <input v-model="form.dt_inicial" id="dt_inicial" type="date" class="form-control" />
                        <InputError :message="form.errors.dt_inicial" />
                    </div>
                    <div class="col-lg-4">
                        <InputLabel value="Data final" for="dt_inicial" />
                        <input v-model="form.dt_final" id="dt_final" type="date" class="form-control" />
                        <InputError :message="form.errors.dt_final" />
                    </div>
                    <div class="col-12">
                        <InputLabel value="Área APP (ha)" for="area_em_app" />
                        <input v-model="form.area_em_app" id="area_em_app" type="number" step="0.1"
                            class="form-control" />
                        <InputError :message="form.errors.area_em_app" />

                    </div>
                    <div class="col-12">
                        <InputLabel value="Shapefile em área de APP (.ZIP)" for="local_shape_em_app" />
                        <input ref="inputShapeApp" @input="form.local_shape_em_app = $event.target.files[0]"
                            id="local_shape_em_app" type="file" class="form-control" accept=".zip">
                        <InputError :message="form.errors.local_shape_em_app" />
                        <div v-if="dados?.local_shape_em_app">
                            <small class="text-muted">Shape APP já carregado</small>
                        </div>

                    </div>
                    <div class="col-12">
                        <InputLabel value="Área fora APP (há)" for="area_fora_app" />
                        <input v-model="form.area_fora_app" id="area_fora_app" type="number" step="0.1"
                            class="form-control" />
                        <InputError :message="form.errors.area_fora_app" />

                    </div>
                    <div class="col-12">
                        <InputLabel value="Shapefile em área fora de APP (.ZIP)" for="local_shape_fora_app" />
                        <input ref="inputShapeForaApp" @input="form.local_shape_fora_app = $event.target.files[0]"
                            id="local_shape_fora_app" type="file" class="form-control" accept=".zip">
                        <InputError :message="form.errors.local_shape_fora_app" />
                        <div v-if="dados?.local_shape_fora_app">
                            <small class="text-muted">Shape FORA APP já carregado</small>
                        </div>
                    </div>
                    <div class="col-12">
                        <InputLabel value="Arquivo de plano de supressão" for="doc" />
                        <input ref="inputDoc" @input="form.doc = $event.target.files[0]" id="doc" type="file"
                            class="form-control" accept=".pdf">
                        <InputError :message="form.errors.doc" />
                        <div v-if="dados?.arquivo">
                            <small class="text-muted">Arquivo atual: {{ dados.arquivo.nome_arquivo }}</small>
                        </div>
                    </div>
                </div>
            </template>
            <template #footer>
                <button @click="fecharModal" type="button" class="btn btn-secondary">Fechar</button>
                <button type="submit" class="btn btn-success">Salvar</button>
            </template>
        </Modal>
    </form>
</template>
