<script setup>
import { ref, onMounted } from 'vue';
import { differenceInDays } from 'date-fns';
import ModalAnalises from './ModalAnalises.vue';
import PaginationSgc from '@/Components/PaginationSgc.vue';

// Definição de propriedades
const props = defineProps({
  contrato: Object,
  empreendimentos: Object,
  estudos: Object,
  subprodutos: Object,
});


// Para sincronização da rolagem
const scrollbarTop1 = ref(null);
const tableWrapper = ref(null);

// Função para sincronizar a rolagem entre a tabela e a barra de rolagem superior
const syncScroll = (source, target) => {
  target.scrollLeft = source.scrollLeft;
};

// Sincronizar barra de rolagem na montagem
onMounted(() => {
  if (scrollbarTop1.value && tableWrapper.value) {
    scrollbarTop1.value.addEventListener('scroll', () => syncScroll(scrollbarTop1.value, tableWrapper.value));
    tableWrapper.value.addEventListener('scroll', () => syncScroll(tableWrapper.value, scrollbarTop1.value));
  }
});

// Para os Modais dos cards (controle individual por card)
const modalsState = ref({
  atrasos: false,
  revisoesInternas: false,
  revisoesExternas: false,
  temposRevisoes: false,
  temposRevisoesExternas: false,
  temposAnalises: false,
  temposAnalisesExternas: false
}); 

const modalTitle = ref('');

const abrirModalAnalises = (modalKey, title) => {
  modalTitle.value = title;
  modalsState.value[modalKey] = true;
};

const fecharModal = (modalKey) => {
  modalsState.value[modalKey] = false;
};


// Variáveis reativas
const mediasAtrasos = ref([]);
const mediasRevisoesInternas = ref([]);
const mediasRevisoesExternas = ref([]);
const temposMediosRevisoes = ref([]);
const temposMediosRevisoesExternas = ref([]);
const mediasTemposAnalises = ref([]);
const mediasTemposAnalisesExternas = ref([]);
const mediasTemposExpedicoes = ref([]);
const tabelaItens = ref([]);

const modalVisible = ref(false);
const selectedItem = ref(null);

// Variáveis para ordenação
const sortColumn = ref('');
const sortOrder = ref('asc');

// Média de atrasos de entrega
const calcularMediaAtrasos = (empreendimentos) => {
  const itemEditalAtrasos = {};
  let totalDiasAtrasoGeral = 0;  
  let totalAtrasosGeral = 0;     

  empreendimentos.forEach(element => {
    props.estudos.forEach(estudo => {
      if (estudo.cod_emp === element.cod_emp) {
        const { item_edital, data_de_entrega_previsto, versao_00_data_de_entrega, subproduto } = estudo;

        if (versao_00_data_de_entrega && data_de_entrega_previsto) {
          const previsto = new Date(data_de_entrega_previsto);
          const entregue = new Date(versao_00_data_de_entrega);

          if (entregue > previsto) {
            const diasAtraso = differenceInDays(entregue, previsto);

            if (!itemEditalAtrasos[item_edital]) {
              itemEditalAtrasos[item_edital] = {
                totalDias: 0,
                count: 0,
                subproduto: subproduto || 'N/A'
              };
            }

            // Incrementar valores por item_edital
            itemEditalAtrasos[item_edital].totalDias += diasAtraso;
            itemEditalAtrasos[item_edital].count += 1;

            // Incrementar os valores para o consolidado geral
            totalDiasAtrasoGeral += diasAtraso;
            totalAtrasosGeral += 1;
          }
        }
      }
    });
  });

  const medias = [];
  for (const item_edital in itemEditalAtrasos) {
    const { totalDias, count, subproduto } = itemEditalAtrasos[item_edital];
    if (count > 0) {
      const mediaDias = totalDias / count;
      medias.push({
        item_edital,
        subproduto,
        mediaDias: mediaDias.toFixed(2),
      });
    }
  }

  // Calcular a média de atrasos geral
  const mediaGeral = totalAtrasosGeral > 0 ? (totalDiasAtrasoGeral / totalAtrasosGeral).toFixed(2) : 0;

  return {
    mediasItemEdital: medias,
    mediaGeral: mediaGeral  // Média geral consolidada
  };
};

// Média de revisões DNIT
const calcularMediaRevisoesInternas = (empreendimentos) => {
  const itemRevisoes = {};

  empreendimentos.forEach(element => {
    props.estudos.forEach(estudo => {
      if (estudo.cod_emp === element.cod_emp) {
        const { item_edital, subproduto } = estudo;

        if (estudo.versao_01_sei) {
          let totalRevisoes = 0;
          if (estudo.versao_01_sei) totalRevisoes++;
          if (estudo.versao_02_sei) totalRevisoes++;
          if (estudo.versao_03_sei) totalRevisoes++;
          if (estudo.versao_04_sei) totalRevisoes++;

          if (!itemRevisoes[item_edital]) {
            itemRevisoes[item_edital] = { subproduto, totalRevisoes: 0, count: 0 };
          }

          itemRevisoes[item_edital].totalRevisoes += totalRevisoes;
          itemRevisoes[item_edital].count++;
        }
      }
    });
  });

  const medias = [];
  let totalRevisoesGeral = 0;
  let countGeral = 0;

  for (const itemEdital in itemRevisoes) {
    const { subproduto, totalRevisoes, count } = itemRevisoes[itemEdital];
    if (count > 0) {
      const mediaRevisoes = totalRevisoes / count;
      medias.push({
        itemEdital,
        subproduto,
        mediaRevisoes: mediaRevisoes.toFixed(2),
      });

      totalRevisoesGeral += totalRevisoes;
      countGeral += count;
    }
  }

  const mediaGeralRevisoes = (totalRevisoesGeral / countGeral).toFixed(2);

  return {
    mediaGeral: mediaGeralRevisoes,
    mediasItemEdital: medias
  };
};

// Média de revisões externas
const calcularMediaRevisoesExternas = (empreendimentos) => {
  const itemRevisoesExternas = {};

  empreendimentos.forEach(element => {
    props.estudos.forEach(estudo => {
      if (estudo.cod_emp === element.cod_emp && estudo.versao_ext_01_sei) {
        const { item_edital, subproduto } = estudo;

        let totalRevisoesExternas = 0;
        if (estudo.analise_ext_01_data) totalRevisoesExternas++;
        if (estudo.analise_ext_02_data) totalRevisoesExternas++;
        if (estudo.analise_ext_03_data) totalRevisoesExternas++;
        if (estudo.analise_ext_04_data) totalRevisoesExternas++;

        if (totalRevisoesExternas > 0) {
          if (!itemRevisoesExternas[item_edital]) {
            itemRevisoesExternas[item_edital] = { subproduto, totalRevisoes: 0, count: 0 };
          }

          itemRevisoesExternas[item_edital].totalRevisoes += totalRevisoesExternas;
          itemRevisoesExternas[item_edital].count++;
        }
      }
    });
  });

  const medias = [];
  let totalRevisoesGeral = 0;
  let countGeral = 0;

  for (const itemEdital in itemRevisoesExternas) {
    const { subproduto, totalRevisoes, count } = itemRevisoesExternas[itemEdital];
    if (count > 0) {
      const mediaRevisoes = totalRevisoes / count;
      medias.push({
        itemEdital,
        subproduto,
        mediaRevisoes: mediaRevisoes.toFixed(2),
      });

      totalRevisoesGeral += totalRevisoes;
      countGeral += count;
    }
  }

  const mediaGeralRevisoes = (totalRevisoesGeral / countGeral).toFixed(2);

  return {
    mediaGeral: mediaGeralRevisoes,
    mediasItemEdital: medias
  };
};

