<script setup>
import {dateTimeFormat} from "@/Utils/DateTimeUtils";

const props = defineProps({
    contrato: {type: Object},
    relatorio: {type: Object}
});
</script>
<template>
    <div class="d-flex flex-column justify-content-between min-vh-100">
        <div class="text-center">
            <div class="mb-4">
                <span><strong>{{ contrato.contratada }}</strong></span>
            </div>
            <div class="d-flex justify-content-center">
                <div class="px-2">
                    <p v-if="contrato.ufs">
            <span v-for="uf in contrato.ufs.split(',')" :key="uf" class="badge bg-warning text-white m-1">
              {{ uf }}
            </span>
                    </p>
                </div>

                <div class="px-2">
                    <p v-if="contrato.brs">
            <span v-for="br in contrato.brs.split(',')" :key="br" class="badge bg-warning text-white m-1">
              {{ br }}
            </span>
                    </p>
                </div>
            </div>
        </div>
        <div class="text-center">
            <h1>{{ relatorio.nome_relatorio }}</h1>
        </div>
        <div class="text-center">
            <span><strong>{{ dateTimeFormat(relatorio.created_at) }}</strong></span>
        </div>
    </div>
</template>
