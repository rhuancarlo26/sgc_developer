<script setup>
import { Link, useForm } from "@inertiajs/vue3";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import Map from "@/Components/Map.vue";
import LinkConfirmation from "@/Components/LinkConfirmation.vue";
import { ref, watch, computed } from "vue";
import Table from "@/Components/Table.vue";
import { IconMapPin } from "@tabler/icons-vue";
import { IconPencil } from "@tabler/icons-vue";
import { IconTrash } from "@tabler/icons-vue";
import { IconPlus } from "@tabler/icons-vue";
import { IconX } from "@tabler/icons-vue";
import { IconMap } from "@tabler/icons-vue";
import { nextTick } from "vue";
import { useToast } from 'vue-toastification';
import Swal from "sweetalert2";

const toast = useToast();

const props = defineProps({
    ufs: { type: Array },
    rodovias: { type: Array },
    tipos: { type: Array },
    tipo: { type: Object },
    situacoes: { type: Array },
    contrato: { type: Object }
});

const form_trecho = useForm({
    id: null,
    contrato_id: null,
    tipo_contrato: null,
    uf_id: null,
    rodovia_id: null,
    km_inicial: null,
    km_final: null,
    tipo_trecho: null,
    versao_snv: null,
    cod_tipo_trecho: null,
    coordenada: null,
});

const mapContainer = ref();

let isProcessing = false;
watch(
    () => [form_trecho.uf_id, form_trecho.rodovia_id],
    async ([uf_id, rodovia_id]) => {
        if (isProcessing) {
            return
        }

        isProcessing = true;

        try {
            if (uf_id && rodovia_id) {
                await getTipoPorUfBr();
            }
        } finally {
            isProcessing = false;
        }
    },
    {
        immediate: true
    }
);

// watch(
//     () => [form_trecho.km_inicial, form_trecho.km_final, form_trecho.tipo_trecho],
//     async ([km_inicial, km_final]) => {
//         if (isProcessing) {
//             return
//         }

//         isProcessing = true;

//         try {
//             if (km_inicial >= 0 && km_final >= 0 && form_trecho.uf_id && form_trecho.rodovia_id && form_trecho.tipo_trecho) {
//                 await espacializarLinha();
//             }
//         } finally {
//             isProcessing = false;
//         }
//     },
//     {
//         immediate: true
//     }
// );

const tipo_trechos = ref('');
const getTipoPorUfBr = async () => {
    if (form_trecho.uf_id && form_trecho.rodovia_id) {
        try {

            Swal.fire({
                title: 'Carregando...',
                didOpen: () => {
                    Swal.showLoading();
                },
                allowOutsideClick: false,
                allowEscapeKey: false,
                backdrop: true
            });

            const resp = await axios.post(route('contratos.gestao.getTipoPorUfBr_trecho'), { uf_id: form_trecho.uf_id, rodovia_id: form_trecho.rodovia_id, data: form_trecho.versao_snv });

            tipo_trechos.value = resp.data.lista_tp_trecho;

            Swal.close();
        } catch (error) {
            Swal.close();

            toast.error('Erro ao obter tipo por UF');
        }
    }
}

const espacializarLinha = async () => {
    if (form_trecho.uf_id && form_trecho.rodovia_id && form_trecho.km_inicial && form_trecho.km_final && form_trecho.tipo_trecho) {
        try {
            Swal.fire({
                title: 'Salvando...',
                didOpen: () => {
                    Swal.showLoading();
                },
                allowOutsideClick: false,
                allowEscapeKey: false,
                backdrop: true
            });

            const resp = await axios.post(route('contratos.gestao.espacializarLinha_trecho'),
                {
                    uf_id: form_trecho.uf_id,
                    rodovia_id: form_trecho.rodovia_id,
                    km_inicial: form_trecho.km_inicial,
                    km_final: form_trecho.km_final,
                    tipo: form_trecho.tipo_trecho,
                    cd_tipo: form_trecho.cod_tipo_trecho,
                    data: form_trecho.versao_snv
                });

            form_trecho.coordenada = resp.data;

            Swal.close();
        } catch (error) {
            Swal.close();

            toast.error('Erro ao obter a espacialização');
        }
    }
}

