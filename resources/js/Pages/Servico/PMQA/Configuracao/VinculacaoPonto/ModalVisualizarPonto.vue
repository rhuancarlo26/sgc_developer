<script setup>
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import Modal from "@/Components/Modal.vue";
import NavButton from "@/Components/NavButton.vue";
import Table from "@/Components/Table.vue";
import {useForm} from "@inertiajs/vue3";
import {IconDeviceFloppy} from "@tabler/icons-vue";
import {computed} from "vue";
import {ref} from "vue";

const modalVisualizarPonto = ref({});
const lista = ref({});

const abrirModal = (item) => {
    lista.value = item;

    modalVisualizarPonto.value.getBsModal().show();
}

defineExpose({abrirModal});
</script>

<template>
    <Modal ref="modalVisualizarPonto" :title="'Pontos vinculados - ' + lista.nome" modal-dialog-class="modal-xl">
        <template #body>
            <div class="card-body">

                <div class="table-responsive">
                    <table class="table card-table table-bordered">
                        <thead>
                        <tr>
                            <th class="text-center">Ponto</th>
                            <th class="text-center">Classe</th>
                            <th class="text-center">Tipo de ambiente</th>
                            <th class="text-center">UF</th>
                            <th class="text-center">Município</th>
                            <th class="text-center">Bacia hifrográfica</th>
                            <th class="text-center">Km rodovia</th>
                            <th class="text-center">Estaca</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="ponto in lista.pontos" :key="ponto.id">
                            <td class="text-center">{{ ponto.id }}</td>
                            <td class="text-center">{{ ponto.classe }}</td>
                            <td class="text-center">{{ ponto.tipo_ambiente }}</td>
                            <td class="text-center">{{ ponto.UF }}</td>
                            <td class="text-center">{{ ponto.municipio }}</td>
                            <td class="text-center">{{ ponto.bacia_hidrografica }}</td>
                            <td class="text-center">{{ ponto.km_rodovia }}</td>
                            <td class="text-center">{{ ponto.estaca }}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </template>
        <template #footer>
        </template>
    </Modal>
</template>
