<script setup>
import Modal from "@/Components/Modal.vue";
import InputLabel from "@/Components/InputLabel.vue";
import { computed, ref } from "vue";
import { dateTimeFormat } from "@/Utils/DateTimeUtils";
import { router } from "@inertiajs/vue3";

const props = defineProps({
    contratoId: {
        type: [Number, String],
        required: true,
    },
});

const modalHistorico = ref(null);
const detalhe = ref({});

const abrirModal = (servico) => {
    detalhe.value = servico ?? {};
    modalHistorico.value?.getBsModal?.()?.show();
};

defineExpose({ abrirModal });

const historicos = computed(() => {
    return detalhe.value?.retornos_confeccao ?? [];
});

const nomeTema = computed(() => {
    return detalhe.value?.tema?.nome_tema
        ?? historicos.value?.[0]?.tema?.nome_tema
        ?? "-";
});

const nomeServico = computed(() => {
    return detalhe.value?.modulo_importado?.nome
        ?? historicos.value?.[0]?.modulo_importado?.nome
        ?? "-";
});

const textoStatus = (status) => {
    const statusNumber = Number(status);

    if (statusNumber === 1) {
        return "Em confecção";
    }

    if (statusNumber === 2) {
        return "Em análise";
    }

    if (statusNumber === 3) {
        return "Aprovado";
    }

    if (statusNumber === 4) {
        return "Reprovado/Pendente";
    }

    return "-";
};

const editarServico = () => {
    const url = route('contratos.contratada.servicos.create', {
        contrato: props.contratoId,
        servico: detalhe.value.id,
    });

    const modal = modalHistorico.value?.getBsModal?.();

    if (!modal) {
        router.visit(url);
        return;
    }

    modal.hide();

    setTimeout(() => {
        router.visit(url);
    }, 300);
};

const podeEditar = computed(() => {
    return Number(detalhe.value?.status_aprovacao) === 1;
});
</script>

<template>
    <Modal ref="modalHistorico" title="Histórico de retorno para em confecção" modal-dialog-class="modal-xl">
        <template #body>
            <div class="row mb-4">
                <div class="col-md-6">
                    <InputLabel value="Tema" />
                    <input type="text" class="form-control" :value="nomeTema" disabled />
                </div>

                <div class="col-md-6">
                    <InputLabel value="Serviço (módulo importado)" />
                    <input type="text" class="form-control" :value="nomeServico" disabled />
                </div>
            </div>

            <div v-if="!historicos.length" class="alert alert-info mb-0">
                Nenhum retorno para confecção registrado até o momento.
            </div>

            <div v-else class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th>Usuário</th>
                            <th>Status anterior</th>
                            <th>Status novo</th>
                            <th>Motivo</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr v-for="historico in historicos" :key="historico.id">
                            <td>{{ dateTimeFormat(historico.created_at) }}</td>
                            <td>{{ historico.usuario?.name ?? "-" }}</td>
                            <td>{{ textoStatus(historico.status_anterior) }}</td>
                            <td>{{ textoStatus(historico.status_novo) }}</td>
                            <td>{{ historico.motivo }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </template>

        <template #footer>
            <button v-if="podeEditar" type="button" class="btn btn-primary" @click="editarServico">
                Editar serviço
            </button>

            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                Fechar
            </button>
        </template>
    </Modal>
</template>
