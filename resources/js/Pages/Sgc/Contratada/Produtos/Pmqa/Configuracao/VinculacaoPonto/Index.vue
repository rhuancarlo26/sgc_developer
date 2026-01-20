<script setup>
import Table from "@/Components/Table.vue";
import NavButton from "@/Components/NavButton.vue";
import { Head, Link, router, useForm } from "@inertiajs/vue3";
import ModalVincularPonto from "./ModalVincularPonto.vue";
import { ref } from "vue";
import LinkConfirmation from "@/Components/LinkConfirmation.vue";
import { IconTrash } from "@tabler/icons-vue";
import { IconEye } from "@tabler/icons-vue";
import { IconPencil } from "@tabler/icons-vue";
import ModalVisualizarPonto from "./ModalVisualizarPonto.vue";
import ModelSearchFormAllColumns from "@/Components/ModelSearchFormAllColumns.vue";

const modalVincularPonto = ref({});
const modalVisualizarPonto = ref({});

const props = defineProps({
    vinculacoes: { type: Object },
    contrato: { type: Object },
    servico: { type: Object },
    listas: { type: Array },
    pontos: { type: Array },
    aprovacao: { type: Object },
});

const abrirModalVincularPonto = (item) => {
    modalVincularPonto.value.abrirModal(item);
};

const abrirModalVisualizarPonto = (item) => {
    modalVisualizarPonto.value.abrirModal(item);
};

const ap = (ap) => {
    if (!ap?.fk_status) {
        return true;
    }
    return ap?.fk_status === 2;
};

const form = useForm({
    id: null,
    fk_status: null,
});
const enviaFiscal = (aprovacao) => {
    form.fk_status = 1;
    form.id = aprovacao?.id;
    form.post(
        route(
            "contratos.contratada.servicos.pmqa.configuracao.envia-fiscal-pmqa",
            {
                contrato: props.contrato.id,
                servico: props.servico.id,
            },
        ),
    );
};
</script>
<template #body>
    <ModelSearchFormAllColumns>
        <template #action>
            <NavButton
                type-button="primary"
                title="Enviar ao fiscal"
                @click="enviaFiscal(aprovacao)"
                v-if="ap(aprovacao)"
            />
            <NavButton
                @click="abrirModalVincularPonto()"
                type-button="success"
                title="Vincular"
                v-if="ap(aprovacao)"
            />
        </template>
    </ModelSearchFormAllColumns>
    <Table
        :columns="['Nome da lista', 'Qtd. pontos', 'Ação']"
        :records="vinculacoes"
        table-class="table-hover"
    >
        <tr>
            <td>{{ item.nome }}</td>
            <td class="text-center">{{ item.pontos.length }}</td>
            <td class="text-center">
                <LinkConfirmation
                    v-slot="confirmation"
                    v-if="ap(aprovacao)"
                    :options="{
                        text: 'A remoção de um ponto será permanente.',
                    }"
                >
                    <Link
                        :onBefore="confirmation.show"
                        :href="
                            route(
                                'contratos.contratada.servicos.pmqa.configuracao.vinculacao_ponto.destroy',
                                {
                                    contrato: contrato.id,
                                    servico: servico.id,
                                    lista: item.id,
                                },
                            )
                        "
                        as="button"
                        method="delete"
                        type="button"
                        class="btn btn-icon btn-danger"
                    >
                        <IconTrash />
                    </Link>
                </LinkConfirmation>
            </td>
        </tr>
    </Table>
    <div class="d-flex justify-content-between my-4 px-1">
        <NavButton
            type="button"
            type-button="secondary"
            title="Voltar"
            @click="$emit('prev')"
        />
        <NavButton
            type="button"
            type-button="primary"
            title="Avançar"
            @click="$emit('next')"
        />
    </div>

    <ModalVincularPonto
        ref="modalVincularPonto"
        :listas="listas"
        :pontos="pontos"
        :contrato="contrato"
        :servico="servico"
    />
    <ModalVisualizarPonto ref="modalVisualizarPonto" />
</template>
