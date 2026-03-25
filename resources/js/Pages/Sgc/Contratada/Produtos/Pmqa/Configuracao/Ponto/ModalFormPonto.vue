<script setup>
import Modal from "@/Components/Modal.vue";
import { ref, computed, watch } from "vue";
import { useForm } from "@inertiajs/vue3";
import NavButton from "@/Components/NavButton.vue";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import { IconDeviceFloppy, IconX } from "@tabler/icons-vue";

const props = defineProps({
  contrato: { type: Object },
  produto: { type: Object },
  pmqa: { type: Object },
});

const emit = defineEmits(["saved"]);

const modalRef = ref(null);
const modo = ref("create");
const pontoAtual = ref(null);

const form = useForm({
  id: null,
  nome_ponto_coleta: null,
  lat_x: null,
  long_y: null,
  classificacao: null,
  classe: null,
  tipo_ambiente: null,
  uf: null,
  municipio: null,
  bacia_hidrografica: null,
  km_rodovia: null,
  estaca: null,
  observacoes: null,
});

const titulo = computed(() =>
  modo.value === "edit" ? `Editar ponto #${form.id}` : "Cadastrar ponto"
);

const abrirModal = (item = null) => {
  modo.value = item?.id ? "edit" : "create";
  pontoAtual.value = item ?? null;

  form.clearErrors();

  form.defaults({
    id: null,
    nome_ponto_coleta: null,
    lat_x: null,
    long_y: null,
    classificacao: null,
    classe: null,
    tipo_ambiente: null,
    uf: null,
    municipio: null,
    bacia_hidrografica: null,
    km_rodovia: null,
    estaca: null,
    observacoes: null,
    ...(item ?? {}),
  });

  form.reset();

  modalRef.value?.getBsModal().show();
};


const fecharModal = () => {
  modalRef.value?.getBsModal().hide();
};

const salvar = () => {
  const params = {
    contrato: props.contrato.id,
    produto: props.produto.slug,
    pmqa: props.pmqa.id,
    ponto: form.id,
  };
  console.log(params)
  if (modo.value === "edit") {
    form.patch(
      route("contratos.contratada.sgc.pmqa.configuracao.ponto.update", params),
      {
        preserveScroll: true,
        onSuccess: () => {
          emit("saved");
          fecharModal();
        },
        onError: (errors) => {
          console.error("Erros de validação:", errors);
          // O form já vai mostrar os erros automaticamente via form.errors
        },
      }
    );
    return;
  }

  form.post(
    route("contratos.contratada.sgc.pmqa.configuracao.ponto.store", {
      contrato: Number(props.contratoId),
      produto: props.produtoSlug,
      pmqa: props.pmqa,
    }),
    {
      preserveScroll: true,
      onSuccess: () => {
        emit("saved");
        fecharModal();
      },
    }
  );
};

defineExpose({ abrirModal });
</script>

<template>
  <Modal ref="modalRef" :title="titulo" modal-dialog-class="modal-xl">
    <template #body>
      <form @submit.prevent="salvar">
        <div class="row mb-4">
          <div class="col form-group">
            <InputLabel value="Nome" for="nome_ponto_coleta" />
            <input type="text" class="form-control" v-model="form.nome_ponto_coleta" />
            <InputError :message="form.errors.nome_ponto_coleta" />
          </div>
          <div class="col form-group">
            <InputLabel value="Latitude" for="lat_x" />
            <input type="number" step="any" class="form-control" v-model="form.lat_x" />
            <InputError :message="form.errors.lat_x" />
          </div>
          <div class="col form-group">
            <InputLabel value="Longitude" for="long_y" />
            <input type="number" step="any" class="form-control" v-model="form.long_y" />
            <InputError :message="form.errors.long_y" />
          </div>
        </div>

        <div class="row mb-4">
          <div class="col form-group">
            <InputLabel value="Classificação" for="classificacao" />
            <input type="text" class="form-control" v-model="form.classificacao" />
            <InputError :message="form.errors.classificacao" />
          </div>
          <div class="col form-group">
            <InputLabel value="Classe" for="classe" />
            <input type="number" class="form-control" v-model="form.classe" />
            <InputError :message="form.errors.classe" />
          </div>
          <div class="col form-group">
            <InputLabel value="Tipo de ambiente" for="tipo_ambiente" />
            <input type="text" class="form-control" v-model="form.tipo_ambiente" />
            <InputError :message="form.errors.tipo_ambiente" />
          </div>
        </div>

        <div class="row mb-4">
          <div class="col form-group">
            <InputLabel value="uf" for="uf" />
            <input type="text" class="form-control" v-model="form.uf" />
            <InputError :message="form.errors.uf" />
          </div>
          <div class="col form-group">
            <InputLabel value="Municipio" for="municipio" />
            <input type="text" class="form-control" v-model="form.municipio" />
            <InputError :message="form.errors.municipio" />
          </div>
          <div class="col form-group">
            <InputLabel value="Bacia hidrografica" for="bacia_hidrografica" />
            <input type="text" class="form-control" v-model="form.bacia_hidrografica" />
            <InputError :message="form.errors.bacia_hidrografica" />
          </div>
        </div>

        <div class="row mb-4">
          <div class="col form-group">
            <InputLabel value="Km rodovia" for="km_rodovia" />
            <input type="number" class="form-control" v-model="form.km_rodovia" />
            <InputError :message="form.errors.km_rodovia" />
          </div>
          <div class="col form-group">
            <InputLabel value="Estaca" for="estaca" />
            <input type="number" class="form-control" v-model="form.estaca" />
            <InputError :message="form.errors.estaca" />
          </div>
          <div class="col form-group">
            <InputLabel value="Observações" for="observacoes" />
            <input type="text" class="form-control" v-model="form.observacoes" />
            <InputError :message="form.errors.observacoes" />
          </div>
        </div>

        <div class="row mb-2">
          <div class="col form-group">
            <InputLabel value="Observações (detalhado)" for="observacoes_texto" />
            <textarea id="observacoes_texto" rows="4" class="form-control" v-model="form.observacoes"></textarea>
          </div>
        </div>
      </form>
    </template>

    <template #footer>
      <div class="d-flex justify-content-end gap-2">
        <NavButton type="button" type-button="secondary" title="Cancelar" :icon="IconX" @click="fecharModal" />
        <NavButton type="button" type-button="success" title="Salvar" :icon="IconDeviceFloppy" :disabled="form.processing" @click="salvar" />
      </div>
    </template>
  </Modal>
</template>
