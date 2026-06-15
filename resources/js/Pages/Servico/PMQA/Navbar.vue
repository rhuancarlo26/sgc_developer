<script setup>
import NavDropdownLink from '@/Components/NavDropdownLink.vue';
import NavDropdown from '@/Components/NavDropdown.vue';
import Navbar from '@/Pages/Contrato/Contratada/Navbar.vue';
import {IconLayoutDashboard} from '@tabler/icons-vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import NavLink from '@/Components/NavLink.vue';
import {onMounted, ref} from "vue";
import axios from "axios";
import CardDadosImportadosServico from "@/Pages/Modulos/Importador/Components/CardDadosImportadosServico.vue";

const props = defineProps({
    contrato: {type: Object},
    servico: {type: Object}
});

const aprovacao = ref({});
const abaAtiva = ref('servico');

onMounted(() => {
    getAprovacao();
})

const getAprovacao = () => {
    axios.get(route('aprovacao-config-pmqa.get', {servico: props.servico.id}))
        .then(response => {
            aprovacao.value = response.data.aprovacao
        })
}

const abrirDadosImportados = () => {
    abaAtiva.value = 'dados_importados';
}
</script>
<template>
    <Navbar :contrato="contrato">
        <template #body>
            <div class="mb-4">
                <Breadcrumb
                    :links="[{ route: route('contratos.contratada.servicos.index', { contrato: contrato.id, servico: servico.id }), label: 'Serviços' }, { route: '#', label: servico?.tipo?.nome }]"/>
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
                                                         title="Configurações"
                                                         :icon="IconLayoutDashboard">

                                                <NavDropdownLink
                                                    route-name="contratos.contratada.servicos.pmqa.configuracao.ponto.index"
                                                    active-on-route-prefix="contratos.contratada.servicos.pmqa.configuracao.ponto*"
                                                    :route-param="{ contrato: contrato.id, servico: servico.id }"
                                                    title="Pontos"/>

                                                <NavDropdownLink
                                                    route-name="contratos.contratada.servicos.pmqa.configuracao.parametro.index"
                                                    active-on-route-prefix="contratos.contratada.servicos.pmqa.configuracao.parametro*"
                                                    :route-param="{ contrato: contrato.id, servico: servico.id }"
                                                    title="Parâmetros"/>
                                                <NavDropdownLink
                                                    route-name="contratos.contratada.servicos.pmqa.configuracao.vinculacao_ponto.index"
                                                    active-on-route-prefix="contratos.contratada.servicos.pmqa.configuracao.vinculacao_ponto*"
                                                    :route-param="{ contrato: contrato.id, servico: servico.id }"
                                                    title="Vinculação de pontos"/>
                                            </NavDropdown>

                                            <NavLink route-name="contratos.contratada.servicos.pmqa.execucao.index"
                                                     active-on-route-prefix="contratos.contratada.servicos.pmqa.execucao*"
                                                     :param="{ contrato: contrato.id, servico: servico.id }"
                                                     title="Execução" v-if="aprovacao?.fk_status === 3"
                                                     :icon="IconLayoutDashboard"/>

                                            <NavLink route-name="contratos.contratada.servicos.pmqa.resultado.index"
                                                     active-on-route-prefix="contratos.contratada.servicos.pmqa.resultado*"
                                                     :param="{ contrato: contrato.id, servico: servico.id }"
                                                     title="Resultado" v-if="aprovacao?.fk_status === 3"
                                                     :icon="IconLayoutDashboard"/>
                                            <NavLink route-name="contratos.contratada.servicos.pmqa.relatorio.index"
                                                     active-on-route-prefix="contratos.contratada.servicos.pmqa.relatorio*"
                                                     :param="{ contrato: contrato.id, servico: servico.id }"
                                                     title="Relatório" v-if="aprovacao?.fk_status === 3"
                                                     :icon="IconLayoutDashboard"/>
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
                    <slot v-else name="body"/>
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
