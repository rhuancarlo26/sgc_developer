<script setup>
import {computed, ref} from "vue";
import Modal from "@/Components/Modal.vue";
import {Link, router, useForm} from "@inertiajs/vue3";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import {IconEye, IconLineDashed, IconMap, IconPencil, IconTrash} from "@tabler/icons-vue";
import {dateTimeFormat} from "@/Utils/DateTimeUtils.js";
import Table from "@/Components/Table.vue";
import LinkConfirmation from "@/Components/LinkConfirmation.vue";
import NavButton from "@/Components/NavButton.vue";

const props = defineProps({
    servico: {type: Object},
    tipos: {type: Array},
    licencas: {type: Array},
    estagios: {type: Array},
    biomas: {type: Array},
})

const form = useForm({
    id: null,
    chave: null,
    dt_inicial: null,
    dt_final: null,
    tipo_bioma_id: null,
    estagio_sucessional_id: null,
    fitofisionomia: null,
    area_em_app: null,
    area_fora_app: null,
    area_total: 0,
    shapefile: null,
    licenca_id: null,
    observacao: null,

    corte_especie: false,
    corte_especies: [],

    fotos: [],
})

const modalRef = ref();
const abrirModal = (item) => {
    form.reset()
    if (item != null) {
        Object.assign(form, {
            ...item,
            corte_especie: item.corte_especies.length > 0,
        });
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
        router.post(route('contratos.contratada.servicos.supressao-vegetacao.execucao.supressao.update'), {
            _method: 'patch',
            ...form.data(),
            fotos: form.fotos.filter(foto => foto instanceof File),
        }, {
            preserveState: true,
            onSuccess
        })
        return
    }

    form.post(route('contratos.contratada.servicos.supressao-vegetacao.execucao.supressao.store'), {
        preserveState: true,
        onSuccess
    })
}

const somaTotalApp = () => {
    form.area_total = parseInt(form.area_em_app) + parseInt(form.area_fora_app);
}

const formCientifica = ref({
    nome: null,
    nome_popular: null,
    qtd_corte: null,
    compensacao: null,
    legislacao: null,
});
const editFormCientifica = ref(null);
const salvarEspecie = () => {
    if (!formCientifica.value.nome || !formCientifica.value.nome_popular || !formCientifica.value.qtd_corte) {
        // alerta('info', 'Atenção aos campos vazios');
        return;
    }
    const value = {...formCientifica.value};
    editFormCientifica.value !== null ? form.corte_especies[editFormCientifica.value] = value : form.corte_especies.push(value);
    Object.keys(formCientifica.value).forEach((i) => formCientifica.value[i] = null);
    editFormCientifica.value = null
}

const removerEspecie = (item, index) => {
    if(item?.id !== null) {
        router.delete(route('contratos.contratada.servicos.supressao-vegetacao.execucao.supressao.corete-especies.delete', item.id), {
            preserveState: true,
        });
    }
    form.corte_especies.splice(index, 1);
}

const editarEspecie = (item, index) => {
    formCientifica.value = {...form.corte_especies[index]};
    editFormCientifica.value = index;
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
        router.delete(route('contratos.contratada.servicos.supressao-vegetacao.execucao.supressao.fotos.delete', {arquivo: photoId, area: form.id}), {
            preserveState: true,
        })
    }
    form.fotos.splice(index, 1)
}

defineExpose({abrirModal});
</script>

