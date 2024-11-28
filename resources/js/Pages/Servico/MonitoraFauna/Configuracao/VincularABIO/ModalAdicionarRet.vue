<script setup>
import InputLabel from "@/Components/InputLabel.vue";
import Modal from "@/Components/Modal.vue";
import { Link, router } from "@inertiajs/vue3";
import { computed, ref } from "vue";
import { dateTimeFormat } from "@/Utils/DateTimeUtils.js";
import { IconTrash } from "@tabler/icons-vue";
import Table from "@/Components/Table.vue";
import LinkConfirmation from "@/Components/LinkConfirmation.vue";
import NavButton from "@/Components/NavButton.vue";

const props = defineProps({
    contrato: { type: Object },
    servico: { type: Object }
})

const modalAdicionarRetRef = ref()

const abio = ref({});
const arquivo = ref({});

const abrirModal = (item) => {
    abio.value = item;

    modalAdicionarRetRef.value.getBsModal().show();
}

const arquivoRetAtual = computed(() => {
    if (!abio.value.rets?.length) {
        return [];
    };
    return abio.value.rets.shift();
})

const salvarRet = () => {
    router.post(route('contratos.contratada.servicos.monitora_fauna.configuracoes.vincular_abio.store_ret', {
        contrato: props.contrato.id,
        servico: props.servico.id
    }), {
        id_abio: abio.value.id,
        arquivo_ret: arquivo.value,
    }, {
        onSuccess() {
            modalAdicionarRetRef.value.getBsModal().hide();
        }
    })
}

defineExpose({ abrirModal });
</script>

<template>
    <Modal ref="modalAdicionarRetRef" :title="`Licença Abio ${abio?.licenca?.numero_licenca}`"
        modal-dialog-class="modal-xl">
        <template #body>
            <div>
                <div class="row row-gap-2">
                    <div class="col-6 mb-4 row row-gap-2">
                        <div class="col-12">
                            <label>Nº Licença: <b>{{ abio?.licenca?.numero_licenca }}</b></label>
                        </div>
                        <div class="col-12">
                            <label>Nome Licença: <b>{{ abio?.licenca?.tipo.nome }}</b></label>
                        </div>
                        <div class="col-12">
                            <label>Data de emissão: <b>{{ dateTimeFormat(abio?.licenca?.data_emissao) }}</b></label>
                        </div>
                        <div class="col-12">
                            <label>Vencimento: <b>{{ dateTimeFormat(abio?.licenca?.vencimento) }}</b></label>
                        </div>
                        <div class="col-12">
                            <label>SEI: <b>{{ abio?.licenca?.numero_sei }}</b></label>
                        </div>
                        <div class="col-12">
                            <label>Processo DNIT: <b>{{ abio?.licenca?.processo_dnit }}</b></label>
                        </div>
                        <div class="col-12">
                            <label>Emissor: <b>{{ abio?.licenca?.emissor }}</b></label>
                        </div>
                        <div class="col-12">
                            <label>Empreendimento: <b>{{ abio?.licenca?.empreendimento }}</b></label>
                        </div>
                    </div>
                    <div class="col-6 mb-4 row row-gap-2">
                        <div class="col-12">
                            <label>BR: <b>{{ abio?.licenca?.brs }}</b></label>
                        </div>
                        <div v-if="abio?.licenca?.segmentos?.length" class="col-12">
                            <label>UF/KM inicial:
                                <b>{{ abio?.licenca?.segmentos[0]?.uf_inicial }}/{{
                                    abio?.licenca?.segmentos[0]?.km_inicio }}</b></label>
                        </div>
                        <div v-if="abio?.licenca?.segmentos?.length" class="col-12">
                            <label>UF/KM final: <b>{{ abio?.licenca?.segmentos[abio?.licenca?.segmentos.length -
                                1].uf_final }}/{{ abio?.licenca?.segmentos[abio?.licenca?.segmentos.length -
                                        1]?.km_fim }}</b></label>
                        </div>
                        <div class="col-12">
                            <label>Extensão: <b>{{ abio?.licenca?.extensao }}</b></label>
                        </div>
                        <div class="col-12">
                            <label>Iníco do Sub-Trecho (PNV): <b>{{ abio?.licenca?.inicio_subtrecho }}</b></label>
                        </div>
                        <div class="col-12">
                            <label>Fim do Sub-Trecho (PNV): <b>{{ abio?.licenca?.fim_subtrecho }}</b></label>
                        </div>
                    </div>
                </div>
                <div class="row row-gap-2">
                    <div class="col-6">
                        <div class="row row-gap-2">
                            <div class="col-12">
                                <h4>RET vigente</h4>
                                <Table :columns="['Arquivo', 'Data Inclusão', 'Ação']"
                                    :records="{ data: [arquivoRetAtual], links: [] }">
                                    <template #body="{ item }">
                                        <tr>
                                            <td>{{ item.nome }}</td>
                                            <td>{{ dateTimeFormat(item.created_at) }}</td>
                                            <td class="w-0">
                                                <LinkConfirmation v-slot="confirmation"
                                                    :options="{ text: 'Excluir registro?' }">
                                                    <Link :onBefore="confirmation.show"
                                                        :href="route('contratos.contratada.servicos.monitora_fauna.configuracoes.vincular_abio.destroy_abio_ret', { contrato: contrato.id, servico: servico.id, abio_ret: item.id })"
                                                        method="DELETE" as="button" type="button"
                                                        class="btn btn-icon btn-danger">
                                                    <IconTrash />
                                                    </Link>
                                                </LinkConfirmation>
                                            </td>
                                        </tr>
                                    </template>
                                </Table>
                            </div>
                            <div class="col-12">
                                <h4>RET(s) anteriores</h4>
                                <Table :columns="['Arquivo', 'Data Inclusão', 'Ação']"
                                    :records="{ data: abio.rets, links: [] }">
                                    <template #body="{ item }">
                                        <tr>
                                            <td>{{ item.nome }}</td>
                                            <td>{{ dateTimeFormat(item.created_at) }}</td>
                                            <td class="w-0">
                                                <LinkConfirmation v-slot="confirmation"
                                                    :options="{ text: 'Excluir registro?' }">
                                                    <Link :onBefore="confirmation.show"
                                                        :href="route('contratos.contratada.servicos.monitora_fauna.configuracoes.vincular_abio.destroy_abio_ret', { contrato: contrato.id, servico: servico.id, abio_ret: item.id })"
                                                        method="DELETE" as="button" type="button"
                                                        class="btn btn-icon btn-danger">
                                                    <IconTrash />
                                                    </Link>
                                                </LinkConfirmation>
                                            </td>
                                        </tr>
                                    </template>
                                </Table>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <InputLabel value="Buscar Arquivo (.pdf)" for="arquivo_ret" />
                        <div class="row g-2">
                            <div class="col">
                                <input @input="arquivo = $event.target.files[0]" accept=".pdf" type="file"
                                    class="form-control">
                            </div>
                            <div class="col-auto">
                                <NavButton @click="salvarRet()" type-button="success" title="Salvar" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </Modal>
</template>
