<template>
    <div>
      <ul class="nav nav-tabs">
        <li class="nav-item" v-for="(tab, index) in tabs" :key="index">
          <a
            class="nav-link"
            :class="{ active: activeTab === index }"
            href="#"
            @click="selectTab(index)"
          >
            {{ tab.label }}
          </a>
        </li>
      </ul>
      <div class="tab-content">
        <div class="tab-pane fade" :class="{ show: true, active: activeTab === index }" v-for="(tab, index) in tabs" :key="index">
          <slot :name="tab.name"></slot>
        </div>
      </div>
    </div>
  </template>

  <script setup>
  import { ref } from "vue";

  const props = defineProps({
    tabs: {
      type: Array,
      required: true,
    },
  });

  const activeTab = ref(0);

  const selectTab = (index) => {
    activeTab.value = index;
  };
  </script>

  <style scoped>
  .nav-tabs .nav-link.active {
    background-color: #007bff;
    color: white;
  }
  </style>
