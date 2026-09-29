<script setup>
import { ref } from 'vue';
import { IconTrash, IconCamera, IconFileText, IconPrinter } from '@tabler/icons-vue';
import exifr from 'exifr';

const props = defineProps({
  paipaId: [Number, String],
  contrato: [Number, String],
});

const emit = defineEmits(['voltar', 'finalizar']);

const tiposAnexo = [
  'Registro Fotográfico',
  'Endosso Financeiro',
  'Endosso Institucional',
  'Materiais de Divulgação',
  'Declarações',
  'Outros'
];

const tipoSelecionado = ref('Registro Fotográfico');
const fotos = ref([]);
const anexosGenericos = ref([]);

// Formata data do EXIF
const formatarDataExif = (dataRaw) => {
  if (!dataRaw) return null;
  try {
    const d = new Date(dataRaw);
    if (isNaN(d.getTime())) return String(dataRaw);
    return `${d.toLocaleDateString('pt-BR')} ${d.toLocaleTimeString('pt-BR')}`;
  } catch {
    return String(dataRaw);
  }
};

// Processa upload de fotos com leitura de EXIF
const onFotosSelected = async (event) => {
  const files = Array.from(event.target.files);
  for (const file of files) {
    const fotoObj = {
      file,
      url: URL.createObjectURL(file),
      legenda: '',
      data: null,
      latitude: null,
      longitude: null,
      lendoExif: true,
    };
    fotos.value.push(fotoObj);

    try {
      const metadados = await exifr.parse(file, ["latitude", "longitude", "DateTimeOriginal"]);
      fotoObj.latitude = metadados?.latitude || 'N/A';
      fotoObj.longitude = metadados?.longitude || 'N/A';
      fotoObj.data = formatarDataExif(metadados?.DateTimeOriginal) || 'N/A';
    } catch (e) {
      console.error("Erro ao ler EXIF", e);
      fotoObj.latitude = 'N/A';
      fotoObj.longitude = 'N/A';
      fotoObj.data = 'N/A';
    } finally {
      fotoObj.lendoExif = false;
    }
  }
  event.target.value = '';
};

const removerFoto = (index) => {
  fotos.value.splice(index, 1);
};

const onArquivosGenericosSelected = (event) => {
  const files = Array.from(event.target.files);
  for (const file of files) {
    const isImage = file.type.startsWith('image/');
    anexosGenericos.value.push({ 
      file, 
      tipo: tipoSelecionado.value,
      url: isImage ? URL.createObjectURL(file) : null,
      isImage
    });
  }
  event.target.value = '';
};

const removerAnexoGenerico = (index) => {
  anexosGenericos.value.splice(index, 1);
};

const imprimirRegistroFotografico = () => {
  alert('Função de exportação/impressão JGP será gerada aqui!');
};
</script>

<template>
  <div class="card p-4 shadow-sm border-0">
    <h4 class="mb-4 text-primary">Anexos do PAIPA</h4>
    
    <div class="row mb-4">
      <div class="col-md-6">
        <label class="form-label fw-bold">Selecione o Tipo de Anexo</label>
        <select class="form-select" v-model="tipoSelecionado">
          <option v-for="tipo in tiposAnexo" :key="tipo" :value="tipo">
            {{ tipo }}
          </option>
        </select>
      </div>
    </div>

    <!-- REGISTRO FOTOGRÁFICO -->
    <div v-if="tipoSelecionado === 'Registro Fotográfico'" class="border p-3 rounded bg-light">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="m-0"><IconCamera class="me-2" />Registro Fotográfico</h5>
        <div>
          <button class="btn btn-outline-secondary btn-sm me-2" @click="imprimirRegistroFotografico" :disabled="!fotos.length">
            <IconPrinter size="18" class="me-1" /> Imprimir JGP
          </button>
          <label class="btn btn-primary btn-sm m-0 cursor-pointer">
            + Adicionar Fotos
            <input type="file" multiple accept="image/*" class="d-none" @change="onFotosSelected" />
          </label>
        </div>
      </div>
      
      <div class="row">
        <div class="col-md-4 mb-3" v-for="(foto, index) in fotos" :key="index">
          <div class="card h-100 shadow-sm">
            <img :src="foto.url" class="card-img-top" style="height: 200px; object-fit: cover;" />
            <div class="card-body p-2 d-flex flex-column">
              <input type="text" class="form-control form-control-sm mb-2" v-model="foto.legenda" placeholder="Digite a legenda..." />
              <div class="small text-muted mb-2">
                <span v-if="foto.lendoExif">Lendo metadados...</span>
                <span v-else>
                  <strong>Data:</strong> {{ foto.data }}<br/>
                  <strong>Lat:</strong> {{ foto.latitude }}<br/>
                  <strong>Lng:</strong> {{ foto.longitude }}
                </span>
              </div>
              <button class="btn btn-danger btn-sm mt-auto w-100" @click="removerFoto(index)">
                <IconTrash size="16" /> Remover
              </button>
            </div>
          </div>
        </div>
        <div v-if="!fotos.length" class="text-center text-muted p-4 w-100">
          Nenhuma foto adicionada ao registro fotográfico.
        </div>
      </div>
    </div>

    <!-- OUTROS TIPOS DE ANEXOS (PDF/GENERICOS) -->
    <div v-else class="border p-3 rounded bg-light">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="m-0"><IconFileText class="me-2" />{{ tipoSelecionado }}</h5>
        <label class="btn btn-primary btn-sm m-0 cursor-pointer">
          + Adicionar Arquivo
          <input type="file" multiple class="d-none" @change="onArquivosGenericosSelected" />
        </label>
      </div>

      <ul class="list-group">
        <li class="list-group-item d-flex justify-content-between align-items-center" v-for="(anexo, index) in anexosGenericos.filter(a => a.tipo === tipoSelecionado)" :key="index">
          <div class="d-flex align-items-center">
            <img v-if="anexo.isImage" :src="anexo.url" alt="preview" style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px;" class="me-3 border" />
            <IconFileText v-else class="me-3 text-muted" size="32" />
            <span>{{ anexo.file.name }}</span>
          </div>
          <button class="btn btn-outline-danger btn-sm" @click="removerAnexoGenerico(index)">
            <IconTrash size="16" />
          </button>
        </li>
      </ul>
      <div v-if="!anexosGenericos.filter(a => a.tipo === tipoSelecionado).length" class="text-center text-muted p-4">
        Nenhum arquivo adicionado para {{ tipoSelecionado }}.
      </div>
    </div>

    <div class="d-flex justify-content-between mt-4">
      <button type="button" class="btn btn-secondary" @click="$emit('voltar')">
        Voltar
      </button>
      <button type="button" class="btn btn-success" @click="$emit('finalizar')">
        Concluir / Salvar
      </button>
    </div>
  </div>
</template>

<style scoped>
.cursor-pointer { cursor: pointer; }
</style>