<template>
    <form @submit.prevent="save">
        <Modal ref="modalRef" title="Cadastro Área de Supressão" modal-dialog-class="modal-xl">
            <template #body>
                <div class="card">
                    <div class="card-header">
                        <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs">
                            <li class="nav-item">
                                <a href="#dados" class="nav-link active" data-bs-toggle="tab">Dados</a>
                            </li>
                            <li class="nav-item">
                                <a href="#especies" class="nav-link" data-bs-toggle="tab">Espécies</a>
                            </li>
                            <li class="nav-item">
                                <a href="#registros" class="nav-link" data-bs-toggle="tab">Registros fotográficos</a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            <div class="tab-pane active show" id="dados">
                                <div class="row row-gap-2">
                                    <div class="col-lg-4">
                                        <InputLabel value="Código" for="codigo"/>
                                        <input v-model="form.chave" id="nome" class="form-control" disabled/>
                                        <InputError :message="form.errors.chave"/>
                                    </div>
                                    <div class="col-lg-4">
                                        <InputLabel value="Data Início" for="dt_inicial"/>
                                        <input v-model="form.dt_inicial" type="date" id="dt_inicial"
                                               class="form-control"/>
                                        <InputError :message="form.errors.dt_inicial"/>
                                    </div>
                                    <div class="col-lg-4">
                                        <InputLabel value="Data Final" for="dt_final"/>
                                        <input v-model="form.dt_final" type="date" id="dt_final" class="form-control"/>
                                        <InputError :message="form.errors.dt_final"/>
                                    </div>
                                    <div class="col-lg-6">
                                        <InputLabel value="Bioma" for="tipo_bioma_id"/>
                                        <v-select :options="biomas" v-model="form.tipo_bioma_id" label="nome"
                                                  :reduce="t => t.id">
                                            <template #no-options="{}">
                                                Nenhum registro encontrado.
                                            </template>
                                        </v-select>
                                        <InputError :message="form.errors.tipo_bioma_id"/>
                                    </div>
                                    <div class="col-lg-6">
                                        <InputLabel value="Estágio Sucessional" for="estagio_sucessional_id"/>
                                        <v-select :options="estagios" v-model="form.estagio_sucessional_id" label="nome"
                                                  :reduce="t => t.id">
                                            <template #no-options="{}">
                                                Nenhum registro encontrado.
                                            </template>
                                        </v-select>
                                        <InputError :message="form.errors.estagio_sucessional_id"/>
                                    </div>
                                    <div class="col-12">
                                        <InputLabel value="Fitofisionomia" for="fitofisionomia"/>
                                        <textarea v-model="form.fitofisionomia" class="form-control" id="fitofisionomia"
                                                  rows="2" maxlength="250"></textarea>
                                        <InputError :message="form.errors.fitofisionomia"/>
                                    </div>
                                    <div class="col-lg-4">
                                        <InputLabel value="Área em APP:" for="area_em_app"/>
                                        <input v-model="form.area_em_app" type="number" @input="somaTotalApp"
                                               id="area_em_app" class="form-control"/>
                                        <InputError :message="form.errors.area_em_app"/>
                                    </div>
                                    <div class="col-lg-4">
                                        <InputLabel value="Área Fora APP:" for="area_fora_app"/>
                                        <input v-model="form.area_fora_app" type="number" @input="somaTotalApp"
                                               id="area_fora_app" class="form-control"/>
                                        <InputError :message="form.errors.area_fora_app"/>
                                    </div>
                                    <div class="col-lg-4">
                                        <InputLabel value="Área total:" for="area_fora_app"/>
                                        <input v-model="form.area_total" type="number" id="area_total"
                                               class="form-control"/>
                                        <InputError :message="form.errors.area_total"/>
                                    </div>
                                    <div class="col-lg-6">
                                        <InputLabel value="Shapefile" for="local_shape_em_app"/>
                                        <input @input="form.shapefile = $event.target.files[0]" id="shapefile"
                                               type="file" class="form-control" accept=".zip">
                                        <InputError :message="form.errors.shapefile"/>
                                    </div>
                                    <div class="col-lg-6">
                                        <InputLabel value="Numero da ASV" for="licenca_id"/>
                                        <v-select v-model="form.licenca_id" :options="licencas"
                                                  :get-option-label='licenca => `${licenca.licenca.numero_licenca} - ${licenca.licenca.emissor} - ${licenca.licenca.tipo.sigla}`'
                                                  :reduce="l => l.licenca.id"
                                        >
                                            <template #no-options="{}">
                                                Nenhum registro encontrado.
                                            </template>
                                        </v-select>
                                        <InputError :message="form.errors.licenca_id"/>
                                    </div>
                                    <div class="col-12">
                                        <InputLabel value="Observações" for="observacao"/>
                                        <textarea v-model="form.observacao" rows="2" id="observacao"
                                                  class="form-control"></textarea>
                                        <InputError :message="form.errors.observacao"/>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="especies">
                                <div class="mb-3">
                                    <div class="form-label">Corte de espécies Ameaçada/Protegida</div>
                                    <div>
                                        <label class="form-check form-check-inline">
                                            <input v-model="form.corte_especie" id="one" :value="true" class="form-check-input"
                                                   type="radio" name="radios-inline">
                                            <label class="form-check-label" for="one">Sim</label>
                                        </label>
                                        <label class="form-check form-check-inline">
                                            <input v-model="form.corte_especie" id="two" :value="false" class="form-check-input"
                                                   type="radio" name="radios-inline">
                                            <label class="form-check-label" for="two">Não</label>
                                        </label>
                                    </div>
                                    <div class="row row-gap-2" id="form-cientifica" v-if="form.corte_especie">
                                        <div class="form-group-sm col-3">
                                            <input v-model="formCientifica.nome" id="nome_cientifico" type="text"
                                                   class="form-control" placeholder="Nome científica">
                                        </div>
                                        <div class="form-group-sm col-3">
                                            <input v-model="formCientifica.nome_popular" type="text"
                                                   class="form-control" placeholder="Nome Popular">
                                        </div>
                                        <div class="form-group-sm col-3">
                                            <input v-model="formCientifica.qtd_corte" type="text" class="form-control"
                                                   placeholder="N° de Indivíduos">
                                        </div>
                                        <div class="form-group-sm col-3">
                                            <input v-model="formCientifica.compensacao" type="number"
                                                   class="form-control" placeholder="Compensação">
                                        </div>
                                        <div class="form-group-sm col-12 mt-2">
                                            <input v-model="formCientifica.legislacao" type="text" class="form-control"
                                                   placeholder="Legislação">
                                        </div>
                                        <div class="form-group-sm col-3 mt-2">
                                            <button type="button" class="btn btn-success mb-2 mr-2"
                                                    @click="salvarEspecie">
                                                Incluir
                                            </button>
                                        </div>
                                        <Table
                                            :columns="['Nome científica', 'Nome popular', 'N° de Indivíduos', 'Compensação', 'Legislação', 'Ações']"
                                            :records="{ data: form.corte_especies, links: [] }"
                                            table-class="table-hover">
                                            <template #body="{ item, key }">
                                                <tr>
                                                    <td>{{ item.nome }}</td>
                                                    <td>{{ item.nome_popular }}</td>
                                                    <td>{{ item.qtd_corte }}</td>
                                                    <td>{{ item.compensacao }}</td>
                                                    <td>{{ item.legislacao }}</td>
                                                    <td>
                                                        <div class="d-flex justify-content-center">
                                                            <NavButton @click="editarEspecie(item, key)" type-button="warning" class="btn-icon btn-sm" :icon="IconPencil" />
                                                            <NavButton @click="removerEspecie(item, key)" type-button="danger" class="btn-icon btn-sm" :icon="IconTrash" />
                                                        </div>
                                                    </td>
                                                </tr>
                                            </template>
                                        </Table>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="registros">
                                <InputLabel value="Anexar fotos" for="fotos"/>
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
