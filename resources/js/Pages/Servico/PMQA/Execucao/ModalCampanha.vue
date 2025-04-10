<script setup>
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import Modal from "@/Components/Modal.vue";
import NavButton from "@/Components/NavButton.vue";
import Table from "@/Components/Table.vue";
import {useForm} from "@inertiajs/vue3";
import {IconDeviceFloppy} from "@tabler/icons-vue";
import {computed} from "vue";
import {ref} from "vue";

const props = defineProps({
    contrato: {type: Object},
    servico: {type: Object},
    pontos: {type: Array},
});

const modalCampanha = ref();

const form = useForm({
    id: null,
    fk_servico: props.servico.id,
    nome_campanha: null,
    dt_inicio: null,
    dt_fim: null,
    pontos: []
});

const arrayPontosSalvo = ref([]);

const abrirModal = (item) => {
    form.reset();
    arrayPontosSalvo.value = [];

    if (item) {
        form.id = item.id;
        form.nome_campanha = item.nome_campanha;
        form.dt_inicio = item.dt_inicio;
        form.dt_fim = item.dt_fim;

        if (item.pontos.length) {
            let arr = item.pontos.map(a => a.id) ?? [];

            arrayPontosSalvo.value = arr;
            form.pontos = arr
        }
    }

    modalCampanha.value.getBsModal().show();
}

const pontosFiltrados = computed(() => {
    return props.pontos.filter(ponto => !ponto.campanhas.length || arrayPontosSalvo.value.includes(ponto.id));
});

const saveCampanha = () => {
    if (form.id) {
        form.patch(route('contratos.contratada.servicos.pmqa.execucao.update', {
            contrato: props.contrato.id,
            servico: props.servico.id
        }), {
            onSuccess: () => modalCampanha.value.getBsModal().hide()
        });
    } else {
        form.post(route('contratos.contratada.servicos.pmqa.execucao.store', {
            contrato: props.contrato.id,
            servico: props.servico.id
        }), {
            onSuccess: () => modalCampanha.value.getBsModal().hide()
        });
    }
}

defineExpose({abrirModal});
</script>

<template>
    <Modal ref="modalCampanha" title="Nova campanha" modal-dialog-class="modal-xl">
        <template #body>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col form-group">
                        <InputLabel value="Nome da campanha" for="nome_campanha"/>
                        <input type="text" class="form-control" name="nome_campanha" id="nome_campanha"
                               v-model="form.nome_campanha">
                        <InputError :message="form.errors.nome_campanha"/>
                    </div>
                    <div class="col form-group">
                        <InputLabel value="Data de início" for="dt_inicio"/>
                        <input type="date" class="form-control" name="dt_inicio" id="dt_inicio"
                               v-model="form.dt_inicio">
                        <InputError :message="form.errors.dt_inicio"/>
                    </div>
                    <div class="col form-group">
                        <InputLabel value="Data de término" for="dt_fim"/>
                        <input type="date" class="form-control" name="dt_fim" id="dt_fim" v-model="form.dt_fim">
                        <InputError :message="form.errors.dt_fim"/>
                    </div>
                </div>

                <div class="table-responsive">
                    <InputError :message="form.errors.pontos"/>
                    <table class="table card-table table-bordered">
                        <thead>
                        <tr>
                            <th class="text-center">Ponto</th>
                            <th class="text-center">Classe</th>
                            <th class="text-center">Tipo de ambiente</th>
                            <th class="text-center">UF</th>
                            <th class="text-center">Município</th>
                            <th class="text-center">Bacia hifrográfica</th>
                            <th class="text-center">Km rodovia</th>
                            <th class="text-center">Estaca</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="ponto in pontosFiltrados" :key="ponto.id">
                            <td class="text-center">
                                <label class="form-check">
                                    <input class="form-check-input" type="checkbox" :value="ponto.id"
                                           v-model="form.pontos">
                                    <span class="form-check-label">{{ ponto.id }}</span>
                                </label>
                            </td>
                            <td class="text-center">{{ ponto.classe }}</td>
                            <td class="text-center">{{ ponto.tipo_ambiente }}</td>
                            <td class="text-center">{{ ponto.UF }}</td>
                            <td class="text-center">{{ ponto.municipio }}</td>
                            <td class="text-center">{{ ponto.bacia_hidrografica }}</td>
                            <td class="text-center">{{ ponto.km_rodovia }}</td>
                            <td class="text-center">{{ ponto.estaca }}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </template>
        <template #footer>
            <NavButton @click="saveCampanha()" type-button="success" :icon="IconDeviceFloppy"
                       :title="form.id ? 'Editar' : 'Salvar'"/>
        </template>
    </Modal>
</template>
