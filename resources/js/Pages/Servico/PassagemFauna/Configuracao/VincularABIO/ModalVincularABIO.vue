<script setup>
import Modal from "@/Components/Modal.vue";
import {ref} from "vue";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import {useForm} from "@inertiajs/vue3";
import NavButton from "@/Components/NavButton.vue";
import {useToast} from "vue-toastification";
import {dateTimeFormat} from "@/Utils/DateTimeUtils.js";
import {IconEye} from "@tabler/icons-vue";

const props = defineProps({
    contrato: {type: Object},
    servico: {type: Object}
});
const toast = useToast();
const modalVisualizarABIO = ref();
const licenca = ref({});
const form = useForm({
    id_servico: props.servico.id,
    id_licenca: null,
    numero_licenca: null
});

const buscarLicenca = () => {
    axios.post(route('contratos.contratada.servicos.passagem_fauna.configuracao.vincular_abio.buscar_licenca', {
        contrato: props.contrato.id,
        servico: props.servico.id
    }), form).then((r) => {
        toast.success('Licença encontrada');

        licenca.value = r.data;
    }).catch((r) => {
        toast.error('Não foi possivel achar a licença');
    });
}

const abrirModal = () => {
    modalVisualizarABIO.value.getBsModal().show();
}

const vincularABIO = () => {
    form.id_licenca = licenca.value.id;

    form.post(route('contratos.contratada.servicos.passagem_fauna.configuracao.vincular_abio.store', {
        contrato: props.contrato.id,
        servico: props.servico.id
    }), {
        onSuccess: () => modalVisualizarABIO.value.getBsModal().hide()
    });
}

defineExpose({abrirModal});
</script>

<template>
    <Modal ref="modalVisualizarABIO" title="Vincular ABIO" modal-dialog-class="modal-xl">
        <template #body>
            <div class="row mb-4">
                <div class="col">
                    <InputLabel value="N° da licença" for="numero_licenca"/>
                    <div class="row g-2">
                        <div class="col">
                            <input type="text" name="numero_licenca" id="numero_licenca" class="form-control"
                                   v-model="form.numero_licenca">
                        </div>
                        <div class="col-auto">
                            <NavButton @click="buscarLicenca()" class="nav-item"
                                       type-button="success" title="Buscar"
                            />
                        </div>
                    </div>
                    <InputError :message="form.errors.numero_licenca"/>
                </div>
            </div>
            <div v-if="licenca.id" class="mt-4">
                <div class="row">
                    <div class="col">
                        <div class="table-responsive">
                            <table class="table card-table table-bordered">
                                <tr>
                                    <th>N° licença</th>
                                    <td>{{ licenca.numero_licenca }}</td>
                                </tr>
                                <tr>
                                    <th>Nome licença</th>
                                    <td>-</td>
                                </tr>
                                <tr>
                                    <th>Data de emissão</th>
                                    <td>{{ dateTimeFormat(licenca.data_emissao) }}</td>
                                </tr>
                                <tr>
                                    <th>Vencimento</th>
                                    <td>{{ dateTimeFormat(licenca.vencimento) }}</td>
                                </tr>
                                <tr>
                                    <th>SEI</th>
                                    <td>{{ licenca.numero_sei }}</td>
                                </tr>
                                <tr>
                                    <th>Processo DNIT</th>
                                    <td>{{ licenca.processo_dnit }}</td>
                                </tr>
                                <tr>
                                    <th>Emissor</th>
                                    <td>{{ licenca.emissor }}</td>
                                </tr>
                                <tr>
                                    <th>Empreendimento</th>
                                    <td>{{ licenca.empreendimento }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div class="col">
                        <div class="table-responsive">
                            <table class="table card-table table-bordered">
                                <tr>
                                    <th>BR</th>
                                    <td>
                                        <template v-if="licenca.brs">
                                          <span v-for="br in licenca?.brs.split(',')" :key="br"
                                                class="badge bg-warning text-white m-1">
                                            {{ br }}
                                          </span>
                                        </template>
                                    </td>
                                </tr>
                                <tr>
                                    <th>UF/KM inicial</th>
                                    <td>
                                        <template v-if="licenca.iniciais">
                                          <span v-for="uf in licenca.iniciais.split(',')" :key="uf"
                                                class="badge bg-warning text-white m-1">
                                            {{ uf }}
                                          </span>
                                        </template>
                                        <template v-if="licenca.segmentos.length">
                                            <span class="badge bg-warning text-white m-1">
                                            {{
                                                    Math.min(...licenca.segmentos.map(segmento => segmento.km_inicio))
                                                }}
                                            </span>
                                        </template>
                                    </td>
                                </tr>
                                <tr>
                                    <th>UF/KM final</th>
                                    <td>
                                        <template v-if="licenca.finais">
                                          <span v-for="uf in licenca.finais.split(',')" :key="uf"
                                                class="badge bg-warning text-white m-1">
                                            {{ uf }}
                                          </span>
                                        </template>
                                        <template v-if="licenca.segmentos.length">
                                            <span class="badge bg-warning text-white m-1">
                                                {{ Math.max(...licenca.segmentos.map(segmento => segmento.km_fim)) }}
                                            </span>
                                        </template>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Extensão</th>
                                    <td>
                                        <span class="badge bg-warning text-white m-1">
                                            {{ licenca.extensao }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Inicio do sub-trecho (PNV)</th>
                                    <td>{{ licenca.inicio_subtrecho }}</td>
                                </tr>
                                <tr>
                                    <th>Fim do sub-trecho (PNV)</th>
                                    <td>{{ licenca.fim_subtrecho }}</td>
                                </tr>
                                <tr>
                                    <th>Visualizar PDF</th>
                                    <td>
                                        <IconEye/>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-end mt-4">
                    <NavButton @click="vincularABIO()" type-button="success" title="Vincular ABIO"/>
                </div>
            </div>
        </template>
        <template #footer>
        </template>
    </Modal>
</template>
