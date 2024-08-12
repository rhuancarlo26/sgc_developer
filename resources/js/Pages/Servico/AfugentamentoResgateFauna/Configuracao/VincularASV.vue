<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import Navbar from "../Navbar.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import ModelSearchFormAllColumns from "@/Components/ModelSearchFormAllColumns.vue";
import Table from "@/Components/Table.vue";
import NavButton from "@/Components/NavButton.vue";
import { ref } from "vue";
import { IconDots } from "@tabler/icons-vue";
import NavLink from "@/Components/NavLink.vue";
import LinkConfirmation from "@/Components/LinkConfirmation.vue";
import DetalheVincularASVModal from "./Modal/DetalheVincularASVModal.vue";
import AdicionarShapeFileModal from "./Modal/AdicionarShapeFileModal.vue";
import { dateTimeFormat } from "@/Utils/DateTimeUtils";
import Swal from 'sweetalert2';

const props = defineProps({
    contrato: { type: Object },
    servico: { type: Object },
    licencaASV: { type: Array },
    licencaVinculadas: { type: Array }
});

const modalDetalheVincularASV = ref();
const modalAdicionarShapeFile = ref();

const abrirModalDetalheVincularASV = (licenca, servico) => {
    console.log(servico);
    modalDetalheVincularASV.value.abrirModal(licenca, servico);
}

const abrirModalAdicionarShapeFile = (licenca) => {
    console.log(licenca);
    modalAdicionarShapeFile.value.abrirModal(licenca);
}

const destroy = (item) => {
    Swal.fire({
        title: "Excluir Vinculação",
        text: "Ao excluir a vinculação, a ASV será desvinculada do serviço e licença. Deseja continuar?",
        icon: "warning",
        showCloseButton: true,
        showCancelButton: true,
        focusConfirm: false,
    }).then((r) => {
        if (r.isConfirmed) {
            router.delete(route('contratos.contratada.servicos.afugentamento.resgate.fauna.configuracao.vincular.asv.delete', { licenca: item.id }));
        }
    })
}

</script>

<template>

    <Head :title="`${contrato.contratada.slice(0, 10)}...`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="w-100 d-flex justify-content-between">
                <Breadcrumb class="align-self-center" :links="[
                    { route: route('contratos.gestao.listagem', contrato.tipo_id), label: `Gestão de Contratos` },
                    { route: '#', label: contrato.contratada }
                ]
                    " />
                <Link class="btn btn-dark"
                    :href="route('contratos.contratada.servicos.index', { contrato: props.contrato.id })">
                Voltar
                </Link>
            </div>
        </template>

        <Navbar :contrato="contrato" :servico="servico">
            <template #body>

                <div class="d-flex">
                    <div class="col-md-6">
                        <!-- Pesquisa-->
                        <ModelSearchFormAllColumns :columns="[]">
                        </ModelSearchFormAllColumns>
                    </div>

                    <div class="col-md-6">
                        <!-- <InputLabel value="Número da ASV" for="asv" /> -->
                        <v-select :options="licencaASV" label="numero_licenca" placeholder="Número da ASV">
                            <template v-slot:option="option">
                                <a href="javascript:void" @click="abrirModalDetalheVincularASV(option, servico)">
                                    {{ option.tipo.sigla }} - {{ option.numero_licenca }}
                                </a>
                            </template>
                            <template #no-options="{}">
                                Nenhum registro encontrado.
                            </template>
                        </v-select>
                        <!-- <InputError :message="form_trecho.errors.asv_id" /> -->
                    </div>
                </div>

                <!-- Tabela -->
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Tipo</th>
                            <th>Nº Licença</th>
                            <th>Empreendimento</th>
                            <th>Emissor</th>
                            <th>Data da Emissão</th>
                            <th>Vencimento</th>
                            <th>ShapeFile</th>
                            <th>Responsável</th>
                            <th>Processo DNIT</th>
                            <th>Ação</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="item in licencaVinculadas" :key="item.id">
                            <td>{{ item.licenca?.tipo?.sigla }}</td>
                            <td>{{ item.licenca?.numero_licenca }}</td>
                            <td>{{ item.licenca?.empreendimento }}</td>
                            <td>{{ item.licenca?.emissor }}</td>
                            <td>{{ dateTimeFormat(item.licenca?.data_emissao) }}</td>
                            <td>{{ dateTimeFormat(item.licenca?.vencimento) }}</td>
                            <td></td>
                            <td>{{ item.licenca?.user?.name }}</td>
                            <td>{{ item.licenca?.processo_dnit }}</td>
                            <td>
                                <span class="dropdown">
                                    <button class="btn dropdown-toggle align-text-top" data-bs-boundary="viewport"
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                        <IconDots />
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                        <li><a class="dropdown-item" href="javascript:void(0);">Visualizar PDF</a></li>
                                        <li><a class="dropdown-item" @click="abrirModalAdicionarShapeFile(item)"
                                                href="javascript:void(0);">Adicionar ShapeFile</a>
                                        </li>
                                        <li><a class="dropdown-item" @click="destroy(item)"
                                                href="javascript:void(0);">Excluir</a>
                                        </li>
                                    </ul>
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </template>
        </Navbar>

        <DetalheVincularASVModal ref="modalDetalheVincularASV" />
        <AdicionarShapeFileModal ref="modalAdicionarShapeFile" />

    </AuthenticatedLayout>
</template>
