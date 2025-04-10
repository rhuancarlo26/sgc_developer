<script setup>

import InputLabel from "@/Components/InputLabel.vue";
import NavButton from "@/Components/NavButton.vue";
import InputError from "@/Components/InputError.vue";
import {Link, useForm} from "@inertiajs/vue3";
import {IconTrash} from "@tabler/icons-vue";
import Table from "@/Components/Table.vue";
import LinkConfirmation from "@/Components/LinkConfirmation.vue";

const props = defineProps({
    contrato: {type: Object},
    servico: {type: Object},
    campanha: {type: Object}
})

const form_rh = useForm({
    id_campanha: null,
    id_servico_rh: null
});

const form_ret = useForm({
    id_campanha: null,
    id_ret: null
});

const vincularRh = () => {
    form_rh.id_campanha = props.campanha.id;

    form_rh.post(route('contratos.contratada.servicos.passagem_fauna.execucao.campanha.store_rh', {
        contrato: props.contrato.id,
        servico: props.servico.id
    }));
}

const vincularRet = () => {
    form_ret.id_campanha = props.campanha.id;

    form_ret.post(route('contratos.contratada.servicos.passagem_fauna.execucao.campanha.store_ret', {
        contrato: props.contrato.id,
        servico: props.servico.id
    }));
}

</script>

<template>
    <div class="mb-4">
        <div class="row mb-4">
            <div class="col">
                <InputLabel value="Seleciar profissional" for="id_servico_rh"/>
                <div class="row g-2">
                    <div class="col">
                        <select name="id_servico_rh" id="id_servico_rh" class="form-select"
                                v-model="form_rh.id_servico_rh">
                            <option v-for="rh in servico.rhs" :key="rh.id" :value="rh.id">{{
                                    rh.nome
                                }}
                            </option>
                        </select>
                    </div>
                    <div class="col-auto">
                        <NavButton @click="vincularRh()" title="Vincular" type-button="success"/>
                    </div>
                </div>
                <InputError :message="form_rh.errors.id_servico_rh"/>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <Table :columns="['Equipe técnica', 'Ação']" :records="{data: campanha.rhs, links: []}"
                       table-class="table-hover">
                    <template #body="{ item }">
                        <tr>
                            <td>{{ item.servico_rh?.rh?.nome }}</td>
                            <td>
                                <LinkConfirmation v-slot="confirmation"
                                                  :options="{ text: 'A remoção da ABIO será permanente.' }">
                                    <Link :onBefore="confirmation.show"
                                          :href="route('contratos.contratada.servicos.passagem_fauna.execucao.campanha.delete_rh', { contrato: contrato.id, servico: servico.id, campanha_rh: item.id })"
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
    </div>
    <div>
        <div class="row mb-4">
            <div class="col">
                <InputLabel value="Selecione uma RET" for="id_ret"/>
                <div class="row g-2">
                    <div class="col">
                        <select name="id_ret" id="id_ret" class="form-select"
                                v-model="form_ret.id_ret">
                            <option v-for="ret in servico.passagem_fauna_abios"
                                    :key="ret.abio_ret.id" :value="ret.abio_ret.id">
                                {{ ret.abio_ret.nome }} - {{ ret.licenca.numero_licenca }}
                            </option>
                        </select>
                    </div>
                    <div class="col-auto">
                        <NavButton @click="vincularRet()" title="Vincular" type-button="success"/>
                    </div>
                </div>
                <InputError :message="form_ret.errors.id_ret"/>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <Table :columns="['Número Abio', 'RET', 'Ação']" :records="{data: campanha.rets, links: []}"
                       table-class="table-hover">
                    <template #body="{ item }">
                        <tr>
                            <td>{{ item.ret?.abio?.licenca?.numero_licenca }}</td>
                            <td>{{ item.ret?.nome }}</td>
                            <td>
                                <LinkConfirmation v-slot="confirmation"
                                                  :options="{ text: 'A remoção da ABIO será permanente.' }">
                                    <Link :onBefore="confirmation.show"
                                          :href="route('contratos.contratada.servicos.passagem_fauna.execucao.campanha.delete_ret', { contrato: contrato.id, servico: servico.id, campanha_ret: item.id })"
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
    </div>
</template>

<style scoped>

</style>
