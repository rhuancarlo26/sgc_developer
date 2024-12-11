<script setup>
import { ref, watch, computed } from "vue";
import { useForm } from "@inertiajs/vue3";
import { Head } from "@inertiajs/vue3";
import axios from "axios";
import { onMounted, reactive } from "vue";
import Timeline from "@/Components/Timeline.vue";

const props = defineProps({
  empreendimentos: Object,
  empreendimentos2: Object,
  fm_eia_estudos_empreendimento: Object,
  fm_pba_estudos_empreendimento: Object,
  abio_emp_estudos_311: Object,
  asv_emp_estudos: Object,
  iphan_emp_estudos_521: Object,
  iphan_emp_estudos_531: Object,
});

// classes das timelines
var exibelp = ref(true);
var exibeli = ref(false);
var exibeab = ref(false);
var exibeasv = ref(false);
var exibeiphan = ref(false);

const datahoje = new Date();
function isSerialDate(data) {
  // Verifica se é um número e se está no intervalo possível de datas em série
  return Number.isInteger(data) && data >= 1 && data <= 2958465;
  // 2958465 é o número serial para "31/12/9999" no sistema baseado em 1900.
}
function dentroPrazo(dataBanco) {
  const hoje = new Date();
  const dataComparada = new Date(dataBanco);
  // Define horas, minutos, segundos e milissegundos para zero para comparar apenas as datas
  hoje.setHours(0, 0, 0, 0);
  dataComparada.setHours(0, 0, 0, 0);
  // Retorna 1 se a data é anterior à data de hoje, 0 se posterior
  return dataComparada < hoje ? 1 : 0;
}

function formatDate(ts) {
  const date = new Date(ts * 1000); // Multiplica por 1000 se for um timestamp Unix
  return date.toLocaleDateString("pt-BR"); // Formato brasileiro de data (dd/mm/aaaa)
}

function formatDateFromExcel(serial) {
  // Recurso: convertia de Serial para o formato data - desativado devido à mudança para date no BD
  /**
   *
   const excelEpoch = new Date(1899, 11, 30); // Excel começa em 30/12/1899
   const date = new Date(excelEpoch.getTime() + serial * 86400000); // 86400000 = milissegundos por dia
   return date.toLocaleDateString('pt-BR'); // Formato brasileiro de data (dd/mm/aaaa)
   */
  //   if (isSerialDate(serial)) {
  //     const excelEpoch = new Date(1899, 11, 30); // Excel começa em 30/12/1899
  //    const date = new Date(excelEpoch.getTime() + serial * 86400000); // 86400000 = milissegundos por dia
  //    return date.toLocaleDateString('pt-BR'); // Formato brasileiro de data (dd/mm/aaaa)
  //   } else {
  const date = new Date(serial);
  return date.toLocaleDateString("pt-BR");
  // }
}

/** DEFINIÇÃO DE FASES LP  ================================================================================================================== */
// PT
var status_lp_pt = 0;
if (
  props.empreendimentos2.plano_de_trabalho_entregue &&
  props.empreendimentos2.plano_de_trabalho_aprovado
) {
  status_lp_pt = 1;
} else if (props.empreendimentos2.plano_de_trabalho_entregue) {
  status_lp_pt = 2;
} else {
  status_lp_pt = 0;
}
// OSE
var status_lp_ose = 0;
if (
  props.empreendimentos2.plano_de_trabalho_aprovado &&
  props.empreendimentos2.ose_sei
) {
  status_lp_ose = 1;
} else if (props.empreendimentos2.plano_de_trabalho_entregue) {
  status_lp_ose = 2;
} else {
  status_lp_ose = 0;
}

// Planilha Estudos ----------------------------------------------------------------------------------------------------
var status_estudos = 0;
var status_req_ext = 0;
var status_aut_ext_sei = 0;
var lp_estudos_periodo = "Indeterminado";
var data_req_ext = "A definir";
var data_aut_ext = "A definir";

