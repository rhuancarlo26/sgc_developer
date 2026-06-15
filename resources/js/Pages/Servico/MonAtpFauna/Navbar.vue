<script setup>
import NavDropdownLink from '@/Components/NavDropdownLink.vue';
import NavDropdown from '@/Components/NavDropdown.vue';
import Navbar from '@/Pages/Contrato/Contratada/Navbar.vue';
import NavLink from '@/Components/NavLink.vue';
import { IconLayoutDashboard } from '@tabler/icons-vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import CardDadosImportadosServico from "@/Pages/Modulos/Importador/Components/CardDadosImportadosServico.vue";
import { ref } from "vue";

const porps = defineProps({
	contrato: { type: Object },
	servico: { type: Object }
});

const abaAtiva = ref('servico');

const abrirDadosImportados = () => {
	abaAtiva.value = 'dados_importados';
}
</script>
<template>
	<Navbar :contrato="contrato">
		<template #body>
			<div class="mb-4">
				<Breadcrumb
					:links="[{ route: route('contratos.contratada.servicos.index', { contrato: contrato.id, servico: servico.id }), label: 'Serviços' }, { route: '#', label: servico?.tipo?.nome }]" />
			</div>
			<div class="card card-body p-0 space-y-3">
				<header class="navbar-expand-md">
					<div class="collapse navbar-collapse" id="navbar-menu">
						<div class="navbar">
							<div class="container-xl">
								<div class="row flex-fill align-items-center">
									<div class="col">
										<ul class="navbar-nav" :class="{ 'dados-importados-ativo': abaAtiva === 'dados_importados' }">
											<!-- Configurações -->
											<NavDropdown prefix="contratos.contratada.servicos.mon_atp_fauna.configuracoes*"
												title="Configurações" :icon="IconLayoutDashboard">
												<!-- Vincular ABIO -->
												<NavDropdownLink
													route-name="contratos.contratada.servicos.mon_atp_fauna.configuracoes.vincular_abio.index"
													active-on-route-prefix="contratos.contratada.servicos.mon_atp_fauna.configuracoes.vincular_abio*"
													:route-param="{ contrato: contrato.id, servico: servico.id }" title="Vincular ABIO" />
												<!-- Malha Viaria -->
												<NavDropdownLink
													route-name="contratos.contratada.servicos.mon_atp_fauna.configuracoes.malha_viaria.index"
													active-on-route-prefix="contratos.contratada.servicos.mon_atp_fauna.configuracoes.malha_viaria*"
													:route-param="{ contrato: contrato.id, servico: servico.id }" title="Malha Viaria" />
											</NavDropdown>
											<template v-if="servico.parecer_atropelamento?.fk_status === 3">
												<!-- Execução -->
												<NavDropdown prefix="contratos.contratada.servicos.mon_atp_fauna.execucao*" title="Execução"
													:icon="IconLayoutDashboard">
													<!-- Campanhas -->
													<NavDropdownLink
														route-name="contratos.contratada.servicos.mon_atp_fauna.execucao.campanhas.index"
														active-on-route-prefix="contratos.contratada.servicos.mon_atp_fauna.execucao.campanhas*"
														:route-param="{ contrato: contrato.id, servico: servico.id }" title="Campanhas" />
													<!-- Registros -->
													<NavDropdownLink
														route-name="contratos.contratada.servicos.mon_atp_fauna.execucao.registros.index"
														active-on-route-prefix="contratos.contratada.servicos.mon_atp_fauna.execucao.registros*"
														:route-param="{ contrato: contrato.id, servico: servico.id }" title="Registros" />
												</NavDropdown>

												<!-- Resultado -->
												<NavLink route-name="contratos.contratada.servicos.mon_atp_fauna.resultado.index"
													active-on-route-prefix="contratos.contratada.servicos.mon_atp_fauna.resultado*"
													:param="{ contrato: contrato.id, servico: servico.id }" title="Resultado"
													:icon="IconLayoutDashboard" />

												<!-- Relatorios -->
												<NavLink route-name="contratos.contratada.servicos.mon_atp_fauna.relatorios.index"
													active-on-route-prefix="contratos.contratada.servicos.mon_atp_fauna.relatorios*"
													:param="{ contrato: contrato.id, servico: servico.id }" title="Relatorios"
													:icon="IconLayoutDashboard" />

												<!-- Pareceres -->
												<NavLink route-name="contratos.contratada.servicos.mon_atp_fauna.pareceres.index"
													active-on-route-prefix="contratos.contratada.servicos.mon_atp_fauna.pareceres*"
													:param="{ contrato: contrato.id, servico: servico.id }" title="Pareceres"
													:icon="IconLayoutDashboard" />
											</template>
											<li class="nav-item pastel-2 aba-dados-importados" :class="{ active: abaAtiva === 'dados_importados' }">
												<button type="button" class="nav-link w-100"
													:class="{ active: abaAtiva === 'dados_importados' }"
													:aria-current="abaAtiva === 'dados_importados' ? 'page' : null"
													@click="abrirDadosImportados">
													<IconLayoutDashboard class="me-1" />
													<span class="nav-link-title">Dados importados</span>
												</button>
											</li>
										</ul>
									</div>
								</div>
							</div>
						</div>
					</div>
				</header>
				<div class="mt-2 card card-body">
					<CardDadosImportadosServico v-if="abaAtiva === 'dados_importados'" :servico="servico" />
					<slot v-else name="body" />
				</div>
			</div>
		</template>
	</Navbar>
</template>

<style scoped>
.dados-importados-ativo :deep(.nav-item.active:not(.aba-dados-importados) > .nav-link) {
    background: transparent;
    color: inherit;
}
</style>
