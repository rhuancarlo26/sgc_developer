<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
    import { Head } from '@inertiajs/vue3';
    import NavbarContrato from "../NavbarContrato.vue";
    import { ref, onMounted, computed } from 'vue';
    import { Doughnut } from 'vue-chartjs';
    import { Chart as ChartJS, Title, Tooltip, Legend, ArcElement } from 'chart.js';
    
    ChartJS.register(Title, Tooltip, Legend, ArcElement);
    
    const props = defineProps({
        quantitativosData: Object,
        contratoId: Number,
        contrato: Object,
    });
    
    const data = ref(props.quantitativosData || {});
    const filtroFamilia = ref(''); 
    
    const familiasUnicas = computed(() => {
        const familias = new Set(data.value.map(item => item.familia));
        return ['Todas', ...Array.from(familias).filter(f => f)];
    });
    
    const dataFiltrada = computed(() => {
        if (!filtroFamilia.value || filtroFamilia.value === 'Todas') {
            return data.value;
        }
        return data.value.filter(item => item.familia === filtroFamilia.value);
    });
    
    const totais = computed(() => {
        if (!dataFiltrada.value || dataFiltrada.value.length === 0) {
            return {
                r_total_contrato: 0,
                r_ose: 0,
                r_medido: 0,
            };
        }
    
        return {
            r_total_contrato: dataFiltrada.value.reduce((sum, item) => sum + (parseFloat(item.r_total_contrato) || 0), 0),
            r_ose: dataFiltrada.value.reduce((sum, item) => sum + (parseFloat(item.r_ose) || 0), 0),
            r_medido: dataFiltrada.value.reduce((sum, item) => sum + (parseFloat(item.r_medido) || 0), 0),
        };
    });
    
    const formatarMoeda = (valor) => {
        if (!valor && valor !== 0) return 'R$ 0,00';
        return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(valor);
    };
    
    const chartData = computed(() => ({
        labels: ['Total Contrato', 'OSE', 'Medido'],
        datasets: [{
            data: [totais.value.r_total_contrato, totais.value.r_ose, totais.value.r_medido],
            backgroundColor: ['#444d71', '#36A2EB', '#2d8407'],
            hoverBackgroundColor: ['#444d71', '#36A2EB', '#2d8407'],
            borderWidth: 1,
        }],
    }));
    
    const chartOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'right',
                labels: {
                    generateLabels: (chart) => {
                        const { data } = chart;
                        return data.labels.map((label, index) => ({
                            text: `${label}: ${formatarMoeda(data.datasets[0].data[index])}`,
                            fillStyle: data.datasets[0].backgroundColor[index],
                            strokeStyle: data.datasets[0].backgroundColor[index],
                            hidden: isNaN(data.datasets[0].data[index]) || data.datasets[0].data[index] === 0,
                            index,
                        }));
                    },
                },
            },
            tooltip: {
                enabled: true,
                callbacks: {
                    label: (context) => {
                        const label = context.label || '';
                        const value = context.raw || 0;
                        return `${label}: ${formatarMoeda(value)}`;
                    },
                },
            },
        },
        cutout: '75%',
    };
    
    onMounted(() => {
        console.log('Dados dos quantitativos:', data.value);
        console.log('Totais calculados:', totais.value);
    });
    </script>
    
    <template>
        <AuthenticatedLayout>
            <Head :title="`Quantitativos - Contrato ${contratoId}`" />
    
            <template #header>
                <div class="w-100 d-flex justify-content-between">
                    <h2>Quantitativos - Contrato {{ contratoId }}</h2>
                </div>
            </template>
    
            <NavbarContrato :tipo="contrato">
                <template #body>
                    <div class="card">
                        <div class="card-body">
                            <label for="filtro-familia" class="me-2">Filtrar por Família:</label>
                            <div class="filter-container mb-3">
                                <select id="filtro-familia" v-model="filtroFamilia" class="form-select">
                                    <option value="">Todas</option>
                                    <option v-for="familia in familiasUnicas" :key="familia" :value="familia">
                                        {{ familia }}
                                    </option>
                                </select>
                            </div>
                            <div class="w-100 d-flex justify-content-center align-items-center mb-3">
                                <h3 class="subprodutos-title">QUANTITATIVOS - SUBPRODUTOS</h3>
                            </div>
                            <div v-if="data.error" class="alert alert-danger">
                                {{ data.error }}
                            </div>
                            <div v-else-if="dataFiltrada.length > 0">
                                <div class="chart-container">
                                    <h4></h4>
                                    <Doughnut :data="chartData" :options="chartOptions" />
                                </div>
    
                                <div class="table-responsive mt-4">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Cod. SIAC</th>
                                                <th>Produto</th>
                                                <th>Subproduto</th>
                                                <th>Família</th>
                                                <th>Descrição SIAC</th>
                                                <th>Descrição Revisada</th>
                                                <th>Und</th>
                                                <th>Etapa</th>
                                                <th>Contrato</th>
                                                <th>Req. Ext.</th>
                                                <th>Prazo de Elaboração</th>
                                                <th>Qtd Contrato</th>
                                                <th>Qtd 1ª TA</th>
                                                <th>Qtd 2º TA</th>
                                                <th>Qtd OSE</th>
                                                <th>Qtd Saldo OSE</th>
                                                <th>Qtd Medido</th>
                                                <th>Qtd Saldo Medido</th>
                                                <th>Preço Unitário</th>
                                                <th>Total Contrato</th>
                                                <th>R$ 1º TA</th>
                                                <th>R$ 2º TA</th>
                                                <th>OSE</th>
                                                <th>Saldo OSE</th>
                                                <th>Medido</th>
                                                <th>Saldo a Medir</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(item, index) in dataFiltrada" :key="index">
                                                <td>{{ item.cod_siac }}</td>
                                                <td>{{ item.produto }}</td>
                                                <td>{{ item.subproduto }}</td>
                                                <td>{{ item.familia }}</td>
                                                <td>{{ item.descricao_siac }}</td>
                                                <td>{{ item.descricao_revisada }}</td>
                                                <td>{{ item.und }}</td>
                                                <td>{{ item.etapa }}</td>
                                                <td>{{ item.contrato }}</td>
                                                <td>{{ item.req_ext }}</td>
                                                <td>{{ item.prazo_de_elaboracao }}</td>
                                                <td>{{ item.qtd_contrato }}</td>
                                                <td>{{ item.qtd_1_ta }}</td>
                                                <td>{{ item.qtd_2_ta }}</td>
                                                <td>{{ item.qtd_ose }}</td>
                                                <td>{{ item.qtd_saldo_ose }}</td>
                                                <td>{{ item.qtd_medido }}</td>
                                                <td>{{ item.qtd_saldo_medido }}</td>
                                                <td>{{ formatarMoeda(item.r_preco_unitario) }}</td>
                                                <td>{{ formatarMoeda(item.r_total_contrato) }}</td>
                                                <td>{{ formatarMoeda(item.r_1_ta) }}</td>
                                                <td>{{ formatarMoeda(item.r_2_ta) }}</td>
                                                <td>{{ formatarMoeda(item.r_ose) }}</td>
                                                <td>{{ formatarMoeda(item.r_saldo_ose) }}</td>
                                                <td>{{ formatarMoeda(item.r_medido) }}</td>
                                                <td>{{ formatarMoeda(item.r_saldo_a_medir) }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
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
    
    .table {
        font-size: 0.9rem;
    }
    
    .text-muted {
        text-align: center;
    }
    
    .chart-container {
        position: relative;
        max-width: 600px;
        height: 300px;
        margin: 0 auto;
        display: flex;
        justify-content: center;
        align-items: center;
    }
    
    .mt-4 {
        margin-top: 1.5rem;
    }
    
    .subprodutos-title {
        font-size: 1.7rem;
        margin: -5rem 0 2rem 0;
        color: #333;
    }
    
    /* Estilo para o container do filtro */
    .filter-container {
        display: flex;
        align-items: center;
        margin-bottom: 1rem;
    }
    
    /* Estilo para o select */
    .form-select {
        width: 200px;
        display: inline-block;
    }
    </style>