if (props.fm_eia_estudos_empreendimento.length > 0) {
  // Etapa estudos
  if (props.fm_eia_estudos_empreendimento[0].versao_aceita_sei) {
    status_estudos = 1;
    lp_estudos_periodo =
      formatDateFromExcel(
        props.fm_eia_estudos_empreendimento[0].data_de_inicio_previsto
      ) +
      " - " +
      formatDateFromExcel(
        props.fm_eia_estudos_empreendimento[0].versao_aceita_data
      );
  } else if (
    props.empreendimentos2.plano_de_trabalho_aprovado &&
    dentroPrazo(
      props.fm_eia_estudos_empreendimento[0].data_de_inicio_previsto
    ) == 1
  ) {
    status_estudos = 2;
    lp_estudos_periodo =
      formatDateFromExcel(
        props.fm_eia_estudos_empreendimento[0].data_de_inicio_previsto
      ) +
      " - " +
      formatDateFromExcel(
        props.fm_eia_estudos_empreendimento[0].data_de_entrega_previsto
      );
  } else {
    status_estudos = 0;
    lp_estudos_periodo =
      formatDateFromExcel(
        props.fm_eia_estudos_empreendimento[0].data_de_inicio_previsto
      ) +
      " - " +
      formatDateFromExcel(
        props.fm_eia_estudos_empreendimento[0].data_de_entrega_previsto
      );
  }
  // Etapa Req. Ext.
  if (props.fm_eia_estudos_empreendimento[0].req_ext_sei) {
    status_req_ext = 1;
    data_req_ext = formatDateFromExcel(
      props.fm_eia_estudos_empreendimento[0].req_ext_data
    );
  } else {
    status_req_ext = 0;
  }
  // Etapa Análise Órgão Ext.
  if (props.fm_eia_estudos_empreendimento[0].aut_ext_sei) {
    status_aut_ext_sei = 1;
    data_aut_ext = formatDateFromExcel(
      props.fm_eia_estudos_empreendimento[0].aut_ext_data
    );
  } else if (props.fm_eia_estudos_empreendimento[0].req_ext_sei) {
    status_aut_ext_sei = 2;
  } else {
    status_aut_ext_sei = 0;
  }
}
// LP EMITIDA ===============================
const fases_lp = reactive([
  {
    id: 1,
    fase: "FCA",
    status: props.empreendimentos2.fca_sei !== null ? 1 : 0,
    periodo: formatDateFromExcel(props.empreendimentos2.fca_data),
  },
  {
    id: 2,
    fase: "TR de Estudos",
    status: props.empreendimentos2.tre_sei_dnit !== null ? 1 : 0,
    periodo: formatDateFromExcel(props.empreendimentos2.tre_data),
  },
  {
    id: 3,
    fase: "Plano de Trabalho",
    status: status_lp_pt,
    periodo: formatDateFromExcel(
      props.empreendimentos2.plano_de_trabalho_aprovado
    ),
  },
  {
    id: 4,
    fase: "OSE",
    status: status_lp_ose,
    periodo: formatDateFromExcel(props.empreendimentos2.ose_data),
  },
  {
    id: 5,
    fase: "Estudo Ambiental",
    status: status_estudos,
    periodo: lp_estudos_periodo,
  },
  {
    id: 6,
    fase: "Requerimento Externo",
    status: status_req_ext,
    periodo: data_req_ext,
  },
  {
    id: 7,
    fase: "Análise do Órgão Externo",
    status: status_aut_ext_sei,
    periodo: data_aut_ext,
  },
  {
    id: 8,
    fase: "Licença Prévia",
    status: props.empreendimentos2.lp_sei !== null ? 1 : 0,
    periodo: props.empreendimentos2.lp_data
      ? formatDateFromExcel(props.empreendimentos2.lp_data)
      : "a definir",
  },
]);
/** DEFINIÇÃO DE FASES LP */
/** DEFINIÇÃO DE FASES LI =================================================================================================== */

// Estudos ===============================

var li_status_estudos = 0;
var li_data_estudos = "A definir";
var li_status_req_ext = 0;
var li_data_req_ext = "A definir";
var li_status_aut_ext_sei = 0;
var li_data_aut_ext = "A definir";

if (props.fm_pba_estudos_empreendimento.length > 0) {
  // Etapa estudos
  if (props.fm_pba_estudos_empreendimento[0].versao_aceita_sei) {
    li_status_estudos = 1;
    li_data_estudos =
      formatDateFromExcel(
        props.fm_pba_estudos_empreendimento[0].data_de_inicio_previsto
      ) +
      " - " +
      formatDateFromExcel(
        props.fm_pba_estudos_empreendimento[0].versao_aceita_data
      );
  } else if (
    dentroPrazo(
      props.fm_pba_estudos_empreendimento[0].data_de_inicio_previsto
    ) == 1
  ) {
    li_status_estudos = 2;
    li_data_estudos =
      formatDateFromExcel(
        props.fm_pba_estudos_empreendimento[0].data_de_inicio_previsto
      ) +
      " - " +
      formatDateFromExcel(
        props.fm_pba_estudos_empreendimento[0].data_de_entrega_previsto
      );
  } else {
    li_status_estudos = 0;
    li_data_estudos =
      formatDateFromExcel(
        props.fm_pba_estudos_empreendimento[0].data_de_inicio_previsto
      ) +
      " - " +
      formatDateFromExcel(
        props.fm_pba_estudos_empreendimento[0].data_de_entrega_previsto
      );
  }
  // <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<=========>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
  // Etapa Req. Ext.
  if (props.fm_pba_estudos_empreendimento[0].req_ext_sei) {
    li_status_req_ext = 1;
    li_data_req_ext = formatDateFromExcel(
      props.fm_pba_estudos_empreendimento[0].req_ext_data
    );
  } else {
    li_status_req_ext = 0;
  }
  // Etapa Análise Órgão Ext.
  // <<<<<<<<<<<<<<<<<<<<<<<<<<<<<< Andando >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
  if (props.fm_pba_estudos_empreendimento[0].aut_ext_sei) {
    li_status_aut_ext_sei = 1;
    li_data_aut_ext = formatDateFromExcel(
      props.fm_pba_estudos_empreendimento[0].aut_ext_data
    );
  } else if (props.fm_pba_estudos_empreendimento[0].req_ext_sei) {
    li_status_aut_ext_sei = 2;
  } else {
    li_status_aut_ext_sei = 0;
  }
}

