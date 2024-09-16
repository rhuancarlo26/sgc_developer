<script setup>
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import {router, useForm} from "@inertiajs/vue3";
import {IconDots, IconEye, IconPlus, IconX} from "@tabler/icons-vue";
import {computed, ref, watch} from "vue";
import NavButton from "@/Components/NavButton.vue";
import ModalVisualizarLote from "@/Pages/Servico/ContOcorr/Configuracao/LoteObra/ModalVisualizarLote.vue";

let uf_rodovias = [];

const modalVisualizarLote = ref({});
const props = defineProps({
    contrato: Object,
    ufs: Array,
    rodovias: Array
})

const form_trecho = useForm({
    id: null,
    contrato_id: props.contrato.id,
    uf: {},
    rodovia: {},
    km_inicial: null,
    km_final: null,
    trecho_tipo: null
});

watch(
    () => form_trecho.uf,
    () => {
        // Filtra rodovias de acordo com UF selecionada;
        if (form_trecho.uf) {
            uf_rodovias = props.rodovias.filter((rodovia) => {
                return rodovia.uf_id === form_trecho.uf.id;
            });
        } else {
            uf_rodovias = [{rodovia: 'Selecione uma UF'}];
        }
    }
);

const lotes = computed(() => {
    return props.contrato.servicos.map(servico => {
        return servico.supervisao_lotes.map(lote => {
            return lote
        })
    }).flat()
})

const abrirModalVisualizarLote = (item) => {
    modalVisualizarLote.value.abrirModal(item)
}

const salvarTrecho = () => {
    if (form_trecho.id) {
        form_trecho.patch(route('contratos.contratada.update_empreendimento_trecho'), {
            onSuccess: () => form_trecho.reset()
        });
    } else {
        form_trecho.post(route('contratos.contratada.store_empreendimento_trecho'), {
            onSuccess: () => form_trecho.reset()
        });
    }
}

const excluirTrecho = (trecho) => {
    router.delete(route('contratos.contratada.destroy_empreendimento_trecho', trecho.id));
}

</script>

<template>
    <div class="row mb-4 mt-4">
        <div class="card-header mb-4">
            <h3 class="my-0">Lote de obra</h3>
        </div>
        <div class="table-responsive">
            <table class="table card-table table-bordered">
                <thead>
                <tr>
                    <th>ID lote</th>
                    <th>Nome do lote</th>
                    <th>BR</th>
                    <th>UF</th>
                    <th>Km inicial</th>
                    <th>Km final</th>
                    <th>Construtora / Consórcio</th>
                    <th>N° Contrato</th>
                    <th>Situação</th>
                    <th>Supervisora de obra</th>
                    <th>Ação</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="lote in lotes" :key="lote.id">
                    <td>{{ lote.nome_id }}</td>
                    <td>{{ lote.nome }}</td>
                    <td>{{ lote.rodovia?.rodovia }}</td>
                    <td>{{ lote.uf?.uf }}</td>
                    <td>{{ lote.km_inicial }}</td>
                    <td>{{ lote.km_final }}</td>
                    <td>{{ lote.empresa }}</td>
                    <td>{{ lote.num_contrato }}</td>
                    <td>{{ lote.situacao_contrato }}</td>
                    <td>{{ lote.supervisora_obras }}</td>
                    <td>
                        <NavButton @click="abrirModalVisualizarLote(lote)" type-button="info" :icon="IconEye"/>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="row mb-4 mt-4">
        <div class="card-header mb-4">
            <h3 class="my-0">Empreendimento</h3>
        </div>
        <div class="col">
            <InputLabel value="UF" for="uf"/>
            <v-select :options="ufs" label="uf" v-model="form_trecho.uf">
                <template #no-options="{}">
                    Nenhum registro encontrado.
                </template>
            </v-select>
            <InputError :message="form_trecho.errors.uf_id"/>
        </div>
        <div class="col">
            <InputLabel value="Rodovia" for="rodovia"/>
            <v-select :options="uf_rodovias" label="rodovia" v-model="form_trecho.rodovia">
                <template #no-options="{}">
                    Nenhum registro encontrado.
                </template>
            </v-select>
            <InputError :message="form_trecho.errors.rodovia_id"/>
        </div>
        <div class="col">
            <InputLabel value="Km Inicial" for="km_inicial"/>
            <input type="number" step="any" id="km_inicial" name="km_inicial" class="form-control"
                   v-model="form_trecho.km_inicial"/>
            <InputError :message="form_trecho.errors.km_inicial"/>
        </div>
        <div class="col">
            <InputLabel value="Km Final" for="km_final"/>
            <input type="number" step="any" id="km_final" name="km_final" class="form-control"
                   v-model="form_trecho.km_final"/>
            <InputError :message="form_trecho.errors.km_final"/>
        </div>
        <div class="col">
            <InputLabel value="Tipo" for="trecho_tipo"/>
            <div class="row g-2">
                <div class="col">
                    <select name="trecho_tipo" id="trecho_tipo" class="form-control form-select"
                            v-model="form_trecho.trecho_tipo">
                        <option value="B">B</option>
                        <option value="U">U</option>
                        <option value="A">A</option>
                        <option value="C">C</option>
                        <option value="N">N</option>
                        <option value="V">V</option>
                    </select>
                </div>
                <div class="col-auto">
                    <button @click="salvarTrecho()" type="button" class="btn btn-icon btn-success">
                        <IconPlus/>
                    </button>
                </div>
                <div v-if="form_trecho.id" class="col-auto">
                    <button @click="form_trecho.reset()" type="button" class="btn btn-icon btn-danger">
                        <IconX/>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="table-responsive mb-4">
        <table class="table table-hover non-hover">
            <thead>
            <tr>
                <th>UF</th>
                <th>Rodovia</th>
                <th>Km inicial</th>
                <th>Km final</th>
                <th>Tipo trecho</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="trecho in contrato.empreendimento_trechos" :key="trecho.id">
                <td>{{ trecho.uf?.uf }}</td>
                <td>{{ trecho.rodovia?.rodovia }}</td>
                <td>{{ trecho.km_inicial }}</td>
                <td>{{ trecho.km_final }}</td>
                <td>{{ trecho.trecho_tipo }}</td>
                <td @click.stop>
            <span class="dropdown">
              <button class="btn dropdown-toggle align-text-top" data-bs-boundary="viewport" data-bs-toggle="dropdown"
                      aria-expanded="false">
                <IconDots/>
              </button>
              <div class="dropdown-menu dropdown-menu-end" style="">
                <a @click="Object.assign(form_trecho, trecho)" class="dropdown-item" href="javascript:void(0)">
                  Editar
                </a>
                <a @click="excluirTrecho(trecho)" class="dropdown-item" href="javascript:void(0)">
                  Excluir
                </a>
              </div>
            </span>
                </td>
            </tr>
            </tbody>
        </table>
    </div>

    <ModalVisualizarLote ref="modalVisualizarLote"/>

</template>