// Tempo médio das revisões DNIT
const calcularTemposMediosRevisoes = (empreendimentos) => {
  const familiaTemposMedios = {};

  empreendimentos.forEach(element => {
    props.estudos.forEach(estudo => {
      if (estudo.cod_emp === element.cod_emp) {
        const { item_edital, subproduto, versao_00_data_oficio, versao_01_data_de_entrega, versao_01_data_oficio, versao_02_data_de_entrega, versao_02_data_oficio, versao_03_data_de_entrega, versao_03_data_oficio, versao_04_data_de_entrega } = estudo;

        if (!familiaTemposMedios[item_edital]) {
          familiaTemposMedios[item_edital] = {
            subproduto,
            totalRevisao1: 0,
            countRevisao1: 0,
            totalRevisao2: 0,
            countRevisao2: 0,
            totalRevisao3: 0,
            countRevisao3: 0,
            totalRevisao4: 0,
            countRevisao4: 0,
          };
        }

        // Revisão 1
        if (versao_01_data_de_entrega && versao_00_data_oficio) {
          const revisao1 = differenceInDays(new Date(versao_01_data_de_entrega), new Date(versao_00_data_oficio));
          familiaTemposMedios[item_edital].totalRevisao1 += revisao1;
          familiaTemposMedios[item_edital].countRevisao1++;
        }

        // Revisão 2
        if (versao_02_data_de_entrega && versao_01_data_oficio) {
          const revisao2 = differenceInDays(new Date(versao_02_data_de_entrega), new Date(versao_01_data_oficio));
          familiaTemposMedios[item_edital].totalRevisao2 += revisao2;
          familiaTemposMedios[item_edital].countRevisao2++;
        }

        // Revisão 3
        if (versao_03_data_de_entrega && versao_02_data_oficio) {
          const revisao3 = differenceInDays(new Date(versao_03_data_de_entrega), new Date(versao_02_data_oficio));
          familiaTemposMedios[item_edital].totalRevisao3 += revisao3;
          familiaTemposMedios[item_edital].countRevisao3++;
        }

        // Revisão 4
        if (versao_04_data_de_entrega && versao_03_data_oficio) {
          const revisao4 = differenceInDays(new Date(versao_04_data_de_entrega), new Date(versao_03_data_oficio));
          familiaTemposMedios[item_edital].totalRevisao4 += revisao4;
          familiaTemposMedios[item_edital].countRevisao4++;
        }
      }
    });
  });

  const mediasTempos = [];
  let totalGeralDias = 0;
  let totalGeralRevisoes = 0;

  for (const item_edital in familiaTemposMedios) {
    const { subproduto, totalRevisao1, countRevisao1, totalRevisao2, countRevisao2, totalRevisao3, countRevisao3, totalRevisao4, countRevisao4 } = familiaTemposMedios[item_edital];

    const mediaRevisao1 = countRevisao1 > 0 ? (totalRevisao1 / countRevisao1).toFixed(2) : null;
    const mediaRevisao2 = countRevisao2 > 0 ? (totalRevisao2 / countRevisao2).toFixed(2) : null;
    const mediaRevisao3 = countRevisao3 > 0 ? (totalRevisao3 / countRevisao3).toFixed(2) : null;
    const mediaRevisao4 = countRevisao4 > 0 ? (totalRevisao4 / countRevisao4).toFixed(2) : null;

    if (mediaRevisao1 || mediaRevisao2 || mediaRevisao3 || mediaRevisao4) {
      mediasTempos.push({
        item_edital,
        subproduto,
        mediaRevisao1,
        mediaRevisao2,
        mediaRevisao3,
        mediaRevisao4
      });

      // Acumular para a média geral consolidada
      totalGeralDias += totalRevisao1 + totalRevisao2 + totalRevisao3 + totalRevisao4;
      totalGeralRevisoes += countRevisao1 + countRevisao2 + countRevisao3 + countRevisao4;
    }
  }

  const mediaGeral = totalGeralRevisoes > 0 ? (totalGeralDias / totalGeralRevisoes).toFixed(2) : null;

  return {
    mediasTempos,
    mediaGeral
  };
};

// Tempo médio das revisões externas
const calcularTemposMediosRevisoesExternas = (empreendimentos) => {
  const familiaTemposMediosExternos = {};

  empreendimentos.forEach(element => {
    props.estudos.forEach(estudo => {
      if (estudo.cod_emp === element.cod_emp) {
        const { item_edital, subproduto, versao_ext_01_data, analise_ext_01_data, versao_ext_02_data, analise_ext_02_data, versao_ext_03_data, analise_ext_03_data, versao_ext_04_data, analise_ext_04_data } = estudo;

        if (!familiaTemposMediosExternos[item_edital]) {
          familiaTemposMediosExternos[item_edital] = {
            subproduto,
            totalRevisao1: 0,
            countRevisao1: 0,
            totalRevisao2: 0,
            countRevisao2: 0,
            totalRevisao3: 0,
            countRevisao3: 0,
            totalRevisao4: 0,
            countRevisao4: 0,
          };
        }

        // Revisão Externa 1
        if (versao_ext_01_data && analise_ext_01_data) {
          const revisao1 = differenceInDays(new Date(versao_ext_01_data), new Date(analise_ext_01_data));
          familiaTemposMediosExternos[item_edital].totalRevisao1 += revisao1;
          familiaTemposMediosExternos[item_edital].countRevisao1++;
        }

        // Revisão Externa 2
        if (versao_ext_02_data && analise_ext_02_data) {
          const revisao2 = differenceInDays(new Date(versao_ext_02_data), new Date(analise_ext_02_data));
          familiaTemposMediosExternos[item_edital].totalRevisao2 += revisao2;
          familiaTemposMediosExternos[item_edital].countRevisao2++;
        }

        // Revisão Externa 3
        if (versao_ext_03_data && analise_ext_03_data) {
          const revisao3 = differenceInDays(new Date(versao_ext_03_data), new Date(analise_ext_03_data));
          familiaTemposMediosExternos[item_edital].totalRevisao3 += revisao3;
          familiaTemposMediosExternos[item_edital].countRevisao3++;
        }

        // Revisão Externa 4
        if (versao_ext_04_data && analise_ext_04_data) {
          const revisao4 = differenceInDays(new Date(versao_ext_04_data), new Date(analise_ext_04_data));
          familiaTemposMediosExternos[item_edital].totalRevisao4 += revisao4;
          familiaTemposMediosExternos[item_edital].countRevisao4++;
        }
      }
    });
  });

  const mediasTemposExternos = [];
  let totalGeralDias = 0;
  let totalGeralRevisoes = 0;

  for (const item_edital in familiaTemposMediosExternos) {
    const { subproduto, totalRevisao1, countRevisao1, totalRevisao2, countRevisao2, totalRevisao3, countRevisao3, totalRevisao4, countRevisao4 } = familiaTemposMediosExternos[item_edital];

    const mediaRevisao1 = countRevisao1 > 0 ? (totalRevisao1 / countRevisao1).toFixed(2) : null;
    const mediaRevisao2 = countRevisao2 > 0 ? (totalRevisao2 / countRevisao2).toFixed(2) : null;
    const mediaRevisao3 = countRevisao3 > 0 ? (totalRevisao3 / countRevisao3).toFixed(2) : null;
    const mediaRevisao4 = countRevisao4 > 0 ? (totalRevisao4 / countRevisao4).toFixed(2) : null;

    if (mediaRevisao1 || mediaRevisao2 || mediaRevisao3 || mediaRevisao4) {
      mediasTemposExternos.push({
        item_edital,
        subproduto,
        mediaRevisao1,
        mediaRevisao2,
        mediaRevisao3,
        mediaRevisao4
      });

      // Acumular para a média geral consolidada
      totalGeralDias += totalRevisao1 + totalRevisao2 + totalRevisao3 + totalRevisao4;
      totalGeralRevisoes += countRevisao1 + countRevisao2 + countRevisao3 + countRevisao4;
    }
  }

  const mediaGeral = totalGeralRevisoes > 0 ? (totalGeralDias / totalGeralRevisoes).toFixed(2) : null;

  return {
    mediasTemposExternos,
    mediaGeral
  };
};

