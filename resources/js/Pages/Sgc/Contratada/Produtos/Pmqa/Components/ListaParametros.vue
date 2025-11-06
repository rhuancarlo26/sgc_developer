<script setup>
import { Head, Link } from "@inertiajs/vue3";
import NavButton from "@/Components/NavButton.vue";
import { ref } from "vue";
import { IconPencil } from "@tabler/icons-vue";
import { IconTrash } from "@tabler/icons-vue";
import LinkConfirmation from "@/Components/LinkConfirmation.vue";
import Table from "@/Components/Table.vue";
import ModalParametros from "../Fases/ModalParametros.vue";

const modalParametros = ref({});

const props = defineProps({
    contrato: { type: Object },
    servico: { type: Object },
    parametros: { type: Object },
    listas: { type: Object },
    aprovacao: { type: Object },
    draftData: { type: Object },
    produto: { type: Object }
});
console.log(props.listas)

const abrirModalParametros = () => {
    modalParametros.value.abrirModal();
};

const editarLista = (item) => {
    modalParametros.value.abrirModal(item);
};

const ap = (ap) => {
    if (!ap?.fk_status) {
        return true;
    }
    return ap?.fk_status === 2;
};
</script>

<template>
    <div class="card">
        <div class="card-body">
            <h2>Criar lista de parâmetros</h2>

            <!-- Table component funcionando -->
            <div class="button d-flex justify-content-end mb-2">
                <button class="btn btn-success" @click="abrirModalParametros()">Criar lista</button>
            </div>
            <Table
                :columns="['Nome', 'Parâmetros', 'Ação']"
                :records="props.listas"
                table-class="table-hover"
            >
                <template #body="{ item }">
                    <tr>
                        <td>{{ item.nome }}</td>
                        <td>
                            <p v-if="item.parametros">
                                <span
                                    v-for="(record, i) in item.parametros.data"
                                    :key="i"
                                    class="badge bg-warning text-white m-1"
                                >
                                    {{ record.parametro }}
                                </span>
                            </p>
                        </td>
                        <td>
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
                                    <!-- <Link
                                        :onBefore="confirmation.show"
                                        :href="
                                            route(
                                                'contratos.contratada.servicos.pmqa.configuracao.parametro.destroy',
                                                {
                                                    contrato: contrato.id,
                                                    // servico: servico.id,
                                                    lista: item.id,
                                                }
                                            )
                                        "
                                        as="button"
                                        method="delete"
                                        type="button"
                                        class="btn btn-icon btn-danger"
                                    >
                                        <IconTrash />
                                    </Link> -->
                                </LinkConfirmation>
                            </div>
                        </td>
                    </tr>
                </template>
            </Table>
        </div>

        <ModalParametros
            :contrato="contrato"
            :draftData="draftData"
            :parametros="parametros"
            :produto="produto"
            ref="modalParametros"
        />
    </div>
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
</template>
