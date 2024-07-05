<script setup>
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import Modal from "@/Components/Modal.vue";
import NavButton from "@/Components/NavButton.vue";
import Table from "@/Components/Table.vue";
import { useForm } from "@inertiajs/vue3";
import { IconDeviceFloppy } from "@tabler/icons-vue";
import { computed } from "vue";
import { ref } from "vue";

const props = defineProps({
  contrato: { type: Object },
  servico: { type: Object },
  pontos: { type: Array },
});

const modalCampanha = ref();

const form = useForm({
  id: null,
  servico_id: props.servico.id,
  nome: null,
  data_inicio: null,
  data_termino: null,
  pontos: []
});

const arrayPontosSalvo = ref([]);

const abrirModal = (item) => {
  form.reset();
  arrayPontosSalvo.value = [];

  if (item) {
    form.id = item.id;
    form.nome = item.nome;
    form.data_inicio = item.data_inicio;
    form.data_termino = item.data_termino;

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
    form.patch(route('contratos.contratada.servicos.pmqa.execucao.update', { contrato: props.contrato.id, servico: props.servico.id }), {
      onSuccess: () => modalCampanha.value.getBsModal().hide()
    });
  } else {
    form.post(route('contratos.contratada.servicos.pmqa.execucao.store', { contrato: props.contrato.id, servico: props.servico.id }), {
      onSuccess: () => modalCampanha.value.getBsModal().hide()
    });
  }
}

defineExpose({ abrirModal });
</script>

<template>
  <Modal ref="modalCampanha" title="Nova campanha" modal-dialog-class="modal-xl">
    <template #body>
      <div class="card-body">
        <div class="row mb-4">
          <div class="col form-group">
            <InputLabel value="Nome da campanha" for="nome" />
            <input type="text" class="form-control" name="nome" id="nome" v-model="form.nome">
            <InputError :message="form.errors.nome" />
          </div>
          <div class="col form-group">
            <InputLabel value="Data de início" for="data_inicio" />
            <input type="date" class="form-control" name="data_inicio" id="data_inicio" v-model="form.data_inicio">
            <InputError :message="form.errors.data_inicio" />
          </div>
          <div class="col form-group">
            <InputLabel value="Data de término" for="data_termino" />
            <input type="date" class="form-control" name="data_termino" id="data_termino" v-model="form.data_termino">
            <InputError :message="form.errors.data_termino" />
          </div>
        </div>

        <div class="table-responsive">
          <InputError :message="form.errors.pontos" />
          <table class="table card-table table-bordered">
            <thead>
              <tr>
                <th>Ponto</th>
                <th>Classe</th>
                <th>Tipo de ambiente</th>
                <th>UF</th>
                <th>Município</th>
                <th>Bacia hifrográfica</th>
                <th>Km rodovia</th>
                <th>Estaca</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="ponto in pontosFiltrados" :key="ponto.id">
                <td>
                  <label class="form-check">
                    <input class="form-check-input" type="checkbox" :value="ponto.id" v-model="form.pontos">
                    <span class="form-check-label">{{ ponto.id }}</span>
                  </label>
                </td>
                <td>{{ ponto.classe }}</td>
                <td>{{ ponto.tipoambiente }}</td>
                <td>{{ ponto.uf }}</td>
                <td>{{ ponto.municipio }}</td>
                <td>{{ ponto.baciahidrografica }}</td>
                <td>{{ ponto.km_rodovia }}</td>
                <td>{{ ponto.estaca }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </template>
    <template #footer>
      <NavButton @click="saveCampanha()" type-button="success" :icon="IconDeviceFloppy"
        :title="form.id ? 'Editar' : 'Salvar'" />
    </template>
  </Modal>
</template>
