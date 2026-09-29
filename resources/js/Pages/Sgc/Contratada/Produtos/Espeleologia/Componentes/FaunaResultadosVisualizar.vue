<script setup>
import { ref } from 'vue';
import { defineProps, defineEmits } from 'vue';
import InputLabel from '@/Components/InputLabel.vue';
import NavButton from '@/Components/NavButton.vue';

const props = defineProps({
    resultadosTerrestre: Array,
    resultadosAquatica: Array,
    resultadosCavernicola: Array,
    consideracoes: String
});

defineEmits(['next','prev']);

const subtab = ref('terrestre');
</script>

<template>
<div class="card">
<div class="card-body">

<h4 class="mb-3 text-center">RESULTADOS</h4>

<!-- SUBTABS -->
<ul class="nav nav-tabs mb-4">
    <li class="nav-item">
        <a class="nav-link" :class="{active: subtab==='terrestre'}"
           @click.prevent="subtab='terrestre'">Fauna Terrestre</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" :class="{active: subtab==='aquatica'}"
           @click.prevent="subtab='aquatica'">Fauna Aquática</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" :class="{active: subtab==='cavernicola'}"
           @click.prevent="subtab='cavernicola'">Fauna Cavernícola</a>
    </li>
</ul>

<!-- ====================== TERRESTRE ====================== -->
<div v-if="subtab==='terrestre'">
<h5 class="text-center mb-3">Fauna Terrestre</h5>

<div v-if="resultadosTerrestre.length" class="table-responsive">
<table class="min-w-full bg-white border border-gray-300">
<thead>
<tr class="bg-gray-100">
    <th>ID</th>
    <th>Campanha</th>
    <th>Estação do Ano</th>
    <th>Data</th>
    <th>Horário</th>
    <th>Clima</th>
    <th>Temp (°C)</th>
    <th>Pluviosidade</th>
    <th>Município</th>
    <th>Unidade Amostral</th>
    <th>Ponto Amostral</th>
    <th>Latitude</th>
    <th>Longitude</th>
    <th>Metodologia</th>
    <th>Tipo Metodologia</th>
    <th>Fitofisionomia</th>
    <th>Habitat</th>
    <th>Características do Ponto</th>

    <!-- TAXONOMIA -->
    <th>Classe</th>
    <th>Ordem</th>
    <th>Família</th>
    <th>Gênero</th>
    <th>Espécie</th>
    <th>Nome Científico</th>
    <th>Nome Comum</th>

    <!-- Atributos -->
    <th>Abundância</th>
    <th>Sensibilidade</th>
    <th>Endemismo</th>
    <th>Observação</th>
    
    <!-- Status -->
    <th>IUCN</th>
    <th>MMA</th>
    <th>Salve</th>
    <th>Estado</th>

    <!-- Coleta -->
    <th>Registro Fotográfico</th>
    <th>Coletado</th>
    <th>Nº Tombo</th>
</tr>
</thead>

<tbody>
<tr v-for="r in resultadosTerrestre" :key="r.id">
    <td>{{ r.id }}</td>
    <td>{{ r.campanha }}</td>
    <td>{{ r.estacao_do_ano }}</td>
    <td>{{ r.data }}</td>
    <td>{{ r.horario }}</td>
    <td>{{ r.condicao_climatica }}</td>
    <td>{{ r.temperatura }}</td>
    <td>{{ r.pluviosidade }}</td>
    <td>{{ r.municipio }}</td>
    <td>{{ r.unidade_amostral }}</td>
    <td>{{ r.ponto_amostral }}</td>
    <td>{{ r.latitude }}</td>
    <td>{{ r.longitude }}</td>
    <td>{{ r.metodologia }}</td>
    <td>{{ r.tipo_metodologia }}</td>
    <td>{{ r.fitofisionomia }}</td>
    <td>{{ r.habitat }}</td>
    <td>{{ r.caracteristicas_ponto }}</td>

    <!-- TAXONOMIA -->
    <td>{{ r.classe }}</td>
    <td>{{ r.ordem }}</td>
    <td>{{ r.familia }}</td>
    <td>{{ r.genero }}</td>
    <td>{{ r.especie }}</td>
    <td>{{ r.nome_cientifico }}</td>
    <td>{{ r.nome_comum }}</td>

    <!-- ATRIBUTOS -->
    <td>{{ r.abundancia }}</td>
    <td>{{ r.sensibilidade }}</td>
    <td>{{ r.endemismo }}</td>
    <td>{{ r.observacao }}</td>

    <!-- STATUS -->
    <td>{{ r.iucn }}</td>
    <td>{{ r.mma }}</td>
    <td>{{ r.salve }}</td>
    <td>{{ r.estado }}</td>

    <!-- COLETA -->
    <td>{{ r.registro_fotografico }}</td>
    <td>{{ r.coletado }}</td>
    <td>{{ r.numero_tombo }}</td>
