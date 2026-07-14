<script setup>
import { ref } from 'vue'
import axios from 'axios'
import InputLabel from '@/Components/InputLabel.vue'
import InputError from '@/Components/InputError.vue'
import Alert from '@/Components/Alert.vue'

/**
 * Props do componente
 */
const props = defineProps({
  idSubproduto: {
    type: [String, Number],
    required: true
  }
})

/**
 * Campos do formulário
 */
const name = ref('')
const file = ref(null)
const loading = ref(false)
const message = ref(null)
const messageType = ref(null)
const errors = ref({})

/**
 * Captura arquivo selecionado
 */
function handleFile(event) {
  file.value = event.target.files[0]
  if (errors.value.file) {
    errors.value.file = null
  }
}

/**
 * Envia shapefile para o backend
 */
async function submit() {
  errors.value = {}
  message.value = null
  messageType.value = null

  // Validação de campos
  if (!name.value.trim()) {
    errors.value.name = 'O nome da camada é obrigatório'
  }

  if (!file.value) {
    errors.value.file = 'Selecione um arquivo .zip com o shapefile'
  }

  if (Object.keys(errors.value).length > 0) {
    return
  }

  loading.value = true

  /**
   * FormData é obrigatório para upload
   */
  const formData = new FormData()
  formData.append('title', name.value)
  formData.append('file', file.value)
  formData.append('id_subproduto', props.idSubproduto)

  try {
    const response = await axios.post(
      '/sgc/contratada/espeleologia/layers/upload-shapefile',
      formData,
      { headers: { 'Content-Type': 'multipart/form-data' } }
    )

    message.value = response.data.message || 'Upload realizado com sucesso'
    messageType.value = 'success'
    name.value = ''
    file.value = null
  } catch (error) {
    messageType.value = 'error'
    if (error.response?.data?.message) {
      message.value = error.response.data.message
    } else if (error.response?.data?.errors) {
      errors.value = error.response.data.errors
      message.value = 'Verifique os erros abaixo'
      messageType.value = 'error'
    } else {
      message.value = 'Erro ao enviar shapefile. Tente novamente.'
    }
    console.error(error)
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="card">
    <div class="card-body">
      <h5 class="card-title mb-2">
        <i class="fas fa-map"></i> Cadastrar Shapefile
      </h5>
      <p class="text-muted small mb-4">
        Faça upload de um arquivo .zip contendo seu shapefile (arquivos .shp, .shx, .dbf, etc)
      </p>

      <!-- Alerta de sucesso/erro -->
      <Alert
        v-if="message"
        :type="messageType"
        :content="message"
        @closeButtonClicked="message = null"
        class="mb-3"
      />

      <!-- Forma de envio -->
      <form @submit.prevent="submit" class="space-y-4">
        <!-- Campo: Nome da camada -->
        <div class="mb-3">
          <InputLabel value="Nome da camada" />
          <input
            v-model="name"
            type="text"
            class="form-control"
            :class="{ 'is-invalid': errors.name }"
            placeholder="Ex: Rodovias Federais"
          />
          <InputError :message="errors.name" />
        </div>

        <!-- Campo: Arquivo -->
        <div class="mb-3">
          <InputLabel value="Arquivo (.zip)" />
          <input
            type="file"
            accept=".zip"
            @change="handleFile"
            class="form-control"
            :class="{ 'is-invalid': errors.file }"
          />
          <small v-if="file" class="text-success d-block mt-1">
            <i class="fas fa-check-circle"></i> {{ file.name }}
          </small>
          <InputError :message="errors.file" />
        </div>

        <!-- Campo: ID Subproduto (oculto) -->
        <input
          type="hidden"
          :value="props.idSubproduto"
        />

        <!-- Botão de envio -->
        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
          <button
            type="submit"
            :disabled="loading"
            class="btn btn-primary"
          >
            <i v-if="!loading" class="fas fa-upload me-2"></i>
            <i v-else class="fas fa-spinner fa-spin me-2"></i>
            {{ loading ? 'Enviando...' : 'Enviar Shapefile' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>