const abaTrecho = () => {
    nextTick(() => {
        mapContainer.value.renderMapa();

        renderTrecho();
    })
}

const renderTrecho = () => {
    mapContainer.value.setLinestrings(coordenadas.value, true);
}

const coordenadas = computed(() => {
    return props.contrato.trechos.map(function (objeto) {
        return [objeto.coordenada, modalTechoMap(objeto), objeto];
    });
});

const modalTechoMap = (objeto) => {
    return `
  <span><strong>UF: </strong> ${objeto.uf.uf}</span><br>
  <span><strong>BR: </strong> ${objeto.rodovia.rodovia}</span><br>
  <span><strong>Km Inicial: </strong> ${objeto.km_inicial}</span><br>
  <span><strong>Km Final: </strong> ${objeto.km_final}</span><br>
  <span><strong>Tipo: </strong> ${objeto.tipo_trecho}</span>
  `;
}

const zoomTrecho = (coordenada) => {
    mapContainer.value.zoomToLinestring(coordenada);
}

const zoomFitBounds = () => {
    mapContainer.value.zoomFitBounds();
}

const salvarTrecho = async () => {
    await espacializarLinha();

    if (!form_trecho.coordenada) {
        toast.error('Coordenada não encontrada. Não foi possível salvar o trecho.');
        return;
    }

    form_trecho.contrato_id = props.contrato.id;
    form_trecho.tipo_contrato = props.tipo.id;

    form_trecho.transform((data) => Object.assign({}, data))

    const url = form_trecho.id ? 'update_trecho' : 'store_trecho'

    form_trecho.post(route('contratos.gestao.' + url, form_trecho.id), {
        onSuccess: () => { form_trecho.reset(); renderTrecho(); }
    });
}

const editarTrecho = (trecho) => {
    Object.assign(form_trecho, trecho)
}

const deletarTrecho = (tipoId, trechoId) => {
    Swal.fire({
        title: 'Tem certeza?',
        text: 'Essa ação não poderá ser desfeita!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sim, deletar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            form_trecho.delete(route('contratos.gestao.delete_trecho', [tipoId, trechoId]), {
                preserveScroll: true,
                onSuccess: () => {
                    toast.success('Trecho removido com sucesso!');
                    renderTrecho();
                },
                onError: () => {
                    toast.error('Erro ao tentar remover o trecho.');
                }
            });
        }
    });
};