// Tempo médio de Análises DNIT
const calcularTemposMediosAnalises = (empreendimentos) => {
  const familiaTemposMediosAnalises = {};

  empreendimentos.forEach(element => {
    props.estudos.forEach(estudo => {
      if (estudo.cod_emp === element.cod_emp) {
        const { item_edital, subproduto, versao_00_data_de_entrega, versao_00_data_nt, versao_01_data_de_entrega, versao_01_data_nt, versao_02_data_de_entrega, versao_02_data_nt, versao_03_data_de_entrega, versao_03_data_nt, versao_04_data_de_entrega, versao_04_data_nt } = estudo;

        if (!familiaTemposMediosAnalises[item_edital]) {
          familiaTemposMediosAnalises[item_edital] = {
            subproduto,
            totalAnalise0: 0,
            countAnalise0: 0,
            totalAnalise1: 0,
            countAnalise1: 0,
            totalAnalise2: 0,
            countAnalise2: 0,
            totalAnalise3: 0,
            countAnalise3: 0,
            totalAnalise4: 0,
            countAnalise4: 0,
          };
        }

        // Análise 0
        if (versao_00_data_nt && versao_00_data_de_entrega) {
          const analise0 = differenceInDays(new Date(versao_00_data_nt), new Date(versao_00_data_de_entrega));
          familiaTemposMediosAnalises[item_edital].totalAnalise0 += analise0;
          familiaTemposMediosAnalises[item_edital].countAnalise0++;
        }

        // Análise 1
        if (versao_01_data_nt && versao_01_data_de_entrega) {
          const analise1 = differenceInDays(new Date(versao_01_data_nt), new Date(versao_01_data_de_entrega));
          familiaTemposMediosAnalises[item_edital].totalAnalise1 += analise1;
          familiaTemposMediosAnalises[item_edital].countAnalise1++;
        }

        // Análise 2
        if (versao_02_data_nt && versao_02_data_de_entrega) {
          const analise2 = differenceInDays(new Date(versao_02_data_nt), new Date(versao_02_data_de_entrega));
          familiaTemposMediosAnalises[item_edital].totalAnalise2 += analise2;
          familiaTemposMediosAnalises[item_edital].countAnalise2++;
        }

        // Análise 3
        if (versao_03_data_nt && versao_03_data_de_entrega) {
          const analise3 = differenceInDays(new Date(versao_03_data_nt), new Date(versao_03_data_de_entrega));
          familiaTemposMediosAnalises[item_edital].totalAnalise3 += analise3;
          familiaTemposMediosAnalises[item_edital].countAnalise3++;
        }

        // Análise 4
        if (versao_04_data_nt && versao_04_data_de_entrega) {
          const analise4 = differenceInDays(new Date(versao_04_data_nt), new Date(versao_04_data_de_entrega));
          familiaTemposMediosAnalises[item_edital].totalAnalise4 += analise4;
          familiaTemposMediosAnalises[item_edital].countAnalise4++;
        }
      }
    });
  });

  const mediasTemposAnalises = [];
  let totalGeralDias = 0;
  let totalGeralAnalises = 0;

  for (const item_edital in familiaTemposMediosAnalises) {
    const {subproduto, totalAnalise0, countAnalise0, totalAnalise1, countAnalise1, totalAnalise2, countAnalise2, totalAnalise3, countAnalise3, totalAnalise4, countAnalise4 } = familiaTemposMediosAnalises[item_edital];

    const mediaAnalise0 = countAnalise0 > 0 ? (totalAnalise0 / countAnalise0).toFixed(2) : null;
    const mediaAnalise1 = countAnalise1 > 0 ? (totalAnalise1 / countAnalise1).toFixed(2) : null;
    const mediaAnalise2 = countAnalise2 > 0 ? (totalAnalise2 / countAnalise2).toFixed(2) : null;
    const mediaAnalise3 = countAnalise3 > 0 ? (totalAnalise3 / countAnalise3).toFixed(2) : null;
    const mediaAnalise4 = countAnalise4 > 0 ? (totalAnalise4 / countAnalise4).toFixed(2) : null;

    if (mediaAnalise0 || mediaAnalise1 || mediaAnalise2 || mediaAnalise3 || mediaAnalise4) {
      mediasTemposAnalises.push({
        item_edital,
        subproduto,
        mediaAnalise0,
        mediaAnalise1,
        mediaAnalise2,
        mediaAnalise3,
        mediaAnalise4,
      });

      // Acumular para a média geral consolidada
      totalGeralDias += totalAnalise0 + totalAnalise1 + totalAnalise2 + totalAnalise3 + totalAnalise4;
      totalGeralAnalises += countAnalise0 + countAnalise1 + countAnalise2 + countAnalise3 + countAnalise4;
    }
  }

  const mediaGeral = totalGeralAnalises > 0 ? (totalGeralDias / totalGeralAnalises).toFixed(2) : null;

  return {
    mediasTemposAnalises,
    mediaGeral
  };
};

// Tempo Médio Analises Externas
const calcularTemposMediosAnalisesExternas = (empreendimentos) => {
  const familiaTemposMediosAnalisesExternas = {};

  empreendimentos.forEach(element => {
    props.estudos.forEach(estudo => {
      if (estudo.cod_emp === element.cod_emp) {
        const {
          item_edital,
          subproduto,
          req_ext_data,
          analise_ext_01_data,
          versao_ext_01_data,
          analise_ext_02_data,
          versao_ext_02_data,
          analise_ext_03_data,
          versao_ext_03_data,
          analise_ext_04_data,
          versao_ext_04_data,
          aut_ext_data
        } = estudo;

        if (!familiaTemposMediosAnalisesExternas[item_edital]) {
          familiaTemposMediosAnalisesExternas[item_edital] = {
            subproduto,
            totalAnaliseExt0: 0,
            countAnaliseExt0: 0,
            totalAnaliseExt1: 0,
            countAnaliseExt1: 0,
            totalAnaliseExt2: 0,
            countAnaliseExt2: 0,
            totalAnaliseExt3: 0,
            countAnaliseExt3: 0,
            totalAnaliseExt4: 0,
            countAnaliseExt4: 0,
          };
        }

        // Análise Externa 0
        if (req_ext_data && analise_ext_01_data) {
          const analiseExt0 = differenceInDays(new Date(analise_ext_01_data), new Date(req_ext_data));
          familiaTemposMediosAnalisesExternas[item_edital].totalAnaliseExt0 += analiseExt0;
          familiaTemposMediosAnalisesExternas[item_edital].countAnaliseExt0++;
        }

        // Análise Externa 1
        if (versao_ext_01_data && analise_ext_02_data) {
          const analiseExt1 = differenceInDays(new Date(analise_ext_02_data), new Date(versao_ext_01_data));
          familiaTemposMediosAnalisesExternas[item_edital].totalAnaliseExt1 += analiseExt1;
          familiaTemposMediosAnalisesExternas[item_edital].countAnaliseExt1++;
        }

        // Análise Externa 2
        if (versao_ext_02_data && analise_ext_03_data) {
          const analiseExt2 = differenceInDays(new Date(analise_ext_03_data), new Date(versao_ext_02_data));
          familiaTemposMediosAnalisesExternas[item_edital].totalAnaliseExt2 += analiseExt2;
          familiaTemposMediosAnalisesExternas[item_edital].countAnaliseExt2++;
        }

        // Análise Externa 3
        if (versao_ext_03_data && analise_ext_04_data) {
          const analiseExt3 = differenceInDays(new Date(analise_ext_04_data), new Date(versao_ext_03_data));
          familiaTemposMediosAnalisesExternas[item_edital].totalAnaliseExt3 += analiseExt3;
          familiaTemposMediosAnalisesExternas[item_edital].countAnaliseExt3++;
        }

        // Análise Externa 4
        if (versao_ext_04_data && aut_ext_data) {
          const analiseExt4 = differenceInDays(new Date(aut_ext_data), new Date(versao_ext_04_data));
          familiaTemposMediosAnalisesExternas[item_edital].totalAnaliseExt4 += analiseExt4;
          familiaTemposMediosAnalisesExternas[item_edital].countAnaliseExt4++;
        }
      }
    });
  });

  const mediasTemposAnalisesExternas = [];
  let totalGeralDias = 0;
  let totalGeralAnalises = 0;

  for (const item_edital in familiaTemposMediosAnalisesExternas) {
    const {
      subproduto,
      totalAnaliseExt0,
      countAnaliseExt0,
      totalAnaliseExt1,
      countAnaliseExt1,
      totalAnaliseExt2,
      countAnaliseExt2,
      totalAnaliseExt3,
      countAnaliseExt3,
      totalAnaliseExt4,
      countAnaliseExt4
    } = familiaTemposMediosAnalisesExternas[item_edital];

    const mediaAnaliseExt0 = countAnaliseExt0 > 0 ? (totalAnaliseExt0 / countAnaliseExt0).toFixed(2) : null;
    const mediaAnaliseExt1 = countAnaliseExt1 > 0 ? (totalAnaliseExt1 / countAnaliseExt1).toFixed(2) : null;
    const mediaAnaliseExt2 = countAnaliseExt2 > 0 ? (totalAnaliseExt2 / countAnaliseExt2).toFixed(2) : null;
    const mediaAnaliseExt3 = countAnaliseExt3 > 0 ? (totalAnaliseExt3 / countAnaliseExt3).toFixed(2) : null;
    const mediaAnaliseExt4 = countAnaliseExt4 > 0 ? (totalAnaliseExt4 / countAnaliseExt4).toFixed(2) : null;

    if (mediaAnaliseExt0 || mediaAnaliseExt1 || mediaAnaliseExt2 || mediaAnaliseExt3 || mediaAnaliseExt4) {
      mediasTemposAnalisesExternas.push({
        item_edital,
        subproduto,
        mediaAnaliseExt0,
        mediaAnaliseExt1,
        mediaAnaliseExt2,
        mediaAnaliseExt3,
        mediaAnaliseExt4,
      });

      // Acumular para a média geral consolidada
      totalGeralDias += totalAnaliseExt0 + totalAnaliseExt1 + totalAnaliseExt2 + totalAnaliseExt3 + totalAnaliseExt4;
      totalGeralAnalises += countAnaliseExt0 + countAnaliseExt1 + countAnaliseExt2 + countAnaliseExt3 + countAnaliseExt4;
    }
  }

  const mediaGeral = totalGeralAnalises > 0 ? (totalGeralDias / totalGeralAnalises).toFixed(2) : null;

  return {
    mediasTemposAnalisesExternas,
    mediaGeral
  };
};

