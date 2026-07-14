<script setup>
import InputLabel from "@/Components/InputLabel.vue";
import Modal from "@/Components/Modal.vue";
import { Link, router, useForm } from "@inertiajs/vue3";
import { computed, ref } from "vue";
import { dateTimeFormat } from "@/Utils/DateTimeUtils.js";
import { IconTrash } from "@tabler/icons-vue";
import Table from "@/Components/Table.vue";
import LinkConfirmation from "@/Components/LinkConfirmation.vue";
import NavButton from "@/Components/NavButton.vue";
import InputError from "@/Components/InputError.vue";
import { usePage } from '@inertiajs/vue3'

const props = defineProps({
  contrato: { type: Object },
  servico: { type: Object },
  ufs: { type: Array }
})

const modalRef = ref({});
const municipios = ref([]);
const fileInput = ref(null);
const { modeloUrl } = usePage().props

const biomas = [
  "Caatinga",
  "Cerrado",
  "Floresta Amazônica",
  "Mata Atlântica",
  "Mata de Araucária",
  "Mata de Cocais",
  "Pampa",
  "Pantanal",
  "Zonas Litorâneas"
];

const form = useForm({
  id: null,
  id_servico: props.servico.id,
  data_cadastro: null,
  tamanho_modulo: null,
  uf: null,
  municipio: null,
  bioma: null,
  fitofisionomia: null,
  latitude_inicial: null,
  longitude_inicial: null,
  latitude_final: null,
  longitude_final: null,
  obs: null,
  arquivo: null,
  arquivoArmadilha: null,
  armadilhas: null
});

const abrirModal = (item) => {
  form.reset();
  Object.assign(form, item);

  modalRef.value.getBsModal().show();
}

const getLocalizacao = async () => {
  if (!form.uf) {
    return municipios.value = [];
  }

  const url = `https://servicodados.ibge.gov.br/api/v1/localidades/estados/${form.uf}/municipios`;

  try {
    const response = await fetch(url);
    const data = await response.json();

    municipios.value = data.map(municipio => municipio.nome);
  } catch (e) {
    console.log(e.responseJSON?.Errors || 'Erro ao carregar municípios');
  }
}

const save = () => {
  const url = form.id ? 'update' : 'store';

  form.post(route('contratos.contratada.servicos.monitora_fauna.configuracoes.modulo_amostral.' + url, { contrato: props.contrato.id, servico: props.servico.id }), {
    onSuccess: () => modalRef.value.getBsModal().hide()
  });
}

function deleteArmadilha(id) {
  if (!confirm('Tem certeza que deseja excluir esta armadilha?')) return;

  const url = route(
    'contratos.contratada.servicos.monitora_fauna.configuracoes.modulo_amostral.armadilhas.destroy',
    {
      contrato:  props.contrato.id,
      servico:   props.servico.id,
      armadilha: id,
    }
  );

  axios
    .delete(url)
    .then(({ data }) => {
      form.armadilhas = form.armadilhas.filter(a => a.id !== id);
    })
    .catch((error) => {
      alert(error.response?.data?.content || 'Erro ao excluir');
    });
}

defineExpose({ abrirModal });
</script>