const fases_li = reactive([
  { id: 1, fase: "PBA", status: li_status_estudos, periodo: li_data_estudos },
  {
    id: 2,
    fase: "Requerimento Externo",
    status: li_status_req_ext,
    periodo: li_data_req_ext,
  },
  {
    id: 3,
    fase: "Análise do Órgão Externo",
    status: li_status_aut_ext_sei,
    periodo: li_data_aut_ext,
  },
  {
    id: 4,
    fase: "Licença de Instalação",
    status: props.empreendimentos2.li_sei ? 1 : 0,
    periodo: props.empreendimentos2.li_data
      ? formatDateFromExcel(props.empreendimentos2.li_data)
      : "a definir",
  },
]);
/** ===================================================================================================== DEFINIÇÃO DE FASES LI */
/** DEFINIÇÃO DE FASES ABIO =================================================================================================== */
// item_edital (P), versao_aceita_sei (BO), versao_aceita_data (BP), data_de_inicio_previsto (AI), data_de_entrega_previsto (AJ)
// req_ext_data (BR), req_ext_sei (BQ), aut_ext_sei(CI), aut_ext_data(CJ)
// Plano de trabalho => item_edital = 3.1.1

var abio_status_trabalho = 0;
var abio_status_req_ext = 0;
var abio_analise_orgao_ext = 0;
var abio_emitida = 0;

var periodo_abio_planotrabalho = "A definir";
var periodo_abio_planotrabalho = "A definir";
var periodo_abio_req_ext = "A definir";
var periodo_abio_req_ext = "A definir";
var periodo_analise_orgao_ext = "A definir";
var abio_emitida_periodo = "A definir";

if (props.abio_emp_estudos_311.length > 0) {
  var data_de_inicio_previsto_formatada =
    props.abio_emp_estudos_311[0].data_de_inicio_previsto * 86400000;
  // console.log(props.abio_emp_estudos_311[0].data_de_inicio_previsto*86400000);
  if (props.abio_emp_estudos_311[0].versao_aceita_sei) {
    abio_status_trabalho = 1;
    if (props.abio_emp_estudos_311[0].data_de_inicio_previsto) {
      periodo_abio_planotrabalho = formatDateFromExcel(
        props.abio_emp_estudos_311[0].data_de_inicio_previsto
      );
    }
    if (props.abio_emp_estudos_311[0].versao_aceita_data) {
      periodo_abio_planotrabalho +=
        " - " +
        formatDateFromExcel(props.abio_emp_estudos_311[0].versao_aceita_data);
    }
  } else if (data_de_inicio_previsto_formatada > datahoje.getTime()) {
    abio_status_trabalho = 2;
    if (props.abio_emp_estudos_311[0].data_de_inicio_previsto) {
      periodo_abio_planotrabalho = formatDateFromExcel(
        props.abio_emp_estudos_311[0].data_de_inicio_previsto
      );
    }
    if (props.abio_emp_estudos_311[0].data_de_entrega_previsto) {
      periodo_abio_planotrabalho +=
        " - " +
        formatDateFromExcel(
          props.abio_emp_estudos_311[0].data_de_entrega_previsto
        );
    }
  } else if (data_de_inicio_previsto_formatada < datahoje.getTime()) {
    abio_status_trabalho = 0;
    if (props.abio_emp_estudos_311[0].data_de_inicio_previsto) {
      periodo_abio_planotrabalho = formatDateFromExcel(
        props.abio_emp_estudos_311[0].data_de_inicio_previsto
      );
    }
    if (props.abio_emp_estudos_311[0].data_de_entrega_previsto) {
      periodo_abio_planotrabalho +=
        " - " +
        formatDateFromExcel(
          props.abio_emp_estudos_311[0].data_de_entrega_previsto
        );
    }
  }
  // abio_status_req_ext
  if (
    props.abio_emp_estudos_311[0].req_ext_data &&
    props.abio_emp_estudos_311[0].req_ext_sei
  ) {
    abio_status_req_ext = 1;
    periodo_abio_req_ext = formatDateFromExcel(
      props.abio_emp_estudos_311[0].req_ext_data
    );
  } else if (props.abio_emp_estudos_311[0].req_ext_data) {
    abio_status_req_ext = 2;
    periodo_abio_req_ext = formatDateFromExcel(
      props.abio_emp_estudos_311[0].req_ext_data
    );
  } else {
    periodo_abio_req_ext = formatDateFromExcel(
      props.abio_emp_estudos_311[0].req_ext_data
    );
  }
  // Análise Órgão Ext.
  if (props.abio_emp_estudos_311[0].aut_ext_data) {
    periodo_analise_orgao_ext = formatDateFromExcel(
      props.abio_emp_estudos_311[0].aut_ext_data
    );
  }
  if (props.abio_emp_estudos_311[0].aut_ext_sei) {
    abio_analise_orgao_ext = 1;
  } else if (props.abio_emp_estudos_311[0].req_ext_sei) {
    abio_analise_orgao_ext = 2;
  }
  // ABIO Emitida
  if (props.abio_emp_estudos_311[0].aut_ext_sei) {
    abio_emitida = 1;
  }
  if (props.abio_emp_estudos_311[0].aut_ext_data) {
    abio_emitida_periodo = formatDateFromExcel(
      props.abio_emp_estudos_311[0].aut_ext_data
    );
  }
}

