<script setup>
import Table from '@/Components/Table.vue';
import { Link, useForm, router } from "@inertiajs/vue3";
import axios from 'axios';
import { onMounted } from 'vue';
import { ref, watch, computed } from "vue";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import { IconTrash } from '@tabler/icons-vue';
import { IconEdit } from '@tabler/icons-vue';
import { IconPlus } from '@tabler/icons-vue';
import { IconMapPin } from "@tabler/icons-vue";
import { IconPencil } from "@tabler/icons-vue";
import { IconX } from "@tabler/icons-vue";
import { IconMap } from "@tabler/icons-vue";
import { nextTick } from "vue";
import { useToast } from 'vue-toastification';
import Swal from "sweetalert2";
import Map from "@/Components/Map.vue";
// import ModalFormLicencaSegmento from './ModalFormLicencaSegmento.vue';

const toast = useToast();

const props = defineProps({
    licenca: { type: Object },
    ufs: { type: Array },
    rodovias: { type: Array },
});

const form_segmentos = useForm({
    idlicenca_br: null,
    licenca_id: props.licenca?.id,
    rodovia_id: null,
    uf_id: null,
    km_inicial: null,
    km_final: null,
    extensao: null,
    tipo_trecho: null,
    versao_snv: null,
    cod_tipo_trecho: null,
    coordenada: null,
});

const mapContainer = ref();

let isProcessing = false;

watch(
    () => [form_segmentos.uf_id, form_segmentos.rodovia_id],
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

const tipo_trechos = ref('');
const getTipoPorUfBr = async () => {
    if (form_segmentos.uf_id && form_segmentos.rodovia_id) {
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

            const resp = await axios.post(route('licenca_segmento.getTipoPorUfBr_trecho'), { uf_id: form_segmentos.uf_id, rodovia_id: form_segmentos.rodovia_id, data: form_segmentos.versao_snv });

            tipo_trechos.value = resp.data.lista_tp_trecho;

            Swal.close();
        } catch (error) {
            Swal.close();

            toast.error('Erro ao obter tipo por UF');
        }
    }
}

const espacializarLinha = async () => {
    if (form_segmentos.uf_id && form_segmentos.rodovia_id && form_segmentos.km_inicial && form_segmentos.km_final && form_segmentos.tipo_trecho) {
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

            const resp = await axios.post(route('licenca_segmento.espacializarLinha_trecho'),
                {
                    uf_id: form_segmentos.uf_id,
                    rodovia_id: form_segmentos.rodovia_id,
                    km_inicial: form_segmentos.km_inicial,
                    km_final: form_segmentos.km_final,
                    tipo: form_segmentos.tipo_trecho,
                    cd_tipo: form_segmentos.cod_tipo_trecho,
                    data: form_segmentos.versao_snv
                });

            form_segmentos.coordenada = resp.data;

            Swal.close();
        } catch (error) {
            Swal.close();

            toast.error('Erro ao obter a espacialização');
        }
    }
}

const recalculaExtensao = () => {
    const extensao = form_segmentos.km_final - form_segmentos.km_inicial;
    form_segmentos.extensao = extensao < 0 ? 0 : extensao.toFixed(2)
}

const salvarTrecho = async () => {
    await espacializarLinha();

    if (!form_segmentos.coordenada) {
        toast.error('Coordenada não encontrada. Não foi possível salvar o trecho.');
        return;
    }

    form_segmentos.transform((data) => Object.assign({}, data))

    const url = form_segmentos.idlicenca_br ? 'update' : 'store'

    form_segmentos.post(route('licenca_segmento.' + url, form_segmentos.idlicenca_br), {
        onSuccess: () => {
            form_segmentos.reset();
            if (props.licenca.id) {
                getLicencaSegmentos();
            }
            renderTrecho();
        }
    });
}

let licencaSegmentos = ref({});
// const modalFormSegmento = ref({});

onMounted(() => {
    if (props.licenca.id) {
        getLicencaSegmentos();
    }
})

