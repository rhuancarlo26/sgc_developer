<script setup>
import {dateTimeFormat} from "@/Utils/DateTimeUtils.js";
import {IconEye, IconTrash} from "@tabler/icons-vue";
import LinkConfirmation from "@/Components/LinkConfirmation.vue";
import {Link} from "@inertiajs/vue3";
import NavButton from "@/Components/NavButton.vue";

const props = defineProps({
    acas: {type: Array}
});
</script>

<template>
    <div class="row mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table card-table table-bordered table-hover">
                    <thead>
                    <tr>
                        <th>ID ACA</th>
                        <th>Data do ACA</th>
                        <th>Relação de RNC's</th>
                        <th>Local do RNC</th>
                        <th>Classificação do RNC</th>
                        <th>Lote</th>
                        <th>Empresa / Consórcio</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="aca in acas" :key="aca.id">
                        <td>{{ aca.nome_id }}</td>
                        <td>{{ dateTimeFormat(aca.data_aca) }}</td>
                        <td>
                                <span v-for="rnc in aca.rncs" :key="rnc.id"
                                      class="badge bg-warning text-white m-1">
                                    {{ rnc.nome_id }}
                                </span>
                        </td>
                        <td>
                            <span v-for="rnc in [...new Set(aca.rncs.map(item => item.local))]" :key="rnc.id"
                                  class="badge bg-warning text-white m-1">
                                    {{ rnc }}
                                </span>
                        </td>
                        <td>
                            <span v-for="rnc in [...new Set(aca.rncs.map(item => item.classificacao))]" :key="rnc.id"
                                  class="badge bg-warning text-white m-1">
                                    {{ rnc }}
                                </span>
                        </td>
                        <td>{{ aca.lote?.nome_id }}</td>
                        <td>{{ aca.lote?.empresa }}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<style scoped>

</style>
