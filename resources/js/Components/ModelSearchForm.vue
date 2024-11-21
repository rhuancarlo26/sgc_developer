<script setup>
import { IconEraser, IconSearch } from "@tabler/icons-vue";
import { useForm } from "@inertiajs/vue3";
import { onMounted } from "vue";

const props = defineProps({
    searchColumns: {}
});

let searchForm = useForm({ searchColumn: "", searchValue: null });

onMounted(() => {
    searchForm.searchColumn = route().params.searchColumn ?? "";
    searchForm.searchValue = route().params.searchValue;
})

const search = () => {
    searchForm.get(route(route().current(), route().params));
}

</script>

<template>
    <form @submit.prevent="search" class="row align-items-center">
        <div class="col-lg-4 mb-2">
            <select class="form-select" v-model="searchForm.searchColumn">
                <option value="" disabled>Pesquisar por</option>
                <option v-for="(label, column) in searchColumns" :key="label + column" :value="column">{{ label }}</option>
            </select>
        </div>
        <div class="col-lg-4 mb-2">
            <input v-model="searchForm.searchValue" placeholder="..."
                :type="['created_at', 'updated_at', 'deleted_at'].includes(searchForm.searchColumn) ? 'date' : 'text'"
                class="form-control">
        </div>
        <div class="col-lg-4 mb-2 text-end">
            <slot name="action" />

            <button type="button" @click="searchForm.reset() && search()" class="btn btn-secondary ms-2"
                title="Limpar Filtros">
                <IconEraser />
            </button>
            <button class="btn btn-primary ms-2" title="Pesquisar" :disabled="searchForm.processing">
                <IconSearch />
            </button>
        </div>
    </form>
</template>

<style scoped></style>
