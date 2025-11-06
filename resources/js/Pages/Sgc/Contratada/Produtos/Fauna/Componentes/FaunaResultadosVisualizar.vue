<script setup>
import { defineProps, defineEmits } from 'vue';
import InputLabel from '@/Components/InputLabel.vue';
import NavButton from '@/Components/NavButton.vue';

defineProps({
    resultados: {
        type: Array,
        default: () => [],
    },
    consideracoes: {
        type: String,
        default: null,
    },
});

defineEmits(['next', 'prev']);
</script>

<template>
    <div class="card">
        <div class="card-body">
            <h4 class="mb-3" style="text-align: center;">RESULTADOS</h4>

            <!-- Tabela de Resultados -->
            <div v-if="resultados && resultados.length > 0" class="overflow-x-auto mb-6">
                <table class="min-w-full bg-white border border-gray-300">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="py-2 px-4 border-b text-left">ID</th>
                            <th class="py-2 px-4 border-b text-left">Módulo</th>
                            <th class="py-2 px-4 border-b text-left">Parcela</th>
                            <th class="py-2 px-4 border-b text-left">ID Armadilha</th>
                            <th class="py-2 px-4 border-b text-left">Grupo Amostrado</th>
                            <th class="py-2 px-4 border-b text-left">Data Registro</th>
                            <th class="py-2 px-4 border-b text-left">Hora Registro</th>
                            <th class="py-2 px-4 border-b text-left">Categoria</th>
                            <th class="py-2 px-4 border-b text-left">Classe</th>
                            <th class="py-2 px-4 border-b text-left">Ordem</th>
                            <th class="py-2 px-4 border-b text-left">Família</th>
                            <th class="py-2 px-4 border-b text-left">Gênero</th>
                            <th class="py-2 px-4 border-b text-left">Espécie</th>
                            <th class="py-2 px-4 border-b text-left">Nome Comum</th>
                            <th class="py-2 px-4 border-b text-left">Sexo</th>
                            <th class="py-2 px-4 border-b text-left">Faixa Etária</th>
                            <th class="py-2 px-4 border-b text-left">Quantidade</th>
                            <th class="py-2 px-4 border-b text-left">Nº Marcação</th>
                            <th class="py-2 px-4 border-b text-left">Coletado</th>
                            <th class="py-2 px-4 border-b text-left">Nº Tombamento</th>
                            <th class="py-2 px-4 border-b text-left">Dados Biométricos</th>
                            <th class="py-2 px-4 border-b text-left">Comp. Total (cm)</th>
                            <th class="py-2 px-4 border-b text-left">Cabeça (cm)</th>
                            <th class="py-2 px-4 border-b text-left">Cauda (cm)</th>
                            <th class="py-2 px-4 border-b text-left">Fêmur (cm)</th>
                            <th class="py-2 px-4 border-b text-left">Orelha (cm)</th>
                            <th class="py-2 px-4 border-b text-left">Peso (g)</th>
                            <th class="py-2 px-4 border-b text-left">Status Federal</th>
                            <th class="py-2 px-4 border-b text-left">Status IUCN</th>
                            <th class="py-2 px-4 border-b text-left">Espécies Bioindicadoras</th>
                            <th class="py-2 px-4 border-b text-left">Espécies Alvo de Monitoramento</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="resultado in resultados" :key="resultado.id" class="hover:bg-gray-50">
                            <td class="py-2 px-4 border-b">{{ resultado.id || 'Não informado' }}</td>
                            <td class="py-2 px-4 border-b">{{ resultado.modulo || 'Não informado' }}</td>
                            <td class="py-2 px-4 border-b">{{ resultado.parcela || 'Não informado' }}</td>
                            <td class="py-2 px-4 border-b">{{ resultado.id_armadilha || 'Não informado' }}</td>
                            <td class="py-2 px-4 border-b">{{ resultado.grupo_amostrado || 'Não informado' }}</td>
                            <td class="py-2 px-4 border-b">{{ resultado.data_registro || 'Não informado' }}</td>
                            <td class="py-2 px-4 border-b">{{ resultado.hora_registro || 'Não informado' }}</td>
                            <td class="py-2 px-4 border-b">{{ resultado.categoria || 'Não informado' }}</td>
                            <td class="py-2 px-4 border-b">{{ resultado.classe || 'Não informado' }}</td>
                            <td class="py-2 px-4 border-b">{{ resultado.ordem || 'Não informado' }}</td>
                            <td class="py-2 px-4 border-b">{{ resultado.familia || 'Não informado' }}</td>
                            <td class="py-2 px-4 border-b">{{ resultado.genero || 'Não informado' }}</td>
                            <td class="py-2 px-4 border-b">{{ resultado.especie || 'Não informado' }}</td>
                            <td class="py-2 px-4 border-b">{{ resultado.nome_comum || 'Não informado' }}</td>
                            <td class="py-2 px-4 border-b">{{ resultado.sexo || 'Não informado' }}</td>
                            <td class="py-2 px-4 border-b">{{ resultado.faixa_etaria || 'Não informado' }}</td>
                            <td class="py-2 px-4 border-b">{{ resultado.qnt_individuos || 'Não informado' }}</td>
                            <td class="py-2 px-4 border-b">{{ resultado.num_marcacao || 'Não informado' }}</td>
                            <td class="py-2 px-4 border-b">{{ resultado.coletado || 'Não informado' }}</td>
                            <td class="py-2 px-4 border-b">{{ resultado.num_tombamento || 'Não informado' }}</td>
                            <td class="py-2 px-4 border-b">{{ resultado.dados_biometricos || 'Não informado' }}</td>
                            <td class="py-2 px-4 border-b">{{ resultado.comp_total || 'Não informado' }}</td>
                            <td class="py-2 px-4 border-b">{{ resultado.cabeca || 'Não informado' }}</td>
                            <td class="py-2 px-4 border-b">{{ resultado.cauda || 'Não informado' }}</td>
                            <td class="py-2 px-4 border-b">{{ resultado.femur || 'Não informado' }}</td>
                            <td class="py-2 px-4 border-b">{{ resultado.orelha || 'Não informado' }}</td>
                            <td class="py-2 px-4 border-b">{{ resultado.peso || 'Não informado' }}</td>
                            <td class="py-2 px-4 border-b">{{ resultado.status_conservacao_federal || 'Não informado' }}</td>
                            <td class="py-2 px-4 border-b">{{ resultado.status_conservacao_iucn || 'Não informado' }}</td>
                            <td class="py-2 px-4 border-b">{{ resultado.especies_bioindicadoras || 'Não informado' }}</td>
                            <td class="py-2 px-4 border-b">{{ resultado.especies_alvo_monitoramento || 'Não informado' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div v-else class="alert alert-info text-center">
                Nenhum resultado disponível.
            </div>

            <!-- Considerações -->
            <div class="mb-6">
                <InputLabel value="CONSIDERAÇÕES" for="consideracoes" />
                <textarea
                    class="form-control"
                    id="consideracoes"
                    rows="4"
                    :value="consideracoes || 'Não informado'"
                    disabled
                ></textarea>
            </div>

            <!-- Navegação -->
            <div class="d-flex justify-content-between mt-4">
                <NavButton type="button" type-button="secondary" title="Voltar" @click="$emit('prev')" />
                <NavButton type="button" type-button="primary" title="Avançar" @click="$emit('next')" />
            </div>
        </div>
    </div>
</template>

<style scoped>
.card {
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    background-color: #fff;
    margin: 1.5rem;
}
.card-body {
    padding: 2rem;
}
.form-control:disabled {
    background-color: #f8f9fa;
    color: #495057;
    border: 1px solid #ced4da;
    border-radius: 4px;
    box-sizing: border-box;
    width: 100%;
    min-width: 200px;
    max-width: 100%;
    padding: 0.5rem 1rem;
    font-size: 1rem;
}
textarea.form-control:disabled {
    resize: none;
}
.alert-info {
    font-size: 1rem;
    padding: 1rem;
    border-radius: 6px;
    background-color: #e7f1ff;
    color: #084298;
}
table {
    border-collapse: collapse;
}
th, td {
    padding: 0.5rem 1rem;
    border: 1px solid #dee2e6;
}
thead {
    background-color: #f8f9fa;
}
tr:hover {
    background-color: #f1f5f9;
}
</style>