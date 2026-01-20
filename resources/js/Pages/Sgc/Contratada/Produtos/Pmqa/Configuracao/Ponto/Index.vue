<script setup>
import { ref, computed } from "vue";
import { router } from "@inertiajs/vue3";
import NavButton from "@/Components/NavButton.vue";
import Table from "@/Components/Table.vue";
import ModalPontos from "./ModalPontos.vue";

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

const abrirModalImportar = () => {
    modalImportarPonto.value.abrirModal();
};

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
            <h2>Importar pontos</h2>
            <div class="d-flex justify-content-end mb-3">
                <a class="btn btn-info me-1" target="_blank">Modelo</a>
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
                        <td class="text-center">{{ item.UF }}</td>
                        <td>{{ item.municipio }}</td>
                        <td>{{ item.bacia_hidrografica }}</td>
                        <td class="text-center">
                            {{ item.km_rodovia }}
                        </td>
                        <td class="text-center">
                            {{ item.estaca }}
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
    />
</template>