const getLicencaSegmentos = () => {
    axios.get(route('licenca_segmento.get', props.licenca?.id))
        .then(response => {
            licencaSegmentos.value = response.data;
        })
        .catch(response => {
            console.log(response);
        });
}

const editarTrecho = (item) => {
    form_segmentos.idlicenca_br = item?.idlicenca_br;
    form_segmentos.licenca_id = item?.licenca_id;
    form_segmentos.km_inicial = item?.km_inicio;
    form_segmentos.km_final = item?.km_fim;
    form_segmentos.extensao = item?.extensao_br;
    form_segmentos.uf_id = item?.uf_inicial_rel?.id;
    form_segmentos.rodovia_id = item?.rodovias?.id;
    form_segmentos.tipo_trecho = item?.trecho_tipo;
}

const deletarTrecho = (idlicenca_br) => {
    Swal.fire({
        title: 'Tem certeza?',
        text: 'Essa ação não poderá ser desfeita!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sim, deletar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            form_segmentos.delete(route('licenca_segmento.delete', [idlicenca_br]), {
                preserveScroll: true,
                onSuccess: () => {
                    toast.success('Segmento removido com sucesso!');
                    getLicencaSegmentos();
                    renderTrecho();
                },
                onError: () => {
                    toast.error('Erro ao tentar remover o segmento.');
                }
            });
        }
    });
};

const abaSegmento = () => {
    nextTick(() => {
        mapContainer.value.renderMapa();

        renderTrecho();
    })
}

const renderTrecho = () => {
    mapContainer.value.setLinestrings(coordenadas.value, true);
}

const coordenadas = computed(() => {
    return props.licenca?.segmentos.map(function (objeto) {
        return [objeto.coordenada, modalTechoMap(objeto), objeto];
    });
});

const modalTechoMap = (objeto) => {
    return `
  <span><strong>UF: </strong> ${objeto.uf_inicial_rel.uf}</span><br>
  <span><strong>BR: </strong> ${objeto.rodovias.rodovia}</span><br>
  <span><strong>Km Inicial: </strong> ${objeto.km_inicio}</span><br>
  <span><strong>Km Final: </strong> ${objeto.km_fim}</span><br>
  <span><strong>Tipo: </strong> ${objeto.trecho_tipo}</span>
  `;
}

const zoomTrecho = (coordenada) => {
    mapContainer.value.zoomToLinestring(coordenada);
}

const zoomFitBounds = () => {
    mapContainer.value.zoomFitBounds();
}

