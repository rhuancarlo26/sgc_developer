<script setup>
import {dateTimeFormat} from "@/Utils/DateTimeUtils.js";
import NavButton from "@/Components/NavButton.vue";
import {IconDeviceFloppy} from "@tabler/icons-vue";

const props = defineProps({
    resultado: {type: Object},
    form: {type: Object},
    acas: {type: Array}
});

const salvar = () => {
    props.form.id = props.resultado.analise?.id;
    props.form.form = 9;

    props.form.post(route('contratos.contratada.servicos.cont_ocorrencia.resultado.store_analise', {
        contrato: props.form.contrato_id,
        servico: props.form.servico_id,
        resultado: props.form.id_resultado
    }))
}
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
    <div class="row mb-4">
        <div class="col">
            <textarea class="form-control" v-model="form.aca_gerados" rows="5"></textarea>
        </div>
    </div>
    <div class="row mb-4">
        <div class="col d-flex justify-content-end">
            <NavButton @click="salvar()" type-button="success" :icon="IconDeviceFloppy"
                       title="Salvar"/>
        </div>
    </div>
</template>

<style scoped>

</style>
