<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Modal from '@/Components/Modal.vue';
import { useForm } from '@inertiajs/vue3';
import { IconDeviceFloppy } from '@tabler/icons-vue';
import { computed } from 'vue';
import { watch } from 'vue';
import { ref } from 'vue';

const props = defineProps({
  licenca: { type: Object },
  ufs: { type: Array },
  rodovias: { type: Array }
})

const emit = defineEmits(['atualizarsegmento']);

const modalLicencaSegmento = ref();

const form = useForm({
  idlicenca_br: null,
  licenca_id: props.licenca?.id,
  rodovia: null,
  uf_inicial: null,
  uf_final: null,
  km_inicio: null,
  km_fim: null,
  extensao_br: null,
});

const reset = () => {
  form.idlicenca_br = null;
  form.licenca_id = props.licenca?.id;
  form.rodovia = null;
  form.uf_inicial = null;
  form.uf_final = null;
  form.km_inicio = null;
  form.km_fim = null;
  form.extensao_br = null;
}

const abrirModal = (item) => {
  item ? Object.assign(form, item) : reset();

  modalLicencaSegmento.value.getBsModal().show();
}

const calcExtensao = () => {
  form.extensao_br = parseFloat(form.km_fim) - parseFloat(form.km_inicio);
}

const salvarLicencaSegmento = () => {
  form.licenca_id = props.licenca?.id;

  if (form.idlicenca_br) {
    form.patch(route('licenca_segmento.update', form.idlicenca_br), {
      onSuccess: () => {
        modalLicencaSegmento.value.getBsModal().hide();
        emit('atualizarsegmento');
      }
    });
  } else {
    form.post(route('licenca_segmento.store'), {
      onSuccess: () => {
        modalLicencaSegmento.value.getBsModal().hide();
        emit('atualizarsegmento');
      }
    });
  }
}

const rodoviasUnicas = computed(() => {
  return Array.from(new Set(props.rodovias.map(r => r.rodovia)));
});

const ufsDaRodovia = computed(() => {
  if (form.rodovia) {
    const ufIdsDaRodoviaSelecionada = props.rodovias.filter(br => br.rodovia === form.rodovia).map(br => br.estados_id);

    return props.ufs.filter(uf => ufIdsDaRodoviaSelecionada.includes(uf.id));
  } else {
    return [];
  }
});

defineExpose({ abrirModal });
</script>
<template>
  <Modal ref="modalLicencaSegmento" title="" modal-dialog-class="modal-xl">
    <template #body>
      <div class="row mb-4">
        <div class="col">
          <InputLabel value="BR:" for="rodovia_id" />
          <v-select :options="rodoviasUnicas" v-model="form.rodovia">
            <template #no-options="{}">
              Nenhum registro encontrado.
            </template>
          </v-select>
          <InputError :message="form.errors.rodovia" />
        </div>

        <div class="col">
          <InputLabel value="UF Inicial:" for="uf_inicial" />
          <v-select :options="ufsDaRodovia" label="uf" v-model="form.uf_inicial">
            <template #no-options="{}">
              Nenhum registro encontrado.
            </template>
          </v-select>
          <InputError :message="form.errors.uf_inicial" />
        </div>

        <div class="col">
          <InputLabel value="UF Final:" for="uf_final" />
          <v-select :options="ufsDaRodovia" label="uf" v-model="form.uf_final">
            <template #no-options="{}">
              Nenhum registro encontrado.
            </template>
          </v-select>
          <InputError :message="form.errors.uf_final" />
        </div>
      </div>
      <div class="row mb-4">
        <div class="col">
          <InputLabel value="KM Inicial:" for="km_inicio" />
          <input @input="calcExtensao()" type="number" step="any" id="km_inicio" name="km_inicio" class="form-control"
            v-model="form.km_inicio" />
          <InputError :message="form.errors.km_inicio" />
        </div>

        <div class="col">
          <InputLabel value="KM Final:" for="km_fim" />
          <input @input="calcExtensao()" type="number" step="any" id="km_fim" name="km_fim" class="form-control"
            v-model="form.km_fim" />
          <InputError :message="form.errors.km_fim" />
        </div>

        <div class="col">
          <InputLabel value="Extensão:" for="extensao_br" />
          <input type="text" id="extensao_br" name="extensao_br" class="form-control" :value="form.extensao_br" disabled
            readonly />
          <InputError :message="form.errors.extensao_br" />
        </div>
      </div>
    </template>
    <template #footer>
      <a class="btn btn-primary" @click="salvarLicencaSegmento()" href="javascript:void(0)">
        <IconDeviceFloppy class="me-2" />
        Salvar
      </a>
    </template>
  </Modal>
</template>
