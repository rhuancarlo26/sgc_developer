<script setup>
import ModalVisualizar from "@/Pages/Licenca/ModalVisualizar.vue";
import { dateTimeFormat } from "@/Utils/DateTimeUtils";
import { router, useForm } from "@inertiajs/vue3";
import InputError from "@/Components/InputError.vue";
import { IconCar, IconDots, IconPencil, IconShip, IconTrain, IconTrash } from "@tabler/icons-vue";
import { ref } from "vue";

const refModalVisualizar = ref(null);

const props = defineProps({
  contrato: Object,
  numero_licencas: Array
});

const form = useForm({
  contrato_id: props.contrato.id,
  licenca: null
});

const form_observacao = useForm({
  id: null,
  contrato_id: props.contrato.id,
  observacao: null
});

const salvarLicenca = () => {
  form.post(route('contratos.contratada.store_licenciamento'), {
    onSuccess: () => {
      form.reset();
    }
  });
}

const excluirLicenciamento = (licenca_id) => {
  router.post(route('contratos.contratada.delete_licenciamento', licenca_id), { contrato_id: props.contrato.id })
}

const salvarObservacao = () => {
  if (form_observacao.id) {
    form_observacao.patch(route('contratos.contratada.update_licenciamento_observacao', form_observacao.id), {
      onSuccess: () => {
        limparObservacao();
      }
    });

  } else {
    form_observacao.post(route('contratos.contratada.store_licenciamento_observacao'), {
      onSuccess: () => {
        limparObservacao();
      }
    });
  }
}

const excluirObservacao = (observacao_id) => {
  router.delete(route('contratos.contratada.delete_licenciamento_observacao', observacao_id));
}

const editarObservacao = (observacao) => {
  Object.assign(form_observacao, observacao);
}

const limparObservacao = () => {
  form_observacao.reset();
}

const abrirModalVisualizar = (item) => {
  refModalVisualizar.value.abrirModal(item);
}

const dtAlerta = (data) => {
  if (data) {
    const data_licenca = new Date(data);
    const hoje = new Date();
    const dia = 1000 * 60 * 60 * 24;

    const resultado = (data_licenca - hoje) / dia;

    return Math.round(resultado);
  }
}

</script>

<template>
  <h4>Licenciamento</h4>
  <div class="mb-4">
    <div class="row g-2">
      <div class="col">
        <v-select :options="numero_licencas" label="numero_licenca" v-model="form.licenca">
          <template #no-options="{}">
            Nenhum registro encontrado.
          </template>
        </v-select>
      </div>
      <div class="col-auto">
        <a @click="salvarLicenca()" href="javascript:void(0)" class="btn btn-success" aria-label="Button">
          Salvar
        </a>
      </div>
    </div>
  </div>

  <div class="table-responsive mb-4">
    <table class="table table-hover non-hover">
      <thead>
        <tr>
          <th>Modal</th>
          <th>Tipo</th>
          <th>Nº Licença</th>
          <th>Empreendimento</th>
          <th>Emissor</th>
          <th>Data da Emissão</th>
          <th>Status</th>
          <th>Vencimento</th>
          <th>Processo DNIT</th>
          <th>Açao</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="licenca in contrato.licenciamentos " :key="licenca.id">
          <td>
            <IconCar v-if="licenca.modal == 1" />
            <IconShip v-if="licenca.modal == 2" />
            <IconTrain v-if="licenca.modal == 3" />
          </td>
          <td>
            {{ licenca.tipo?.sigla }} - {{ licenca.tipo?.nome }}
          </td>
          <td>
            {{ licenca.numero_licenca }}
          </td>
          <td>
            {{ licenca.empreendimento }}
          </td>
          <td>
            {{ licenca.emissor }}
          </td>
          <td>
            {{ dateTimeFormat(licenca.data_emissao) }}
          </td>
          <td>
            <a href="javascript:void(0)">
              <span v-if="licenca.requerimentos.length" class="badge text-bg-primary">
                Em Análise
              </span>
              <span v-else-if="dtAlerta(licenca.vencimento) <= 0" class="badge text-bg-danger">
                Vencida
              </span>
              <span v-else class="badge text-bg-success">
                Vigente
              </span>
            </a>
          </td>
          <td>
            <a href="javascript:void(0)">
              <span v-if="dtAlerta(licenca.vencimento) <= 0" class="badge text-bg-danger">
                {{ dateTimeFormat(licenca.vencimento) }}
              </span>
              <span v-else class="badge text-bg-success">
                {{ dateTimeFormat(licenca.vencimento) }}
              </span>
            </a>
          </td>
          <td>
            {{ licenca.processo_dnit }}
          </td>
          <td @click.stop>
            <span class="dropdown">
              <button class="btn dropdown-toggle align-text-top" data-bs-boundary="viewport" data-bs-toggle="dropdown"
                aria-expanded="false">
                <IconDots />
              </button>
              <div class="dropdown-menu dropdown-menu-end" style="">
                <a @click="abrirModalVisualizar(licenca)" class="dropdown-item" href="javascript:void(0)">
                  Visualizar
                </a>
                <a v-if="licenca.documento?.id" class="dropdown-item" target="_blank"
                  :href="route('licenca.documento.visualizar', licenca.documento.id)">
                  Visualizar PDF
                </a>
                <a @click="excluirLicenciamento(licenca.id)" class="dropdown-item" href="javascript:void(0)">
                  Excluir
                </a>
              </div>
            </span>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
  <div class="mb-4">
    <h4>Observação</h4>
    <div class="form-group mb-4">
      <textarea v-model="form_observacao.observacao" class="form-control" rows="5"></textarea>
      <InputError :message="form_observacao.errors.observacao" />
    </div>
    <div class="text-end">
      <button v-if="form_observacao.id" @click="limparObservacao()" type="button"
        class="btn btn-danger me-2">Limpar</button>
      <button @click="salvarObservacao()" type="button" class="btn btn-success">Salvar</button>
    </div>
  </div>
  <div class="table-responsive mb-4">
    <table class="table table-hover non-hover">
      <thead>
        <tr>
          <th>Observação</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="observacao in contrato.licenciamento_observacoes" :key="observacao.id">
          <td>{{ observacao.observacao }}</td>
          <td>
            <button @click="excluirObservacao(observacao.id)" type="button" class="btn btn-danger me-2">
              <IconTrash />
            </button>
            <button @click="editarObservacao(observacao)" type="button" class="btn btn-primary">
              <IconPencil />
            </button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>

  <ModalVisualizar ref="refModalVisualizar" />
</template>