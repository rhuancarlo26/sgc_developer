<script setup>
</script>
<template>
  <div id="a4">
    <slot />
  </div>
</template>
<style scoped>
#a4 {
  width: 210mm;
  min-height: 297mm;
  border: 1px solid #000;
  margin: auto;
  box-shadow: 0 0 5mm rgba(0, 0, 0, 0.5);
  background: white;
  padding: 20mm;
}
</style>