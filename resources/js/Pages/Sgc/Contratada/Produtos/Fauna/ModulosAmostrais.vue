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
    formModuloAmostral: Object,
    ufs: Array,
    biomas: Array,
    moduloRecords: Array,
});

defineEmits(['get-localizacao', 'adicionar-modulo', 'excluir-modulo', 'next', 'prev']);

// Estado para gerenciar municípios
const municipiosOptions = ref([]);
const loadingMunicipios = ref(false);

// Função para buscar municípios com base na UF selecionada
const fetchMunicipios = async (uf) => {
    if (!uf) {
        municipiosOptions.value = [];
        props.formModuloAmostral.municipio = null;
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
        props.formModuloAmostral.municipio = null;
    } catch (error) {
        console.error('Erro ao buscar municípios:', error);
        municipiosOptions.value = [];
        alert('Erro ao carregar os municípios. Tente novamente.');
    } finally {
        loadingMunicipios.value = false;
    }
};

// Observar mudanças na UF para buscar municípios
watch(() => props.formModuloAmostral.uf, (newUf) => {
    fetchMunicipios(newUf);
});
</script>

<template>
    <form @submit.prevent="$emit('next')">
        <h4 class="mb-3" style="text-align: center;">MÓDULOS AMOSTRAIS</h4>
        <div class="mb-4">
            <div class="row mb-4">
                <div class="col-12 col-md-6">
                    <InputLabel value="ID Módulo Amostral" for="id_modulo" />
                    <input disabled type="text" class="form-control" :value="formModuloAmostral.id" />
                    <InputError :message="formModuloAmostral.errors.id" />
                </div>
                <div class="col-12 col-md-6">
                    <InputLabel value="Data" for="data_cadastro" />
                    <input type="date" class="form-control" v-model="formModuloAmostral.data_cadastro" />
                    <InputError :message="formModuloAmostral.errors.data_cadastro" />
                </div>
            </div>
            <div class="row mb-4">
                <div class="col-12 col-md-6">
                    <InputLabel value="Selecionar o tamanho do Módulo Amostral" for="tamanho_modulo" />
                    <div>
                        <label class="form-check form-check-inline me-3">
                            <input class="form-check-input" type="radio" name="tamanho_modulo" value="5" v-model="formModuloAmostral.tamanho_modulo" />
                            <span class="form-check-label">5km</span>
                        </label>
                        <label class="form-check form-check-inline me-3">
                            <input class="form-check-input" type="radio" name="tamanho_modulo" value="4" v-model="formModuloAmostral.tamanho_modulo" />
                            <span class="form-check-label">4km</span>
                        </label>
                        <label class="form-check form-check-inline me-3">
                            <input class="form-check-input" type="radio" name="tamanho_modulo" value="3" v-model="formModuloAmostral.tamanho_modulo" />
                            <span class="form-check-label">3km</span>
                        </label>
                        <label class="form-check form-check-inline me-3">
                            <input class="form-check-input" type="radio" name="tamanho_modulo" value="2" v-model="formModuloAmostral.tamanho_modulo" />
                            <span class="form-check-label">2km</span>
                        </label>
                        <label class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="tamanho_modulo" value="1" v-model="formModuloAmostral.tamanho_modulo" />
                            <span class="form-check-label">1km</span>
                        </label>
                    </div>
                    <InputError :message="formModuloAmostral.errors.tamanho_modulo" />
                </div>
                <div class="col-12 col-md-6 d-flex align-items-end">
                    <InputLabel :value="`Nº parcelas: ${formModuloAmostral.tamanho_modulo || 0} parcela(s)`" />
                </div>
            </div>
            <div class="row mb-4">
                <div class="col-12 col-md-4">
                    <InputLabel value="UF" for="uf" />
                    <v-select
                        @option:selected="$emit('get-localizacao')"
                        :options="ufs"
                        label="uf"
                        v-model="formModuloAmostral.uf"
                        :reduce="uf => uf.uf"
                        placeholder="Selecione uma UF"
                    >
                        <template #no-options>
                            Nenhum registro encontrado.
                        </template>
                    </v-select>
                    <InputError :message="formModuloAmostral.errors.uf" />
                </div>
                <div class="col-12 col-md-4">
                    <InputLabel value="Municípios" for="municipio" />
                    <v-select
                        v-model="formModuloAmostral.municipio"
                        :options="municipiosOptions"
                        :reduce="mun => mun.value"
                        placeholder="Selecione um município"
                        :disabled="loadingMunicipios || !formModuloAmostral.uf"
                    >
                        <template #no-options>
                            {{ loadingMunicipios ? 'Carregando...' : 'Selecione uma UF primeiro.' }}
                        </template>
                    </v-select>
                    <InputError :message="formModuloAmostral.errors.municipio" />
                </div>
                <div class="col-12 col-md-4">
                    <InputLabel value="Bioma" for="bioma" />
                    <v-select
                        :options="biomas"
                        v-model="formModuloAmostral.bioma"
                        placeholder="Selecione um bioma"
                    >
                        <template #no-options>
                            Nenhum registro encontrado.
                        </template>
                    </v-select>
                    <InputError :message="formModuloAmostral.errors.bioma" />
                </div>
            </div>
            <div class="row mb-4">
                <div class="col-12">
                    <InputLabel value="Fitofisionomia" for="fitofisionomia" />
                    <textarea class="form-control" id="fitofisionomia" rows="5" v-model="formModuloAmostral.fitofisionomia"></textarea>
                    <InputError :message="formModuloAmostral.errors.fitofisionomia" />
                </div>
            </div>
            <div class="row mb-4">
                <div class="col-12 col-md-6">
                    <InputLabel value="Latitude inicial" for="latitude_inicial" />
                    <input type="number" placeholder="-12.2001" step="any" class="form-control" id="latitude_inicial" v-model="formModuloAmostral.latitude_inicial" />
                    <InputError :message="formModuloAmostral.errors.latitude_inicial" />
                </div>
                <div class="col-12 col-md-6">
                    <InputLabel value="Longitude inicial" for="longitude_inicial" />
                    <input type="number" placeholder="-38.2001" step="any" class="form-control" id="longitude_inicial" v-model="formModuloAmostral.longitude_inicial" />
                    <InputError :message="formModuloAmostral.errors.longitude_inicial" />
                </div>
            </div>
            <div class="row mb-4">
                <div class="col-12 col-md-6">
                    <InputLabel value="Latitude final" for="latitude_final" />
                    <input type="number" placeholder="-12.2001" step="any" class="form-control" id="latitude_final" v-model="formModuloAmostral.latitude_final" />
                    <InputError :message="formModuloAmostral.errors.latitude_final" />
                </div>
                <div class="col-12 col-md-6">
                    <InputLabel value="Longitude final" for="longitude_final" />
                    <input type="number" placeholder="-38.2001" step="any" class="form-control" id="longitude_final" v-model="formModuloAmostral.longitude_final" />
                    <InputError :message="formModuloAmostral.errors.longitude_final" />
                </div>
            </div>
            <div class="row mb-4">
                <div class="col-12">
                    <InputLabel value="Shapefile" for="arquivo" />
                    <input @input="formModuloAmostral.arquivo = $event.target.files[0]" type="file" class="form-control" id="arquivo" accept=".shp,.zip" />
                    <InputError :message="formModuloAmostral.errors.arquivo" />
                </div>
            </div>
            <div class="row mb-4">
                <div class="col-12">
                    <InputLabel value="Observações" for="obs_modulo" />
                    <textarea id="obs_modulo" rows="5" class="form-control" v-model="formModuloAmostral.obs"></textarea>
                    <InputError :message="formModuloAmostral.errors.obs" />
                </div>
            </div>
            <div class="row mb-4">
                <div class="col d-flex justify-content-end">
                    <NavButton type="button" type-button="success" title="Adicionar Módulo" @click="$emit('adicionar-modulo')" />
                </div>
            </div>
            <div class="table-responsive">
                <Table :columns="['Data', 'Tamanho', 'UF', 'Município', 'Bioma', 'Ação']" :records="{ data: moduloRecords, links: [] }">
                    <template #body="{ item }">
                        <tr>
                            <td>{{ item.data_cadastro || 'N/A' }}</td>
                            <td>{{ item.tamanho_modulo ? `${item.tamanho_modulo}km` : 'N/A' }}</td>
                            <td>{{ item.uf || 'N/A' }}</td>
                            <td>{{ item.municipio || 'N/A' }}</td>
                            <td>{{ item.bioma || 'N/A' }}</td>
                            <td class="text-center" style="min-width: 100px;">
                                <NavButton @click="$emit('excluir-modulo', item.id)" type-button="danger" title="Excluir">
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