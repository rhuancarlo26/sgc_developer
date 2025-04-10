<script setup>
import {dateTimeFormat} from "../../../../../Utils/DateTimeUtils.js";

const props = defineProps({
    relatorio: {type: Object}
});
</script>
<template>
    <div class="d-flex flex-column justify-content-between min-vh-100">
        <div>
            <h4>7. Campanhas</h4>
            <div class="card mb-4">
                <div class="table-responsive">
                    <table class="table card-table table-bordered">
                        <thead>
                        <tr>
                            <th class="text-center">ID</th>
                            <th class="text-center">N° ABIO</th>
                            <th class="text-center">Período</th>
                            <th class="text-center">Data inicial</th>
                            <th class="text-center">Data final</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="campanha in relatorio.resultado?.campanhas" :key="campanha.id">
                            <td class="text-center">{{ campanha.id }}</td>
                            <td class="text-center">
                                <span class="badge text-white bg-warning me-1"
                                      v-for="licenca in campanha.abios.map(abio => abio.abio?.licenca?.numero_licenca)"
                                      :key="licenca">{{ licenca }}</span>
                            </td>
                            <td class="text-center">{{ campanha.periodo }}</td>
                            <td class="text-center">{{ dateTimeFormat(campanha.data_inicial) }}</td>
                            <td class="text-center">{{ dateTimeFormat(campanha.data_final) }}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div>
                <h4>8. Sobre o relatório</h4>
                <span>{{ relatorio.sobre_relatorio }}</span>
            </div>
        </div>
    </div>
</template>
