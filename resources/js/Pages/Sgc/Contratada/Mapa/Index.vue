<template>
  <div class="p-6">
    <h1 class="text-2xl font-bold mb-4">Camadas</h1>

    <!-- upload -->
    <form @submit.prevent="submit" class="mb-6 space-y-3">
      <input v-model="form.name" class="border p-2 w-full" placeholder="Nome da camada">

      <input type="file" @change="e => form.file = e.target.files[0]" />

      <button class="bg-blue-600 text-white px-4 py-2 rounded">
        Enviar
      </button>
    </form>

    <!-- lista -->
    <table class="w-full border">
      <thead>
        <tr class="bg-gray-200">
          <th class="p-2">Nome</th>
          <th class="p-2">Status</th>
          <th class="p-2">Tiles</th>
        </tr>
      </thead>

      <tbody>
        <tr v-for="layer in layers" :key="layer.id" class="border-t">
          <td class="p-2">{{ layer.name }}</td>
          <td class="p-2">{{ layer.status }}</td>
          <td class="p-2">
            <span v-if="layer.status === 'ready'">
              /tileserver/data/{{ layer.name }}/{{ '{z}/{x}/{y}.pbf' }}
            </span>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
  layers: Array
});

const form = useForm({
  name: '',
  file: null
});

function submit() {
    // enviar para o caminho em contratada.layers.store
  form.post(route('sgc.contratada.layers.store'), {
    forceFormData: true
  });
}
</script>
