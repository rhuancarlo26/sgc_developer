<script setup>
import InputLabel from '@/Components/InputLabel.vue';
import { router, useForm } from '@inertiajs/vue3';
import { IconDots } from '@tabler/icons-vue';

const props = defineProps({
  servico: Object,
  rhs: Array,
  veiculos: Array,
  equipamentos: Array
});

const form = useForm({
  contrato_id: props.servico.contrato_id,
  servico_id: props.servico.id,
  rh: null,
  veiculo: null,
  equipamento: null
});

const salvarRh = () => {
  form.post(route('contratos.contratada.servicos.rh.store'));
}

const excluirRh = (rh_id) => {
  router.post(route('contratos.contratada.servicos.rh.delete', rh_id), {
    contrato_id: props.servico.contrato_id,
    servico_id: props.servico.id
  })
}

const salvarVeiculo = () => {
  form.post(route('contratos.contratada.servicos.veiculo.store'));
}

const excluirVeiculo = (veiculo_id) => {
  router.post(route('contratos.contratada.servicos.veiculo.delete', veiculo_id), {
    contrato_id: props.servico.contrato_id,
    servico_id: props.servico.id
  })
}

const salvarEquipamento = () => {
  form.post(route('contratos.contratada.servicos.equipamento.store'));
}

const excluirEquipamento = (equipamento_id) => {
  router.post(route('contratos.contratada.servicos.equipamento.delete', equipamento_id), {
    contrato_id: props.servico.contrato_id,
    servico_id: props.servico.id
  })
}

</script>
<template>
  <div class="mb-4">
    <div class="mb-4">
      <InputLabel value="Recursos humanos disponiveis" for="rh" />
      <form @submit.prevent="salvarRh()">
        <div class="row g-2">
          <div class="col">
            <v-select :options="rhs" label="nome" v-model="form.rh">
              <template #no-options="{}">
                Nenhum registro encontrado.
              </template>
            </v-select>
          </div>
          <div class="col-auto">
            <button type="submit" class="btn btn-success" :disabled="form.processing">Vincular</button>
          </div>
        </div>
      </form>
    </div>

    <div class="table-responsive mb-4">
      <table class="table card-table table-bordered table-hover">
        <thead>
          <tr>
            <th>Nome do recurso</th>
            <th>Descrição</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="rh in servico.rhs" :key="rh.id" class="cursor-pointer">
            <td>{{ rh.nome }}</td>
            <td>{{ rh.formacao }}</td>
            <td>
              <button type="button" class="btn btn-icon btn-info dropdown-toggle p-2" data-bs-boundary="viewport"
                data-bs-toggle="dropdown" aria-expanded="false">
                <IconDots />
              </button>
              <div class="dropdown-menu dropdown-menu-end">
                <a @click="excluirRh(rh.id)" class="dropdown-item" href="javascript:void(0)">
                  Excluir
                </a>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

  <div class="mb-4">
    <div class="mb-4">
      <InputLabel value="Veículos disponíveis" for="veiculos" />
      <form @submit.prevent="salvarVeiculo()">
        <div class="row g-2">
          <div class="col">
            <v-select :options="[...veiculos.map(c => { return { ...c, label: `${c.codigo.codigo}` } })]"
              v-model="form.veiculo">
              <template #no-options="{}">
                Nenhum registro encontrado.
              </template>
            </v-select>
          </div>
          <div class="col-auto">
            <button type="submit" class="btn btn-success" :disabled="form.processing">Vincular</button>
          </div>
        </div>
      </form>
    </div>

    <div class="table-responsive mb-4">
      <table class="table card-table table-bordered table-hover">
        <thead>
          <tr>
            <th>Nome do recurso</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="veiculo in servico.veiculos" :key="veiculo.id" class="cursor-pointer">
            <td>{{ veiculo.codigo?.codigo }}</td>
            <td>
              <button type="button" class="btn btn-icon btn-info dropdown-toggle p-2" data-bs-boundary="viewport"
                data-bs-toggle="dropdown" aria-expanded="false">
                <IconDots />
              </button>
              <div class="dropdown-menu dropdown-menu-end">
                <a @click="excluirVeiculo(veiculo.id)" class="dropdown-item" href="javascript:void(0)">
                  Excluir
                </a>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

  <div class="mb-4">
    <div class="mb-4">
      <form @submit.prevent="salvarEquipamento()">
        <InputLabel value="Equipamentos disponíveis" for="rh" />
        <div class="row g-2">
          <div class="col">
            <v-select :options="equipamentos" label="nome" v-model="form.equipamento">
              <template #no-options="{}">
                Nenhum registro encontrado.
              </template>
            </v-select>
          </div>
          <div class="col-auto">
            <button type="submit" class="btn btn-success" :disabled="form.processing">Vincular</button>
          </div>
        </div>
      </form>
    </div>

    <div class="table-responsive mb-4">
      <table class="table card-table table-bordered table-hover">
        <thead>
          <tr>
            <th>Nome do recurso</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="equipamento in servico.equipamentos" :key="equipamento.id" class="cursor-pointer">
            <td>{{ equipamento.nome }}</td>
            <td>
              <button type="button" class="btn btn-icon btn-info dropdown-toggle p-2" data-bs-boundary="viewport"
                data-bs-toggle="dropdown" aria-expanded="false">
                <IconDots />
              </button>
              <div class="dropdown-menu dropdown-menu-end">
                <a @click="excluirEquipamento(equipamento.id)" class="dropdown-item" href="javascript:void(0)">
                  Excluir
                </a>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>