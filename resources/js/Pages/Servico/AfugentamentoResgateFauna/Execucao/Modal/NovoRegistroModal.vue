<script setup>
import Modal from "@/Components/Modal.vue";
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import { ref } from "vue";
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    grupoAmostrado: { type: Array },
    frenteSupressao: { type: Array },
    formaRegistro: { type: Array },
    ufs: { type: Array },
    statusConservacaoFederal: { type: Array },
    statusConservacaoIucn: { type: Array },
});

const modalDetalhes = ref(null);
const contrato = ref({});
const servico = ref({});

const abrirModal = (itemContrato, itemServico) => {
    contrato.value = itemContrato;
    servico.value = itemServico;
    form.reset();
    modalDetalhes.value.getBsModal().show();
}

const updateModal = (itemRegistro) => {
    console.log(itemRegistro);
    
    form.id = itemRegistro.id;
    form.nome_registro = itemRegistro.nome_registro;
    form.id_frente = itemRegistro.id_frente;
    form.id_grupo_amostrado = itemRegistro.id_grupo_amostrado;
    form.data_registro = formatDate(itemRegistro.data_registro);
    form.hora_registro = itemRegistro.hora_registro;
    form.id_estado = itemRegistro.id_estado;
    form.km = itemRegistro.km;
    form.latitude = itemRegistro.latitude;
    form.longitude = itemRegistro.longitude;
    form.sentido = itemRegistro.sentido;
    form.margem = itemRegistro.margem;
    form.classe = itemRegistro.classe;
    form.ordem = itemRegistro.ordem;
    form.familia = itemRegistro.familia;
    form.genero = itemRegistro.genero;
    form.especie = itemRegistro.especie;
    form.nome_comum = itemRegistro.nome_comum;
    form.categoria = itemRegistro.categoria;
    form.sexo = itemRegistro.sexo;
    form.faixa_etaria = itemRegistro.faixa_etaria;
    form.n_individuos = itemRegistro.n_individuos;
    form.id_forma_registro = itemRegistro.id_forma_registro;
    form.id_tipo_registro = itemRegistro.id_tipo_registro;
    form.id_destinacao_registro = itemRegistro.id_destinacao_registro;
    form.latitude_soltura = itemRegistro.latitude_soltura;
    form.longitude_soltura = itemRegistro.longitude_soltura;
    form.nome_local = itemRegistro.nome_local;
    form.coletado = itemRegistro.coletado;
    form.n_registro_tombamento = itemRegistro.n_registro_tombamento;
    form.id_status_conservacao_federal = itemRegistro.id_status_conservacao_federal;
    form.id_status_conservacao_iucn = itemRegistro.id_status_conservacao_iucn;
    console.log(form);
    
    classe();
    formaTipoRegistro();
    tipoDestinacaoRegistro();
    modalDetalhes.value.getBsModal().show();
}

const form = useForm({
    id: null,
    nome_registro: '',
    id_frente: null,
    id_grupo_amostrado: null,
    data_registro: null,
    hora_registro: null,
    id_estado: null,
    km: '',
    latitude: '',
    longitude: '',
    sentido: '',
    margem: '',
    classe: null,
    ordem: '',
    familia: '',
    genero: '',
    especie: '',
    nome_comum: '',
    categoria: '',
    sexo: '',
    faixa_etaria: '',
    n_individuos: '',
    id_forma_registro: null,
    id_tipo_registro: null,
    id_destinacao_registro: null,
    latitude_soltura: '',
    longitude_soltura: '',
    nome_local: '',
    coletado: '',
    n_registro_tombamento: '',
    documento: null,
    id_status_conservacao_federal: null,
    id_status_conservacao_iucn: null,
});

const salvar = () => {
    if (form.id) {
        form.patch(route('contratos.contratada.servicos.afugentamento.resgate.fauna.execucao.registro.update', form.id), {
            preserveScroll: true,
            onSuccess: () => {
                modalDetalhes.value.getBsModal().hide();
                form.reset();
            },
        });
        return;
    }

    form.post(route('contratos.contratada.servicos.afugentamento.resgate.fauna.execucao.registro.create', servico.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            modalDetalhes.value.getBsModal().hide();
            form.reset();
        },
    });
}

const classeRegistro = ref([]);

