<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import NavbarContrato from "../NavbarContrato.vue";
import { ref, computed, onMounted } from 'vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';

const props = defineProps({
    fichaData: Object,
    contratoId: Number,
    contrato: Object,
    contratos: Object
});

const data = ref(props.fichaData || {});

const formatarData = (dataStr) => {
    if (!dataStr) return 'N/A';
    const dataFormatada = new Date(dataStr.split(' ')[0]);
    return dataFormatada.toLocaleDateString('pt-BR');
};

const formatarMoeda = (valor) => {
    if (valor === null || valor === undefined) return 'R$ 0,00';
    return Number(valor).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
};

const piVigente = computed(() => {
    if (!data.value.data?.[0]) return 0;
    const valorInicial = Number(data.value.data[0]?.VALOR_INICIAL) || 0;
    const valorAditivos = Number(data.value.data[0]?.VALOR_TOTAL_DE_ADITIVOS) || 0;
    return valorInicial + valorAditivos;
});

const totalPIR = computed(() => {
    if (!data.value.data?.[0]) return 0;
    const piVigenteValue = piVigente.value;
    const reajuste = Number(data.value.data[0]?.VALOR_TOTAL_DE_REAJUSTE) || 0;
    return piVigenteValue + reajuste;
});

const totalMedidoPIR = computed(() => {
    if (!data.value.data?.[0]) return 0;
    const piMedicao = Number(data.value.data[0]?.VALOR_PI_MEDICAO) || 0;
    const reajusteMedicao = Number(data.value.data[0]?.VALOR_REAJUSTE_MEDICAO) || 0;
    return piMedicao + reajusteMedicao;
});

const saldoPI = computed(() => {
    if (!data.value.data?.[0]) return 0;
    const valorInicial = Number(data.value.data[0]?.VALOR_INICIAL) || 0;
    const piMedicao = Number(data.value.data[0]?.VALOR_PI_MEDICAO) || 0;
    return valorInicial - piMedicao;
});

const saldoReajuste = computed(() => {
    if (!data.value.data?.[0]) return 0;
    const valorTotalReajuste = Number(data.value.data[0]?.VALOR_TOTAL_DE_REAJUSTE) || 0;
    const reajusteMedicao = Number(data.value.data[0]?.VALOR_REAJUSTE_MEDICAO) || 0;
    return valorTotalReajuste - reajusteMedicao;
});

const saldoTotalPIR = computed(() => {
    if (!data.value.data?.[0]) return 0;
    return saldoPI.value + saldoReajuste.value;
});

onMounted(() => {
    console.log('Dados da ficha:', data.value);
    console.log('Primeiro item do array:', data.value.data?.[0]);
});
</script>