// Tempo Médio das Expedições
const calcularTemposMediosExpedicoes = (empreendimentos) => {
  const familiaTemposMediosExpedicoes = {};

  empreendimentos.forEach(element => {
    props.estudos.forEach(estudo => {
      if (estudo.cod_emp === element.cod_emp) {
        const {
          item_edital,
          subproduto,
          versao_00_data_oficio,
          versao_00_data_nt,
          versao_01_data_oficio,
          versao_01_data_nt,
          versao_02_data_oficio,
          versao_02_data_nt,
          versao_03_data_oficio,
          versao_03_data_nt,
          versao_04_data_oficio,
          versao_04_data_nt
        } = estudo;

        if (!familiaTemposMediosExpedicoes[item_edital]) {
          familiaTemposMediosExpedicoes[item_edital] = {
            subproduto,
            totalExpedicao0: 0,
            countExpedicao0: 0,
            totalExpedicao1: 0,
            countExpedicao1: 0,
            totalExpedicao2: 0,
            countExpedicao2: 0,
            totalExpedicao3: 0,
            countExpedicao3: 0,
            totalExpedicao4: 0,
            countExpedicao4: 0,
          };
        }

        // Expedição 0
        if (versao_00_data_oficio && versao_00_data_nt) {
          const expedicao0 = differenceInDays(new Date(versao_00_data_oficio), new Date(versao_00_data_nt));
          familiaTemposMediosExpedicoes[item_edital].totalExpedicao0 += expedicao0;
          familiaTemposMediosExpedicoes[item_edital].countExpedicao0++;
        }

        // Expedição 1
        if (versao_01_data_oficio && versao_01_data_nt) {
          const expedicao1 = differenceInDays(new Date(versao_01_data_oficio), new Date(versao_01_data_nt));
          familiaTemposMediosExpedicoes[item_edital].totalExpedicao1 += expedicao1;
          familiaTemposMediosExpedicoes[item_edital].countExpedicao1++;
        }

        // Expedição 2
        if (versao_02_data_oficio && versao_02_data_nt) {
          const expedicao2 = differenceInDays(new Date(versao_02_data_oficio), new Date(versao_02_data_nt));
          familiaTemposMediosExpedicoes[item_edital].totalExpedicao2 += expedicao2;
          familiaTemposMediosExpedicoes[item_edital].countExpedicao2++;
        }

        // Expedição 3
        if (versao_03_data_oficio && versao_03_data_nt) {
          const expedicao3 = differenceInDays(new Date(versao_03_data_oficio), new Date(versao_03_data_nt));
          familiaTemposMediosExpedicoes[item_edital].totalExpedicao3 += expedicao3;
          familiaTemposMediosExpedicoes[item_edital].countExpedicao3++;
        }

        // Expedição 4
        if (versao_04_data_oficio && versao_04_data_nt) {
          const expedicao4 = differenceInDays(new Date(versao_04_data_oficio), new Date(versao_04_data_nt));
          familiaTemposMediosExpedicoes[item_edital].totalExpedicao4 += expedicao4;
          familiaTemposMediosExpedicoes[item_edital].countExpedicao4++;
        }
      }
    });
  });

  const mediasTemposExpedicoes = [];
  let totalGeralDias = 0;
  let totalGeralExpedicoes = 0;

  for (const item_edital in familiaTemposMediosExpedicoes) {
    const {
      subproduto,
      totalExpedicao0,
      countExpedicao0,
      totalExpedicao1,
      countExpedicao1,
      totalExpedicao2,
      countExpedicao2,
      totalExpedicao3,
      countExpedicao3,
      totalExpedicao4,
      countExpedicao4
    } = familiaTemposMediosExpedicoes[item_edital];

    const mediaExpedicao0 = countExpedicao0 > 0 ? (totalExpedicao0 / countExpedicao0).toFixed(2) : null;
    const mediaExpedicao1 = countExpedicao1 > 0 ? (totalExpedicao1 / countExpedicao1).toFixed(2) : null;
    const mediaExpedicao2 = countExpedicao2 > 0 ? (totalExpedicao2 / countExpedicao2).toFixed(2) : null;
    const mediaExpedicao3 = countExpedicao3 > 0 ? (totalExpedicao3 / countExpedicao3).toFixed(2) : null;
    const mediaExpedicao4 = countExpedicao4 > 0 ? (totalExpedicao4 / countExpedicao4).toFixed(2) : null;

    if (mediaExpedicao0 || mediaExpedicao1 || mediaExpedicao2 || mediaExpedicao3 || mediaExpedicao4) {
      mediasTemposExpedicoes.push({
        item_edital,
        subproduto,
        mediaExpedicao0,
        mediaExpedicao1,
        mediaExpedicao2,
        mediaExpedicao3,
        mediaExpedicao4,
      });

      // Acumular para a média geral consolidada
      totalGeralDias += totalExpedicao0 + totalExpedicao1 + totalExpedicao2 + totalExpedicao3 + totalExpedicao4;
      totalGeralExpedicoes += countExpedicao0 + countExpedicao1 + countExpedicao2 + countExpedicao3 + countExpedicao4;
    }
  }

  const mediaGeral = totalGeralExpedicoes > 0 ? (totalGeralDias / totalGeralExpedicoes).toFixed(2) : null;

  return {
    mediasTemposExpedicoes,
    mediaGeral
  };
};

// Função para criar a tabela de itens
const criarTabelaItens = (empreendimentos) => {
  const tabela = [];

  empreendimentos.forEach((element) => {
    props.estudos.forEach((estudo) => {
      if (estudo.cod_emp === element.cod_emp) {
        tabela.push({
          ...estudo,
          data_de_inicio_previsto: estudo.data_de_inicio_previsto
            ? new Date(estudo.data_de_inicio_previsto).toLocaleDateString() 
            : null,
          data_de_entrega_previsto: estudo.data_de_entrega_previsto
            ? new Date(estudo.data_de_entrega_previsto)
            : null,
        });
      }
    });
  });

  return tabela;
};

// Tabela Subprodutos: tempo de elaboração interno
const calcularTempoElaboracaoInterno = (item) => {
  const datas = [
    { entrega: item.versao_00_data_de_entrega, referencia: item.data_de_inicio_prvst },
    { entrega: item.versao_01_data_de_entrega, referencia: item.versao_00_data_oficio },
    { entrega: item.versao_02_data_de_entrega, referencia: item.versao_01_data_oficio },
    { entrega: item.versao_03_data_de_entrega, referencia: item.versao_02_data_oficio },
    { entrega: item.versao_04_data_de_entrega, referencia: item.versao_03_data_oficio }
  ];

  let totalDias = 0;

  datas.forEach((par) => {
    if (par.entrega && par.referencia) {
      const dataEntrega = new Date(par.entrega);
      const dataReferencia = new Date(par.referencia);

      if (!isNaN(dataEntrega.getTime()) && !isNaN(dataReferencia.getTime())) {
        const diffTime = Math.abs(dataEntrega - dataReferencia);
        const diffDias = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

        totalDias += diffDias;
      }
    }
  });

  return totalDias > 0 ? `${totalDias} dias` : "";
};


// Função para extrair apenas o número de dias da string
const extrairDias = (texto) => {
  if (!texto) return 0;
  const resultado = texto.match(/\d+/); // Pega o número da string
  return resultado ? parseInt(resultado[0], 10) : 0;
};
// Função para calcular o total de dias de análise
const calcularTotalAnalise = (item) => {
  const dnit = calcularTempoAnaliseDnit(item);
  const externo = calcularTempoAnaliseExterno(item);
  
  const diasDnit = extrairDias(dnit);
  const diasExterno = extrairDias(externo);

  const totalDias = diasDnit + diasExterno;

  return totalDias > 0 ? `${totalDias} dias` : "";
};

// Função para calcular o tempo total de elaboração
const calcularTempoTotalElaboracao = (item) => {
  const interno = calcularTempoElaboracaoInterno(item);
  const externo = calcularTempoElaboracaoExterno(item);

  const diasInterno = extrairDias(interno);
  const diasExterno = extrairDias(externo);

  const totalDias = diasInterno + diasExterno;

  return totalDias > 0 ? `${totalDias} dias` : "";
};

