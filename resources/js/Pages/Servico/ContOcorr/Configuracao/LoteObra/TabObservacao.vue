<script setup>
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import NavButton from "@/Components/NavButton.vue";
import { useForm } from "@inertiajs/vue3";

const props = defineProps({
  contrato: { type: Object },
  servico: { type: Object }
});

const form = useForm({
  obs: null
});
</script>
<template>
  <div class="row">
    <div class="col">
      <InputLabel value="Observações" for="obs" />
      <textarea class="form-control" rows="5" v-model="form.obs"></textarea>
      <InputError :message="form.errors.obs" />
    </div>
  </div>
</template>