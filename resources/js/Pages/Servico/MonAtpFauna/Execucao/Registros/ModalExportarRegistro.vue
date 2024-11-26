<script setup>
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import Modal from "@/Components/Modal.vue";
import NavButton from "@/Components/NavButton.vue";
import { useForm } from "@inertiajs/vue3";
import axios from "axios";
import { computed } from "vue";
import { ref } from "vue";

const props = defineProps({
  servico: { type: Object },
  campanhas: { type: Array },
  ufs: { type: Array },
  rodovias: { type: Array }
});

const modal = ref();
const grupo_amostrados = ref([
  { id: 1, nome: 'Avifauna' },
  { id: 2, nome: 'Herpetofauna' },
  { id: 3, nome: 'Mastofauna' }
]);

const form = useForm({
  campanhas: [],
  grupo_amostrados: [],
  ufs: [],
  rodovias: []
});

const abrirModal = () => {
  modal.value.getBsModal().show();
}

const filterRodovia = computed(() => {
  const rodovias = props.rodovias.filter(rodovia => form.ufs.includes(rodovia.estados_id));

  return rodovias.reduce((acc, obj) => {
    if (!acc.some(item => item.rodovia === obj.rodovia)) {
      acc.push(obj);
    }
    return acc;
  }, []);
});

const allCheckedCampanha = computed(() => form.campanhas.length === props.campanhas.length && props.campanhas.length > 0);
const allCheckedGrupoAmostrado = computed(() => form.grupo_amostrados.length === grupo_amostrados.value.length);
const allCheckedUfs = computed(() => form.ufs.length === props.ufs.length);
const allCheckedRodovias = computed(() => form.rodovias.length === filterRodovia.value.length && filterRodovia.value.length > 0);

function splitIntoColumns(arr, columns) {
  const rows = [];
  for (let i = 0; i < arr.length; i += columns) {
    rows.push(arr.slice(i, i + columns));
  }
  return rows;
}

const splitItemsCampanha = computed(() => splitIntoColumns(props.campanhas, 4));
const splitItemsUF = computed(() => splitIntoColumns(props.ufs, 4));
const splitItemsRodovias = computed(() => splitIntoColumns(filterRodovia.value, 4));

const toggleSelectAllCampanha = () => {
  if (allCheckedCampanha.value) {
    form.campanhas = [];
  } else {
    form.campanhas = props.campanhas.map(campanha => campanha.id);
  }
}

const toggleSelectAllGrupoAmostrado = () => {
  if (allCheckedGrupoAmostrado.value) {
    form.grupo_amostrados = [];
  } else {
    form.grupo_amostrados = grupo_amostrados.value.map(grupo => grupo.id);
  }
}

const toggleSelectAllUf = () => {
  if (allCheckedUfs.value) {
    form.ufs = [];
  } else {
    form.ufs = props.ufs.map(uf => uf.id);
  }
}

const toggleSelectAllRodovia = () => {
  if (allCheckedRodovias.value) {
    form.rodovias = [];
  } else {
    form.rodovias = filterRodovia.value.map(rodovia => rodovia.rodovia);
  }
}

defineExpose({ abrirModal });
</script>

