<script setup>

import Pagination from "@/Components/Pagination.vue";
import { ref } from "vue";

const props = defineProps({
    columns: Array,
    records: { type: Object, default: { data: [], links: [] } },
    tableClass: String,
    rowClass: String,
    inertiaPagination: { type: Boolean, default: true },
    only: { type: Array, default: [] },
    axiosPagination: { type: Boolean, default: false },
    excelRoute: String
});

const recordsState = ref({ ...props.records });

function updateRecordsState(records) {
    recordsState.value = { ...records }
}

</script>

<template>
    <div class="card">
        <div class="table-responsive">
            <table class="table card-table table-bordered" :class="tableClass">
                <thead>
                    <tr>
                        <th v-for="column in columns" :key="column">{{ column }}</th>
                    </tr>
                </thead>
                <tbody>
                    <slot v-if="records?.data?.length" v-for="(record, i) in records.data" name="body" :key="i"
                        :item="record" />

                    <tr v-else>
                        <td :colspan="columns.length" class="text-center py-3">
                            Nenhum registro encontrado
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="card-footer" v-if="records?.data?.length">
            <Pagination :records="records" @pageChange="updateRecordsState" :inertiaPagination="inertiaPagination"
                :axiosPagination="axiosPagination" :only="only" :excelRoute="excelRoute" />

        </div>
    </div>
</template>
