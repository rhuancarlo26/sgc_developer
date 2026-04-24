<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, router, useForm } from "@inertiajs/vue3";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import { ref, watch, computed } from "vue";
import { IconDoorExit, IconDeviceFloppy, IconSend, IconCircleX, IconCircleCheck } from "@tabler/icons-vue";
import Swal from "sweetalert2";

import CardInformacoesGerais from "./Components/CardInformacoesGerais.vue"
import CardPareceres from "./Components/CardPareceres.vue"
import CardFotos from "./Components/CardFotos.vue"
import CardAnexos from "./Components/CardAnexos.vue"
import CardDadosPlanilha from "./Components/CardDadosPlanilha.vue"

import { badgeStatus } from '@/Utils/ImportadorUtils';

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
    status: null,
    arquivo: null,
    parecer_tecnico: null,
    parecer_analise: null,
    fotos: [],
    anexos: [],
    enviar_analise: null,
    update_modulo: null,
    ...props.moduloImportador
});

const CardFotosRef = ref(null)
const CardAnexosRef = ref(null)

const importar = async (enviarAnalise = false) => {

    if(form.fotos.length && CardFotosRef.value.validarCampos()) {
        return
    }

    if(form.anexos.length && CardAnexosRef.value.validarCampos()) {
        return
    }

    if(form.id && form.modulo_id != props.moduloImportador.modulo_id) {
        await Swal.fire({
                title: 'Tem certeza?',
                text: 'O módulo foi alterado, se prosseguir irá excluir todos os dados das planilhas importadas!',
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: 'Sim, Continuar',
                cancelButtonText: 'Cancelar'
            }).then(result => {

                if(result.isConfirmed) {
                    form.update_modulo = true
                }
            })
    }    

    form.enviar_analise = enviarAnalise

    const method = form.id ? 'update' : 'store'
    form.post(route(`modulos.importador.${method}`, [form.id]))
}

const enviarAnalise = () => {

    importar(true)

    // form.post(route('modulos.importador.enviarAnalise', [form.id]), {
    //     preserveScroll: true
    // })
}

const aprovReprovImportacao = (status) => {
    form.post(route('modulos.importador.aprovReprov', [form.id, status]), {
        preserveScroll: true
    })
}

</script>

<template>

    <Head :title="form.id ? 'Importação' : 'Nova Importação'" />

    <AuthenticatedLayout>

        <template #header>
            <div class="w-100 d-flex justify-content-between align-items-center">
                <div class="d-flex gap-3 align-items-center">
                    <Breadcrumb :links="[
                        { route: route('modulos.importador.index'), label: `Importadores` },
                        { route: '#', label: labelBreadcrumb }
                    ]" />
                    <span v-if="form.id" class="badge" :class="badgeStatus(form.status)">{{ form.status_formatado }}</span>
                </div>
                <Link class="btn btn-info" :href="route('modulos.importador.index')">
                    <IconDoorExit class="me-2" /> Voltar
                </Link>
            </div>
        </template>

        <form @submit.prevent="importar()" :disabled="form.processing">
            <div class="d-flex flex-column">
                <div class="d-flex flex-column gap-4 flex-grow-1 mb-4">
                    <CardInformacoesGerais :form="form" :modulos="modulos" :contratos="contratos" />

                    <CardDadosPlanilha v-if="form.id" :form="form" />

                    <CardPareceres :form="form" />

                    <CardFotos :form="form" ref="CardFotosRef" />

                    <CardAnexos :form="form" ref="CardAnexosRef" />
                </div>
                
                <div class="card-body">
                    <div class="d-flex justify-content-end gap-2">
                        <template v-if="[1, 3, null].includes(form.status)">
                            <button class="btn btn-light" :disabled="form.processing">
                                <IconDeviceFloppy class="me-2"/>
                                Salvar Rascunho
                            </button>
                            <button @click="enviarAnalise" type="button" class="btn btn-primary">
                                <IconSend class="me-2"/>
                                Enviar para Análise
                            </button>
                        </template>
                        <template v-else-if="[2].includes(form.status)">
                            <button type="button" @click="aprovReprovImportacao(3)" class="btn btn-danger">
                                <IconCircleX class="me-2"/>
                                Reprovar
                            </button>
                            <button type="button" @click="aprovReprovImportacao(4)" class="btn btn-success">
                                <IconCircleCheck class="me-2"/>
                                Aprovar
                            </button>
                        </template>
                    </div>
                </div>
            </div>
        </form>

    </AuthenticatedLayout>
</template>
