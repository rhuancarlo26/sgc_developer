<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import { ref, watch, computed } from "vue";
import { IconDoorExit, IconDeviceFloppy } from "@tabler/icons-vue";
import TabInformacoesGerais from "./TabInformacoesGerais.vue"
import TabValidacoes from "./TabValidacoes.vue"
import { useToast } from "vue-toastification";

const props = defineProps({
    modulo: { type: Object },
    tipos: { type: Array },
    contrato: [String, Number],
    produto: String,
    produtosDisponiveis: { type: Array, default: () => [] },
});

const form = useForm({
    nome: null,
    planilha_modelo: null,
    campos: [],
    produto_slug: null,
    produto_titulo: null,
    ...props.modulo
});

const toast = useToast();

const TabValidacoesRef = ref(null)
const salvarModulo = () => {

    if(!form.campos.length) {
        toast.error('Os campos para validação não foram preenchidos');
        return
    }

    if(TabValidacoesRef.value.validaCampos()) {
        toast.error('Preencha os campos obrigatórios da aba Validações');
        return
    }

    form.clearErrors()

    const url = props.modulo.id ? 'update' : 'store'
    const params = props.modulo.id
        ? [props.contrato, props.produto, props.modulo.id]
        : [props.contrato, props.produto]

    form.post(route(`sgc.contratada.produtos.modulos.configuracoes.${url}`, params), {
        preserveState: false
    });
}

</script>

<template>

    <Head title="Novo Módulo" />

    <AuthenticatedLayout>

        <template #header>
            <div class="w-100 d-flex justify-content-between align-items-center">
                <Breadcrumb :links="[
                    { route: route('sgc.contratada.produtos.modulos.configuracoes.index', [props.contrato, props.produto]), label: `Módulos` },
                    { route: '#', label: 'Novo Módulo' }
                ]" />
                <Link class="btn btn-info" :href="route('sgc.contratada.produtos.modulos.configuracoes.index', [props.contrato, props.produto])">
                    <IconDoorExit class="me-2" /> Voltar
                </Link>
            </div>
        </template>

        <div class="card mb-4">
            <div class="card">
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a href="#tabs-info-1" class="nav-link active" data-bs-toggle="tab" aria-selected="true"
                                role="tab"> Informações Gerais </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a href="#tabs-validacoes-1" class="nav-link" data-bs-toggle="tab" aria-selected="true"
                                role="tab"> Validações ({{form.campos.length}}) </a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <form @submit.prevent="salvarModulo()" :disabled="form.processing">
                        <div class="tab-content">
                            <div class="tab-pane active show" id="tabs-info-1" role="tabpanel">
                                <TabInformacoesGerais
                                    :form="form"
                                    :contrato="props.contrato"
                                    :produto="props.produto"
                                    :produtos-disponiveis="props.produtosDisponiveis"
                                />
                            </div>

                            <div class="tab-pane" id="tabs-validacoes-1" role="tabpanel">
                                <TabValidacoes
                                    ref="TabValidacoesRef"
                                    :form="form"
                                    :tipos="tipos"
                                    :contrato="props.contrato"
                                    :produto="props.produto"
                                />
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="d-flex justify-content-end">
                                <button class="btn btn-success" :disabled="form.processing">
                                    <IconDeviceFloppy class="me-2"/>
                                    {{ form.id ? 'Editar' : 'Salvar' }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
