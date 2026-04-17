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
    <div class="card">
        <div class="card-header justify-content-between">
            <h3 class="my-0">Anexos</h3>
            <button type="button" @click="addAnexo" class="btn btn-light">
                <IconPaperclip class="me-2" /> Adicionar Anexo
            </button>
        </div>

        <div class="card-body">

            <div v-if="!props.form.anexos.length" class="d-flex justify-content-center">
                <span>Nenhum anexo adicionado</span>
            </div>
            
            <div v-else class="d-flex flex-column gap-3">
                <div class="row" v-for="(a, key) in props.form.anexos" :key="key">
                    <div class="col-12 d-flex gap-2">
                        <div class="flex-grow-1">
                            <InputLabel :for="`upload_anexo_${key}`">Arquivo</InputLabel>
                            <input type="file" :id="`upload_anexo_${key}`" @change="selecionarArquivo(key, $event)" class="form-control"
                                :class="a.valida_arquivo ? 'border-danger' : ''"/>
                            <small v-if="a.nome_arquivo">
                                Arquivo original: <strong>{{a.nome_arquivo}}</strong>
                                <a :href="`${page.props.app_url}/storage/${a.caminho_arquivo}`" 
                                    title="Ver Foto" class="btn btn-sm btn-ligth ms-1 border-0" target="_blank">
                                    <IconDownload class="text-info" />
                                </a>
                            </small>
                        </div>
                        <div class="d-flex gap-2 mb-2" :class="a.nome_arquivo ? 'align-self-center' : 'align-self-end'">
                            <!-- <a v-if="a.nome_arquivo" :href="`${page.props.app_url}/storage/${a.caminho_arquivo}`" 
                                title="Ver Foto" class="btn btn-sm btn-info" target="_blank" download>
                                <IconDownload />
                            </a> -->
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
