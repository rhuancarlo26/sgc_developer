<script setup>
import { ref, watch } from "vue";
import Modal from "@/Components/Modal.vue";
import { router, useForm, usePage } from "@inertiajs/vue3";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import { IconSearch, IconTrash } from "@tabler/icons-vue";
import NavButton from "@/Components/NavButton.vue";
import Table from "@/Components/Table.vue";
import axios from "axios";

const props = defineProps({
    pilhas: { type: Array },
    servico: { type: Object },
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

const imagens = ref([]);
const novaImagem = ref(null);
const imagemFile = ref(null);
const destinacaoId = ref(null);

const salvarImagem = () => {
    if (!novaImagem.value) return;

    const payload = new FormData();
    payload.append('arquivo', novaImagem.value);
    payload.append('destinacao_id', form.id);

    axios.post(
        route('contratos.contratada.servicos.supressao-vegetacao.execucao.destinacao.arquivo.upload'),
        payload,
        {
            headers: {
                'Content-Type': 'multipart/form-data',
            },
        }
    )
        .then(response => {
            imagens.value.push(response.data);
            novaImagem.value = null;
            imagemFile.value.value = null;
        })
        .catch(error => {
            if (error.response) {
                console.error('Erro resposta:', error.response.data);
            } else {
                console.error('Erro geral:', error.message);
            }
        });
};

const deletarImagem = (id) => {
    if (!confirm("Deseja excluir esta imagem?")) return;

    axios.delete(route(
        'contratos.contratada.servicos.supressao-vegetacao.execucao.destinacao.arquivo.delete',
        { destinacao: form.id, arquivo: id }
    ))
        .then(() => {
            imagens.value = imagens.value.filter(img => img.id !== id);
        });
};

const onSelecionarImagem = (event) => {
    novaImagem.value = event.target.files[0];
};

const visualizarImagem = (item) => {
    window.open(item.caminho, '_blank');
};

const carregarImagens = () => {
    if (!form.id) return;

    axios.get(route('contratos.contratada.servicos.supressao-vegetacao.execucao.destinacao.arquivo.listar', form.id))
        .then(response => imagens.value = response.data)
        .catch(error => console.error('Erro ao carregar imagens:', error));
};

const modalRef = ref();

const abrirModal = (item) => {
    form.reset()
    if (item != null) {
        Object.assign(form, item);
        carregarImagens();
    }
    if (item && item.dt_envio) {
        form.dt_envio = item.dt_envio.substring(0, 10);
    }
    modalRef.value.getBsModal().show();
}

const page = usePage()

const save = () => {
    const payload = {
        ...form.data(),
        servico_id: props.servico.id,
        pilhas: form.pilhas.map(p => p.id),
    };

    if (form.id !== null) {
        router.post(route('contratos.contratada.servicos.supressao-vegetacao.execucao.destinacao.update'), {
            _method: 'patch',
            ...payload,
        }, {
            preserveState: true,
            onSuccess: () => carregarImagens()
        });
        return;
    }

    axios.post(route('contratos.contratada.servicos.supressao-vegetacao.execucao.destinacao.store'), payload)
        .then(({ data }) => {
            form.id = data.destinacao_id;
            form.chave = data.destinacao_chave;
            carregarImagens();
            alert('Formulário salvo com sucesso!');
        })
        .catch(error => {
            if (error.response?.data?.errors) {
                form.setError(error.response.data.errors);
            } else {
                console.error('Erro geral ao salvar:', error.message);
            }
        });
};

const selectedPilha = ref({});

const incluirPilha = () => {
    if (!selectedPilha.value || Object.keys(selectedPilha.value).length === 0) {
        return;
    }
    if (form.pilhas.find(pilha => pilha.id === selectedPilha.value.id)) {
        return;
    }
    form.pilhas.push(selectedPilha.value);
    selectedPilha.value = {};
}

const removerEspecie = (item, index) => {
    form.pilhas.splice(index, 1);
}


defineExpose({ abrirModal });
</script>

<template>
    <form @submit.prevent="save">
        <Modal ref="modalRef" title="Cadastro de Destinação de Pilhas" modal-dialog-class="modal-xl">
            <template #body>
                <div class="row row-gap-2 mb-2">
                    <div class="col-lg-4">
                        <InputLabel value="Código" for="codigo" />
                        <input v-model="form.chave" id="codigo" class="form-control" disabled />
                        <InputError :message="form.errors.chave" />
                    </div>
                    <div class="col-lg-4">
                        <InputLabel value="Data do Envio" for="dt_envio" />
                        <input v-model="form.dt_envio" type="date" id="dt_envio" class="form-control" />
                    </div>
                </div>
                <div class="row row-gap-2 mb-2 align-items-end">
                    <div class="col-lg-4">
                        <InputLabel value="Incluir Pilha" for="controle_pilha_id" />
                        <v-select :options="pilhas" v-model="selectedPilha" label="chave">
                            <template #no-options="{ }">
                                Nenhum registro encontrado.
                            </template>
                        </v-select>
                    </div>
                    <div class="col-lg-4">
                        <button @click="incluirPilha" type="button" class="btn py-2 btn-success">Incluir</button>
                    </div>
                    <InputError :message="form.errors.pilhas" />
                    <div class="col-12">
                        <Table :columns="['Pilhas', 'Tipo de Pilha', 'Espécie', 'Volume (m³)', 'Nº ASV', 'Ações']"
                            :records="{ data: form.pilhas, links: [] }" table-class="table-hover">
                            <template #body="{ item, key }">
                                <tr>
                                    <td>{{ item.chave }}</td>
                                    <td>{{ item.tipo_pilha_label }}</td>
                                    <td>{{ item.corte_especie?.nome }}</td>
                                    <td>{{ item.volume }}</td>
                                    <td>{{ item.licenca?.numero_licenca }}</td>
                                    <td>
                                        <div class="d-flex justify-content-center">
                                            <NavButton @click="removerEspecie(item, key)" type-button="danger"
                                                class="btn-icon btn-sm" :icon="IconTrash" />
                                        </div>
                                    </td>
                                </tr>
                            </template>
                        </Table>
                    </div>
                </div>
                <div class="row row-gap-2 mb-2">
                    <div class="col-12">
                        <InputLabel value="Uso da Madeira" for="uso_da_madeira" />
                        <textarea v-model="form.uso_da_madeira" id="uso_da_madeira" class="form-control"></textarea>
                        <InputError :message="form.errors.uso_da_madeira" />
                    </div>

                    <div class="col-12">
                        <InputLabel value="Destinatário" for="destinatario" />
                        <textarea v-model="form.destinatario" id="destinatario" class="form-control"></textarea>
                        <InputError :message="form.errors.destinatario" />
                    </div>

                    <div class="col-12">
                        <InputLabel value="Observações" for="observacao" />
                        <textarea v-model="form.observacao" id="observacao" class="form-control"></textarea>
                        <InputError :message="form.errors.observacao" />
                    </div>
                    <div class="col-12 d-flex justify-content-end mt-3 mb-3">
                        <button type="button" class="btn btn-success" @click="save()">Salvar Formulário</button>
                    </div>
                    <hr>

                    <div class="col-12" v-if="!form.id">
                        <div class="alert alert-warning">
                            ⚠️ Para cadastrar imagens, é necessário primeiro salvar o formulário antes.
                        </div>
                    </div>

                    <div class="col-12" v-if="form.id" >
                        <div class="card border-secondary mt-3">
                            <div class="card-header bg-secondary text-white">
                                Upload de Imagens
                            </div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col">
                                        <input type="file" ref="imagemFile" class="form-control"
                                            @change="onSelecionarImagem">
                                    </div>
                                    <div class="col-auto">
                                        <button class="btn btn-success" type="button" @click="salvarImagem"
                                            :disabled="!novaImagem">Salvar
                                            Imagem</button>
                                    </div>
                                </div>

                                <Table :columns="['Nome', 'Ações']" :records="{ data: imagens, links: [] }">
                                    <template #body="{ item }">
                                        <tr class="align-middle text-center">
                                            <td>{{ item.nome_arquivo }}</td>
                                            <td class="w-25">
                                                <button class="btn btn-sm btn-primary me-2" type="button"
                                                    @click="visualizarImagem(item)">
                                                    <component :is="IconSearch" />
                                                </button>
                                                <button class="btn btn-sm btn-danger" type="button"
                                                    @click="deletarImagem(item.id)">
                                                    <component :is="IconTrash" />
                                                </button>
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
                <button type="button" class="btn btn-success"
                    @click="save(), modalRef.getBsModal().hide()">Salvar</button>
            </template>
        </Modal>
    </form>
</template>
