<script setup>
import { defineProps, ref } from 'vue';
import { IconNote, IconX, IconTrash } from '@tabler/icons-vue'
import { router, useForm, usePage } from '@inertiajs/vue3';
import { dateTimeFormat } from '@/Utils/DateTimeUtils';

const props = defineProps({
    note: Object,
    index: Number,
    itemId: Number,
    contrato: Object
});


let isMinimized;

if (props.note.load) {
    isMinimized = ref(true);
} else {
    isMinimized = ref(false);
}
const existeComentario = props.note.comentario.length;
const user = usePage().props.auth.user;
const [perfil] = user.roles.map(tipo => tipo.name);
const perfilUser = `${user.name} ${perfil}`

const form = useForm({
    comentario: null,
    comentario_id: props.note.id,
    tipo_perfil: perfilUser,
    item_id: props.itemId,
    relatorio_num: 1
});

const formComentario = useForm({
    item_id: props.itemId,
    contrato_id: props.contrato.id,
    posicao_x: props.note.x,
    posicao_y: props.note.y
});


const enviarComentario = () => {
    if (!existeComentario) {

        formComentario.post(route('sgc.contratada.store_comentario'), {
            onSuccess: (response) => {
                const ultimo = response.props.comentarios.length - 1;;
                const comentarioId = response.props.comentarios[ultimo].id;

                form.comentario_id = comentarioId;
                form.post(route('sgc.contratada.store_comentarios'), {
                onSuccess: () => {
                    props.note.comentario.push({
                        id: form.comentario_id,
                        comentario: form.comentario,
                        tipo_perfil: perfilUser,
                        created_at: new Date().toISOString()
                    });
                    props.note.load = true;
                    form.comentario = null;
                },
                onError: (error) => {
                    console.error('Erro ao enviar comentário', error);
                }
        });
            }});
    } else {

        form.post(route('sgc.contratada.store_comentarios'), {
            onSuccess: () => {
                props.note.comentario.push({
                    id: form.comentario_id,
                    comentario: form.comentario,
                    tipo_perfil: perfilUser,
                    created_at: new Date().toISOString()
                });
                form.comentario = null;
            },
            onError: (error) => {
                console.error('Erro ao enviar comentário', error);
            }
        });
    }
};

const excluirComentarios = (comentarios_id) => {
    router.delete(route('sgc.contratada.destroy_comentarios', comentarios_id), {
        onSuccess: () => {
            props.note.comentario = props.note.comentario.filter(comentario => comentario.id !== comentarios_id);
        },
        onError: (error) => {
            console.error('Erro ao excluir comentário', error);
        }
    });
};

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
            <div class="card-body chat-body">
                <div v-if="props.note.load" v-for="mensagem in note.comentario" :key="mensagem.id" class="chat-message mb-2"
                     :class="mensagem.tipo_perfil !== perfilUser ? 'text-start' : 'text-end'">
                    <div :class="['message', 'container-xl', 'border', 'rounded',
                        mensagem.tipo_perfil  !== perfilUser ? 'bg-primary text-white' : 'bg-success text-white ms-auto', 'm-0']">
                        <div class="d-flex justify-content-between p-2">
                            <p class="message-text mb-0 p-2">{{ mensagem.comentario }}</p>
                            <IconTrash class="mt-1" @click="excluirComentarios(mensagem.id)"/>
                        </div>
                    </div>
                    <small :class="['text-secondary-emphasis', 'd-block', 'mt-1',
                        mensagem.tipo_perfil !== perfilUser ? 'text-start' : 'text-end']">
                        {{ mensagem.tipo_perfil }} {{ dateTimeFormat(mensagem.created_at) }}
                    </small>
                </div>
            </div>
            <div class="card-footer d-flex">
                <textarea v-model="form.comentario" class="form-control me-2" placeholder="Enviar comentário..."></textarea>
                <button class="btn btn-primary" @click="enviarComentario">Enviar</button>
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
    background-color: rgba(127, 160, 221, 0.192);
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

.message {
    max-width: 80%;
}

.message-text {
    margin: 0;
}

.text-end {
    text-align: end;
}

.ms-auto {
    margin-left: auto;
}
</style>
