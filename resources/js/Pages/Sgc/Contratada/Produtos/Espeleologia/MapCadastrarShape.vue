<script setup>
import { ref } from 'vue'
import axios from 'axios'

/**
 * Campos do formulário
 */
const name = ref('')
const file = ref(null)
const loading = ref(false)
const message = ref(null)

/**
 * Captura arquivo selecionado
 */
function handleFile(event) {
  file.value = event.target.files[0]
}

/**
 * Envia shapefile para o backend
 */
async function submit() {
  if (!file.value) {
    alert('Selecione um arquivo .zip com o shapefile')
    return
  }

  loading.value = true
  message.value = null

  /**
   * FormData é obrigatório para upload
   */
  const formData = new FormData()
  formData.append('title', name.value)
  formData.append('file', file.value)

  try {
    // /espeleologia/layers/upload-shapefile
    const response = await axios.post(
      '/sgc/contratada/espeleologia/layers/upload-shapefile',
      formData,
      { headers: { 'Content-Type': 'multipart/form-data' } }
    )

    message.value = response.data.message || 'Upload realizado com sucesso'
    name.value = ''
    file.value = null
  } catch (error) {
    message.value = 'Erro ao enviar shapefile'
    console.error(error)
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="max-w-xl p-6 border rounded bg-white">
    <h2 class="font-bold mb-4">Cadastrar Shapefile</h2>

    <div class="mb-4">
      <label class="block text-sm mb-1">Nome da camada</label>
      <input
        v-model="name"
        type="text"
        class="w-full border rounded p-2"
        placeholder="Ex: Rodovias Federais"
      />
    </div>

    <div class="mb-4">
      <label class="block text-sm mb-1">Arquivo (.zip)</label>
      <input
        type="file"
        accept=".zip"
        @change="handleFile"
      />
    </div>

    <button
      @click="submit"
      :disabled="loading"
      class="bg-blue-600 text-white px-4 py-2 rounded disabled:opacity-50"
    >
      {{ loading ? 'Enviando...' : 'Enviar' }}
    </button>

    <p v-if="message" class="mt-4 text-sm">
      {{ message }}
    </p>
  </div>
</template>
