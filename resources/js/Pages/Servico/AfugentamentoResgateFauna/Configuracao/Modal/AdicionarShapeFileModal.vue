<script setup>
import Modal from '@/Components/Modal.vue';
import { ref } from "vue";
import { router } from "@inertiajs/vue3";
import { IconEye } from "@tabler/icons-vue";
import { dateTimeFormat } from "@/Utils/DateTimeUtils";

const licenca = ref({});
const modalRef = ref(null);

const abrirModal = (item) => {
    console.log(item);
    licenca.value = item;
    modalRef.value.getBsModal().show();
}

const vincularShapeFile = (licenca) => {
    // router.post(route('rota para o shapefile'));
};

defineExpose({ abrirModal });
</script>

<template>

    <Modal ref="modalRef" title="VIncular ShapeFile" modal-dialog-class="modal-xl">
        <template #body>
            <div class="card">
                <div class="card-body">
                    <div class="card-header mb-4">
                        <span>Dados de ASV</span>
                    </div>
                    <div class="d-flex">
                        <div class="col-md-6">
                            <div class="mb-2">
                                <strong>Tipo: </strong> {{ licenca.licenca?.tipo_rel?.sigla }}
                            </div>
                            <div class="mb-2">
                                <strong>Nº Licença: </strong> {{ licenca.licenca?.numero_licenca }}
                            </div>
                            <div class="mb-2">
                                <strong>Empreendimento: </strong> {{ licenca.licenca?.empreendimento }}
                            </div>
                            <div class="mb-2">
                                <strong>Emissor: </strong> {{ licenca.licenca?.emissor }}
                            </div>
                            <div class="mb-2">
                                <strong>Data de emissão: </strong> {{ dateTimeFormat(licenca.licenca?.data_emissao) }}
                            </div>
                            <div class="mb-2">
                                <strong>Vencimento: </strong> {{ licenca.licenca?.vencimento }}
                            </div>
                            <div class="mb-2">
                                <strong>SEI: </strong> {{ licenca.licenca?.numero_sei }}
                            </div>
                            <div class="mb-2">
                                <strong>Processo DNIT: </strong> {{ licenca.licenca?.processo_dnit }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="custom-file-container" data-upload-id="myFirstImage">
                        <label class="form-label">Buscar Arquivo (.zip)</label>
                        <input type="file" accept=".zip,.rar,.7zip" id="files_afugent_shape" class="form-control">
                    </div>
                </div>
            </div>
        </template>
        <template #footer>
            <div class="">
                <button type="button" class="btn btn-success" @click="vincularShapeFile(licenca)" data-dismiss="modal"
                    aria-label="vincular shapefile">
                    Vincular ShapeFile
                </button>
            </div>
        </template>
    </Modal>

</template>
