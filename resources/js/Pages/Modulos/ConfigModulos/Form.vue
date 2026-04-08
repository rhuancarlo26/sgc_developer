<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import { ref, watch, computed } from "vue";
import { IconDoorExit, IconDeviceFloppy } from "@tabler/icons-vue";
import TabInformacoesGerais from "./TabInformacoesGerais.vue"
import TabValidacoes from "./TabValidacoes.vue"

const props = defineProps({
    modulo: { type: Object },
});

const form = useForm({
    nome: null,
    planilha_modelo: null,
    campos: [],
    ...props.modulo
});

const numeroValidacoes = ref(0)


const salvarModulo = () => {
    
    console.log(form)

    // form.transform((data) => Object.assign({}, data))

    // const url = props.contrato.id ? 'atualizar' : 'store'

    // form.post(route('contratos.gestao.' + url, props.contrato.id), {
    //     onSuccess: () => Object.assign(form, props.contrato)
    // });
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
                                <TabInformacoesGerais :form="form" />
                            </div>

                            <div class="tab-pane" id="tabs-validacoes-1" role="tabpanel">
                                <TabValidacoes :form="form" />
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="d-flex justify-content-end">
                                <button @click="salvarContrato()" type="button" class="btn btn-success" :disabled="form.processing">
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
