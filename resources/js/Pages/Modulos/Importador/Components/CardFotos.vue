<script setup>
import InputLabel from "@/Components/InputLabel.vue";
import { IconCamera, IconTrash, IconEye, IconDatabase, IconFileTypeZip, IconPlaylistAdd } from "@tabler/icons-vue";
import Modal from "@/Components/Modal.vue";
import { ref } from "vue";
import exifr from "exifr";
import JSZip from "jszip";

const props = defineProps({
    form: { type: Object }
});

const inputFotosMultiplasRef = ref(null);
const inputZipFotosRef = ref(null);
const carregandoZip = ref(false);

const modalMetadadosRef = ref(null);
const fotoMetadadosSelecionada = ref(null);

const tituloModalMetadados = ref("Metadados da Foto");
const TAMANHO_MAX_FOTO_MB = 10;
const TAMANHO_MAX_FOTO_BYTES = TAMANHO_MAX_FOTO_MB * 1024 * 1024;
const LADO_MAXIMO_FOTO = 1920;
const QUALIDADE_FOTO = 0.82;

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

const abrirSeletorZipFotos = () => {
    inputZipFotosRef.value?.click();
};

const extensaoImagemValida = (nomeArquivo) => {
    return /\.(jpe?g|png|webp|tiff?|heic|heif)$/i.test(nomeArquivo || "");
};

const nomeArquivoDoZip = (caminho) => {
    return String(caminho || "")
        .split("/")
        .pop()
        .split("\\")
        .pop();
};

const mimePorExtensao = (nomeArquivo) => {
    const nome = String(nomeArquivo || "").toLowerCase();

    if (nome.endsWith(".jpg") || nome.endsWith(".jpeg")) {
        return "image/jpeg";
    }

    if (nome.endsWith(".png")) {
        return "image/png";
    }

    if (nome.endsWith(".webp")) {
        return "image/webp";
    }

    if (nome.endsWith(".tif") || nome.endsWith(".tiff")) {
        return "image/tiff";
    }

    if (nome.endsWith(".heic")) {
        return "image/heic";
    }

    if (nome.endsWith(".heif")) {
        return "image/heif";
    }

    return "image/jpeg";
};

const montarFotoParaFormulario = async (arquivo) => {
    const dadosImagem = await extrairDadosDaImagem(arquivo);
    const arquivoEnvio = await compactarImagem(arquivo);

    return {
        arquivo: arquivoEnvio,
        latitude: dadosImagem.latitude,
        longitude: dadosImagem.longitude,
        data_captura: dadosImagem.data_captura,
        metadados: dadosImagem.metadados,
        descricao: null,
        nome_original: arquivo.name,
        compactada: arquivoEnvio.name !== arquivo.name,

        tentouExtrairCoordenadas: true,
        possuiCoordenadasExif: dadosImagem.possuiCoordenadas,
        origem_zip: true,
    };
};

const selecionarZipFotos = async ({ target }) => {
    const arquivoZip = target.files?.[0];

    if (!arquivoZip) {
        return;
    }

    if (!String(arquivoZip.name).toLowerCase().endsWith(".zip")) {
        alert("Selecione um arquivo .zip válido.");
        target.value = "";
        return;
    }

    carregandoZip.value = true;

    try {
        const zip = await JSZip.loadAsync(arquivoZip);

        const entradasImagem = Object.values(zip.files).filter((entrada) => {
            return !entrada.dir && extensaoImagemValida(entrada.name);
        });

        if (!entradasImagem.length) {
            alert("Nenhuma imagem foi encontrada dentro do arquivo .zip.");
            target.value = "";
            return;
        }

        for (const entrada of entradasImagem) {
            const nomeOriginal = nomeArquivoDoZip(entrada.name);

            const blob = await entrada.async("blob");

            const arquivoImagem = new File(
                [blob],
                nomeOriginal,
                {
                    type: mimePorExtensao(nomeOriginal),
                    lastModified: arquivoZip.lastModified,
                }
            );

            const foto = await montarFotoParaFormulario(arquivoImagem);

            props.form.fotos.push(foto);
        }

        aplicarValoresPadraoNasFotos();
    } catch (error) {
        console.error("Erro ao processar ZIP de imagens:", error);
        alert("Não foi possível processar o arquivo .zip. Verifique se ele contém imagens válidas.");
    } finally {
        carregandoZip.value = false;
        target.value = "";
    }
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
    return (
        valor === "" ||
        valor === null ||
        valor === undefined ||
        Number.isNaN(valor) ||
        String(valor).trim() === "NaN"
    );
};

const primeiroValorPreenchido = (campo) => {
    return props.form.fotos.find((foto) => !campoVazio(foto[campo]))?.[campo] ?? null;
};

