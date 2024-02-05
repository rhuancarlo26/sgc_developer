<script setup>
import { onMounted, ref } from "vue";

defineProps({
    title: String,
    modalDialogClass: String,
    modalClass: String,
    options: Object
})

const modalEl = ref(null);
let bootstrapModal = null;

onMounted(() => {
    bootstrapModal = new bootstrap.Modal(modalEl.value, {})
})

function getBsModal() {
    return bootstrapModal;
}

function getElement() {
    return modalEl.value;
}


defineExpose({ getBsModal, getElement });

</script>


<template>
    <div class="modal fade" id="teste" ref="modalEl" tabindex="-1" aria-hidden="true" :class="modalClass">
        <div class="modal-dialog" :class="`${modalDialogClass}`">
            <div class="modal-content">
                <div class="modal-header" v-if="title">
                    <h5 class="modal-title">
                        {{ title }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <slot name="body" />
                </div>
                <div class="modal-footer" v-if="$slots.footer">
                    <slot name="footer" />
                </div>
            </div>
        </div>
    </div>
</template>