<template>
  <Modal ref="modalRef" title="Novo Módulo Amostral" modal-dialog-class="modal-xl">
    <template #body>
      <form @submit.prevent="save()">
        <div class="row mb-4">
          <div class="col">
            <div class="group-control">
              <InputLabel value="ID Módulo Amostral" for="id" />
              <input disabled type="text" class="form-control" :value="form.id">
              <InputError :message="form.errors.id" />
            </div>
          </div>
          <div class="col">
            <div class="group-control">
              <InputLabel value="Data" for="data_cadastro" />
              <input type="date" class="form-control" v-model="form.data_cadastro">
              <InputError :message="form.errors.data_cadastro" />
            </div>
          </div>
        </div>
        <div class="row mb-4">
          <div class="col">
            <div class="group-control">
              <InputLabel value="Selecionar o tamanho do Módulo Amostral" for="tamanho_modulo" />
              <div>
                <label class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="tamanho_modulo" id="tamanho_modulo" value="5"
                    v-model="form.tamanho_modulo">
                  <span class="form-check-label">5km</span>
                </label>
                <label class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="tamanho_modulo" id="tamanho_modulo" value="4"
                    v-model="form.tamanho_modulo">
                  <span class="form-check-label">4km</span>
                </label>
                <label class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="tamanho_modulo" id="tamanho_modulo" value="3"
                    v-model="form.tamanho_modulo">
                  <span class="form-check-label">3km</span>
                </label>
                <label class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="tamanho_modulo" id="tamanho_modulo" value="2"
                    v-model="form.tamanho_modulo">
                  <span class="form-check-label">2km</span>
                </label>
                <label class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="tamanho_modulo" id="tamanho_modulo" value="1"
                    v-model="form.tamanho_modulo">
                  <span class="form-check-label">1km</span>
                </label>
              </div>
              <InputError :message="form.errors.id" />
            </div>
          </div>
          <div class="col">
            <div class="group-control">
              <InputLabel :value="`N° parcelas: ${form.tamanho_modulo ?? 0} parcela(s)`" />
            </div>
          </div>
        </div>
        <div class="row mb-4">
          <div class="col">
            <div class="group-control">
              <InputLabel value="UF" for="uf" />
              <v-select @option:selected="getLocalizacao()" :options="ufs" label="uf" v-model="form.uf"
                :reduce="uf => uf.uf">
                <template #no-options="{ }">
                  Nenhum registro encontrado.
                </template>
              </v-select>
              <InputError :message="form.errors.uf" />
            </div>
          </div>
          <div class="col">
            <div class="group-control">
              <InputLabel value="Municípios" for="municipio" />
              <v-select :options="municipios" v-model="form.municipio">
                <template #no-options="{ }">
                  Nenhum registro encontrado.
                </template>
              </v-select>
              <InputError :message="form.errors.uf" />
            </div>
          </div>
          <div class="col">
            <div class="group-control">
              <InputLabel value="Bioma" for="bioma" />
              <v-select :options="biomas" v-model="form.bioma">
                <template #no-options="{ }">
                  Nenhum registro encontrado.
                </template>
              </v-select>
              <InputError :message="form.errors.bioma" />
            </div>
          </div>
        </div>
        <div class="row mb-4">
          <div class="col">
            <div class="group-control">
              <InputLabel value="Fitofisionomia" for="fitofisionomia" />
              <textarea class="form-control" name="fitofisionomia" id="fitofisionomia" rows="5"
                v-model="form.fitofisionomia"></textarea>
              <InputError :message="form.errors.fitofisionomia" />
            </div>
          </div>
        </div>
        <div class="row mb-4">
          <div class="col">
            <div class="group-control">
              <InputLabel value="Latitude inicial" for="latitude_inicial" />
              <input type="number" step="any" class="form-control" name="latitude_inicial" id="latitude_inicial"
                rows="5" v-model="form.latitude_inicial" />
              <InputError :message="form.errors.latitude_inicial" />
            </div>
          </div>
          <div class="col">
            <div class="group-control">
              <InputLabel value="Longitude inicial" for="longitude_inicial" />
              <input type="number" step="any" class="form-control" name="longitude_inicial" id="longitude_inicial"
                rows="5" v-model="form.longitude_inicial" />
              <InputError :message="form.errors.longitude_inicial" />
            </div>
          </div>
        </div>
        <div class="row mb-4">
          <div class="col">
            <div class="group-control">
              <InputLabel value="Latitude final" for="latitude_final" />
              <input type="number" step="any" class="form-control" name="latitude_final" id="latitude_final" rows="5"
                v-model="form.latitude_final" />
              <InputError :message="form.errors.latitude_final" />
            </div>
          </div>
          <div class="col">
            <div class="group-control">
              <InputLabel value="Longitude final" for="longitude_final" />
              <input type="number" step="any" class="form-control" name="longitude_final" id="longitude_final" rows="5"
                v-model="form.longitude_final" />
              <InputError :message="form.errors.longitude_final" />
            </div>
          </div>
        </div>
        <div class="row mb-4">
          <div class="col">
            <div class="group-control">
              <InputLabel value="Shapefile" for="arquivo" />
              <input @input="form.arquivo = $event.target.files[0]" type="file" class="form-control" name="arquivo"
                id="arquivo" />
              <InputError :message="form.errors.arquivo" />
            </div>
          </div>
        </div>
        <div class="row mb-4">
          <div class="col">
            <div class="group-control">
              <InputLabel value="Observações" for="obs" />
              <textarea name="obs" id="obs" rows="5" class="form-control" v-model="form.obs"></textarea>
              <InputError :message="form.errors.obs" />
            </div>
          </div>
        </div>
           <div class="row mb-3">
        <div class="col">
          <a
            :href="modeloUrl"
            class="btn btn-outline-primary"
            download
          >
            <IconDownload class="me-1" />
            Baixar Planilha Modelo
          </a>
        </div>
      </div>
         <div class="row mb-4">
          <div class="col">
            <div class="group-control">
              <InputLabel value="Upload de Armadilhas" for="arquivoArmadilha" />
              <input @input="form.arquivoArmadilha = $event.target.files[0]" type="file" class="form-control" name="arquivoArmadilha"
                id="arquivoArmadilha" />
              <InputError :message="form.errors.arquivoArmadilha" />
            </div>
          </div>
        </div>
          <div class="row">
            <div class="col d-flex justify-content-end">
              <button type="submit" class="btn btn-success" :disabled="form.processing">
                Salvar
              </button>
            </div>
          </div>
        </form>
      
     <div class="accordion" id="armadilhasAccordion">
        <div class="accordion-item mt-5">
          <h2 class="accordion-header" id="headingArmadilhas">
            <button
              class="accordion-button collapsed"
              type="button"
              data-bs-toggle="collapse"
              data-bs-target="#collapseArmadilhas"
              aria-expanded="false"
              aria-controls="collapseArmadilhas"
            >
              Armadilhas ({{ form?.armadilhas?.length }})
            </button>
          </h2>
          <div
            id="collapseArmadilhas"
            class="accordion-collapse collapse"
            aria-labelledby="headingArmadilhas"
            data-bs-parent="#armadilhasAccordion"
          >
            <div class="accordion-body p-0">
              <!-- TABELA DE ARMADILHAS -->
              <div class="row" v-if="form?.armadilhas?.length">
                <div class="col-12">
                  <table class="table table-bordered mb-0">
                    <thead>
                      <tr>
                        <th>ID</th>
                        <th>Parcela</th>
                        <th>Forma</th>
                        <th>Tipo</th>
                        <th>Nº Armadilha</th>
                        <th>Lat.</th>
                        <th>Long.</th>
                        <th>Zona</th>
                        <th>Observação</th>
                        <th>Ação</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="arm in form.armadilhas" :key="arm.id">
                        <td>{{ arm.id }}</td>
                        <td>{{ arm.parcela }}</td>
                        <td>{{ arm.forma }}</td>
                        <td>{{ arm.tipo }}</td>
                        <td>{{ arm.numero_armadilha_metodo }}</td>
                        <td>{{ arm.latitude }}</td>
                        <td>{{ arm.longitude }}</td>
                        <td>{{ arm.zona }}</td>
                        <td>{{ arm.observacao }}</td>
                        <td>
                        <button
                          type="button"
                          class="btn btn-sm btn-danger"
                          @click="deleteArmadilha(arm.id)"
                        >
                          <IconTrash size="18" />
                        </button>
                      </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
              <div v-else class="p-3">
                Nenhuma armadilha cadastrada.
              </div>
            </div>
          </div>
        </div>
      </div>
    </template>
  </Modal>
</template>