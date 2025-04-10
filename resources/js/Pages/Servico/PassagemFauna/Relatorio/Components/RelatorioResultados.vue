<script setup>
import {computed} from "vue";

const props = defineProps({
    relatorio: {type: Object},
    servico: {type: Object}
});

const numTotalIndividuos = computed(() => {
    return props.relatorio.resultado.campanhas.reduce((acumuladorCampanha, campanha) => {
        return acumuladorCampanha + campanha.registros.reduce((acumulador, item) => acumulador + item.n_individuos, 0);
    }, 0);
});
</script>
<template>
    <div>
        <div class="mb-4">
            <h4>11. Resultados</h4>
            <h3>11.1 Tabela com o registro identificados das espécies nas passagens de fauna</h3>
            <div class="card">
                <div class="table-responsive">
                    <table class="table card-table table-bordered">
                        <thead>
                        <tr>
                            <th class="text-center" rowspan="2">Espécie</th>
                            <th class="text-center" rowspan="2">Nome comum</th>
                            <th class="text-center" rowspan="2">N° indivíduos</th>
                            <th class="text-center" rowspan="2">Frequência (%)</th>
                            <th class="text-center" colspan="2">Status conservação</th>
                        </tr>
                        <tr>
                            <th class="text-center">IUCN</th>
                            <th class="text-center">FEDERAL</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="registro in relatorio.resultado?.campanhas?.map(campanha => campanha.registros).flat()"
                            :key="registro.id">
                            <td class="text-center">{{ registro.especie }}</td>
                            <td class="text-center">{{ registro.nome_comum }}</td>
                            <td class="text-center">{{ registro.n_individuos }}</td>
                            <td class="text-center">
                                {{ parseFloat(registro.n_individuos * 100 / numTotalIndividuos).toFixed(4) }}%
                            </td>
                            <td class="text-center">
                                {{ `${registro.status_iunc?.sigla} - ${registro.status_iunc?.nome}` }}
                            </td>
                            <td class="text-center">
                                {{ `${registro.status_federal?.sigla} - ${registro.status_federal?.nome}` }}
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div>
            <h3>Análise</h3>
        </div>
    </div>
</template>