<template>
    <AuthenticatedLayout>
        <Head :title="`Ficha Contratual - Contrato ${contratoId}`" />

        <template #header>
            <div class="w-100 d-flex justify-content-between">
                <Breadcrumb
                    class="align-self-center"
                    :links="[
                        { route: route('sgc.gestao.listagem', contratos.tipo_contrato), label: `Gestão de Contratos` },
                        { route: '#', label: contratos.contratada }
                    ]"
                />
            </div>
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
                            <div class="col-md-4 mb-4">
                                <div class="block-card">
                                    <h4 class="text-center">DADOS GERAIS</h4>
                                    <p v-if="data.data[0].NU_CON_FORMATADO"><strong>Número do Contrato:</strong> {{ data.data[0].NU_CON_FORMATADO }}</p>
                                    <p v-if="data.data[0].NO_EMPRESA"><strong>Empresa Executora:</strong> {{ data.data[0].NO_EMPRESA }}</p>
                                </div>
                            </div>

                            <!-- Unidades Responsáveis -->
                            <div class="col-md-4 mb-4">
                                <div class="block-card">
                                    <h4 class="text-center">UNIDADES RESPONSÁVEIS</h4>
                                    <p v-if="data.data[0].NM_UND_FISCAL"><strong>Fiscalização:</strong> {{ data.data[0].NM_UND_FISCAL }}</p>
                                    <p v-if="data.data[0].NM_UND_GESTORA"><strong>Gestora:</strong> {{ data.data[0].NM_UND_GESTORA }}</p>
                                    <p v-if="data.data[0].NM_FISCAL"><strong>Fiscal:</strong> {{ data.data[0].NM_FISCAL }}</p>
                                    <p><strong>Substitutos:</strong> Sem dados</p>
                                </div>
                            </div>

                            <!-- Dados Básicos -->
                            <div class="col-md-4 mb-4">
                                <div class="block-card">
                                    <h4 class="text-center">DADOS BÁSICOS</h4>
                                    <p v-if="data.data[0].DS_FAS_CONTRATO"><strong>Situação:</strong> {{ data.data[0].DS_FAS_CONTRATO }}</p>
                                    <p v-if="data.data[0].DT_DIA"><strong>Início:</strong> {{ formatarData(data.data[0].DT_DIA) }}</p>
                                    <p v-if="data.data[0].DT_TER_ATZ"><strong>Término:</strong> {{ formatarData(data.data[0].DT_TER_ATZ) }}</p>
                                    <p v-if="data.data[0].NU_PROCESSO"><strong>Processo:</strong> {{ data.data[0].NU_PROCESSO }}</p>
                                    <p v-if="data.data[0].NU_EDITAL"><strong>Edital:</strong> {{ data.data[0].NU_EDITAL }}</p>
                                </div>
                            </div>

                            <!-- Valores Contratados -->
                            <div class="col-md-4 mb-4">
                                <div class="block-card">
                                    <h4 class="text-center">VALORES CONTRATADOS</h4>
                                    <p v-if="data.data[0].VALOR_INICIAL"><strong>Valor PI:</strong> {{ formatarMoeda(data.data[0].VALOR_INICIAL) }}</p>
                                    <p v-if="data.data[0].VALOR_TOTAL_DE_ADITIVOS"><strong>Aditivos:</strong> {{ formatarMoeda(data.data[0].VALOR_TOTAL_DE_ADITIVOS) }}</p>
                                    <p v-if="piVigente"><strong>PI Vigente:</strong> {{ formatarMoeda(piVigente) }}</p>
                                    <p v-if="data.data[0].VALOR_TOTAL_DE_REAJUSTE"><strong>Reajuste:</strong> {{ formatarMoeda(data.data[0].VALOR_TOTAL_DE_REAJUSTE) }}</p>
                                    <p v-if="totalPIR"><strong>Total (PI+R):</strong> {{ formatarMoeda(totalPIR) }}</p>
                                </div>
                            </div>

                            <!-- Valores Medidos -->
                            <div class="col-md-4 mb-4">
                                <div class="block-card">
                                    <h4 class="text-center">VALORES MEDIDOS</h4>
                                    <p v-if="data.data[0].VALOR_PI_MEDICAO"><strong>PI:</strong> {{ formatarMoeda(data.data[0].VALOR_PI_MEDICAO) }}</p>
                                    <p v-if="data.data[0].VALOR_REAJUSTE_MEDICAO"><strong>Reajuste:</strong> {{ formatarMoeda(data.data[0].VALOR_REAJUSTE_MEDICAO) }}</p>
                                    <p v-if="totalMedidoPIR"><strong>Total (PI+R):</strong> {{ formatarMoeda(totalMedidoPIR) }}</p>
                                </div>
                            </div>

                            <!-- Saldo Contratual -->
                            <div class="col-md-4 mb-4">
                                <div class="block-card">
                                    <h4 class="text-center">SALDO CONTRATUAL</h4>
                                    <p v-if="saldoPI !== 0"><strong>PI:</strong> {{ formatarMoeda(saldoPI) }}</p>
                                    <p v-if="saldoReajuste !== 0"><strong>Reajuste:</strong> {{ formatarMoeda(saldoReajuste) }}</p>
                                    <p v-if="saldoTotalPIR !== 0"><strong>Total (PI+R):</strong> {{ formatarMoeda(saldoTotalPIR) }}</p>
                                </div>
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
    margin-top: px;
}

h2 {
    font-size: 1.5rem;
    margin: 0;
}

h4 {
    font-size: 1.1rem;
    margin-bottom: 30px;
    text-align: center;
}

.block-card {
    background-color: #fdfdfd; /* Fundo leve para destacar os blocos */
    border: 1px solid #ecebeb; /* Borda sutil */
    border-radius: 5px; /* Cantos arredondados */
    padding: 15px; /* Espaçamento interno */
    min-height: 230px; /* Altura mínima para uniformidade */
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

.col-md-4 {
    margin-bottom: 20px;
}

.row {
    justify-content: space-between;
}
</style>
