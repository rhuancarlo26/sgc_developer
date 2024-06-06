<script setup>
import Modal from '@/Components/Modal.vue';
import Comment from '@/Components/Comment.vue';
import { IconMessageDots } from "@tabler/icons-vue";
import { renderAsync } from 'docx-preview';
import { ref } from "vue";

const props = defineProps({
    itemId: Number
})

const modalDetalhes = ref(null);
const wordDocument = ref(null);
const docModal = ref(null);
const notes = ref([]);
const modalKey = ref(0);
const counter = ref(0);
const isAddNote = ref(false);
const isCounting = ref(false);

let filePath = null;

const abrirModal = async (idItem) => {
    modalKey.value += 1;

    modalDetalhes.value.getBsModal().show();
    filePath = await fetchDocumentos(idItem);

    if (!filePath) {
        console.log('Documento não encontrado.');
        return;
    }

    try {
        const response = await fetch(`http://127.0.0.1:5173/storage/app/${filePath}`);
        if (!response.ok) {
            throw new Error('Erro ao carregar o documento do Word');
        }
        const wordBlob = await response.blob({ type: 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' });

        wordDocument.value = wordBlob;

        renderAsync(wordDocument.value, docModal.value);

    } catch (error) {
        console.error('Erro ao carregar o documento do Word:', error);
    }
}

const fetchDocumentos = async (itemId) => {
    try {
        const response = await fetch(route('sgc.contratada.visualizar_doc'));
        const data = await response.json();

        for (const item of data) {
            if (itemId === item.item_id) {
                return item.caminho;
            }
        }
        console.log('Documento não localizado.');
        return null;

    } catch (error) {
        console.error('Erro ao buscar documentos:', error);
        return null;
    }
};

const enableCounter = (event) => {
    if (event) {
        isCounting.value = true;
    }
};

const increment = () => {

    if (!isCounting.value) return;

    counter.value += 1;

    if (counter.value > 1) {
        counter.value = 0;
        isAddNote.value = true
        isCounting.value = false;
    }
}

const addNote = (event) => {

    if (isAddNote.value) {
        const rect = docModal.value.getBoundingClientRect();
        const newNote = {
          title: 'Nova nota',
          x: event.clientX - rect.left + docModal.value.scrollLeft,
          y: event.clientY - rect.top + docModal.value.scrollTop
        };
        notes.value.push(newNote);
        isAddNote.value = false;
    }
}

defineExpose({ abrirModal });

</script>

<template>
    <Modal ref="modalDetalhes" modal-dialog-class="modal-xl">
        <template #body>
            <div class="card" @click="increment">
                <div class="card-header">
                    <div class="container mt-5">
                        <div class="d-flex justify-content-between">
                            <h3 class="my-0">RELATÓRIO DE COORDENAÇÃO</h3>
                            <div >
                                <IconMessageDots
                                    class="position-fixed z-3"
                                    @click="enableCounter"
                                    style="cursor: pointer;"/>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body" ref="docModal" :key="modalKey"  @mousemove="addNote"/>
                <Comment v-for="(note, index) in notes"
                    :key="index"
                    :note="note"
                    :index = "index"
                    :item-id="props.itemId"/>
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
    z-index: 1;
}
</style>
