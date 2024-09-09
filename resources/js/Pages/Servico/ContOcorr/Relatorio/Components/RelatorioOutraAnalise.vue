<script setup>
import {usePage} from "@inertiajs/vue3";

const props = defineProps({
    outrasAnalises: {type: Array}
})
</script>

<template>
    <div>
        <h2>11. Outras análises</h2>
        <div class="mb-4" v-for="(outraAnalise, index) in outrasAnalises" :key="outraAnalise.id">
            <h4>11.{{ index + 1 }} {{ outraAnalise.nome }}</h4>
            <div v-if="['jpeg', 'png'].includes(outraAnalise.extensao)">
                <img :src="usePage().props.app_url + '/storage/' + outraAnalise.caminho_arquivo" alt="Gráfico">
            </div>

            <div>
                <span><strong>Análise: </strong>{{ outraAnalise.analise }}</span>
            </div>
        </div>
    </div>
</template>

<style scoped>

</style>
