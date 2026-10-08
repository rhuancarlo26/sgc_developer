<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
    import { Head } from '@inertiajs/vue3';
    import NavbarContrato from "../NavbarContrato.vue";
    import { ref, computed, watch, nextTick, onMounted, onBeforeUnmount } from 'vue';
    import { Doughnut } from 'vue-chartjs';
    import { Chart as ChartJS, Title, Tooltip, Legend, ArcElement } from 'chart.js';
    import Breadcrumb from '@/Components/Breadcrumb.vue';

    ChartJS.register(Title, Tooltip, Legend, ArcElement);

    const props = defineProps({
        quantitativosData: Object,
        contratoId: Number,
        contrato: Object,
        contratos: Object
    });

    const data = computed(() => Array.isArray(props.quantitativosData) ? props.quantitativosData : []);
    const filtroFamilia = ref('');

    const familiasUnicas = computed(() => {
        const familias = new Set(data.value.map(item => item.familia));
        return Array.from(familias).filter(Boolean).sort();
    });

    const dataFiltrada = computed(() => {
        if (!filtroFamilia.value || filtroFamilia.value === 'Todas') {
            return data.value;
        }
        return data.value.filter(item => item.familia === filtroFamilia.value);
    });

    const pagina = ref(1);
    const porPagina = ref(25);
    const totalPaginas = computed(() => Math.max(1, Math.ceil(dataFiltrada.value.length / porPagina.value)));
    const inicio = computed(() => (pagina.value - 1) * porPagina.value);
    const dadosPagina = computed(() => dataFiltrada.value.slice(inicio.value, inicio.value + porPagina.value));
    watch([filtroFamilia, porPagina], () => { pagina.value = 1; });
    watch(totalPaginas, total => { pagina.value = Math.min(pagina.value, total); });
    const rolagemSuperior = ref(null);
    const tabelaContainer = ref(null);
    const tabela = ref(null);
    const larguraTabela = ref(0);
    const temRolagem = ref(false);
    const cabecalhoFixo = ref(false);
    const colunasCabecalho = ref([]);
    const estiloCabecalho = ref({});
    const larguraCabecalho = ref(0);
    const deslocamentoCabecalho = ref(0);
    let quadroRolagem;
    const atualizarCabecalho = () => {
        const container = tabelaContainer.value;
        const cabecalho = tabela.value?.tHead;
        if (!container || !cabecalho) { cabecalhoFixo.value = false; return; }
        const limite = container.getBoundingClientRect();
        const altura = cabecalho.getBoundingClientRect().height;
        cabecalhoFixo.value = limite.top < 0 && limite.bottom > altura;
        estiloCabecalho.value = { left: limite.left + container.clientLeft + 'px', width: container.clientWidth + 'px' };
        deslocamentoCabecalho.value = container.scrollLeft;
    };
    const medirCabecalho = () => {
        const cabecalho = tabela.value?.tHead;
        if (!cabecalho) return;
        colunasCabecalho.value = Array.from(cabecalho.rows[0].cells, celula => ({ titulo: celula.textContent, largura: celula.getBoundingClientRect().width }));
        larguraCabecalho.value = tabela.value.getBoundingClientRect().width;
        atualizarCabecalho();
    };
    const agendarCabecalho = () => {
        if (quadroRolagem) return;
        quadroRolagem = requestAnimationFrame(() => { quadroRolagem = null; atualizarCabecalho(); });
    };
    onMounted(() => window.addEventListener('scroll', agendarCabecalho, { passive: true, capture: true }));
    let observador;
    const atualizarRolagem = () => {
        larguraTabela.value = tabela.value?.scrollWidth || 0;
        temRolagem.value = larguraTabela.value > (tabelaContainer.value?.clientWidth || 0);
        medirCabecalho();
    };
    const sincronizarRolagem = (origem, destino) => {
        if (origem && destino && Math.abs(destino.scrollLeft - origem.scrollLeft) > 1) destino.scrollLeft = origem.scrollLeft;
        agendarCabecalho();
    };
    watch([tabela, tabelaContainer], async () => {
        observador?.disconnect();
        await nextTick();
        if (!tabela.value || !tabelaContainer.value) return;
        observador = new ResizeObserver(atualizarRolagem);
        observador.observe(tabela.value);
        observador.observe(tabelaContainer.value);
        atualizarRolagem();
    }, { flush: 'post' });
    watch(rolagemSuperior, elemento => {
        if (elemento && tabelaContainer.value) elemento.scrollLeft = tabelaContainer.value.scrollLeft;
    }, { flush: 'post' });
    onBeforeUnmount(() => {
        observador?.disconnect();
        window.removeEventListener('scroll', agendarCabecalho, true);
        if (quadroRolagem) cancelAnimationFrame(quadroRolagem);
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

    const grafico = ref(null);
    const seriesOcultas = ref([]);
    const resumoValores = computed(() => [
        { titulo: 'Total do contrato', valor: totais.value.r_total_contrato, cor: '#444d71' },
        { titulo: 'OSE', valor: totais.value.r_ose, cor: '#36A2EB' },
        { titulo: 'Medido', valor: totais.value.r_medido, cor: '#2d8407' },
    ]);
    const alternarSerie = (index) => {
        const chart = grafico.value?.chart;
        if (!chart) return;
        chart.toggleDataVisibility(index);
        chart.update();
        seriesOcultas.value = resumoValores.value.map((_, i) => i).filter(i => !chart.getDataVisibility(i));
    };

    const chartOptions = {
        responsive: true,
        maintainAspectRatio: false,
        rotation: 130,
        circumference: 360,
        plugins: {
            legend: { display: false },
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
</script>

<template>
    <Teleport to="body">
        <div v-show="cabecalhoFixo" class="cabecalho-congelado" :style="estiloCabecalho" aria-hidden="true">
            <table class="table tabela-quantitativos" :style="{ width: larguraCabecalho + 'px', transform: 'translateX(-' + deslocamentoCabecalho + 'px)' }">
                <colgroup><col v-for="(coluna, index) in colunasCabecalho" :key="index" :style="{ width: coluna.largura + 'px' }" /></colgroup>
                <thead><tr><th v-for="(coluna, index) in colunasCabecalho" :key="index">{{ coluna.titulo }}</th></tr></thead>
            </table>
        </div>
    </Teleport>
    <AuthenticatedLayout>
        <Head :title="`Quantitativos - Contrato ${contratoId}`" />

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

        <NavbarContrato :tipo="contrato" class="quantitativos-layout">
            <template #body>
                <div class="card quantitativos-conteudo">
                    <div class="card-body">
                        <div class="quantitativos-titulo">
                            <h3 class="subprodutos-title">QUANTITATIVOS - SUBPRODUTOS</h3>
                        </div>
                        <div v-if="quantitativosData?.error" class="alert alert-danger">
                            {{ quantitativosData.error }}
                        </div>
                        <div v-else-if="dataFiltrada.length > 0">
                            <section class="resumo-quantitativos" aria-label="Resumo financeiro dos subprodutos">
                                <div class="chart-container">
                                    <Doughnut ref="grafico" :data="chartData" :options="chartOptions" />
                                </div>
                                <div class="resumo-valores">
                                    <button v-for="(item, index) in resumoValores" :key="item.titulo" type="button" class="resumo-valor" :class="{ 'serie-oculta': seriesOcultas.includes(index) }" :aria-pressed="!seriesOcultas.includes(index)" @click="alternarSerie(index)">
                                        <span class="indicador-serie" :style="{ backgroundColor: item.cor }" aria-hidden="true"></span>
                                        <span class="resumo-texto"><span class="resumo-label">{{ item.titulo }}</span><strong>{{ formatarMoeda(item.valor) }}</strong></span>
                                    </button>
                                </div>
                            </section>

                            <div class="tabela-controles">
                                <div class="filtro-contagem">
                        <label for="filtro-familia" class="mb-0">Filtrar por Família:</label>
                        <div class="filter-container">
                            <select id="filtro-familia" v-model="filtroFamilia" class="form-select">
                                <option value="">Todas</option>
                                <option v-for="familia in familiasUnicas" :key="familia" :value="familia">
                                    {{ familia }}
                                </option>
                            </select>
                        </div>
                                <span>{{ dataFiltrada.length }} subprodutos</span>
                                </div>
                                <label class="itens-pagina" for="itens-pagina">Itens por página
                                    <select id="itens-pagina" v-model.number="porPagina" class="form-select">
                                        <option :value="25">25</option><option :value="50">50</option><option :value="100">100</option>
                                    </select>
                                </label>
                            </div>
                            <div v-show="temRolagem" ref="rolagemSuperior" class="rolagem-superior" tabindex="0" aria-label="Rolagem horizontal da tabela" @scroll="sincronizarRolagem(rolagemSuperior, tabelaContainer)">
                                <div :style="{ width: larguraTabela + 'px', height: '1px' }"></div>
                            </div>
                            <div ref="tabelaContainer" class="table-responsive tabela-container" tabindex="0" aria-label="Quantitativos dos subprodutos" @scroll="sincronizarRolagem(tabelaContainer, rolagemSuperior)">
                                <table ref="tabela" class="table table-striped tabela-quantitativos">
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
                                        <tr v-for="(item, index) in dadosPagina" :key="inicio + index">
                                            <td>{{ item.cod_siac }}</td>
                                            <td>{{ item.produto }}</td>
                                            <td>{{ item.subproduto }}</td>
                                            <td>{{ item.familia }}</td>
                                            <td class="descricao">{{ item.descricao_siac }}</td>
                                            <td class="descricao">{{ item.descricao_revisada }}</td>
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
                            <div class="tabela-paginacao">
                                <span>Exibindo {{ inicio + 1 }} a {{ Math.min(inicio + porPagina, dataFiltrada.length) }} de {{ dataFiltrada.length }}</span>
                                <nav v-if="totalPaginas > 1" class="paginas" aria-label="Páginas dos quantitativos">
                                    <button class="btn btn-sm btn-outline-secondary" :disabled="pagina === 1" @click="pagina--">Anterior</button>
                                    <span>Página {{ pagina }} de {{ totalPaginas }}</span>
                                    <button class="btn btn-sm btn-outline-secondary" :disabled="pagina === totalPaginas" @click="pagina++">Próxima</button>
                                </nav>
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
    .filtro-contagem { display: flex; align-items: center; flex-wrap: wrap; gap: 10px 16px; }
    .filtro-contagem .filter-container { margin: 0; }
    .cabecalho-congelado { position: fixed; top: 0; z-index: 100; overflow: hidden; background: #f1f5f9; box-shadow: 0 2px 5px #0f172a26; pointer-events: none; }
    .cabecalho-congelado table { table-layout: fixed; font-size: 0.9rem; }

    .tabela-controles, .tabela-paginacao { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; color: #475569; }
    .tabela-controles { margin: 24px 0 10px; padding: 18px 0 0; border-top: 1px solid #e2e8f0; }
    .itens-pagina, .paginas { display: flex; align-items: center; gap: 10px; margin: 0; }
    .itens-pagina .form-select { width: 80px; }
    .rolagem-superior { overflow-x: auto; height: 20px; }
    .tabela-container { border: 1px solid #cbd5e1; border-radius: 6px; }
    .tabela-quantitativos { margin: 0; }
    .tabela-quantitativos th { text-align: center; font-weight: 600; color: #334155; background: #f1f5f9; border: 1px solid #cbd5e1; padding: 10px 12px; white-space: nowrap; }
    .tabela-quantitativos td { padding: 10px 12px; vertical-align: top; white-space: nowrap; }
    .tabela-quantitativos td.descricao { min-width: 260px; max-width: 320px; white-space: normal; line-height: 1.5; }
    .tabela-paginacao { margin-top: 14px; }
    .rolagem-superior:focus-visible, .tabela-container:focus-visible { outline: 2px solid #3b82f6; outline-offset: 2px; }

    .card.quantitativos-conteudo { margin-top: 0; }
    .quantitativos-layout.card { background: transparent; border: 0; box-shadow: none; padding: 0; }
    .quantitativos-layout :deep(> .d-flex) { display: grid !important; grid-template-columns: 190px minmax(0, 1fr); gap: 20px; align-items: start; }
    .quantitativos-layout :deep(> .d-flex > .col-md-1) { width: auto; padding: 12px 8px; border: 1px solid #e2e8f0; border-radius: 8px; background: #fff; }
    .quantitativos-layout :deep(> .d-flex > .col-md-11) { width: auto; min-width: 0; }
    .quantitativos-layout :deep(.navbar-nav .nav-link) { padding: 12px 10px; border-radius: 6px; }
    .quantitativos-layout :deep(.navbar-nav .nav-item.active > .nav-link) { background: #eff6ff; color: #1d4ed8; }
    @media (max-width: 1199px) {
        .quantitativos-layout :deep(> .d-flex) { grid-template-columns: 170px minmax(0, 1fr); gap: 16px; }
    }
    @media (max-width: 767px) {
        .quantitativos-layout :deep(> .d-flex) { grid-template-columns: 1fr; }
        .quantitativos-layout :deep(.navbar-nav) { flex-direction: row; flex-wrap: wrap; gap: 4px; }
    }
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

    .quantitativos-conteudo > .card-body { padding: 24px; }
    .quantitativos-titulo { text-align: center; margin-bottom: 22px; }
    .resumo-quantitativos { display: grid; grid-template-columns: 240px minmax(0, 300px); align-items: center; justify-content: center; gap: 36px; max-width: 680px; margin: 0 auto; padding: 20px 28px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; }
    .chart-container { position: relative; width: 240px; height: 240px; }
    .resumo-valores { display: flex; flex-direction: column; gap: 4px; min-width: 0; }
    .resumo-valor { display: flex; align-items: center; gap: 12px; padding: 12px 8px; width: 100%; border: 0; border-bottom: 1px solid #e2e8f0; background: transparent; text-align: left; color: #0f172a; border-radius: 4px; }
    .resumo-valor:last-child { border-bottom: 0; }
    .resumo-valor:hover { background: #eff6ff; }
    .resumo-valor:focus-visible { outline: 2px solid #3b82f6; outline-offset: 2px; }
    .indicador-serie { width: 10px; height: 10px; border-radius: 3px; flex-shrink: 0; }
    .resumo-texto { display: flex; flex-direction: column; gap: 4px; }
    .resumo-label { color: #475569; font-size: .9rem; }
    .resumo-texto strong { font-size: 1.15rem; font-weight: 600; font-variant-numeric: tabular-nums; }
    .serie-oculta .resumo-texto { text-decoration: line-through; color: #64748b; }
    @media (max-width: 767px) {
        .quantitativos-conteudo > .card-body { padding: 16px; }
        .resumo-quantitativos { grid-template-columns: minmax(0, 1fr); gap: 16px; padding: 20px; max-width: 400px; }
        .chart-container { width: 220px; height: 220px; margin: 0 auto; }
    }

    .mt-4 {
        margin-top: 1.5rem;
    }

    .subprodutos-title {
        font-size: 1.7rem;
        margin: 0;
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
