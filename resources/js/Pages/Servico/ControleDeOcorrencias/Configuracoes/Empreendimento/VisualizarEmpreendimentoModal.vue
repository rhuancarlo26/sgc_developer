<template>
    <Modal ref="modalRef" title="Visualizar Empreendimento" modal-dialog-class="modal-xl">
        <template #body>
            <table class="table table-bordered">
                <tbody>
                    <tr>
                        <td><strong>UF Inicial:</strong></td>
                        <td>
                            <p v-if="empreendimento?.licenca?.iniciais">
                                <span v-for="uf in empreendimento.licenca.iniciais.split(',')" :key="uf"
                                    class="badge bg-warning text-white m-1">
                                    {{ uf }}
                                </span>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>UF Final:</strong></td>
                        <td>
                            <p v-if="empreendimento?.licenca?.finais">
                                <span v-for="uf in empreendimento.licenca.finais.split(',')" :key="uf"
                                    class="badge bg-warning text-white m-1">
                                    {{ uf }}
                                </span>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Rodovia:</strong></td>
                        <td>
                            <p v-if="empreendimento?.licenca?.brs">
                                <span v-for="br in empreendimento.licenca.brs.split(',')" :key="br"
                                    class="badge bg-warning text-white m-1">
                                    {{ br }}
                                </span>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Empreendimento:</strong></td>
                        <td>{{ empreendimento?.licenca?.empreendimento }}</td>
                    </tr>
                    <tr>
                        <td><strong>KM Início:</strong></td>
                        <td v-if="empreendimento?.licenca?.segmentos && empreendimento.licenca.segmentos.length > 0">
                            {{ Math.min(...empreendimento.licenca.segmentos.map(segmento => segmento.km_inicial)) }}
                        </td>
                        <td v-else>--</td>
                    </tr>
                    <tr>
                        <td><strong>KM Fim:</strong></td>
                        <td v-if="empreendimento?.licenca?.segmentos && empreendimento.licenca.segmentos.length > 0">
                            {{ Math.max(...empreendimento.licenca.segmentos.map(segmento => segmento.km_final)) }}
                        </td>
                        <td v-else>--</td>
                    </tr>
                    <tr>
                        <td><strong>Extensão BR:</strong></td>
                        <td>{{ empreendimento?.licenca?.extensao }}</td>
                    </tr>
                    <tr>
                        <td><strong>Nº Licença:</strong></td>
                        <td>{{ empreendimento?.licenca?.numero_licenca }}</td>
                    </tr>
                </tbody>
            </table>
        </template>
        <template #footer>
            <NavButton @click="fecharModal" type-button="secondary" title="Fechar" />
        </template>
    </Modal>
</template>

<script setup>
import { ref, watch } from 'vue';
import Modal from "@/Components/Modal.vue";
import NavButton from "@/Components/NavButton.vue";

const props = defineProps({
    empreendimento: { type: Object, default: () => null }
});

const modalRef = ref(null);

const open = () => {
    if (modalRef.value && modalRef.value.getBsModal) {
        const bsModal = modalRef.value.getBsModal();
        if (bsModal) bsModal.show();
    }
};

const close = () => {
    if (modalRef.value && modalRef.value.getBsModal) {
        const bsModal = modalRef.value.getBsModal();
        if (bsModal) bsModal.hide();
    }
};

const fecharModal = () => {
    close();
};

watch(() => props.empreendimento, (newVal) => {
    if (newVal) {
        open();
    }
});
</script>

<style scoped>
.modal-xl {
    max-width: 80%;
}

.table {
    width: 100%;
    margin-bottom: 1rem;
    color: #212529;
}

.table-bordered {
    border: 1px solid #dee2e6;
}

.table-bordered td,
.table-bordered th {
    border: 1px solid #dee2e6;
}
</style>
