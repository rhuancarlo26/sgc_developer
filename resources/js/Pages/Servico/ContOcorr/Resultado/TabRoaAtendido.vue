<script setup>
import {dateTimeFormat} from "@/Utils/DateTimeUtils.js";

const props = defineProps({
    roa_atendido: {type: Array}
});
</script>

<template>
    <div class="row mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table card-table table-bordered table-hover">
                    <thead>
                    <tr>
                        <th>ID ocorrência</th>
                        <th>Intensidade</th>
                        <th>Local ocorrência</th>
                        <th>Classificação ocorrência</th>
                        <th>Data da ocorrência</th>
                        <th>Lote</th>
                        <th>Empresa / Consórcio</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="roa in roa_atendido" :key="roa.id">
                        <td>{{ roa.nome_id }}</td>
                        <td>{{ roa.intensidade }}</td>
                        <td>{{ roa.local }}</td>
                        <td>{{ roa.classificacao }}</td>
                        <td>{{ dateTimeFormat(roa.data_ocorrencia) }}</td>
                        <td>{{ roa.lote?.nome_id }}</td>
                        <td>{{ roa.lote?.empresa }}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<style scoped>

</style>
