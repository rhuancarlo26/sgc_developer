<script setup>
import { ref } from 'vue';

defineProps({
  icon: { type: [Function, Object] },
  title: { type: String },
});

const isExpanded = ref(false);

const toggleExpand = () => {
  isExpanded.value = !isExpanded.value;
};
</script>

<template>
  <li class="nav-item">
    <!-- Título com ícone e funcionalidade de expansão -->
    <a class="nav-link" href="#" @click.prevent="toggleExpand">
      <span class="nav-link-icon d-md-none d-lg-inline-block">
        <component :is="icon" />
      </span>
      <span class="nav-link-title">{{ title }}</span>
      <span class="ms-auto">
        <!-- Ícone de expandir/recolher -->
        <i :class="isExpanded ? 'bi bi-chevron-up' : 'bi bi-chevron-down'"></i>
      </span>
    </a>
    <!-- Sublista expansível -->
    <ul v-show="isExpanded" class="nav-sublist">
      <slot />
    </ul>
  </li>
</template>

<style scoped>
.nav-link {
  display: flex;
  align-items: center;
  color: #182433;
  padding: 0.5rem 0rem;
  cursor: pointer;
}

.nav-link-icon {
  color: #182433;
  margin-right: 0.5rem;
}

.nav-link-title {
  flex-grow: 1;
}

.nav-sublist {
  list-style: none;
  padding-left: 2rem;
  margin: 0;
}

.nav-sublist li {
  padding: 0.25rem 0;
}
</style>