</tr>
</tbody>
</table>
</div>

<div v-else class="alert alert-info text-center">Nenhum registro terrestre.</div>
</div>

<!-- ====================== AQUÁTICA ====================== -->
<div v-if="subtab==='aquatica'">
<h5 class="text-center mb-3">Fauna Aquática</h5>

<div v-if="resultadosAquatica.length" class="table-responsive">
<table class="min-w-full bg-white border border-gray-300">
<thead>
<tr class="bg-gray-100">
    <th>ID</th>
    <th>Campanha</th>
    <th>Estação</th>
    <th>Data</th>
    <th>Horário</th>
    <th>Clima</th>
    <th>Temperatura</th>
    <th>Pluviosidade</th>
    <th>Município</th>
    <th>Unidade Amostral</th>
    <th>Ponto Amostral</th>
    <th>Latitude</th>
    <th>Longitude</th>

    <th>Metodologia</th>
    <th>Tipo Metodologia</th>
    <th>Fitofisionomia</th>

    <th>Habitat Preferencial</th>
    <th>Tipo de Ambiente</th>
    <th>Largura Médio Rio</th>
    <th>Profundidade Média</th>
    <th>Substrato</th>
    <th>Características da Água</th>
    <th>Características do Entorno</th>

    <!-- TAXONOMIA -->
    <th>Classe</th>
    <th>Ordem</th>
    <th>Família</th>
    <th>Gênero</th>
    <th>Espécie</th>
    <th>Nome Científico</th>
    <th>Nome Comum</th>

    <th>Abundância</th>
    <th>Sensibilidade</th>
    <th>Endemismo</th>
    <th>Observação</th>

    <th>IUCN</th>
    <th>MMA</th>
    <th>Salve</th>
    <th>Estado</th>

    <th>Registro Fotográfico</th>
    <th>Coletado</th>
    <th>Nº Tombo</th>
</tr>
</thead>

<tbody>
<tr v-for="r in resultadosAquatica" :key="r.id">
    <td>{{ r.id }}</td>
    <td>{{ r.campanha }}</td>
    <td>{{ r.estacao_do_ano }}</td>
    <td>{{ r.data }}</td>
    <td>{{ r.horario }}</td>
    <td>{{ r.condicao_climatica }}</td>
    <td>{{ r.temperatura }}</td>
    <td>{{ r.pluviosidade }}</td>
    <td>{{ r.municipio }}</td>
    <td>{{ r.unidade_amostral }}</td>
    <td>{{ r.ponto_amostral }}</td>
    <td>{{ r.latitude }}</td>
    <td>{{ r.longitude }}</td>

    <td>{{ r.metodologia }}</td>
    <td>{{ r.tipo_metodologia }}</td>
    <td>{{ r.fitofisionomia }}</td>

    <td>{{ r.habitat_preferencial }}</td>
    <td>{{ r.tipo_ambiente }}</td>
    <td>{{ r.largura_media_rio }}</td>
    <td>{{ r.profundidade_media }}</td>
    <td>{{ r.tipo_substrato }}</td>
    <td>{{ r.caracteristicas_agua }}</td>
    <td>{{ r.caracteristicas_entorno_ponto }}</td>

    <td>{{ r.classe }}</td>
    <td>{{ r.ordem }}</td>
    <td>{{ r.familia }}</td>
    <td>{{ r.genero }}</td>
    <td>{{ r.especie }}</td>
    <td>{{ r.nome_cientifico }}</td>
    <td>{{ r.nome_comum }}</td>

    <td>{{ r.abundancia }}</td>
    <td>{{ r.sensibilidade }}</td>
    <td>{{ r.endemismo }}</td>
    <td>{{ r.observacao }}</td>

    <td>{{ r.iucn }}</td>
    <td>{{ r.mma }}</td>
    <td>{{ r.salve }}</td>
    <td>{{ r.estado }}</td>

    <td>{{ r.registro_fotografico }}</td>
    <td>{{ r.coletado }}</td>
    <td>{{ r.numero_tombo }}</td>
</tr>
</tbody>
</table>
</div>

<div v-else class="alert alert-info text-center">Nenhum registro aquático.</div>
</div>

<!-- ====================== CAVERNÍCOLA ====================== -->
<div v-if="subtab==='cavernicola'">
<h5 class="text-center mb-3">Fauna Cavernícola</h5>

