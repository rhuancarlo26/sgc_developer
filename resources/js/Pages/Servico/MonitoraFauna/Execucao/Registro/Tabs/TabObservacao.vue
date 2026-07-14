<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
  form: { type: Object }
});
</script>
<template>
  <form>
    <div class="row mb-4">
      <div class="col">
        <InputLabel value="Observaçõs" for="obs" />
        <textarea name="obs" id="obs" class="form-control" v-model="form.obs" rows="5"></textarea>
        <InputError :message="form.errors.obs" />
      </div>
    </div>
  </form>
</template>