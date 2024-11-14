<script setup>

import { onMounted, ref, watch } from "vue";
import { Link, router, useForm } from "@inertiajs/vue3";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import Modal from "@/Components/Modal.vue";
import { IconTrash } from "@tabler/icons-vue";
import Table from "@/Components/Table.vue";
import axios from "axios";
import LinkConfirmation from "@/Components/LinkConfirmation.vue";

const props = defineProps({
    showAction: { type: Boolean, default: true },
    ufs: { type: Array },
    campanhas: { type: Array },
    licencasVigente: { type: Array },
    servico: { type: Object },
})

const form = useForm({
    id: null,
    arquivo: null,
    fk_servico: null,
    fk_campanha: null,
    fk_grupo_amostrado: null,
    data_registro: null,
    fk_estado: null,
    km: null,
    hora_registro: null,
    latitude: null,
    longitude: null,
    sentido: null,
    margem: null,
    classe: null,
    ordem: null,
    familia: null,
    genero: null,
    especie: null,
    nome_comum: null,
    sexo: null,
    faixa_etaria: null,
    coletado: null,
    n_registro_tombamento: null,
    carcaca_removida: null,
    reducao_biologica: null,
    n_individuos: null,
    estadual: null,
    federal: null,
    iucn: null,
});

const save = () => {
    form.transform((data) => ({
        ...data,
        fk_servico: props.servico.id,
    }));

    const onSuccess = () => {
        modalRef.value.getBsModal().hide();
        form.reset();
    }

    if (form.id !== null) {
        form.patch(route('contratos.contratada.servicos.mon_atp_fauna.execucao.registros.update'), {
            preserveState: true,
            onSuccess
        })
        return
    }

    form.post(route('contratos.contratada.servicos.mon_atp_fauna.execucao.registros.store'), {
        preserveState: true,
        onSuccess
    })
}

const tab = ref(null)
const modalRef = ref();
const abrirModal = (item = null) => {
    form.reset()
    Object.assign(form, item)
    modalRef.value.getBsModal().show();
    tab.value = 'registro_local';
}

const getImagensRegistro = async () => {
    const { data } = await axios.get(route('contratos.contratada.servicos.mon_atp_fauna.execucao.registros.imagens', form.id));
    return data;
}

const imagensRegistro = ref(null)

watch(tab, async (value) => {
    if (value === 'registro_fotografico' && !imagensRegistro.value) {
        imagensRegistro.value = await getImagensRegistro();
    }
}, { immediate: true });

onMounted(() => {
    modalRef.value.$el.addEventListener('hidden.bs.modal', () => {
        form.reset()
        tab.value = null;
        imagensRegistro.value = null;
    });
});

defineExpose({ abrirModal });

</script>

