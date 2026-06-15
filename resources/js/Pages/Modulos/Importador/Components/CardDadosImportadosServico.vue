<script setup>
import Table from "@/Components/Table.vue";
import axios from "axios";
import { computed, onMounted, ref, watch } from "vue";

const props = defineProps({
    servico: { type: Object, required: true },
});

const carregando = ref(false);
const erro = ref(null);
const importadores = ref([]);

const temDados = computed(() => {
    return importadores.value.some((importador) => importador.dados?.length);
});

const servicoId = computed(() => {
    return props.servico?.id ?? props.servico?.servico_id ?? props.servico?.fk_servico ?? null;
});

const camposImportador = (importador) => {
    return importador.modulo?.campos ?? [];
};

const colunasImportador = (importador) => {
    const campos = camposImportador(importador);

    return campos.length
        ? campos.map((campo) => campo.nome_campo)
        : Object.keys(importador.dados?.[0] ?? {});
};

const buscarDados = () => {
    if (!servicoId.value) {
        return;
    }

    carregando.value = true;
    erro.value = null;

    axios.get(`/modulos/importador-modulo/buscar-dados-servico/${servicoId.value}`)
        .then(({ data }) => {
            importadores.value = data ?? [];
        })
        .catch((error) => {
            importadores.value = [];
            erro.value = error?.response?.data?.message ?? 'Nao foi possivel carregar os dados importados.';
        })
        .finally(() => {
            carregando.value = false;
        });
};

onMounted(() => {
    buscarDados();
});

watch(servicoId, () => {
    importadores.value = [];
    buscarDados();
});
</script>

<template>
    <div v-if="servicoId" class="border rounded p-3 mb-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="my-0">Dados da planilha importada</h3>
        </div>

        <div>
            <div v-if="carregando" class="text-muted">
                Carregando dados importados...
            </div>

            <div v-else-if="erro" class="text-danger">
                {{ erro }}
            </div>

            <div v-else-if="!temDados" class="text-muted">
                Nenhum dado de planilha importada encontrado para este servico.
            </div>

            <div v-else class="d-flex flex-column gap-4">
                <div v-for="importador in importadores" :key="importador.id">
                    <div class="d-flex flex-wrap gap-2 align-items-center mb-2">
                        <h4 class="my-0">
                            {{ importador.modulo?.nome ?? 'Módulo importador' }}
                        </h4>

                        <span class="badge bg-blue-lt">
                            Campanha {{ importador.campanha }}
                        </span>

                        <span class="badge bg-secondary-lt">
                            {{ importador.mes_ano_referencia }}
                        </span>

                        <span class="badge bg-green-lt">
                            {{ importador.total_dados }} registro(s)
                        </span>
                    </div>

                    <Table :columns="colunasImportador(importador)" :records="{ data: importador.dados, links: [] }"
                        table-class="table-hover">
                        <template #body="{ item }">
                            <tr>
                                <td v-for="coluna in colunasImportador(importador)" :key="coluna" class="text-center">
                                    {{ item[coluna] ?? '-' }}
                                </td>
                            </tr>
                        </template>
                    </Table>

                    <small v-if="importador.total_dados > importador.dados.length" class="text-muted">
                        Exibindo os primeiros {{ importador.dados.length }} registros da planilha.
                    </small>
                </div>
            </div>
        </div>
    </div>
</template>
