<script setup>
import { defineProps, ref } from 'vue';
import { IconNote, IconX } from '@tabler/icons-vue'
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    note: Object,
    index: Number,
    itemId: Number,
    comentarios: Object
});


const isMinimized = ref(false);

const form = useForm({
  id: props.index + 1,
  comentario: null,
  comentario_id: props.index,
  encaminhado_para_revisao: 1,
  ec_para_consorcio: 1,
  perfil_id: 1,
  item_id: props.itemId,
  relatorio_num: 1,
  posicao_x: props.note.x,
  posicao_y: props.note.y
});

const enviarComentario = () => {
    form.post(route('sgc.contratada.store_comentario'));
}

const toggleMinimize = () => {
    isMinimized.value = !isMinimized.value;
};

</script>

<template>
    <div class="note" :style="{ top: note.y + 'px', left: note.x + 'px' }">
        <div v-if="!isMinimized" class="card border-primary-subtle">
            <div class="card-body d-flex justify-content-between align-items-center">
                <h5 class="card-title">{{ note.title }}</h5>
                <div>
                    <button @click="toggleMinimize" class="border-0 rounded btn btn-sm btn-outline-secondary">
                        <IconX />
                    </button>
                </div>
            </div>
            <div class="card-body">
                <textarea v-if="note.comentario" class="form-control" readonly>{{ note.comentario }}</textarea>
                <textarea v-else v-model="form.comentario" class="form-control"></textarea>
                <a v-if="note.title === 'Nova nota'" href="#" class="btn btn-success mt-3" @click="enviarComentario">Salvar</a>
            </div>
        </div>
        <div v-else @click="toggleMinimize" class="minimized-note btn btn-sm btn-warning">
            <IconNote />
        </div>
    </div>
</template>


<style scoped>
.note {
    position: absolute;
    z-index: 2;
}
textarea {
    width: 100%;
    height: 50px;
}
.minimized-note {
    background-color: rgb(127, 160, 221);
    cursor: pointer;
    padding: 5px;
    border-radius: 5px;
    text-align: center;
    overflow: hidden;
    white-space: nowrap;
    text-overflow: ellipsis;
}
.minimized-note:hover {
    background-color: rgb(70, 122, 219);
}
</style>
