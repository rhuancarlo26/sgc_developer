<script setup>
import {computed, ref, watch} from "vue";
import Modal from "@/Components/Modal.vue";
import {router, useForm} from "@inertiajs/vue3";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import axios from "axios";

const props = defineProps({
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
    licenca_id: null,
    area_supressao_id: null,
    patio_estocagem_id: null,
    tipo_produto_id: null,
    corte_especie_id: null,
    tipo_pilha: null,
    created_at: null,
    volume: null,
    latitude: null,
    longitude: null,
    observacao: null,
    fotos: [],
})

const modalRef = ref();
const abrirModal = (item) => {
    form.reset()
    if (item != null) {
        console.log(item)
        Object.assign(form, item);
    }
    modalRef.value.getBsModal().show();
}

const save = () => {
    form.transform((data) => ({
        ...data,
        servico_id: props.servico.id,
    }));

    const onSuccess = () => {
        modalRef.value.getBsModal().hide();
        form.reset();
    }

    if (form.id !== null) {
        router.post(route('contratos.contratada.servicos.supressao-vegetacao.execucao.pilhas.update'), {
            _method: 'patch',
            ...form.data(),
            fotos: form.fotos.filter(foto => foto instanceof File),
        }, {
            preserveState: true,
            onSuccess
        })
        return
    }

    form.post(route('contratos.contratada.servicos.supressao-vegetacao.execucao.pilhas.store'), {
        preserveState: true,
        onSuccess
    })
}

const images = computed(() => {
    return (form.fotos).map((foto, index) =>  ({
        id: foto?.id ?? null,
        index,
        path: foto?.caminho ?? URL.createObjectURL(foto)
    }));
})

const fileRef = ref()
const onChangeFotos = (event) => {
    form.fotos.push(...Array.from(event.target.files));
    fileRef.value.value = '';
}

const destroyPhoto = (photoId, index) => {
    if (photoId !== null) {
        router.delete(route('contratos.contratada.servicos.supressao-vegetacao.execucao.pilhas.fotos.delete', {arquivo: photoId, pilha: form.id}), {
            preserveState: true,
            onSuccess() {
                modalRef.value.getBsModal().hide();
                form.reset();
            }
        })
    }
    form.fotos.splice(index, 1)
}

const produtosLista = computed(() => {
    if(form.tipo_pilha === 2) return props.produtos.filter(p => p.id === 1);
    if(form.tipo_pilha === 1 || form.tipo_pilha === 0) return props.produtos.filter(p => p.id === 2 || p.id === 3);
    return []
});

const cortes = ref([]);
const selectedCorte = ref({});

watch(() => [form.tipo_pilha, form.area_supressao_id], async ([pilha, supressao]) => {
    selectedCorte.value = {};
    if(pilha !== 1 || pilha === null || !supressao) {
        cortes.value = [];
        return;
    }
    const {data} = await axios.get(route('contratos.contratada.servicos.supressao-vegetacao.execucao.pilhas.get-corte-especie', supressao))
    cortes.value = data;
    if(form.id !== null) {
        selectedCorte.value = cortes.value.find(c => c.id === form.corte_especie_id);
    }
}, {immediate: true});

watch(selectedCorte, (value) => {
    if(!value || Object.keys(value).length === 0) {
        selectedCorte.value = {};
        return;
    }
    form.corte_especie_id = value.id
}, {immediate: true});

defineExpose({abrirModal});
</script>

