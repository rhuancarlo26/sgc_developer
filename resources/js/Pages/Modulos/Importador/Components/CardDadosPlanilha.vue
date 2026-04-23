<script setup>
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import { computed, onMounted, ref } from "vue";
import { dateTimeFormat } from '@/Utils/DateTimeUtils';
import Table from '@/Components/Table.vue';
import axios from "axios";

const props = defineProps({
    form: { type: Object }
});

const dadosComputed = computed(() => {
    return props.dados.map(item => item.dados)
})

const camposComputed = computed(() => {
    return props.form.modulo?.campos
})

const dados = ref({
    data: []
})

const buscarDados = () => {
    axios.get(route('modulos.importador.buscarDados', [route().params.importador]))
        .then(resp => {
            dados.value = { ...resp.data }
        })
}

onMounted(() => {
    buscarDados()
})

const updateRecordsState = (registros) => {
    dados.value = { ...registros }
}

</script>
<template>
    <div class="card">
        <div class="card-header">
            <h3 class="my-0">Dados Planilha</h3>
        </div>

        <div class="card-body">
            <Table :columns="camposComputed.map(item=>item.nome_campo)" :records="dados"
            table-class="table-hover" :axiosPagination="true" @updateRecordsState="updateRecordsState">
                <template #body="{ item }">
                    <tr class="cursor-pointer">
                        <td v-for="(col, keyCampo) in camposComputed" :key="keyCampo" class="text-center">
                            <span v-if="col.tipo === 'data'">{{ dateTimeFormat(item[col.nome_campo]) }}</span>
                            <span v-else>{{ item[col.nome_campo] }} </span>
                        </td>
                    </tr>
                </template>
            </Table>
            <div class="table-responsive">
                <!-- <table class="table card-table table-bordered" :class="tableClass">
                    <thead>
                        <tr>
                            <th v-for="(column, key) in camposComputed" :key="key" class="text-center">{{ column.nome_campo }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(d, keyD) in dadosComputed" :key="keyD">
                            <td v-for="(col, keyCampo) in camposComputed" :key="keyCampo" class="text-center">
                                <span v-if="col.tipo === 'data'">{{ dateTimeFormat(d[col.nome_campo]) }}</span>
                                <span v-else>{{ d[col.nome_campo] }} </span>
                            </td>
                        </tr>
                    </tbody>
                </table> -->
            </div>                
        </div>
    </div>
</template>