defineExpose({ abaSegmento })
</script>
<template>
    <form @submit.prevent="salvarTrecho()" :disabled="form_segmentos.processing">
        <div class="card-header">
            <h3 class="my-0">Segmentos</h3>
        </div>

        <div class="card-body">
            <div class="row">
                <!-- UF -->
                <div class="form-group col">
                    <InputLabel value="UF" for="uf" class="required" />
                    <v-select @option:selected="form_segmentos.rodovia_id = null, form_segmentos.tipo_trecho = null"
                        class="w-100" id="uf" :options="ufs" label="uf" v-model="form_segmentos.uf_id"
                        :reduce="uf => uf.id">
                        <template #no-options="{ }"> Nenhum registro encontrado</template>
                    </v-select>
                </div>
                <!-- RODOVIA  -->
                <div class="form-group col">
                    <InputLabel value="Rodovia" for="rodovia" class="required" />
                    <v-select @option:selected="form_segmentos.tipo_trecho = null" id="rodovia" class="w-100"
                        :options="rodovias.filter(i => i.estados_id === form_segmentos.uf_id)" label="rodovia"
                        v-model="form_segmentos.rodovia_id" :reduce="i => i.id">
                        <template #no-options="{ }"> Nenhum registro encontrado</template>
                    </v-select>
                </div>
                <!-- KM INICIAL  -->
                <div class="form-group col">
                    <InputLabel value="Km Inicial" for="km_inicial" class="required" />
                    <input @change="recalculaExtensao" type="number" step="any" min="0" id="km_inicial"
                        name="km_inicial" v-model="form_segmentos.km_inicial" class="form-control" />
                    <InputError :message="form_segmentos.errors.km_inicial" class="mt-2" />
                </div>
                <!-- KM FINAL  -->
                <div class="form-group col">
                    <InputLabel value="Km Final" for="km_final" class="required" />
                    <input @change="recalculaExtensao" type="number" id="km_final" name="km_final" step="any" min="0"
                        v-model="form_segmentos.km_final" class="form-control" />
                    <InputError :message="form_segmentos.errors.km_final" class="mt-2" />
                </div>
                <!-- Extensão  -->
                <div class="form-group col">
                    <InputLabel value="Extensão" for="extensao" class="required" />
                    <input disabled type="number" id="extensao" name="extensao" step="any" min="0"
                        v-model="form_segmentos.extensao" class="form-control" />
                    <InputError :message="form_segmentos.errors.extensao" class="mt-2" />
                </div>
                <!-- TIPO  -->
                <div class="form-group col">
                    <InputLabel value="Tipo" for="tipo_trecho" />
                    <div class="row g-2">
                        <div class="col">
                            <select name="" id="tipo" class="form-control form-select"
                                v-model="form_segmentos.tipo_trecho">
                                <option v-for="tipo in tipo_trechos?.split(',')" :key="tipo" :value="tipo">{{ tipo }}
                                </option>
                            </select>
                        </div>
                        <div class="col-auto">
                            <button style="height: 40px;" type="submit" class="btn btn-icon btn-success">
                                <IconPlus />
                            </button>
                        </div>
                        <div v-if="form_segmentos.id" class="col-auto">
                            <button style="height: 40px;" @click="form_segmentos.reset()" type="button"
                                class="btn btn-icon btn-danger">
                                <IconX />
                            </button>
                        </div>
                    </div>
                    <InputError :message="form_segmentos.errors.tipo_trecho" class="mt-2" />
                </div>
            </div>
        </div>
    </form>

    <hr>

    <div class="card-body">
        <Table :columns="['UF', 'Rodovia', 'KM Inicial', 'KM Final', 'Extensão', 'Tipo', 'Ação']"
            :records="licencaSegmentos" :axios-pagination="true" table-class="table-hover">
            <template #body="{ item }">
                <tr>
                    <td class="text-center">
                        {{ item?.uf_inicial_rel?.uf }}
                    </td>
                    <td class="text-center">
                        {{ item?.rodovias?.rodovia }}
                    </td>
                    <td class="text-center">
                        {{ item?.km_inicio }}
                    </td>
                    <td class="text-center">
                        {{ item?.km_fim }}
                    </td>
                    <td class="text-center">
                        {{ item?.extensao_br }}
                    </td>
                    <td class="text-center">
                        {{ item?.trecho_tipo }}
                    </td>
                    <td class="w-1">
                        <div class="d-flex">
                            <button @click="zoomTrecho(item?.coordenada)" type="button"
                                class="btn btn-icon btn-primary me-2" :disabled="form_segmentos.processing">
                                <IconMapPin />
                            </button>
                            <button @click="editarTrecho(item)" type="button" class="btn btn-icon btn-info me-2"
                                :disabled="form_segmentos.processing">
                                <IconPencil />
                            </button>
                            <button @click="deletarTrecho(item?.idlicenca_br)" type="button"
                                class="btn btn-icon btn-danger">
                                <IconTrash />
                            </button>
                        </div>
                    </td>
                </tr>
            </template>
        </Table>
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

    <!-- <ModalFormLicencaSegmento @atualizarsegmento="getLicencaSegmentos()" :licenca="licenca" :ufs="ufs"
        :rodovias="rodovias" ref="modalFormSegmento" /> -->

</template>
<style>
.vs__dropdown-toggle {
    height: 40px !important;
}
</style>
