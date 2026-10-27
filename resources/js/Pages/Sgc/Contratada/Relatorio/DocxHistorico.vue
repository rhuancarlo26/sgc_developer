<script setup>
import Modal from '@/Components/Modal.vue';
import { renderAsync } from 'docx-preview';
import { ref, nextTick } from 'vue';
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

const erroDocumento = ref('');
const versaoSelecionada = ref(null);
let aberturaAtual = 0;

const abrirModal = async (idItem, contratoId, versao) => {
    const abertura = ++aberturaAtual;
    versaoSelecionada.value = Number(versao);
    modalKey.value += 1;
    erroDocumento.value = '';
    wordDocument.value = null;
    modalDetalhes.value.getBsModal().show();
    await nextTick();
    notes.value = [];

    try {
        const response = await fetch(route('sgc.contratada.get_docx', {
            itemId: idItem, contratoId, versao, numRelatorio: props.numRelatorio,
        }));
        if (!response.ok) throw new Error('Documento indisponivel para esta revisao.');
        const blob = await response.blob();
        if (abertura !== aberturaAtual) return;
        wordDocument.value = blob;
        await renderAsync(blob, docModal.value);
    } catch (error) {
        if (abertura !== aberturaAtual) return;
        erroDocumento.value = 'N\u00e3o foi poss\u00edvel abrir o documento desta revis\u00e3o.';
        console.error(error);
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
                            <h3 class="my-0">RELATÓRIO DE COORDENAÇÃO <span class="badge text-bg-secondary ms-2">{{ versaoSelecionada === 0 ? 'VER 0' : 'REV ' + versaoSelecionada }}</span></h3>

                        </div>
                    </div>
                </div>
                <p v-if="erroDocumento" class="alert alert-warning m-3" role="alert">{{ erroDocumento }}</p>
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