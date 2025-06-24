<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import NavButton from '@/Components/NavButton.vue';
import Table from '@/Components/Table.vue';
import vSelect from 'vue-select';
import 'vue-select/dist/vue-select.css';
import { ref, computed } from 'vue';

const props = defineProps({
    formDadosGerais: Object,
    formAbio: Object,
    formProfissional: Object,
    formNovoProfissional: Object,
    abios: {
        type: Array,
        default: () => []
    },
    profissionais: {
        type: Array,
        default: () => []
    },
    abioRecords: Array,
    profissionalRecords: Array,
});

defineEmits(['vincular-abio', 'excluir-abio', 'salvar-novo-profissional', 'vincular-profissional', 'excluir-profissional', 'next', 'prev']);

const showModalProfissional = ref(false);

const abioOptions = computed(() => {
    if (!props.abios) {
        console.warn('Props abios está indefinido ou vazio:', props.abios);
        return [];
    }
    return props.abios
        .filter(abio => abio?.licenca)
        .map(abio => ({
            ...abio,
            label: abio.licenca?.numero_licenca || 'Sem Licença',
        }));
});

const profissionalOptions = computed(() => {
    if (!props.profissionais) {
        console.warn('Props profissionais está indefinido ou vazio:', props.profissionais);
        return [];
    }
    return props.profissionais.map(p => ({
        value: p.profissional,
        label: `${p.profissional} (${p.formacao})`,
    }));
});

const grupoFaunisticoOptions = [
    { value: 'Avifauna', label: 'Avifauna' },
    { value: 'Herpetofauna', label: 'Herpetofauna' },
    { value: 'Mastofauna', label: 'Mastofauna' },
    { value: 'Ictiofauna', label: 'Ictiofauna' },
    { value: 'Bentos', label: 'Bentos' },
];
</script>

