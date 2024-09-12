<script setup>

import {IconDeviceFloppy, IconDots, IconTrash} from "@tabler/icons-vue";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import NavButton from "@/Components/NavButton.vue";
import {Link, useForm} from "@inertiajs/vue3";
import Table from "@/Components/Table.vue";
import LinkConfirmation from "@/Components/LinkConfirmation.vue";

const props = defineProps({
    contrato: {type: Object},
    servico: {type: Object},
    campanha: {type: Object},
    abios: {type: Array}
})

const form = useForm({
    id_campanha: null,
    id_abio: null
});

const vincularAbio = () => {
    form.id_campanha = props.campanha.id;

    form.post(route('contratos.contratada.servicos.passagem_fauna.execucao.campanha.store_abio', {
        contrato: props.contrato.id,
        servico: props.servico.id
    }));
}
</script>

<template>
    <div class="row mb-4">
        <div class="col">
            <InputLabel value="N° ABIO" for="id_abio"/>
            <div class="row g-2">
                <div class="col">
                    <select name="abio" id="abio" class="form-select" v-model="form.id_abio">
                        <option v-for="abio in abios" :key="abio.id" :value="abio.id">{{
                                abio.licenca?.numero_licenca
                            }}
                        </option>
                    </select>
                </div>
                <div class="col-auto">
                    <NavButton @click="vincularAbio()" title="Vincular" type-button="success"/>
                </div>
            </div>
            <InputError :message="form.errors.id_abio"/>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <Table :columns="['Abio\'s vigentes', 'Ação']" :records="{data: campanha.abios, links: []}"
                   table-class="table-hover">
                <template #body="{ item }">
                    <tr>
                        <td>{{ item.abio?.licenca?.numero_licenca }}</td>
                        <td>
                            <LinkConfirmation v-slot="confirmation"
                                              :options="{ text: 'A remoção da ABIO será permanente.' }">
                                <Link :onBefore="confirmation.show"
                                      :href="route('contratos.contratada.servicos.passagem_fauna.execucao.campanha.delete_abio', { contrato: contrato.id, servico: servico.id, campanha_abio: item.id })"
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
