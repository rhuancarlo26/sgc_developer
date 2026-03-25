<script setup>
defineProps({
  ponto: { type: Object }
})
</script>
<template>
  <div class="row mb-4">
    <div class="col-12">
      <h5 class="mb-3">Informações Básicas</h5>
      <div class="row">
        <div class="col-2">
          <span><strong>ID do ponto: </strong>{{ ponto.ponto?.id }}</span>
        </div>
        <div class="col-2">
          <span><strong>Nome: </strong>{{ ponto.ponto?.nome_ponto_coleta }}</span>
        </div>
      </div>
    </div>
  </div>

  <hr class="my-4">

  <div class="row mb-4">
    <div class="col-12">
      <h5 class="mb-3">Localização</h5>
      <div class="row">
        <div class="col">
          <span><strong>Latitude: </strong>{{ ponto.ponto?.lat_x }}</span>
        </div>
        <div class="col">
          <span><strong>Longitude: </strong>{{ ponto.ponto?.long_y }}</span>
        </div>
        <div class="col">
          <span><strong>UF: </strong>{{ ponto.ponto?.UF }}</span>
        </div>
        <div class="col">
          <span><strong>Município: </strong>{{ ponto.ponto?.municipio }}</span>
        </div>
      </div>
    </div>
  </div>

  <hr class="my-4">

  <div class="row mb-4">
    <div class="col-12">
      <h5 class="mb-3">Classificação e Ambiente</h5>
      <div class="row">
        <div class="col">
          <span><strong>Classificação: </strong>{{ ponto.ponto?.classificacao }}</span>
        </div>
        <div class="col">
          <span><strong>Classe: </strong>{{ ponto.ponto?.classe }}</span>
        </div>
        <div class="col">
          <span><strong>Tipo de ambiente: </strong>{{ ponto.ponto?.tipo_ambiente }}</span>
        </div>
        <div class="col">
          <span><strong>Bacia: </strong>{{ ponto.ponto?.bacia_hidrografica }}</span>
        </div>
      </div>
    </div>
  </div>

  <hr class="my-4">

  <div class="row mb-4">
    <div class="col-12">
      <h5 class="mb-3">Informações da Rodovia</h5>
      <div class="row">
        <div class="col">
          <span><strong>Km da rodovia: </strong>{{ ponto.ponto?.km_rodovia }}</span>
        </div>
        <div class="col">
          <span><strong>Estaca: </strong>{{ ponto.ponto?.municipio }}</span>
        </div>
      </div>
    </div>
  </div>

  <hr class="my-4">

  <div class="row mb-4">
    <div class="col-12">
      <h5 class="mb-3">Observações</h5>
      <div class="row">
        <div class="col">
          <span><strong>Observações: </strong>{{ ponto.ponto?.observacoes }}</span>
        </div>
      </div>
    </div>
  </div>
</template>
