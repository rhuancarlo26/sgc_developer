<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import Navbar from "../../Navbar.vue";
import {Head, Link, useForm} from "@inertiajs/vue3";
import TabDadosGerais from "./TabDadosGerais.vue";
import TabIdentificacaoEspecime from "./TabIdentificacaoEspecime.vue";
import TabDadosEspecime from "./TabDadosEspecime.vue";
import TabConservacaoEspecime from "./TabConservacaoEspecime.vue";
import TabRegistroFotografico from "./TabRegistroFotografico.vue";
import TabObservacao from "./TabObservacao.vue";

const props = defineProps({
    contrato: {type: Object},
    servico: {type: Object},
    registro: {type: Object},
    campanhas: {type: Array},
    passagens: {type: Array},
    status_conservacoes: {type: Array}
});

const form = useForm({
    id: null,
    id_servico: null,
    nome_id: null,
    id_campanha: null,
    id_grupo: null,
    id_passagem: null,
    km: null,
    forma: null,
    latitude: null,
    longitude: null,
    tipo: null,
    classe: null,
    ordem: null,
    familia: null,
    genero: null,
    especie: null,
    nome_comum: null,
    sexo: null,
    faixa_etaria: null,
    n_individuos: null,
    data_registro: null,
    hora_registro: null,
    id_status_conservacao_federal: null,
    id_status_conservacao_iucn: null,
    obs: null,
    ...props.registro,
    arquivo: null
});

const salvarRegistro = () => {
    form.id_servico = props.servico.id;

    const url = form.id ? 'update' : 'store';

    form.post(route('contratos.contratada.servicos.passagem_fauna.execucao.registro.' + url, {
        contrato: props.contrato.id,
        servico: props.servico.id
    }), {
        onSuccess: () => form.id = props.registro.id
    });
}

const salvarArquivo = () => {
    form.post(route('contratos.contratada.servicos.passagem_fauna.execucao.registro.store_arquivo', {
        contrato: props.contrato.id,
        servico: props.servico.id
    }), {
        onSuccess: () => form.imagem = props.registro.imagem
    });
}

const excluirArquivo = () => {
    form.delete(route('contratos.contratada.servicos.passagem_fauna.execucao.registro.delete_imagem', {
        contrato: props.contrato.id,
        servico: props.servico.id,
        imagem: form.imagem.id
    }), {
        onSuccess: () => form.imagem = props.registro.imagem
    });
}

</script>
<template>

    <Head :title="`${contrato.contratada.slice(0, 10)}...`"/>

    <AuthenticatedLayout>

        <template #header>
            <div class="w-100 d-flex justify-content-between">
                <Breadcrumb class="align-self-center" :links="[
          { route: route('contratos.gestao.listagem', contrato.tipo_contrato), label: `Gestão de Contratos` },
          { route: '#', label: contrato.contratada }
        ]
          "/>
                <Link class="btn btn-dark"
                      :href="route('contratos.contratada.servicos.passagem_fauna.execucao.registro.index', { contrato: contrato.id, servico: servico.id })">
                    Voltar
                </Link>
            </div>
        </template>

        <Navbar :contrato="contrato" :servico="servico">
            <template #body>
                <div class="card">
                    <div class="card-header">
                        <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <a href="#tab-dados-gerais" class="nav-link active" data-bs-toggle="tab"
                                   aria-selected="true"
                                   role="tab">Dados gerais</a>
                            </li>
                            <template v-if="form.id">
                                <li class="nav-item" role="presentation">
                                    <a href="#tab-identificacao-especime" class="nav-link" data-bs-toggle="tab"
                                       aria-selected="false"
                                       tabindex="-1"
                                       role="tab">Identificação do espécime</a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a href="#tab-dados-especime" class="nav-link" data-bs-toggle="tab"
                                       aria-selected="false"
                                       tabindex="-1"
                                       role="tab">Dados do espécime</a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a href="#tab-conservacao-especime" class="nav-link" data-bs-toggle="tab"
                                       aria-selected="false"
                                       tabindex="-1"
                                       role="tab">Status de conservação do espécime</a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a href="#tab-registro-fotografico" class="nav-link" data-bs-toggle="tab"
                                       aria-selected="false"
                                       tabindex="-1"
                                       role="tab">Registro fotográfico</a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a href="#tab-observacao" class="nav-link" data-bs-toggle="tab"
                                       aria-selected="false"
                                       tabindex="-1"
                                       role="tab">Observações</a>
                                </li>
                            </template>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            <div class="tab-pane active show" id="tab-dados-gerais" role="tabpanel">
                                <TabDadosGerais @salvar-registro="salvarRegistro()" :form="form" :campanhas="campanhas"
                                                :passagens="passagens"/>
                            </div>
                            <template v-if="form.id">
                                <div class="tab-pane" id="tab-identificacao-especime" role="tabpanel">
                                    <TabIdentificacaoEspecime @salvar-registro="salvarRegistro()" :form="form"/>
                                </div>
                                <div class="tab-pane" id="tab-dados-especime" role="tabpanel">
                                    <TabDadosEspecime @salvar-registro="salvarRegistro()" :form="form"/>
                                </div>
                                <div class="tab-pane" id="tab-conservacao-especime" role="tabpanel">
                                    <TabConservacaoEspecime @salvar-registro="salvarRegistro()" :form="form"
                                                            :status_conservacoes="status_conservacoes"/>
                                </div>
                                <div class="tab-pane" id="tab-registro-fotografico" role="tabpanel">
                                    <TabRegistroFotografico @salvar-arquivo="salvarArquivo()"
                                                            @excluir-registro="excluirArquivo()" :contrato="contrato"
                                                            :servico="servico" :form="form"/>
                                </div>
                                <div class="tab-pane" id="tab-observacao" role="tabpanel">
                                    <TabObservacao @salvar-registro="salvarRegistro()" :form="form"/>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </template>
        </Navbar>

    </AuthenticatedLayout>
</template>
