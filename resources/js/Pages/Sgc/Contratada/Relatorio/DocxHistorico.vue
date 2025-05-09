<script setup>
import Modal from '@/Components/Modal.vue';
import { renderAsync } from 'docx-preview';
import { onMounted, ref } from "vue";
import Comment from '@/Components/Comment.vue';
import { IconMessageDots } from "@tabler/icons-vue";

const modalDetalhes = ref(null);
const wordDocument = ref(null);
const documento = ref(null);
let caminho = null;
let filePath = null;

const props = defineProps({
    itemId: Number,
    comentarios: Object,
    contrato: Object,
    numRelatorio: Number
});

const docModal = ref(null);
const notes = ref([]);
const modalKey = ref(0);
const counter = ref(0);
const isAddNote = ref(false);
const isCounting = ref(false);


const abrirModal = async (idItem, contratoId, versao) => {
    modalDetalhes.value.getBsModal().show();
    const caminhoDocumento = await fetchDocumentos(idItem, contratoId, versao);
    
    if (caminhoDocumento) {
        filePath = `http://127.0.0.1:5173/storage/app/${caminhoDocumento}`;

        try {
            const response = await fetch(filePath);
            if (!response.ok) {
                throw new Error('Erro ao carregar o documento do Word');
            }
            const wordBlob = await response.blob();

            wordDocument.value = wordBlob;

            renderAsync(wordDocument.value, docModal.value);

        } catch (error) {
            console.error('Erro ao carregar o documento do Word:', error);
        }
    } else {
        console.log('Documento não encontrado para o item:', idItem);
    }
};


const fetchDocumentos = async (itemId, contratoId, versao) => {
    try {
        const response = await fetch(route('sgc.relatorio_coordenacao_upload.index'));
        const data = await response.json();
        console.log('Dados localizados:', data);

        const documentoEncontrado = data.find(doc => 
            doc.item_id === itemId &&
            doc.contrato_id === contratoId &&
            doc.num_relatorio === props.numRelatorio && 
            doc.versao === versao
        );

        console.log('Documento encontrado:', documentoEncontrado);

        if (documentoEncontrado) {
            return documentoEncontrado.caminho;
        } else {
            console.error('Nenhum documento encontrado com a versão especificada.');
            return null;
        }
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
        isAddNote.value = true;
        isCounting.value = false;
    }
};

const addNote = (event) => {
    if (isAddNote.value) {
        const rect = docModal.value.getBoundingClientRect();
        const newNote = {
            title: 'Nova nota',
            comentario: [],
            x: event.clientX - rect.left + docModal.value.scrollLeft,
            y: event.clientY - rect.top + docModal.value.scrollTop,
        };
        notes.value.push(newNote);
        isAddNote.value = false;
    }
};

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
                            <div>
                                <IconMessageDots
                                    class="position-fixed z-3"
                                    @click="enableCounter"
                                    style="cursor: pointer;" />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body" ref="docModal" :key="modalKey" @mousemove="addNote" />
                <Comment v-for="(note, index) in notes"
                    :note="note"
                    :index="index"
                    :item-id="itemId"
                    :comentarios="comentarios"
                    :contrato="contrato"
                    />
            </div>
        </template>
    </Modal>
</template>

<style>
.docx-wrapper {
  background-color: rgb(255, 255, 255) !important;
}
</style>
