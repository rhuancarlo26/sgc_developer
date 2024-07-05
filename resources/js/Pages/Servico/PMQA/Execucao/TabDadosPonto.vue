<script setup>
defineProps({
  ponto: { type: Object }
})
</script>
<template>
  <div class="row mb-4">
    <div class="col">
      <span><strong>ID do ponto: </strong>{{ ponto.id }}</span>
    </div>
    <div class="col">
      <span><strong>Nome: </strong>{{ ponto.nomepontocoleta }}</span>
    </div>
  </div>
  <div class="row mb-4">
    <div class="col">
      <span><strong>Latitude: </strong>{{ ponto.lat_x }}</span>
    </div>
    <div class="col">
      <span><strong>Longitude: </strong>{{ ponto.long_y }}</span>
    </div>
    <div class="col">
      <span><strong>Classificação: </strong>{{ ponto.classificacao }}</span>
    </div>
  </div>
  <div class="row mb-4">
    <div class="col">
      <span><strong>Classe: </strong>{{ ponto.classe }}</span>
    </div>
    <div class="col">
      <span><strong>Tipo de ambiente: </strong>{{ ponto.tipoambiente }}</span>
    </div>
    <div class="col">
      <span><strong>UF: </strong>{{ ponto.uf }}</span>
    </div>
  </div>
  <div class="row mb-4">
    <div class="col">
      <span><strong>Município: </strong>{{ ponto.municipio }}</span>
    </div>
    <div class="col">
      <span><strong>Bacia: </strong>{{ ponto.baciahidrografica }}</span>
    </div>
    <div class="col">
      <span><strong>Km da rodovia: </strong>{{ ponto.km_rodovia }}</span>
    </div>
    <div class="col">
      <span><strong>Estaca: </strong>{{ ponto.municipio }}</span>
    </div>
  </div>
  <div class="row mb-4">
  </div>
  <div class="row mb-4">
    <div class="col">
      <span><strong>Observações: </strong>{{ ponto.observacoes }}</span>
    </div>
  </div>
</template>