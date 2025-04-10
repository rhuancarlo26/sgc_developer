<script setup>
import Modal from "@/Components/Modal.vue";
import {ref} from "vue";
import {router, useForm} from "@inertiajs/vue3";
import InputError from "@/Components/InputError.vue";

const modalRef = ref({});
const malhaViaria = ref({});

const form = useForm({
    id: null,
    shapefile: null,
    fk_servico_licenca: null,
});
const abrirModal = (item) => {
    malhaViaria.value = item;
    form.id = item.id;
    form.fk_servico_licenca = item.fk_servico_licenca;
    modalRef.value.getBsModal().show();
}

const shapefile = ref(null)

const handleFile = (e) => {
    form.shapefile = e.target.files[0];
}

const saveShapefile = () => {
    form.post(route('contratos.contratada.servicos.mon_atp_fauna.configuracoes.malha_viaria.store_shapefile'), {
        onSuccess: () => {
            modalRef.value.getBsModal().hide();
            shapefile.value = null;
        }
    })
}

defineExpose({abrirModal});
</script>

<template>
    <Modal ref="modalRef" title="Adicionar Shapefile" >
        <template #body>
            <div class="row">
                <div class="col-12">
                    <div class="mb-3 error-placeholder">
                        <div class="custom-file-container" data-upload-id="myFirstImage">
                            <input type="file" @change="handleFile" id="arquivo_shapefile" class="form-control" accept=".zip">
                            <InputError :message="form.errors.shapefile" />
                        </div>
                    </div>
                </div>
            </div>
        </template>
        <template #footer>
            <button type="button" @click="saveShapefile" class="btn btn-success" data-dismiss="modal" aria-label="Close">
                Salvar
            </button>
        </template>
    </Modal>
</template>
