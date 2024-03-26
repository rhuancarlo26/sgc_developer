<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import Navbar from "../Navbar.vue";
import ModalNovoServico from "./Modais/ModalNovoServico.vue";

defineProps({
    contrato: Object,
    numero_licencas: Array
})

</script>

<template>

    <Head :title="`${contrato.contratada.slice(0, 10)}...`" />

    <AuthenticatedLayout>

        <template #header>
            <div class="w-100 d-flex justify-content-between">
                <Breadcrumb 
                    class="align-self-center" 
                    :links="[
                        { route: route('contratos.gestao.listagem', contrato.tipo_id), label: `Gestão de Contratos` },
                        { route: '#', label: contrato.contratada }
                    ]
                "/>
            </div>
        </template>

        <Navbar :contrato="contrato">
            <template #body>
                <div class="w-100 d-flex justify-content-end mb-4">
                    <div class="container-buttons">
                        <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#modalNovoServico">
                            Novo Serviço
                        </button>
                    </div>
                </div>
                <!-- Listagem-->
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Tema</th>
                                <th>Serviço</th>
                                <th>Especificação</th>
                                <th>Licença</th>
                                <th>Status Aprovação</th>
                                <th>Ação</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    1
                                </td>
                                <td>
                                    Supervisão Ambiental
                                </td>
                                <td>
                                    Controle de Ocorrências
                                </td>
                                <td>
                                    teste
                                </td>
                                <td>
                                    LI - 1336/2020 - N/A
                                </td>
                                <td>
                                    <span class="badge text-bg-success">
                                        Vigente
                                    </span>
                                </td>
                                <td>
                                    ...
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    2
                                </td>
                                <td>
                                    Supervisão Ambiental
                                </td>
                                <td>
                                    Controle de Ocorrências
                                </td>
                                <td>
                                    teste
                                </td>
                                <td>
                                    LI - 1336/2020 - N/A
                                </td>
                                <td>
                                    <span class="badge text-bg-warning">
                                        Em Análise
                                    </span>
                                </td>
                                <td>
                                    ...
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    3
                                </td>
                                <td>
                                    Supervisão Ambiental
                                </td>
                                <td>
                                    Controle de Ocorrências
                                </td>
                                <td>
                                    teste
                                </td>
                                <td>
                                    LI - 1336/2020 - N/A
                                </td>
                                <td>
                                    <span class="badge text-bg-info">
                                        Não Vigente
                                    </span>
                                </td>
                                <td>
                                    ...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <ModalNovoServico />
            </template>
        </Navbar>

    </AuthenticatedLayout>
</template>
