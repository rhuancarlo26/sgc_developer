<script setup>
import ModelSearchFormAllColumns from "@/Components/ModelSearchFormAllColumns.vue";
import Table from "@/Components/Table.vue";
import NavButton from "@/Components/NavButton.vue";
import { ref } from "vue";
import { IconPencil } from "@tabler/icons-vue";
import { IconTrash } from "@tabler/icons-vue";
import LinkConfirmation from "@/Components/LinkConfirmation.vue";
import ModalParametros from "./ModalParametros.vue";

const modalParametros = ref(null);

const props = defineProps({
    contrato: { type: Object },
    pmqa: { type: Object },
    parametros: { type: Array },
    listas: { type: Object },
    aprovacao: { type: Object },
    produto: { type: Object },
});

const emit = defineEmits(["next", "prev"]);

const abrirModalParametros = () => {
    modalParametros.value?.abrirModal?.();
};

const editarLista = (item) => {
    modalParametros.value?.abrirModal?.(item);
};

const ap = (ap) => {
    if (!ap?.fk_status) {
        return true;
    }
    return ap?.fk_status === 2;
};
</script>

<template #body>
    <ModelSearchFormAllColumns
        :columns="['nome', 'parametros.nome']"
        v-if="ap(aprovacao)"
    >
        <template #action>
            <NavButton
                @click="abrirModalParametros()"
                type-button="success"
                title="Novo parâmetro"
            />
        </template>
    </ModelSearchFormAllColumns>

    <Table
        :columns="['Nome', 'Parâmetros', 'Ação']"
        :records="listas"
        table-class="table-hover"
    >
        <template #body="{ item }">
            <tr>
                <td>{{ item.nome }}</td>
                <td>
                    <p v-if="item.parametros">
                        <span
                            v-for="(record, i) in item.parametros"
                            :key="parametro"
                            class="badge bg-warning text-white m-1"
                        >
                            {{ record.parametro }}
                        </span>
                    </p>
                </td>
                <td v-if="ap(aprovacao)">
                    <div class="d-flex">
                        <NavButton
                            :icon="IconPencil"
                            class="btn-icon"
                            type-button="primary"
                            @click="editarLista(item)"
                        />
                        <LinkConfirmation
                            v-slot="confirmation"
                            :options="{
                                text: 'A remoção de um ponto será permanente.',
                            }"
                        >
                        </LinkConfirmation>
                    </div>
                </td>
            </tr>
        </template>
    </Table>
    <div class="d-flex justify-content-between my-4 px-1">
        <NavButton
            type="button"
            type-button="secondary"
            title="Voltar"
            @click="$emit('prev')"
        />
        <NavButton
            @click="$emit('next')"
            type-button="primary"
            title="Avançar"
        />
    </div>
    <ModalParametros
        :contrato="contrato"
        :parametros="parametros"
        :produto="produto"
        :pmqa="pmqa"
        ref="modalParametros"
    />
</template>
