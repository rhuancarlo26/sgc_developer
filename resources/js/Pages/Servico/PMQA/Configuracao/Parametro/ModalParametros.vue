<script setup>
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import Modal from "@/Components/Modal.vue";
import NavButton from "@/Components/NavButton.vue";
import { useForm } from "@inertiajs/vue3";
import { IconDeviceFloppy } from "@tabler/icons-vue";
import { watch } from "vue";
import { ref } from "vue";
import DivTabelaParametro from "./DivTabelaParametro.vue";
import DivTabelaMedirIqa from "./DivTabelaMedirIqa.vue";
import { computed } from "vue";

const props = defineProps({
  contrato: { type: Object },
  servico: { type: Object },
  parametros: { type: Array }
});

const modalParametros = ref();

const form = useForm({
  id: null,
  contrato_id: props.contrato.id,
  servico_id: props.servico.id,
  nome: null,
  medir_iqa: false,
  parametros: []
});

watch(
  () => form.medir_iqa,
  (medir) => {
    const parametrosIqa = [3, 5, 7, 8, 9, 10, 12, 20, 21];

    if (medir) {
      parametrosIqa.forEach(numero => {
        if (!form.parametros.includes(numero)) {
          form.parametros.push(numero);
        }
      });
    } else {
      let numeroFiltrado = parametrosIqa.filter(numero => form.parametros.includes(numero));

      numeroFiltrado.forEach(numero => {
        form.parametros.splice(form.parametros.indexOf(numero), 1);
      });
    }
  }
);

const isChecked = (parametro_id) => {
  const semDetalhes = [0, 1, 2, 3, 4, 5, 6, 7];
  if (semDetalhes.includes(parametro_id)) {
    return false
  } else {
    return form.parametros.includes(parametro_id)
  }
}

const abrirModal = () => {
  modalParametros.value.getBsModal().show();
}

const saveParametros = () => {
  form.post(route('contratos.contratada.servicos.pmqa.configuracao.parametro.store', { contrato: props.contrato.id, servico: props.servico.id }), {
    onSuccess: () => modalParametros.value.getBsModal.hide()
  })
}

defineExpose({ abrirModal });
</script>

<template>
  <Modal ref="modalParametros" title="Parâmetros" modal-dialog-class="modal-xl">
    <template #body>
      <div class="card-body">
        <div class="col form-group mb-4">
          <InputLabel value="Nome da lista" for="nome" />
          <input type="text" class="form-control" v-model="form.nome">
          <InputError :message="form.errors.nome" />
        </div>
        <div class="accordion" id="accordion-example">
          <div class="accordion-item">
            <h2 class="accordion-header" id="heading-4">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                data-bs-target="#collapse-4" aria-expanded="false">
                <span class="me-2">Lista Parâmetros</span>
                <InputError :message="form.errors.parametros" />
              </button>
            </h2>
            <div id="collapse-4" class="accordion-collapse collapse" data-bs-parent="#accordion-example">
              <div class="accordion-body pt-0">
                <div>
                  <div class="form-selectgroup form-selectgroup-boxes d-flex flex-column">
                    <label class="form-selectgroup-item flex-fill">
                      <input type="checkbox" id="parametroIqa" name="parametroIqa" v-model="form.medir_iqa"
                        class="form-selectgroup-input">
                      <div class="form-selectgroup-label d-flex align-items-center p-3">
                        <div class="me-3">
                          <span class="form-selectgroup-check"></span>
                        </div>
                        <div class="form-selectgroup-label-content d-flex align-items-center">
                          <div>
                            <div class="font-weight-medium">Indice de qualidade das aguas (IQA)</div>
                            <div>
                              <DivTabelaMedirIqa v-show="form.medir_iqa" />
                            </div>
                          </div>
                        </div>
                      </div>
                    </label>
                    <label v-for="parametro in parametros" :key="parametro.id" class="form-selectgroup-item flex-fill">
                      <input type="checkbox" :id="'parametro-' + parametro.id" :name="'parametro-' + parametro.id"
                        :value="parametro.id" v-model="form.parametros" class="form-selectgroup-input">
                      <div class="form-selectgroup-label d-flex align-items-center p-3">
                        <div class="me-3">
                          <span class="form-selectgroup-check"></span>
                        </div>
                        <div class="form-selectgroup-label-content d-flex align-items-center">
                          <div>
                            <div class="font-weight-medium">{{ parametro.nome }} {{ parametro.unidade ? `-
                              ${parametro.unidade}` : null }}</div>
                            <div>
                              <DivTabelaParametro v-show="isChecked(parametro.id)" :parametro="parametro" />
                            </div>
                          </div>
                        </div>
                      </div>
                    </label>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </template>
    <template #footer>
      <NavButton @click="saveParametros()" type-button="success" :icon="IconDeviceFloppy"
        :title="form.id ? 'Alterar' : 'Salvar'" />
    </template>
  </Modal>
</template>
