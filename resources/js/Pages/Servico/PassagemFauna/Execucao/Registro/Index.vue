<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import Navbar from "../../Navbar.vue";
import {Head, Link} from "@inertiajs/vue3";
import ModelSearchFormAllColumns from "@/Components/ModelSearchFormAllColumns.vue";
import Table from "@/Components/Table.vue";
import NavLink from "@/Components/NavLink.vue";

const props = defineProps({
    contrato: {type: Object},
    servico: {type: Object}
});

</script>
<template>

    <Head :title="`${contrato.contratada.slice(0, 10)}...`"/>

    <AuthenticatedLayout>

        <template #header>
            <div class="w-100 d-flex justify-content-between">
                <Breadcrumb class="align-self-center" :links="[
          { route: route('contratos.gestao.listagem', contrato.tipo_contrato), label: `Gestão de Contratos` },
          { route: '#', label: contrato.contratada }
        ]
          "/>
                <Link class="btn btn-dark"
                      :href="route('contratos.contratada.servicos.index', { contrato: props.contrato.id })">
                    Voltar
                </Link>
            </div>
        </template>

        <Navbar :contrato="contrato" :servico="servico">
            <template #body>
                <ModelSearchFormAllColumns :columns="[]">
                    <template #action>
                        <NavLink title="Novo registro" class="btn btn-success"
                                 route-name="contratos.contratada.servicos.passagem_fauna.execucao.registro.create"
                                 :param="{contrato: contrato.id, servico:servico.id}"/>
                    </template>
                </ModelSearchFormAllColumns>
                <Table
                    :columns="['Nome do registro', 'ID da campanha', 'Grupo amostrado', 'Espécie', 'Data registro', 'Data cadastro', 'Ação']"
                    :records="[]" table-class="table-hover">
                    <template #body="{ item }">
                        <tr>

                        </tr>
                    </template>
                </Table>
            </template>
        </Navbar>

    </AuthenticatedLayout>
</template>
