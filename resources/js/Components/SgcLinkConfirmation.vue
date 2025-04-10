<script setup>
import Modal from '@/Components/Modal.vue';
import {ref} from "vue";
import {router} from "@inertiajs/vue3";
import {IconExclamationCircle} from "@tabler/icons-vue";

const modalRef = ref(null);
const cancelButton = ref(null);
const confirmButton = ref(null);

const defaultOptions = {
    title: 'Você tem certeza?',
    text: null,
    confirmButtonClass: 'btn-primary',
    confirmButtonText: 'Confirmar',
    cancelButtonClass: 'btn-secondary',
    cancelButtonText: 'Cancelar',
}

const props = defineProps({
    options: {
        type: Object, default: {}
    }
});

const mergedOptions = {...defaultOptions, ...props.options}

const show = (request, onConfirmCallback = null) => {
    const modal = modalRef.value.getBsModal();

    modal.show();

    // On Confirm
    confirmButton.value.addEventListener(
        'click',
        (e) => {
            // Aguarde o modal fechar antes de prosseguir
            modal._element.addEventListener(
                'hidden.bs.modal',
                () => {
                    router.visit(request.url, request);

                    // Executar callback, se fornecido
                    if (onConfirmCallback) {
                        onConfirmCallback();
                    }
                },
                {once: true}
            );

            // Esconde o modal
            modal.hide();
        },
        {once: true}
    );

    // On Cancel
    cancelButton.value.addEventListener('click', () => modal.hide(), {once: true});

    return false;
};



</script>

<template>

    <Modal ref="modalRef" modal-dialog-class="modal-dialog-centered">
        <template #body>
            <div class="d-flex align-items-center gap-2">
                <IconExclamationCircle class="text-warning" size="36"/>
                <h1 class="my-0"> {{ mergedOptions.title }}</h1>
            </div>
            <p class="fs-3 mt-4" v-if="mergedOptions.text">{{ mergedOptions.text }}</p>
        </template>
        <template #footer>
            <button class="btn btn-secondary" ref="cancelButton" type="button" :class="mergedOptions.cancelButtonClass">
                {{ mergedOptions.cancelButtonText }}
            </button>
            <button class="btn btn-primary" ref="confirmButton" type="button" :class="mergedOptions.confirmButtonClass">
                {{ mergedOptions.confirmButtonText }}
            </button>
        </template>
    </Modal>

    <slot :show="show"/>

</template>
