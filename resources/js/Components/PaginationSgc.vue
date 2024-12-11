<script setup>
import { ref, watch } from 'vue';

const props = defineProps({
  totalItems: Number,
  itemsPerPageOptions: {
    type: Array,
    default: () => [10, 20, 50]
  }
});

const emit = defineEmits(['pageChanged', 'itemsPerPageChanged']);

// Controle de paginação
const currentPage = ref(1);
const itemsPerPage = ref(props.itemsPerPageOptions[0]);
const totalPages = ref(Math.ceil(props.totalItems / itemsPerPage.value));

watch([() => props.totalItems, itemsPerPage], () => {
  totalPages.value = Math.ceil(props.totalItems / itemsPerPage.value);
});

// Alterar página
const changePage = (newPage) => {
  if (newPage >= 1 && newPage <= totalPages.value) {
    currentPage.value = newPage;
    emit('pageChanged', currentPage.value);
  }
};

// Alterar o número de itens por página
const changeItemsPerPage = (event) => {
  itemsPerPage.value = parseInt(event.target.value, 10);
  currentPage.value = 1; // Resetar para a primeira página ao mudar os itens por página
  emit('itemsPerPageChanged', itemsPerPage.value);
};
</script>

<template>
    <div class="d-flex justify-content-between align-items-center mt-3">
      <!-- Opção de selecionar o número de itens por página -->
      <div class="d-flex align-items-center ms-3">
          <label for="itemsPerPage">Itens por página: </label>
          <select id="itemsPerPage" @change="changeItemsPerPage" class="form-select form-select-sm me-4" style="width: 70px;">
              <option v-for="option in itemsPerPageOptions" :key="option" :value="option">{{ option }}</option>
          </select>
          
      </div>
  
      <div class="d-flex">
        <!-- Controles de páginas -->
        <ul class="pagination m-0 ms-auto">
          <li class="page-item" :class="{ disabled: currentPage === 1 }">
            <a href="#" class="page-link" @click.prevent="changePage(currentPage - 1)" :class="{ disabled: currentPage === 1 }">« Anterior</a>
          </li>
  
          <!-- Página atual -->
          <li v-for="page in totalPages" :key="page" class="page-item mx-1" :class="{ active: page === currentPage }">
            <a href="#" class="page-link" @click.prevent="changePage(page)" :class="{ active: page === currentPage }">{{ page }}</a>
          </li>
  
          <li class="page-item" :class="{ disabled: currentPage === totalPages }">
            <a href="#" class="page-link" @click.prevent="changePage(currentPage + 1)" :class="{ disabled: currentPage === totalPages }">Próximo »</a>
          </li>
        </ul>
  
      </div>
    </div>
</template>


<style scoped>
.page-item.disabled a {
  pointer-events: none;
  color: #d1d1d1;
}

.page-item.active a {
  color: #fff;
  background-color: #0054A6;
  border-radius: 4px;
}

.page-link {
  color: #1c2530;
  background: none;
  border: none;
  padding: 0.5rem 0.75rem;
  text-decoration: none;
}

.page-link:hover {
  text-decoration: underline;
}

.pagination {
  display: flex;
  padding-left: 0;
  list-style: none;
  margin: 0;
}

.form-select-sm {
  width: auto;
  padding: 0.25rem 0.5rem;
  font-size: 0.875rem;
}
</style>
