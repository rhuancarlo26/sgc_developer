<script setup>
import {computed, ref, watch} from "vue";
import Modal from "@/Components/Modal.vue";
import {router, useForm, usePage} from "@inertiajs/vue3";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import {IconTrash} from "@tabler/icons-vue";
import NavButton from "@/Components/NavButton.vue";
import Table from "@/Components/Table.vue";

const props = defineProps({
    pilhas: {type: Array},

    patios: {type: Array},
    servico: {type: Object},
    tipos: {type: Array},
    licencas: {type: Array},
    produtos: {type: Array},
    areasSuprimidas: {type: Array},
})

const form = useForm({
    id: null,
    chave: null,
    dt_envio: null,
    uso_da_madeira: null,
    destinatario: null,
    observacao: null,
    pilhas: [],
    arquivos: [],
})

const modalRef = ref();
const abrirModal = (item) => {
    form.reset()
    if (item != null) {
        Object.assign(form, item);
    }
    modalRef.value.getBsModal().show();
}

const page = usePage()

watch(() => page.props.errors, (errors) => {
    form.setError(errors)
})

const save = () => {
    form.transform((data) => ({
        ...data,
        servico_id: props.servico.id,
        pilhas: data.pilhas.map(pilha => pilha.id),
    }));

    const onSuccess = () => {
        modalRef.value.getBsModal().hide();
        form.reset();
    }

    if (form.id !== null) {
        router.post(route('contratos.contratada.servicos.supressao-vegetacao.execucao.destinacao.update'), {
            _method: 'patch',
            ...form.data(),
            pilhas: form.pilhas.map(pilha => pilha?.id),
            arquivos: form.arquivos.filter(foto => foto instanceof File),
        }, {
            preserveState: true,
            onSuccess
        })
        return
    }

    form.post(route('contratos.contratada.servicos.supressao-vegetacao.execucao.destinacao.store'), {
        preserveState: true,
        onSuccess
    })
}

const arquivos = computed(() => {
    return (form.arquivos).map((foto, index) =>  ({
        id: foto?.id ?? null,
        index,
        path: foto?.caminho ?? URL.createObjectURL(foto),
        name: foto?.nome_arquivo ?? foto.name,
    }));
})

const fileRef = ref()
const onChangeArquivos = (event) => {
    form.arquivos.push(...Array.from(event.target.files));
    fileRef.value.value = '';
}

const selectedPilha = ref({});

const incluirPilha = () => {
    if(!selectedPilha.value || Object.keys(selectedPilha.value).length === 0) {
        return;
    }
    if(form.pilhas.find(pilha => pilha.id === selectedPilha.value.id)) {
        return;
    }
    form.pilhas.push(selectedPilha.value);
    selectedPilha.value = {};
}

const removerEspecie = (item, index) => {
    form.pilhas.splice(index, 1);
}


defineExpose({abrirModal});
</script>

<template>
    <form @submit.prevent="save">
        <Modal ref="modalRef" title="Cadastro de Destinação de Pilhas" modal-dialog-class="modal-xl">
            <template #body>
                <div class="row row-gap-2 mb-2">
                    <div class="col-lg-4">
                        <InputLabel value="Código" for="codigo"/>
                        <input v-model="form.chave" id="codigo" class="form-control" disabled/>
                        <InputError :message="form.errors.chave"/>
                    </div>
                    <div class="col-lg-4">
                        <InputLabel value="Data do Envio" for="dt_envio"/>
                        <input v-model="form.dt_envio" type="date" id="dt_envio" class="form-control" />
                    </div>
                </div>
                <div class="row row-gap-2 mb-2 align-items-end">
                    <div class="col-lg-4">
                        <InputLabel value="Incluir Pilha" for="controle_pilha_id"/>
                        <v-select :options="pilhas" v-model="selectedPilha" label="chave">
                            <template #no-options="{}">
                                Nenhum registro encontrado.
                            </template>
                        </v-select>
                    </div>
                    <div class="col-lg-4">
                        <button @click="incluirPilha" type="button" class="btn py-2 btn-success">Incluir</button>
                    </div>
                    <InputError :message="form.errors.pilhas"/>

                    <div class="col-12">
                        <Table
                            :columns="['Pilhas', 'Tipo de Pilha', 'Espécie', 'Volume (m³)', 'Nº ASV', 'Ações']"
                            :records="{ data: form.pilhas, links: [] }"
                            table-class="table-hover">
                            <template #body="{ item, key }">
                                <tr>
                                    <td>{{ item.chave }}</td>
                                    <td>{{ item.tipo_pilha_label }}</td>
                                    <td>{{ item.corte_especie?.nome }}</td>
                                    <td>{{ item.volume }}</td>
                                    <td>{{ item.licenca?.numero_licenca }}</td>
                                    <td>
                                        <div class="d-flex justify-content-center">
                                            <NavButton @click="removerEspecie(item, key)" type-button="danger" class="btn-icon btn-sm" :icon="IconTrash" />
                                        </div>
                                    </td>
                                </tr>
                            </template>
                        </Table>
                    </div>
                </div>
                <div class="row row-gap-2 mb-2">
                    <div class="col-12">
                        <InputLabel value="Uso da Madeira" for="uso_da_madeira"/>
                        <textarea v-model="form.uso_da_madeira" id="uso_da_madeira" class="form-control"></textarea>
                        <InputError :message="form.errors.uso_da_madeira"/>
                    </div>

                    <div class="col-12">
                        <InputLabel value="Destinatário" for="destinatario"/>
                        <textarea v-model="form.destinatario" id="destinatario" class="form-control"></textarea>
                        <InputError :message="form.errors.destinatario"/>
                    </div>

                    <div class="col-12">
                        <InputLabel value="Observações" for="observacao"/>
                        <textarea v-model="form.observacao" id="observacao" class="form-control"></textarea>
                        <InputError :message="form.errors.observacao"/>
                    </div>
                    <div class="col-12">
                        <InputLabel value="Anexar Arquivos:" for="arquivos"/>
                        <input ref="fileRef" @input="onChangeArquivos" id="arquivos" type="file" class="form-control" multiple>
                        <InputError :message="form.errors.arquivos"/>
                        <ul class="list-group mt-3">
                            <li v-for="img in arquivos" class="list-group-item">
                                {{img.name}}
                            </li>
                        </ul>
                    </div>
                </div>
            </template>
            <template #footer>
                <button @click="modalRef.getBsModal().hide()" type="button" class="btn btn-secondary">Fechar</button>
                <button type="submit" class="btn btn-success">Salvar</button>
            </template>
        </Modal>
    </form>
</template>
