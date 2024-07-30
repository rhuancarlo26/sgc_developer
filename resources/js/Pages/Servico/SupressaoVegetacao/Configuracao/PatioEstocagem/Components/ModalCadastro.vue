<script setup>
import {ref} from "vue";
import Modal from "@/Components/Modal.vue";
import {useForm} from "@inertiajs/vue3";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";

const props = defineProps({
    servico: {type: Object},
    tipos: {type: Array},
    licencas: {type: Array},
})

const form = useForm({
    id: null,
    chave: null,
    licenca_id: null,
    tipo_patio_id: null,
    shapefile: null,
    observacao: null,
    fotos: [],
})

const modalRef = ref();
const abrirModal = (item) => {
    form.reset()
    if (item != null) {
        Object.assign(form, item);
    }
    modalRef.value.getBsModal().show();
}

const save = () => {
    form.transform((data) => ({
        ...data,
        servico_id: props.servico.id,
    }));

    const onSuccess = () => {
        modalRef.value.getBsModal().hide();
        form.reset();
    }

    if(form.id !== null) {
        // A FAZER
        form.patch(route('contratos.contratada.servicos.supressao-vegetacao.configuracao.patio-estocagem.update', form.id), {
            preserveState: true,
            onSuccess
        })
        return
    }

    form.post(route('contratos.contratada.servicos.supressao-vegetacao.configuracao.patio-estocagem.store'), {
        preserveState: true,
        onSuccess
    })
}

defineExpose({abrirModal});
</script>

<template>
    <form @submit.prevent="save">
        <Modal ref="modalRef" title="Cadastro pátio de estocagem" modal-dialog-class="modal-xl">
            <template #body>
                <div class="row row-gap-2">
                    <div class="col-lg-4">
                        <InputLabel value="Código" for="codigo"/>
                        <input v-model="form.chave" id="nome" class="form-control" disabled/>
                        <InputError :message="form.errors.chave"/>
                    </div>
                    <div class="col-lg-4">
                        <InputLabel value="Numero da ASV" for="dt_inicial"/>
                        <v-select v-model="form.licenca_id" :options="licencas"
                                  :get-option-label='licenca => `${licenca.licenca.numero_licenca} - ${licenca.licenca.emissor} - ${licenca.licenca.tipo.sigla}`'
                                  :reduce="l => l.licenca.id"
                        >
                            <template #no-options="{}">
                                Nenhum registro encontrado.
                            </template>
                        </v-select>
                        <InputError :message="form.errors.licenca_id"/>
                    </div>
                    <div class="col-lg-4">
                        <InputLabel value="Tipo de pátio" for="tipo_patio_id"/>
                        <v-select :options="tipos" v-model="form.tipo_patio_id" label="nome" :reduce="t => t.id">
                            <template #no-options="{}">
                                Nenhum registro encontrado.
                            </template>
                        </v-select>
                        <InputError :message="form.errors.tipo_patio_id"/>
                    </div>
                    <div class="col-12">
                        <InputLabel value="Shapefile" for="local_shape_em_app"/>
                        <input @input="form.shapefile = $event.target.files[0]" id="shapefile"
                               type="file" class="form-control" accept=".zip">
                        <InputError :message="form.errors.shapefile"/>
                    </div>
                    <div class="col-12">
                        <InputLabel value="Observações" for="area_fora_app"/>
                        <textarea v-model="form.observacao" id="observacao" class="form-control"></textarea>
                        <InputError :message="form.errors.observacao"/>
                    </div>
                    <div class="col-12">
                        <InputLabel value="Anexar fotos" for="local_shape_fora_app"/>
                        <input @input="form.fotos = $event.target.files" id="fotos"
                               type="file" class="form-control" accept=".jpg, .jpeg, .png" multiple>
                        <InputError :message="form.errors.fotos"/>
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
