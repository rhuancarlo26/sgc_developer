<script setup>
import {dateTimeFormat} from "../../../../../Utils/DateTimeUtils.js";

const props = defineProps({
    analise: {type: Object},
    acas: {type: Object}
});
</script>
<template>
    <div class="mb-4">
        <h4>8. Tabela de empreendimentos</h4>
        <div class="card mb-4">
            <div class="table-responsive">
                <table class="table card-table table-bordered">
                    <thead>
                    <tr>
                        <th class="text-center">ID ACA</th>
                        <th class="text-center">Data do ACA</th>
                        <th class="text-center">Relação de RNC's</th>
                        <th class="text-center">Local do RNC</th>
                        <th class="text-center">Classificação do RNC</th>
                        <th class="text-center">Lote</th>
                        <th class="text-center">Empresa / Consórcio</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="aca in acas" :key="aca.id">
                        <td>{{ aca.nome_id }}</td>
                        <td>{{ dateTimeFormat(aca.data_aca) }}</td>
                        <td>
                                <span v-for="rnc in aca.rncs" :key="rnc.id"
                                      class="badge bg-warning text-white m-1">
                                    {{ rnc.nome_id }}
                                </span>
                        </td>
                        <td>
                            <span v-for="local in [...new Set(aca.rncs.map(rnc => rnc.local))]" :key="local.id"
                                  class="badge bg-warning text-white m-1">
                                    {{ local }}
                                </span>
                        </td>
                        <td>
                            <span v-for="classificacao in [...new Set(aca.rncs.map(rnc => rnc.classificacao))]"
                                  :key="classificacao.id"
                                  class="badge bg-warning text-white m-1">
                                    {{ classificacao }}
                                </span>
                        </td>
                        <td>{{ aca.lote?.nome_id }}</td>
                        <td>{{ aca.lote?.empresa }}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <span><strong>Análise: </strong>{{ analise?.aca_gerados }}</span>
            </div>
        </div>
    </div>
</template>
