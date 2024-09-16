<script setup>
import Navbar from '@/Pages/Contrato/Contratada/Navbar.vue';
import NavDropdown from '@/Components/NavDropdown.vue';
import NavDropdownLink from '@/Components/NavDropdownLink.vue';
import {IconLayoutDashboard} from '@tabler/icons-vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import NavLink from '@/Components/NavLink.vue';
import axios from "axios";
import {onMounted, ref} from "vue";

const props = defineProps({
    contrato: {type: Object},
    servico: {type: Object}
});

const aprovacao = ref({});

onMounted(() => {
    getAprovacao();
})

const getAprovacao = () => {
    axios.get(route('aprovacao-config-afugentamento.get', {servico: props.servico.id}))
        .then(response => {
            aprovacao.value = response.data.aprovacao
        })
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
                                        <ul class="navbar-nav">
                                            <NavDropdown
                                                prefix="contratos.contratada.servicos.afugentamento.resgate.fauna.configuracao*"
                                                title="Configurações" :icon="IconLayoutDashboard">

                                                <NavDropdownLink
                                                    route-name="contratos.contratada.servicos.afugentamento.resgate.fauna.configuracao.vincular.asv.index"
                                                    active-on-route-prefix="contratos.contratada.servicos.afugentamento.resgate.fauna.configuracao.vincular.asv*"
                                                    :route-param="{ contrato: contrato.id, servico: servico.id }"
                                                    title="Vincular ASV"/>

                                                <NavDropdownLink
                                                    route-name="contratos.contratada.servicos.afugentamento.resgate.fauna.configuracao.vincular.abio.index"
                                                    active-on-route-prefix="contratos.contratada.servicos.afugentamento.resgate.fauna.configuracao.vincular.abio*"
                                                    :route-param="{ contrato: contrato.id, servico: servico.id }"
                                                    title="Vincular Abio"/>

                                            </NavDropdown>

                                            <NavDropdown v-if="aprovacao.fk_status === 3"
                                                prefix="contratos.contratada.servicos.afugentamento.resgate.fauna.execucao*"
                                                title="Execução" :icon="IconLayoutDashboard">

                                                <NavDropdownLink
                                                    route-name="contratos.contratada.servicos.afugentamento.resgate.fauna.execucao.frente.supressao.index"
                                                    active-on-route-prefix="contratos.contratada.servicos.afugentamento.resgate.fauna.execucao.frente.supressao*"
                                                    :route-param="{ contrato: contrato.id, servico: servico.id }"
                                                    title="Frente de Supressão"/>

                                                <NavDropdownLink
                                                    route-name="contratos.contratada.servicos.afugentamento.resgate.fauna.execucao.registros.index"
                                                    active-on-route-prefix="contratos.contratada.servicos.afugentamento.resgate.fauna.execucao.registros*"
                                                    :route-param="{ contrato: contrato.id, servico: servico.id }"
                                                    title="Registros"/>

                                            </NavDropdown>

                                            <NavLink route-name="contratos.contratada.servicos.afugentamento.resgate.fauna.resultado.index"
                                                active-on-route-prefix="contratos.contratada.servicos.afugentamento.resgate.fauna.resultado.index*"
                                                :param="{ contrato: contrato.id, servico: servico.id }" title="Resultado"
                                                :icon="IconLayoutDashboard" />

                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </header>
                <div class="mt-2 card card-body">
                    <slot name="body"/>
                </div>
            </div>
        </template>
    </Navbar>
</template>
