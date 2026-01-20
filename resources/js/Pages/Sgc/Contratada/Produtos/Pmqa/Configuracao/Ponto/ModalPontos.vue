<script setup>
import InputLabel from "@/Components/InputLabel.vue";
import Modal from "@/Components/Modal.vue";
import NavButton from "@/Components/NavButton.vue";
import { ref, nextTick } from "vue";
import axios from "axios";

const props = defineProps({
    pmqa: { type: Object, required: true },
    contrato: { type: [Object, Number, String], required: true },
    produto: { type: [Object, String], required: true },
});

const emit = defineEmits(["importacaoConcluida"]);

const modalImportarPonto = ref();
const selectedFile = ref(null);
const isProcessing = ref(false);

const abrirModal = async () => {
    await nextTick();
    modalImportarPonto.value?.getBsModal().show();
};

const importarArquivo = () => {
    if (!selectedFile.value) {
        alert("Por favor, selecione um arquivo.");
        return;
    }

    isProcessing.value = true;

    const formData = new FormData();
    formData.append("arquivo", selectedFile.value);

    const url = route(
        "contratos.contratada.sgc.pmqa.configuracao.ponto.importar",
        {
            contrato: props.contrato.id ?? props.contrato,
            produto: props.produto.slug ?? props.produto,
            pmqa: props.pmqa.id,
        }
    );

    axios
        .post(url, formData)

        .then(() => {
            emit("importacaoConcluida");
            modalImportarPonto.value.getBsModal().hide();
        })
        .catch((error) => {
            console.error(error.response?.data || error);
            alert("Erro ao importar arquivo.");
        })
        .finally(() => {
            isProcessing.value = false;
        });
};

const handleFileChange = (event) => {
    selectedFile.value = event.target.files[0];
};

defineExpose({ abrirModal });
</script>

<template>
    <Modal
        ref="modalImportarPonto"
        title="Importar dados dos pontos de coleta"
        modal-dialog-class="modal-xl"
    >
        <template #body>
            <div class="mb-3">
                <InputLabel value="Arquivo" />
                <div class="row g-2">
                    <div class="col">
                        <input
                            type="file"
                            class="form-control"
                            @change="handleFileChange"
                        />
                    </div>
                    <div class="col-auto">
                        <NavButton
                            title="Importar"
                            type-button="success"
                            :disabled="isProcessing"
                            @click="importarArquivo"
                        />
                    </div>
                </div>
            </div>
        </template>
    </Modal>
</template>
