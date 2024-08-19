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

const props = defineProps({
  ufs: { type: Array },
  rodovias: { type: Array },
  tipos: { type: Array },
  tipo: { type: Object },
  situacoes: { type: Array },
  contrato: { type: Object }
});

let uf_rodovias = [];

const form_trecho = useForm({
  id: null,
  contrato_id: null,
  tipo_contrato: null,
  uf: {},
  rodovia: {},
  km_inicial: null,
  km_final: null,
  tipo_trecho: null
});

const mapContainer = ref();

watch(
  () => form_trecho.uf,
  () => {
    if (form_trecho.uf) {
      uf_rodovias = props.rodovias.filter((rodovia) => {
        return rodovia.uf_id === form_trecho.uf.id;
      });
    } else {
      uf_rodovias = [{ rodovia: 'Selecione uma UF' }];
    }
  }
);

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

const salvarTrecho = () => {
  form_trecho.contrato_id = props.contrato.id;
  form_trecho.tipo_contrato = props.tipo.id;

  form_trecho.transform((data) => Object.assign({}, data))

  if (form_trecho.id) {
    form_trecho.patch(route('contratos.gestao.update_trecho', form_trecho.id), {
      preserveScroll: true,
      onSuccess: () => form_trecho.reset()
    });
  } else {
    form_trecho.post(route('contratos.gestao.store_trecho'), {
      preserveScroll: true,
      onSuccess: () => form_trecho.reset()
    });
  }
}

const editarTrecho = (trecho) => {
  Object.assign(form_trecho, trecho)
}

defineExpose({ abaTrecho })
</script>
<template>
  <form @submit.prevent="salvarTrecho()" :disabled="form_trecho.processing">
    <div class="card-header">
      <h3 class="my-0">Trecho</h3>
    </div>
    <div class="card-body">
      <div class="row">
        <div class="col">
          <InputLabel value="UF" for="uf" />
          <v-select :options="ufs" label="uf" class="" v-model="form_trecho.uf">
            <template #no-options="{}">
              Nenhum registro encontrado.
            </template>
          </v-select>
          <InputError :message="form_trecho.errors.uf_id" />
        </div>
        <div class="col">
          <InputLabel value="Rodovia" for="rodovia" />
          <v-select :options="uf_rodovias" label="rodovia" v-model="form_trecho.rodovia">
            <template #no-options="{}">
              Nenhum registro encontrado.
            </template>
          </v-select>
          <InputError :message="form_trecho.errors.rodovia_id" />
        </div>
        <div class="col">
          <InputLabel value="Km Inicial" for="km_inicial" />
          <input type="number" step="any" id="km_inicial" name="km_inicial" class="form-control"
            v-model="form_trecho.km_inicial" />
          <InputError :message="form_trecho.errors.km_inicial" />
        </div>
        <div class="col">
          <InputLabel value="Km Final" for="km_final" />
          <input type="number" step="any" id="km_final" name="km_final" class="form-control"
            v-model="form_trecho.km_final" />
          <InputError :message="form_trecho.errors.km_final" />
        </div>
        <div class="col">
          <InputLabel value="Tipo" for="tipo_trecho" />
          <div class="row g-2">
            <div class="col">
              <select name="tipo_trecho" id="tipo_trecho" class="form-control form-select"
                v-model="form_trecho.tipo_trecho">
                <option value="B">B</option>
                <option value="U">U</option>
                <option value="A">A</option>
                <option value="C">C</option>
                <option value="N">N</option>
                <option value="V">V</option>
              </select>
            </div>
            <div class="col-auto">
              <button type="submit" class="btn btn-icon btn-success">
                <IconPlus />
              </button>
            </div>
            <div v-if="form_trecho.id" class="col-auto">
              <button @click="form_trecho.reset()" type="button" class="btn btn-icon btn-danger">
                <IconX />
              </button>
            </div>
          </div>
        </div>
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
                <button @click="zoomTrecho(trecho.coordenada)" type="button" class="btn btn-icon btn-primary me-2"
                  :disabled="form_trecho.processing">
                  <IconMapPin />
                </button>
                <button @click="editarTrecho(trecho)" type="button" class="btn btn-icon btn-info me-2"
                  :disabled="form_trecho.processing">
                  <IconPencil />
                </button>
                <LinkConfirmation v-slot="confirmation"
                  :options="{ text: 'Contratos removidos não podem ser restaurados' }">
                  <Link :onBefore="confirmation.show"
                    :href="route('contratos.gestao.delete_trecho', [tipo.id, trecho.id])" method="DELETE" as="button"
                    type="button" class="btn btn-icon btn-danger">
                  <IconTrash />
                  </Link>
                </LinkConfirmation>
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
