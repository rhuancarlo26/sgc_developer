<script setup>
import RimaCreate from '../Rima/Create.vue';

defineProps({
    contrato: [String, Number],
    produto: { type: String, default: 'ASV' },
    contratos: { type: Object, default: () => ({ contratada: 'Contratada', tipo_contrato: null }) },
    modulos: { type: Array, default: () => [] },
    empreendimentos: { type: Array, default: () => [] },
    subproduto: { type: [String, Number], default: null },
});
</script>

<template>
    <RimaCreate
        :contrato="contrato"
        :produto="produto"
        :contratos="contratos"
        :modulos="modulos"
        :empreendimentos="empreendimentos"
        :subproduto="subproduto"
    />
</template>
