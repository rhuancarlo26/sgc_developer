<script setup>
import { ref, computed } from "vue";
import { Link, router } from "@inertiajs/vue3";
import NavButton from "@/Components/NavButton.vue";
import Table from "@/Components/Table.vue";
import ModalPontos from "./ModalPontos.vue";
import SgcLinkConfirmation from "@/Components/SgcLinkConfirmation.vue";
import { IconEye } from "@tabler/icons-vue";
import { IconPencil } from "@tabler/icons-vue";
import { IconTrash } from "@tabler/icons-vue";
import ModalVisualizarPonto from "./ModalVisualizarPonto.vue";
import ModalFormPonto from "./ModalFormPonto.vue";

const props = defineProps({
    contrato: { type: [Object, String] },
    pontos: { type: Array, default: () => [] },
    produto: { type: [Object, String] },
    contratos: { type: Object },
    pmqa: { type: Object },
});

const emit = defineEmits(["next", "prev"]);

const campanhaId = computed(() => props.pmqa?.campanha_id);

const contratoId = computed(() =>
    typeof props.contrato === "object" ? props.contrato.id : props.contrato,
);

const modalImportarPonto = ref(null);
const modalVisualizarPonto = ref(null);
const modalFormPonto = ref(null);

const abrirModalImportar = () => {
    modalImportarPonto.value.abrirModal();
};

const abrirModalVisualizar = (item) => {
    modalVisualizarPonto.value?.abrirModal(item);
};

const abrirEditar = (item) => modalFormPonto.value?.abrirModal(item);
const abrirNovo = () => modalFormPonto.value?.abrirModal(null);

const onSaved = () => router.reload({ only: ["pontos"] });

const pontosTable = computed(() => ({
    data: props.pontos,
    links: [],
}));

const atualizarListaDePontos = () => {
    router.reload({
        only: ["pontos"],
    });
};
</script>

<template #body>
    <div class="card">
        <div class="card-body">
            <h2>Pontos de coleta</h2>
            <div class="d-flex justify-content-end mb-3">
                <a class="btn btn-info me-1" target="_blank"
                :href="route('contratos.contratada.servicos.pmqa.configuracao.ponto.download_modelo')">Modelo</a>
                <NavButton
                    @click="abrirModalImportar()"
                    type-button="success"
                    title="Importar"
                />
            </div>

            <Table
                :columns="[
                    'Cod. ponto',
                    'Pt. coleta',
                    'Latitude',
                    'Longitude',
                    'Classificação',
                    'Classe',
                    'Tipo de ambiente',
                    'UF',
                    'Municipio',
                    'Bacia hidrografica',
                    'Km rodovia',
                    'Estaca',
                    'Ação',
                ]"
                :records="pontosTable"
                table-class="table-hover"
            >
                <template #body="{ item }">
                    <tr>
                        <td class="text-center">{{ item.id }}</td>
                        <td class="text-center">
                            {{ item.nome_ponto_coleta }}
                        </td>
                        <td class="text-center">
                            {{ item.lat_x }}
                        </td>
                        <td class="text-center">
                            {{ item.long_y }}
                        </td>
                        <td>{{ item.classificacao }}</td>
                        <td class="text-center">
                            {{ item.classe }}
                        </td>
                        <td class="text-center">
                            {{ item.tipo_ambiente }}
                        </td>
                        <td class="text-center">{{ item.uf }}</td>
                        <td>{{ item.municipio }}</td>
                        <td>{{ item.bacia_hidrografica }}</td>
                        <td class="text-center">
                            {{ item.km_rodovia }}
                        </td>
                        <td class="text-center">
                            {{ item.estaca }}
                        </td>
                        <td class="text-center">
                            <div class="acao-btns">
                                <NavButton
                                    type-button="info"
                                    class="btn-icon"
                                    @click="abrirModalVisualizar(item)"
                                    :icon="IconEye"
                                />

                                <NavButton
                                    type-button="primary"
                                    class="btn-icon"
                                    :icon="IconPencil"
                                    @click="abrirEditar(item)"
                                />

                                <SgcLinkConfirmation
                                    v-slot="confirmation"
                                    :options="{
                                        text: 'A remoção de um ponto será permanente.',
                                    }"
                                >
                                    <Link
                                        :onBefore="confirmation.show"
                                        :href="
                                            route(
                                                'contratos.contratada.sgc.pmqa.configuracao.ponto.delete',
                                                {
                                                    contrato: contratoId,
                                                    produto: props.produto.slug,
                                                    pmqa: props.pmqa,
                                                    ponto: item.id,
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
                                </SgcLinkConfirmation>
                            </div>
                        </td>
                    </tr>
                </template>
            </Table>
        </div>
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
    <ModalPontos
        :contrato="contrato"
        :produto="produto"
        :pmqa="pmqa"
        ref="modalImportarPonto"
        @importacao-concluida="onSaved"
    />
    <ModalFormPonto
        ref="modalFormPonto"
        :contrato="contrato"
        :produto="produto"
        :pmqa="pmqa"
        @saved="onSaved"
    />
    <ModalVisualizarPonto ref="modalVisualizarPonto" />
</template>

<style scoped>
.table-icon-wrapper {
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

/* força o <li> a não quebrar layout */
.table-icon-wrapper li {
    list-style: none;
    margin: 0;
    padding: 0;
}

/* centraliza o conteúdo do NavLink */
.table-icon-wrapper .nav-link {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0.375rem; /* igual btn-icon */
}

/* corrige alinhamento do SVG */
.table-icon-wrapper svg {
    display: block;
}
</style>
