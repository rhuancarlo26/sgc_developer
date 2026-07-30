<script setup>
import InputLabel from "@/Components/InputLabel.vue";
import { IconCamera, IconTrash, IconEye, IconDatabase } from "@tabler/icons-vue";
import Modal from "@/Components/Modal.vue";
import { ref } from "vue";
import exifr from "exifr";
import { usePage } from "@inertiajs/vue3";

const props = defineProps({
    form: { type: Object }
});

const page = usePage();

const getFotoUrl = (foto) => {
    if (foto?.url) {
        return foto.url;
    }

    if (foto?.caminho_arquivo) {
        return `${page.props.app_url}/storage/${String(foto.caminho_arquivo).replace(/\\/g, '/')}`;
    }

    return null;
};

const inputFotosMultiplasRef = ref(null);

const modalMetadadosRef = ref(null);
const fotoMetadadosSelecionada = ref(null);

const tituloModalMetadados = ref("Metadados da Foto");

const normalizarMetadados = (metadados) => {
    if (!metadados) {
        return null;
    }

    if (typeof metadados === "string") {
        try {
            return JSON.parse(metadados);
        } catch (e) {
            return metadados;
        }
    }

    return metadados;
};

const temMetadados = (foto) => {
    const metadados = normalizarMetadados(foto?.metadados);

    if (!metadados) {
        return false;
    }

    if (typeof metadados === "object") {
        return Object.keys(metadados).length > 0;
    }

    return String(metadados).trim() !== "";
};

const formatarValorMetadado = (valor) => {
    if (valor === null || valor === undefined || valor === "") {
        return "-";
    }

    if (typeof valor === "object") {
        return JSON.stringify(valor);
    }

    return String(valor);
};

const montarLinhasMetadados = (dados, prefixo = "") => {
    const metadados = normalizarMetadados(dados);

    if (!metadados || typeof metadados !== "object") {
        return [];
    }

    const linhas = [];

    Object.entries(metadados).forEach(([chave, valor]) => {
        const nomeCampo = prefixo ? `${prefixo}.${chave}` : chave;

        if (valor && typeof valor === "object" && !Array.isArray(valor)) {
            linhas.push(...montarLinhasMetadados(valor, nomeCampo));
            return;
        }

        linhas.push({
            campo: nomeCampo,
            valor: formatarValorMetadado(valor),
        });
    });

    return linhas;
};

const abrirModalMetadados = (foto) => {
    fotoMetadadosSelecionada.value = foto;
    tituloModalMetadados.value = `Metadados - ${foto.nome_arquivo ?? foto.arquivo?.name ?? "Foto"}`;

    modalMetadadosRef.value?.getBsModal().show();
};

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

const formatarDataExif = (valor) => {
    if (!valor) {
        return null;
    }

    if (valor instanceof Date && !Number.isNaN(valor.getTime())) {
        const ano = valor.getFullYear();
        const mes = String(valor.getMonth() + 1).padStart(2, "0");
        const dia = String(valor.getDate()).padStart(2, "0");
        const hora = String(valor.getHours()).padStart(2, "0");
        const minuto = String(valor.getMinutes()).padStart(2, "0");
        const segundo = String(valor.getSeconds()).padStart(2, "0");

        return `${ano}-${mes}-${dia} ${hora}:${minuto}:${segundo}`;
    }

    return String(valor).replace(/^(\d{4}):(\d{2}):(\d{2})/, "$1-$2-$3");
};

const extrairDadosDaImagem = async (arquivo) => {
    try {
        const metadados = await exifr.parse(arquivo, [
            "latitude",
            "longitude",
            "DateTimeOriginal",
            "DateTimeDigitized",
            "DateTime",
        ]);

        const latitude = metadados?.latitude ?? null;
        const longitude = metadados?.longitude ?? null;

        const dataCaptura = formatarDataExif(
            metadados?.DateTimeOriginal
            ?? metadados?.DateTimeDigitized
            ?? metadados?.DateTime
            ?? null
        );

        return {
            latitude,
            longitude,
            data_captura: dataCaptura,
            possuiCoordenadas: !!latitude && !!longitude,
        };
    } catch (error) {
        return {
            latitude: null,
            longitude: null,
            data_captura: null,
            possuiCoordenadas: false,
        };
    }
};

const selecionarMultiplosArquivos = async ({ target }) => {
    const arquivos = Array.from(target.files || []);

    if (!arquivos.length) {
        return;
    }

    for (const arquivo of arquivos) {
        const dadosImagem = await extrairDadosDaImagem(arquivo);

        props.form.fotos.push({
            arquivo,
            latitude: dadosImagem.latitude,
            longitude: dadosImagem.longitude,
            data_captura: dadosImagem.data_captura,
            descricao: null,

            tentouExtrairCoordenadas: true,
            possuiCoordenadasExif: dadosImagem.possuiCoordenadas,
        });
    }

    target.value = "";
};

const selecionarArquivo = async (key, { target }) => {
    const arquivo = target.files?.[0];

    if (!arquivo) {
        return;
    }

    const dadosImagem = await extrairDadosDaImagem(arquivo);

    props.form.fotos[key].arquivo = arquivo;
    props.form.fotos[key].latitude = dadosImagem.latitude;
    props.form.fotos[key].longitude = dadosImagem.longitude;
    props.form.fotos[key].data_captura = dadosImagem.data_captura;
    props.form.fotos[key].tentouExtrairCoordenadas = true;
    props.form.fotos[key].possuiCoordenadasExif = dadosImagem.possuiCoordenadas;

    delete props.form.fotos[key].nome_arquivo;
    delete props.form.fotos[key].caminho_arquivo;

    target.value = "";
};

