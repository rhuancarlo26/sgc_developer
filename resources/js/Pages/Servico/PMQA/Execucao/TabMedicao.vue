<script setup>
import { IconEye } from '@tabler/icons-vue';
import { ref } from 'vue';
import { watch } from 'vue';

const props = defineProps({
    contrato: { type: Object },
    servico: { type: Object },
    campanha: { type: Object },
    ponto: { type: Object }
});

const medicaoParametro = ref([]);

watch(() => props.ponto,
    () => {
        medicaoParametro.value = [];
        if (props.ponto.medicao?.parametros.length) {
            console.log('pontos', props.ponto.medicao?.parametros)
            props.ponto.medicao?.parametros.forEach(parametro => {
                medicaoParametro.value[parametro.fk_parametro] = parametro.medicao;
            });
        }
    });

</script>
<template>
    <div v-if="ponto.medicao">
        <div v-if="!ponto.medicao?.sem_coleta">
            <div class="table-responsive mb-4">
                <table class="table table-hover non-hover">
                    <thead>
                        <tr>
                            <th>Parâmetro</th>
                            <th>Unidade</th>
                            <th>Limite</th>
                            <th>Medição</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="3">IQA</td>
                            <td>{{ ponto.medicao?.iqa }}</td>
                        </tr>
                        <tr v-for="vinculado in ponto.ponto?.lista.parametros_vinculados" :key="vinculado.id">
                            <td>{{ vinculado.parametro?.parametro }}</td>
                            <td>{{ vinculado.parametro?.unidade }}</td>
                            <td>{{ vinculado.parametro?.classe_2 ?? 'Sem limite' }}</td>
                            <td>{{ medicaoParametro[vinculado.fk_parametro] }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div v-if="ponto.medicao?.arquivos?.length" class="table-responsive mt-4">
                <table class="table table-hover non-hover">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Açao</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="arquivo in ponto.medicao.arquivos" :key="arquivo.id">
                            <td>{{ arquivo.nome }}</td>
                            <td>
                                <a class="btn btn-icon btn-primary me-1" target="_blank"
                                    :href="route('contratos.contratada.servicos.pmqa.execucao.medir.show_arquivo', { contrato: props.contrato.id, servico: props.servico.id, campanha: props.campanha.id, arquivo: arquivo.id })">
                                    <IconEye />
                                </a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div v-else>
            <div class="row mb-4">
                <div class="col">
                    <span><strong>Realizado a coleta?: </strong>{{ ponto.medicao?.sem_coleta ? 'Não' : 'Sim' }}</span>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <span><strong>Justificativa: </strong>{{ ponto.medicao?.justificativa }}</span>
                </div>
            </div>
        </div>
    </div>
    <div v-else>
        <div class="row mb-4">
            <div class="col">
                <span class="d-flex align-item-center"><strong>Sem medição</strong></span>
            </div>
        </div>
    </div>
</template>
