<script setup>
import { Link } from "@inertiajs/vue3";
import { onMounted, reactive } from "vue";
// import Timeline from "@/Components/Timeline.vue";
import Timelinex from "@/Components/Timelinex.vue";
import { Head } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import Navbar from "@/Pages/Sgc/Contratada/Navbar.vue";
import TableEmpreendimento from "./TableEmpreendimento/TableEmpreendimento.vue";
import TableFinanceiro from "./TableFinanceiro/TableFinanceiro.vue";
import TableAnalises from "./TableAnalises/TableAnalises.vue";
import TableSubprodutos from "./TableSubprodutos/TableSubprodutos.vue";
import { ref } from "vue";
import TableDadosGerais from "./TableDadosGerais/TableDadosGerais.vue";
import TableStatus from "./TableStatus/TableStatus.vue";
import TableForuns from "./TableForuns/TableForuns.vue";
import NavLink from "@/Components/NavLink.vue";
import { IconZoomCheck } from "@tabler/icons-vue";
import { computed } from 'vue';


// Definindo as props que o componente receberá
const props = defineProps({
  	contratos: Object,
    tipo: Object,
	empreendimentos: Object,
    posts: Object,
	estudos: Object,
	subprodutos: Object,
	empreendimentos2:Object,
	fases:Object,
	fm_eia_estudos_empreendimento:Object,
    fm_pba_estudos_empreendimento:Object,
    abio_emp_estudos_311:Object,
    asv_emp_estudos:Object,
    iphan_emp_estudos_521:Object,
    iphan_emp_estudos_531:Object,
});

console.log(props.contratos.data[0])

const datahoje = new Date();
function isSerialDate(data) {
  // Verifica se é um número e se está no intervalo possível de datas em série
  return Number.isInteger(data) && data >= 1 && data <= 2958465;
  // 2958465 é o número serial para "31/12/9999" no sistema baseado em 1900.
}
function dentroPrazo(dataBanco) {
  const hoje = new Date();
  const dataComparada = new Date(dataBanco);
  // Define horas, minutos, segundos e milissegundos para zero para comparar apenas as datas
  hoje.setHours(0, 0, 0, 0);
  dataComparada.setHours(0, 0, 0, 0);
  // Retorna 1 se a data é anterior à data de hoje, 0 se posterior
  return dataComparada < hoje ? 1 : 0;
}

function formatDate(ts) {
  const date = new Date(ts * 1000); // Multiplica por 1000 se for um timestamp Unix
  return date.toLocaleDateString('pt-BR'); // Formato brasileiro de data (dd/mm/aaaa)
}

function formatDateFromExcel(serial) {
    const date = new Date(serial);
    return date.toLocaleDateString('pt-BR');
    
}

const mapaTabDadosContratuais = ref();

let mostratable = false;
var mostralista = ref(false);

const empreendimento_detalhes = reactive({
	cod_emp: "a definir",
	situacao_processo_licenciamento_dnit: "a definir",
	situacao_ibama_oema: "a definir",
});

const exibeempreendimento = (emp) => {
	emp.forEach(element => {
		empreendimento_detalhes.cod_emp = element.cod_emp;
		empreendimento_detalhes.situacao_processo_licenciamento_dnit =
		element.situacao_processo_licenciamento_dnit;
		empreendimento_detalhes.situacao_ibama_oema =
		element.situacao_ibama_oema;
	});
}

const formatarMoeda = (valor) => {
	const valorFormatado = new Intl.NumberFormat("pt-BR", {
		style: "currency",
		currency: "BRL",
	}).format(valor);
	return valorFormatado;
};
const exibiremps = () => {
  mostralista.value = !mostralista.value;
};

const visualizarMapa = () => {
  mapaTabDadosContratuais.value.visualizarTrecho();
}

onMounted(() => {
    exibeempreendimento(props.empreendimentos);
});

// Campo de busca e lista filtrada
const searchTerm = ref('');
const filteredPosts = computed(() => {
  if (!props.posts || !Array.isArray(props.posts)) {
    return [];
  }
  return props.posts
    .filter(emp => emp.cod_emp.toLowerCase().includes(searchTerm.value.toLowerCase()))
    .sort((a, b) => a.cod_emp.localeCompare(b.cod_emp, 'pt', { numeric: true }));
});


// Função para gerar a rota e logar a URL
function getEmpreendimentoRoute(emp) {
  if (!emp || !props.tipo || !emp.id) return '#'; // Garante que sempre haverá uma URL válida
  return route('sgc.gestao.dashboard.empreendimento.index', { tipo: props.tipo.id, empreendimento: emp.id });
}