<template>
  <Modal ref="modal" title="Exportar Plannilha de Dados" modal-dialog-class="modal-xl">
    <template #body>
      <div class="card-body">
        <div class="row mb-4">
          <div class="col">
            <InputLabel :value="'Selecionar Campanha: ' + form.campanhas.length" for="campanhas" />
            <div class="card">
              <div class="table-responsive">
                <table class="table card-table table-bordered">
                  <thead>
                    <tr>
                      <th class="col-1 text-center">
                        <input class="form-check-input m-0 align-middle" :checked="allCheckedCampanha"
                          @change="toggleSelectAllCampanha" type="checkbox" aria-label="Select all invoices">
                      </th>
                      <th class="text-center">Campanha</th>
                      <th colspan="2" class="text-center">Campanha</th>
                      <th colspan="2" class="text-center">Campanha</th>
                      <th colspan="2" class="text-center">Campanha</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="(row, index) in splitItemsCampanha" :key="index">
                      <template v-for="campanha in row" :key="campanha.id">
                        <td class="text-center">
                          <input class="form-check-input m-0 align-middle" :value="campanha.id" type="checkbox"
                            v-model="form.campanhas" aria-label="Select all invoices">
                        </td>
                        <td class="text-center">
                          {{ campanha.id }}
                        </td>
                      </template>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
            <InputError :message="form.errors.campanhas" />
          </div>
        </div>
        <div class="row mb-4">
          <div class="col">
            <InputLabel :value="'Selecionar Grupo Amostrado: ' + form.grupo_amostrados.length" for="grupo_amostrados" />
            <div class="card">
              <div class="table-responsive">
                <table class="table card-table table-bordered">
                  <thead>
                    <tr>
                      <th class="col-1 text-center">
                        <input class="form-check-input m-0 align-middle" :checked="allCheckedGrupoAmostrado"
                          @change="toggleSelectAllGrupoAmostrado" type="checkbox" aria-label="Select all invoices">
                      </th>
                      <th class="text-center">Grupo Amostrado</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="grupo in grupo_amostrados" :key="grupo.id">
                      <td class="text-center">
                        <input class="form-check-input m-0 align-middle" :value="grupo.id" type="checkbox"
                          v-model="form.grupo_amostrados" aria-label="Select all invoices">
                      </td>
                      <td class="text-center">
                        {{ grupo.nome }}
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
            <InputError :message="form.errors.grupo_amostrados" />
          </div>
        </div>
        <div class="row mb-4">
          <div class="col">
            <InputLabel :value="'Selecionar UF: ' + form.ufs.length" for="ufs" />
            <div class="card">
              <div class="table-responsive">
                <table class="table card-table table-bordered">
                  <thead>
                    <tr>
                      <th class="col-1 text-center">
                        <input class="form-check-input m-0 align-middle" :checked="allCheckedUfs"
                          @change="toggleSelectAllUf" type="checkbox" aria-label="Select all invoices">
                      </th>
                      <th class="text-center">UF</th>
                      <th colspan="2" class="text-center">UF</th>
                      <th colspan="2" class="text-center">UF</th>
                      <th colspan="2" class="text-center">UF</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="(row, index) in splitItemsUF" :key="index">
                      <template v-for="uf in row" :key="uf.id">
                        <td class="text-center">
                          <input class="form-check-input m-0 align-middle" :value="uf.id" type="checkbox"
                            v-model="form.ufs" aria-label="Select all invoices">
                        </td>
                        <td class="text-center">
                          {{ `${uf.uf} - ${uf.nome}` }}
                        </td>
                      </template>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
            <InputError :message="form.errors.ufs" />
          </div>
        </div>
        <div class="row">
          <div class="col">
            <InputLabel :value="'Selecionar BR: ' + form.rodovias.length" for="rodovias" />
            <div class="card">
              <div class="table-responsive">
                <table class="table card-table table-bordered">
                  <thead>
                    <tr>
                      <th class="col-1 text-center">
                        <input class="form-check-input m-0 align-middle" :checked="allCheckedRodovias"
                          @change="toggleSelectAllRodovia" type="checkbox" aria-label="Select all invoices">
                      </th>
                      <th class="text-center">Rodovia</th>
                      <th colspan="2" class="text-center">Rodovia</th>
                      <th colspan="2" class="text-center">Rodovia</th>
                      <th colspan="2" class="text-center">Rodovia</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="row in splitItemsRodovias" :key="row">
                      <template v-for="rodovia in row" :key="rodovia.id">
                        <td class="text-center">
                          <input class="form-check-input m-0 align-middle" :value="rodovia.rodovia" type="checkbox"
                            v-model="form.rodovias" aria-label="Select all invoices">
                        </td>
                        <td class="text-center">
                          {{ rodovia.rodovia }}
                        </td>
                      </template>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
            <InputError :message="form.errors.rodovias" />
          </div>
        </div>
      </div>
    </template>
    <template #footer>
      <a target="_blank"
        :href="route('contratos.contratada.servicos.mon_atp_fauna.execucao.registros.export', { servico: props.servico.id, ...form })"
        class="btn btn-primary">
        Exportar
      </a>
    </template>
  </Modal>
</template>
