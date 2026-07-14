<script setup>
import InputLabel from "@/Components/InputLabel.vue";
import Modal from "@/Components/Modal.vue";
import NavButton from "@/Components/NavButton.vue";
import {useForm} from "@inertiajs/vue3";
import {ref} from "vue";

const props = defineProps({
    contrato: {type: Object},
    servico: {type: Object}
})

const modalImportarPassagem = ref();

const form = useForm({
    arquivo: null
});

const abrirModal = () => {
    modalImportarPassagem.value.getBsModal().show();
}

const importarArquivo = () => {
    form.post(route('contratos.contratada.servicos.passagem_fauna.configuracao.passagem_fauna.importar', {
        contrato: props.contrato.id,
        servico: props.servico.id
    }), {
        onSuccess: () => modalImportarPassagem.value.getBsModal().hide()
    });
}

defineExpose({abrirModal});
</script>

<template>
    <Modal ref="modalImportarPassagem" title="Importar dados das passagens de fauna" modal-dialog-class="modal-xl">
        <template #body>
            <div class="mb-3">
                <InputLabel value="Arquivo"/>
                <div class="row g-2">
                    <div class="col">
                        <input @input="form.arquivo = $event.target.files[0]" type="file" class="form-control">
                    </div>
                    <div class="col-auto">
                        <NavButton @click="importarArquivo()" type-button="success" title="Importar"/>
                    </div>
                </div>
            </div>
        </template>
        <template #footer>
        </template>
    </Modal>
</template>
