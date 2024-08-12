<script setup>
import {ref} from "vue";
import Modal from "@/Components/Modal.vue";
import {router, useForm} from "@inertiajs/vue3";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";

const props = defineProps({
    servico: {type: Object},
    periodos: {type: Array},
})

const form = useForm({
    id: null,
    chave: null,
    periodo: null,
    dt_inicio: null,
    dt_final: null,
    mes: null,
    semestre: null,
    ano: null,
})

const modalRef = ref();
const abrirModal = (item) => {
    form.reset()
    if (item != null) {
        Object.assign(form, item);
    }
    modalRef.value.getBsModal().show();
}

const save = () => {
    form.transform((data) => ({
        ...data,
        servico_id: props.servico.id,
    }));

    const onSuccess = () => {
        modalRef.value.getBsModal().hide();
        form.reset();
    }

    if (form.id !== null) {
        router.post(route('contratos.contratada.servicos.supressao-vegetacao.execucao.supressao.update'), {
            _method: 'patch',
            ...form.data(),
            fotos: form.fotos.filter(foto => foto instanceof File),
        }, {
            preserveState: true,
            onSuccess
        })
        return
    }

    form.post(route('contratos.contratada.servicos.supressao-vegetacao.resultado.store'), {
        preserveState: true,
        onSuccess
    })
}

defineExpose({abrirModal});
</script>

<template>
    <form @submit.prevent="save">
        <Modal ref="modalRef" title="Cadastro Área de Supressão" modal-dialog-class="modal-xl">
            <template #body>
                <div class="row row-gap-2 mb-2">
                    <div v-if="form.chave" class="col">
                        <InputLabel value="Código" for="codigo"/>
                        <input v-model="form.chave" id="nome" class="form-control" disabled/>
                        <InputError :message="form.errors.chave"/>
                    </div>
                    <div class="col">
                        <InputLabel value="Período de Análise" for="periodo_analise"/>
                        <v-select :options="periodos" v-model="form.periodo" :reduce="p => p.id">
                            <template #no-options="{}">
                                Nenhum registro encontrado.
                            </template>
                        </v-select>
                        <InputError :message="form.errors.periodo"/>
                    </div>
                </div>
                <div class="row row-gap-2 mb-2" v-if="form.periodo === 'P'">
                    <div class="col-lg-6">
                        <InputLabel value="Data Início" for="dt_inicio"/>
                        <input v-model="form.dt_inicio" type="date" id="dt_inicio" class="form-control" />
                        <InputError :message="form.errors.dt_inicio"/>
                    </div>
                    <div class="col-lg-6">
                        <InputLabel value="Data Final" for="dt_final"/>
                        <input v-model="form.dt_final" type="date" id="dt_inicio" class="form-control" />
                        <InputError :message="form.errors.dt_final"/>
                    </div>
                </div>
                <div class="row row-gap-2 mb-2" v-if="form.periodo === 'M'">
                    <div class="col">
                        <InputLabel value="Mês" for="mes"/>
                        <input v-model="form.mes" type="month" id="mes" class="form-control" />
                        <InputError :message="form.errors.mes"/>
                    </div>
                </div>
                <div class="row row-gap-2 mb-2" v-if="form.periodo === 'S'">
                    <div class="col">
                        <InputLabel value="Escolha o semestre" for="semestre"/>
                        <v-select :options="[{id: 1, label: '1° Semestre'}, {id: 2, label: '2° Semestre'}]" v-model="form.semestre" :reduce="s => s.id">
                            <template #no-options="{}">
                                Nenhum registro encontrado.
                            </template>
                        </v-select>
                        <InputError :message="form.errors.semestre"/>
                    </div>
                </div>
                <div class="row row-gap-2 mb-2"  v-if="form.periodo === 'S' || form.periodo === 'A'">
                    <div class="col">
                        <InputLabel value="Ano" for="ano"/>
                        <input type="number" v-model="form.ano" class="form-control" id="ano"
                               min="2000" max="2100" maxlength="4" />
                        <InputError :message="form.errors.ano"/>
                    </div>
                </div>
            </template>
            <template #footer>
                <button @click="modalRef.getBsModal().hide()" type="button" class="btn btn-secondary">Fechar</button>
                <button type="submit" class="btn btn-success">Salvar</button>
            </template>
        </Modal>
    </form>
</template>
