<script setup>
import { ref } from 'vue';
import NavButton from "@/Components/NavButton.vue";
import Table from "@/Components/Table.vue";
import { router } from "@inertiajs/vue3";
import ModalPontos from '../Fases/ModalPontos.vue';

const props = defineProps({
    contrato: { type: [Object, String] }, // ✅ ACEITA STRING
    servico: { type: Object },
    pontos: { type: Object },
    produto: { type: [Object, String] },   // ✅ ACEITA STRING
    contratos: { type: Object },
    draftData: { type: Object },
});

const modalImportarPonto = ref(null); // ✅ DECLARAR REF
const modalVisualizarPonto = ref(null); // ✅ DECLARAR REF

const abrirModalImportar = () => {
    modalImportarPonto.value.abrirModal();
};

const abrirModalVisualizar = (item) => {
    modalVisualizarPonto.value.abrirModal(item);
};

const atualizarListaDePontos = () => {
    console.log("Evento 'importacaoConcluida' recebido!");
    router.reload({
        only: ["pontos"],
        onSuccess: () => console.log("Pontos atualizados!"),
        onError: () => alert("Erro ao atualizar pontos."),
    });
};
</script>

<template #body>
    <div class="card">
        <div class="card-body">
            <h2>Importar pontos</h2>
            <div class="d-flex justify-content-end mb-3">
                <a
                    class="btn btn-info me-1"
                    target="_blank"
                    :href="
                        route(
                            'contratos.contratada.servicos.pmqa.configuracao.ponto.download_modelo'
                        )
                    "
                    >Modelo</a
                >
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
                :records="props.pontos"
                table-class="table-hover"
            >
                <template #body="{ item }">
                    <tr>
                        <td class="text-center">{{ item.id }}</td>
                        <td class="text-center">
                            {{ item.nome_ponto_coleta }}
                        </td>
                        <td class="text-center">{{ item.lat_x }}</td>
                        <td class="text-center">{{ item.long_y }}</td>
                        <td>{{ item.classificacao }}</td>
                        <td class="text-center">{{ item.classe }}</td>
                        <td class="text-center">{{ item.tipo_ambiente }}</td>
                        <td class="text-center">{{ item.UF }}</td>
                        <td>{{ item.municipio }}</td>
                        <td>{{ item.bacia_hidrografica }}</td>
                        <td class="text-center">{{ item.km_rodovia }}</td>
                        <td class="text-center">{{ item.estaca }}</td>
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
        :draftData="draftData"
        :produto="produto"
        ref="modalImportarPonto"
    />
    <!-- <ModalVisualizarPonto ref="modalVisualizarPonto" /> -->
</template>
