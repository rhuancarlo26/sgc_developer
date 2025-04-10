<script setup>
import {dateTimeFormat} from "@/Utils/DateTimeUtils.js";
import {useForm} from "@inertiajs/vue3";
import {IconDeviceFloppy} from "@tabler/icons-vue";
import NavButton from "@/Components/NavButton.vue";

const props = defineProps({
    resultado: {type: Object},
    form: {type: Object},
    roa_atendido: {type: Array}
});

const salvar = () => {
    props.form.id = props.resultado.analise?.id;
    props.form.form = 1;

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
                        <th>ID ocorrência</th>
                        <th>Intensidade</th>
                        <th>Local ocorrência</th>
                        <th>Classificação ocorrência</th>
                        <th>Data da ocorrência</th>
                        <th>Lote</th>
                        <th>Empresa / Consórcio</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="roa in roa_atendido" :key="roa.id">
                        <td>{{ roa.nome_id }}</td>
                        <td>{{ roa.intensidade }}</td>
                        <td>{{ roa.local }}</td>
                        <td>{{ roa.classificacao }}</td>
                        <td>{{ dateTimeFormat(roa.data_ocorrencia) }}</td>
                        <td>{{ roa.lote?.nome_id }}</td>
                        <td>{{ roa.lote?.empresa }}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="row mb-4">
        <div class="col">
            <textarea class="form-control" v-model="form.roas_atendidos" rows="5"></textarea>
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
