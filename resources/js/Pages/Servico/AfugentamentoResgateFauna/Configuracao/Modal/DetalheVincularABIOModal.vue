<script setup>
import Modal from '@/Components/Modal.vue';
import { ref } from "vue";
import { router } from "@inertiajs/vue3";
import { IconEye } from "@tabler/icons-vue";
import { dateTimeFormat } from "@/Utils/DateTimeUtils";

const licenca = ref({});
const servico = ref({});
const modalRef = ref(null);

const abrirModal = (item, ItemServico) => {
    console.log(ItemServico);
    licenca.value = item;
    servico.value = ItemServico;
    modalRef.value.getBsModal().show();
}

const vincularABIO = (licenca, servico) => {
    router.post(route('contratos.contratada.servicos.afugentamento.resgate.fauna.configuracao.vincular.abio.create', { licenca: licenca, servico: servico }));
};

defineExpose({ abrirModal });
</script>

<template>

    <Modal ref="modalRef" title="VIncular ABIO" modal-dialog-class="modal-xl">
        <template #body>
            <div class="card">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="col-md-6">
                            <div class="mb-2">
                                <strong>Nº Licença: </strong> {{ licenca.numero_licenca }}
                            </div>
                            <div class="mb-2">
                                <strong>Nome Licença: </strong> {{ licenca.tipo_rel?.nome }}
                            </div>
                            <div class="mb-2">
                                <strong>Data de emissão: </strong> {{ dateTimeFormat(licenca.data_emissao) }}
                            </div>
                            <div class="mb-2">
                                <strong>Vencimento: </strong> {{ dateTimeFormat(licenca.vencimento) }}
                            </div>
                            <div class="mb-2">
                                <strong>SEI: </strong> {{ licenca.numero_sei }}
                            </div>
                            <div class="mb-2">
                                <strong>Processo DNIT: </strong> {{ licenca.processo_dnit }}
                            </div>
                            <div class="mb-2">
                                <strong>Emissor: </strong> {{ licenca.emissor }}
                            </div>
                            <div class="mb-2">
                                <strong>Empreendimento: </strong> {{ licenca.empreendimento }}
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-2">
                                <strong>BR: </strong>
                            </div>
                            <div class="mb-2">
                                <strong>UF/KM Inicial: </strong>
                            </div>
                            <div class="mb-2">
                                <strong>UF/KM Final: </strong>
                            </div>
                            <div class="mb-2">
                                <strong>Extensão: </strong> {{ licenca.extensao }}
                            </div>
                            <div class="mb-2">
                                <strong>Iníco do Sub-Trecho (PNV): </strong> {{ licenca.inicio_subtrecho }}
                            </div>
                            <div class="mb-2">
                                <strong>Fim do Sub-Trecho (PNV): </strong> {{ licenca.fim_subtrecho }}
                            </div>
                            <div class="mb-2">
                                <strong>Visualizar PDF: </strong>
                                <IconEye />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>
        <template #footer>
            <div class="">
                <button type="button" class="btn btn-success" @click="vincularABIO(licenca, servico)" data-dismiss="modal"
                    aria-label="vincular abio">
                    Vincular ABIO
                </button>
            </div>
        </template>
    </Modal>

</template>