</script>
<template>
	<div>
		<Head :title="`${props.tipo.nome}...`" />
		<AuthenticatedLayout>
			<template #header>
				<div class="w-100 d-flex justify-content-between">
                    <Breadcrumb class="align-self-center" :links="[
                    { route: '#', label: `Gestão de Contratos - ${props.tipo.nome}` }
                ]"/>
				</div>
			</template>

			<Navbar :tipo="tipo">
				<template #body>
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start">
                        <!-- Filtro "Escolher Empreendimento" -->
                        <div class="filters-container">
                            <div class="p-3 mb-3 border rounded bg-light" style="width: 220px; cursor: pointer;" @click="exibiremps()">
                                <label for="select-empreendimentos" class="form-label text-center w-100" style="cursor: pointer;">
                                    Escolher Empreendimento
                                </label>
                            </div>
                            <div v-show="mostralista" class="dropdown-list" style="position: absolute; z-index: 10000; background-color: white;">
								<input type="text" v-model="searchTerm" placeholder="Buscar..." class="search-input" />
								<ul style="background: white; padding: 0; list-style: none; border-radius: 4px;">
									<li v-if="filteredPosts.length === 0" style="padding: 10px; text-align: center;">
									Nenhum empreendimento encontrado.
									</li>
									<li
									v-for="emp in filteredPosts"
									:key="emp.id"
									style="padding: 10px; text-align: center; transition: background-color 0.2s ease; cursor: pointer; display: flex; justify-content: center; align-items: center;"
									@mouseover="event => event.currentTarget.style.backgroundColor = '#f0f0f0'"
									@mouseout="event => event.currentTarget.style.backgroundColor = ''"
									>
									<Link
										:href="getEmpreendimentoRoute(emp)"
										class="empreendimento-link"
									>
										{{ emp.cod_emp }}
									</Link>
									</li>
								</ul>
							</div>

                        </div>

                    </div>
                    <Timelinex
                        :empreendimentos="empreendimentos"
                        :empreendimentos2="empreendimentos2"
                        :fm_eia_estudos_empreendimento="fm_eia_estudos_empreendimento"
                        :fm_pba_estudos_empreendimento="fm_pba_estudos_empreendimento"
                        :abio_emp_estudos_311="abio_emp_estudos_311"
                        :asv_emp_estudos="asv_emp_estudos"
                        :iphan_emp_estudos_521="iphan_emp_estudos_521"
                        :iphan_emp_estudos_531="iphan_emp_estudos_531"
                    />

                    <div class="row"  style=" margin-top: 1.5%;">
							<div class="col-md-12">
								<ul class="nav nav-tabs card-header-tabs nav-fill" data-bs-toggle="tabs" role="tablist">
									<li class="nav-item" role="presentation">
										<a @click="visualizarMapa()" href="#empreendimento" class="nav-link active d-flex justify-content-between"
											data-bs-toggle="tab" aria-selected="true" role="tab">
											<span>
												Empreendimento
											</span>
											<IconCircleCheck class="text-success" />
										</a>
									</li>
									<li class="nav-item" role="presentation">
										<a href="#1status" class="nav-link d-flex justify-content-between" data-bs-toggle="tab" aria-selected="false"
											tabindex="-1" role="tab">
											<span>
												Status do Licenciamento
											</span>
											<IconCircleCheck class="text-success"/>
											<IconCircleX class="text-danger"/>
										</a>
									</li>
									<li class="nav-item" role="presentation">
										<a href="#dadosgerais" class="nav-link d-flex justify-content-between" data-bs-toggle="tab"
											aria-selected="false" role="tab" tabindex="-1">
											<span>
												Dados Gerais
											</span>
											<IconCircleCheck class="text-success" />
											<IconCircleX class="text-danger" />
										</a>
									</li>
									<li class="nav-item" role="presentation">
										<a href="#financeiro" class="nav-link d-flex justify-content-between" data-bs-toggle="tab"
											aria-selected="false" tabindex="-1" role="tab">
											<span>
												Financeiro
											</span>
											<IconCircleX class="text-danger"/>
										</a>
									</li>
									<li class="nav-item" role="presentation">
										<a href="#subprodutos" class="nav-link d-flex justify-content-between" data-bs-toggle="tab"
											aria-selected="false" tabindex="-1" role="tab">
											<span>
												Subprodutos
											</span>
											<IconCircleCheck class="text-success"/>
											<IconCircleX class="text-danger"/>
										</a>
									</li>
									<li class="nav-item" role="presentation">
										<a href="#analises" class="nav-link d-flex justify-content-between" data-bs-toggle="tab" aria-selected="false"
										tabindex="-1" role="tab">
										<span>
											Analises/Revisões
										</span>
										<IconCircleCheck class="text-success"/>
										<IconCircleX class="text-danger"/>
										</a>
									</li>
									<li class="nav-item" role="presentation">
										<a href="#foruns" class="nav-link d-flex justify-content-between" data-bs-toggle="tab" aria-selected="false"
											tabindex="-1" role="tab">
											<span>
												Foruns
											</span>
											<IconCircleCheck class="text-success"/>
											<IconCircleX class="text-danger"/>
										</a>
									</li>
								</ul>
							</div>
							<div class="card-body" style="padding: 0; margin-top: 1.5%;">
								<div class="tab-content">
									<div class="tab-pane" id="1status" role="tabpanel">
									<TableStatus
										:contrato="contratos.data[0]"
										:empreendimentos="empreendimentos"
										:estudos="estudos"
										:empreendimentos2="empreendimentos2"
										:subprodutos/>
									</div>
									<div class="tab-pane active show" id="empreendimento" role="tabpanel">
									<TableEmpreendimento
										:contrato="contratos.data[0]"
										:empreendimentos="empreendimentos"
										:estudos="estudos"
										ref="mapaTabDadosContratuais" />
									</div>
									<div class="tab-pane" id="dadosgerais" role="tabpanel">
									<TableDadosGerais
										:contrato="contratos.data[0]"
										:empreendimentos="empreendimentos"
										:estudos="estudos"
										:empreendimentos2="empreendimentos2"
										:subprodutos/>
									</div>
									<div class="tab-pane" id="financeiro" role="tabpanel">
									<TableFinanceiro
										:contrato="contratos.data[0]"
										:empreendimentos="empreendimentos"
										:estudos="estudos"
										:subprodutos/>
									</div>
									<div class="tab-pane" id="analises" role="tabpanel">
									<TableAnalises
										:contrato="contratos.data[0]"
										:empreendimentos="empreendimentos"
										:estudos="estudos"
										:subprodutos/>
									</div>
									<div class="tab-pane" id="subprodutos" role="tabpanel">
									<TableSubprodutos
										:contrato="contratos.data[0]"
										:empreendimentos="empreendimentos"
										:estudos="estudos"
										:subprodutos/>
									</div>
									<div class="tab-pane" id="foruns" role="tabpanel">
									<TableForuns
										:contrato="contratos.data[0]"
										:empreendimentos="empreendimentos"
										:estudos="estudos"
										:empreendimentos2="empreendimentos2"
										:subprodutos/>
									</div>

								</div>
							</div>
						</div>
				</template>
			</Navbar>
		</AuthenticatedLayout>
	</div>
