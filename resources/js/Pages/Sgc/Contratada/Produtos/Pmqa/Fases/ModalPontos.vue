<script setup>
import InputLabel from "@/Components/InputLabel.vue";
import Modal from "@/Components/Modal.vue";
import NavButton from "@/Components/NavButton.vue";
import { ref, nextTick } from "vue";
import axios from "axios";

const props = defineProps({
    draftData: { type: Object },
    contrato: { type: Object },
    produto: { type: Object },
});


const emit = defineEmits(["importacaoConclucluida"]);

const modalImportarPonto = ref();
const selectedFile = ref(null);
const isProcessing = ref(false);

const abrirModal = async () => {
    await nextTick();
    if (modalImportarPonto.value) {
        modalImportarPonto.value.getBsModal().show();
    } else {
        console.error("A referência para o modal não foi encontrada!");
    }
};

const importarArquivo = () => {
    if (!selectedFile.value) {
        alert("Por favor, selecione um arquivo.");
        return;
    }

    isProcessing.value = true;

    const formData = new FormData();
    formData.append("arquivo", selectedFile.value);
    formData.append("campanha_id", props.draftData.id);

    const url = route("sgc.contratada.produtos.pmqa.importar_pontos", {
        contrato: props.contrato,
        produto: props.produto,
    });

    axios.post(url, formData, {
        headers: { "Content-Type": "multipart/form-data" },
    })
    .then((response) => {
        console.log("Sucesso:", response.data);
        alert("Importação realizada com sucesso!");

        // ALTERADO: Em vez de buscar os dados aqui, emitimos o evento para o pai.
        emit("importacaoConcluida");

        modalImportarPonto.value.getBsModal().hide();
    })
    .catch((error) => {
        console.error("--- RESPOSTA COMPLETA DO ERRO ---");
        console.error(error.response.data);
        alert(
            "Falha na importação. Verifique o console para a resposta detalhada do erro."
        );
    })
    .finally(() => {
        isProcessing.value = false;
    });
};

// Função para atualizar a variável quando um arquivo é escolhido
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
                            @change="handleFileChange"
                            type="file"
                            class="form-control"
                        />
                    </div>
                    <div class="col-auto">
                        <NavButton
                            @click="importarArquivo()"
                            :disabled="isProcessing"
                            type-button="success"
                            title="Importar"
                        />
                    </div>
                </div>
            </div>
        </template>
    </Modal>
</template>