const classe = () => {
    if (form.id_grupo_amostrado === 1) {
        classeRegistro.value = [];
        classeRegistro.value.push('Aves');
        form.classe = 'Aves';
    }
    if (form.id_grupo_amostrado === 2) {
        classeRegistro.value = [];
        classeRegistro.value.push('Anfíbios');
        classeRegistro.value.push('Répteis');
    }
    if (form.id_grupo_amostrado === 3) {
        classeRegistro.value = [];
        classeRegistro.value.push('Mamíferos');
        form.classe = 'Mamíferos';
    }
}

const tipoRegistro = ref([]);

const formaTipoRegistro = () => {
    if (form.id_forma_registro == 1) {
        tipoRegistro.value = [];
        tipoRegistro.value.push({ id: 1, nome: 'Vivo sem ferimento' });
        tipoRegistro.value.push({ id: 2, nome: 'Vivo com ferimento' });
    }
    if (form.id_forma_registro == 2) {
        tipoRegistro.value = [];
        tipoRegistro.value.push({ id: 4, nome: 'Vivo sem ferimento' });
        tipoRegistro.value.push({ id: 5, nome: 'Vivo com ferimento' });
        tipoRegistro.value.push({ id: 6, nome: 'Ninho' });
        tipoRegistro.value.push({ id: 7, nome: 'Colméia' });
    }
    if (form.id_forma_registro == 3) {
        tipoRegistro.value = [];
        tipoRegistro.value.push({ id: 3, nome: 'Morto' });
    }
    if (form.id_forma_registro == 4) {
        tipoRegistro.value = [];
        tipoRegistro.value.push({ id: 8, nome: 'Ninho' });

    }
}

const destinacaoRegistro = ref([]);

const tipoDestinacaoRegistro = () => {
    if (form.id_tipo_registro == 1) {
        destinacaoRegistro.value = [];
        destinacaoRegistro.value.push({ id: 1, nome: 'Soltura em área adjacente' });
    }
    if (form.id_tipo_registro == 5) {
        destinacaoRegistro.value = [];
        destinacaoRegistro.value.push({ id: 4, nome: 'Eutanásia' });
        destinacaoRegistro.value.push({ id: 8, nome: 'Encaminhado ao hospital' });
        destinacaoRegistro.value.push({ id: 2, nome: 'Encaminhado ao Veterinário' });
    }
    if (form.id_tipo_registro == 6) {
        destinacaoRegistro.value = [];
        destinacaoRegistro.value.push({ id: 6, nome: 'Área adjacente' });
    }
    if (form.id_tipo_registro == 7) {
        destinacaoRegistro.value = [];
        destinacaoRegistro.value.push({ id: 5, nome: 'Realocação' });
    }
    if (form.id_tipo_registro == 8) {
        destinacaoRegistro.value = [];
        destinacaoRegistro.value.push({ id: 7, nome: 'Monitoramento' });
    }
}

const salvarImagens = () => {
    form.post(route('contratos.contratada.servicos.afugentamento.resgate.fauna.execucao.registro.fotografico', { registro: form.id }), { forceFormData: true }, {
        preserveScroll: true,
        onSuccess: () => {
            modalDetalhes.value.getBsModal().hide();
            form.reset();
        },
    });
}

const formatDate = (dateString) => {
    if (!dateString) return '';
    const date = new Date(dateString);
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
};

const close = () => {
    modalDetalhes.value.getBsModal().hide();
    form.clearErrors();
    form.reset();
};

defineExpose({ abrirModal, updateModal });

</script>