<div v-if="resultadosCavernicola.length" class="table-responsive">
<table class="min-w-full bg-white border border-gray-300">
<thead>
<tr class="bg-gray-100">
    <th>ID</th>
    <th>Caverna</th>
    <th>Campanha</th>
    <th>Estação</th>
    <th>Data</th>
    <th>Horário</th>
    <th>Clima</th>
    <th>Temp</th>
    <th>Pluviosidade</th>
    <th>Município</th>
    <th>Unidade Amostral</th>
    <th>Ponto Amostral</th>
    <th>Latitude</th>
    <th>Longitude</th>
    <th>Metodologia</th>
    <th>Tipo Metodologia</th>
    <th>Fitofisionomia</th>
    <th>Substrato</th>
    <th>Características Entorno</th>

    <!-- TAXONOMIA -->
    <th>Classe</th>
    <th>Ordem</th>
    <th>Família</th>
    <th>Gênero</th>
    <th>Especie</th>
    <th>Nome Científico</th>
    <th>Nome Comum</th>

    <!-- Atributos -->
    <th>Abundância</th>
    <th>Categoria Ecológica</th>
    <th>Sensibilidade</th>
    <th>Endemismo</th>
    <th>Observação</th>

    <th>Guano</th>
    <th>Presença de Água</th>
    <th>Conectividade Externa</th>
    <th>Perturbação Antropica</th>

    <th>IUCN</th>
    <th>MMA</th>
    <th>Salve</th>
    <th>Estado</th>

    <th>Registro Fotográfico</th>
    <th>Coletado</th>
    <th>Nº Tombo</th>
</tr>
</thead>

<tbody>
<tr v-for="r in resultadosCavernicola" :key="r.id">
    <td>{{ r.id }}</td>
    <td>{{ r.caverna }}</td>
    <td>{{ r.campanha }}</td>
    <td>{{ r.estacao_do_ano }}</td>
    <td>{{ r.data }}</td>
    <td>{{ r.horario }}</td>
    <td>{{ r.condicao_climatica }}</td>
    <td>{{ r.temperatura }}</td>
    <td>{{ r.pluviosidade }}</td>
    <td>{{ r.municipio }}</td>
    <td>{{ r.unidade_amostral }}</td>
    <td>{{ r.ponto_amostral }}</td>
    <td>{{ r.latitude }}</td>
    <td>{{ r.longitude }}</td>

    <td>{{ r.metodologia }}</td>
    <td>{{ r.tipo_metodologia }}</td>
    <td>{{ r.fitofisionomia }}</td>
    <td>{{ r.substrato_amostrado }}</td>
    <td>{{ r.caracteristicas_entorno_ponto }}</td>

    <td>{{ r.classe }}</td>
    <td>{{ r.ordem }}</td>
    <td>{{ r.familia }}</td>
    <td>{{ r.genero }}</td>
    <td>{{ r.especie }}</td>
    <td>{{ r.nome_cientifico }}</td>
    <td>{{ r.nome_comum }}</td>

    <td>{{ r.abundancia }}</td>
    <td>{{ r.categoria_ecologica }}</td>
    <td>{{ r.sensibilidade }}</td>
    <td>{{ r.endemismo }}</td>
    <td>{{ r.observacao }}</td>

    <td>{{ r.presenca_guano }}</td>
    <td>{{ r.presenca_agua }}</td>
    <td>{{ r.conectividade_externa }}</td>
    <td>{{ r.perturbacao_antropica }}</td>

    <td>{{ r.iucn }}</td>
    <td>{{ r.mma }}</td>
    <td>{{ r.salve }}</td>
    <td>{{ r.estado }}</td>

    <td>{{ r.registro_fotografico }}</td>
    <td>{{ r.coletado }}</td>
    <td>{{ r.numero_tombo }}</td>
</tr>
</tbody>
</table>
</div>

<div v-else class="alert alert-info text-center">Nenhum registro cavernícola.</div>
</div>

<!-- Considerações -->
<div class="mt-4">
    <InputLabel value="CONSIDERAÇÕES" />
    <textarea class="form-control" rows="4" :value="consideracoes || 'Não informado'" disabled></textarea>
</div>

<!-- Navegação -->
<div class="d-flex justify-content-between mt-4">
    <NavButton type-button="secondary" title="Voltar" @click="$emit('prev')" />
    <NavButton type-button="primary" title="Avançar" @click="$emit('next')" />
</div>

</div>
</div>
</template>

<style scoped>
.card { border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
.card-body { padding: 2rem; }
.nav-tabs .nav-link { cursor: pointer; }
table th, table td { padding: .5rem 1rem; border: 1px solid #ddd; }
tr:hover { background-color: #f9fafb; }
</style>
