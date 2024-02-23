<template>
    <Head title="Gestão de Licenças" />

    <AuthenticatedLayout>

        <template #header>
            <div class="w-100 d-flex justify-content-between">
                <Breadcrumb class="align-self-center" :links="[{ route: '#', label: 'Gestão de Licenças' }]" />
                <div class="container-buttons">
                    <Link class="btn btn-success me-2" :href="route('licenca.create')">
                    Cadastrar Licenças
                    </Link>
                    <button @click="arquivado = !arquivado" class="btn me-2"
                        :class="arquivado ? 'btn-success' : 'btn-outline-success'">
                        Licenças Arquivados
                    </button>
                    <button @click="abrirMapaGeral()" type="button" class="btn btn-success">
                        Mapa das Licenças
                    </button>
                </div>
            </div>
        </template>

        <div class="card card-body space-y-3">

            <!-- Pesquisa-->
            <ModelSearchForm :search-columns="{
                'numero_contrato': 'N° do Contrato',
                'cnpj': 'CNPJ',
                'contratada': 'Contratada',
                'processo_sei': 'Processo SEI',
                'situacao': 'Situação'
            }" />

            <!-- Listagem-->
            <div class="table-responsive">
                <table class="table table-hover non-hover">
                    <thead>
                        <tr>
                            <th>Modal</th>
                            <th>Tipo</th>
                            <th>Nº Licença</th>
                            <th>Empreendimento</th>
                            <th>Emissor</th>
                            <th>Data da Emissão</th>
                            <th>Status</th>
                            <th>Vencimento</th>
                            <th>Processo DNIT</th>
                            <th>Açao</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="licenca, index in licencas.data" :key="licenca.id">
                            <td>
                                <IconCar v-if="licenca.modal == 1" />
                                <IconShip v-if="licenca.modal == 2" />
                                <IconTrain v-if="licenca.modal == 3" />
                            </td>
                            <td>
                                {{ licenca.tipo.sigla }} - {{ licenca.tipo.nome }}
                            </td>
                            <td>
                                {{ licenca.numero_licenca }}
                            </td>
                            <td>
                                {{ licenca.empreendimento }}
                            </td>
                            <td>
                                {{ licenca.emissor }}
                            </td>
                            <td>
                                {{ dateTimeFormat(licenca.data_emissao) }}
                            </td>
                            <td>
                                <a href="javascript:void(0)">
                                    <span class="badge text-bg-success">
                                        Vigente
                                    </span>
                                    <span class="badge text-bg-warning">
                                        Em Análise
                                    </span>
                                    <br>
                                    <span class="badge text-bg-danger">
                                        Vencida
                                    </span>
                                    <span class="badge text-bg-info">
                                        Não Vigente
                                    </span>
                                </a>
                            </td>
                            <td>
                                {{ dateTimeFormat(licenca.vencimento) }}
                            </td>
                            <td>
                                {{ licenca.processo_dnit }}
                            </td>
                            <td @click.stop>
                                <span class="dropdown">
                                    <button class="btn dropdown-toggle align-text-top" data-bs-boundary="viewport"
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                        <IconDots />
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end" style="">
                                        <a class="dropdown-item" :href="route('licenca.create', licenca.id)">
                                            Editar
                                        </a>
                                        <a @click="abrirModalVisualizar(licenca)" class="dropdown-item"
                                            href="javascript:void(0)">
                                            Visualizar
                                        </a>
                                        <a v-if="licenca.documento?.id" class="dropdown-item" target="_blank"
                                            :href="route('licenca.documento.visualizar', licenca.documento.id)">
                                            Visualizar PDF
                                        </a>
                                        <a class="dropdown-item" :href="route('licenca.condicionante.index', licenca.id)">
                                            Condicionante
                                        </a>
                                        <a @click="abrirModalRequerimento(licenca)" class="dropdown-item"
                                            href="javascript:void(0)">
                                            Adicionar requerimento
                                        </a>
                                        <a v-if="!licenca.arquivado" @click="gerenciarArquivo(licenca, index, true)"
                                            class="dropdown-item" href="javascript:void(0)">
                                            Arquivar
                                        </a>
                                        <a v-else @click="gerenciarArquivo(licenca, index, false)" class="dropdown-item"
                                            href="javascript:void(0)">
                                            Desarquivar
                                        </a>
                                    </div>
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="p-1 d-flex w-100 align-items-end justify-content-center">
                <div v-if="licencas.data?.length">
                    <p class="text-center mt-3">
                        Mostrando {{ licencas.from ?? 0 }} até {{ licencas.to ?? 0 }} de {{
                            licencas.total ?? 0 }}
                        registros.
                    </p>
                    <ul class="pagination mb-0 mt-2">
                        <li v-for="(link, i) in licencas.links" :key="i" class="page-item" :class="getLinkCSSClass(link)">
                            <button class="page-link" :class="getLinkCSSClass(link)" @click="getLicenca(link.url)"
                                v-html="link.label">
                            </button>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <ModalRequerimento ref="refModalRequerimento" />
        <ModalVisualizar ref="refModalVisualizar" />
        <ModalMapaGeral ref="refModalMapaGeral" />
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import Table from '@/Components/Table.vue';
import Breadcrumb from "@/Components/Breadcrumb.vue";
import ModelSearchForm from "@/Components/ModelSearchForm.vue";
import { IconDots, IconShip, IconCar, IconTrain } from "@tabler/icons-vue";
import { dateTimeFormat } from "@/Utils/DateTimeUtils";
import ModalRequerimento from "./Requerimento/ModalRequerimento.vue";
import ModalVisualizar from "./ModalVisualizar.vue";
import ModalMapaGeral from "./ModalMapaGeral.vue";
import { ref } from "vue";
import { onMounted } from "vue";
import axios from "axios";
import { watch } from "vue";

const refModalRequerimento = ref(null);
const refModalVisualizar = ref(null);
const refModalMapaGeral = ref(null);
let licencas = ref({});
let arquivado = ref(false);

onMounted(() => {
    getLicenca();
});

watch(() => arquivado.value, () => {
    getLicenca();
})

// FUNCTIONS
const getLicenca = (link = null) => {
    axios.post(link ?? route('licenca.get_licenca'), { arquivado: arquivado.value }).then(r => {
        licencas.value = r.data.licencas;
    });
}

const gerenciarArquivo = (licenca, index, status) => {
    router.patch(route('licenca.gerenciar_licenca', licenca), { id: licenca.id, arquivado: status }, {
        preserveScroll: true,
        onSuccess: () => {
            licencas.value.data.splice(index, 1)
        }
    })
}

const abrirModalRequerimento = (item) => {
    refModalRequerimento.value.abrirModal(item);
}

const abrirModalVisualizar = (item) => {
    refModalVisualizar.value.abrirModal(item);
}

const abrirMapaGeral = () => {
    refModalMapaGeral.value.abrirModal();
}

const editarLicenca = (id) => {
    axios.patch(route('licenca.update', { licenca: id }))
        .then(response => {
            // Lógica após a edição
            console.log("response: ", response)
        })
        .catch(error => {
            console.error("Error editing license:", error);
        });
}

const getLinkCSSClass = (link) => {
    let css = '';

    if (link.active) {
        css = ' active ';
    }

    if (isNaN(link.label) && link.url === null) {
        css += ' disabled ';
    }

    return css;
}
</script>

<style scoped></style>
