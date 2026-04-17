<script setup>
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import { IconCamera, IconTrash, IconEye } from "@tabler/icons-vue";
import { ref } from "vue";
import { usePage } from "@inertiajs/vue3";

const props = defineProps({
    form: { type: Object }
});

const page = usePage()

const selecionarArquivo = (key, {target}) => {
    props.form.fotos[key].arquivo = target.files?.[0]

    delete props.form.fotos[key].nome_arquivo
}

const addFoto = () => {
    props.form.fotos.push({
        arquivo: null,
        latitude: null,
        longitude: null,
        descricao: null,
    })
}

const removerFoto = (key) => {
    props.form.fotos.splice(key, 1)
}

const validarCampos = () => {

    const erros = []
    props.form.fotos.forEach(item => {

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
            <h3 class="my-0">Fotos</h3>
            <button type="button" @click="addFoto" class="btn btn-light">
                <IconCamera class="me-2" /> Adicionar Foto
            </button>
        </div>

        <div class="card-body">

            <div v-if="!props.form.fotos.length" class="d-flex justify-content-center">
                <span>Nenhuma foto adicionada</span>
            </div>
            
            <div v-else class="d-flex flex-column gap-3">
                <div class="row" v-for="(f, key) in props.form.fotos" :key="key">
                    <div class="col-lg-3">
                        <InputLabel :for="`upload_foto_${key}`">Arquivo</InputLabel>
                        <input type="file" :id="`upload_foto_${key}`" @change="selecionarArquivo(key, $event)" class="form-control"
                            accept="image/*" :class="f.valida_arquivo ? 'border-danger' : ''"/>
                        <small v-if="f.nome_arquivo">
                            Arquivo original: <strong>{{f.nome_arquivo}}</strong>
                            <a :href="`${page.props.app_url}/storage/${f.caminho_arquivo}`" 
                                title="Ver Foto" class="btn btn-sm btn-ligth ms-1 border-0" target="_blank">
                                <IconEye class="text-warning" />
                            </a>
                        </small>
                    </div>
                    <div class="col-lg-3">
                        <InputLabel for="latitude">Latitude</InputLabel>
                        <input type="number" v-model="f.latitude" id="latitude" class="form-control" step="any" :class="f.valida_latitude ? 'border-danger' : ''"/>
                    </div>
                    <div class="col-lg-3">
                        <InputLabel for="longitude">Longitude</InputLabel>
                        <input type="number" v-model="f.longitude" id="longitude" class="form-control" step="any" :class="f.valida_longitude ? 'border-danger' : ''"/>
                    </div>
                    <div class="col-lg-3 d-flex gap-2">
                        <div class="flex-grow-1">
                            <InputLabel for="descricao">Descrição</InputLabel>
                            <input type="text" v-model="f.descricao" id="descricao" class="form-control" :class="f.valida_descricao ? 'border-danger' : ''"/>
                        </div>
                        <div class="d-flex gap-2 mb-2" :class="f.nome_arquivo ? 'align-self-center' : 'align-self-end'">
                            <button type="button" @click="removerFoto(key)" class="btn btn-sm btn-danger">
                                <IconTrash />
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
