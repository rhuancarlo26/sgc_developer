<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import NavButton from '@/Components/NavButton.vue';
import Table from '@/Components/Table.vue';
import vSelect from 'vue-select';
import 'vue-select/dist/vue-select.css';

defineProps({
    formMetodologia: Object,
    metodologiaRecords: Array,
});

defineEmits(['adicionar-metodologia', 'excluir-metodologia', 'prev', 'salvar']);

const grupoFaunisticoOptions = [
    { value: 'Avifauna', label: 'Avifauna' },
    { value: 'Herpetofauna', label: 'Herpetofauna' },
    { value: 'Mastofauna', label: 'Mastofauna' },
    { value: 'Ictiofauna', label: 'Ictiofauna' },
    { value: 'Bentos', label: 'Bentos' },
];
</script>

<template>
    <form @submit.prevent="$emit('adicionar-metodologia')">
        <h4 class="mb-3" style="text-align: center;">METODOLOGIA</h4>
        <div class="mb-4">
            <div class="row mb-3">
                <div class="col-12 col-md-8">
                    <InputLabel value="Grupo Faunístico" for="grupo_faunistico" />
                    <v-select
                        v-model="formMetodologia.grupo_faunistico"
                        :options="grupoFaunisticoOptions"
                        :reduce="g => g.value"
                        placeholder="Selecione um grupo"
                        class="v-select-custom"
                    />
                    <InputError :message="formMetodologia.errors.grupo_faunistico" />
                </div>
                <div class="col-15 col-md-8" style="margin-top: 1.5rem;">
                    <InputLabel value="Metodologia" for="metodologia" />
                    <textarea
                        v-model="formMetodologia.metodologia"
                        id="metodologia"
                        class="form-control"
                        rows="4"
                        placeholder="Descreva a metodologia"
                    ></textarea>
                    <InputError :message="formMetodologia.errors.metodologia" />
                </div>
                <div class="col-12 col-md-1 d-flex align-items-end">
                    <NavButton type="submit" type-button="success" title="Inserir" class="w-100" />
                </div>
            </div>
            <div class="table-responsive">
                <Table :columns="['Grupo Faunístico', 'Metodologia', 'Ação']" :records="{ data: metodologiaRecords, links: [] }">
                    <template #body="{ item }">
                        <tr>
                            <td>{{ item.grupo_faunistico }}</td>
                            <td>{{ item.metodologia }}</td>
                            <td class="text-center" style="min-width: 100px;">
                                <NavButton @click="$emit('excluir-metodologia', item.id)" type-button="danger" title="Excluir">
                                    <i class="bi bi-trash"></i>
                                </NavButton>
                            </td>
                        </tr>
                    </template>
                </Table>
            </div>
        </div>
        <div class="d-flex justify-content-between">
            <NavButton type="button" type-button="secondary" title="Voltar" @click="$emit('prev')" />
            <NavButton type="button" type-button="primary" title="Salvar" @click="$emit('salvar')" />
        </div>
        <slot name="footer"></slot>
    </form>
</template>

<style scoped>
.v-select-custom {
    width: 100%;
}
.v-select-custom :deep(.vs__dropdown-toggle) {
    border: 1px solid #ced4da;
    border-radius: 0.25rem;
    padding: 0.375rem 0.75rem;
}
.v-select-custom :deep(.vs__selected) {
    margin: 2px;
}
.table-responsive {
    margin-bottom: 1rem;
}
</style>