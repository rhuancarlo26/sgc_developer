<script setup>
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import { useForm, router } from "@inertiajs/vue3";
import { IconDeviceFloppy } from "@tabler/icons-vue";
import { computed } from "vue";
import NavButton from "@/Components/NavButton.vue";

const props = defineProps({
    pmqa: Object,
    contratos: Object,
    produto: [String, Object],
    subproduto: String,
    temas: Array,
    empreendimentos: [Array, Object],
    canApprove: Boolean,
});

const emit = defineEmits(["saved"]);

const form = useForm({
    id: props.pmqa?.id ?? null,
    subproduto: props.subproduto ?? (props.pmqa?.subproduto ?? "EIA"),
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

const aprovarFase = () => {
    if (!confirm("Confirmar a aprovação desta fase de Apresentação?")) return;
    router.post(route('sgc.contratada.produtos.pmqa.aprovarFase', [props.contratos.id, props.produto, props.pmqa.id]), {
        fase: 'apresentacao'
    }, { preserveScroll: true });
};

const enviarParaAnalise = () => {
    if (!confirm("Tem certeza que deseja enviar a Apresentação para análise?")) return;
    router.post(route('sgc.contratada.produtos.pmqa.enviarAnaliseFase', [props.contratos.id, props.produto, props.pmqa.id]), {
        fase: 'apresentacao'
    }, { preserveScroll: true });
};

const isFormReadonly = computed(() => {
    if (props.canApprove) return true;
    if (!props.pmqa || !props.pmqa.id) return false;
    
    const status = props.pmqa.status_apresentacao;
    if (!status) return false; // If status is null/undefined, it's a new record in draft

    return status !== 'Em elaboração' && status !== 'Reprovada';
});
</script>

<template #body>
    <form @submit.prevent="salvar">
        <div class="row mb-4">
            <div class="col form-group">
                <InputLabel value="Tema" for="tema" />
                <v-select
                    :options="temas"
                    label="nome_tema"
                    :reduce="(t) => t.nome_tema"
                    v-model="form.tema"
                    :disabled="isFormReadonly"
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
                    v-model="form.cod_emp"
                    :disabled="isFormReadonly"
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
                    :disabled="isFormReadonly"
                />
                <InputError :message="form.errors[field[0]]" />
            </div>
        </div>

        <div class="col-auto d-flex gap-2">
            <NavButton
                v-if="!canApprove && pmqa?.status_apresentacao !== 'Em análise' && pmqa?.status_apresentacao !== 'Aprovada' && pmqa?.id"
                type-button="warning"
                title="Enviar para análise"
                @click="enviarParaAnalise"
            />
            <NavButton
                v-if="!isFormReadonly"
                @click="salvar()"
                type-button="success"
                :icon="IconDeviceFloppy"
                title="Salvar"
            />
            <NavButton
                v-if="canApprove && pmqa?.status_apresentacao === 'Em análise'"
                type-button="primary"
                title="✓ Aprovar Apresentação"
                @click="aprovarFase"
            />
        </div>
    </form>
</template>
