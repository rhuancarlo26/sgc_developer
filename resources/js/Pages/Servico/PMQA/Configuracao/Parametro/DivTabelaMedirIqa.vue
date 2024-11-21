<script setup>
</script>
<template>
  <div class="table-responsive">
    <table class="table table-hover non-hover">
      <thead>
        <tr class="text-center">
          <th colspan="3">CLASSIFICAÇÃO DO IQA</th>
        </tr>
      </thead>
      <tbody>
        <tr class="text-center">
          <td>
            <div class="bg-blue color-iqa"></div>
          </td>
          <td> Ótima </td>
          <td> {{ '79 < IQA ≤ 100' }}</td>
        </tr>
        <tr class="text-center">
          <td>
            <div class="bg-success color-iqa"></div>
          </td>
          <td> Boa </td>
          <td> {{ '51 < IQA ≤ 79' }} </td>
        </tr>
        <tr class="text-center">
          <td>
            <div class="bg-warning color-iqa"></div>
          </td>
          <td> Média </td>
          <td> {{ '36 < IQA ≤ 51' }} </td>
        </tr>
        <tr class="text-center">
          <td>
            <div class="bg-danger color-iqa"></div>
          </td>
          <td> Ruim </td>
          <td> {{ '19 < IQA ≤ 36' }} </td>
        </tr>
        <tr class="text-center">
          <td>
            <div class="bg-secondary color-iqa"></div>
          </td>
          <td> Péssimo </td>
          <td> {{ 'IQA ≤ 19' }} </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<style>
.color-iqa {
  width: 40px;
  height: 20px;
  margin: auto;
}
</style>