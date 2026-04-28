<script setup>
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import Modal from "@/Components/Modal.vue";
import NavButton from "@/Components/NavButton.vue";
import Table from "@/Components/Table.vue";
import { dateTimeFormat } from "@/Utils/DateTimeUtils";
import { useForm } from "@inertiajs/vue3";
import { IconDeviceFloppy } from "@tabler/icons-vue";
import { computed } from "vue";
import { ref } from "vue";

const props = defineProps({
    contrato: { type: Object },
    pmqa: { type: Object },
    resultados: { type: Array },
});

const modalCampanha = ref();

const form = useForm({
    id: null,
    pmqa_id: props.pmqa.id,
    nome: null,
    pmqa_resultado_id: null,
    observacao: null,
});

const reset = () => {
    form.id = null;
    form.pmqa_id = props.pmqa.id;
    form.nome = null;
    form.pmqa_resultado_id = null;
    form.observacao = null;
};

const salvarRelatorio = () => {
    const url = form.id ? "update" : "store";

    form.post(
        route("contratos.contratada.relatorio.pmqa.relatorio." + url, {
            contrato: props.contrato.id,
            produto: "eia",
            pmqa: props.pmqa.id,
        }),
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: (response) => {
                console.log("Sucesso:", response);
                modalCampanha.value?.getBsModal().hide();
                reset();
            },
            onError: (errors) => {
                console.error("Erros de validação:", errors);
                let errorMessage = "Erro ao salvar:\n";
                Object.keys(errors).forEach((key) => {
                    errorMessage += `- ${key}: ${errors[key]}\n`;
                });
                alert(errorMessage);
            },
        },
    );
};

const abrirModal = (item) => {
    form.reset();

    if (item) {
        Object.assign(form, item);
    }

    modalCampanha.value.getBsModal().show();
};

defineExpose({ abrirModal });
</script>

<template>
    <Modal
        ref="modalCampanha"
        title="Nova campanha"
        modal-dialog-class="modal-xl"
    >
        <template #body>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col form-group">
                        <InputLabel value="Nome do relatório" for="nome" />
                        <input
                            type="text"
                            class="form-control"
                            name="nome"
                            id="nome"
                            v-model="form.nome"
                        />
                        <InputError :message="form.errors.nome" />
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col form-group">
                        <InputLabel value="Selecionar resultado:" />
                        <InputError :message="form.errors.pmqa_resultado_id" />
                        <div class="table-responsive">
                            <table class="table card-table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Nome</th>
                                        <th>Campanhas</th>
                                        <th>Data</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr
                                        v-for="resultado in resultados"
                                        :key="resultado.id"
                                    >
                                        <td>
                                            <label class="form-check">
                                                <input
                                                    class="form-check-input"
                                                    type="radio"
                                                    :name="
                                                        'resultado-' +
                                                        resultado.id
                                                    "
                                                    :id="
                                                        'resultado-' +
                                                        resultado.id
                                                    "
                                                    :value="resultado.id"
                                                    v-model="
                                                        form.pmqa_resultado_id
                                                    "
                                                />
                                                <span
                                                    class="form-check-label"
                                                    >{{ resultado.nome }}</span
                                                >
                                            </label>
                                        </td>
                                        <td>
                                            <span
                                                v-for="campanha in resultado.campanhas.map(
                                                    (campanha) =>
                                                        campanha.nome_campanha,
                                                )"
                                                :key="campanha"
                                                class="badge bg-warning text-white m-1"
                                            >
                                                {{ campanha }}
                                            </span>
                                        </td>
                                        <td>
                                            {{
                                                dateTimeFormat(
                                                    resultado.created_at,
                                                )
                                            }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col form-group">
                        <InputLabel value="Observações" for="observacao" />
                        <textarea
                            class="form-control"
                            name="observacao"
                            id="observacao"
                            rows="6"
                            v-model="form.observacao"
                        ></textarea>
                        <InputError :message="form.errors.observacao" />
                    </div>
                </div>
            </div>
        </template>
        <template #footer>
            <NavButton
                @click="salvarRelatorio()"
                type-button="success"
                :icon="IconDeviceFloppy"
                :title="form.id ? 'Editar' : 'Salvar'"
            />
        </template>
    </Modal>
</template>
