```vue
<template>
  <div>
    <h3>Anexos - Upload de Imagens</h3>
    <p class="text-muted mb-4">
      Carregue imagens (JPG, PNG) para a campanha, adicione legendas e gerencie os anexos.
    </p>

    <div
      @drop="onDrop($event)"
      @dragover.prevent
      @dragenter.prevent
      class="border p-4 rounded bg-light mb-3 text-center"
      :class="{ 'border-danger': errors.foto }"
      style="min-height: 100px;"
    >
      <p>
        Arraste imagens aqui ou
        <button type="button" @click.prevent="triggerFileInput" class="btn btn-link p-0">clique para selecionar</button>.
      </p>
      <input
        id="fileInput-foto"
        type="file"
        @change="onFileChange($event)"
        multiple
        accept="image/jpeg,image/png"
        style="display: none;"
      />
      <div v-if="uploadedFiles.length > 0" class="mt-2 text-success">
        <small>Carregado: {{ uploadedFiles.map(f => f.name).join(', ') }}</small>
      </div>
      <small v-if="errors.foto" class="text-danger d-block">{{ errors.foto }}</small>
    </div>

    <button
      v-if="uploadedFiles.length > 0"
      @click="vincularImagens"
      class="btn btn-primary mb-3"
      type="button"
    >
      Vincular Imagens
    </button>

    <!-- Lista de anexos -->
    <div v-if="anexos.length > 0" class="mt-3">
      <h5>Anexos Carregados</h5>
      <div
        v-for="anexo in anexos"
        :key="anexo.id"
        class="border rounded-lg p-3 mb-2"
      >
        <div class="d-flex align-items-center justify-content-between">
          <div class="d-flex align-items-center">
            <img
              :src="anexo.url_publica"
              alt="Imagem anexada"
              class="me-3"
              style="width: 100px; height: 100px; object-fit: cover; border-radius: 4px;"
            />
            <div>
              <div class="text-sm font-medium">{{ anexo.nome }}</div>
              <div class="text-muted small">Tamanho: {{ formatFileSize(anexo.size) }}</div>
            </div>
          </div>
          <div class="d-flex align-items-center gap-2">
            <a :href="anexo.url_publica" target="_blank" class="text-blue-600 hover:underline">
              Download
            </a>
            <button
              @click="confirmarExclusao(anexo.id)"
              class="btn btn-sm btn-outline-danger"
              type="button"
            >
              <i class="fas fa-trash-alt"></i> Excluir
            </button>
          </div>
        </div>

        <div class="mt-2 d-flex align-items-end gap-2">
          <div class="flex-grow-1">
            <label class="form-label small">Legenda</label>
            <textarea
              v-model="legendas[anexo.id]"
              class="form-control"
              rows="2"
              placeholder="Adicione uma legenda para a imagem"
              @keyup.enter="updateLegenda(anexo.id)"
            ></textarea>
          </div>
          <button
            @click="updateLegenda(anexo.id)"
            class="btn btn-sm btn-primary"
            type="button"
          >
            Salvar
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import axios from 'axios';

const props = defineProps({
  campanhaId: {
    type: Number,
    required: true,
  },
  contrato: {
    type: Number,
    required: true,
  },
  anexos: {
    type: Array,
    default: () => [],
  },
  errors: {
    type: Object,
    default: () => ({}),
  },
});

const emit = defineEmits(['update-anexos']);

const uploadedFiles = ref([]);
const legendas = ref({});
const anexos = ref(props.anexos || []);

// Inicializa legendas para anexos existentes
watch(
  () => props.anexos,
  (newVal) => {
    anexos.value = [...newVal];
    newVal.forEach(anexo => {
      legendas.value[anexo.id] = anexo.legenda || '';
    });
  },
  { deep: true, immediate: true }
);

const triggerFileInput = () => {
  const el = document.getElementById('fileInput-foto');
  if (el) el.click();
};

const onFileChange = (event) => {
  const files = Array.from(event.target.files);
  processFiles(files);
};

const onDrop = (event) => {
  event.preventDefault();
  const files = Array.from(event.dataTransfer.files);
  processFiles(files);
};

const processFiles = (files) => {
  uploadedFiles.value = files.filter(f => f.type === 'image/jpeg' || f.type === 'image/png');
  if (uploadedFiles.value.length !== files.length) {
    alert('Apenas arquivos JPG ou PNG são permitidos.');
  }
};

const vincularImagens = async () => {
  if (!uploadedFiles.value.length) return;

  const formData = new FormData();
  uploadedFiles.value.forEach(file => formData.append('fotos[]', file));
  formData.append('campanha_id', props.campanhaId);
  formData.append('tipo', 'foto');

  console.log('Enviando para:', route('sgc.contratada.produtos.espeleo.anexos.upload', { 
    contrato: props.contrato, 
    produto: 'espeleologia' 
  }));
  console.log('Dados enviados:', {
    campanha_id: props.campanhaId,
    files: uploadedFiles.value.map(f => f.name),
  });

  try {
    const response = await axios.post(
      route('sgc.contratada.produtos.espeleo.anexos.upload', { 
        contrato: props.contrato, 
        produto: 'espeleologia' 
      }),
      formData,
      { headers: { 'Content-Type': 'multipart/form-data' } }
    );

    console.log('Resposta do backend:', response.data);

    if (response.data.success) {
      anexos.value = [...anexos.value, ...response.data.anexos];
      emit('update-anexos', anexos.value);
      uploadedFiles.value = [];
      alert('Imagens vinculadas com sucesso!');
    } else {
      console.error('Erro retornado pelo backend:', response.data.message);
      alert(`Erro ao vincular imagens: ${response.data.message}`);
    }
  } catch (error) {
    console.error('Erro ao vincular imagens:', error.response?.data || error.message);
    alert(`Erro ao vincular imagens: ${error.response?.data?.message || 'Tente novamente.'}`);
  }
};

const updateLegenda = (id) => {
  router.post(
    route('sgc.contratada.produtos.espeleo.anexos.update', {
      contrato: props.contrato,
      produto: 'espeleologia',
      id,
    }),
    { legenda: legendas.value[id] },
    {
      preserveState: true,
      preserveScroll: true,
      onSuccess: () => {
        alert('Legenda salva com sucesso!');
      },
      onError: (err) => {
        console.error('Erro ao atualizar legenda:', err);
        alert('Erro ao atualizar a legenda. Tente novamente.');
      },
    }
  );
};

const confirmarExclusao = (id) => {
  if (confirm('Tem certeza que deseja excluir esta imagem?')) {
    removerAnexo(id);
  }
};

const removerAnexo = (id) => {
  router.delete(
    route('sgc.contratada.produtos.espeleo.anexos.delete', {
      contrato: props.contrato,
      produto: 'espeleologia',
      id,
    }),
    {
      preserveState: true,
      preserveScroll: true,
      onSuccess: () => {
        anexos.value = anexos.value.filter(a => a.id !== id);
        delete legendas.value[id];
        emit('update-anexos', anexos.value);
      },
      onError: (err) => {
        console.error('Erro ao remover anexo:', err);
        alert('Erro ao excluir a imagem. Tente novamente.');
      },
    }
  );
};

const formatFileSize = (bytes) => {
  if (!bytes) return 'N/A';
  const sizes = ['Bytes', 'KB', 'MB', 'GB'];
  if (bytes === 0) return '0 Byte';
  const i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
  return Math.round(bytes / Math.pow(1024, i), 2) + ' ' + sizes[i];
};
</script>

<style scoped>
@import '@fortawesome/fontawesome-free/css/all.min.css';

.text-muted {
  color: #6c757d;
}
.border-danger {
  border-color: #dc3545 !important;
}
.btn-outline-danger {
  display: flex;
  align-items: center;
}
.btn-outline-danger i {
  margin-right: 4px;
}
</style>