<template>
    <form @submit.prevent="save">
        <Modal ref="modalRef" title="Cadastro de Controle de Pilhas" modal-dialog-class="modal-xl">
            <template #body>
                <div class="row row-gap-2 mb-2">
                    <div class="col-lg-4">
                        <InputLabel value="Código" for="codigo"/>
                        <input v-model="form.chave" id="codigo" class="form-control" disabled/>
                        <InputError :message="form.errors.chave"/>
                    </div>
                    <div class="col-lg-4">
                        <InputLabel value="Data do Cadastro" for="created_at"/>
                        <input v-model="form.created_at" type="date" id="created_at" class="form-control" disabled/>
                    </div>
                </div>
                <div class="row row-gap-2 mb-2">
                    <div class="col-lg-6">
                        <InputLabel value="Área Supressão" for="area_supressao_id"/>
                        <v-select :options="areasSuprimidas" v-model="form.area_supressao_id" label="chave" :reduce="t => t.id">
                            <template #no-options="{}">
                                Nenhum registro encontrado.
                            </template>
                        </v-select>
                        <InputError :message="form.errors.area_supressao_id"/>
                    </div>
                    <div class="col-lg-6">
                        <InputLabel value="Numero da ASV" for="dt_inicial"/>
                        <v-select v-model="form.licenca_id" :options="licencas"
                                  :get-option-label="licenca => licenca.licenca.numero_licenca"
                                  :reduce="l => l.licenca.id"
                        >
                            <template #no-options="{}">
                                Nenhum registro encontrado.
                            </template>
                        </v-select>
                        <InputError :message="form.errors.licenca_id"/>
                    </div>
                    <div class="col-lg-6">
                        <InputLabel value="Pátio de Estocagem" for="patio_estocagem_id"/>
                        <v-select :options="patios" v-model="form.patio_estocagem_id" label="chave" :reduce="t => t.id">
                            <template #no-options="{}">
                                Nenhum registro encontrado.
                            </template>
                        </v-select>
                        <InputError :message="form.errors.patio_estocagem_id"/>
                    </div>
                </div>
                <div class="row row-gap-2 mb-2">
                    <div class="col-lg-6">
                        <InputLabel value="Tipo de Pilha" for="tipo_pilha"/>
                        <v-select
                            v-model="form.tipo_pilha" @option:selected="form.tipo_produto_id = null"
                            :options="tipos" :reduce="t => t.id">
                            <template #no-options="{}">
                                Nenhum registro encontrado.
                            </template>
                        </v-select>
                        <InputError :message="form.errors.tipo_pilha"/>
                    </div>
                    <div class="col-lg-6">
                        <InputLabel value="Tipo de Produto" for="tipo_produto_id"/>
                        <v-select :options="produtosLista" v-model="form.tipo_produto_id" label="nome" :reduce="t => t.id">
                            <template #no-options="{}">
                                Nenhum registro encontrado.
                            </template>
                        </v-select>
                        <InputError :message="form.errors.tipo_produto_id"/>
                    </div>
                </div>
                <div class="row row-gap-2 mb-2" v-if="form.tipo_pilha === 1 && cortes.length">
                    <div class="col-lg-3">
                        <InputLabel value="Nome científica" for="tipo_produto_id"/>
                        <v-select :options="cortes" v-model="selectedCorte" label="nome">
                            <template #no-options="{}">
                                Nenhum registro encontrado.
                            </template>
                        </v-select>
                    </div>
                    <div class="col-lg-3">
                        <InputLabel value="Nome Popular" for="nome_popular"/>
                        <input v-model="selectedCorte.nome_popular" id="nome_popular" class="form-control" disabled/>
                    </div>
                </div>
                <div class="row row-gap-2">
                    <div class="col-lg-4">
                        <InputLabel value="Volume (m³)" for="volume"/>
                        <input v-model="form.volume" id="volume" class="form-control" />
                        <InputError :message="form.errors.volume"/>
                    </div>
                    <div class="col-lg-4">
                        <InputLabel value="Latitude" for="latitude"/>
                        <input v-model="form.latitude" id="latitude" class="form-control" />
                        <InputError :message="form.errors.latitude"/>
                    </div>
                    <div class="col-lg-4">
                        <InputLabel value="Longitude" for="longitude"/>
                        <input v-model="form.longitude" id="longitude" class="form-control" />
                        <InputError :message="form.errors.longitude"/>
                    </div>
                    <div class="col-12">
                        <InputLabel value="Observações" for="area_fora_app"/>
                        <textarea v-model="form.observacao" id="observacao" class="form-control"></textarea>
                        <InputError :message="form.errors.observacao"/>
                    </div>
                    <div class="col-12">
                        <InputLabel value="Registro Fotográfico" for="fotos"/>
                        <input ref="fileRef" @input="onChangeFotos" id="fotos" type="file" class="form-control" accept=".jpg, .jpeg, .png" multiple>
                        <InputError :message="form.errors.fotos"/>
                        <ul class="list-unstyled d-flex gap-2 flex-wrap mt-3">
                            <li v-for="img in images" class="d-flex flex-column">
                                <span class="avatar avatar-xl">
                                    <img :src="img.path" alt />
                                </span>
                                <button @click="destroyPhoto(img.id, img.index)" type="button" class="btn btn-sm btn-danger">Remover</button>
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
