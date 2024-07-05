<script setup>
defineProps({
  ponto: { type: Object }
})
</script>
<template>
  <div class="row mb-4">
    <div class="col">
      <span><strong>ID do ponto: </strong>{{ ponto.ponto?.id }}</span>
    </div>
    <div class="col">
      <span><strong>Nome: </strong>{{ ponto.ponto?.nomepontocoleta }}</span>
    </div>
  </div>
  <div class="row mb-4">
    <div class="col">
      <span><strong>Latitude: </strong>{{ ponto.ponto?.lat_x }}</span>
    </div>
    <div class="col">
      <span><strong>Longitude: </strong>{{ ponto.ponto?.long_y }}</span>
    </div>
    <div class="col">
      <span><strong>Classificação: </strong>{{ ponto.ponto?.classificacao }}</span>
    </div>
  </div>
  <div class="row mb-4">
    <div class="col">
      <span><strong>Classe: </strong>{{ ponto.ponto?.classe }}</span>
    </div>
    <div class="col">
      <span><strong>Tipo de ambiente: </strong>{{ ponto.ponto?.tipoambiente }}</span>
    </div>
    <div class="col">
      <span><strong>UF: </strong>{{ ponto.ponto?.uf }}</span>
    </div>
  </div>
  <div class="row mb-4">
    <div class="col">
      <span><strong>Município: </strong>{{ ponto.ponto?.municipio }}</span>
    </div>
    <div class="col">
      <span><strong>Bacia: </strong>{{ ponto.ponto?.baciahidrografica }}</span>
    </div>
    <div class="col">
      <span><strong>Km da rodovia: </strong>{{ ponto.ponto?.km_rodovia }}</span>
    </div>
    <div class="col">
      <span><strong>Estaca: </strong>{{ ponto.ponto?.municipio }}</span>
    </div>
  </div>
  <div class="row mb-4">
  </div>
  <div class="row mb-4">
    <div class="col">
      <span><strong>Observações: </strong>{{ ponto.ponto?.observacoes }}</span>
    </div>
  </div>
</template>