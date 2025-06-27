<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import NavButton from '@/Components/NavButton.vue';
import Table from '@/Components/Table.vue';

defineProps({
    formPontosCavernicola: Object,
    naoSeAplica: Boolean,
    pontoCavernicolaRecords: Array,
});

defineEmits(['adicionar-ponto-cavernicola', 'excluir-ponto-cavernicola', 'next', 'prev', 'update:naoSeAplica']);
</script>

<template>
    <form @submit.prevent="$emit('next')">
        <h4 class="mb-3" style="text-align: center;">FAUNA CAVERNÍCOLA</h4>
        <!-- <h4 class="mb-3">1.2 Área de Amostragem</h4>
        <h5 class="mb-3">1.2.4 Pontos de Fauna Cavernícola</h5> -->
        <div class="mb-4">
            <div class="form-check mb-3">
                <input
                    type="checkbox"
                    class="form-check-input"
                    id="nao_se_aplica"
                    :value="naoSeAplica"
                    @input="$emit('update:naoSeAplica', $event.target.checked)"
                />
                <label class="form-check-label" for="nao_se_aplica">Não se aplica</label>
            </div>
            <div class="row mb-3">
                <div class="col-12 col-md-6">
                    <InputLabel value="Cavidade" for="cavidade" />
                    <input type="text" id="cavidade" class="form-control" v-model="formPontosCavernicola.cavidade" :disabled="naoSeAplica" />
                    <InputError :message="formPontosCavernicola.errors.cavidade" />
                </div>
                <div class="col-12 col-md-6">
                    <InputLabel value="Latitude" for="latitude" />
                    <input type="number" step="any" id="latitude" class="form-control" v-model="formPontosCavernicola.latitude" :disabled="naoSeAplica" />
                    <InputError :message="formPontosCavernicola.errors.latitude" />
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-12 col-md-6">
                    <InputLabel value="Longitude" for="longitude" />
                    <input type="number" step="any" id="longitude" class="form-control" v-model="formPontosCavernicola.longitude" :disabled="naoSeAplica" />
                    <InputError :message="formPontosCavernicola.errors.longitude" />
                </div>
                <div class="col-12 col-md-6">
                    <InputLabel value="Distância do Eixo da Rodovia (m)" for="distancia_eixo_rodovia" />
                    <input type="number" step="any" id="distancia_eixo_rodovia" class="form-control" v-model="formPontosCavernicola.distancia_eixo_rodovia" :disabled="naoSeAplica" />
                    <InputError :message="formPontosCavernicola.errors.distancia_eixo_rodovia" />
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-12">
                    <InputLabel value="Formação Associada" for="formacao_associada" />
                    <input type="text" id="formacao_associada" class="form-control" v-model="formPontosCavernicola.formacao_associada" :disabled="naoSeAplica" />
                    <InputError :message="formPontosCavernicola.errors.formacao_associada" />
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-12 col-md-6">
                    <InputLabel value="Temperatura Média Interna (°C)" for="temperatura_media_interna" />
                    <input type="number" step="any" id="temperatura_media_interna" class="form-control" v-model="formPontosCavernicola.temperatura_media_interna" :disabled="naoSeAplica" />
                    <InputError :message="formPontosCavernicola.errors.temperatura_media_interna" />
                </div>
                <div class="col-12 col-md-6">
                    <InputLabel value="Temperatura Média Externa (°C)" for="temperatura_media_externa" />
                    <input type="number" step="any" id="temperatura_media_externa" class="form-control" v-model="formPontosCavernicola.temperatura_media_externa" :disabled="naoSeAplica" />
                    <InputError :message="formPontosCavernicola.errors.temperatura_media_externa" />
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-12 col-md-6">
                    <InputLabel value="Umidade Relativa Interna (%)" for="umidade_relativa_interna" />
                    <input type="number" step="any" id="umidade_relativa_interna" class="form-control" v-model="formPontosCavernicola.umidade_relativa_interna" :disabled="naoSeAplica" />
                    <InputError :message="formPontosCavernicola.errors.umidade_relativa_interna" />
                </div>
                <div class="col-12 col-md-6">
                    <InputLabel value="Umidade Relativa Externa (%)" for="umidade_relativa_externa" />
                    <input type="number" step="any" id="umidade_relativa_externa" class="form-control" v-model="formPontosCavernicola.umidade_relativa_externa" :disabled="naoSeAplica" />
                    <InputError :message="formPontosCavernicola.errors.umidade_relativa_externa" />
                </div>
            </div>
            <div class="row mb-4">
                <div class="col d-flex justify-content-end">
                    <NavButton type="button" type-button="success" title="Adicionar Ponto" @click="$emit('adicionar-ponto-cavernicola')" :disabled="naoSeAplica" />
                </div>
            </div>
            <div class="table-responsive">
                <Table :columns="['Cavidade', 'Latitude', 'Longitude', 'Distância (m)', 'Formação', 'Ação']" :records="{ data: pontoCavernicolaRecords, links: [] }">
                    <template #body="{ item }">
                        <tr>
                            <td>{{ item.cavidade || 'N/A' }}</td>
                            <td>{{ item.latitude || '' }}</td>
                            <td>{{ item.longitude || '' }}</td>
                            <td>{{ item.distancia_eixo_rodovia || '' }}</td>
                            <td>{{ item.formacao_associada || '' }}</td>
                            <td class="text-center" align="center" style="min-width: 100px;">
                                <NavButton @click="$emit('excluir-ponto-cavernicola', item.id)" type-button="danger" title="Excluir">
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
.table-responsive {
    margin-bottom: 1rem;
}
</style>