defineExpose({ abaTrecho })
</script>
<template>
    <form @submit.prevent="salvarTrecho()" :disabled="form_trecho.processing">
        <div class="card-header">
            <h3 class="my-0">Trecho</h3>
        </div>

        <div class="card-body">
            <div class="row">
                <!-- UF -->
                <div class="form-group col">
                    <InputLabel value="UF" for="uf" class="required" />
                    <v-select @option:selected="form_trecho.rodovia_id = null, form_trecho.tipo_trecho = null"
                        class="w-100" id="uf" :options="ufs" label="uf" v-model="form_trecho.uf_id"
                        :reduce="uf => uf.id">
                        <template #no-options="{ }"> Nenhum registro encontrado</template>
                    </v-select>
                </div>
                <!-- RODOVIA  -->
                <div class="form-group col">
                    <InputLabel value="Rodovia" for="rodovia" class="required" />
                    <v-select @option:selected="form_trecho.tipo_trecho = null" id="rodovia" class="w-100"
                        :options="rodovias.filter(i => i.estados_id === form_trecho.uf_id)" label="rodovia"
                        v-model="form_trecho.rodovia_id" :reduce="i => i.id">
                        <template #no-options="{ }"> Nenhum registro encontrado</template>
                    </v-select>
                </div>
                <!-- KM INICIAL  -->
                <div class="form-group col">
                    <InputLabel value="Km Inicial" for="km_inicial" class="required" />
                    <input @change="recalculaExtensao" type="number" step="any" min="0" id="km_inicial"
                        name="km_inicial" v-model="form_trecho.km_inicial" class="form-control" />
                    <InputError :message="form_trecho.errors.km_inicial" class="mt-2" />
                </div>
                <!-- KM FINAL  -->
                <div class="form-group col">
                    <InputLabel value="Km Final" for="km_final" class="required" />
                    <input @change="recalculaExtensao" type="number" id="km_final" name="km_final" step="any" min="0"
                        v-model="form_trecho.km_final" class="form-control" />
                    <InputError :message="form_trecho.errors.km_final" class="mt-2" />
                </div>
                <!-- TIPO  -->
                <div class="form-group col">
                    <InputLabel value="Tipo" for="tipo_trecho" />
                    <div class="row g-2">
                        <div class="col">
                            <select name="" id="tipo" class="form-control form-select"
                                v-model="form_trecho.tipo_trecho">
                                <option v-for="tipo in tipo_trechos?.split(',')" :key="tipo" :value="tipo">{{ tipo }}
                                </option>
                            </select>
                        </div>
                        <div class="col-auto">
                            <button style="height: 40px;" type="submit" class="btn btn-icon btn-success">
                                <IconPlus />
                            </button>
                        </div>
                        <div v-if="form_trecho.id" class="col-auto">
                            <button style="height: 40px;" @click="form_trecho.reset()" type="button"
                                class="btn btn-icon btn-danger">
                                <IconX />
                            </button>
                        </div>
                    </div>
                    <InputError :message="form_trecho.errors.tipo_trecho" class="mt-2" />
                </div>

                <!-- Utilize o código abaixo caso queira adicionara a data de referência para consulta. -->
                <!-- <div class="form-group col">
                    <InputLabel value="Data Referência" for="versao_snv" />
                    <div class="row g-2">
                        <div class="col">
                            <input type="date" id="versao_snv" name="versao_snv" v-model="form_trecho.versao_snv"
                                class="form-control" />
                        </div>
                        <div class="col-auto">
                            <button style="height: 40px;" type="submit" class="btn btn-icon btn-success">
                                <IconPlus />
                            </button>
                        </div>
                        <div v-if="form_trecho.id" class="col-auto">
                            <button style="height: 40px;" @click="form_trecho.reset()" type="button"
                                class="btn btn-icon btn-danger">
                                <IconX />
                            </button>
                        </div>
                    </div>
                    <InputError :message="form_trecho.errors.versao_snv" class="mt-2" />
                </div> -->
            </div>
        </div>
    </form>

    <div class="card-body">
        <div class="table-responsive mb-4">
            <table class="table card-table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>UF</th>
                        <th>BR</th>
                        <th>Km Inicial</th>
                        <th>Km Final</th>
                        <th>Tipo</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="trecho in contrato.trechos" :key="trecho.id" class="cursor-pointer">
                        <td>{{ trecho.uf?.uf }}</td>
                        <td>{{ trecho.rodovia?.rodovia }}</td>
                        <td>{{ trecho.km_inicial }}</td>
                        <td>{{ trecho.km_final }}</td>
                        <td>{{ trecho.tipo_trecho }}</td>
                        <td class="w-1">
                            <div class="d-flex">
                                <button @click="zoomTrecho(trecho.coordenada)" type="button"
                                    class="btn btn-icon btn-primary me-2" :disabled="form_trecho.processing">
                                    <IconMapPin />
                                </button>
                                <button @click="editarTrecho(trecho)" type="button" class="btn btn-icon btn-info me-2"
                                    :disabled="form_trecho.processing">
                                    <IconPencil />
                                </button>
                                <button @click="deletarTrecho(tipo.id, trecho.id)" type="button"
                                    class="btn btn-icon btn-danger">
                                    <IconTrash />
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card-header d-flex justify-content-between">
        <h3 class="my-0">Mapa</h3>
        <button @click="zoomFitBounds()" class="btn btn-icon btn-success">
            <IconMap />
        </button>
    </div>
    <div class="card-body">
        <Map ref="mapContainer" :manual-render="true" :height="'350px'" />
    </div>
</template>
<style>
.vs__dropdown-toggle {
    height: 40px !important;
}
</style>
