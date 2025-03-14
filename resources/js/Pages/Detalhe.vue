<script setup>

import NavButton from "@/Components/NavButton.vue";
import { onMounted, ref } from "vue";
import { computed } from "vue";

const propsData = ref({});
const servicos = ref([]);
const webRoute = {
  1: 'dashboard.pmqa',
  2: 'dashboard.afugentamentoFauna',
  3: 'dashboard.mon-atp-fauna',
  4: 'dashboard.monitora-fauna',
  5: 'dashboard.passagem-fauna',
  6: 'dashboard.supressaoVegetal',
  7: 'dashboard.supervisaoAmbiental'
};

defineExpose({ servicos, propsData })


onMounted(() => {
  document.getElementById('offcanvasDetalhes').addEventListener('hidden.bs.offcanvas', function () {
    propsData.value = null;
    servicos.value = null;
  })
})

</script>

<template>
  <div class="offcanvas offcanvas-start" style="min-width: 36vw" tabindex="-1" id="offcanvasDetalhes"
    aria-labelledby="offcanvasDetalhesLabel">
    <div class="offcanvas-header p-3">
      <h2 class="offcanvas-title" id="offcanvasDetalhesLabel">Detalhes</h2>
      <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body p-3">
      <div class="accordion" id="accordionDetalhes">

        <div class="accordion-item" v-if="propsData.id">
          <h2 class="accordion-header" id="headingServicos">
            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseServicos"
              aria-expanded="true" aria-controls="collapseServicos">
              Serviços
            </button>
          </h2>
          <div id="collapseServicos" class="accordion-collapse collapse show" aria-labelledby="headingServicos"
            data-bs-parent="#accordionDetalhes">
            <div class="accordion-body">
              <table class="table table-bordered table-striped two-col-table">
                <thead>
                  <tr>
                    <th class="text-info text-center" colspan="3"> {{
                      propsData.contratada
                    }}
                    </th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="servico in servicos" :key="servico.id">
                    <td>Serviço:</td>
                    <td>{{ servico.especificacao }}</td>
                    <td v-if="webRoute[servico.servico]">
                      <a :href="route(webRoute[servico.servico], { servico: servico.id })">
                        <NavButton type-button="success" title="Dashboard" />
                      </a>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="offcanvas-footer p-2 text-end border-top">
      <button class="btn btn-secondary px-2 py-1" type="button" data-bs-dismiss="offcanvas">
        Fechar
      </button>
    </div>
  </div>
</template>

<style scoped>
table tr th,
table tr td {
  text-wrap: wrap;
}

th {
  font-weight: bold;
}

.two-col-table tr td:first-child {
  font-weight: bold;
}

.accordion-body {
  padding: .2em 1em;
}

.accordion-button:disabled {
  background-color: var(--tblr-gray-200);
  cursor: not-allowed;
}

.accordion-button:disabled:after {
  content: none;
}
</style>
