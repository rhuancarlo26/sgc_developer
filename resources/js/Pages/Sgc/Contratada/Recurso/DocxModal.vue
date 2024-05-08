<script setup>
import Modal from '@/Components/Modal.vue';
import { renderAsync } from 'docx-preview';
import { onMounted, ref } from "vue";

const modalDetalhes = ref(null);

const abrirModal= () => {
    modalDetalhes.value.getBsModal().show();
    console.log('modal aberto')
}

const wordDocument = ref(null);
const teste = ref(null)

onMounted(async () => {
  try {
    const filePath = new URL('./teste.docx', import.meta.url);
    const response = await fetch(filePath);
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
    // Lidar com o erro, se necessário
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
