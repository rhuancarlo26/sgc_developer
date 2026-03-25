<script setup>
import Modal from "@/Components/Modal.vue";
import { dateTimeFormat } from "@/Utils/DateTimeUtils";
import { ref } from "vue";

const registro = ref({
  id: null,
  nome_registro: '',
  id_frente: null,
  id_grupo_amostrado: null,
  data_registro: null,
  hora_registro: null,
  id_estado: null,
  km: '',
  latitude: '',
  longitude: '',
  sentido: '',
  margem: '',
  classe: null,
  ordem: '',
  familia: '',
  genero: '',
  especie: '',
  nome_comum: '',
  categoria: '',
  sexo: '',
  faixa_etaria: '',
  n_individuos: '',
  id_forma_registro: null,
  id_tipo_registro: null,
  id_destinacao_registro: null,
  latitude_soltura: '',
  longitude_soltura: '',
  nome_local: '',
  coletado: '',
  n_registro_tombamento: '',
  id_status_conservacao_federal: null,
  id_status_conservacao_iucn: null,
})

const modalDetalhes = ref(null);

const abrirModal = (item) => {
  registro.value = item;

  modalDetalhes.value.getBsModal().show();
}

defineExpose({ abrirModal });
</script>

<template>
  <Modal ref="modalDetalhes" :title="'Registro de ' + registro.nome_registro" modal-dialog-class="modal-xl">
    <template #body>
      <div class="row mb-4">
        <div class="col">
          <span><strong>Nome do Registro: </strong>{{ registro.nome_registro }}</span>
        </div>
        <div class="col">
          <span><strong>Data do Registro: </strong>{{ dateTimeFormat(registro.data_registro) }}</span>
        </div>
        <div class="col">
          <span><strong>Hora do Registro: </strong>{{ registro.hora_registro }}</span>
        </div>
      </div>
      <div class="row mb-4">
        <div class="col">
          <span><strong>Estado: </strong>{{ registro.uf }}</span>
        </div>
        <div class="col">
          <span><strong>Km: </strong>{{ registro.km }}</span>
        </div>
        <div class="col">
          <span><strong>Latitude: </strong>{{ registro.latitude }}</span>
        </div>
      </div>
      <div class="row mb-4">
        <div class="col">
          <span><strong>Longitude: </strong>{{ registro.longitude }}</span>
        </div>
        <div class="col">
          <span><strong>Sentido: </strong>{{ registro.sentido }}</span>
        </div>
        <div class="col">
          <span><strong>Margem: </strong>{{ registro.margem }}</span>
        </div>
      </div>
      <div class="row mb-4">
        <div class="col">
          <span><strong>Classe: </strong>{{ registro.classe }}</span>
        </div>
        <div class="col">
          <span><strong>Ordem: </strong>{{ registro.ordem }}</span>
        </div>
        <div class="col">
          <span><strong>Família: </strong>{{ registro.familia }}</span>
        </div>
      </div>
      <div class="row mb-4">
        <div class="col">
          <span><strong>Gênero: </strong>{{ registro.genero }}</span>
        </div>
        <div class="col">
          <span><strong>Espécie: </strong>{{ registro.especie }}</span>
        </div>
        <div class="col">
          <span><strong>Nome Comum: </strong>{{ registro.nome_comum }}</span>
        </div>
      </div>
      <div class="row mb-4">
        <div class="col">
          <span><strong>Categoria: </strong>{{ registro.categoria }}</span>
        </div>
        <div class="col">
          <span><strong>Sexo: </strong>{{ registro.sexo }}</span>
        </div>
        <div class="col">
          <span><strong>Faixa Etária: </strong>{{ registro.faixa_etaria }}</span>
        </div>
      </div>
      <div class="row mb-4">
        <div class="col">
          <span><strong>Número de Indivíduos: </strong>{{ registro.n_individuos }}</span>
        </div>
        <div class="col">
          <span><strong>Forma de Registro: </strong>{{ registro.nome_forma_registro }}</span>
        </div>
        <div class="col">
          <span><strong>Tipo de Registro: </strong>{{ registro.nome_tipo_registro }}</span>
        </div>
      </div>
      <div class="row mb-4">
        <div class="col">
          <span><strong>Destinação do Registro: </strong>{{ registro.nome_destinacao_registro }}</span>
        </div>
        <div class="col">
          <span><strong>Latitude de Soltura: </strong>{{ registro.latitude_soltura }}</span>
        </div>
        <div class="col">
          <span><strong>Longitude de Soltura: </strong>{{ registro.longitude_soltura }}</span>
        </div>
      </div>
      <div class="row mb-4">
        <div class="col">
          <span><strong>Nome do Local: </strong>{{ registro.nome_local }}</span>
        </div>
        <div class="col">
          <span><strong>Coletado: </strong>{{ registro.coletado }}</span>
        </div>
        <div class="col">
          <span><strong>Número de Registro de Tombamento: </strong>{{ registro.n_registro_tombamento }}</span>
        </div>
      </div>
      <div class="row mb-4">
        <div class="col">
          <span><strong>Status de Conservação Federal: </strong>{{ registro.nome_status_conserv_federal }} - {{ registro.sigla_status_conserv_federal }}</span>
        </div>
        <div class="col">
          <span><strong>Status de Conservação IUCN: </strong>{{ registro.nome_status_conserv_iucn }} - {{ registro.sigla_status_conserv_iucn }}</span>
        </div>
        <div class="col">
          <span><strong></strong></span>
        </div>
      </div>
    </template>
  </Modal>
</template>