const addFoto = () => {
    props.form.fotos.push({
        arquivo: null,
        latitude: null,
        longitude: null,
        data_captura: null,
        descricao: null,
    });
};

const removerFoto = (key) => {
    props.form.fotos.splice(key, 1);
};

const formatarDataHoraFoto = (valor) => {
    if (!valor) {
        return "";
    }

    const data = new Date(valor);

    if (Number.isNaN(data.getTime())) {
        return String(valor);
    }

    const dia = String(data.getDate()).padStart(2, "0");
    const mes = String(data.getMonth() + 1).padStart(2, "0");
    const ano = data.getFullYear();

    const hora = String(data.getHours()).padStart(2, "0");
    const minuto = String(data.getMinutes()).padStart(2, "0");
    const segundo = String(data.getSeconds()).padStart(2, "0");

    return `${dia}-${mes}-${ano} / ${hora}:${minuto}:${segundo}`;
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

const extrairCoordenadasDaImagem = async (arquivo) => {
    try {
        const gps = await exifr.gps(arquivo);

        if (!gps || !gps.latitude || !gps.longitude) {
            return {
                latitude: null,
                longitude: null,
                possuiCoordenadas: false,
            };
        }

        return {
            latitude: gps.latitude,
            longitude: gps.longitude,
            possuiCoordenadas: true,
        };
    } catch (error) {
        return {
            latitude: null,
            longitude: null,
            possuiCoordenadas: false,
        };
    }
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

                            <a v-if="getFotoUrl(f)" :href="getFotoUrl(f)"
                                title="Ver Foto" class="btn btn-sm btn-light ms-1 border-0" target="_blank" rel="noopener">
                                <IconEye class="text-warning" />
                            </a>

                            <button v-if="temMetadados(f)" type="button" title="Ver Metadados"
                                class="btn btn-sm btn-light ms-1 border-0" @click="abrirModalMetadados(f)">
                                <IconDatabase class="text-info" />
                            </button>
                        </small>
                    </div>

                    <div class="col-lg-2">
                        <InputLabel :for="`latitude_${key}`">Latitude</InputLabel>

                        <input type="number" v-model="f.latitude" :id="`latitude_${key}`" class="form-control"
                            step="any" :placeholder="valorPadrao('latitude', key)"
                            :class="f.valida_latitude ? 'border-danger' : ''" :disabled="[2, 4].includes(form.status)"
                            @blur="aplicarValoresPadraoNasFotos" />

                        <small v-if="key > 0 && campoVazio(f.latitude) && valorPadrao('latitude', key)"
                            class="text-muted">
                            Usará o valor da primeira foto.
                        </small>

                        <small v-if="f.tentouExtrairCoordenadas
                            && !f.possuiCoordenadasExif
                            && campoVazio(f.latitude)
                            && !valorPadrao('latitude', key)" class="text-danger">
                            A imagem não possui latitude nos metadados. Preencha manualmente.
                        </small>
                    </div>

                    <div class="col-lg-2">
                        <InputLabel :for="`longitude_${key}`">Longitude</InputLabel>

                        <input type="number" v-model="f.longitude" :id="`longitude_${key}`" class="form-control"
                            step="any" :placeholder="valorPadrao('longitude', key)"
                            :class="f.valida_longitude ? 'border-danger' : ''" :disabled="[2, 4].includes(form.status)"
                            @blur="aplicarValoresPadraoNasFotos" />

                        <small v-if="key > 0 && campoVazio(f.longitude) && valorPadrao('longitude', key)"
                            class="text-muted">
                            Usará o valor da primeira foto.
                        </small>

                        <small v-if="f.tentouExtrairCoordenadas
                            && !f.possuiCoordenadasExif
                            && campoVazio(f.longitude)
                            && !valorPadrao('longitude', key)" class="text-danger">
                            A imagem não possui longitude nos metadados. Preencha manualmente.
                        </small>
                    </div>

                    <div class="col-lg-2">
                        <InputLabel :for="`data_captura_${key}`">Data/Hora </InputLabel>

                        <input type="text" :value="formatarDataHoraFoto(f.data_captura)" :id="`data_captura_${key}`"
                            class="form-control" placeholder="Sem data no metadado" />

                        <small v-if="campoVazio(f.data_captura)" class="text-muted">
                            Não encontrada nos metadados.
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

    <Modal ref="modalMetadadosRef" :title="tituloModalMetadados" modal-dialog-class="modal-xl">
        <template #body>
            <div v-if="!temMetadados(fotoMetadadosSelecionada)" class="alert alert-info mb-0">
                Nenhum metadado salvo para esta foto.
            </div>

            <div v-else class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 35%;">
                                Campo
                            </th>
                            <th class="text-center">
                                Valor
                            </th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr v-for="(item, index) in montarLinhasMetadados(fotoMetadadosSelecionada?.metadados)"
                            :key="index">
                            <td>
                                <code>{{ item.campo }}</code>
                            </td>

                            <td style="word-break: break-word;">
                                {{ item.valor }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </template>
    </Modal>
</template>
