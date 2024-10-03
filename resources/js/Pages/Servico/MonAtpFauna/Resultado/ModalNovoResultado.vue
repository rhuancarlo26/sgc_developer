<script setup>

import { ref } from "vue";
import { Link, useForm } from "@inertiajs/vue3";
import Modal from "@/Components/Modal.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import LinkConfirmation from "@/Components/LinkConfirmation.vue";
import { IconTrash } from "@tabler/icons-vue";
import Table from "@/Components/Table.vue";
import { computed } from "vue";

const props = defineProps({
    showAction: { type: Boolean, default: true },
    campanhas: { type: Array },
    servico: { type: Object },
})

const form = useForm({
    id: null,
    nome_resultado: null,
    fk_campanha: null,
});

const modalRef = ref();
const onSuccess = () => {
    modalRef.value.getBsModal().hide();
    form.reset();
}
const save = () => {
    form.transform((data) => ({
        ...data,
        fk_servico: props.servico.id
    }));

    if (form.id !== null) {
        form.patch(route('contratos.contratada.servicos.mon_atp_fauna.resultado.update'), {
            preserveState: true,
            onSuccess
        })
        return
    }

    form.post(route('contratos.contratada.servicos.mon_atp_fauna.resultado.store'), {
        preserveState: true,
        onSuccess
    })
}

const saveResultadoCampanha = () => {
    form.transform((data) => ({
        ...data,
        fk_resultado: form.id,
    }));
    form.post(route('contratos.contratada.servicos.mon_atp_fauna.resultado.store-campanha'), {
        preserveState: true,
        preserveScroll: true,
        onSuccess
    })
}

const resultadosCampanha = ref([])
const abrirModal = async (item = null) => {
    form.reset()
    resultadosCampanha.value = [];
    if (item !== null) {
        Object.assign(form, item)
        const { data } = await axios.get(route('contratos.contratada.servicos.mon_atp_fauna.resultado.get-campanhas', form.id))
        resultadosCampanha.value = data;
    }
    modalRef.value.getBsModal().show();
}


const formattedCreatedAt = computed(() => {
    if (!form.created_at) return '';
    const date = new Date(form.created_at);
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
});

defineExpose({ abrirModal });

</script>

<template>
    <form @submit.prevent="save">
        <Modal ref="modalRef" title="Resultado" modal-dialog-class="modal-xl">
            <template #body>
                <div class="row">
                    <div :class="[form.id ? 'col-lg-6' : 'col-lg-12']">
                        <div class="row row-gap-2 mb-2">
                            <div class="col-lg-6">
                                <InputLabel value="ID resultado" for="resultado_id" />
                                <input v-model="form.id" type="text" id="resultado_id" class="form-control" disabled />
                                <InputError :message="form.errors.id" />
                            </div>
                            <div class="col-lg-6">
                                <InputLabel value="Data de criação" for="created_at" />
                                <input :value="formattedCreatedAt" type="date" id="created_at" class="form-control"
                                    disabled />
                                <InputError :message="form.errors.created_at" />
                            </div>
                        </div>
                        <div class="row row-gap-2 mb-2">
                            <div class="col-lg-12">
                                <InputLabel value="Nome do resultado" for="nome_resultado" />
                                <input v-model="form.nome_resultado" type="text" id="nome_resultado"
                                    class="form-control" />
                                <InputError :message="form.errors.nome_resultado" />
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">

                        <div v-if="form.id" class="row row-gap-2 mb-2">
                            <div class="col-lg-12">
                                <InputLabel value="Selecionar campanha" for="fk_campanha" />
                                <v-select @option:selected="saveResultadoCampanha" v-model="form.fk_campanha"
                                    :options="campanhas" label="id" :reduce="t => t.id">
                                    <template #no-options="{}">
                                        Nenhum registro encontrado.
                                    </template>
                                </v-select>
                            </div>
                            <div class="col-lg-12">
                                <Table :columns="['ID', 'Campanha', 'Ação']"
                                    :records="{ data: resultadosCampanha, links: [] }" table-class="table-hover">
                                    <template #body="{ item }">
                                        <tr>
                                            <td>{{ item.id }}</td>
                                            <td>{{ item.id_campanha }}</td>
                                            <td v-if="showAction">
                                                <LinkConfirmation v-slot="confirmation"
                                                    :options="{ text: 'Excluir registro?' }">
                                                    <Link :onBefore="(request) => confirmation.show({
                                                        ...request,
                                                        onSuccess: () => {
                                                            modalRef.getBsModal().hide();
                                                        }
                                                    })" :href="route('contratos.contratada.servicos.mon_atp_fauna.resultado.delete-campanha', item.id)"
                                                        as="button" method="delete" type="button"
                                                        class="btn btn-icon btn-danger">
                                                    <IconTrash />
                                                    </Link>
                                                </LinkConfirmation>
                                            </td>
                                        </tr>
                                    </template>
                                </Table>
                            </div>
                        </div>
                    </div>
                </div>

            </template>
            <template #footer>
                <button @click="modalRef.getBsModal().hide()" type="button" class="btn btn-secondary">Fechar</button>
                <button v-if="showAction" type="submit" class="btn btn-success">Salvar</button>
            </template>
        </Modal>
    </form>
</template>
