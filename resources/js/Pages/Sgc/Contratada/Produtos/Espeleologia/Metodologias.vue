<template>
  <div>
    <h3>Metodologias</h3>
    <p class="text-muted mb-3">Descreva as metodologias utilizadas na campanha de Espeleologia.</p>
    <div class="mb-3">
      <textarea
        v-model="localMetodologia"
        class="form-control"
        rows="8"
        placeholder="Insira aqui o texto livre descrevendo a metodologia aplicada (ex: técnicas de prospecção, equipamentos usados, etc.)."
      ></textarea>
      <small v-if="errors.metodologia" class="text-danger">{{ errors.metodologia }}</small>
    </div>
  </div>
</template>

<script setup>
import { defineProps, defineEmits, ref, watch } from 'vue';

const props = defineProps({
  metodologia: {
    type: String,
    default: '',
  },
  errors: {
    type: Object,
    default: () => ({}),
  },
});

const emit = defineEmits(['update-metodologia']);

const localMetodologia = ref(props.metodologia || '');

// Watch para sincronizar com props (bidirecional)
watch(() => props.metodologia, (newVal) => {
  localMetodologia.value = newVal || '';
});

watch(localMetodologia, (newVal) => {
  emit('update-metodologia', newVal);
});
</script>

<style scoped>

textarea {
  resize: vertical; 
  min-height: 150px;
}

</style>