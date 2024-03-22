<script setup>
import Map from '@/Components/Map.vue';
import { dateTimeFormat } from '@/Utils/DateTimeUtils';
import { Head, Link, router } from "@inertiajs/vue3";
import { computed } from 'vue';
import { onMounted, ref } from 'vue';
import ModalNovoServico from "../Modais/ModalNovoServico.vue";

</script>

<template>
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