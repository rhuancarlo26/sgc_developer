<script setup>
const props = defineProps({
  servico: { type: Object }
});
</script>
<template>
  <div class="d-flex flex-column justify-content-between min-vh-100">
    <div class="mb-4">
      <h4>Recursos Humanos</h4>
      <div class="card">
        <div class="table-responsive">
          <table class="table card-table table-bordered">
            <thead>
              <tr>
                <th class="text-center">Nome do profissional</th>
                <th class="text-center">Profissão</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="rh in servico.rhs" :key="rh.id">
                <td class="text-center">{{ rh.nome }}</td>
                <td class="text-center">{{ rh.formacao }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <div class="mb-4">
      <h4>Veículos</h4>
      <div class="card">
        <div class="table-responsive">
          <table class="table card-table table-bordered">
            <thead>
              <tr>
                <th class="text-center">Nome do veículo</th>
                <th class="text-center">Modelo</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="veiculo in servico.veiculos" :key="veiculo.id">
                <td class="text-center">{{ veiculo.codigo.descricao }}</td>
                <td class="text-center">{{ veiculo.codigo.codigo }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <div class="mb-4">
      <h4>Equipamentos</h4>
      <div class="card">
        <div class="table-responsive">
          <table class="table card-table table-bordered">
            <thead>
              <tr>
                <th class="text-center">Nome do equipamento</th>
                <th class="text-center">Modelo</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="equipamento in servico.equipamentos" :key="equipamento.id">
                <td class="text-center">{{ equipamento.nome }}</td>
                <td class="text-center">{{ equipamento.numero_serie }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <div class="mb-4">
      <h4>Licencas</h4>
      <div class="card">
        <div class="table-responsive">
          <table class="table card-table table-bordered">
            <thead>
              <tr>
                <th class="text-center">Número da licenca</th>
                <th class="text-center">Condicionante</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="licenca_condicionante in servico.licencas_condicionantes" :key="licenca_condicionante.id">
                <td class="text-center">{{ licenca_condicionante.licenca.numero_licenca }}</td>
                <td class="text-center">{{ licenca_condicionante.condicionante.numero_condicionante }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>