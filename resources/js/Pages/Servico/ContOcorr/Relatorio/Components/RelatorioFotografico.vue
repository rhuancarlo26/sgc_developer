<script setup>
import {dateTimeFormat} from "../../../../../Utils/DateTimeUtils.js";
import {usePage} from "@inertiajs/vue3";

const props = defineProps({
    ocorrencias: {type: Array}
})
</script>

<template>
    <div class="mb-4">
        <h4>12. Registro fotográfico</h4>
        <div class="card">
            <div class="table-responsive">
                <table class="table card-table table-bordered">
                    <thead>
                    <tr>
                        <th class="text-center">ID</th>
                        <th class="text-center">Data da ocorrência</th>
                        <th class="text-center">Intensidade</th>
                        <th class="text-center">Tipo</th>
                        <th class="text-center">Local</th>
                        <th class="text-center">Classificação</th>
                        <th class="text-center">Corrigido</th>
                        <th class="text-center">Imagens</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="ocorrencia in ocorrencias" :key="ocorrencia.id">
                        <td>{{ ocorrencia.nome_id }}</td>
                        <td>{{ dateTimeFormat(ocorrencia.data_ocorrencia) }}</td>
                        <td>{{ ocorrencia.intensidade }}</td>
                        <td>{{ ocorrencia.tipo }}</td>
                        <td>{{ ocorrencia.local }}</td>
                        <td>{{ ocorrencia.classificacao }}</td>
                        <td>{{ ocorrencia.vistorias[0]?.corrigido }}</td>
                        <td>
                            <div v-for="imagem in ocorrencia.registros" :key="imagem.id">
                                <div v-if="['png','jpeg'].includes(imagem.nome.split('.')[1])">
                                    <img :src="usePage().props.app_url + '/storage/' + imagem.caminho_arquivo"
                                         alt="Gráfico">
                                </div>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<style scoped>

</style>
