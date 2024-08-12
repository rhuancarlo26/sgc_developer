<script setup>
import Modal from "@/Components/Modal.vue";
import {dateTimeFormat} from "@/Utils/DateTimeUtils";
import {usePage} from "@inertiajs/vue3";
import {ref} from "vue";
import {IconEye, IconTrash} from "@tabler/icons-vue";
import NavButton from "@/Components/NavButton.vue";

const props = defineProps({
    ocorrencia: {type: Object}
})

const modalVisualizarVistoria = ref();

const vistoria = ref({});

const abrirModal = (item) => {
    vistoria.value = {};

    if (item) {
        vistoria.value = item;
    }

    modalVisualizarVistoria.value.getBsModal().show();
}

defineExpose({abrirModal});
</script>

<template>
    <Modal ref="modalVisualizarVistoria" :title="'Visualizar vistoria ' + vistoria.nome_id"
           modal-dialog-class="modal-xl">
        <template #body>
            <div class="mb-4">
                <div class="row">
                    <h3>Local</h3>
                    <div class="col">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table card-table table-bordered table-hover">
                                    <tbody>
                                    <tr>
                                        <th>ID ocorrência</th>
                                        <td>{{ ocorrencia.nome_id }}</td>
                                    </tr>
                                    <tr>
                                        <th>ID vistoria</th>
                                        <td>{{ vistoria.nome_id }}</td>
                                    </tr>
                                    <tr>
                                        <th>Ocorrência corrigida</th>
                                        <td>{{ vistoria.corrigido }}</td>
                                    </tr>
                                    <tr>
                                        <th>Intensidade de ocorrência</th>
                                        <td>{{ vistoria.intensidade_vistoria }}</td>
                                    </tr>
                                    <tr>
                                        <th>Realizado acordo de prazo</th>
                                        <td>{{ vistoria.acordo_prazo }}</td>
                                    </tr>
                                    <tr>
                                        <th>Observações da vistoria</th>
                                        <td>{{ vistoria.obs_vistoria }}</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table card-table table-bordered table-hover">
                                    <tbody>
                                    <tr>
                                        <th>Data da ocorrência</th>
                                        <td>{{ dateTimeFormat(ocorrencia.data_ocorrencia) }}</td>
                                    </tr>
                                    <tr>
                                        <th>Data da vistoria</th>
                                        <td>{{ dateTimeFormat(vistoria.data_vistoria) }}</td>
                                    </tr>
                                    <tr>
                                        <template v-if="vistoria.corrigido">
                                            <th>Data fim</th>
                                            <td>{{ dateTimeFormat(vistoria.data_fim) }}</td>
                                        </template>
                                        <template v-else>
                                            <th></th>
                                            <td></td>
                                        </template>
                                    </tr>
                                    <tr>
                                        <th>Tipo de ocorrência</th>
                                        <td>{{ ocorrencia.tipo }}</td>
                                    </tr>
                                    <tr>
                                        <template v-if="vistoria.corrigido">
                                            <th>Prazo para correção (dias)</th>
                                            <td>{{ vistoria.prazo_vistoria }}</td>
                                        </template>
                                        <template v-else>
                                            <th></th>
                                            <td></td>
                                        </template>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mb-4">
                <h3>Imagens da vistoria</h3>
                <span class="col-2" v-for="imagem in vistoria.imagens" :key="imagem.id">
                    <img class="mb-2" :src="usePage().props.app_url + '/storage/' + imagem.caminho_arquivo"
                         alt="Imagem vistoria">
                </span>
            </div>
            <div class="row">
                <h3>Arquivos de acordo de prazo</h3>
                <div class="table-responsive">
                    <table class="table card-table table-bordered">
                        <thead>
                        <tr>
                            <th>Arquivo</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="arquivo in vistoria.arquivos" :key="arquivo.id">
                            <td>{{ arquivo.nome }}</td>
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