const valorPadrao = (campo, key) => {
    if (key === 0) {
        return "";
    }

    return primeiroValorPreenchido(campo) ?? "";
};

const aplicarValoresPadraoNasFotos = () => {
    const latitudePadrao = primeiroValorPreenchido("latitude");
    const longitudePadrao = primeiroValorPreenchido("longitude");
    const dataCapturaPadrao = primeiroValorPreenchido("data_captura");
    const descricaoPadrao = primeiroValorPreenchido("descricao");

    props.form.fotos.forEach((foto) => {
        if (campoVazio(foto.latitude) && !campoVazio(latitudePadrao)) {
            foto.latitude = latitudePadrao;
            foto.latitude_preenchida_automaticamente = true;
        }

        if (campoVazio(foto.longitude) && !campoVazio(longitudePadrao)) {
            foto.longitude = longitudePadrao;
            foto.longitude_preenchida_automaticamente = true;
        }

        if (campoVazio(foto.data_captura) && !campoVazio(dataCapturaPadrao)) {
            foto.data_captura = dataCapturaPadrao;
            foto.data_preenchida_automaticamente = true;
        }

        if (campoVazio(foto.descricao) && !campoVazio(descricaoPadrao)) {
            foto.descricao = descricaoPadrao;
            foto.descricao_preenchida_automaticamente = true;
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

const compactarImagem = (arquivo) => {
    return new Promise((resolve) => {
        if (!arquivo.type?.startsWith("image/") || arquivo.size <= TAMANHO_MAX_FOTO_BYTES) {
            resolve(arquivo);
            return;
        }

        const reader = new FileReader();

        reader.onload = () => {
            const img = new Image();

            img.onload = () => {
                const escala = Math.min(
                    1,
                    LADO_MAXIMO_FOTO / Math.max(img.width, img.height)
                );

                const canvas = document.createElement("canvas");
                canvas.width = Math.max(1, Math.round(img.width * escala));
                canvas.height = Math.max(1, Math.round(img.height * escala));

                const ctx = canvas.getContext("2d");
                ctx.drawImage(img, 0, 0, canvas.width, canvas.height);

                canvas.toBlob((blob) => {
                    if (!blob) {
                        resolve(arquivo);
                        return;
                    }

                    const nomeBase = arquivo.name.replace(/\.[^.]+$/, "");
                    const arquivoCompactado = new File(
                        [blob],
                        `${nomeBase}_compactada.jpg`,
                        {
                            type: "image/jpeg",
                            lastModified: arquivo.lastModified,
                        }
                    );

                    resolve(arquivoCompactado.size < arquivo.size ? arquivoCompactado : arquivo);
                }, "image/jpeg", QUALIDADE_FOTO);
            };

            img.onerror = () => resolve(arquivo);
            img.src = reader.result;
        };

        reader.onerror = () => resolve(arquivo);
        reader.readAsDataURL(arquivo);
    });
};

const limparMetadadosParaEnvio = (valor) => {
    try {
        return JSON.parse(JSON.stringify(valor, (chave, item) => {
            if (item instanceof Date) {
                return formatarDataExif(item);
            }

            if (typeof item === "bigint") {
                return item.toString();
            }

            if (
                item instanceof ArrayBuffer ||
                item instanceof Uint8Array ||
                item instanceof Int8Array ||
                item instanceof Uint16Array ||
                item instanceof Int16Array ||
                item instanceof Uint32Array ||
                item instanceof Int32Array
            ) {
                return "[dados binários]";
            }

            return item;
        }));
    } catch (e) {
        return null;
    }
};

const normalizarNumero = (valor) => {
    const numero = Number(valor);

    if (valor === null || valor === undefined || valor === "" || Number.isNaN(numero)) {
        return null;
    }

    return numero;
};

const extrairDadosDaImagem = async (arquivo) => {
    try {
        const metadadosGerais = await exifr.parse(arquivo, {
            tiff: true,
            ifd0: true,
            exif: true,
            gps: true,
            jfif: true,
            ihdr: true,
            xmp: true,
            icc: true,
            iptc: true,
            mergeOutput: true,
            reviveValues: true,
        });

        const gps = await exifr.gps(arquivo).catch(() => null);

        const latitude = normalizarNumero(gps?.latitude ?? metadadosGerais?.latitude ?? null);
        const longitude = normalizarNumero(gps?.longitude ?? metadadosGerais?.longitude ?? null);

        const dataCaptura = formatarDataExif(
            metadadosGerais?.DateTimeOriginal
            ?? metadadosGerais?.DateTimeDigitized
            ?? metadadosGerais?.DateTime
            ?? metadadosGerais?.CreateDate
            ?? null
        );

        const metadados = limparMetadadosParaEnvio({
            ...metadadosGerais,
            latitude_extraida: latitude,
            longitude_extraida: longitude,
            data_captura_extraida: dataCaptura,
        });

        return {
            latitude,
            longitude,
            data_captura: dataCaptura,
            metadados,
            possuiCoordenadas: !campoVazio(latitude) && !campoVazio(longitude),
        };
    } catch (error) {
        return {
            latitude: null,
            longitude: null,
            data_captura: null,
            metadados: null,
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
        const foto = await montarFotoParaFormulario(arquivo);
        foto.origem_zip = false;

        props.form.fotos.push(foto);
    }

    aplicarValoresPadraoNasFotos();

    target.value = "";
};

const selecionarArquivo = async (key, { target }) => {
    const arquivo = target.files?.[0];

    if (!arquivo) {
        return;
    }

    const dadosImagem = await extrairDadosDaImagem(arquivo);
    const arquivoEnvio = await compactarImagem(arquivo);

    props.form.fotos[key].arquivo = arquivoEnvio;
    props.form.fotos[key].latitude = dadosImagem.latitude;
    props.form.fotos[key].longitude = dadosImagem.longitude;
    props.form.fotos[key].data_captura = dadosImagem.data_captura;
    props.form.fotos[key].metadados = dadosImagem.metadados;
    props.form.fotos[key].nome_original = arquivo.name;
    props.form.fotos[key].compactada = arquivoEnvio.name !== arquivo.name;
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

        if (item.arquivo?.size > TAMANHO_MAX_FOTO_BYTES) {
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

                <input ref="inputZipFotosRef" type="file" class="d-none"
                    accept=".zip,application/zip,application/x-zip-compressed" @change="selecionarZipFotos" />

                <button type="button" @click="abrirSeletorFotos" class="btn btn-outline-primary"
                    :disabled="[2, 4].includes(form.status)">
                    <IconCamera class="me-2" />
                    Adicionar Fotos
                </button>

                <button type="button" @click="abrirSeletorZipFotos" class="btn btn-outline-warning"
                    :disabled="[2, 4].includes(form.status) || carregandoZip">
                    <IconFileZip class="me-2" />
                 <IconFileTypeZip class="me-2"/>   {{ carregandoZip ? "Processando ZIP..." : "Importar ZIP de fotos" }}
                </button>

                <button type="button" @click="addFoto" class="btn btn-outline-secondary"
                    :disabled="[2, 4].includes(form.status)">
                  <IconPlaylistAdd class="me-2" />  Adicionar Linha
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

                        <small v-if="f.arquivo?.size > TAMANHO_MAX_FOTO_BYTES" class="text-danger d-block">
                            A foto deve ter no mÃ¡ximo {{ TAMANHO_MAX_FOTO_MB }}MB.
                        </small>

                        <small v-else-if="f.compactada" class="text-muted d-block">
                            Foto compactada automaticamente para envio.
                        </small>

                        <small v-else-if="f.nome_arquivo">
                            Arquivo original:
                            <strong>{{ f.nome_arquivo }}</strong>

                            <a v-if="f.id" :href="route('modulos.importador.visualizarFoto', { foto: f.id })"
                                title="Ver Foto" class="btn btn-sm btn-light ms-1 border-0" target="_blank">
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
                            class="form-control" :placeholder="key > 0 && valorPadrao('data_captura', key)
                                ? formatarDataHoraFoto(valorPadrao('data_captura', key))
                                : 'Sem data no metadado'" />
                        <small v-if="key > 0 && campoVazio(f.data_captura) && valorPadrao('data_captura', key)"
                            class="text-muted">
                            Usará o valor da primeira foto.
                        </small>

                        <small v-if="campoVazio(f.data_captura)" class="text-muted">
                            Não encontrada nos metadados.
                        </small>
                    </div>

                    <div class="col-lg-3 d-flex gap-2">
                        <div class="flex-grow-1">
                            <InputLabel :for="`descricao_${key}`">Descrição</InputLabel>

                            <input type="text" v-model="f.descricao" :id="`descricao_${key}`" class="form-control"
                                :placeholder="valorPadrao('descricao', key)" :disabled="[2, 4].includes(form.status)"
                                @blur="aplicarValoresPadraoNasFotos" />

                            <small v-if="f.descricao_preenchida_automaticamente" class="text-success d-block">
                                Preenchido automaticamente.
                            </small>

                            <small v-if="key > 0 && campoVazio(f.descricao) && valorPadrao('descricao', key)"
                                class="text-muted">
                                Usará o valor da primeira foto.
                            </small>
                        </div>

                        <div v-if="[1, 3, null].includes(form.status)" class="d-flex gap-2 mb-4"
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
