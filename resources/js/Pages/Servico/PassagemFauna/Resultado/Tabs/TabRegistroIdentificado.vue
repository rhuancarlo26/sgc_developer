<script setup>
import Table from "@/Components/Table.vue";
import {computed} from "vue";
import InputLabel from "@/Components/InputLabel.vue";
import NavButton from "@/Components/NavButton.vue";

const emit = defineEmits(['salvar']);

const props = defineProps({
    form: {type: Object},
    resultado: {type: Object}
});

const numTotalIndividuos = computed(() => {
    return props.resultado.campanhas.reduce((acumuladorCampanha, campanha) => {
        return acumuladorCampanha + campanha.registros.reduce((acumulador, item) => acumulador + item.n_individuos, 0);
    }, 0);
});

const salvar = () => {
    props.form.id_resultado = props.resultado.id;
    props.form.form = 1;

    const url = props.form.id ? 'update_analise' : 'store_analise'

    props.form.post(route('contratos.contratada.servicos.passagem_fauna.resultado.' + url, {
        contrato: props.contrato.id,
        servico: props.servico.id,
        resultado: props.resultado.id
    }))
}

</script>

<template>
    <div class="row mb-4">
        <div class="col">
            <Table table-class="table-hover"
                   :columns="['Espécie', 'Nome comum', 'N° indivíduos', 'Frequência (%)', 'IUCN', 'Federal']"
                   :records="{data:resultado.campanhas.map(campanha => campanha.registros).flat(), links:[]}">
                <template #body="{item}">
                    <tr class="cursor-pointer">
                        <td>{{ item.especie }}</td>
                        <td>{{ item.nome_comum }}</td>
                        <td>{{ item.n_individuos }}</td>
                        <td>{{ parseFloat(item.n_individuos * 100 / numTotalIndividuos).toFixed(4) }}%</td>
                        <td>{{ `${item.status_iunc.sigla} - ${item.status_iunc.nome}` }}</td>
                        <td>{{ `${item.status_federal.sigla} - ${item.status_federal.nome}` }}</td>
                    </tr>
                </template>
            </Table>
        </div>
    </div>
    <div class="row mb-4">
        <div class="col">
            <span><strong>Número toal de indivíduos: </strong>{{ numTotalIndividuos }}</span>
        </div>
    </div>
    <div class="row mb-4">
        <div class="col">
            <InputLabel value="Análise" for="registros_identificados"/>
            <textarea class="form-control" name="registros_identificados" id="registros_identificados"
                      v-model="form.registros_identificados"
                      rows="5"></textarea>
        </div>
    </div>
    <div class="d-flex justify-content-end">
        <NavButton @click="salvar()" type-button="success" :title="'Salvar'"/>
    </div>
</template>

<style scoped>

</style>
