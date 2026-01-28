<script setup>
import { useForm } from "@inertiajs/vue3";
import { computed } from "vue";

const props = defineProps({
    pmqa: Object,
    contratos: Object,
    produto: [String, Object],
    temas: Array,
    empreendimentos: [Array, Object],
});

const emit = defineEmits(["saved"]);

const form = useForm({
    id: props.pmqa?.id ?? null,
    cod_emp: props.pmqa?.cod_emp ?? "",
    tema: props.pmqa?.tema ?? "Recursos Hídricos",
    especificacao: props.pmqa?.especificacao ?? "",
    introducao: props.pmqa?.introducao ?? "",
    justificativa: props.pmqa?.justificativa ?? "",
    objetivos: props.pmqa?.objetivos ?? "",
    metodologia: props.pmqa?.metodologia ?? "",
    publico_alvo: props.pmqa?.publico_alvo ?? "",
});

const salvar = () => {
    form.patch(
        route("sgc.contratada.produtos.pmqa.update", [
            props.contratos,
            props.produto,
        ]),
        {
            preserveScroll: true,
            onSuccess: () => emit("saved"),
        },
    );
};

const isReadonly = computed(() => !!form.id);
</script>

<template>
    <form @submit.prevent="salvar">
        <div class="row mb-4">
            <div class="col form-group">
                <InputLabel value="Tema" for="tema" />
                <v-select
                    :options="temas"
                    label="nome_tema"
                    :reduce="(t) => t.nome_tema"
                    v-model="form.tema"
                    :disabled="isReadonly"
                >
                    <template #no-options>
                        Nenhum registro encontrado.
                    </template>
                </v-select>
                <InputError :message="form.errors.tema" />
            </div>

            <div class="col form-group">
                <InputLabel value="Código de Empreendimento" for="cod_emp" />
                <v-select
                    :options="empreendimentos"
                    label="nome"
                    v-model="form.cod_emp"
                    :disabled="isReadonly"
                >
                    <template #no-options>
                        Nenhum registro encontrado.
                    </template>
                </v-select>
                <InputError :message="form.errors.cod_emp" />
            </div>
        </div>

        <div
            class="row mb-4"
            v-for="field in [
                ['especificacao', 'Especificação'],
                ['introducao', 'Introdução'],
                ['justificativa', 'Justificativa'],
                ['objetivos', 'Objetivos'],
                ['metodologia', 'Metodologia'],
                ['publico_alvo', 'Público alvo'],
            ]"
            :key="field[0]"
        >
            <div class="col form-group">
                <InputLabel :value="field[1]" :for="field[0]" />
                <textarea
                    class="form-control"
                    :id="field[0]"
                    v-model="form[field[0]]"
                    rows="5"
                    :disabled="isReadonly"
                />
                <InputError :message="form.errors[field[0]]" />
            </div>
        </div>

        <div class="mb-4 d-flex justify-content-end">
            <button
                v-if="!form.id"
                type="submit"
                class="btn btn-success"
                :disabled="processing"
            >
                <IconDeviceFloppy class="me-2" />
                Salvar
            </button>
        </div>
    </form>
</template>