// correções forçadas
// if (abio_status_trabalho == 2) {
//     abio_status_req_ext = 0;
// }
// if (abio_status_req_ext == 2) {
//     abio_analise_orgao_ext = 0;
// }

const fases_abio = reactive([
  {
    id: 1,
    fase: "Plano de Trabalho",
    status: abio_status_trabalho,
    periodo: periodo_abio_planotrabalho,
  },
  {
    id: 2,
    fase: "Requerimento Externo",
    status: abio_status_req_ext,
    periodo: periodo_abio_req_ext,
  },
  {
    id: 3,
    fase: "Análise do Órgão Externo",
    status: abio_analise_orgao_ext,
    periodo: periodo_analise_orgao_ext,
  },
  {
    id: 4,
    fase: "ABio",
    status: abio_emitida ? 1 : 0,
    periodo: abio_emitida_periodo,
  },
]);
/** ================================================================================================== DEFINIÇÃO DE FASES ABIO  */
/** DEFINIÇÃO DE FASES ASV ==================================================================================================== */
// familia (Q), versao_aceita_sei (BO), versao_aceita_data (BP), data_de_inicio_previsto (AI), data_de_entrega_previsto (AJ)
// req_ext_data (BR), req_ext_sei (BQ), aut_ext_sei(CI), aut_ext_data(CJ)
// Plano de trabalho => familia = ASV

var asv_inv_florestal = 0;
var asv_status_req_ext = 0;
var asv_analise_orgao_ext = 0;
var asv_emitida = 0;

var periodo_asv_inv_florestal = "A definir";
var periodo_asv_req_ext = "A definir";
var periodo_asv_analise_orgao_ext = "A definir";
var asv_emitida_periodo = "A definir";

if (props.asv_emp_estudos.length > 0) {
  var data_de_inicio_previsto_formatada =
    props.asv_emp_estudos[0].data_de_inicio_previsto * 86400000;
  if (props.asv_emp_estudos[0].versao_aceita_sei) {
    asv_inv_florestal = 1;
    if (props.asv_emp_estudos[0].data_de_inicio_previsto) {
      periodo_asv_inv_florestal = formatDateFromExcel(
        props.asv_emp_estudos[0].data_de_inicio_previsto
      );
    }
    if (props.asv_emp_estudos[0].versao_aceita_data) {
      periodo_asv_inv_florestal +=
        " - " +
        formatDateFromExcel(props.asv_emp_estudos[0].versao_aceita_data);
    }
  } else if (data_de_inicio_previsto_formatada > datahoje.getTime()) {
    asv_inv_florestal = 2;
    if (props.asv_emp_estudos[0].data_de_inicio_previsto) {
      periodo_asv_inv_florestal = formatDateFromExcel(
        props.asv_emp_estudos[0].data_de_inicio_previsto
      );
    }
    if (props.asv_emp_estudos[0].data_de_entrega_previsto) {
      periodo_asv_inv_florestal +=
        " - " +
        formatDateFromExcel(props.asv_emp_estudos[0].data_de_entrega_previsto);
    }
  } else if (data_de_inicio_previsto_formatada < datahoje.getTime()) {
    asv_inv_florestal = 0;
    if (props.asv_emp_estudos[0].data_de_inicio_previsto) {
      periodo_asv_inv_florestal = formatDateFromExcel(
        props.asv_emp_estudos[0].data_de_inicio_previsto
      );
    }
    if (props.asv_emp_estudos[0].data_de_entrega_previsto) {
      periodo_asv_inv_florestal +=
        " - " +
        formatDateFromExcel(props.asv_emp_estudos[0].data_de_entrega_previsto);
    }
  }
  // asv_status_req_ext
  if (
    props.asv_emp_estudos[0].req_ext_data & props.asv_emp_estudos[0].req_ext_sei
  ) {
    asv_status_req_ext = 1;
    if (props.asv_emp_estudos[0].req_ext_data) {
      periodo_asv_req_ext = formatDateFromExcel(
        props.asv_emp_estudos[0].req_ext_data
      );
    }
  } else if (props.asv_emp_estudos[0].req_ext_data) {
    asv_status_req_ext = 2;
    if (props.asv_emp_estudos[0].req_ext_data) {
      periodo_asv_req_ext = formatDateFromExcel(
        props.asv_emp_estudos[0].req_ext_data
      );
    }
  } else {
    if (props.asv_emp_estudos[0].req_ext_data) {
      periodo_asv_req_ext = formatDateFromExcel(
        props.asv_emp_estudos[0].req_ext_data
      );
    }
  }
  // Análise Órgão Ext.
  if (props.asv_emp_estudos[0].aut_ext_data) {
    periodo_asv_analise_orgao_ext = formatDateFromExcel(
      props.asv_emp_estudos[0].aut_ext_data
    );
  }
  if (props.asv_emp_estudos[0].aut_ext_sei) {
    asv_analise_orgao_ext = 1;
  } else if (props.asv_emp_estudos[0].req_ext_sei) {
    asv_analise_orgao_ext = 2;
  }
  // ASV Emitida
  if (props.asv_emp_estudos[0].aut_ext_sei) {
    asv_emitida = 1;
  }
  if (props.asv_emp_estudos[0].aut_ext_data) {
    asv_emitida_periodo = formatDateFromExcel(
      props.asv_emp_estudos[0].aut_ext_data
    );
  }
}

