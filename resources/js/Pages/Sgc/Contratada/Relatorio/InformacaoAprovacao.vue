<script setup>
import { formatarDataRelatorio } from './dataRelatorio';
defineProps({ aprovacao: Object });
</script>

<template>
  <div class="text-muted small mt-2">
    <template v-if="aprovacao">
      <div>{{ aprovacao.user?.name ? 'Aprovado por ' + aprovacao.user.name : 'Responsável pela aprovação não registrado' }}</div>
      <div>{{ formatarDataRelatorio(aprovacao.created_at, 'Data da aprovação não registrada') }}</div>
    </template>
    <span v-else>Responsável e data da aprovação não registrados.</span>
  </div>
</template>
