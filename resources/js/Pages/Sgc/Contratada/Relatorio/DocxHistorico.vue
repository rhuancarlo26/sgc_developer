<script setup>
import Modal from '@/Components/Modal.vue';
import { renderAsync } from 'docx-preview';
import { ref } from 'vue';
import Comment from '@/Components/Comment.vue';
import { IconMessageDots } from '@tabler/icons-vue';
import { usePage } from '@inertiajs/vue3';
import { route } from 'ziggy-js';

const modalDetalhes = ref(null);
const wordDocument = ref(null);
const docModal = ref(null);
const notes = ref([]);
const modalKey = ref(0);
const counter = ref(0);
const isAddNote = ref(false);
const isCounting = ref(false);

const props = defineProps({
    itemId: Number,
    comentarios: Object,
    contrato: Object,
    numRelatorio: Number,
});

const page = usePage();

const abrirModal = async (idItem, contratoId, ) => {
    modalKey.value += 1;
    modalDetalhes.value.getBsModal().show();
    const caminhoDocumento = await fetchDocumentos(idItem, contratoId, );
    console.log('Parâmetros recebidos:', { idItem, contratoId,  });
    
    if (caminhoDocumento) {
        const filePath = route('sgc.contratada.get_docx', {
            itemId: idItem, 
            contratoId,
            versao: 0,
            numRelatorio: props.numRelatorio,
        });
        console.log('URL gerada:', filePath);
        
        try {
            const response = await fetch(filePath);
            if (!response.ok) {
                throw new Error('Erro ao carregar o documento do Word: ' + response.statusText);
            }
            const wordBlob = await response.blob({ type: 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' });
            wordDocument.value = wordBlob;
            renderAsync(wordDocument.value, docModal.value);
        } catch (error) {
            console.error('Erro ao carregar o documento do Word:', error);
        }
    } else {
        console.log('Documento não encontrado para o item:', idItem);
    }
};

const fetchDocumentos = async (itemId, contratoId,) => {
    try {
        const response = await fetch(route('sgc.relatorio_coordenacao_upload.index'));
        const data = await response.json();
        console.log('Dados localizados:', data);

        const documentoEncontrado = data.find(doc => 
            doc.item_id === itemId &&
            doc.contrato_id === contratoId &&
            doc.num_relatorio === props.numRelatorio &&
            doc.versao === 0
        );

        console.log('Documento encontrado:', documentoEncontrado);

        if (documentoEncontrado) {
            return documentoEncontrado.caminho;
        } else {
            console.error('Nenhum documento encontrado com a versão especificada:', { itemId, contratoId, numRelatorio: props.numRelatorio, versao });
            return null;
        }
    } catch (error) {
        console.error('Erro ao buscar documentos:', error);
        return null;
    }
};

const enableCounter = (event) => {
    if (event) isCounting.value = true;
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
        notes.value.push({
            title: 'Nova nota',
            comentario: [],
            x: event.clientX - rect.left + docModal.value.scrollLeft,
            y: event.clientY - rect.top + docModal.value.scrollTop,
        });
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

                        </div>
                    </div>
                </div>
                <div class="card-body" ref="docModal" :key="modalKey" @mousemove="addNote" :class="{ 'comment-enabled': isCounting }" />
                <Comment
                    v-for="(note, index) in notes"
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
.active-comment {
    color: #1a06ce;
    border: 2px solid #17a2b8;
    border-radius: 50%;
    padding: 2px;
}
.comment-enabled {
    cursor: crosshair;
    background-color: rgba(23, 162, 184, 0.1);
}
</style>