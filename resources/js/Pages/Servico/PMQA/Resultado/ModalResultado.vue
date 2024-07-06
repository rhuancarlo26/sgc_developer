<script setup>
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import Modal from "@/Components/Modal.vue";
import NavButton from "@/Components/NavButton.vue";
import Table from "@/Components/Table.vue";
import { useForm } from "@inertiajs/vue3";
import { IconTrash } from "@tabler/icons-vue";
import { IconDeviceFloppy } from "@tabler/icons-vue";
import { computed, watch } from "vue";
import { ref } from "vue";

const props = defineProps({
  contrato: { type: Object },
  servico: { type: Object },
  campanhas: { type: Array }
});

const modalResultado = ref();
let message = ref(null);

const form = useForm({
  id: null,
  servico_id: props.servico.id,
  nome: null,
  campanha: null,
  campanhas_selecionadas: []
});

watch(() => form.campanha,
  (item) => {
    let resposta = null;

    if (item) {
      resposta = form.campanhas_selecionadas.find(campanha => campanha.id === item.id) ? 'Essa campanha já existe!' : null;
    } else {
      resposta = null;
    }

    message.value = resposta;
  });

const abrirModal = (item) => {
  form.reset();

  if (item) {
    form.id = item.id;
    form.nome = item.nome;

    form.campanhas_selecionadas = item.campanhas
  }

  modalResultado.value.getBsModal().show();
}

const adicionarCampanha = () => {

  const existe = form.campanhas_selecionadas.find(campanha => campanha.id === form.campanha.id);
  if (!existe) {
    form.campanhas_selecionadas.push(form.campanha);
    form.campanha = null;
  }
}

const removerCampanha = (index) => {
  form.campanhas_selecionadas.splice(index, 1);
}

const salvarResultado = () => {
  if (form.id) {
    form.patch(route('contratos.contratada.servicos.pmqa.resultado.update', { contrato: props.contrato.id, servico: props.servico.id }), {
      onSuccess: () => modalResultado.value.getBsModal().hide()
    })
  } else {
    form.post(route('contratos.contratada.servicos.pmqa.resultado.store', { contrato: props.contrato.id, servico: props.servico.id }), {
      onSuccess: () => modalResultado.value.getBsModal().hide()
    })
  }
}

defineExpose({ abrirModal });
</script>

<template>
  <Modal ref="modalResultado" title="Novo resultado" modal-dialog-class="modal-xl">
    <template #body>
      <div class="card-body">
        <div class="row mb-4">
          <div class="col form-group">
            <InputLabel value="Nome do resultado" for="nome" />
            <input type="text" name="nome" id="nome" class="form-control" v-model="form.nome">
            <InputError :message="form.errors.justificativa" />
          </div>
          <div class="col form-group mb-4">
            <InputLabel value="Campanha" for="campanha" />
            <div class="row g-2">
              <div class="col">
                <v-select :options="campanhas" label="nome" v-model="form.campanha">
                  <template #no-options="{}">
                    Nenhum registro encontrado.
                  </template>
                </v-select>
              </div>
              <div class="col-auto">
                <NavButton @click="adicionarCampanha()" type-button="success" title="Salvar" />
              </div>
            </div>
            <InputError :message="form.errors.campanhas_selecionadas" />
            <div v-show="message">
              <small class="text-danger">
                {{ message }}
              </small>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="table-responsive">
            <table class="table card-table table-bordered">
              <thead>
                <tr>
                  <th>Nome</th>
                  <th>Ação</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="campanha, index in form.campanhas_selecionadas" :key="campanha.id">
                  <td>{{ campanha.nome }}</td>
                  <td>
                    <NavButton @click="removerCampanha(index)" :icon="IconTrash" class="btn-icon"
                      type-button="danger" />
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </template>
    <template #footer>
      <NavButton @click="salvarResultado()" type-button="success" :icon="IconDeviceFloppy"
        :title="form.id ? 'Editar' : 'Salvar'" />
    </template>
  </Modal>
</template>