const fases_asv = reactive([
  {
    id: 1,
    fase: "Inventário Florestal",
    status: asv_inv_florestal,
    periodo: periodo_asv_inv_florestal,
  },
  {
    id: 2,
    fase: "Requerimento Externo",
    status: asv_status_req_ext,
    periodo: periodo_asv_req_ext,
  },
  {
    id: 3,
    fase: "Análise do Órgão Externo",
    status: asv_analise_orgao_ext,
    periodo: periodo_asv_analise_orgao_ext,
  },
  {
    id: 4,
    fase: "ASV",
    status: asv_emitida ? 1 : 0,
    periodo: asv_emitida_periodo,
  },
]);

/** =================================================================================================== DEFINIÇÃO DE FASES ASV  */
/** DEFINIÇÃO DE FASES IPHAN ================================================================================================== */
/**
 * Empreendimentos:
	BA: fca_iphan_sei | BB: fca_iphan_data | BC: tre_iphan_sei | BD: tre_iphan_data
*/
/**
 * Estudos:
    'item_edital' => P |
    'data_de_inicio_previsto' => AI | 'data_de_entrega_previsto' => AJ
    'versao_aceita_sei' => BO | 'versao_aceita_data' => BP | 'req_ext_sei', => BQ | 'req_ext_data' => BR
    'aut_ext_sei' => CI | 'aut_ext_data' => CJ
 */
// console.log('-------------------------------');
// console.log(props.iphan_emp_estudos_521);
// console.log('-------------------------------');
// console.log(props.iphan_emp_estudos_531);
// console.log('-------------------------------');

var fca_iphan_status = 0;
var fca_iphan_data = "A definir";
var tre_iphan_status = 0;
var tre_iphan_data = "A definir";

if (props.empreendimentos2.processo_iphan) {
  fca_iphan_status = 1;
}
if (props.empreendimentos2.situacao_iphan) {
  fca_iphan_data = formatDateFromExcel(props.empreendimentos2.situacao_iphan);
}
if (props.empreendimentos2.tre_sei_dnit) {
  tre_iphan_status = 1;
}
if (props.empreendimentos2.tre_data) {
  tre_iphan_data = formatDateFromExcel(props.empreendimentos2.tre_data);
}

var paipa_iphan_status = 0;
var paipa_iphan_data = "A definir";
var reqext_iphan_status = 0;
var reqext_iphan_data = "A definir";
var analise_iphan_status = 0;
var analise_iphan_data = "A definir";
var portaria_iphan_status = 0;
var portaria_iphan_data = "A definir";

