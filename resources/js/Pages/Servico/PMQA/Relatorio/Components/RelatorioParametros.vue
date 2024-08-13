<script setup>
import { computed } from "vue";

const props = defineProps({
  servico: { type: Object },
  relatorio: { type: Object }
});

const parametrosVinculados = computed(() => {
  return props.relatorio.resultado?.campanhas.map(campanha => campanha.pontos.map(ponto => ponto.lista.parametros)).flat(2).reduce((acc, curr) => {
    if (!acc.some(item => item.id === curr.id)) {
      acc.push(curr);
    }

    return acc;
  }, []);
})
</script>
<template>
  <div class="overflow-auto">
    <div>
      <h4>Parâmetros</h4>
      <div class="card">
        <div class="table-responsive">
          <table class="table card-table table-bordered">
            <thead>
              <tr>
                <th rowspan="3">Parâmetro</th>
                <th rowspan="3">Unidade</th>
                <th colspan="4">Limites - resoluções CONAMA N° 357/2005</th>
              </tr>
              <tr>
                <th colspan="4">Água doce</th>
              </tr>
              <tr>
                <th>Classe 1</th>
                <th>Classe 2</th>
                <th>Classe 3</th>
                <th>Classe 4</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="parametro in parametrosVinculados" :key="parametro.id">
                <td>{{ parametro.nome }}</td>
                <td>{{ parametro.unidade }}</td>
                <td>{{ parametro.classe_1 }}</td>
                <td>{{ parametro.classe_2 }}</td>
                <td>{{ parametro.classe_3 }}</td>
                <td>{{ parametro.classe_4 }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <div class="footer">
      <div class="indice"> * V.A. = Virtualmente Ausente</div>
      <div class="indice">
        ** Limite é binário, ou seja, presença ou não de óleos e graxas.
        Apesar disso, na etapa de execução, a contratada deverá inserir o
        valor medido pelo laboratório. Podemos considerar sem limite
        definido.
      </div>
      <div class="indice"> *** São 2 limites: superior e inferior</div>
      <div class="indice"> **** O limite é condicionado ao "TipoAmbiente": Lótico ou Lêntico.</div>
      <div class="indice"> ***** Definidos de acordo com o pH da amotra.</div>
    </div>
    <div class="card">
      <div class="table-responsive">
        <table class="table card-table table-bordered">
          <thead>
            <tr>
              <th class="thead_relatorio" colspan="2"><span>PARÂMETROS DO IQA (CETESB/SP)</span></th>
              <th class="thead_relatorio"> PESO (w)</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td class="tbody_relatorio">1</td>
              <td class="tbody_relatorio">OXIGÊNIO DISSOLVIDO - OD</td>
              <td class="tbody_relatorio">0,17</td>
            </tr>
            <tr>
              <td class="tbody_relatorio">2</td>
              <td class="tbody_relatorio">COLIFORMES TERMOTOLERANTES</td>
              <td class="tbody_relatorio">0,15</td>
            </tr>
            <tr>
              <td class="tbody_relatorio">3</td>
              <td class="tbody_relatorio">pH***</td>
              <td class="tbody_relatorio">0,12</td>
            </tr>
            <tr>
              <td class="tbody_relatorio">4</td>
              <td class="tbody_relatorio">DEMANDA BIOQUÍMICA DE OXIGÊNIO - DBO</td>
              <td class="tbody_relatorio">0,1</td>
            </tr>
            <tr>
              <td class="tbody_relatorio">5</td>
              <td class="tbody_relatorio">TEMPERATURA - TEMP.</td>
              <td class="tbody_relatorio">0,1</td>
            </tr>
            <tr>
              <td class="tbody_relatorio">6</td>
              <td class="tbody_relatorio">NITROGÊNIO TOTAL</td>
              <td class="tbody_relatorio">0,1</td>
            </tr>
            <tr>
              <td class="tbody_relatorio">7</td>
              <td class="tbody_relatorio">FÓSFORO TOTAL - P****</td>
              <td class="tbody_relatorio">0,1</td>
            </tr>
            <tr>
              <td class="tbody_relatorio">8</td>
              <td class="tbody_relatorio">TURBIDEZ - TURB</td>
              <td class="tbody_relatorio">0,08</td>
            </tr>
            <tr>
              <td class="tbody_relatorio">9</td>
              <td class="tbody_relatorio">SÓLIDOS DISSOLVIDOS TOTAIS - SDT</td>
              <td class="tbody_relatorio">0,08</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>