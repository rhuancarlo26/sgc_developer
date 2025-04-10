<script setup>
import {ref} from "vue";
import Modal from "@/Components/Modal.vue";
import {dateTimeFormat} from "@/Utils/DateTimeUtils.js";
import Table from "@/Components/Table.vue";
import InputLabel from "@/Components/InputLabel.vue";
import {Link, router} from "@inertiajs/vue3";
import {IconEye, IconTrash} from "@tabler/icons-vue";
import LinkConfirmation from "@/Components/LinkConfirmation.vue";

const modalRef = ref();
const conclusao = ref(null);
const abrirModal = (item) => {
    conclusao.value = item;
    modalRef.value.getBsModal().show();
}

const saveConclusao = () => {
    router.patch(route('contratos.contratada.servicos.supressao-vegetacao.relatorio.update'), conclusao.value, {
        preserveState: true,
    })
}

const saveRelatorioAnexo = (event) => {
    router.post(route('contratos.contratada.servicos.supressao-vegetacao.relatorio.relatorio-anexo.store'), {
        arquivo: event.target.files[0],
        fk_relatorio: conclusao.value.id
    }, {
        preserveState: true,
        onSuccess: () => {
            modalRef.value.getBsModal().hide();
        }
    })
}

defineExpose({abrirModal});
</script>

<template>
    <Modal ref="modalRef" :title="'Conclusão do relatório ' + conclusao?.nome_relatorio" modal-dialog-class="modal-xl">
        <template #body>
            <div v-if="conclusao">
                <div class="row row-gap-2 mb-2">
                    <div class="col-12">
                        <InputLabel value="Conclusão" for="conclusao"/>
                        <textarea v-model="conclusao.conclusao" id="conclusao" class="form-control"></textarea>
                    </div>
                    <div class="col-12 d-flex justify-items-end">
                        <button type="button" @click="saveConclusao" class="ms-auto btn btn-success">Salvar Conclusão
                        </button>
                    </div>
                </div>
                <div class="row row-gap-2 mb-2">
                    <div class="col-12">
                        <InputLabel value="Buscar arquivo (.pdf)" for="local_shape_em_app"/>
                        <input @change="saveRelatorioAnexo" id="shapefile" type="file" class="form-control"
                               accept=".pdf">
                    </div>
                </div>
                <div class="row row-gap-2 mb-2">
                    <Table :columns="['Documentos anexados', 'Data', 'Ações']"
                           :records="{ data: conclusao.anexos, links: [] }">
                        <template #body="{item}">
                            <tr>
                                <td>{{ item.nome_arquivo }}</td>
                                <td>{{ dateTimeFormat(item.created_at) ?? '-' }}</td>
                                <td class="text-center d-flex justify-content-center">
                                    <a :href="item.caminho" target="_blank" class="btn btn-icon btn-info me-1">
                                        <IconEye/>
                                    </a>
                                    <LinkConfirmation v-slot="confirmation" :options="{ text: 'Excluir registro?' }">
                                        <Link
                                            :onBefore="(request) => confirmation.show({
                                                ...request,
                                                onSuccess: () => {
                                                    modalRef.getBsModal().hide();
                                                }
                                            })"
                                              :href="route('contratos.contratada.servicos.supressao-vegetacao.relatorio.relatorio-anexo.delete', item.id)"
                                              as="button" method="delete" type="button" class="btn btn-icon btn-danger">
                                            <IconTrash/>
                                        </Link>
                                    </LinkConfirmation>
                                </td>
                            </tr>
                        </template>
                    </Table>
                </div>
            </div>
        </template>
        <template #footer>
            <button @click="modalRef.getBsModal().hide()" type="button" class="btn btn-secondary">Fechar</button>
        </template>
    </Modal>
</template>
