<script setup>
const props = defineProps({
    servico: {type: Object},
    lotes: {type: Array}
});
</script>
<template>
    <div class="mb-4">
        <h4>8. Tabela de empreendimentos</h4>
        <div class="card">
            <div class="table-responsive">
                <table class="table card-table table-bordered">
                    <thead>
                    <tr>
                        <th class="text-center">Tipo</th>
                        <th class="text-center">N° licença</th>
                        <th class="text-center">Empreendimento</th>
                        <th class="text-center">UF inicial</th>
                        <th class="text-center">UF final</th>
                        <th class="text-center">Rodovia</th>
                        <th class="text-center">Km inicial</th>
                        <th class="text-center">Km final</th>
                        <th class="text-center">Extensão</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="condicionante in servico.licencas_condicionantes" :key="condicionante.id">
                        <td>{{ condicionante.licenca?.tipo?.sigla }}</td>
                        <td>{{ condicionante.licenca?.numero_licenca }}</td>
                        <td>{{ condicionante.licenca?.empreendimento }}</td>
                        <td class="w-8">
                            <template v-if="condicionante.licenca?.iniciais">
                                  <span v-for="uf in condicionante.licenca?.iniciais.split(',')" :key="uf"
                                        class="badge bg-warning text-white m-1">
                                    {{ uf }}
                                  </span>
                            </template>
                        </td>
                        <td class="w-8">
                            <template v-if="condicionante.licenca?.finais">
                                  <span v-for="uf in condicionante.licenca?.finais.split(',')" :key="uf"
                                        class="badge bg-warning text-white m-1">
                                    {{ uf }}
                                  </span>
                            </template>
                        </td>
                        <td class="w-8">
                            <template v-if="condicionante.licenca?.brs">
                                  <span v-for="br in condicionante.licenca?.brs.split(',')" :key="br"
                                        class="badge bg-warning text-white m-1">
                                    {{ br }}
                                  </span>
                            </template>
                        </td>
                        <td>
                            <template v-if="condicionante.licenca?.segmentos.length">
                                {{
                                    Math.min(...condicionante.licenca?.segmentos.map(segmento => segmento.km_inicio))
                                }}
                            </template>
                        </td>
                        <td>
                            <template v-if="condicionante.licenca?.segmentos.length">
                                {{ Math.max(...condicionante.licenca?.segmentos.map(segmento => segmento.km_fim)) }}
                            </template>
                        </td>
                        <td>{{ condicionante.licenca?.extensao }}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="mb-4">
        <h4>9. Dados dos lotes de obra</h4>
        <div class="card">
            <div class="table-responsive">
                <table class="table card-table table-bordered">
                    <thead>
                    <tr>
                        <th class="text-center">ID lote</th>
                        <th class="text-center">Nome</th>
                        <th class="text-center">BR</th>
                        <th class="text-center">UF</th>
                        <th class="text-center">Km inicial</th>
                        <th class="text-center">Km final</th>
                        <th class="text-center">Construtora / Consórcio (lote de obra)</th>
                        <th class="text-center">N° contrato (lote de obra)</th>
                        <th class="text-center">Situação (lote de obra)</th>
                        <th class="text-center">Supervisora de obra</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="lote in lotes" :key="lote.id">
                        <td>{{ lote.nome_id }}</td>
                        <td>{{ lote.nome }}</td>
                        <td>{{ lote.rodovia?.rodovia }}</td>
                        <td>{{ lote.uf?.uf }}</td>
                        <td>{{ lote.km_inicial }}</td>
                        <td>{{ lote.km_final }}</td>
                        <td>{{ lote.empresa }}</td>
                        <td>{{ lote.num_contrato }}</td>
                        <td>{{ lote.situacao_contrato }}</td>
                        <td>{{ lote.supervisora_obras }}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>
