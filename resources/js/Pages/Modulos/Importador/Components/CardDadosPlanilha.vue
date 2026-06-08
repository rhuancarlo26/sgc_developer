<script setup>
import { computed, onMounted, ref } from "vue";
import { dateTimeFormat } from '@/Utils/DateTimeUtils';
import Table from '@/Components/Table.vue';
import axios from "axios";
import Swal from "sweetalert2";
import { IconTrash } from "@tabler/icons-vue";

const props = defineProps({
    form: { type: Object }
});

const emit = defineEmits(['tem-dados-changed']);

const dados = ref({
    data: []
});

const carregando = ref(false);
const excluindo = ref(false);

const camposComputed = computed(() => {
    return props.form.modulo?.campos || [];
});

const totalRegistros = computed(() => {
    return Number(
        dados.value?.total
        ?? dados.value?.meta?.total
        ?? dados.value?.data?.length
        ?? 0
    );
});

const temDados = computed(() => {
    return totalRegistros.value > 0;
});

const notificarEstadoDados = () => {
    emit('tem-dados-changed', temDados.value);
};

const buscarDados = () => {
    carregando.value = true;

    return axios.get(route('modulos.importador.buscarDados', [route().params.importador]))
        .then(resp => {
            dados.value = { ...resp.data };
            notificarEstadoDados();

            return resp.data;
        })
        .catch(() => {
            dados.value = { data: [] };
            notificarEstadoDados();

            return { data: [] };
        })
        .finally(() => {
            carregando.value = false;
        });
};

onMounted(() => {
    buscarDados();
});

const updateRecordsState = (registros) => {
    dados.value = { ...registros };
    notificarEstadoDados();
};

const excluirDadosPlanilha = async () => {
    if (!temDados.value || excluindo.value) {
        return;
    }

    const result = await Swal.fire({
        title: 'Excluir dados da planilha?',
        text: 'Todos os dados importados desta planilha serão excluídos e não poderão ser recuperados. Após a exclusão, será possível importar novamente.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sim, excluir',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#d33',
    });

    if (!result.isConfirmed) {
        return;
    }

    excluindo.value = true;

    axios.delete(route('modulos.importador.excluirDados', [route().params.importador]))
        .then(() => {
            dados.value = { data: [] };
            emit('tem-dados-changed', false);

            Swal.fire({
                title: 'Excluído!',
                text: 'Os dados da planilha foram excluídos com sucesso.',
                icon: 'success',
                timer: 1800,
                showConfirmButton: false,
            });

            buscarDados();
        })
        .catch(() => {
            Swal.fire({
                title: 'Erro',
                text: 'Não foi possível excluir os dados da planilha.',
                icon: 'error',
            });
        })
        .finally(() => {
            excluindo.value = false;
        });
};

defineExpose({
    buscarDados,
});
</script>

<template>
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="my-0">Dados Planilha</h3>

            <button v-if="temDados" type="button" class="btn btn-danger" :disabled="excluindo || carregando"
                @click="excluirDadosPlanilha">
                <span v-if="excluindo" class="spinner-border spinner-border-sm me-2" role="status"
                    aria-hidden="true"></span>

                <IconTrash v-else class="me-2" :size="18" />

                {{ excluindo ? 'Excluindo...' : 'Excluir dados da planilha' }}
            </button>
        </div>

        <div class="card-body">
            <Table :columns="camposComputed.map(item => item.nome_campo)" :records="dados" table-class="table-hover"
                :axiosPagination="true" @updateRecordsState="updateRecordsState">
                <template #body="{ item }">
                    <tr class="cursor-pointer">
                        <td v-for="(col, keyCampo) in camposComputed" :key="keyCampo" class="text-center">
                            <span v-if="col.tipo === 'data'">
                                {{ dateTimeFormat(item[col.nome_campo]) }}
                            </span>

                            <span v-else>
                                {{ item[col.nome_campo] }}
                            </span>
                        </td>
                    </tr>
                </template>
            </Table>

            <div v-if="!temDados && !carregando" class="alert alert-info mt-3 mb-0">
                Nenhum dado importado para esta planilha.
            </div>
        </div>
    </div>
</template>
