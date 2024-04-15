<script setup>
import { IconSearch } from "@tabler/icons-vue";
import { useForm } from "@inertiajs/vue3";
import { onMounted } from "vue";

const props = defineProps({
    columns: []
});

let q = useForm({ columns: '', value: null });

onMounted(() => {
    q.columns = props.columns.join(',');
    q.value = route().params.value;
})

const search = () => {
    if (!q.processing) {
        q.get(route(route().current(), route().params));
    }
}

</script>

<template>
    <form @submit.prevent="search()" class="d-flex justify-content-end">
        <div class="col-lg-4 input-icon mb-4">
            <input v-model="q.value" placeholder="..." type="text" class="form-control" :disabled="q.processing">
            <span class="input-icon-addon">
                <IconSearch class="me-4" />
            </span>
        </div>
    </form>
</template>

<style scoped></style>
