<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import { ref, watch, computed } from "vue";
import { IconDoorExit, IconDeviceFloppy } from "@tabler/icons-vue";

import CardInformacoesGerais from "./Components/CardInformacoesGerais.vue"
import CardPareceres from "./Components/CardPareceres.vue"
import CardFotos from "./Components/CardFotos.vue"
import CardAnexos from "./Components/CardAnexos.vue"

const props = defineProps({
    moduloImportador: { type: Object },
    modulos: { type: Array },
    contratos: { type: Array },
});

const labelBreadcrumb = computed(() => props.moduloImportador.id ? 'Importação' : 'Nova Importação')

const form = useForm({
    modulo_id: null,
    mes_ano_referencia: null,
    campanha: null,
    contrato_id: null,
    arquivo: null,
    parecer_tecnico: null,
    parecer_analise: null,
    fotos: [],
    anexos: [],
    ...props.moduloImportador
});

const CardFotosRef = ref(null)
const CardAnexosRef = ref(null)

const importar = () => {

    if(form.fotos.length && CardFotosRef.value.validarCampos()) {
        return
    }

    if(form.anexos.length && CardAnexosRef.value.validarCampos()) {
        return
    }

    const method = form.id ? 'update' : 'store'
    form.post(route(`modulos.importador.${method}`, [form.id]))
}

</script>

<template>

    <Head title="Nova Importação" />

    <AuthenticatedLayout>

        <template #header>
            <div class="w-100 d-flex justify-content-between align-items-center">
                <Breadcrumb :links="[
                    { route: route('modulos.importador.index'), label: `Importadores` },
                    { route: '#', label: labelBreadcrumb }
                ]" />
                <Link class="btn btn-info" :href="route('modulos.importador.index')">
                    <IconDoorExit class="me-2" /> Voltar
                </Link>
            </div>
        </template>

        <form @submit.prevent="importar()" :disabled="form.processing">
            <div class="d-flex flex-column">
                <div class="d-flex flex-column gap-4 flex-grow-1 mb-4">
                    <CardInformacoesGerais :form="form" :modulos="modulos" :contratos="contratos" />

                    <CardPareceres :form="form" />

                    <CardFotos :form="form" ref="CardFotosRef" />

                    <CardAnexos :form="form" ref="CardAnexosRef" />
                </div>
                
                <div class="card-body">
                    <div class="d-flex justify-content-end">
                        <button class="btn btn-success" :disabled="form.processing">
                            <IconDeviceFloppy class="me-2"/>
                            Salvar Rascunho
                        </button>
                    </div>
                </div>
            </div>
        </form>

    </AuthenticatedLayout>
</template>
