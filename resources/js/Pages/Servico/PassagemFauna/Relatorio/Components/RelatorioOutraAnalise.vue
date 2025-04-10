<script setup>
import {usePage} from "@inertiajs/vue3";

const props = defineProps({
    relatorio: {type: Object},
    servico: {type: Object}
});
</script>
<template>
    <div>
        <div v-for="(analise,index) in relatorio.resultado?.outras_analises" :key="analise.id" class="mb-4">
            <h4>12. Outras análises</h4>
            <h4>{{ `12.${(index + 1)} - ${analise.nome}` }}</h4>
            <div>
                <img :src="usePage().props.app_url + '/storage/' + analise.caminho_arquivo"
                     alt="">
            </div>
            <div>
                <span><strong>análise: </strong>{{ analise.analise }}</span>
            </div>
        </div>
    </div>
</template>