// Tabela Subprodutos: tempo de análise DNIT
const calcularTempoAnaliseDnit = (item) => {
  const datas = [
    { entrega: item.versao_00_data_de_entrega, oficio: item.versao_00_data_oficio },
    { entrega: item.versao_01_data_de_entrega, oficio: item.versao_01_data_oficio },
    { entrega: item.versao_02_data_de_entrega, oficio: item.versao_02_data_oficio },
    { entrega: item.versao_03_data_de_entrega, oficio: item.versao_03_data_oficio },
    { entrega: item.versao_04_data_de_entrega, oficio: item.versao_03_data_oficio }
  ];

  let totalDias = 0;

  
  datas.forEach((par) => {
    if (par.entrega && par.oficio) {
      const dataEntrega = new Date(par.entrega);
      const dataOficio = new Date(par.oficio);
      
      const diffTime = Math.abs(dataEntrega - dataOficio);
      const diffDias = Math.ceil(diffTime / (1000 * 60 * 60 * 24)); 
      
      totalDias += diffDias;
    }
  });

  return totalDias > 0 ? `${totalDias} dias` : ""; 
};

// Tabela Subprodutos: tempo de análise externo
const calcularTempoAnaliseExterno = (item) => {
  const datas = [
    { entrega: item.analise_ext_01_data, oficio: item.req_ext_data },
    { entrega: item.analise_ext_02_data, oficio: item.versao_ext_01_data },
    { entrega: item.analise_ext_03_data, oficio: item.versao_ext_02_data },
    { entrega: item.analise_ext_04_data, oficio: item.versao_ext_03_data },
    { entrega: item.aut_ext_data, oficio: item.versao_ext_04_data }
  ];

  let totalDias = 0;

  datas.forEach((par) => {
    if (par.entrega && par.oficio) {
      const dataEntrega = new Date(par.entrega);
      const dataOficio = new Date(par.oficio);
      
      const diffTime = Math.abs(dataEntrega - dataOficio);
      const diffDias = Math.ceil(diffTime / (1000 * 60 * 60 * 24)); 
      
      totalDias += diffDias;
    }
  });

  return totalDias > 0 ? `${totalDias} dias` : ""; 
};

// Tabela Subprodutos: tempo de elaboração externo
const calcularTempoElaboracaoExterno = (item) => {
  const datas = [
    { entrega: item.versao_ext_01_data, referencia: item.analise_ext_01_data },
    { entrega: item.versao_ext_02_data, referencia: item.analise_ext_02_data },
    { entrega: item.versao_ext_03_data, referencia: item.analise_ext_03_data },
    { entrega: item.versao_ext_04_data, referencia: item.analise_ext_04_data }
  ];

  let totalDias = 0;

  datas.forEach((par) => {
    if (par.entrega && par.referencia) {
      const dataEntrega = new Date(par.entrega);
      const dataReferencia = new Date(par.referencia);
      
      const diffTime = Math.abs(dataEntrega - dataReferencia);
      const diffDias = Math.ceil(diffTime / (1000 * 60 * 60 * 24)); 
      
      totalDias += diffDias;
    }
  });

  return totalDias > 0 ? `${totalDias} dias` : ""; 
};

// Tempo de ELABORAÇÃO (Data da OSE / Hoje)
const calcularDiferencaDiasOse = (empreendimentos) => {
  const diferencasDias = [];

  empreendimentos.forEach(element => {
    if (element.ose_data) {
      const oseData = new Date(element.ose_data);
      const hoje = new Date();
      
      // Verifica se a data OSE é válida
      if (!isNaN(oseData.getTime())) {
        const diffTime = Math.abs(hoje - oseData);
        const diffDias = Math.ceil(diffTime / (1000 * 60 * 60 * 24));  // Converte a diferença de milissegundos para dias
        diferencasDias.push(`${diffDias}`);  // Adiciona a diferença à lista
      } else {
        diferencasDias.push("Data inválida");
      }
    }
  });

  return diferencasDias.length > 0 ? diferencasDias.join(', ') : "Nenhuma data OSE válida encontrada";  // Retorna as diferenças ou uma mensagem padrão
};


// Tabela Subprodutos: contar as versões externas
const contarVersoesExterno = (item) => {
  let count = 0;
  if (item.versao_ext_01_sei) { 
    if (item.versao_ext_01_sei) count++;
    if (item.versao_ext_02_sei) count++;
    if (item.versao_ext_03_sei) count++;
    if (item.versao_ext_04_sei) count++;
  }
  return count;
};

// Tabela Subprodutos: contar as versões DNIT
const contarVersoesDnit = (item) => {
  let count = 0;
  if (item.versao_01_sei) count++;
  if (item.versao_02_sei) count++;
  if (item.versao_03_sei) count++;
  if (item.versao_04_sei) count++;
  return count;
};

const abrirModal = (item) => {
  selectedItem.value = item;
  modalVisible.value = true;
};

// Ordenar os itens com clique na coluna
const ordenarTabela = (coluna) => {
  if (sortColumn.value === coluna) {
    sortOrder.value = sortOrder.value === 'asc' ? 'desc' : 'asc';
  } else {
    sortColumn.value = coluna;
    sortOrder.value = 'asc';
  }

  tabelaItens.value.sort((a, b) => {
    const aValue = a[coluna] !== null ? a[coluna] : ''; 
    const bValue = b[coluna] !== null ? b[coluna] : '';

    if (coluna === 'item_edital') {
      return compararVersoes(aValue, bValue);
    } else if (!isNaN(aValue) && !isNaN(bValue)) {
      return sortOrder.value === 'asc' ? aValue - bValue : bValue - aValue;
    } else {
      return sortOrder.value === 'asc'
        ? aValue.toString().localeCompare(bValue.toString())
        : bValue.toString().localeCompare(aValue.toString());
    }
  });
};

// Para as versões no formato: 3.1.1, 3.2.1.3
const compararVersoes = (a, b) => {
  const aPartes = a.split('.').map(Number);
  const bPartes = b.split('.').map(Number);

  for (let i = 0; i < Math.max(aPartes.length, bPartes.length); i++) {
    const aParte = aPartes[i] || 0; 
    const bParte = bPartes[i] || 0;

    if (aParte !== bParte) {
      return sortOrder.value === 'asc' ? aParte - bParte : bParte - aParte;
    }
  }

  return 0; 
};

onMounted(() => {
  mediasAtrasos.value = calcularMediaAtrasos(props.empreendimentos);
  mediasRevisoesInternas.value = calcularMediaRevisoesInternas(props.empreendimentos);
  mediasRevisoesExternas.value = calcularMediaRevisoesExternas(props.empreendimentos);
  temposMediosRevisoes.value = calcularTemposMediosRevisoes(props.empreendimentos);
  temposMediosRevisoesExternas.value = calcularTemposMediosRevisoesExternas(props.empreendimentos);
  mediasTemposAnalises.value = calcularTemposMediosAnalises(props.empreendimentos);
  mediasTemposAnalisesExternas.value = calcularTemposMediosAnalisesExternas(props.empreendimentos); 
  mediasTemposExpedicoes.value = calcularTemposMediosExpedicoes(props.empreendimentos);
  tabelaItens.value = criarTabelaItens(props.empreendimentos);
});
</script>