<template>
    <form @submit.prevent="save">
        <Modal ref="modalRef" title="Registro" modal-dialog-class="modal-xl">
            <template #body>
                <div class="card">
                    <div class="card-header">
                        <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs">
                            <li class="nav-item">
                                <a href="#registro_local" @click="tab = 'registro_local'"
                                    :class="{ active: tab === 'registro_local' }" class="nav-link"
                                    data-bs-toggle="tab">Local</a>
                            </li>
                            <li class="nav-item">
                                <a href="#registro_identificacao" @click="tab = 'registro_identificacao'"
                                    :class="{ active: tab === 'registro_identificacao' }" class="nav-link"
                                    data-bs-toggle="tab"> Identificação do Espécime</a>
                            </li>
                            <li class="nav-item">
                                <a href="#registro_dados_especime" @click="tab = 'registro_dados_especime'"
                                    :class="{ active: tab === 'registro_dados_especime' }" class="nav-link"
                                    data-bs-toggle="tab">Dados do Espécime</a>
                            </li>
                            <li class="nav-item">
                                <a href="#condicionantes" @click="tab = 'condicionantes'"
                                    :class="{ active: tab === 'condicionantes' }" class="nav-link"
                                    data-bs-toggle="tab">Status de conservação do espécime</a>
                            </li>
                            <li class="nav-item">
                                <a href="#registro_fotografico" @click="tab = 'registro_fotografico'"
                                    :class="{ active: tab === 'registro_fotografico' }" class="nav-link"
                                    data-bs-toggle="tab">Registro fotográfico</a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            <div :class="[tab === 'registro_local' ? 'active show' : '']" class="tab-pane"
                                id="registro_local">

                                <div class="row row-gap-2 mb-2">
                                    <div class="col-lg-2">
                                        <InputLabel value="ID registro" for="campanha_id" />
                                        <input v-model="form.id" type="text" id="campanha_id" class="form-control"
                                            disabled />
                                        <InputError :message="form.errors.id" />
                                    </div>
                                </div>
                                <div class="row row-gap-2 mb-2">
                                    <div class="col-lg-4">
                                        <InputLabel value="Selecionar campanha" for="fk_campanha" />
                                        <v-select :options="campanhas" v-model="form.fk_campanha" :reduce="t => t.id"
                                            :disabled="!showAction" label="id">
                                            <template #no-options="{}">
                                                Nenhum registro encontrado.
                                            </template>
                                        </v-select>
                                        <InputError :message="form.errors.fk_campanha" />
                                    </div>
                                    <div class="col-lg-4">
                                        <InputLabel value="Grupo amostrado" for="fk_campanha" />
                                        <v-select :options="[
                                            { id: '1', label: 'Avifauna' },
                                            { id: '2', label: 'Herpetofauna' },
                                            { id: '3', label: 'Mastofauna' }
                                        ]" v-model="form.fk_grupo_amostrado" :reduce="t => t.id"
                                            :disabled="!showAction">
                                            <template #no-options="{}">
                                                Nenhum registro encontrado.
                                            </template>
                                        </v-select>
                                        <InputError :message="form.errors.fk_grupo_amostrado" />
                                    </div>
                                    <div class="col-lg-3">
                                        <InputLabel value="Hora Registro" for="data_registro" />
                                        <input v-model="form.data_registro" type="date" id="data_registro"
                                            class="form-control" :disabled="!showAction" />
                                        <InputError :message="form.errors.data_registro" />
                                    </div>
                                </div>
                                <div class="row row-gap-2 mb-2">
                                    <div class="col-lg-4">
                                        <InputLabel value="UF" for="fk_estado" />
                                        <v-select :options="licencasVigente" v-model="form.fk_estado"
                                            :get-option-label="(item) => `${item.uf} - ${item.nome_estado}`"
                                            :reduce="t => t.id_estados" :disabled="!showAction">
                                            <template #no-options="{}">
                                                Nenhum registro encontrado.
                                            </template>
                                        </v-select>
                                        <InputError :message="form.errors.fk_estado" />
                                    </div>
                                    <div class="col-lg-4">
                                        <InputLabel value="KM" for="km" />
                                        <input v-model="form.km" type="text" id="km" class="form-control"
                                            :disabled="!showAction" />
                                        <InputError :message="form.errors.km" />
                                    </div>
                                    <div class="col-lg-4">
                                        <InputLabel value="Hora Registro" for="km" />
                                        <input v-model="form.hora_registro" type="time" id="hora_registro"
                                            class="form-control" :disabled="!showAction" />
                                        <InputError :message="form.errors.hora_registro" />
                                    </div>
                                </div>
                                <div class="row row-gap-2 mb-2">
                                    <div class="col-lg-4">
                                        <InputLabel value="Latitude" for="latitude" />
                                        <input v-model="form.latitude" type="text" id="latitude" class="form-control"
                                            :disabled="!showAction" />
                                        <InputError :message="form.errors.latitude" />
                                    </div>
                                    <div class="col-lg-4">
                                        <InputLabel value="Longitude" for="longitude" />
                                        <input v-model="form.longitude" type="text" id="longitude" class="form-control"
                                            :disabled="!showAction" />
                                        <InputError :message="form.errors.longitude" />
                                    </div>
                                </div>
                                <div class="row row-gap-2 mb-2">
                                    <div class="col-3">
                                        <InputLabel value="Sentido" />
                                        <input type="radio" value="C" v-model="form.sentido" :disabled="!showAction">
                                        Crescente
                                        <br />
                                        <input type="radio" value="D" v-model="form.sentido" :disabled="!showAction">
                                        Decrescente
                                    </div>
                                    <div class="col-2">
                                        <InputLabel value="Margem" />
                                        <input type="radio" value="E" v-model="form.margem" :disabled="!showAction">
                                        Esquerda
                                        <br />
                                        <input type="radio" value="D" v-model="form.margem" :disabled="!showAction">
                                        Direita
                                    </div>
                                </div>
                            </div>
                            <div :class="[tab === 'registro_identificacao' ? 'active show' : '']" class="tab-pane"
                                id="registro_identificacao">
                                <div class="row mb-2">
                                    <div class="col-lg-4">
                                        <InputLabel value="Classe" for="classe" />
                                        <v-select :options="[
                                            { id: '1', label: 'Aves' },
                                            { id: '2', label: 'Mamíferos' },
                                            { id: '3', label: 'Répteis' },
                                            { id: '4', label: 'Anfíbios' },
                                        ]" v-model="form.classe" :reduce="t => t.id" :disabled="!showAction">
                                            <template #no-options="{}">
                                                Nenhum registro encontrado.
                                            </template>
                                        </v-select>
                                        <InputError :message="form.errors.classe" />
                                    </div>
                                    <div class="col-lg-4">
                                        <InputLabel value="Ordem" for="ordem" />
                                        <input v-model="form.ordem" type="text" id="ordem" class="form-control"
                                            :disabled="!showAction" />
                                        <InputError :message="form.errors.ordem" />
                                    </div>
                                    <div class="col-lg-4">
                                        <InputLabel value="Família" for="familia" />
                                        <input v-model="form.familia" type="text" id="familia" class="form-control"
                                            :disabled="!showAction" />
                                        <InputError :message="form.errors.familia" />
                                    </div>
                                    <div class="col-lg-4">
                                        <InputLabel value="Gênero" for="genero" />
                                        <input v-model="form.genero" type="text" id="genero" class="form-control"
                                            :disabled="!showAction" />
                                        <InputError :message="form.errors.genero" />
                                    </div>
                                    <div class="col-lg-4">
                                        <InputLabel value="Espécie" for="especie" />
                                        <input v-model="form.especie" type="text" id="especie" class="form-control"
                                            :disabled="!showAction" />
                                        <InputError :message="form.errors.especie" />
                                    </div>
                                    <div class="col-lg-4">
                                        <InputLabel value="Nome comum" for="nome_comum" />
                                        <input v-model="form.nome_comum" type="text" id="nome_comum"
                                            class="form-control" :disabled="!showAction" />
                                        <InputError :message="form.errors.nome_comum" />
                                    </div>
                                </div>
                            </div>
                            <div :class="[tab === 'registro_dados_especime' ? 'active show' : '']" class="tab-pane"
                                id="registro_dados_especime">
                                <div class="row mb-2">
                                    <div class="col-lg-4">
                                        <InputLabel value="Sexo" for="sexo" />
                                        <v-select :options="[
                                            { id: 'macho', label: 'Macho' },
                                            { id: 'femea', label: 'Fêmea' },
                                            { id: 'sem_indentificacao', label: 'Sem indentificação' }
                                        ]" v-model="form.sexo" :reduce="t => t.id" :disabled="!showAction">
                                            <template #no-options="{}">
                                                Nenhum registro encontrado.
                                            </template>
                                        </v-select>
                                        <InputError :message="form.errors.sexo" />
                                    </div>
                                    <div class="col-lg-4">
                                        <InputLabel value="Faixa etária" for="faixa_etaria" />
                                        <v-select :options="[
                                            { id: 'Jovem', label: 'Jovem' },
                                            { id: 'Adulto', label: 'Adulto' },
                                            { id: 'Indeterminado', label: 'Indeterminado' }
                                        ]" v-model="form.faixa_etaria" :reduce="t => t.id" :disabled="!showAction">
                                            <template #no-options="{}">
                                                Nenhum registro encontrado.
                                            </template>
                                        </v-select>
                                        <InputError :message="form.errors.faixa_etaria" />
                                    </div>
                                    <div class="col-lg-4">
                                        <InputLabel value="Coletado" for="coletado" />
                                        <v-select :options="[
                                            { id: 'sim', label: 'Sim' },
                                            { id: 'nao', label: 'Não' },
                                        ]" v-model="form.coletado" :reduce="t => t.id" :disabled="!showAction">
                                            <template #no-options="{}">
                                                Nenhum registro encontrado.
                                            </template>
                                        </v-select>
                                        <InputError :message="form.errors.coletado" />
                                    </div>
                                    <div v-if="form.coletado === 'sim'" class="col-lg-12">
                                        <InputLabel value="N° tombamento ou Registro de entrada na coleção:"
                                            for="n_registro_tombamento" />
                                        <input v-model="form.n_registro_tombamento" type="text"
                                            id="n_registro_tombamento" class="form-control" :disabled="!showAction" />
                                        <InputError :message="form.errors.n_registro_tombamento" />
                                    </div>
                                    <div class="col-lg-4">
                                        <InputLabel value="Carcaça removida:" for="carcaca_removida" />
                                        <v-select :options="[
                                            { id: 'sim', label: 'Sim' },
                                            { id: 'nao', label: 'Não' },
                                        ]" v-model="form.carcaca_removida" :reduce="t => t.id" :disabled="!showAction">
                                            <template #no-options="{}">
                                                Nenhum registro encontrado.
                                            </template>
                                        </v-select>
                                        <InputError :message="form.errors.carcaca_removida" />
                                    </div>
                                    <div class="col-lg-4">
                                        <InputLabel value="Redução Biológica com Cal:" for="reducao_biologica" />
                                        <v-select :options="[
                                            { id: 'sim', label: 'Sim' },
                                            { id: 'nao', label: 'Não' },
                                        ]" v-model="form.reducao_biologica" :reduce="t => t.id"
                                            :disabled="!showAction">
                                            <template #no-options="{}">
                                                Nenhum registro encontrado.
                                            </template>
                                        </v-select>
                                        <InputError :message="form.errors.reducao_biologica" />
                                    </div>
                                </div>
                                <div class="row row-gap-2 mb-2">
                                    <div class="col-lg-12">
                                        <InputLabel value="Nº de indivíduos do registro:" for="n_individuos" />
                                        <input v-model="form.n_individuos" type="text" id="n_individuos"
                                            class="form-control" :disabled="!showAction" />
                                        <InputError :message="form.errors.n_individuos" />
                                    </div>
                                </div>
                            </div>
                            <div :class="[tab === 'condicionantes' ? 'active show' : '']" class="tab-pane"
                                id="condicionantes">
                                <div class="row row-gap-2 mb-2">
                                    <div class="col-lg-4">
                                        <InputLabel value="Estadual:" for="estadual" />
                                        <v-select :options="[
                                            { id: 'Ameaçado (CR,EN e VU)', label: 'Ameaçado (CR,EN e VU)' },
                                            { id: 'NT – Quase Ameaçada', label: 'NT – Quase Ameaçada' },
                                            { id: 'DD – Dados Insuficientes', label: 'DD – Dados Insuficientes' },
                                            { id: 'NE – Não Avaliado', label: 'NE – Não Avaliado' }
                                        ]" v-model="form.estadual" :reduce="t => t.id" :disabled="!showAction">
                                            <template #no-options="{}">
                                                Nenhum registro encontrado.
                                            </template>
                                        </v-select>
                                        <InputError :message="form.errors.estadual" />
                                    </div>
                                    <div class="col-lg-4">
                                        <InputLabel value="Federal:" for="federal" />
                                        <v-select :options="[
                                            { id: 'Ameaçado (CR,EN e VU)', label: 'Ameaçado (CR,EN e VU)' },
                                            { id: 'NT – Quase Ameaçada', label: 'NT – Quase Ameaçada' },
                                            { id: 'DD – Dados Insuficientes', label: 'DD – Dados Insuficientes' },
                                            { id: 'NE – Não Avaliado', label: 'NE – Não Avaliado' }
                                        ]" v-model="form.federal" :reduce="t => t.id" :disabled="!showAction">
                                            <template #no-options="{}">
                                                Nenhum registro encontrado.
                                            </template>
                                        </v-select>
                                        <InputError :message="form.errors.federal" />
                                    </div>
                                    <div class="col-lg-4">
                                        <InputLabel value="IUCN:" for="iucn" />
                                        <v-select :options="[
                                            { id: 'EX – Extinto', label: 'EX – Extinto' },
                                            { id: 'EW – Extinto na Natureza', label: 'EW – Extinto na Natureza' },
                                            { id: 'CR – Criticamente em Perigo', label: 'CR – Criticamente em Perigo' },
                                            { id: 'EN – Em Perigo', label: 'EN – Em Perigo' },
                                            { id: 'VU – Vulnerável', label: 'VU – Vulnerável' },
                                            { id: 'NT – Quase Ameaçada', label: 'NT – Quase Ameaçada' },
                                            { id: 'LC – Pouco Preocupante', label: 'LC – Pouco Preocupante' },
                                            { id: 'DD – Dados Insuficientes', label: 'DD – Dados Insuficientes' },
                                            { id: 'NE – Não Avaliado', label: 'NE – Não Avaliado' }
                                        ]" v-model="form.iucn" :reduce="t => t.id" :disabled="!showAction">
                                            <template #no-options="{}">
                                                Nenhum registro encontrado.
                                            </template>
                                        </v-select>
                                        <InputError :message="form.errors.iucn" />
                                    </div>
                                </div>
                            </div>
                            <div :class="[tab === 'registro_fotografico' ? 'active show' : '']" class="tab-pane"
                                id="registro_fotografico">
                                <div class="row row-gap-2 mb-2">
                                    <div class="col-lg-6">
                                        <InputLabel value="Buscar Arquivo (.jpg/png)" for="n_registro_tombamento" />
                                        <input @input="form.arquivo = $event.target.files[0]" type="file"
                                            accept="image/png, image/jpeg" class="form-control">
                                        <InputError :message="form.errors.arquivo" />
                                    </div>
                                    <div class="col-lg-6">
                                        <InputLabel value="Imagens do Espécime:" for="anexo_foto" />
                                        <Table :columns="['Arquivo', ...[showAction ? 'Ação' : null]]"
                                            :records="{ data: imagensRegistro, links: [] }" table-class="table-hover">
                                            <template #body="{ item }">
                                                <tr>
                                                    <td>{{ item.nome }}</td>
                                                    <td v-if="showAction">
                                                        <LinkConfirmation v-slot="confirmation"
                                                            :options="{ text: 'Excluir registro?' }">
                                                            <Link :onBefore="(request) => confirmation.show({
                                                                ...request,
                                                                preserveState: true,
                                                                onSuccess: () => {
                                                                    modalRef.getBsModal().hide();
                                                                }
                                                            })" :href="route('contratos.contratada.servicos.mon_atp_fauna.execucao.registros.imagens-delete', item.id)"
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
