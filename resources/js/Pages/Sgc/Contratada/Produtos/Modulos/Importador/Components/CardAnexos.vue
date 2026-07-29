<script setup>
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import { IconPaperclip, IconTrash, IconDownload } from "@tabler/icons-vue";
import { ref } from "vue";
import { usePage } from "@inertiajs/vue3";

const props = defineProps({
    form: { type: Object }
});

const page = usePage()

const getAnexoUrl = (anexo) => {
    if (anexo?.url) {
        return anexo.url;
    }

    if (anexo?.caminho_arquivo) {
        return `${page.props.app_url}/storage/${String(anexo.caminho_arquivo).replace(/\\/g, '/')}`;
    }

    return null;
}

const selecionarArquivo = (key, {target}) => {
    props.form.anexos[key].arquivo = target.files?.[0]

    delete props.form.anexos[key].nome_arquivo
}

const addAnexo = () => {
    props.form.anexos.push({arquivo: null})
}

const removerAnexo = (key) => {
    props.form.anexos.splice(key, 1)
}

const validarCampos = () => {

    const erros = []

    props.form.anexos.forEach(item => {

        const keys = Object.keys(item)

        keys.forEach(key => {
            delete item[`valida_${key}`]

            if(item[key] === '' || item[key] === null) {
                item[`valida_${key}`] = true
                erros.push(true)
            }
        })
    })

    return erros.length
}

defineExpose({ validarCampos })

</script>
<template>
    <div class="card w-100">
        <div class="card-header justify-content-between">
            <h3 class="my-0">Anexos</h3>
            <button type="button" @click="addAnexo" class="btn btn-light" :disabled="[2, 4].includes(form.status)">
                <IconPaperclip class="me-2" /> Adicionar Anexo
            </button>
        </div>

        <div class="card-body p-4">

            <div v-if="!props.form.anexos.length" class="d-flex justify-content-center py-4">
                <span>Nenhum anexo adicionado</span>
            </div>
            
            <div v-else class="d-flex flex-column gap-4">
                <div class="row" v-for="(a, key) in props.form.anexos" :key="key">
                    <div class="col-12 d-flex gap-2">
                        <div class="flex-grow-1">
                            <InputLabel :for="`upload_anexo_${key}`">Arquivo</InputLabel>
                            <input type="file" :id="`upload_anexo_${key}`" @change="selecionarArquivo(key, $event)" class="form-control"
                                :class="a.valida_arquivo ? 'border-danger' : ''" :disabled="[2, 4].includes(form.status)"/>
                            <small v-if="a.nome_arquivo">
                                Arquivo original: <strong>{{a.nome_arquivo}}</strong>
                                <a v-if="getAnexoUrl(a)" :href="getAnexoUrl(a)" 
                                    title="Ver Anexo" class="btn btn-sm btn-ligth ms-1 border-0" target="_blank" rel="noopener">
                                    <IconDownload class="text-info" />
                                </a>
                            </small>
                        </div>
                        <div v-if="[1, 3, null].includes(form.status)" class="d-flex gap-2 mb-2" :class="a.nome_arquivo ? 'align-self-center' : 'align-self-end'">
                            <button type="button" @click="removerAnexo(key)" class="btn btn-sm btn-danger">
                                <IconTrash />
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