<template>
  <div class="container my-4">
    <div class="row">
      <!-- Tempo Médio de Atraso de Entregas -->
      <div class="col-md-2 mb-4">
        <div class="card" style="height: 100%;">
          <div class="card-header d-flex justify-content-center align-items-center" style="height: 60px; text-align: center; font-weight: bold; background-color: #dde1e4;">
            <!-- Médio de Atraso de Entregas -->
            TEMPO MÉDIO DE ATRASO DE ENTREGAS
          </div>
          <div class="card-body d-flex flex-column justify-content-between" style="height: 120px;">
            <div v-if="mediasAtrasos.mediaGeral" class="d-flex justify-content-center align-items-center" style="flex-grow: 1;">
              <h3 style="font-size: 24px; text-align: center;">{{ mediasAtrasos.mediaGeral }} dias</h3>
            </div>
            <button class="btn btn-sm btn-info mt-2 mx-auto" @click="abrirModalAnalises('atrasos', 'Detalhamento de Atrasos')">Detalhes</button>
          </div>
        </div>
      </div>
      <!-- MODAL -->
      <ModalAnalises :isVisible="modalsState.atrasos" :title="modalTitle" @close="fecharModal('atrasos')">
        <table class="table table-striped custom-table">
          <thead>
            <tr>
              <th class="text-center" style="font-size: 12px;">Item Edital</th>
              <th class="text-center " style="font-size: 12px;">Subproduto</th>
              <th class="text-center" style="font-size: 12px;">Média de Dias de Atraso</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(atraso, index) in mediasAtrasos.mediasItemEdital" :key="index">
              <td class="text-center">{{ atraso.item_edital }}</td>
              <td class="subproduto-col" :title="atraso.subproduto">{{ atraso.subproduto }}</td>
              <td class="text-center">{{ atraso.mediaDias }} dias</td>
            </tr>
          </tbody>
        </table>
      </ModalAnalises>

      <!-- Tempo Médio das Análises Internas -->
      <div class="col-md-2 mb-4">
        <div class="card" style="height: 100%;">
          <div class="card-header d-flex justify-content-center align-items-center" style="height: 60px; text-align: center; font-weight: bold; background-color: #dde1e4;">
            <!-- Tempo Médio de Análises Internas -->
             TEMPO MÉDIO DE ANÁLISES INTERNAS
          </div>
          <div class="card-body d-flex flex-column justify-content-between" style="height: 120px;">
            <div v-if="mediasTemposAnalises.mediaGeral" class="d-flex justify-content-center align-items-center" style="flex-grow: 1;">
              <h3 style="font-size: 24px; text-align: center;">{{ mediasTemposAnalises.mediaGeral }} dias</h3>
            </div>
            <button class="btn btn-sm btn-info mt-2 mx-auto" @click="abrirModalAnalises('temposAnalises', 'Detalhamento de Tempos Médios de Análises Internas')">Detalhes</button>
          </div>
        </div>
      </div>
      <!-- MODAL -->
      <ModalAnalises :isVisible="modalsState.temposAnalises" :title="modalTitle" @close="fecharModal('temposAnalises')">
        <table class="table table-striped">
          <thead>
            <tr>
              <th>Item Edital</th>
              <th>Subproduto</th>
              <th>Análise 0</th>
              <th>Análise 1</th>
              <th>Análise 2</th>
              <th>Análise 3</th>
              <th>Análise 4</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(media, index) in mediasTemposAnalises.mediasTemposAnalises" :key="index">
              <td>{{ media.item_edital }}</td>
              <td class="subproduto-col" :title="media.subproduto">{{ media.subproduto }}</td>
              <td v-if="media.mediaAnalise0">{{ media.mediaAnalise0 }} dias</td>
              <td v-else>-</td>
              <td v-if="media.mediaAnalise1">{{ media.mediaAnalise1 }} dias</td>
              <td v-else>-</td>
              <td v-if="media.mediaAnalise2">{{ media.mediaAnalise2 }} dias</td>
              <td v-else>-</td>
              <td v-if="media.mediaAnalise3">{{ media.mediaAnalise3 }} dias</td>
              <td v-else>-</td>
              <td v-if="media.mediaAnalise4">{{ media.mediaAnalise4 }} dias</td>
              <td v-else>-</td>
            </tr>
          </tbody>
        </table>
      </ModalAnalises>

      <!-- Tempo Médio das Revisões -->
      <div class="col-md-2 mb-4">
        <div class="card" style="height: 100%;">
          <div class="card-header d-flex justify-content-center align-items-center" style="height: 60px; text-align: center; font-weight: bold; background-color: #dde1e4;">
            <!-- Tempo Médio de Revisões Internas -->
             TEMPO MÉDIO DE REVISÕES INTERNAS
          </div>
          <div class="card-body d-flex flex-column justify-content-between" style="height: 120px;">
            <div v-if="temposMediosRevisoes.mediaGeral" class="d-flex justify-content-center align-items-center" style="flex-grow: 1;">
              <h3 style="font-size: 24px; text-align: center;">{{ temposMediosRevisoes.mediaGeral }} dias</h3>
            </div>
            <button class="btn btn-sm btn-info mt-2 mx-auto" @click="abrirModalAnalises('temposRevisoes', 'Detalhamento de Tempos Médios de Revisão')">Detalhes</button>
          </div>
        </div>
      </div>
      <!-- MODAL -->
      <ModalAnalises :isVisible="modalsState.temposRevisoes" :title="modalTitle" @close="fecharModal('temposRevisoes')">
        <table class="table table-striped">
          <thead>
            <tr>
              <th>Item Edital</th>
              <th>Subproduto</th>
              <th>Revisão 1</th>
              <th>Revisão 2</th>
              <th>Revisão 3</th>
              <th>Revisão 4</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(media, index) in temposMediosRevisoes.mediasTempos" :key="index">
              <td>{{ media.item_edital }}</td>
              <td class="subproduto-col" :title="media.subproduto">{{ media.subproduto }}</td>
              <td v-if="media.mediaRevisao1">{{ media.mediaRevisao1 }} dias</td>
              <td v-else>-</td>
              <td v-if="media.mediaRevisao2">{{ media.mediaRevisao2 }} dias</td>
              <td v-else>-</td>
              <td v-if="media.mediaRevisao3">{{ media.mediaRevisao3 }} dias</td>
              <td v-else>-</td>
              <td v-if="media.mediaRevisao4">{{ media.mediaRevisao4 }} dias</td>
              <td v-else>-</td>
            </tr>
          </tbody>
        </table>
      </ModalAnalises>

      <!-- Médias de Revisões Internas -->
      <div class="col-md-2 mb-4">
        <div class="card" style="height: 100%;">
          <div class="card-header d-flex justify-content-center align-items-center" style="height: 60px; text-align: center; font-weight: bold; background-color: #dde1e4;">
            <!-- Média de Revisões Internas -->
            QTD MÉDIA DE REVISÕES INTERNAS
          </div>
          <div class="card-body d-flex flex-column justify-content-between" style="height: 120px;">
            <div v-if="mediasRevisoesInternas.mediaGeral" class="d-flex justify-content-center align-items-center" style="flex-grow: 1;">
              <h3 style="font-size: 24px; text-align: center;">{{ mediasRevisoesInternas.mediaGeral }} rev</h3>
            </div>
            <button class="btn btn-sm btn-info mt-2 mx-auto" @click="abrirModalAnalises('revisoesInternas', 'Detalhamento de Revisões Internas')">Detalhes</button>
          </div>
        </div>
      </div>
      <!-- MODAL -->
      <ModalAnalises :isVisible="modalsState.revisoesInternas" :title="modalTitle" @close="fecharModal('revisoesInternas')">
        <table class="table table-striped">
          <thead>
            <tr>
              <th>Item Edital</th>
              <th>Subproduto</th>
              <th>Média de Revisões</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(revisao, index) in mediasRevisoesInternas.mediasItemEdital" :key="index">
              <td>{{ revisao.itemEdital }}</td>
              <td class="subproduto-col" :title="revisao.subproduto">{{ revisao.subproduto }}</td>
              <td>{{ revisao.mediaRevisoes }} rev</td>
            </tr>
          </tbody>
        </table>
      </ModalAnalises>

      <!-- Tempo Médio das Expedições -->
      <div class="col-md-2 mb-4">
        <div class="card" style="height: 100%;">
          <div class="card-header d-flex justify-content-center align-items-center" style="height: 60px; text-align: center; font-weight: bold; background-color: #dde1e4;">
            <!-- Tempo Médio de Expedições -->
              TEMPO MÉDIO DE EXPEDIÇÕES DE ANALISES INTERNAS
          </div>
          <div class="card-body d-flex flex-column justify-content-between" style="height: 120px;">
            <div v-if="mediasTemposExpedicoes.mediaGeral" class="d-flex justify-content-center align-items-center" style="flex-grow: 1;">
              <h3 style="font-size: 24px; text-align: center;">{{ mediasTemposExpedicoes.mediaGeral }} dias</h3>
            </div>
            <button class="btn btn-sm btn-info mt-2 mx-auto" @click="abrirModalAnalises('temposExpedicoes', 'Detalhamento de Tempos Médios de Expedições')">Detalhes</button>
          </div>
        </div>
      </div>
      <!-- MODAL -->
      <ModalAnalises :isVisible="modalsState.temposExpedicoes" :title="modalTitle" @close="fecharModal('temposExpedicoes')">
        <table class="table table-striped">
          <thead>
            <tr>
              <th>Item Edital</th>
              <th>Subproduto</th>
              <th>Expedição 0</th>
              <th>Expedição 1</th>
              <th>Expedição 2</th>
              <th>Expedição 3</th>
              <th>Expedição 4</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(media, index) in mediasTemposExpedicoes.mediasTemposExpedicoes" :key="index">
              <td>{{ media.item_edital }}</td>
              <td class="subproduto-col" :title="media.subproduto">{{ media.subproduto }}</td>
              <td v-if="media.mediaExpedicao0">{{ media.mediaExpedicao0 }} dias</td>
              <td v-else>-</td>
              <td v-if="media.mediaExpedicao1">{{ media.mediaExpedicao1 }} dias</td>
              <td v-else>-</td>
              <td v-if="media.mediaExpedicao2">{{ media.mediaExpedicao2 }} dias</td>
              <td v-else>-</td>
              <td v-if="media.mediaExpedicao3">{{ media.mediaExpedicao3 }} dias</td>
              <td v-else>-</td>
              <td v-if="media.mediaExpedicao4">{{ media.mediaExpedicao4 }} dias</td>
              <td v-else>-</td>
            </tr>
          </tbody>
        </table>
      </ModalAnalises>

      <!-- Tempo Médio das Análises Externas -->
      <div class="col-md-2 mb-4">
        <div class="card" style="height: 100%;">
          <div class="card-header d-flex justify-content-center align-items-center" style="height: 60px; text-align: center; font-weight: bold; background-color: #dde1e4;">
            <!-- Tempo Médio de Análises Externas -->
             TEMPO MÉDIO DE ANÁLISES EXTERNAS
          </div>
          <div class="card-body d-flex flex-column justify-content-between" style="height: 120px;">
            <div v-if="mediasTemposAnalisesExternas.mediaGeral" class="d-flex justify-content-center align-items-center" style="flex-grow: 1;">
              <h3 style="font-size: 24px; text-align: center;">{{ mediasTemposAnalisesExternas.mediaGeral }} dias</h3>
            </div>
            <button class="btn btn-sm btn-info mt-2 mx-auto" @click="abrirModalAnalises('temposAnalisesExternas', 'Detalhamento de Tempos Médios de Análises Externas')">Detalhes</button>
          </div>
        </div>
      </div>
      <!-- MODAL -->
      <ModalAnalises :isVisible="modalsState.temposAnalisesExternas" :title="modalTitle" @close="fecharModal('temposAnalisesExternas')">
        <table class="table table-striped">
          <thead>
            <tr>
              <th>Item Edital</th>
              <th>Subproduto</th>
              <th>Análise 0</th>
              <th>Análise 1</th>
              <th>Análise 2</th>
              <th>Análise 3</th>
              <th>Análise 4</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(media, index) in mediasTemposAnalisesExternas.mediasTemposAnalisesExternas" :key="index">
              <td>{{ media.item_edital }}</td>
              <td class="subproduto-col" :title="media.subproduto">{{ media.subproduto }}</td>
              <td v-if="media.mediaAnaliseExt0">{{ media.mediaAnaliseExt0 }} dias</td>
              <td v-else>-</td>
              <td v-if="media.mediaAnaliseExt1">{{ media.mediaAnaliseExt1 }} dias</td>
              <td v-else>-</td>
              <td v-if="media.mediaAnaliseExt2">{{ media.mediaAnaliseExt2 }} dias</td>
              <td v-else>-</td>
              <td v-if="media.mediaAnaliseExt3">{{ media.mediaAnaliseExt3 }} dias</td>
              <td v-else>-</td>
              <td v-if="media.mediaAnaliseExt4">{{ media.mediaAnaliseExt4 }} dias</td>
              <td v-else>-</td>
            </tr>
          </tbody>
        </table>
      </ModalAnalises>
    </div>

    <div class="row">
      <!-- Tempo Médio das Revisões Externas -->
      <div class="col-md-2 mb-4">
        <div class="card" style="height: 100%;">
          <div class="card-header d-flex justify-content-center align-items-center" style="height: 60px; text-align: center; font-weight: bold; background-color: #dde1e4;">
            <!-- Tempo Médio de Revisões Externas -->
            TEMPO MÉDIO DE REVISÕES EXTERNAS
          </div>
          <div class="card-body d-flex flex-column justify-content-between" style="height: 120px;">
            <div v-if="temposMediosRevisoesExternas.mediaGeral" class="d-flex justify-content-center align-items-center" style="flex-grow: 1;">
              <h3 style="font-size: 24px; text-align: center;">{{ temposMediosRevisoesExternas.mediaGeral }} dias</h3>
            </div>
            <button class="btn btn-sm btn-info mt-2 mx-auto" @click="abrirModalAnalises('temposRevisoesExternas', 'Detalhamento do Tempo Médio de Revisão Externa')">Detalhes</button>
          </div>
        </div>
      </div>
      <!-- MODAL -->
      <ModalAnalises :isVisible="modalsState.temposRevisoesExternas" :title="modalTitle" @close="fecharModal('temposRevisoesExternas')">
        <table class="table table-striped">
          <thead>
            <tr>
              <th>Item Edital</th>
              <th>Subproduto</th>
              <th>Revisão 1</th>
              <th>Revisão 2</th>
              <th>Revisão 3</th>
              <th>Revisão 4</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(media, index) in temposMediosRevisoesExternas.mediasTemposExternos" :key="index">
              <td>{{ media.item_edital }}</td>
              <td class="subproduto-col" :title="media.subproduto">{{ media.subproduto }}</td>
              <td v-if="media.mediaRevisao1">{{ media.mediaRevisao1 }} dias</td>
              <td v-else>-</td>
              <td v-if="media.mediaRevisao2">{{ media.mediaRevisao2 }} dias</td>
              <td v-else>-</td>
              <td v-if="media.mediaRevisao3">{{ media.mediaRevisao3 }} dias</td>
              <td v-else>-</td>
              <td v-if="media.mediaRevisao4">{{ media.mediaRevisao4 }} dias</td>
              <td v-else>-</td>
            </tr>
          </tbody>
        </table>
      </ModalAnalises>

      <!-- Médias de Revisões Externas -->
      <div class="col-md-2 mb-4">
        <div class="card" style="height: 100%;">
          <div class="card-header d-flex justify-content-center align-items-center" style="height: 60px; text-align: center; font-weight: bold; background-color: #dde1e4;">
            <!-- Média de Revisões Externas -->
             QTD MÉDIA DE REVISÕES EXTERNAS
          </div>
          <div class="card-body d-flex flex-column justify-content-between" style="height: 120px;">
            <div v-if="mediasRevisoesExternas.mediaGeral" class="d-flex justify-content-center align-items-center" style="flex-grow: 1;">
              <h3 style="font-size: 24px; text-align: center;">{{ mediasRevisoesExternas.mediaGeral }} rev</h3>
            </div>
            <button class="btn btn-sm btn-info mt-2 mx-auto" @click="abrirModalAnalises('revisoesExternas', 'Detalhamento de Revisões Externas')">Detalhes</button>
          </div>
        </div>
      </div>
      <!-- MODAL -->
      <ModalAnalises :isVisible="modalsState.revisoesExternas" :title="modalTitle" @close="fecharModal('revisoesExternas')">
        <table class="table table-striped">
          <thead>
            <tr>
              <th>Item Edital</th>
              <th>Subproduto</th>
              <th>Média de Revisões</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(revisao, index) in mediasRevisoesExternas.mediasItemEdital" :key="index">
              <td>{{ revisao.itemEdital }}</td>
              <td class="subproduto-col" :title="revisao.subproduto">{{ revisao.subproduto }}</td>
              <td>{{ revisao.mediaRevisoes }} rev</td>
            </tr>
          </tbody>
        </table>
      </ModalAnalises>    

      <!-- Tempo de Elaboração (Data de OSE - Data Atual) -->
      <div class="col-md-2 mb-4">
        <div class="card" style="height: 100%;">
          <div class="card-header d-flex justify-content-center align-items-center" style="height: 60px; text-align: center; font-weight: bold; background-color: #dde1e4;">
            <!-- Tempo de Elaboração -->
             TEMPO DE OSE
          </div>
          <div class="card-body d-flex flex-column justify-content-between" style="height: 120px;">
            <div v-if="calcularDiferencaDiasOse(props.empreendimentos)" class="d-flex justify-content-center align-items-center" style="flex-grow: 1;">
              <h3 style="font-size: 24px; text-align: center;">{{ calcularDiferencaDiasOse(props.empreendimentos) }} dias</h3>
            </div>
          </div>
        </div>
      </div>
    </div>
  
    <!-- Tabela de Subprodutos -->
    <div class="row">
      <div class="col-12">
        <div class="card" style="overflow-x: auto;">
          <div class="card-header d-flex justify-content-center align-items-center" style="height: 60px; text-align: center; font-weight: bold; background-color: #f4f4f4">
            <h3>SUBPRODUTOS</h3>
          </div>
          <div class="card-body">
            <div class="scrollbar-top1" ref="scrollbarTop1" style="overflow-x: auto; height: 15px; position: sticky; top: 0; background-color: white; z-index: 1000;">
              <div style="width: 3000px; height: 1px;"></div>
            </div>
            <div class="table-responsive" ref="tableWrapper" style="overflow-x: auto; background-color: white;">
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th class="text-center" style="font-size: 12px; cursor: pointer;" @click="ordenarTabela('cod_siac')">Cod SIAC</th>
                    <th class="text-center" style="font-size: 12px; cursor: pointer;" @click="ordenarTabela('produto')">Produto</th>
                    <th style="font-size: 12px; cursor: pointer; position: sticky; left: 0; background-color: white; z-index: 10;" @click="ordenarTabela('item_edital')">Item Edital</th>
                    <th style="font-size: 12px; position: sticky; left: 90px; background-color: white; z-index: 10;" @click="ordenarTabela('subproduto')" class="subproduto-col; text-center">Descrição do Subproduto</th>
                    <th class="text-center" style="font-size: 12px; cursor: pointer;" @click="ordenarTabela('familia')">Família</th>
                    <th class="text-center" style="font-size: 12px; cursor: pointer;" @click="ordenarTabela('qtd_ose')">Qtd OSE</th>
                    <th class="text-center" style="font-size: 12px; cursor: pointer;" @click="ordenarTabela('r_ose')">R$ OSE</th>
                    <th class="text-center" style="font-size: 12px;">Data de Início Previsto</th>
                    <th class="text-center" style="font-size: 12px;">Data de Entrega Previsto</th>
                    <th class="text-center" style="font-size: 12px;">Qtd de Versões DNIT</th>
                    <th class="text-center" style="font-size: 12px;">Qtd de Versões Externo</th>
                    <th class="text-center" style="font-size: 12px;">Tempo de Elaboração/Revisão - Interno</th>
                    <th class="text-center" style="font-size: 12px;">Tempo de Análise - Interno</th>
                    <th class="text-center" style="font-size: 12px;">Tempo de Elaboração - Externo</th>
                    <th class="text-center" style="font-size: 12px;">Tempo de Análise - Externo</th>
                    <th class="text-center" style="font-size: 12px;">Tempo Total de Elaboração</th>
                    <th class="text-center" style="font-size: 12px;">Tempo Total de Análise</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(item, index) in tabelaItens" :key="index" @click="abrirModal(item)">
                    <td class="text-center">{{ item.cod_siac }}</td>
                    <td class="text-center">{{ item.produto }}</td>
                    <td style="position: sticky; left: 0; background-color: white;">{{ item.item_edital }}</td>
                    <td class="subproduto-col" :title="item.subproduto" style="position: sticky; left: 90px; background-color: white; z-index: 10;">{{ item.subproduto }}</td>
                    <td class="text-center">{{ item.familia }}</td>
                    <td class="text-center">{{ item.qtd_ose }}</td>
                    <td class="text-center">{{ item.r_ose.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' }) }}</td>
                    <td class="text-center">{{ item.data_de_inicio_previsto }}</td>
                    <td class="text-center">{{ item.data_de_entrega_previsto ? item.data_de_entrega_previsto.toLocaleDateString() : '' }}</td>
                    <td class="text-center">{{ contarVersoesDnit(item) }}</td>
                    <td class="text-center">{{ contarVersoesExterno(item) }}</td>
                    <td class="text-center">{{ calcularTempoElaboracaoInterno(item) }}</td>
                    <td class="text-center">{{ calcularTempoAnaliseDnit(item) }}</td>
                    <td class="text-center">{{ calcularTempoElaboracaoExterno(item) }}</td>
                    <td class="text-center">{{ calcularTempoAnaliseExterno(item) }}</td>
                    <td class="text-center">{{ calcularTempoTotalElaboracao(item) }}</td>
                    <td class="text-center">{{ calcularTotalAnalise(item) }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Modal para detalhes item selecionado -->
    <div v-if="modalVisible" class="modal fade show" style="display: block;" tabindex="-1">
      <div class="modal-dialog modal-xl"> 
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title text-center w-100">SUBPRODUTOS DO ESTUDO AMBIENTAL</h5>
            <button type="button" class="btn-close" @click="modalVisible = false"></button>
          </div>
          <div class="modal-body">
            <div class="row">
              <!-- Primeira coluna -->
              <div class="col-md-6">
                <p><strong>Cod SIAC:</strong> {{ selectedItem.cod_siac }}</p>
                <p><strong>Produto:</strong> {{ selectedItem.produto }}</p>
                <p><strong>Item Edital:</strong> {{ selectedItem.item_edital }}</p>
                <p><strong>Subproduto:</strong> {{ selectedItem.subproduto }}</p>
                <p><strong>Família:</strong> {{ selectedItem.familia }}</p>
                <p><strong>Qtd OSE:</strong> {{ selectedItem.qtd_ose }}</p>
                <p><strong>R$ OSE:</strong> {{ selectedItem.r_ose }}</p>
                <p><strong>Data de Início Previsto:</strong> {{ selectedItem.data_de_inicio_previsto }}</p>
                <p><strong>Data de Entrega Previsto:</strong> {{ new Date(selectedItem.data_de_entrega_previsto).toLocaleDateString('pt-BR') }}</p>
                <p><strong>Versão 00 SEI:</strong> {{ selectedItem.versao_00_sei }}</p>
                <p><strong>Data de Entrega Versão 00:</strong> {{ selectedItem.versao_00_data_de_entrega }}</p>
                <p><strong>Versão 00 SEI NT:</strong> {{ selectedItem.versao_00_sei_nt }}</p>
                <p><strong>Data NT Versão 00:</strong> {{ selectedItem.versao_00_data_nt }}</p>
                <p><strong>Versão 00 SEI Ofício:</strong> {{ selectedItem.versao_00_sei_oficio }}</p>
                <p><strong>Data Ofício Versão 00:</strong> {{ selectedItem.versao_00_data_oficio }}</p>
                <p><strong>Versão 01 SEI:</strong> {{ selectedItem.versao_01_sei }}</p>
                <p><strong>Data de Entrega Versão 01:</strong> {{ selectedItem.versao_01_data_de_entrega }}</p>
                <p><strong>Versão 01 SEI NT:</strong> {{ selectedItem.versao_01_sei_nt }}</p>
                <p><strong>Data NT Versão 01:</strong> {{ selectedItem.versao_01_data_nt }}</p>
              </div>

              <!-- Segunda coluna -->
              <div class="col-md-6">
                <p><strong>Versão 01 SEI Ofício:</strong> {{ selectedItem.versao_01_sei_oficio }}</p>
                <p><strong>Data Ofício Versão 01:</strong> {{ selectedItem.versao_01_data_oficio }}</p>
                <p><strong>Versão 02 SEI:</strong> {{ selectedItem.versao_02_sei }}</p>
                <p><strong>Data de Entrega Versão 02:</strong> {{ selectedItem.versao_02_data_de_entrega }}</p>
                <p><strong>Versão 02 SEI NT:</strong> {{ selectedItem.versao_02_sei_nt }}</p>
                <p><strong>Data NT Versão 02:</strong> {{ selectedItem.versao_02_data_nt }}</p>
                <p><strong>Versão 02 SEI Ofício:</strong> {{ selectedItem.versao_02_sei_oficio }}</p>
                <p><strong>Data Ofício Versão 02:</strong> {{ selectedItem.versao_02_data_oficio }}</p>
                <p><strong>Versão 03 SEI:</strong> {{ selectedItem.versao_03_sei }}</p>
                <p><strong>Data de Entrega Versão 03:</strong> {{ selectedItem.versao_03_data_de_entrega }}</p>
                <p><strong>Versão 03 SEI NT:</strong> {{ selectedItem.versao_03_sei_nt }}</p>
                <p><strong>Data NT Versão 03:</strong> {{ selectedItem.versao_03_data_nt }}</p>
                <p><strong>Versão 03 SEI Ofício:</strong> {{ selectedItem.versao_03_sei_oficio }}</p>
                <p><strong>Data Ofício Versão 03:</strong> {{ selectedItem.versao_03_data_oficio }}</p>
                <p><strong>Versão 04 SEI:</strong> {{ selectedItem.versao_04_sei }}</p>
                <p><strong>Data de Entrega Versão 04:</strong> {{ selectedItem.versao_04_data_de_entrega }}</p>
                <p><strong>Versão 04 SEI NT:</strong> {{ selectedItem.versao_04_sei_nt }}</p>
                <p><strong>Data NT Versão 04:</strong> {{ selectedItem.versao_04_data_nt }}</p>
                <p><strong>Versão 04 SEI Ofício:</strong> {{ selectedItem.versao_04_sei_oficio }}</p>
                <p><strong>Data Ofício Versão 04:</strong> {{ selectedItem.versao_04_data_oficio }}</p>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" @click="modalVisible = false">Fechar</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.modal.fade.show {
  background-color: rgba(0, 0, 0, 0.5);
}

.scrollbar-top1::-webkit-scrollbar {
  height: 4px;
}

.scrollbar-top1::-webkit-scrollbar-thumb {
  background-color: #cccccc;
  border-radius: 4px;
}

/* Coluna Subproduto */
.subproduto-col {
  width: 315px; 
  max-width: 315px; 
  white-space: nowrap; 
  overflow: hidden;
  text-overflow: ellipsis; 
}

 /* Estilo para a coluna "Item Edital" quando fixada */
  th[style*="sticky"],
  td[style*="sticky"] {
    background-color: white; 
    z-index: 10; 
  }
 
</style>
