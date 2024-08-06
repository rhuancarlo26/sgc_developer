<!-- ModalNovaCampanha.vue -->
<template>
  <div class="modal fade" id="modalNovaCampanha" tabindex="-1" aria-labelledby="modalNovaCampanhaLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalNovaCampanhaLabel">Nova Campanha</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div id="campanha_dados_gerais">
            <div class="row mb-2">
              <div class="col-2">
                <label for="campanha_id">ID campanha</label>
                <input type="text" id="campanha_id" disabled="disabled" class="form-control">
              </div>
              <div class="col-2">
                <label for="campanha_rodovia">Rodovia</label>
                <input type="text" id="campanha_rodovia" disabled="disabled" class="form-control">
              </div>
            </div>
            <div class="row mb-2">
              <div class="col-2">
                <label>UF inicial</label>
                <select class="form-control">
                  <option value="14">PA - Pará</option>
                </select>
              </div>
              <div class="col-2">
                <label>KM inicial</label>
                <input type="text" class="form-control">
              </div>
              <div class="col-3">
                <label>Latitude inicial</label>
                <input type="text" class="form-control">
              </div>
              <div class="col-3">
                <label>Longitude inicial</label>
                <input type="text" class="form-control">
              </div>
            </div>
            <div class="row mb-2">
              <div class="col-2">
                <label>UF final</label>
                <select class="form-control">
                  <option value="14">PA - Pará</option>
                </select>
              </div>
              <div class="col-2">
                <label>KM final</label>
                <input type="text" class="form-control">
              </div>
              <div class="col-3">
                <label>Latitude final</label>
                <input type="text" class="form-control">
              </div>
              <div class="col-3">
                <label>Longitude final</label>
                <input type="text" class="form-control">
              </div>
            </div>
            <div class="row mb-2">
              <div class="col-4">
                <label>Data inicial</label>
                <input type="date" class="form-control">
              </div>
              <div class="col-4">
                <label>Data final</label>
                <input type="date" class="form-control">
              </div>
            </div>
            <div class="row mb-2">
              <div class="col-12">
                <label>Observação</label>
                <textarea class="form-control"></textarea>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-success btn-lg">Salvar</button>
          <button type="button" class="btn btn-primary btn-lg" data-bs-dismiss="modal">
            Fechar
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';

const modalNovaCampanha = ref(null);

const abrirModal = () => {
  const modal = new bootstrap.Modal(modalNovaCampanha.value);
  modal.show();
};

export { abrirModal };
</script>