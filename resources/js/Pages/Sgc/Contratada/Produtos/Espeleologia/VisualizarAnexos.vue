<template>
  <div class="anexos-viewer">
    <h4 class="mb-3" style="text-align: center;">ANEXOS - IMAGENS</h4>

    <div v-if="anexos && anexos.length > 0">
      <div class="row">
        <div v-for="anexo in anexos" :key="anexo.id" class="col-md-4 mb-4">
          <div class="card h-100">
            <div class="card-img-top" style="height: 250px; overflow: hidden; background-color: #f5f5f5;">
              <a :href="anexo.caminho" target="_blank" rel="noopener noreferrer">
                <img
                  v-if="isImage(anexo.nome_arquivo)"
                  :src="anexo.caminho"
                  :alt="anexo.nome_arquivo"
                  style="width: 100%; height: 100%; object-fit: cover; cursor: pointer;"
                  @click="viewImageFull(anexo)"
                />
                <div v-else class="d-flex align-items-center justify-content-center h-100 text-muted">
                  <i class="fas fa-file-image" style="font-size: 3rem;"></i>
                </div>
              </a>
            </div>
            <div class="card-body">
              <h5 class="card-title" :title="anexo.nome_arquivo">
                {{ truncateFileName(anexo.nome_arquivo) }}
              </h5>
              <p v-if="anexo.legenda" class="card-text small text-muted">
                <strong>Legenda:</strong> {{ anexo.legenda }}
              </p>
              <p class="card-text small text-muted">
                <strong>Data:</strong> {{ anexo.created_at }}
              </p>
              <div class="d-flex gap-2">
                <a
                  :href="anexo.caminho"
                  target="_blank"
                  rel="noopener noreferrer"
                  class="btn btn-sm btn-primary"
                >
                  <i class="fas fa-download"></i> Baixar
                </a>
                <button
                  type="button"
                  @click="viewImageFull(anexo)"
                  class="btn btn-sm btn-secondary"
                >
                  <i class="fas fa-expand"></i> Ampliar
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div v-else class="alert alert-info text-center">
      <i class="fas fa-info-circle"></i> Nenhuma imagem anexada para esta campanha.
    </div>

    <!-- Modal para visualização em tela cheia -->
    <div v-if="fullScreenImage" class="modal d-block" style="background-color: rgba(0, 0, 0, 0.8);">
      <div class="modal-dialog modal-lg" style="max-width: 90vw; margin-top: 5vh;">
        <div class="modal-content" style="background: transparent; border: none;">
          <div class="modal-header" style="border: none;">
            <h5 class="modal-title" style="color: white;">
              {{ fullScreenImage.nome_arquivo }}
            </h5>
            <button
              type="button"
              class="btn-close btn-close-white"
              @click="fullScreenImage = null"
              aria-label="Close"
            ></button>
          </div>
          <div class="modal-body" style="text-align: center;">
            <img
              :src="fullScreenImage.caminho"
              :alt="fullScreenImage.nome_arquivo"
              style="max-width: 100%; max-height: 70vh; object-fit: contain;"
            />
          </div>
          <div v-if="fullScreenImage.legenda" class="modal-footer" style="border-top: 1px solid #666;">
            <p style="color: white; margin: 0; flex-grow: 1;">
              <strong>Legenda:</strong> {{ fullScreenImage.legenda }}
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';

defineProps({
  anexos: {
    type: Array,
    default: () => [],
  },
});

const fullScreenImage = ref(null);

const isImage = (fileName) => {
  if (!fileName) return false;
  const imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
  const ext = fileName.split('.').pop()?.toLowerCase();
  return imageExtensions.includes(ext);
};

const truncateFileName = (name) => {
  if (!name || name.length <= 40) return name;
  return name.substring(0, 37) + '...';
};

const viewImageFull = (anexo) => {
  fullScreenImage.value = anexo;
};
</script>

<style scoped>
.anexos-viewer {
  padding: 1.5rem 0;
}

.card {
  border: 1px solid #dee2e6;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  transition: transform 0.2s, box-shadow 0.2s;
}

.card:hover {
  transform: translateY(-4px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.card-img-top {
  border-bottom: 1px solid #dee2e6;
}

.card-title {
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
  font-size: 0.95rem;
}

.btn-group {
  display: flex;
  gap: 0.5rem;
}

.modal {
  display: flex;
  align-items: center;
  justify-content: center;
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  z-index: 1050;
}

.modal-content {
  max-height: 90vh;
  display: flex;
  flex-direction: column;
}

.modal-body {
  overflow: auto;
  flex-grow: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 1rem;
}
</style>
