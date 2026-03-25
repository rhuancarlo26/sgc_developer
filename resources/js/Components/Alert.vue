<script setup>

import {computed} from "vue";
import {BIconCheckCircle, BIconExclamationCircle, BIconXCircle, BIconInfoCircle} from "bootstrap-icons-vue";

const props = defineProps({
    type: {type: String, default: 'primary'},
    content: String
})

const type = props.type === 'error' ? 'danger' : props.type;

defineEmits(['closeButtonClicked'])

const alertIcon = computed(() => {
    return {
        info: BIconInfoCircle,
        danger: BIconXCircle,
        success: BIconCheckCircle,
        warning: BIconExclamationCircle
    }[type]
});

</script>

<template>
    <div class="p-4 shadow-sm" :class="`bg-${type} text-bg-${type}`" role="alert">
        <div class="d-flex justify-content-between align-items-center gap-2">
            <strong class="d-flex align-items-center gap-2">
                <component class="fs-3" v-if="alertIcon" :is="alertIcon"></component>
                {{ content }}
            </strong>
            <button type="button" class="btn btn-close" @click="$emit('closeButtonClicked')" aria-label="Fechar">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    </div>
</template>
