<script setup>
import {dateTimeFormat} from "@/Utils/DateTimeUtils.js";

const props = defineProps({
    analise: {type: Object},
    rncsAtendidos: {type: Array},
    rncsEmAberto: {type: Array}
});
</script>
<template>
    <h2>10. Resultados</h2>
    <div class="mb-4">
        <h4>10.3 Tabela com os RNC's atendidos no período</h4>
        <div class="card mb-4">
            <div class="table-responsive">
                <table class="table card-table table-bordered">
                    <thead>
                    <tr>
                        <th class="text-center">ID ocorrência</th>
                        <th class="text-center">Intensidade</th>
                        <th class="text-center">Local ocorrência</th>
                        <th class="text-center">Classificação ocorrência</th>
                        <th class="text-center">Data da ocorrência</th>
                        <th class="text-center">Lote</th>
                        <th class="text-center">Empresa / Consórcio</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="roa in rncsAtendidos" :key="roa.id">
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
        <div class="row">
            <div class="col">
                <span><strong>Análise: </strong>{{ analise?.rncs_atendidos }}</span>
            </div>
        </div>
    </div>
    <div>
        <h4>10.4 Tabela com os RNC's em aberto no período</h4>
        <div class="card mb-4">
            <div class="table-responsive">
                <table class="table card-table table-bordered">
                    <thead>
                    <tr>
                        <th class="text-center">ID ocorrência</th>
                        <th class="text-center">Intensidade</th>
                        <th class="text-center">Local ocorrência</th>
                        <th class="text-center">Classificação ocorrência</th>
                        <th class="text-center">Data da ocorrência</th>
                        <th class="text-center">Lote</th>
                        <th class="text-center">Empresa / Consórcio</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="roa in rncsEmAberto" :key="roa.id">
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
        <div class="row">
            <div class="col">
                <span><strong>Análise: </strong>{{ analise?.rncs_em_aberto }}</span>
            </div>
        </div>
    </div>
</template>
