<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import NavButton from '@/Components/NavButton.vue';
import Table from '@/Components/Table.vue';

defineProps({
    formPontosAmostragem: Object,
    naoSeAplica: Boolean, // Prop recebida como valor
    pontoRecords: Array,
});

defineEmits(['adicionar-ponto', 'excluir-ponto', 'next', 'prev', 'update:naoSeAplica']); // Adiciona evento de atualização
</script>

<template>
    <form @submit.prevent="$emit('next')">
        <h4 class="mb-3" style="text-align: center;">QUELÔNIOS E CROCODILIANOS</h4>
        <!-- <h4 class="mb-3">1.2 Área de Amostragem</h4>
        <h5 class="mb-3">1.2.3 Pontos de Quelônios e Crocodilianos</h5> -->
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
                    <InputLabel value="Ponto de Coleta" for="ponto_de_coleta" />
                    <input type="text" id="ponto_de_coleta" class="form-control" v-model="formPontosAmostragem.ponto_de_coleta" :disabled="naoSeAplica" />
                    <InputError :message="formPontosAmostragem.errors.ponto_de_coleta" />
                </div>
                <div class="col-12 col-md-6">
                    <InputLabel value="Nome do Curso Hídrico" for="nome_curso_hidrico" />
                    <input type="text" id="nome_curso_hidrico" class="form-control" v-model="formPontosAmostragem.nome_curso_hidrico" :disabled="naoSeAplica" />
                    <InputError :message="formPontosAmostragem.errors.nome_curso_hidrico" />
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-12 col-md-6">
                    <InputLabel value="Latitude" for="latitude" />
                    <input type="text" id="latitude" class="form-control" v-model="formPontosAmostragem.latitude" :disabled="naoSeAplica" />
                    <InputError :message="formPontosAmostragem.errors.latitude" />
                </div>
                <div class="col-12 col-md-6">
                    <InputLabel value="Longitude" for="longitude" />
                    <input type="text" id="longitude" class="form-control" v-model="formPontosAmostragem.longitude" :disabled="naoSeAplica" />
                    <InputError :message="formPontosAmostragem.errors.longitude" />
                </div>
                <div class="col-12 col-md-6">
                    <InputLabel value="Bacia Hidrográfica" for="bacia" />
                    <input type="text" id="bacia" class="form-control" v-model="formPontosAmostragem.bacia" :disabled="naoSeAplica" />
                    <InputError :message="formPontosAmostragem.errors.bacia" />
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-12 col-md-6">
                    <InputLabel value="Profundidade (m)" for="profundidade" />
                    <input type="number" step="any" id="profundidade" class="form-control" v-model="formPontosAmostragem.profundidade" :disabled="naoSeAplica" />
                    <InputError :message="formPontosAmostragem.errors.profundidade" />
                </div>
                <div class="col-12 col-md-6">
                    <InputLabel value="Largura (m)" for="largura" />
                    <input type="number" step="any" id="largura" class="form-control" v-model="formPontosAmostragem.largura" :disabled="naoSeAplica" />
                    <InputError :message="formPontosAmostragem.errors.largura" />
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-12">
                    <InputLabel value="Tipo de Substrato" for="tipo_substrato" />
                    <input type="text" id="tipo_substrato" class="form-control" v-model="formPontosAmostragem.tipo_substrato" :disabled="naoSeAplica" />
                    <InputError :message="formPontosAmostragem.errors.tipo_substrato" />
                </div>
            </div>
            <div class="row mb-4">
                <div class="col d-flex justify-content-end">
                    <NavButton type="button" type-button="success" title="Adicionar Ponto" @click="$emit('adicionar-ponto')" :disabled="naoSeAplica" />
                </div>
            </div>
            <div class="table-responsive">
                <Table :columns="['Ponto de Coleta', 'Curso Hídrico', 'Bacia', 'Largura (m)', 'Ação']" :records="{ data: pontoRecords, links: [] }">
                    <template #body="{ item }">
                        <tr>
                            <td>{{ item.ponto_de_coleta || 'N/A' }}</td>
                            <td>{{ item.nome_curso_hidrico || 'N/A' }}</td>
                            <td>{{ item.bacia || 'N/A' }}</td>
                            <td>{{ item.largura || 'N/A' }}</td>
                            <td class="text-center" style="min-width: 100px;">
                                <NavButton @click="$emit('excluir-ponto', item.id)" type-button="danger" title="Excluir">
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