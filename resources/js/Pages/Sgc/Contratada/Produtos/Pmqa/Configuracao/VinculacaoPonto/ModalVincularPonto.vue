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
    pmqa: {type: Object},
    listas: {type: Array},
    pontos: {type: Array},
    produto: {type: Object},
});

const modalVincularPonto = ref();
const vinculacao = ref({});

const form = useForm({
  pmqa_id: props.pmqa.id,   // ✅
  lista: null,
  periodicidade: null,
  relatorio_parcial: null,
  relatorio_acomulado: null,
  pontos: [],
});

const reset = () => {
  form.pmqa_id = props.pmqa.id;  // ✅
  form.lista = null;
  form.periodicidade = null;
  form.relatorio_parcial = null;
  form.relatorio_acomulado = null;
  form.pontos = [];
};


const abrirModal = (item = null) => {
  reset();
  vinculacao.value = {};

  if (item) {
    vinculacao.value = item;

    form.id = item.id ?? null;
    form.periodicidade = item.periodicidade ?? null;
    form.relatorio_parcial = item.relatorio_parcial ?? null;
    form.relatorio_acomulado = item.relatorio_acomulado ?? null;

    form.lista =
      props.listas.find(l => l.id === (item.lista_id ?? item.id)) ?? null;

    form.pontos = item.pontos?.map(p => p.id) ?? [];
  }

  modalVincularPonto.value?.getBsModal?.()?.show(); // ✅ não quebra
};

const listasArray = computed(() =>
  Array.isArray(props.listas) ? props.listas : (props.listas?.data ?? [])
);

const listasNaoUtilizados = computed(() => {
  return listasArray.value.filter((lista) => {
    return !lista.pontos?.length || lista.id === vinculacao.value.id;
  });
});

const pontosNaoUtilizados = computed(() => {
    return props.pontos.filter(ponto => {
        let vinculado = !ponto.vinculado;
        let editar = false;

        if (vinculacao.value.id) {
            let arr = vinculacao.value.pontos.map(ponto => ponto.id);

            editar = arr.includes(ponto.id);
        }

        return vinculado || editar;
    });
});

const saveVinculacaoPontos = () => {
    form.post(route('contratos.contratada.sgc.pmqa.configuracao.vinculacao_ponto.store', {
        contrato: props.contrato.id,
        produto: props.produto.slug,
        pmqa: props.pmqa.id
    }), {
        onSuccess: () => modalVincularPonto.value.getBsModal().hide()
    });
}

defineExpose({ abrirModal });
</script>

<template>
    <Modal ref="modalVincularPonto" title="Vincular pontos" modal-dialog-class="modal-xl">
        <template #body>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col form-group">
                        <InputLabel value="Nome da lista" for="nome"/>
                        <v-select :options="listasNaoUtilizados" label="nome" v-model="form.lista">
                            <template #no-options="{}">
                                Nenhum registro encontrado.
                            </template>
                        </v-select>
                        <InputError :message="form.errors.lista"/>
                    </div>
                    <div class="col form-group">
                        <InputLabel value="Periodicidade" for="periodicidade"/>
                        <v-select :options="['Continua', 'Por demanda', 'Periodico']" label="periodicidade"
                                  v-model="form.periodicidade">
                            <template #no-options="{}">
                                Nenhum registro encontrado.
                            </template>
                        </v-select>
                        <InputError :message="form.errors.lista"/>
                    </div>
                    <div v-if="form.periodicidade === 'Periodico'" class="row col">
                        <div class="col form-group">
                            <InputLabel value="Relatório parcial" for="relatorio_parcial"/>
                            <v-select :options="[30, 60, 90, 120]" label="relatorio_parcial"
                                      v-model="form.relatorio_parcial">
                                <template #no-options="{}">
                                    Nenhum registro encontrado.
                                </template>
                            </v-select>
                            <InputError :message="form.errors.lista"/>
                        </div>
                        <div class="col form-group">
                            <InputLabel value="Relatório acomulado" for="relatorio_acomulado"/>
                            <v-select :options="[180, 360]" label="relatorio_acomulado"
                                      v-model="form.relatorio_acomulado">
                                <template #no-options="{}">
                                    Nenhum registro encontrado.
                                </template>
                            </v-select>
                            <InputError :message="form.errors.lista"/>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
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
                        <tr v-for="ponto in pontosNaoUtilizados" :key="ponto.id">
                            <td>
                                <label class="form-check">
                                    <input class="form-check-input" type="checkbox" :value="ponto.id"
                                           v-model="form.pontos">
                                    <span class="form-check-label">{{ ponto.id }}</span>
                                </label>
                            </td>
                            <td class="text-center">{{ ponto.classe }}</td>
                            <td class="text-center">{{ ponto.tipo_ambiente }}</td>
                            <td class="text-center">{{ ponto.uf }}</td>
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
            <NavButton @click="saveVinculacaoPontos()" type-button="success" :icon="IconDeviceFloppy"
                       :title="form.id ? 'Alterar' : 'Salvar'"/>
        </template>
    </Modal>
</template>
