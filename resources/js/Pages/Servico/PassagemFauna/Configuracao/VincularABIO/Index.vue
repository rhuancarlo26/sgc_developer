<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import Navbar from "../../Navbar.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import ModelSearchFormAllColumns from "@/Components/ModelSearchFormAllColumns.vue";
import Table from "@/Components/Table.vue";
import { dateTimeFormat } from "../../../../../Utils/DateTimeUtils.js";
import { IconDots, IconPlus } from "@tabler/icons-vue";
import NavButton from "@/Components/NavButton.vue";
import ModalVincularABIO from "./ModalVincularABIO.vue";
import { ref } from "vue";
import ModalAdicionarRET from "./ModalAdicionarRET.vue";

const modalVincularABIO = ref({});
const modalAdicionarRET = ref({});
const props = defineProps({
    contrato: { type: Object },
    servico: { type: Object },
    abios: { type: Object }
});

const abrirModalVincularABIO = () => {
    modalVincularABIO.value.abrirModal();
}

const abrirModalAdicionarRET = (item) => {
    modalAdicionarRET.value.abrirModal(item);
}

const excluirABIO = (abio_id) => {
    router.delete(route('contratos.contratada.servicos.passagem_fauna.configuracao.vincular_abio.delete_abio', {
        contrato: props.contrato.id,
        servico: props.servico.id,
        abio: abio_id
    }));
}

</script>
<template>

    <Head :title="`${contrato.contratada.slice(0, 10)}...`" />

    <AuthenticatedLayout>

        <template #header>
            <div class="w-100 d-flex justify-content-between">
                <Breadcrumb class="align-self-center" :links="[
                    { route: route('contratos.gestao.listagem', contrato.tipo_contrato), label: `Gestão de Contratos` },
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
                <ModelSearchFormAllColumns :columns="[]">
                    <template #action
                        v-if="!servico.parecer_passagem_fauna || ![1, 3].includes(servico.parecer_passagem_fauna.fk_status)">
                        <NavButton @click="abrirModalVincularABIO()" :icon="IconPlus" class="nav-item"
                            type-button="success" title="Vincular ABIO" />
                    </template>
                </ModelSearchFormAllColumns>
                <Table
                    :columns="['Tipo', 'N° licença', 'Empreendimento', 'Emissor', 'Data da emissão', 'Vencimento', 'Responsável', 'Processo DNIT', 'Ação']"
                    :records="abios" table-class="table-hover">
                    <template #body="{ item }">
                        <tr>
                            <td>{{ item.licenca?.tipo_rel.sigla }}</td>
                            <td>{{ item.licenca?.numero_licenca }}</td>
                            <td>{{ item.licenca?.empreendimento }}</td>
                            <td>{{ item.licenca?.emissor }}</td>
                            <td>{{ dateTimeFormat(item.licenca?.data_emissao) }}</td>
                            <td>{{ dateTimeFormat(item.licenca?.vencimento) }}</td>
                            <td>{{ item.licenca?.fiscal }}</td>
                            <td>{{ item.licenca?.processo_dnit }}</td>
                            <td class="text-center">
                                <button type="button" class="btn btn-icon btn-info dropdown-toggle p-2"
                                    data-bs-boundary="viewport" data-bs-toggle="dropdown" aria-expanded="false">
                                    <IconDots />
                                </button>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <span
                                        v-if="!servico.parecer_passagem_fauna || ![1, 3].includes(servico.parecer_passagem_fauna.fk_status)">
                                        <a @click="abrirModalAdicionarRET(item)" class="dropdown-item"
                                            href="javascript:void(0)">Adicionar RET</a>
                                        <a v-if="item.abio_ret" class="dropdown-item" target="_blank"
                                            :href="route('contratos.contratada.servicos.passagem_fauna.configuracao.vincular_abio.visualizar_ret', { contrato: contrato.id, servico: servico.id, abio_ret: item.abio_ret?.id })">Visualizar
                                            RET</a>
                                        <a @click="excluirABIO(item.id)" class="dropdown-item"
                                            href="javascript:void(0)">
                                            Excluir
                                        </a>
                                    </span>
                                </div>
                            </td>
                        </tr>
                    </template>
                </Table>
            </template>
        </Navbar>

        <ModalVincularABIO ref="modalVincularABIO" :contrato="contrato" :servico="servico" />
        <ModalAdicionarRET ref="modalAdicionarRET" :contrato="contrato" :servico="servico" />

    </AuthenticatedLayout>
</template>
