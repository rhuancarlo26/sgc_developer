<template>
    <form @submit.prevent="search()" class="row justify-content-between align-items-center">
        <div class="col-lg-4 input-icon mb-4">
            <input ref="inputSearch" v-model="q.value" @input="delayedSearch" placeholder="..." type="text"
                class="form-control" :disabled="q.processing">
            <span class="input-icon-addon">
                <IconSearch class="me-4" />
            </span>
        </div>
        <div class="col-lg-4 mb-2 text-end">
            <slot name="action" />
        </div>
    </form>
</template>

<style scoped></style>
<script setup>
import { IconSearch } from "@tabler/icons-vue";
import { useForm } from "@inertiajs/vue3";
import { onMounted, ref, onUpdated } from "vue";

const props = defineProps({
    columns: { type: Array }
});

let q = useForm({ columns: '', value: null });
let searchTimer = ref(null);
let inputSearch = ref(null);

onMounted(() => {
    q.columns = props.columns.join(',');
    q.value = route().params.value;
});

const delayedSearch = () => {
    clearTimeout(searchTimer.value);
    searchTimer.value = setTimeout(() => {
        search();
    }, 2000);
};

const search = () => {
    if (!q.processing) {
        q.get(route(route().current(), route().params));
    }
};

onUpdated(() => {
    if (!q.processing) {
        inputSearch.value.focus();
    }
});
</script>
