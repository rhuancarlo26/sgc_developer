<script setup>
import RimaEditCampanha from '../Rima/EditCampanha.vue';
defineProps({ campanha: Object, modulos: Array, empreendimentos: Array, subprodutos: Array, contrato: [String, Number], produto: String, contratos: Object });
</script>
<template><RimaEditCampanha :campanha="campanha" :modulos="modulos" :empreendimentos="empreendimentos" :subprodutos="subprodutos" :contrato="contrato" :produto="produto" :contratos="contratos" /></template>
