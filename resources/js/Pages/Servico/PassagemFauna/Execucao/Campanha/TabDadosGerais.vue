<script setup>

import { IconDeviceFloppy } from "@tabler/icons-vue";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import NavButton from "@/Components/NavButton.vue";
import { router, useForm } from "@inertiajs/vue3";

const props = defineProps({
    contrato: { type: Object },
    servico: { type: Object },
    campanha: { type: Object }
})

const formatDate = (date) => {
    if (!date) return null;
    return date.split(" ")[0];
};

const form = useForm({
    ...props.campanha,
    id: props.campanha?.id || null,
    periodo: props.campanha?.periodo || null,
    data_inicial: formatDate(props.campanha?.data_inicial) || null,
    data_final: formatDate(props.campanha?.data_final) || null
});


const salvarDadosGerais = () => {
    if (form.id) {
        form.patch(
            route('contratos.contratada.servicos.passagem_fauna.execucao.campanha.update', {
                contrato: props.contrato.id,
                servico: props.servico.id,
                campanha: form.id,
            }),
            {
                onSuccess: () => {
                    router.visit(
                        route('contratos.contratada.servicos.passagem_fauna.execucao.campanha.create', {
                            contrato: props.contrato.id,
                            servico: props.servico.id,
                            campanha: form.id,
                        })
                    );
                }
            }
        );
    } else {
        form.post(
            route('contratos.contratada.servicos.passagem_fauna.execucao.campanha.store', {
                contrato: props.contrato.id,
                servico: props.servico.id,
            }),
        );
    }
};

</script>

<template>
    <div class="row mb-4">
        <div class="col">
            <InputLabel value=" N° campanha" for="id" />
            <input type="text" name="id" id="id" class="form-control" v-model="form.id" disabled>
            <InputError :message="form.errors.id" />
        </div>
        <div class="col">
            <InputLabel value="Período" for="periodo" />
            <div>
                <label class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="radios-inline" value="Seca"
                        v-model="form.periodo">
                    <span class="form-check-label">Seca</span>
                </label>
                <label class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="radios-inline" value="Chuva"
                        v-model="form.periodo">
                    <span class="form-check-label">Chuva</span>
                </label>
            </div>
            <InputError :message="form.errors.periodo" />
        </div>
    </div>
    <div class="row mb-4">
        <div class="col">
            <InputLabel value="Data inicial" for="data_inicial" />
            <input type="date" name="data_inicial" id="data_inicial" class="form-control" v-model="form.data_inicial">
            <InputError :message="form.errors.data_inicial" />
        </div>
        <div class="col">
            <InputLabel value="Data final" for="data_final" />
            <input type="date" name="data_final" id="data_final" class="form-control" v-model="form.data_final">
            <InputError :message="form.errors.data_final" />
        </div>
    </div>
    <div class="row">
        <div class="col d-flex justify-content-end">
            <NavButton @click="salvarDadosGerais()" type-button="success" :icon="IconDeviceFloppy"
                :title="form.id ? 'Alterar' : 'Salvar'" />
        </div>
    </div>
</template>

<style scoped></style>
