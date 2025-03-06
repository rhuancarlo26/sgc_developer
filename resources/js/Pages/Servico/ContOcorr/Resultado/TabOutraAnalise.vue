<script setup>

import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import NavButton from "@/Components/NavButton.vue";
import {IconDeviceFloppy, IconEye, IconPencil, IconTrash} from "@tabler/icons-vue";
import {Link, useForm} from "@inertiajs/vue3";
import LinkConfirmation from "@/Components/LinkConfirmation.vue";

const props = defineProps({
    contrato: {type: Object},
    servico: {type: Object},
    resultado: {type: Object},
});

const form = useForm({
    id: null,
    id_resultado: props.resultado.id,
    nome: null,
    caminho_arquivo: null,
    arquivo: null,
    analise: null
});

const salvar = () => {
    const url = form.id ? 'update_outra_analise' : 'store_outra_analise'
    form.post(route('contratos.contratada.servicos.cont_ocorrencia.resultado.' + url, {
        contrato: props.contrato.id,
        servico: props.servico.id,
        resultado: props.resultado.id
    }), {
        onSuccess: () => form.reset()
    });
}

</script>

<template>
    <div class="row mb-4">
        <div class="col">
            <InputLabel value="Arquivo" for="arquivo"/>
            <input @input="form.arquivo = $event.target.files[0]" type="file" name="arquivo" id="arquivo"
                   class="form-control">
            <InputError/>
        </div>
        <div class="col">
            <InputLabel value="Nome" for="nome"/>
            <input type="text" name="nome" id="nome" class="form-control" v-model="form.nome">
            <InputError/>
        </div>
    </div>
    <div class="row mb-4">
        <div class="col">
            <InputLabel value="Análise" for="analise"/>
            <textarea name="analise" id="analise" class="form-control" rows="5" v-model="form.analise"></textarea>
        </div>
    </div>
    <div class="row mb-4">
        <div class="col d-flex justify-content-end">
            <NavButton @click="salvar()" type-button="success" :icon="IconDeviceFloppy"
                       title="Salvar"/>
        </div>
    </div>
    <hr>
    <div class="row mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table card-table table-bordered table-hover">
                    <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Análise dos resultados</th>
                        <th>Visualizar arquivo</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="outra_analise in resultado.outras_analises" :key="outra_analise.id">
                        <td>{{ outra_analise.nome }}</td>
                        <td>{{ outra_analise.analise }}</td>
                        <td>
                            <a class="btn btn-icon btn-primary me-1" target="_blank"
                               :href="route('contratos.contratada.servicos.cont_ocorrencia.resultado.visualizar', { contrato: props.contrato.id, servico: props.servico.id, resultado: props.resultado.id, outraAnalise: outra_analise.id })">
                                <IconEye/>
                            </a>
                            <NavButton @click="Object.assign(form, outra_analise)" class="btn-icon"
                                       type-button="primary" :icon="IconPencil"/>
                            <LinkConfirmation v-slot="confirmation"
                                              :options="{ text: 'A remoção do resultado será permanente.' }">
                                <Link :onBefore="confirmation.show"
                                      :href="route('contratos.contratada.servicos.cont_ocorrencia.resultado.delete_outra_analise', { contrato: contrato.id, servico: servico.id, resultado: resultado.id, outraAnalise: outra_analise.id })"
                                      as="button" method="delete" type="button" class="btn btn-icon btn-danger">
                                    <IconTrash/>
                                </Link>
                            </LinkConfirmation>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<style scoped>

</style>