</template>

<style scoped>

/* Estilo principal de fundo das abas e das abas */
	.nav-tabs .nav-link {
		background-color: #fffefe; /* Cor de fundo leve para as abas inativas */
		border-radius: 5px 5px 0 0; /* Bordas arredondadas nas abas superiores */
		color: #555; /* Cor do texto */
		box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1); /* Sombra para dar destaque */
	}

	.nav-tabs .nav-link.active {
		background-color: #f2f2f2; /* Cor de fundo da aba ativa */
		border-color: #f2f2f2 #f2f2f2 transparent; /* Conectar visualmente a aba ao conteúdo */
		color: #000; /* Cor do texto da aba ativa */
	}

	.tab-content {
		background-color: #fcfcfc; /* Cor de fundo leve para o conteúdo das abas */
		border-radius: 0 0 5px 5px; /* Bordas arredondadas no conteúdo das abas */
		border-top: none; /* Remover a borda superior para mesclar com a aba ativa */
		border: 2px solid #e4e2e2; /* Borda para o conteúdo da aba */
		padding: 0 10px;
		box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1); /* Sombra para dar destaque */
	}
/*  */


.d-flex {
	display: flex;
	align-items: center;
	justify-content: space-between;
}

.text-center {
	text-align: center;
	margin-left: auto;
	margin-right: auto;
}

h1 {
	margin: 0;
}

/* Estilização da lista como dropdown */
.dropdown-list {
	position: absolute;
	background-color: white;
	border: 1px solid #ddd;
	padding: 10px;
	box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
	border-radius: 5px;
	max-height: 200px; /* Limite de altura para a lista */
	overflow-y: auto; /* Habilita rolagem se necessário */
	z-index: 1000;
}

.dropdown-list ul {
	list-style-type: none;
	padding-left: 0;
}

.dropdown-list ul li {
	margin-bottom: 10px;
}

.btn-group {
	display: flex;
	justify-content: flex-start;
}

.card ul {
	list-style-type: none;
	padding-left: 0;
}

.card ul li {
	margin-bottom: 10px;
}

/* Estilo para a organização dos filtros e etapas na lateral esquerda */
.etapas-container {
    display: flex;
    flex-direction: column;
    align-items: center;
}

.etapa-btn {
    padding: 10px 20px;
    background-color: #ffffff;
    border: 1px solid #ccc;
    border-radius: 5px;
    font-weight: bold;
    cursor: pointer;
    transition: background-color 0.3s ease;
    width: 100%;
    max-width: 200px; /* Ajusta a largura dos botões */
}

.etapa-btn:hover {
    background-color: #e2e6ea;
}

.etapa-btn:focus {
    outline: none;
    background-color: #dae0e5;
}

.text-center {
    text-align: center;
}

/* Estilo para o campo de busca */
.search-input {
  width: 100%;
  padding: 8px;
  margin-bottom: 10px;
  box-sizing: border-box;
}

.dropdown-list {
  width: 350px;
  max-height: 600px;
  font-size: 16px;
  padding: 15px;
}


/* Estilo para o campo de busca */
.search-input {
  width: 100%;
  padding: 8px;
  margin-bottom: 10px;
  box-sizing: border-box;
}


.dropdown-list ul {
  list-style-type: none;
  padding-left: 0s;
}

.dropdown-list ul li {
  display: flex;
  align-items: center;
}

.icon {
  margin-right: 12px;
}


</style>