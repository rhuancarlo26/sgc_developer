<script setup>
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import NavButton from "@/Components/NavButton.vue";
import Table from "@/Components/Table.vue";
import {IconEye, IconTrash} from "@tabler/icons-vue";

const emit = defineEmits(['salvar-arquivo', 'excluir-registro']);

const props = defineProps({
    contrato: {type: Object},
    servico: {type: Object},
    form: {type: Object}
});

const salvarArquivo = () => {
    emit('salvar-arquivo');
}

const excluirArquivo = () => {
    emit('excluir-registro');
}
</script>

<template>
    <div v-if="form.imagem" class="row">
        <Table class="table-hover" :columns="['Arquivo', 'Ação']" :records="{data: [form.imagem], links: []}">
            <template #body="{item}">
                <tr class="cursor-pointer">
                    <td>{{ item.nome }}</td>
                    <td>
                        <a class="btn btn-icon btn-info me-1" target="_blank"
                           :href="route('contratos.contratada.servicos.passagem_fauna.execucao.registro.visualizar_imagem', {contrato:contrato.id, servico:servico.id, imagem:item.id})">
                            <IconEye/>
                        </a>
                        <NavButton @click="excluirArquivo()" class="btn-icon" :icon="IconTrash" type-button="danger"/>
                    </td>
                </tr>
            </template>
        </Table>
    </div>
    <div v-else class="row">
        <div class="col">
            <InputLabel value="Buscar Arquivo (.jpg)"/>
            <div class="row g-2">
                <div class="col">
                    <input @input="form.arquivo = $event.target.files[0]" type="file" name="arquivo"
                           id="arquivo"
                           class="form-control">
                </div>
                <div class="col-auto">
                    <NavButton @click="salvarArquivo()" title="Salvar" type-button="success"/>
                </div>
            </div>
            <InputError :message="form.errors.arquivo"/>
        </div>
    </div>
</template>

<style scoped>

</style>
