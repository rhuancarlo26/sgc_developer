<script setup>
import Modal from "@/Components/Modal.vue";
import {ref} from "vue";
import {dateTimeFormat} from "@/Utils/DateTimeUtils.js";
import {usePage} from "@inertiajs/vue3";

const modalVisualizarRegistro = ref();
const registro = ref({});
const grupo = ref({
    1: 'Avifauna',
    2: 'Herpetofauna',
    3: 'Mastofauna'
});

const abrirModal = (item) => {
    registro.value = item;

    modalVisualizarRegistro.value.getBsModal().show();
}

defineExpose({abrirModal});
</script>

<template>
    <Modal ref="modalVisualizarRegistro" title="Visualização do registro" modal-dialog-class="modal-xl">
        <template #body>
            <div class="mb-4">
                <h2>Dados gerais</h2>

                <div class="row mb-4">
                    <span class="col"><strong>Registro: </strong></span>
                    <span class="col"><strong>Campanha: </strong>{{ registro.id_campanha }}</span>
                    <span
                        class="col"><strong>Grupo amostrado: </strong>{{ grupo[registro.id_grupo] ?? '' }}</span>
                </div>
                <div class="row mb-4">
                    <span class="col"><strong>Passagem de fauna: </strong>{{ registro.passagem?.nome_id }}</span>
                    <span class="col"><strong>Km: </strong>{{ registro.km }}</span>
                </div>
                <div class="row">
                    <span class="col"><strong>Latitude: </strong>{{ registro.latitude }}</span>
                    <span class="col"><strong>Longitude: </strong>{{ registro.longitude }}</span>
                </div>
            </div>
            <div class="mb-4">
                <h2>Identificação do espécime</h2>

                <div class="row mb-4">
                    <span class="col"><strong>Classe: </strong>{{ registro.classe }}</span>
                    <span class="col"><strong>Ordem: </strong>{{ registro.ordem }}</span>
                    <span class="col"><strong>Família: </strong>{{ registro.familia }}</span>
                </div>
                <div class="row mb-4">
                    <span class="col"><strong>Gênero: </strong>{{ registro.genero }}</span>
                    <span class="col"><strong>Espécie: </strong>{{ registro.especie }}</span>
                    <span class="col"><strong>Nome comum: </strong>{{ registro.nome_comum }}</span>
                </div>
            </div>
            <div class="mb-4">
                <h2>Dados do espécime</h2>

                <div class="row mb-4">
                    <span class="col"><strong>Sexo: </strong>{{ registro.sexo }}</span>
                    <span class="col"><strong>Faixa etária: </strong>{{ registro.faixa_etaria }}</span>
                    <span class="col"><strong>N° de indivíduos do registro: </strong>{{ registro.n_individuos }}</span>
                </div>
                <div class="row">
                    <span
                        class="col"><strong>Data do registro: </strong>{{
                            dateTimeFormat(registro.data_registro)
                        }}</span>
                    <span class="col"><strong>Hora do registro: </strong>{{ registro.hora_registro }}</span>
                </div>
            </div>
            <div class="mb-4">
                <h2>Status de conservação do espécime</h2>

                <div class="row">
                    <span class="col"><strong>Federal: </strong>{{ registro.status_federal?.nome }}</span>
                    <span class="col"><strong>IUCN: </strong>{{ registro.status_iunc?.nome }}</span>
                </div>
            </div>
            <div class="mb-4">
                <h2>Registro fotográfico</h2>

                <div class="row">
                    <span class="col"><strong>Imagens do espécime: </strong></span>
                    <img class="mb-2" :src="usePage().props.app_url + '/storage/' + registro.imagem?.caminho_imagem"
                         alt="Gráfico">
                </div>
            </div>
        </template>
        <template #footer>
        </template>
    </Modal>
</template>
