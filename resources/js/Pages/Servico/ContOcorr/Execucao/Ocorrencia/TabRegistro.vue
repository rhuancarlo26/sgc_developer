<script setup>
import { useForm } from "@inertiajs/vue3";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import NavButton from "@/Components/NavButton.vue";
import { IconDeviceFloppy, IconEye } from "@tabler/icons-vue";
import { watch } from "vue";
import { computed } from "vue";
import { Link } from "@inertiajs/vue3";
import LinkConfirmation from "@/Components/LinkConfirmation.vue";
import { IconTrash } from "@tabler/icons-vue";

const props = defineProps({
  contrato: { type: Object },
  servico: { type: Object },
  ocorrencia: { type: Object }
});

const form = useForm({
  id_ocorrencia: null,
  arquivo: null,
  ...props.ocorrencia
});

const salvarRegistro = () => {
  form.id_ocorrencia = props.ocorrencia.id;

  form.post(route('contratos.contratada.servicos.cont_ocorrencia.execucao.ocorrencia.store_registro', { contrato: props.contrato.id, servico: props.servico.id }));
}
</script>
<template>
  <div class="row mb-4">
    <div class="col">
      <InputLabel value="Fotos do equipamento" for="anexo" />
      <div class="row g-2">
        <div class="col">
          <input @input="form.arquivo = $event.target.files[0]" type="file" class="form-control">
        </div>
        <div class="col-auto">
          <a @click="salvarRegistro()" href="javascript:void(0)" class="btn btn-success" aria-label="Button">
            Salvar
          </a>
        </div>
      </div>
      <InputError :message="form.errors.arquivo" />
    </div>
  </div>
  <div class="row">
    <div class="table-responsive mb-4">
      <table class="table table-hover non-hover">
        <thead>
          <tr>
            <th>Arquivo</th>
            <th>Ação</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="registro in ocorrencia.registros" :key="registro.id">
            <td>{{ registro.nome }}</td>
            <td>
              <a target="_blank"
                :href="route('contratos.contratada.servicos.cont_ocorrencia.execucao.ocorrencia.visualizar_registro', { contrato: contrato.id, servico: servico.id, registro: registro.id })"
                class="btn btn-icon btn-primary me-1">
                <IconEye />
              </a>
              <LinkConfirmation v-slot="confirmation" :options="{ text: 'A remoção do lote será permanente.' }">
                <Link :onBefore="confirmation.show"
                  :href="route('contratos.contratada.servicos.cont_ocorrencia.execucao.ocorrencia.delete_registro', { contrato: contrato.id, servico: servico.id, registro: registro.id })"
                  as="button" method="delete" type="button" class="btn btn-icon btn-danger">
                <IconTrash />
                </Link>
              </LinkConfirmation>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>