<template>
    <Modal ref="modalParecer" title="Parecer" modal-dialog-class="modal-xl">
        <template #body>
            <div class="">
                <div class="card">
                    <div class="card-body">
                        <div class="mb-2">
                            <IconCode class="icon me-2 text-secondary"/>
                            Programa: <strong>{{ servico?.tema?.nome_tema }}</strong>
                        </div>
                        <div class="mb-2">
                            <IconBriefcase class="icon me-2 text-secondary"/>
                            Tipo: <strong>{{ servico?.tipo?.nome }}</strong>
                        </div>
                        <div class="mb-2">
                            <IconHome class="icon me-2 text-secondary"/>
                            Status: <strong>
                                <span v-if="servico?.status_aprovacao === 2" class="badge bg-yellow-lt">
                                    Em análise
                                </span>
                            <span v-else-if="servico?.status_aprovacao === 3" class="badge bg-blue-lt">
                                    Aprovado
                                </span>
                            <span v-else-if="servico?.status_aprovacao === 4" class="badge bg-red-lt">
                                    Pendente
                                </span>
                        </strong>
                        </div>
                        <div class="mb-2" v-if="servico?.status_aprovacao === 2">
                            <textarea name="parecer" id="parecer" class="form-control" v-model="form.parecer"
                                      rows="5"></textarea>
                            <InputError :message="form.errors.parecer"/>
                        </div>
                        <div class="mb-2" v-if="servico?.status_aprovacao !== 2">
                            <IconMessage class="icon me-2 text-secondary"/>
                            Parecer: <strong>{{ form.parecer }}</strong>
                        </div>
                        <div class="mb-2" v-if="servico?.status_aprovacao !== 2">
                            <IconCalendar class="icon me-2 text-secondary"/>
                            Data do parece: <strong>{{servico?.parecer.updated_at}}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </template>
        <template #footer>
            <NavButton @click="emiteParecer(3)" v-if="servico?.status_aprovacao === 2" type-button="success" :icon="IconCheck" title="Aprovar"/>
            <NavButton @click="emiteParecer(4)" v-if="servico?.status_aprovacao === 2" type-button="danger" :icon="IconCheck" title="Reprovar"/>
        </template>
    </Modal>
</template>

<script setup>
import {
    IconCode,
    IconMessage,
    IconBriefcase,
    IconHome,
    IconCalendar,
    IconCheck
} from "@tabler/icons-vue";
import Modal from "@/Components/Modal.vue";
import {ref} from "vue";
import InputError from "@/Components/InputError.vue";
import {useForm} from "@inertiajs/vue3";
import NavButton from "@/Components/NavButton.vue";

const servico = ref(null);
const modalParecer = ref(null);

const abrirModal = (item) => {
    console.log(item)
    servico.value = item;
    form.id_parecer = item.parecer?.id
    form.parecer = item.parecer?.parecer
    modalParecer.value.getBsModal().show();
}

const form = useForm({
    id_parecer: null,
    parecer: null,
    fk_servico: null,
    status_aprovacao: null
});

const emiteParecer = (status) => {
    form.status_aprovacao = status;
    form.fk_servico = servico.value.id;
    form.post(route('fiscal.emite-parecer-servico'), {
        onSuccess: () => modalParecer.value.getBsModal().hide()
    });
}

defineExpose({abrirModal});
</script>