<template>
    <form @submit.prevent="salvar">
        <Modal ref="modalDetalhes" title="Registro" modal-dialog-class="modal-xl">
            <template #body>
                <div class="mb-4">
                    <div class="card-body">
                        <div class="accordion" id="servico">
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="heading-1">
                                    <button class="accordion-button " type="button" data-bs-toggle="collapse"
                                        data-bs-target="#dadosGerais" aria-expanded="true">
                                        Dados Gerais
                                    </button>
                                </h2>
                                <div id="dadosGerais" class="accordion-collapse collapse show"
                                    data-bs-parent="#servico">
                                    <div class="accordion-body pt-0 card-body space-y-5">
                                        <div class="d-flex">
                                            <div class="col-lg-4 me-2">
                                                <InputLabel for="id" value="ID registro" />
                                                <input id="id" type="number" class="form-control" v-model="form.id"
                                                    autofocus placeholder="ID do registro" autocomplete="id" disabled />
                                                <InputError class="mt-2" :message="form.errors.id" />
                                            </div>

                                            <div class="col-lg-4 me-2">
                                                <InputLabel for="" value="Selecionar Frente de Supressão" />
                                                <select class="form-select" v-model="form.id_frente"
                                                    aria-label="Default select example">
                                                    <option selected>Selecione a frente de supressão</option>
                                                    <option v-for="frente in frenteSupressao" :value="frente.id">
                                                        {{ frente.id }}
                                                    </option>
                                                </select>
                                                <InputError class="mt-2" :message="form.errors.id_frente" />
                                            </div>

                                            <div class="col-lg-4">
                                                <InputLabel for="" value="Grupo amostrado" />
                                                <select class="form-select" v-model="form.id_grupo_amostrado"
                                                    @change="classe()" aria-label="Default select example">
                                                    <option selected>Selecione o grupo amostrado</option>
                                                    <option v-for="amostrado in grupoAmostrado" :value="amostrado.id"
                                                        :key="amostrado.id">
                                                        {{ amostrado.nome }}
                                                    </option>
                                                    <InputError class="mt-2"
                                                        :message="form.errors.id_grupo_amostrado" />
                                                </select>
                                            </div>
                                        </div>

                                        <div class="d-flex">
                                            <div class="col-lg-4 me-2">
                                                <InputLabel for="data_registro" value="Data Registro" />
                                                <input id="data_registro" type="date" class="form-control"
                                                    v-model="form.data_registro" autofocus placeholder="Data registro"
                                                    autocomplete="data_registro" />
                                                <InputError class="mt-2" :message="form.errors.data_registro" />
                                            </div>

                                            <div class="col-lg-4 me-2">
                                                <InputLabel for="" value="Hora Registro" />
                                                <input id="" type="time" class="form-control"
                                                    v-model="form.hora_registro" autofocus placeholder="Hora registro"
                                                    autocomplete="hora_registro" />
                                                <InputError class="mt-2" :message="form.errors.hora_registro" />
                                            </div>

                                            <div class="col-lg-4">
                                                <InputLabel for="" value="UF" />
                                                <select class="form-select" v-model="form.id_estado"
                                                    aria-label="Default select example">
                                                    <option selected>Selecione a UF</option>
                                                    <option v-for="uf in ufs" :value="uf.id">
                                                        {{ uf.uf }}
                                                    </option>
                                                    <InputError class="mt-2" :message="form.errors.id_estado" />
                                                </select>
                                            </div>
                                        </div>

                                        <div class="d-flex">
                                            <div class="col-lg-4 me-2">
                                                <InputLabel for="" value="KM" />
                                                <input id="" type="text" class="form-control" v-model="form.km"
                                                    autofocus placeholder="KM" autocomplete="km" />
                                                <InputError class="mt-2" :message="form.errors.km" />
                                            </div>

                                            <div class="col-lg-4 me-2">
                                                <InputLabel for="" value="Latitude" />
                                                <input id="" type="text" class="form-control" v-model="form.latitude"
                                                    autofocus placeholder="Latitude" autocomplete="latitude" />
                                                <InputError class="mt-2" :message="form.errors.latitude" />
                                            </div>

                                            <div class="col-lg-4 me-2">
                                                <InputLabel for="" value="Longitude" />
                                                <input id="" type="text" class="form-control" v-model="form.longitude"
                                                    autofocus placeholder="Longitude" autocomplete="longitude" />
                                                <InputError class="mt-2" :message="form.errors.longitude" />
                                            </div>
                                        </div>

                                        <div class="d-flex">
                                            <div class="col-lg-4 me-2">
                                                <InputLabel for="" value="Sentido" />
                                                <div class="d-flex">
                                                    <input type="radio" class="form-check-input me-2"
                                                        v-model="form.sentido" name="crescente" id="crescente"
                                                        value="C">
                                                    <span class="form-check-label me-4" for="crescente">Crescente</span>
                                                    <input type="radio" class="form-check-input me-2"
                                                        v-model="form.sentido" name="decrescente" id="decrescente"
                                                        value="D">
                                                    <span class="form-check-label" for="decrescente">Decrescente</span>
                                                </div>
                                                <InputError class="mt-2" :message="form.errors.sentido" />
                                            </div>

                                            <div class="col-lg-4">
                                                <InputLabel for="" value="Margem" />
                                                <div class="d-flex">
                                                    <input type="radio" class="form-check-input me-2"
                                                        v-model="form.margem" name="direita" id="direita" value="D">
                                                    <span class="form-check-label me-4" for="direita">Direita</span>
                                                    <input type="radio" class="form-check-input me-2"
                                                        v-model="form.margem" name="esquerda" id="esquerda" value="E">
                                                    <span class="form-check-label" for="esquerda">Esquerda</span>
                                                </div>
                                                <InputError class="mt-2" :message="form.errors.margem" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="heading-2">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#identificacaoEspecime" aria-expanded="false">
                                        Identificação do Espécime
                                    </button>
                                </h2>
                                <div id="identificacaoEspecime" class="accordion-collapse collapse"
                                    data-bs-parent="#servico">
                                    <div class="accordion-body pt-0 card-body space-y-5">
                                        <div class="d-flex">
                                            <div class="col-lg-4 me-2">
                                                <InputLabel for="" value="Classe" />
                                                <select class="form-select" v-model="form.classe"
                                                    aria-label="Default select example">
                                                    <option selected>Selecione a classe</option>
                                                    <option v-for="item in classeRegistro" :value="item">{{ item }}
                                                    </option>
                                                </select>
                                                <!-- <InputError class="mt-2" :message="form.errors." /> -->
                                            </div>

                                            <div class="col-lg-4 me-2">
                                                <InputLabel for="" value="Ordem" />
                                                <input id="" type="text" class="form-control" v-model="form.ordem"
                                                    autofocus placeholder="Ordem" autocomplete="ordem" />
                                                <InputError class="mt-2" :message="form.errors.ordem" />
                                            </div>

                                            <div class="col-lg-4 me-2">
                                                <InputLabel for="" value="Família" />
                                                <input id="" type="text" class="form-control" v-model="form.familia"
                                                    autofocus placeholder="Familia" autocomplete="familia" />
                                                <InputError class="mt-2" :message="form.errors.familia" />
                                            </div>
                                        </div>

                                        <div class="d-flex">
                                            <div class="col-lg-4 me-2">
                                                <InputLabel for="" value="Gênero" />
                                                <input id="" type="text" class="form-control" v-model="form.genero"
                                                    autofocus placeholder="Gênero" autocomplete="genero" />
                                                <InputError class="mt-2" :message="form.errors.genero" />
                                            </div>

                                            <div class="col-lg-4 me-2">
                                                <InputLabel for="" value="Espécie" />
                                                <input id="" type="text" class="form-control" v-model="form.especie"
                                                    autofocus placeholder="Espécie" autocomplete="especie" />
                                                <InputError class="mt-2" :message="form.errors.especie" />
                                            </div>

                                            <div class="col-lg-4 me-2">
                                                <InputLabel for="" value="Nome comum" />
                                                <input id="" type="text" class="form-control" v-model="form.nome_comum"
                                                    autofocus placeholder="Nome comum" autocomplete="nome_comum" />
                                                <InputError class="mt-2" :message="form.errors.nome_comum" />
                                            </div>
                                        </div>

                                        <div class="d-flex">
                                            <div class="col-lg-4 me-2">
                                                <InputLabel for="" value="Categoria" />
                                                <select class="form-select" v-model="form.categoria"
                                                    aria-label="Default select example">
                                                    <option selected>Selecione a Categoria</option>
                                                    <option value="Fauna silvestre brasileira">Fauna silvestre
                                                        brasileira</option>
                                                    <option value="Fauna silvestre exótica">Fauna silvestre exótica
                                                    </option>
                                                    <option value="Fauna doméstica">Fauna doméstica</option>
                                                    <option value="Fauna sinantrópica">Fauna sinantrópica</option>
                                                </select>
                                                <InputError class="mt-2" :message="form.errors.categoria" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="heading-3">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#dadosEspecime" aria-expanded="false">
                                        Dados do Espécime
                                    </button>
                                </h2>
                                <div id="dadosEspecime" class="accordion-collapse collapse" data-bs-parent="#servico">
                                    <div class="accordion-body pt-0 card-body space-y-5">
                                        <div class="d-flex">
                                            <div class="col-lg-4 me-2">
                                                <InputLabel for="" value="Sexo" />
                                                <select class="form-select" v-model="form.sexo"
                                                    aria-label="Default select example">
                                                    <option selected>Selecione o Sexo</option>
                                                    <option value="Macho">Macho</option>
                                                    <option value="Fêmea">Fêmea</option>
                                                    <option value="Indefinido">Indefinido</option>
                                                </select>
                                                <InputError class="mt-2" :message="form.errors.sexo" />
                                            </div>

                                            <div class="col-lg-4 me-2">
                                                <InputLabel for="" value="Faixa etária" />
                                                <select class="form-select" v-model="form.faixa_etaria"
                                                    aria-label="Default select example">
                                                    <option selected>Selecione a Faixa etária</option>
                                                    <option value="Jovem">Jovem</option>
                                                    <option value="Adulto">Adulto</option>
                                                    <option value="Indeterminado">Indeterminado</option>
                                                </select>
                                                <InputError class="mt-2" :message="form.errors.faixa_etaria" />
                                            </div>

                                            <div class="col-lg-4 me-2">
                                                <InputLabel for="" value="Nº de indivíduos do registro" />
                                                <input id="" type="text" class="form-control"
                                                    v-model="form.n_individuos" autofocus
                                                    placeholder="Nº de indivíduos do registro" autocomplete="" />
                                                <InputError class="mt-2" :message="form.errors.n_individuos" />
                                            </div>
                                        </div>

                                        <div class="d-flex">
                                            <div class="col-lg-4 me-2">
                                                <InputLabel for="id_forma_registro" value="Forma Registro" />
                                                <select class="form-select" v-model="form.id_forma_registro"
                                                    @change="formaTipoRegistro()" aria-label="Default select example">
                                                    <option selected>Selecione a forma registro</option>
                                                    <option v-for="forma in formaRegistro" :value="forma.id">
                                                        {{ forma.nome }}
                                                    </option>
                                                </select>
                                                <InputError class="mt-2" :message="form.errors.id_forma_registro" />
                                            </div>

                                            <div class="col-lg-4 me-2">
                                                <InputLabel for="" value="Tipo de Registro" />
                                                <select class="form-select" :disabled="form.id_forma_registro == 3"
                                                    v-model="form.id_tipo_registro" @change="tipoDestinacaoRegistro()"
                                                    aria-label="Default select example">
                                                    <option selected>Selecione o Tipo de Registro</option>
                                                    <option v-for="tipo in tipoRegistro" :value="tipo.id">
                                                        {{ tipo.nome }}
                                                    </option>
                                                </select>
                                                <InputError class="mt-2" :message="form.errors.id_tipo_registro" />
                                            </div>

                                            <div class="col-lg-4 me-2">
                                                <InputLabel for="" value="Destinação" />
                                                <select class="form-select"
                                                    :disabled="form.id_forma_registro == 3 || form.id_forma_registro == 1"
                                                    v-model="form.id_destinacao_registro"
                                                    aria-label="Default select example">
                                                    <option selected>Selecione a Destinação</option>
                                                    <option v-for="destinacao in destinacaoRegistro" :value="destinacao.id">
                                                        {{ destinacao.nome }}
                                                    </option>
                                                </select>
                                                <InputError class="mt-2"
                                                    :message="form.errors.id_destinacao_registro" />
                                            </div>
                                        </div>

                                        <div class="d-flex">
                                            <div class="col-lg-12 me-2">
                                                <InputLabel for="latitude_soltura" value="Local de Soltura" />
                                                <div class="d-flex">
                                                    <div class="d-flex flex-column col-md-4 me-2">
                                                        <!-- v-if="soltura" -->
                                                        <div class="mb-3">
                                                            <InputLabel for="latitude_soltura"
                                                                value="Latitude de Soltura" />
                                                            <input id="latitude_soltura" type="text"
                                                                class="form-control" v-model="form.latitude_soltura"
                                                                autofocus placeholder="latitude de soltura"
                                                                autocomplete="" />
                                                            <InputError class="mt-2"
                                                                :message="form.errors.latitude_soltura" />
                                                        </div>
                                                    </div>
                                                    <div class="d-flex flex-column col-md-4 me-2">
                                                        <!-- v-if="soltura" -->
                                                        <div class="mb-3">
                                                            <InputLabel for="longitude_soltura"
                                                                value="Longitude de Soltura" />
                                                            <input id="longitude_soltura" type="text"
                                                                class="form-control" v-model="form.longitude_soltura"
                                                                autofocus placeholder="longitude de soltura"
                                                                autocomplete="" />
                                                            <InputError class="mt-2"
                                                                :message="form.errors.longitude_soltura" />
                                                        </div>
                                                    </div>
                                                    <div class="d-flex flex-column col-md-4"> <!-- v-if="local" -->
                                                        <div class="mb-3">
                                                            <InputLabel for="nome_local" value="Nome do local" />
                                                            <input id="nome_local" type="text" class="form-control"
                                                                v-model="form.nome_local" autofocus placeholder="Local"
                                                                autocomplete="" />
                                                            <InputError class="mt-2"
                                                                :message="form.errors.nome_local" />
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex"> <!-- v-if="formRegistro.id_forma_registro == 3" -->
                                            <div class="col-lg-4 me-2">
                                                <InputLabel for="coletado" value="Coletado" />
                                                <select class="form-select" v-model="form.coletado"
                                                    aria-label="Default select example">
                                                    <option selected>Selecione</option>
                                                    <option value="sim">Sim</option>
                                                    <option value="nao">Não</option>
                                                </select>
                                                <InputError class="mt-2" :message="form.errors.coletado" />
                                            </div>
                                            <div class="col-lg-4 me-2"> <!-- v-if="formRegistro.coletado == 'sim'" -->
                                                <InputLabel for="n_registro_tombamento"
                                                    value="N° tombamento ou Registro de entrada na coleção" />
                                                <input id="n_registro_tombamento" type="text" class="form-control"
                                                    v-model="form.n_registro_tombamento" autofocus
                                                    placeholder="N° tombamento ou Registro de entrada na coleção"
                                                    autocomplete="" />
                                                <InputError class="mt-2" :message="form.errors.n_registro_tombamento" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="heading-4">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#statusConservacaoEspecime" aria-expanded="false">
                                        Status de Conservação do Espécime
                                    </button>
                                </h2>
                                <div id="statusConservacaoEspecime" class="accordion-collapse collapse"
                                    data-bs-parent="#servico">
                                    <div class="accordion-body pt-0 card-body space-y-5">
                                        <div class="d-flex">
                                            <div class="col-lg-6 me-2">
                                                <InputLabel for="id_status_conservacao_federal" value="Federal" />
                                                <select class="form-select" v-model="form.id_status_conservacao_federal"
                                                    aria-label="Default select example">
                                                    <option selected>Selecione a Federal</option>
                                                    <option v-for="federal in statusConservacaoFederal"
                                                        :value="federal.id">
                                                        {{ federal.nome }} - {{ federal.sigla }}
                                                    </option>
                                                </select>
                                                <InputError class="mt-2"
                                                    :message="form.errors.id_status_conservacao_federal" />
                                            </div>

                                            <div class="col-lg-6 me-2">
                                                <InputLabel for="" value="IUCN" />
                                                <select class="form-select" v-model="form.id_status_conservacao_iucn"
                                                    aria-label="Default select example">
                                                    <option selected>Selecione a IUCN</option>
                                                    <option v-for="iucn in statusConservacaoIucn" :value="iucn.id">
                                                        {{ iucn.sigla }} - {{ iucn.nome }}
                                                    </option>
                                                </select>
                                                <InputError class="mt-2"
                                                    :message="form.errors.id_status_conservacao_iucn" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="heading-4">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#registroFotografico" aria-expanded="false">
                                        Registro Fotográfico
                                    </button>
                                </h2>
                                <div id="registroFotografico" class="accordion-collapse collapse"
                                    data-bs-parent="#servico">
                                    <div class="accordion-body pt-0 card-body space-y-5">
                                        <div class="d-flex">
                                            <div class="col-lg-6 me-2">
                                                <div>
                                                    <InputLabel for="" value="Buscar Arquivo (.jpg)" />
                                                    <input @input="form.documento = $event.target.files[0]" type="file"
                                                        class="form-control">
                                                </div>

                                                <div class="mt-2">
                                                    <button type="button" @click="salvarImagens" class="btn btn-success"
                                                        aria-label="Button" :disabled="form.processing">
                                                        Enviar
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
            <template #footer>
                <div class="mt-2">
                    <a @click="close" href="#" class="btn btn-danger me-2" aria-label="Button"
                        :disabled="form.processing">
                        Cancelar
                    </a>
                    <button type="submit" class="btn btn-success" aria-label="Button" :disabled="form.processing">
                        Salvar
                    </button>
                </div>
            </template>
        </Modal>
    </form>
</template>
