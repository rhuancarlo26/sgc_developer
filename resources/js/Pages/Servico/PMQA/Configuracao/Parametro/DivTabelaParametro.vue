<script setup>

const props = defineProps({
  parametro: { type: Object }
});

const isTabelaSimples = () => {
  const parametroSimples = [7, 8, 9, 10, 13, 14, 15, 16, 17, 18];

  if (parametroSimples.includes(props.parametro.id)) {
    return true;
  }

  return false;
}

</script>
<template>
  <div v-if="isTabelaSimples()">
    <div class="table-responsive">
      <table class="table table-hover non-hover">
        <thead>
          <tr>
            <th>Classe 1</th>
            <th>Classe 2</th>
            <th>Classe 3</th>
            <th>Classe 4</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>{{ parametro.classe_1 }}</td>
            <td>{{ parametro.classe_2 }}</td>
            <td>{{ parametro.classe_3 }}</td>
            <td>{{ parametro.classe_4 }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
  <div v-else-if="parametro.id === 19">
    <div class="table-responsive">
      <table class="table table-hover non-hover">
        <thead>
          <tr class="text-center bg-light">
            <th>pH</th>
            <th>Classe 1</th>
            <th>Classe 2</th>
            <th>Classe 3</th>
            <th>Classe 4</th>
          </tr>
        </thead>
        <tbody>
          <tr class="text-center">
            <td> {{ 'pH ≤ 7,5' }} </td>
            <td> {{ '≤ 3,7' }} </td>
            <td> {{ '≤ 3,7' }} </td>
            <td> {{ '≤ 13,3' }} </td>
            <td class="text-danger">Limite não especificado</td>
          </tr>
          <tr class="text-center">
            <td> {{ '7,5 pH ≤8,0' }} </td>
            <td> {{ '≤ 2,0' }} </td>
            <td> {{ '≤ 2,0' }} </td>
            <td> {{ '≤ 5,6' }} </td>
            <td class="text-danger">Limite não especificado</td>
          </tr>
          <tr class="text-center">
            <td> {{ '8,0 pH ≤8,5' }} </td>
            <td> {{ '≤ 1,0' }} </td>
            <td> {{ '≤ 1,0' }} </td>
            <td> {{ '≤ 2,0' }} </td>
            <td class="text-danger">Limite não especificado</td>
          </tr>
          <tr class="text-center">
            <td> {{ 'pH ≥ 8,5' }}</td>
            <td> {{ '≤ 0,5' }} </td>
            <td> {{ '≤ 0,5' }} </td>
            <td> {{ '≤ 1,0' }} </td>
            <td class="text-danger">Limite não especificado</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
  <div v-else-if="parametro.id === 20">
    <div class="table-responsive">
      <table class="table table-hover non-hover">
        <thead>
          <tr class="text-center">
            <th>Ambiente Lêntico</th>
            <th>Ambiente Lótico</th>
          </tr>
        </thead>
        <tbody>
          <tr class="text-center">
            <td> {{ '≤ 0,020' }} </td>
            <td> {{ '≤ 0,1' }} </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
  <div v-else-if="parametro.id === 21">
    <div class="table-responsive">
      <table class="table table-hover non-hover">
        <thead>
          <tr class="text-center">
            <th>Classe 1</th>
            <th colspan="2">Classe 2</th>
            <th colspan="2">Classe 3</th>
            <th colspan="2">Classe 4</th>
          </tr>
        </thead>
        <tbody>
          <tr class="text-center">
            <td rowspan="2" class="text-danger">Limite não especificado</td>
            <td class="px-3">Limite <span style="display: block;">Inferior</span></td>
            <td class="px-3">Limite <span style="display: block;">Superior</span></td>
            <td class="px-3">Limite <span style="display: block;">Inferior</span></td>
            <td class="px-3">Limite <span style="display: block;">Superior</span></td>
            <td class="px-3">Limite <span style="display: block;">Inferior</span></td>
            <td class="px-3">Limite <span style="display: block;">Superior</span></td>
          </tr>
          <tr class="text-center">
            <td> {{ '≥ 6' }} </td>
            <td> {{ '≤ 9' }} </td>
            <td> {{ '≥ 6' }} </td>
            <td> {{ '≤ 9' }} </td>
            <td> {{ '≥ 6' }} </td>
            <td> {{ '≤ 9' }} </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>