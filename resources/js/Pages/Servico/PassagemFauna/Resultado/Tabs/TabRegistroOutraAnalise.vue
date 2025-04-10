<script setup>

import InputLabel from "@/Components/InputLabel.vue";
import {Link, useForm} from "@inertiajs/vue3";
import InputError from "@/Components/InputError.vue";
import NavButton from "@/Components/NavButton.vue";
import Table from "@/Components/Table.vue";
import {IconEye, IconPencil, IconTrash} from "@tabler/icons-vue";
import LinkConfirmation from "@/Components/LinkConfirmation.vue";

const props = defineProps({
    contrato: {type: Object},
    servico: {type: Object},
    resultado: {type: Object},
})

const form = useForm({
    id: null,
    id_resultado: null,
    nome: null,
    arquivo: null,
    analise: null,
    caminho_arquivo: null
})

const salvar = () => {
    form.id_resultado = props.resultado.id;

    const url = form.id ? 'update_outra_analise' : 'store_outra_analise';

    form.post(route('contratos.contratada.servicos.passagem_fauna.resultado.' + url, {
        contrato: props.contrato.id,
        servico: props.servico.id,
        resultado: props.resultado.id
    }));
}
</script>

<template>
    <div class="mb-4">
        <div class="row mb-4">
            <div class="col">
                <InputLabel value="Nome da análise" for="nome"/>
                <input type="text" name="nome" id="nome" class="form-control" v-model="form.nome">
                <InputError :message="form.errors.nome"/>
            </div>
            <div class="col">
                <InputLabel value="Buscar arquivo"/>
                <input @input="form.arquivo = $event.target.files[0]" type="file" name="arquivo" id="arquivo"
                       class="form-control">
                <InputError :message="form.errors.arquivo"/>
            </div>
        </div>
        <div class="row mb-4">
            <div class="col">
                <InputLabel value="Análise dos resultados" for="analise"/>
                <textarea class="form-control" name="analise" id="analise" v-model="form.analise" rows="5"></textarea>
                <InputError :message="form.errors.analise"/>
            </div>
        </div>
        <div class="d-flex justify-content-end">
            <NavButton @click="salvar()" type-button="success" :title="form.id ? 'Alterar' : 'Salvar'"/>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <Table :columns="['Nome', 'Análise dos resultado', 'Extensão', 'Ação']"
                   :records="{data:resultado.outras_analises, links: []}"
                   table-class="table-hover">
                <template #body="{item}">
                    <tr class="cursor-pointer">
                        <td>{{ item.nome }}</td>
                        <td>{{ item.analise }}</td>
                        <td>{{ item.extensao }}</td>
                        <td>
                            <a target="_blank"
                               :href="route('contratos.contratada.servicos.passagem_fauna.resultado.visualizar_outra_analise', {contrato:contrato.id, servico:servico.id,resultado:resultado.id,outra_analise: item.id})"
                               class="btn btn-icon btn-info me-1">
                                <IconEye/>
                            </a>
                            <NavButton @click="Object.assign(form, item)" :icon="IconPencil" class="btn-icon"
                                       type-button="primary"/>
                            <LinkConfirmation v-slot="confirmation"
                                              :options="{ text: 'A remoção da outra análise será permanente.' }">
                                <Link :onBefore="confirmation.show"
                                      :href="route('contratos.contratada.servicos.passagem_fauna.resultado.delete_outra_analise', { contrato: contrato.id, servico: servico.id, resultado: resultado.id, outra_analise:item.id })"
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

<style scoped>

</style>