if (props.iphan_emp_estudos_521.length > 0) {
  var data_de_inicio_previsto_formatada =
    props.iphan_emp_estudos_521[0].data_de_inicio_previsto * 86400000;
  // PAIPA
  if (props.iphan_emp_estudos_521[0].versao_aceita_sei) {
    paipa_iphan_status = 1;
    if (props.iphan_emp_estudos_521[0].data_de_inicio_previsto) {
      paipa_iphan_data = formatDateFromExcel(
        props.iphan_emp_estudos_521[0].data_de_inicio_previsto
      );
    }
    if (props.iphan_emp_estudos_521[0].versao_aceita_data) {
      paipa_iphan_data +=
        " - " +
        formatDateFromExcel(props.iphan_emp_estudos_521[0].versao_aceita_data);
    }
  } else if (data_de_inicio_previsto_formatada > datahoje.getTime()) {
    paipa_iphan_status = 2;
    if (props.iphan_emp_estudos_521[0].data_de_inicio_previsto) {
      paipa_iphan_data = formatDateFromExcel(
        props.iphan_emp_estudos_521[0].data_de_inicio_previsto
      );
    }
    if (props.iphan_emp_estudos_521[0].data_de_entrega_previsto) {
      paipa_iphan_data +=
        " - " +
        formatDateFromExcel(
          props.iphan_emp_estudos_521[0].data_de_entrega_previsto
        );
    }
  } else if (data_de_inicio_previsto_formatada < datahoje.getTime()) {
    paipa_iphan_status = 0;
    if (props.iphan_emp_estudos_521[0].data_de_inicio_previsto) {
      paipa_iphan_data = formatDateFromExcel(
        props.iphan_emp_estudos_521[0].data_de_inicio_previsto
      );
    }
    if (props.iphan_emp_estudos_521[0].data_de_entrega_previsto) {
      paipa_iphan_data +=
        " - " +
        formatDateFromExcel(
          props.iphan_emp_estudos_521[0].data_de_entrega_previsto
        );
    }
  }
  // REQ EXT PAIPA
  if (props.iphan_emp_estudos_521[0].req_ext_sei) {
    reqext_iphan_status = 1;
  }
  if (props.iphan_emp_estudos_521[0].req_ext_data) {
    reqext_iphan_data = formatDateFromExcel(
      props.iphan_emp_estudos_521[0].req_ext_data
    );
  }
  // Análise PAIPA
  if (props.iphan_emp_estudos_521[0].aut_ext_sei) {
    analise_iphan_status = 1;
  }
  if (props.iphan_emp_estudos_521[0].aut_ext_data) {
    analise_iphan_data = formatDateFromExcel(
      props.iphan_emp_estudos_521[0].aut_ext_data
    );
  }
  if (
    props.iphan_emp_estudos_521[0].req_ext_sei &
    !props.iphan_emp_estudos_521[0].aut_ext_sei
  ) {
    analise_iphan_status = 2;
  }
  if (!props.iphan_emp_estudos_521[0].req_ext_sei) {
    analise_iphan_status = 0;
  }
  // Portaria IPHAN
  if (props.iphan_emp_estudos_521[0].aut_ext_sei) {
    portaria_iphan_status = 1;
  }
  if (props.iphan_emp_estudos_521[0].aut_ext_data) {
    portaria_iphan_data = formatDateFromExcel(
      props.iphan_emp_estudos_521[0].aut_ext_data
    );
  }
}
// RAIPA
var raipa_iphan_status = 0;
var raipa_iphan_data = "A definir";
var extrep_raipa_iphan_status = 0;
var extrep_raipa_iphan_data = "A definir";
var analise_raipa_iphan_status = 0;
var analise_raipa_iphan_data = "A definir";
var anuencia_lp_iphan_status = 0;
var anuencia_lp_iphan_data = "A definir";
if (props.iphan_emp_estudos_531.length > 0) {
  var data_de_inicio_previsto_formatada =
    props.iphan_emp_estudos_531[0].data_de_inicio_previsto * 86400000;
  // RAIPA
  if (props.iphan_emp_estudos_531[0].versao_aceita_sei) {
    raipa_iphan_status = 1;
    if (props.iphan_emp_estudos_531[0].data_de_inicio_previsto) {
      raipa_iphan_data = formatDateFromExcel(
        props.iphan_emp_estudos_531[0].data_de_inicio_previsto
      );
    }
    if (props.iphan_emp_estudos_531[0].versao_aceita_data) {
      raipa_iphan_data +=
        " - " +
        formatDateFromExcel(props.iphan_emp_estudos_531[0].versao_aceita_data);
    }
  } else if (data_de_inicio_previsto_formatada > datahoje.getTime()) {
    raipa_iphan_status = 2;
    if (props.iphan_emp_estudos_531[0].data_de_inicio_previsto) {
      raipa_iphan_data = formatDateFromExcel(
        props.iphan_emp_estudos_531[0].data_de_inicio_previsto
      );
    }
    if (props.iphan_emp_estudos_531[0].data_de_entrega_previsto) {
      raipa_iphan_data +=
        " - " +
        formatDateFromExcel(
          props.iphan_emp_estudos_531[0].data_de_entrega_previsto
        );
    }
  } else if (data_de_inicio_previsto_formatada < datahoje.getTime()) {
    raipa_iphan_status = 0;
    if (props.iphan_emp_estudos_531[0].data_de_inicio_previsto) {
      raipa_iphan_data = formatDateFromExcel(
        props.iphan_emp_estudos_531[0].data_de_inicio_previsto
      );
    }
    if (props.iphan_emp_estudos_531[0].data_de_entrega_previsto) {
      raipa_iphan_data +=
        " - " +
        formatDateFromExcel(
          props.iphan_emp_estudos_531[0].data_de_entrega_previsto
        );
    }
  }
  // REQ EXT RAIPA
  if (props.iphan_emp_estudos_531[0].req_ext_sei) {
    extrep_raipa_iphan_status = 1;
  }
  if (props.iphan_emp_estudos_531[0].req_ext_data) {
    extrep_raipa_iphan_data = formatDateFromExcel(
      props.iphan_emp_estudos_531[0].req_ext_data
    );
  }
  // Análise IPHAN RAIPA
  if (props.iphan_emp_estudos_531[0].aut_ext_sei) {
    analise_raipa_iphan_status = 1;
  } else if (
    props.iphan_emp_estudos_531[0].req_ext_sei &
    !props.iphan_emp_estudos_531[0].aut_ext_sei
  ) {
    analise_raipa_iphan_status = 2;
  } else if (!props.iphan_emp_estudos_531[0].req_ext_sei) {
    analise_raipa_iphan_status = 0;
  }
  if (props.iphan_emp_estudos_531[0].aut_ext_data) {
    analise_raipa_iphan_data = formatDateFromExcel(
      props.iphan_emp_estudos_531[0].aut_ext_data
    );
  }
  // Anuência LP IPHAN RAIPA
  if (props.iphan_emp_estudos_531[0].aut_ext_sei) {
    anuencia_lp_iphan_status = 1;
  }
  if (props.iphan_emp_estudos_531[0].aut_ext_data) {
    anuencia_lp_iphan_data = formatDateFromExcel(
      props.iphan_emp_estudos_531[0].aut_ext_data
    );
  }
  /**
     versao_aceita_sei' => BO | 'versao_aceita_data' => BP | 'req_ext_sei', => BQ | 'req_ext_data' => BR
    'aut_ext_sei' => CI | 'aut_ext_data' => CJ
     */
}

