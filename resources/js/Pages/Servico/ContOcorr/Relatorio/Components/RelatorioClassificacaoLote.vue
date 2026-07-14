<script setup>
import {usePage} from "@inertiajs/vue3";

const props = defineProps({
    analise: {type: Object}
})
</script>

<template>
    <div>
        <h2>10. Resultados</h2>
        <div class="mb-4">
            <h4>10.5 Gráfico do número de ocorrências por classificação</h4>
            <img :src="usePage().props.app_url + '/storage/' + analise?.graf_reg_classificacao" alt="Gráfico">

            <div>
                <span><strong>Análise: </strong>{{ analise?.ocorr_por_classificacao }}</span>
            </div>
        </div>
        <div>
            <h4>10.6 Gráfico do número de ocorrências por lote</h4>
            <img :src="usePage().props.app_url + '/storage/' + analise?.graf_reg_lote" alt="Gráfico">

            <div>
                <span><strong>Análise: </strong>{{ analise?.ocorr_por_lote }}</span>
            </div>
        </div>
    </div>
</template>

<style scoped>

</style>
