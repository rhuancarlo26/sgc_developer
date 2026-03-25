<template>
  <div v-if="isVisible" class="modal-overlay" @click.self="closeModal">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">{{ title.toUpperCase() }}</h5> <!-- Título em maiúsculo -->
        <button type="button" class="close" @click="closeModal">&times;</button>
      </div>
      <div class="modal-body">
        <slot></slot> <!-- Conteúdo dinâmico inserido através do slot -->
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" @click="closeModal">Fechar</button>
      </div>
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  isVisible: Boolean, // Controle de visibilidade do modal
  title: String       // Título dinâmico do modal
});

const emit = defineEmits(['close']); // Evento de fechamento

const closeModal = () => {
  emit('close');
};
</script>

<style scoped>
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1050;
}

.modal-content {
  background-color: white;
  width: 70%;
  max-width: 900px;
  padding: 20px;
  border-radius: 12px; /* Cantos mais arredondados */
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2); /* Sombra mais acentuada */
  overflow: hidden;
  border: 1px solid #e0e0e0; /* Borda sutil */
}

.modal-header {
  display: flex;
  justify-content: center;
  align-items: center;
  border-bottom: 2px solid #bdb7b7; 
  font-weight: bold; 
  padding-bottom: 10px;
  text-transform: uppercase;
  background-color: #ffffff;
}

.modal-title {
  font-size: 20px; /* Aumenta o tamanho da fonte */
  font-weight: bold; /* Negrito no título */
}

.close {
  position: absolute;
  right: 20px;
  background: none;
  border: none;
  font-size: 24px; 
  cursor: pointer;
  color: #6c757d; 
}

.modal-body {
  margin-top: 20px;
  font-size: 15px; 
  line-height: 1.6; /* Espaçamento entre linhas */
}

.modal-footer {
  display: flex;
  justify-content: flex-end;
  border-top: 2px solid #f0f0f0; 
  padding-top: 10px;
  background-color: #f8f9fa; 
}

.modal-content .table thead th {
  font-size: 18px; /* Aumenta o tamanho da fonte do cabeçalho */
  text-align: center; /* Centraliza o texto das colunas */
  font-weight: bold;
}

.btn {
  padding: 8px 16px;
  font-size: 14px;
  border-radius: 6px; 
}

.btn-secondary {
  background-color: #757d6c;
  color: white;
  border: none;
  transition: background-color 0.2s;
}

.btn-secondary:hover {
  background-color: #5a685f;
}

</style>