const fases_iphan = reactive([
  { id: 1, fase: "FCA", status: fca_iphan_status, periodo: fca_iphan_data },
  {
    id: 2,
    fase: "TR de Estudos",
    status: tre_iphan_status,
    periodo: tre_iphan_data,
  },
  {
    id: 3,
    fase: "PAIPA",
    status: paipa_iphan_status,
    periodo: paipa_iphan_data,
  },
  {
    id: 4,
    fase: "Requerimento Externo do PAIPA",
    status: reqext_iphan_status,
    periodo: reqext_iphan_data,
  },
  {
    id: 5,
    fase: "Análise do Órgão Externo",
    status: analise_iphan_status,
    periodo: analise_iphan_data,
  },
  {
    id: 6,
    fase: "Portaria IPHAN",
    status: portaria_iphan_status,
    periodo: portaria_iphan_data,
  },
  {
    id: 7,
    fase: "RAIPA",
    status: raipa_iphan_status,
    periodo: raipa_iphan_data,
  },
  {
    id: 8,
    fase: "Requerimento Externo do RAIPA",
    status: extrep_raipa_iphan_status,
    periodo: extrep_raipa_iphan_data,
  },
  {
    id: 9,
    fase: "Análise do Órgão Externo",
    status: analise_raipa_iphan_status,
    periodo: analise_raipa_iphan_data,
  },
  {
    id: 10,
    fase: "Anuência do IPHAN p/ LP",
    status: anuencia_lp_iphan_status,
    periodo: anuencia_lp_iphan_data,
  },
]);

/** ================================================================================================= DEFINIÇÃO DE FASES IPHAN  */

