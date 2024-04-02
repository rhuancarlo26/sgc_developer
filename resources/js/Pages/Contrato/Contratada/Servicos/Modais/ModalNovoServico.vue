<script setup>
import { ref, defineExpose } from 'vue';

const abrirModal = () => {
    const modal = document.getElementById("modalNovoServico");
    console.log("modal: ", modal)
    if (modal) {
        modal.classList.add("show");
        modal.style.display = "block"; // Certifique-se de definir o estilo de exibição como "block"
    }
}

defineExpose({ abrirModal });
</script>

<template>
    <!-- Modal -->
    <div class="modal fade" id="modalNovoServico" data-bs-backdrop="static" data-bs-keyboard="false" 
    tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title" id="modalNovoServicoLabel">Novo Serviço</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    ...
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Understood</button>
                </div>
            </div>
        </div>
    </div>
</template>
