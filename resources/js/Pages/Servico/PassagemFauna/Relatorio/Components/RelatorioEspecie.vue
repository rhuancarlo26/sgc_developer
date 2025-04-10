<script setup>
import {usePage} from "@inertiajs/vue3";

const props = defineProps({
    relatorio: {type: Object},
    servico: {type: Object}
});
</script>
<template>
    <div>
        <div class="mb-4">
            <h4>11. Resultados</h4>
            <h3>11.6 Gráfico com o número de animais registrados por espécie</h3>
            <div>
                <img :src="usePage().props.app_url + '/storage/' + relatorio.resultado?.analise?.graf_reg_especie"
                     alt="">
            </div>
            <div>
                <span><strong>análise: </strong>{{ relatorio.resultado?.analise?.registros_especie }}</span>
            </div>
        </div>
    </div>
</template>
