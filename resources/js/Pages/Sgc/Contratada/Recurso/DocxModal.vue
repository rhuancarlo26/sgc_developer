<script setup>
import Modal from '@/Components/Modal.vue';
import { renderAsync } from 'docx-preview';
import { onMounted, ref } from "vue";

const modalDetalhes = ref(null);
const wordDocument = ref(null);
const teste = ref(null)

const props = defineProps({
  caminho: Object
})

let caminho = null;
let filePath = null;

const abrirModal = async (idItem) => {
    modalDetalhes.value.getBsModal().show();
    caminho = await fetchDocumentos(idItem)
    filePath = new URL(caminho, import.meta.url);
    console.log(filePath)
}

const fetchDocumentos = async (itemId) => {
  try {
    const response = await fetch(route('sgc.contratada.visualizar_doc')); // Substitua pela sua rota Laravel
    const data = await response.json()
    console.log(itemId)

    if (itemId == data[0].item_id) {
        return data[0].caminho
        
    } else {
        console.log('não localizado.')
    }
  } catch (error) {
    console.error('Erro ao buscar documentos:', error);
  }
};


onMounted(async () => {
  try {
    const response = await fetch('http://127.0.0.1:5173/resources/js/Pages/Sgc/Contratada/Recurso/664257d510021_example.docx');
    if (!response.ok) {
      throw new Error('Erro ao carregar o documento do Word');
    }
    const wordBlob = await response.blob({ type: 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' });

    wordDocument.value = wordBlob;

    renderAsync(wordDocument.value, teste.value)

    if (teste.value) {

    } else {
      console.error('Elemento #wordViewer não encontrado.');
    }
  } catch (error) {
    console.error('Erro ao carregar o documento do Word:', error);
  }
});

defineExpose({ abrirModal });

</script>

<template>
    <Modal ref="modalDetalhes" modal-dialog-class="modal-xl">
        <template #body>
            <div class="card">
                <div class="card-header">
                    <h3 class="my-0">RELATÓRIO DE COORDENAÇÃO</h3>
                </div>
                <div class="card-body" ref="teste" />
            </div>
        </template>
    </Modal>
</template>

<style>
.docx-wrapper {
  background-color: rgb(255, 255, 255) !important;

}
.docx-wrapper .docx-preview {
  position: relative;
  z-index: 1; /* Garantindo que o conteúdo esteja na frente do background */
}

</style>