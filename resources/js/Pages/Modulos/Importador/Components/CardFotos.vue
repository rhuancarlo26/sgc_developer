<script setup>
import InputLabel from "@/Components/InputLabel.vue";
import { IconCamera, IconTrash, IconEye } from "@tabler/icons-vue";
import { ref } from "vue";
import { usePage } from "@inertiajs/vue3";

const props = defineProps({
    form: { type: Object }
});

const page = usePage();

const inputFotosMultiplasRef = ref(null);

const abrirSeletorFotos = () => {
    inputFotosMultiplasRef.value?.click();
};

const campoVazio = (valor) => {
    return valor === "" || valor === null || valor === undefined;
};

const valorPadrao = (campo, key) => {
    if (key === 0) {
        return "";
    }

    const primeiraFoto = props.form.fotos?.[0];

    if (!primeiraFoto || campoVazio(primeiraFoto[campo])) {
        return "";
    }

    return primeiraFoto[campo];
};

const aplicarValoresPadraoNasFotos = () => {
    const primeiraFoto = props.form.fotos?.[0];

    if (!primeiraFoto) {
        return;
    }

    props.form.fotos.forEach((foto, key) => {
        if (key === 0) {
            return;
        }

        if (campoVazio(foto.latitude) && !campoVazio(primeiraFoto.latitude)) {
            foto.latitude = primeiraFoto.latitude;
        }

        if (campoVazio(foto.longitude) && !campoVazio(primeiraFoto.longitude)) {
            foto.longitude = primeiraFoto.longitude;
        }

        if (campoVazio(foto.descricao) && !campoVazio(primeiraFoto.descricao)) {
            foto.descricao = primeiraFoto.descricao;
        }
    });
};

const selecionarMultiplosArquivos = ({ target }) => {
    const arquivos = Array.from(target.files || []);

    if (!arquivos.length) {
        return;
    }

    arquivos.forEach((arquivo) => {
        props.form.fotos.push({
            arquivo,
            latitude: null,
            longitude: null,
            descricao: null,
        });
    });

    target.value = "";
};

const selecionarArquivo = (key, { target }) => {
    const arquivo = target.files?.[0];

    if (!arquivo) {
        return;
    }

    props.form.fotos[key].arquivo = arquivo;

    delete props.form.fotos[key].nome_arquivo;
    delete props.form.fotos[key].caminho_arquivo;

    target.value = "";
};

const addFoto = () => {
    props.form.fotos.push({
        arquivo: null,
        latitude: null,
        longitude: null,
        descricao: null,
    });
};

const removerFoto = (key) => {
    props.form.fotos.splice(key, 1);
};

const validarCampos = () => {
    aplicarValoresPadraoNasFotos();

    const erros = [];

    props.form.fotos.forEach((item) => {
        delete item.valida_arquivo;
        delete item.valida_latitude;
        delete item.valida_longitude;
        delete item.valida_descricao;

        if (!item.arquivo && !item.nome_arquivo) {
            item.valida_arquivo = true;
            erros.push(true);
        }

        if (campoVazio(item.latitude)) {
            item.valida_latitude = true;
            erros.push(true);
        }

        if (campoVazio(item.longitude)) {
            item.valida_longitude = true;
            erros.push(true);
        }

        if (campoVazio(item.descricao)) {
            item.valida_descricao = true;
            erros.push(true);
        }
    });

    return erros.length;
};

defineExpose({ validarCampos });
</script>

<template>
    <div class="card">
        <div class="card-header justify-content-between">
            <h3 class="my-0">Fotos</h3>

            <div class="d-flex gap-2">
                <input ref="inputFotosMultiplasRef" type="file" class="d-none" accept="image/*" multiple
                    @change="selecionarMultiplosArquivos" />

                <button type="button" @click="abrirSeletorFotos" class="btn btn-light"
                    :disabled="[2, 4].includes(form.status)">
                    <IconCamera class="me-2" />
                    Adicionar Fotos
                </button>

                <button type="button" @click="addFoto" class="btn btn-outline-secondary"
                    :disabled="[2, 4].includes(form.status)">
                    Adicionar Linha
                </button>
            </div>
        </div>

        <div class="card-body">
            <div v-if="!props.form.fotos.length" class="d-flex justify-content-center">
                <span>Nenhuma foto adicionada</span>
            </div>

            <div v-else class="d-flex flex-column gap-3">
                <div class="row" v-for="(f, key) in props.form.fotos" :key="key">
                    <div class="col-lg-3">
                        <InputLabel :for="`upload_foto_${key}`">Arquivo</InputLabel>

                        <input type="file" :id="`upload_foto_${key}`" @change="selecionarArquivo(key, $event)"
                            class="form-control" accept="image/*" :class="f.valida_arquivo ? 'border-danger' : ''"
                            :disabled="[2, 4].includes(form.status)" />

                        <small v-if="f.arquivo">
                            Arquivo selecionado:
                            <strong>{{ f.arquivo.name }}</strong>
                        </small>

                        <small v-else-if="f.nome_arquivo">
                            Arquivo original:
                            <strong>{{ f.nome_arquivo }}</strong>

                            <a :href="`${page.props.app_url}/storage/${String(f.caminho_arquivo).replace(/\\/g, '/')}`"
                                title="Ver Foto" class="btn btn-sm btn-light ms-1 border-0" target="_blank">
                                <IconEye class="text-warning" />
                            </a>
                        </small>
                    </div>

                    <div class="col-lg-3">
                        <InputLabel :for="`latitude_${key}`">Latitude</InputLabel>

                        <input type="number" v-model="f.latitude" :id="`latitude_${key}`" class="form-control"
                            step="any" :placeholder="valorPadrao('latitude', key)"
                            :class="f.valida_latitude ? 'border-danger' : ''"
                            :disabled="[2, 4].includes(form.status)" />

                        <small v-if="key > 0 && campoVazio(f.latitude) && valorPadrao('latitude', key)"
                            class="text-muted">
                            Usará o valor da primeira foto.
                        </small>
                    </div>

                    <div class="col-lg-3">
                        <InputLabel :for="`longitude_${key}`">Longitude</InputLabel>

                        <input type="number" v-model="f.longitude" :id="`longitude_${key}`" class="form-control"
                            step="any" :placeholder="valorPadrao('longitude', key)"
                            :class="f.valida_longitude ? 'border-danger' : ''"
                            :disabled="[2, 4].includes(form.status)" />

                        <small v-if="key > 0 && campoVazio(f.longitude) && valorPadrao('longitude', key)"
                            class="text-muted">
                            Usará o valor da primeira foto.
                        </small>
                    </div>

                    <div class="col-lg-3 d-flex gap-2">
                        <div class="flex-grow-1">
                            <InputLabel :for="`descricao_${key}`">Descrição</InputLabel>

                            <input type="text" v-model="f.descricao" :id="`descricao_${key}`" class="form-control"
                                :placeholder="valorPadrao('descricao', key)"
                                :class="f.valida_descricao ? 'border-danger' : ''"
                                :disabled="[2, 4].includes(form.status)" />

                            <small v-if="key > 0 && campoVazio(f.descricao) && valorPadrao('descricao', key)"
                                class="text-muted">
                                Usará o valor da primeira foto.
                            </small>
                        </div>

                        <div v-if="[1, 3, null].includes(form.status)" class="d-flex gap-2 mb-2"
                            :class="f.nome_arquivo || f.arquivo ? 'align-self-center' : 'align-self-end'">
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
