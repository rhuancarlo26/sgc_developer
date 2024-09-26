<script setup>
import InputLabel from "@/Components/InputLabel.vue";
import Modal from "@/Components/Modal.vue";
import {Link, router} from "@inertiajs/vue3";
import {computed, ref} from "vue";
import { dateTimeFormat } from "@/Utils/DateTimeUtils.js";
import { IconTrash} from "@tabler/icons-vue";
import Table from "@/Components/Table.vue";
import LinkConfirmation from "@/Components/LinkConfirmation.vue";

const modalRef = ref()

const data = ref({});
const abrirModal = (item) => {
  data.value = item;
  modalRef.value.getBsModal().show();
}

const arquivoRetAtual = computed(() => {
    if(!data.value.rets?.length) return [];
    const first = data.value.rets.shift()
    return [first];
})

const saveRET = (e) => {
    router.post(route('contratos.contratada.servicos.mon_atp_fauna.configuracoes.vincular_abio.store_ret'), {
        fk_at_config_vinculacao: data.value.id,
        arquivo_ret: e.target.files[0],
    }, {
        onSuccess() {
            modalRef.value.getBsModal().hide();
        }
    })
}

defineExpose({ abrirModal });
</script>

<template>
  <Modal ref="modalRef" :title="`Licença Abio ${data?.licenca?.numero_licenca}`" modal-dialog-class="modal-xl">
    <template #body>
        <div v-if="Object.keys(data).length > 0">
            <div class="row row-gap-2">
                <div class="col-6 mb-4 row row-gap-2">
                    <div class="col-12">
                        <label>Nº Licença: <b>{{data.licenca.numero_licenca}}</b></label>
                    </div>
                    <div class="col-12">
                        <label>Nome Licença: <b>{{ data.licenca.tipo.nome}}</b></label>
                    </div>
                    <div class="col-12">
                        <label>Data de emissão: <b>{{ dateTimeFormat(data.licenca.data_emissao)}}</b></label>
                    </div>
                    <div class="col-12">
                        <label>Vencimento: <b>{{dateTimeFormat(data.licenca.vencimento)}}</b></label>
                    </div>
                    <div class="col-12">
                        <label>SEI: <b>{{data.licenca.numero_sei}}</b></label>
                    </div>
                    <div class="col-12">
                        <label>Processo DNIT: <b>{{data.licenca.processo_dnit}}</b></label>
                    </div>
                    <div class="col-12">
                        <label>Emissor: <b>{{data.licenca.emissor}}</b></label>
                    </div>
                    <div class="col-12">
                        <label>Empreendimento: <b>{{data.licenca.empreendimento}}</b></label>
                    </div>
                </div>
                <div class="col-6 mb-4 row row-gap-2">
                    <div class="col-12">
                        <label>BR: <b>{{data.licenca.brs}}</b></label>
                    </div>
                    <div v-if="data.licenca.segmentos?.length" class="col-12">
                        <label>UF/KM inicial:
                            <b>{{data.licenca.segmentos[0].uf_inicial}}/{{data.licenca.segmentos[0].km_inicio}}</b></label>
                    </div>
                    <div v-if="data.licenca.segmentos?.length" class="col-12">
                        <label>UF/KM final: <b>{{data.licenca.segmentos[data.licenca.segmentos.length - 1].uf_final}}/{{data.licenca.segmentos[data.licenca.segmentos.length - 1].km_fim}}</b></label>
                    </div>
                    <div class="col-12">
                        <label>Extensão: <b>{{data.licenca.extensao}}</b></label>
                    </div>
                    <div class="col-12">
                        <label>Iníco do Sub-Trecho (PNV): <b>{{data.licenca.inicio_subtrecho}}</b></label>
                    </div>
                    <div class="col-12">
                        <label>Fim do Sub-Trecho (PNV): <b>{{data.licenca.fim_subtrecho}}</b></label>
                    </div>
                </div>
            </div>
            <div class="row row-gap-2">
                <div class="col-6">
                    <div class="row row-gap-2">
                        <div class="col-12">
                            <h4>RET vigente</h4>
                            <Table :columns="['Arquivo', 'Data Inclusão',  'Ação']" :records="{data: arquivoRetAtual, links: []}">
                                <template #body="{item}">
                                    <tr>
                                        <td>{{item.nome_arquivo}}</td>
                                        <td>{{ dateTimeFormat(item.created_at)}}</td>
                                        <td class="w-0">
                                            <LinkConfirmation v-slot="confirmation" :options="{ text: 'Excluir registro?' }">
                                                <Link :onBefore="(request) => confirmation.show({
                                                  ...request,
                                                  onSuccess: () => {
                                                      modalRef.getBsModal().hide();
                                                  }
                                                })"
                                                      :href="route('contratos.contratada.servicos.mon_atp_fauna.configuracoes.vincular_abio.delete_ret', item.id)"
                                                      as="button" method="delete" type="button" class="btn btn-sm btn-icon btn-danger">
                                                    <IconTrash/>
                                                </Link>
                                            </LinkConfirmation>
                                        </td>
                                    </tr>
                                </template>
                            </Table>
                        </div>
                        <div class="col-12">
                            <h4>RET(s) anteriores</h4>
                            <Table :columns="['Arquivo', 'Data Inclusão',  'Ação']" :records="{data: data.rets, links: []}">
                                <template #body="{item}">
                                    <tr>
                                        <td>{{item.nome_arquivo}}</td>
                                        <td>{{ dateTimeFormat(item.created_at)}}</td>
                                        <td class="w-0">
                                            <LinkConfirmation v-slot="confirmation" :options="{ text: 'Excluir registro?' }">
                                                <Link :onBefore="(request) => confirmation.show({
                                                  ...request,
                                                  onSuccess: () => {
                                                      modalRef.getBsModal().hide();
                                                  }
                                                })"
                                                      :href="route('contratos.contratada.servicos.mon_atp_fauna.configuracoes.vincular_abio.delete_ret', item.id)"
                                                      as="button" method="delete" type="button" class="btn btn-sm btn-icon btn-danger">
                                                    <IconTrash/>
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
                    <InputLabel value="Buscar Arquivo (.pdf)" for="arquivo_ret"/>
                    <input @change="saveRET" id="arquivo_ret" type="file" class="form-control" accept=".pdf">
                </div>
            </div>
        </div>
    </template>
    <template #footer>
        <button @click="modalRef.getBsModal().hide()" type="button" class="btn btn-secondary">Fechar</button>
    </template>
  </Modal>
</template>
