<script setup>
import NavDropdownLink from '@/Components/NavDropdownLink.vue';
import NavDropdown from '@/Components/NavDropdown.vue';
import Navbar from '@/Pages/Contrato/Contratada/Navbar.vue';
import { IconLayoutDashboard } from '@tabler/icons-vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import NavLink from '@/Components/NavLink.vue';
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
                                            <NavDropdown prefix="contratos.contratada.servicos.pmqa.configuracao*"
                                                title="Configurações" :icon="IconLayoutDashboard">
                                                <NavDropdownLink
                                                    route-name="contratos.contratada.servicos.cont_ocorrencia.configuracao.empreendimento.index"
                                                    :route-param="{ contrato: contrato.id, servico: servico.id }"
                                                    title="Empreendimento" />
                                                <NavDropdownLink
                                                    route-name="contratos.contratada.servicos.cont_ocorrencia.configuracao.lote_obra.index"
                                                    :route-param="{ contrato: contrato.id, servico: servico.id }"
                                                    title="Lote de obra" />
                                            </NavDropdown>

                                            <template v-if="servico.cont_ocorr_parecer_configuracao?.fk_status === 3">
                                                <NavDropdown
                                                    prefix="contratos.contratada.servicos.cont_ocorrencia.execucao.ocorrencia*"
                                                    title="Execução" :icon="IconLayoutDashboard">
                                                    <NavDropdownLink
                                                        route-name="contratos.contratada.servicos.cont_ocorrencia.execucao.ocorrencia.index"
                                                        :route-param="{ contrato: contrato.id, servico: servico.id }"
                                                        title="Ocorrência" />
                                                    <NavDropdownLink
                                                        route-name="contratos.contratada.servicos.cont_ocorrencia.execucao.controle_rnc.index"
                                                        :route-param="{ contrato: contrato.id, servico: servico.id }"
                                                        title="Controle RNC" />
                                                    <NavDropdownLink
                                                        route-name="contratos.contratada.servicos.cont_ocorrencia.execucao.aca.index"
                                                        :route-param="{ contrato: contrato.id, servico: servico.id }"
                                                        title="ACA" />
                                                </NavDropdown>

                                                <NavLink
                                                    route-name="contratos.contratada.servicos.cont_ocorrencia.resultado.index"
                                                    active-on-route-prefix="contratos.contratada.servicos.cont_ocorrencia.resultado*"
                                                    :param="{ contrato: contrato.id, servico: servico.id }"
                                                    title="Resultado" :icon="IconLayoutDashboard" />

                                                <NavLink
                                                    route-name="contratos.contratada.servicos.cont_ocorrencia.relatorio.index"
                                                    active-on-route-prefix="contratos.contratada.servicos.cont_ocorrencia.relatorio*"
                                                    :param="{ contrato: contrato.id, servico: servico.id }"
                                                    title="Relatorio" :icon="IconLayoutDashboard" />

                                                <!-- Pareceres -->
                                                <NavLink
                                                    route-name="contratos.contratada.servicos.cont_ocorrencia.pareceres.index"
                                                    active-on-route-prefix="contratos.contratada.servicos.cont_ocorrencia.pareceres*"
                                                    :param="{ contrato: contrato.id, servico: servico.id }"
                                                    title="Pareceres" :icon="IconLayoutDashboard" />

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
