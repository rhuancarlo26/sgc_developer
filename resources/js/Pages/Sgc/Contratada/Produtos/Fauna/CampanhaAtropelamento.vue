<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import NavButton from '@/Components/NavButton.vue';
import Table from '@/Components/Table.vue';
import vSelect from 'vue-select';
import 'vue-select/dist/vue-select.css';
import { ref, watch } from 'vue';
import axios from 'axios';

const props = defineProps({
    formCampanha: Object,
    idCampanha: [String, Number],
    ufs: Array,
    campanhaRecords: Array,
});

defineEmits(['adicionar-campanha', 'excluir-campanha', 'next', 'prev']);

// Estado para gerenciar municípios
const municipiosOptions = ref([]);
const loadingMunicipios = ref(false);

// Função para buscar municípios com base na UF selecionada
const fetchMunicipios = async (uf) => {
    if (!uf) {
        municipiosOptions.value = [];
        props.formCampanha.municipio = null;
        return;
    }

    loadingMunicipios.value = true;
    try {
        const response = await axios.get(`https://servicodados.ibge.gov.br/api/v1/localidades/estados/${uf}/municipios`);
        municipiosOptions.value = response.data.map(mun => ({
            value: mun.nome,
            label: mun.nome,
        }));
        // Resetar o município selecionado ao mudar a UF
        props.formCampanha.municipio = null;
    } catch (error) {
        console.error('Erro ao buscar municípios:', error);
        municipiosOptions.value = [];
        alert('Erro ao carregar os municípios. Tente novamente.');
    } finally {
        loadingMunicipios.value = false;
    }
};

// Observar mudanças na UF para buscar municípios
watch(() => props.formCampanha.uf_inicial, (newUf) => {
    fetchMunicipios(newUf);
});
</script>

<template>
    <form @submit.prevent="$emit('next')">
        <h4 class="mb-3" style="text-align: center;">CAMPANHA DE ATROPELAMENTO</h4>
        <div class="mb-4">
            <div class="row mb-4">
                <div class="col-12 col-md-6">
                    <InputLabel value="ID Campanha" for="id_campanha" />
                    <input disabled type="text" class="form-control" :value="idCampanha" />
                    <InputError :message="formCampanha.errors.id_campanha" />
                </div>
                <div class="col-12 col-md-6">
                    <InputLabel value="Rodovia" for="rodovia" />
                    <input type="text" class="form-control" v-model="formCampanha.rodovia" placeholder="Ex: BR-101, MG-123" />
                    <InputError :message="formCampanha.errors.rodovia" />
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-12 col-md-6">
                    <InputLabel value="Data Inicial" for="data_inicial" />
                    <input type="date" class="form-control" v-model="formCampanha.data_inicial" />
                    <InputError :message="formCampanha.errors.data_inicial" />
                </div>
                <div class="col-12 col-md-6">
                    <InputLabel value="Data Final" for="data_final" />
                    <input type="date" class="form-control" v-model="formCampanha.data_final" />
                    <InputError :message="formCampanha.errors.data_final" />
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-12 col-md-6">
                    <InputLabel value="UF Inicial" for="uf_inicial" />
                    <v-select
                        :options="ufs"
                        label="uf"
                        v-model="formCampanha.uf_inicial"
                        :reduce="uf => uf.uf"
                        placeholder="Selecione uma UF"
                    >
                        <template #no-options>
                            Nenhum registro encontrado.
                        </template>
                    </v-select>
                    <InputError :message="formCampanha.errors.uf_inicial" />
                </div>
                <div class="col-12 col-md-6">
                    <InputLabel value="UF Final" for="uf_final" />
                    <v-select
                        :options="ufs"
                        label="uf"
                        v-model="formCampanha.uf_final"
                        :reduce="uf => uf.uf"
                        placeholder="Selecione uma UF"
                    >
                        <template #no-options>
                            Nenhum registro encontrado.
                        </template>
                    </v-select>
                    <InputError :message="formCampanha.errors.uf_final" />
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-12 col-md-6">
                    <InputLabel value="KM Inicial" for="km_inicial" />
                    <input type="number" placeholder="0" step="any" class="form-control" v-model="formCampanha.km_inicial" />
                    <InputError :message="formCampanha.errors.km_inicial" />
                </div>
                <div class="col-12 col-md-6">
                    <InputLabel value="KM Final" for="km_final" />
                    <input type="number" placeholder="0" step="any" class="form-control" v-model="formCampanha.km_final" />
                    <InputError :message="formCampanha.errors.km_final" />
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-12 col-md-6">
                    <InputLabel value="Latitude Inicial" for="latitude_inicial" />
                    <input type="number" placeholder="-12.2001" step="any" class="form-control" v-model="formCampanha.latitude_inicial" />
                    <InputError :message="formCampanha.errors.latitude_inicial" />
                </div>
                <div class="col-12 col-md-6">
                    <InputLabel value="Longitude Inicial" for="longitude_inicial" />
                    <input type="number" placeholder="-38.2001" step="any" class="form-control" v-model="formCampanha.longitude_inicial" />
                    <InputError :message="formCampanha.errors.longitude_inicial" />
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-12 col-md-6">
                    <InputLabel value="Latitude Final" for="latitude_final" />
                    <input type="number" placeholder="-12.2001" step="any" class="form-control" v-model="formCampanha.latitude_final" />
                    <InputError :message="formCampanha.errors.latitude_final" />
                </div>
                <div class="col-12 col-md-6">
                    <InputLabel value="Longitude Final" for="longitude_final" />
                    <input type="number" placeholder="-38.2001" step="any" class="form-control" v-model="formCampanha.longitude_final" />
                    <InputError :message="formCampanha.errors.longitude_final" />
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-12">
                    <InputLabel value="Observações" for="obs" />
                    <textarea id="obs" rows="5" class="form-control" v-model="formCampanha.obs"></textarea>
                    <InputError :message="formCampanha.errors.obs" />
                </div>
            </div>

            <div class="row mb-4">
                <div class="col d-flex justify-content-end">
                    <NavButton type="button" type-button="success" title="Adicionar Campanha" @click="$emit('adicionar-campanha')" />
                </div>
            </div>

            <div class="table-responsive">
                <Table :columns="['Rodovia', 'Data Inicial', 'Data Final', 'UF Inicial', 'UF Final', 'KM Inicial', 'KM Final', 'Ação']" :records="{ data: campanhaRecords, links: [] }">
                    <template #body="{ item }">
                        <tr>
                            <td>{{ item.rodovia || 'N/A' }}</td>
                            <td>{{ item.data_inicial || 'N/A' }}</td>
                            <td>{{ item.data_final || 'N/A' }}</td>
                            <td>{{ item.uf_inicial || 'N/A' }}</td>
                            <td>{{ item.uf_final || 'N/A' }}</td>
                            <td>{{ item.km_inicial || 'N/A' }}</td>
                            <td>{{ item.km_final || 'N/A' }}</td>
                            <td class="text-center" style="min-width: 100px;">
                                <NavButton @click="$emit('excluir-campanha', item.id)" type-button="danger" title="Excluir">
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
