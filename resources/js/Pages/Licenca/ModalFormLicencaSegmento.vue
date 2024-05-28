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

let form = useForm({
  id: null,
  licenca_id: props.licenca.id,
  rodovia: null,
  uf_inicial: null,
  uf_final: null,
  km_inicial: null,
  km_final: null,
  extensao: null,
});

const abrirModal = (item) => {
  item ? Object.assign(form, item) : form.reset();

  modalLicencaSegmento.value.getBsModal().show();
}

const calcExtensao = () => {
  form.extensao = parseFloat(form.km_final) - parseFloat(form.km_inicial);
}

const salvarLicencaSegmento = () => {
  if (form.id) {
    form.patch(route('licenca_segmento.update', form.id), {
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
    const ufIdsDaRodoviaSelecionada = props.rodovias.filter(br => br.rodovia === form.rodovia).map(br => br.uf_id);

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
          <InputLabel value="KM Inicial:" for="km_inicial" />
          <input @input="calcExtensao()" type="number" step="any" id="km_inicial" name="km_inicial" class="form-control"
            v-model="form.km_inicial" />
          <InputError :message="form.errors.km_inicial" />
        </div>

        <div class="col">
          <InputLabel value="KM Final:" for="km_final" />
          <input @input="calcExtensao()" type="number" step="any" id="km_final" name="km_final" class="form-control"
            v-model="form.km_final" />
          <InputError :message="form.errors.km_final" />
        </div>

        <div class="col">
          <InputLabel value="Extensão:" for="extensao" />
          <input type="text" id="extensao" name="extensao" class="form-control" :value="form.extensao" disabled
            readonly />
          <InputError :message="form.errors.extensao" />
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