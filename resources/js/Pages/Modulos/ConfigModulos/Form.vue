<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import { ref } from "vue";
import { IconDoorExit, IconDeviceFloppy } from "@tabler/icons-vue";
import TabInformacoesGerais from "./TabInformacoesGerais.vue"
import TabValidacoes from "./TabValidacoes.vue"
import { useToast } from "vue-toastification";

const props = defineProps({
    modulo: {
        type: Object,
        default: () => ({}),
    },

    tipos: {
        type: Array,
        default: () => [],
    },

    contratos: {
        type: Array,
        default: () => [],
    },
});

const form = useForm({
    nome: null,
    pmqa: 0,
    contrato_id: null,
    planilha_modelo: null,
    campos: [],
    ...props.modulo,
    pmqa: props.modulo?.pmqa ? 1 : 0,
    contrato_id: props.modulo?.contrato_id ?? null,
});

const toast = useToast();

const TabValidacoesRef = ref(null)

const salvarModulo = () => {
    if (!form.campos.length) {
        toast.error('Os campos para validação não foram preenchidos')
        return
    }

    const possuiErroValidacao =
        TabValidacoesRef.value?.validaCampos?.() ?? false

    if (possuiErroValidacao) {
        toast.error(
            'Existem campos obrigatórios não preenchidos na aba Validações'
        )
        return
    }

    form.clearErrors()

    const rota = form.id
        ? route('modulos.config-modulos.update', form.id)
        : route('modulos.config-modulos.store')

    form.post(rota, {
        preserveState: false,

        onError: () => {
            toast.error('Não foi possível salvar o módulo. Verifique os campos preenchidos.')
        },
    })
}
</script>

<template>

    <Head title="Novo Módulo" />

    <AuthenticatedLayout>

        <template #header>
            <div class="w-100 d-flex justify-content-between align-items-center">
                <Breadcrumb :links="[
                    { route: route('modulos.config-modulos.index'), label: `Módulos` },
                    { route: '#', label: 'Novo Módulo' }
                ]" />
                <Link class="btn btn-info" :href="route('modulos.config-modulos.index')">
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
                                role="tab">
                                Informações Gerais </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a href="#tabs-validacoes-1" class="nav-link" data-bs-toggle="tab" aria-selected="true"
                                role="tab">
                                Validações ({{ form.campos.length }}) </a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <form @submit.prevent="salvarModulo" novalidate>
                        <div class="tab-content">
                            <div class="tab-pane active show" id="tabs-info-1" role="tabpanel">
                                <TabInformacoesGerais :form="form" :contratos="contratos" />
                            </div>

                            <div class="tab-pane" id="tabs-validacoes-1" role="tabpanel">
                                <TabValidacoes ref="TabValidacoesRef" :form="form" :tipos="tipos" />
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-success" :disabled="form.processing">
                                    <IconDeviceFloppy class="me-2" />
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
