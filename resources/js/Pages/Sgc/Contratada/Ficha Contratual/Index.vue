<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import NavbarContrato from "../NavbarContrato.vue";
import { ref, onMounted } from 'vue';

const props = defineProps({
    fichaData: Object,
    contratoId: Number,
    contrato: Object, 
});

const data = ref(props.fichaData || {});

const formatarData = (dataStr) => {
    if (!dataStr) return 'N/A';
    const dataFormatada = new Date(dataStr.split(' ')[0]);
    return dataFormatada.toLocaleDateString('pt-BR');
};

onMounted(() => {
    console.log('Dados da ficha:', data.value);
    console.log('Primeiro item do array:', data.value.data?.[0]);
});
</script>

<template>
    <AuthenticatedLayout>
        <Head :title="`Ficha Contratual - Contrato ${contratoId}`" />

        <template #header>

        </template>

        <NavbarContrato :tipo="contrato">
            <template #body>
                <div class="card">
                    <div class="card-body">
                        <div v-if="data.error" class="alert alert-danger">
                            {{ data.error }}
                        </div>
                        <div v-else-if="data.data && data.data.length > 0" class="row">
                            <!-- Dados Gerais -->
                            <div class="col-md-4">
                              <h4>Dados Gerais</h4>
                              <p v-if="data.data[0].NU_CON_FORMATADO"><strong>Número do Contrato:</strong> {{ data.data[0].NU_CON_FORMATADO }}</p>
                              <p v-if="data.data[0].NO_EMPRESA"><strong>Empresa Executora:</strong> {{ data.data[0].NO_EMPRESA }}</p>
                            </div>

                            <!-- Unidades Responsáveis -->
                            <div class="col-md-4">
                              <h4>Unidades Responsáveis</h4>
                              <p v-if="data.data[0].NM_UND_FISCAL"><strong>Fiscalização:</strong> {{ data.data[0].NM_UND_FISCAL }}</p>
                              <p v-if="data.data[0].NM_UND_GESTORA"><strong>Gestora:</strong> {{ data.data[0].NM_UND_GESTORA }}</p>
                              <p v-if="data.data[0].NM_FISCAL"><strong>Fiscal:</strong> {{ data.data[0].NM_FISCAL }}</p>
                              <p><strong>Substitutos:</strong> Sem dados</p>
                            </div>

                            <!-- Dados Básicos -->
                            <div class="col-md-4">
                              <h4>Dados Básicos</h4>
                              <p v-if="data.data[0].DS_FAS_CONTRATO"><strong>Situação:</strong> {{ data.data[0].DS_FAS_CONTRATO }}</p>
                              <p v-if="data.data[0].DT_INICIO"><strong>Início:</strong> {{ formatarData(data.data[0].DT_INICIO) }}</p>
                              <p v-if="data.data[0].DT_TERMINO_VIGENCIA"><strong>Término:</strong> {{ formatarData(data.data[0].DT_TERMINO_VIGENCIA) }}</p>
                              <p v-if="data.data[0].NU_PROCESSO"><strong>Processo:</strong> {{ data.data[0].NU_PROCESSO }}</p>
                              <p v-if="data.data[0].NU_EDITAL"><strong>Edital:</strong> {{ data.data[0].NU_EDITAL }}</p>
                            </div>

                            <!-- Valores Contratados -->
                            <div class="col-md-4">
                              <h4>Valores Contratados</h4>
                              <p v-if="data.data[0].VALOR_PI_MEDICAO"><strong>Valor PI:</strong> {{ data.data[0].VALOR_PI_MEDICAO }}</p>
                              <p v-if="data.data[0].VALOR_TOTAL_DE_ADITIVOS"><strong>Aditivos:</strong> {{ data.data[0].VALOR_TOTAL_DE_ADITIVOS }}</p>
                              <p v-if="data.data[0].VALOR_MEDICAO_PI_R"><strong>PI Vigente:</strong> {{ data.data[0].VALOR_MEDICAO_PI_R }}</p>
                              <p v-if="data.data[0].VALOR_TOTAL_DE_REAJUSTE"><strong>Reajuste:</strong> {{ data.data[0].VALOR_TOTAL_DE_REAJUSTE }}</p>
                              <p v-if="data.data[0].Valor_Medicao_PI_R_Ajuste_Acumulado"><strong>Total (PI+R):</strong> {{ data.data[0].Valor_Medicao_PI_R_Ajuste_Acumulado }}</p>
                            </div>

                            <!-- Valores Medidos -->
                            <div class="col-md-4">
                              <h4>Valores Medidos</h4>
                              <p><strong>Medição Atual:</strong> Sem dados</p>
                              <p v-if="data.data[0].VALOR_MEDICAO_PI_R"><strong>PI:</strong> {{ data.data[0].VALOR_MEDICAO_PI_R }}</p>
                              <p v-if="data.data[0].VALOR_TOTAL_DE_REAJUSTE"><strong>Reajuste:</strong> {{ data.data[0].VALOR_TOTAL_DE_REAJUSTE }}</p>
                              <p v-if="data.data[0].Valor_Medicao_PI_R_Ajuste_Acumulado"><strong>Total (PI+R):</strong> {{ data.data[0].Valor_Medicao_PI_R_Ajuste_Acumulado }}</p>
                            </div>

                            <!-- Saldo Contratual -->
                            <div class="col-md-4">
                              <h4>Saldo Contratual</h4>
                              <p><strong>Realizado:</strong> Sem dados</p>
                              <p v-if="data.data[0].VALOR_MEDICAO_PI_R"><strong>PI:</strong> {{ data.data[0].VALOR_MEDICAO_PI_R }}</p>
                              <p v-if="data.data[0].VALOR_TOTAL_DE_REAJUSTE"><strong>Reajuste:</strong> {{ data.data[0].VALOR_TOTAL_DE_REAJUSTE }}</p>
                              <p v-if="data.data[0].Valor_Medicao_PI_R_Ajuste_Acumulado"><strong>Total (PI+R):</strong> {{ data.data[0].Valor_Medicao_PI_R_Ajuste_Acumulado }}</p>
                            </div>
                        </div>
                        <div v-else>
                            <p class="text-muted">Nenhum dado encontrado</p>
                        </div>
                    </div>
                </div>
            </template>
        </NavbarContrato>
    </AuthenticatedLayout>
</template>

<style scoped>
.card {
    margin-top: 20px;
}

h2 {
    font-size: 1.5rem;
    margin: 0;
}

h4 {
    font-size: 1.1rem;
    margin-bottom: 10px;
}

p {
    font-size: 0.9rem;
    margin-bottom: 5px;
}

strong {
    display: inline-block;
    min-width: 120px;
}

.text-muted {
    text-align: center;
}
</style>