onMounted(() => {
  exibelp.value = true; // Define LP como selecionada por padrão
  exibeli.value = false;
  exibeab.value = false;
  exibeasv.value = false;
  exibeiphan.value = false;
});
</script>
<template>
  <div class="">
    <!-- Filtro "Escolher Etapa" -->
    <div class="row">
      <div class="col-md-2">
        <div class="p-3 border" style="width: 220px; background-color: white">
          <div class="text-center">
            <b class="text-center">SELECIONAR ETAPA</b>
            <div
              class="etapas-container mt-4 d-flex flex-column align-items-center"
            >
              <button
                class="etapa-btn my-2"
                @click="
                  exibelp = true;
                  exibeli = false;
                  exibeab = false;
                  exibeasv = false;
                  exibeiphan = false;
                "
              >
                LP
              </button>
              <button
                class="etapa-btn my-2"
                @click="
                  exibelp = false;
                  exibeli = true;
                  exibeab = false;
                  exibeasv = false;
                  exibeiphan = false;
                "
              >
                LI
              </button>
              <button
                class="etapa-btn my-2"
                @click="
                  exibelp = false;
                  exibeli = false;
                  exibeab = true;
                  exibeasv = false;
                  exibeiphan = false;
                "
              >
                ABIO
              </button>
              <button
                class="etapa-btn my-2"
                @click="
                  exibelp = false;
                  exibeli = false;
                  exibeab = false;
                  exibeasv = true;
                  exibeiphan = false;
                "
              >
                ASV
              </button>
              <button
                class="etapa-btn my-2"
                @click="
                  exibelp = false;
                  exibeli = false;
                  exibeab = false;
                  exibeasv = false;
                  exibeiphan = true;
                "
              >
                IPHAN
              </button>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-10">
        <!-- Conteúdo do Empreendimento -->
          <h1 class="text-center">
            EMPREENDIMENTO - {{ empreendimentos2.cod_emp }}
          </h1>

          <Timeline
            class="my-4"
            :licenciamento="'LP: fases'"
            :fases="fases_lp"
            :empreendimento_id="empreendimentos2.id"
            v-show="exibelp"
          />
          <Timeline
            class="my-4"
            :licenciamento="'LI: fases'"
            :fases="fases_li"
            :empreendimento_id="empreendimentos2.id"
            v-show="exibeli"
          />
          <Timeline
            class="my-4"
            :licenciamento="'ABIO: fases'"
            :fases="fases_abio"
            :empreendimento_id="empreendimentos2.id"
            v-show="exibeab"
          />
          <Timeline
            class="my-4"
            :licenciamento="'ASV: fases'"
            :fases="fases_asv"
            :empreendimento_id="empreendimentos2.id"
            v-show="exibeasv"
          />
          <Timeline
            class="my-4"
            :licenciamento="'IPHAN: fases'"
            :fases="fases_iphan"
            :empreendimento_id="empreendimentos2.id"
            v-show="exibeiphan"
          />
      </div>
    </div>
  </div>
</template>
<style scoped>
/* Estilo principal de fundo das abas e das abas */
.nav-tabs .nav-link {
  background-color: #fffefe; /* Cor de fundo leve para as abas inativas */
  border-radius: 5px 5px 0 0; /* Bordas arredondadas nas abas superiores */
  color: #555; /* Cor do texto */
  box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1); /* Sombra para dar destaque */
}

.nav-tabs .nav-link.active {
  background-color: #f2f2f2; /* Cor de fundo da aba ativa */
  border-color: #f2f2f2 #f2f2f2 transparent; /* Conectar visualmente a aba ao conteúdo */
  color: #000; /* Cor do texto da aba ativa */
}

.tab-content {
  background-color: #fcfcfc; /* Cor de fundo leve para o conteúdo das abas */
  border-radius: 0 0 5px 5px; /* Bordas arredondadas no conteúdo das abas */
  border-top: none; /* Remover a borda superior para mesclar com a aba ativa */
  border: 2px solid #e4e2e2; /* Borda para o conteúdo da aba */
  padding: 0 10px;
  box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1); /* Sombra para dar destaque */
}
/*  */

.d-flex {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.text-center {
  text-align: center;
  margin-left: auto;
  margin-right: auto;
}

h1 {
  margin: 0;
}

/* Estilização da lista como dropdown */
.dropdown-list {
  position: absolute;
  background-color: white;
  border: 1px solid #ddd;
  padding: 10px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  border-radius: 5px;
  max-height: 200px; /* Limite de altura para a lista */
  overflow-y: auto; /* Habilita rolagem se necessário */
  z-index: 1000;
}

.dropdown-list ul {
  list-style-type: none;
  padding-left: 0;
}

.dropdown-list ul li {
  margin-bottom: 10px;
}

.btn-group {
  display: flex;
  justify-content: flex-start;
}

.card ul {
  list-style-type: none;
  padding-left: 0;
}

.card ul li {
  margin-bottom: 10px;
}

/* Estilo para a organização dos filtros e etapas na lateral esquerda */
.etapas-container {
  display: flex;
  flex-direction: column;
  align-items: center;
}

.etapa-btn {
  padding: 10px 20px;
  background-color: #ffffff;
  border: 1px solid #ccc;
  border-radius: 5px;
  font-weight: bold;
  cursor: pointer;
  transition: background-color 0.3s ease;
  width: 100%;
  max-width: 200px; /* Ajusta a largura dos botões */
}

.etapa-btn:hover {
  background-color: #e2e6ea;
}

.etapa-btn:focus {
  outline: none;
  background-color: #dae0e5;
}

.text-center {
  text-align: center;
}

/* Estilo para o campo de busca */
.search-input {
  width: 100%;
  padding: 8px;
  margin-bottom: 10px;
  box-sizing: border-box;
}

.dropdown-list {
  width: 350px;
  max-height: 600px;
  font-size: 16px;
  padding: 15px;
}

/* Estilo para o campo de busca */
.search-input {
  width: 100%;
  padding: 8px;
  margin-bottom: 10px;
  box-sizing: border-box;
}

.dropdown-list ul {
  list-style-type: none;
  padding-left: 0s;
}

.dropdown-list ul li {
  display: flex;
  align-items: center;
}

.icon {
  margin-right: 12px;
}
</style>