<template>
    <form @submit.prevent="$emit('next')">
        <!-- Dados Gerais -->
        <h4 class="mb-3">DADOS GERAIS</h4>
        <div class="mb-4">
            <div class="row mb-3">
                <div class="col-12 col-md-6">
                    <InputLabel value="Data Inicial" for="data_campanha_inicial" />
                    <input type="date" id="data_campanha_inicial" class="form-control" v-model="formDadosGerais.data_campanha_inicial" />
                    <InputError :message="formDadosGerais.errors.data_campanha_inicial" />
                </div>
                <div class="col-12 col-md-6">
                    <InputLabel value="Data Final" for="data_campanha_final" />
                    <input type="date" id="data_campanha_final" class="form-control" v-model="formDadosGerais.data_campanha_final" />
                    <InputError :message="formDadosGerais.errors.data_campanha_final" />
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-12">
                    <InputLabel value="Período" for="periodo" />
                    <div class="d-flex align-items-center">
                        <label class="form-check form-check-inline me-3">
                            <input class="form-check-input" type="radio" name="periodo" value="Seca" v-model="formDadosGerais.periodo" />
                            <span class="form-check-label">Seca</span>
                        </label>
                        <label class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="periodo" value="Chuva" v-model="formDadosGerais.periodo" />
                            <span class="form-check-label">Chuva</span>
                        </label>
                    </div>
                    <InputError :message="formDadosGerais.errors.periodo" />
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-12">
                    <InputLabel value="Observações" for="obs" />
                    <textarea id="obs" class="form-control" v-model="formDadosGerais.obs" rows="5"></textarea>
                    <InputError :message="formDadosGerais.errors.obs" />
                </div>
            </div>
        </div>

        <!-- Vincular ABIO -->
        <h4 class="mb-3">VINCULAR ABIO</h4>
        <div class="mb-4">
            <div class="row mb-3">
                <div class="col-12 col-md-10">
                    <InputLabel value="N° ABIO Vigente" for="id_abio" />
                    <v-select
                        :options="abioOptions"
                        :reduce="a => a.id"
                        v-model="formAbio.id_abio"
                        placeholder="Selecione um ABIO"
                        class="v-select-custom"
                    >
                        <template #no-options>
                            Nenhum registro encontrado.
                        </template>
                    </v-select>
                    <InputError :message="formAbio.errors.id_abio" />
                </div>
                <div class="col-12 col-md-2 d-flex align-items-end">
                    <NavButton type="button" type-button="success" title="Vincular Abio" class="w-100" @click="$emit('vincular-abio')" />
                </div>
            </div>
            <div class="table-responsive">
                <Table :columns="['ABIOs Vigentes', 'Ação']" :records="{ data: abioRecords, links: [] }">
                    <template #body="{ item }">
                        <tr>
                            <td>{{ item.abio?.licenca?.numero_licenca || 'N/A' }}</td>
                            <td class="text-center" style="min-width: 100px;">
                                <NavButton @click="$emit('excluir-abio', item.id)" type-button="danger" title="Excluir">
                                    <i class="bi bi-trash"></i>
                                </NavButton>
                            </td>
                        </tr>
                    </template>
                </Table>
            </div>
        </div>

        <!-- Vincular Profissionais -->
        <h4 class="mb-3">VINCULAR PROFISSIONAIS</h4>
        <div class="mb-4">
            <div class="row mb-3">
                <div class="col-12 col-md-5">
                    <InputLabel value="Selecionar Profissional" for="profissional" />
                    <v-select
                        v-model="formProfissional.profissional"
                        :options="profissionalOptions"
                        :reduce="p => p.value"
                        placeholder="Selecione um profissional"
                        class="v-select-custom"
                    />
                    <InputError :message="formProfissional.errors.profissional" />
                </div>
                <div class="col-12 col-md-5">
                    <InputLabel value="Grupo Responsável" for="grupo_faunistico" />
                    <v-select
                        v-model="formProfissional.grupo_faunistico"
                        :options="grupoFaunisticoOptions"
                        :reduce="g => g.value"
                        placeholder="Selecione um grupo"
                        class="v-select-custom"
                    />
                    <InputError :message="formProfissional.errors.grupo_faunistico" />
                </div>
                <div class="col-12 col-md-2 d-flex align-items-end">
                    <NavButton type="button" type-button="success" title="Vincular" class="w-100" @click="$emit('vincular-profissional')" />
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-12">
                    <NavButton type="button" type-button="primary" title="Cadastrar Profissional" @click="showModalProfissional = true" />
                </div>
            </div>
            <div class="table-responsive">
                <Table :columns="['Equipe Técnica', 'Formação', 'Grupo Responsável', 'Ação']" :records="{ data: profissionalRecords, links: [] }">
                    <template #body="{ item }">
                        <tr>
                            <td>{{ item.profissional || 'N/A' }}</td>
                            <td>{{ item.formacao || 'N/A' }}</td>
                            <td>{{ item.grupo_faunistico || 'N/A' }}</td>
                            <td class="text-center" style="min-width: 100px;">
                                <NavButton @click="$emit('excluir-profissional', item.id)" type-button="danger" title="Excluir">
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
            <NavButton type="submit" type-button="primary" title="Avançar" />
        </div>

        <!-- Modal para Cadastrar Profissional -->
        <div v-if="showModalProfissional" class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Cadastrar Profissional</h5>
                        <button type="button" class="btn-close" @click="showModalProfissional = false"></button>
                    </div>
                    <div class="modal-body">
                        <form @submit.prevent="$emit('salvar-novo-profissional')">
                            <div class="mb-3">
                                <InputLabel value="Nome do Profissional" for="profissional" />
                                <input v-model="formNovoProfissional.profissional" type="text" class="form-control" id="profissional" required />
                                <InputError :message="formNovoProfissional.errors.profissional" />
                            </div>
                            <div class="mb-3">
                                <InputLabel value="Formação" for="formacao" />
                                <input v-model="formNovoProfissional.formacao" type="text" class="form-control" id="formacao" required />
                                <InputError :message="formNovoProfissional.errors.formacao" />
                            </div>
                            <div class="d-flex justify-content-end">
                                <NavButton type="button" type-button="secondary" title="Cancelar" @click="showModalProfissional = false" class="me-2" />
                                <NavButton type="submit" type-button="primary" title="Salvar" />
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
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