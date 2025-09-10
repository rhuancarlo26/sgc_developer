<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import NavbarContrato from '@/Pages/Sgc/Contratada/NavbarContrato.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import InputError from '@/Components/InputError.vue';
import NavButton from '@/Components/NavButton.vue';
import { ref, computed } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import DadosGeraisEditar from './Componentes/DadosGeraisEditar.vue';
import ModulosAmostraisEditar from './Componentes/ModulosAmostraisEditar.vue';
import QueloniosCrocodilianosEditar from './Componentes/QueloniosCrocodilianosEditar.vue';
import FaunaCavernicolaEditar from './Componentes/FaunaCavernicolaEditar.vue';
import MetodologiaEditar from './Componentes/MetodologiaEditar.vue';
import ResultadosEditar from './Componentes/FaunaResultadosEditar.vue';

const props = defineProps({
  campanha: {
    type: Object,
    default: () => ({}),
  },
  contrato: [Number, String],
  produto: String,
  contratos: Object,
  empreendimentos: Array,
  abios: {
    type: Array,
    default: () => [],
  },
  profissionais: {
    type: Array,
    default: () => [],
  },
  ufs: {
    type: Array,
    default: () => [],
  },
  biomas: {
    type: Array,
    default: () => [],
  },
  comentarios: {
    type: Array,
    default: () => [],
  },
});

const activeTab = ref('apresentacao');
const subStep = ref(1);
const showModal = ref(false);
const modalEtapa = ref('');

// Mapeamento de etapas para labels
const etapas = [
  { value: 'apresentacao_geral', label: 'Apresentação Geral' },
  { value: 'caracterizacao_area', label: 'Dados Gerais' },
  { value: 'modulos_amostrais', label: 'Módulos Amostrais' },
  { value: 'pontos_quelo_crocod', label: 'Pontos Quelo/Crocod' },
  { value: 'pontos_cavernicola', label: 'Pontos Cavernícola' },
  { value: 'metodologia', label: 'Metodologia' },
  { value: 'resultados', label: 'Resultados' },
  { value: 'anexos', label: 'Anexos' },
];

// Mapeamento de activeTab para valores de etapa
const etapaMap = {
  apresentacao: ['apresentacao_geral', 'caracterizacao_area', 'modulos_amostrais', 'pontos_quelo_crocod', 'pontos_cavernicola'],
  metodologia: ['metodologia'],
  resultados: ['resultados'],
  anexos: ['anexos'],
};

// Formulário principal para edição da campanha
const form = useForm({
  cod_emp: props.campanha.cod_emp || '',
  subproduto: props.campanha.subproduto || '',
  data_campanha_inicial: props.campanha.data_ini || '',
  data_campanha_final: props.campanha.data_fim || '',
  periodo: props.campanha.periodo || '',
  obs: props.campanha.observacoes || '',
  abio: { id_abio: null },
  profissional: { profissional: null, grupo_faunistico: null },
  abios: props.campanha.abios?.map(abio => ({
    id: abio.id,
    abio: {
      id: abio.n_abio || abio.abio?.licenca?.id || abio.id,
      numero_licenca: abio.abio?.licenca?.numero_licenca || abio.abio?.numero_licenca || 'N/A',
    },
  })) || [],
  profissionais: props.campanha.profissionais?.map(prof => ({
    id: prof.id,
    profissional: {
      id: prof.profissional?.id || prof.id,
      profissional: prof.profissional?.profissional || 'N/A',
      formacao: prof.profissional?.formacao || 'N/A',
    },
    grupo_faunistico: prof.grupo_faunistico || 'N/A',
  })) || [],
  modulos_amostrais: props.campanha.modulos_amostrais?.map(modulo => ({
    id: modulo.id || null,
    data_cadastro: modulo.data_cadastro || '',
    tamanho_modulo: modulo.tamanho_modulo || '',
    uf: modulo.uf || '',
    municipio: modulo.municipio || '',
    bioma: modulo.bioma || '',
    fitofisionomia: modulo.fitofisionomia || '',
    latitude_inicial: modulo.latitude_inicial || null,
    longitude_inicial: modulo.longitude_inicial || null,
    latitude_final: modulo.latitude_final || null,
    longitude_final: modulo.longitude_final || null,
    obs: modulo.obs || '',
    arquivo: null,
    arquivo_existente: modulo.arquivo_existente || null,
  })) || [],
  pontos_quelo_crocod: props.campanha.pontos_quelo_crocod?.map(ponto => ({
    id: ponto.id || null,
    ponto_de_coleta: ponto.ponto_de_coleta || '',
    nome_curso_hidrico: ponto.nome_curso_hidrico || '',
    coordenadas: ponto.coordenadas || '',
    bacia: ponto.bacia || '',
    profundidade: ponto.profundidade || null,
    largura: ponto.largura || null,
    tipo_substrato: ponto.tipo_substrato || '',
  })) || [],
  pontos_cavernicola: props.campanha.pontos_cavernicola?.map(ponto => ({
    id: ponto.id || null,
    cavidade: ponto.cavidade || '',
    latitude: ponto.latitude || null,
    longitude: ponto.longitude || null,
    distancia_eixo_rodovia: ponto.distancia_eixo_rodovia || null,
    formacao_associada: ponto.formacao_associada || '',
    temperatura_media_interna: ponto.temperatura_media_interna || null,
    temperatura_media_externa: ponto.temperatura_media_externa || null,
    umidade_relativa_interna: ponto.umidade_relativa_interna || null,
    umidade_relativa_externa: ponto.umidade_relativa_externa || null,
  })) || [],
  nao_se_aplica: props.campanha.nao_se_aplica || false,
  metodologias: props.campanha.metodologias?.map(met => ({
    id: met.id || null,
    grupo_faunistico: met.grupo_faunistico || '',
    metodologia: met.metodologia || '',
  })) || [],
  resultados: props.campanha.resultados?.map(res => ({
    modulo: res.modulo || '',
    parcela: res.parcela || '',
    id_armadilha: res.id_armadilha || '',
    grupo_amostrado: res.grupo_amostrado || '',
    data_registro: res.data_registro || '',
    hora_registro: res.hora_registro || '',
    categoria: res.categoria || '',
    classe: res.classe || '',
    ordem: res.ordem || '',
    familia: res.familia || '',
    genero: res.genero || '',
    especie: res.especie || '',
    nome_comum: res.nome_comum || '',
    sexo: res.sexo || '',
    faixa_etaria: res.faixa_etaria || '',
    qnt_individuos: res.qnt_individuos || null,
    num_marcacao: res.num_marcacao || '',
    coletado: res.coletado || '',
    num_tombamento: res.num_tombamento || '',
    dados_biometricos: res.dados_biometricos || '',
    comp_total: res.comp_total || null,
    cabeca: res.cabeca || null,
    cauda: res.cauda || null,
    femur: res.femur || null,
    orelha: res.orelha || null,
    peso: res.peso || null,
    status_conservacao_federal: res.status_conservacao_federal || '',
    status_conservacao_iucn: res.status_conservacao_iucn || '',
  })) || [],
  consideracoes: props.campanha.consideracoes || '',
  planilha: null,
  anexos: {
    anuencia_proprietarios: null,
    registro_fotografico: null,
    dados_secundarios: null,
    art: null,
    ret: null,
    cr: null,
    ctf: null,
    anuencia_colecoes: null,
    oficio_atividades_campo: null,
  },
});

// Formulário para comentários da análise mais recente
const comentarioForm = ref(
  useForm({
    analise_id: null,
    comentario: '',
    etapa: '',
    id_modulo: null,
  })
);

// Função para abrir o modal com as análises da etapa
const openAnaliseModal = (etapa) => {
  modalEtapa.value = etapa;
  const analiseAtual = analiseAtualPorEtapa.value;
  if (analiseAtual) {
    comentarioForm.value.analise_id = analiseAtual.id;
    comentarioForm.value.etapa = analiseAtual.etapa;
    comentarioForm.value.comentario = '';
    comentarioForm.value.id_modulo = null;
  }
  showModal.value = true;
};

// Função para salvar comentário
const salvarComentario = () => {
  if (!comentarioForm.value.comentario.trim()) {
    comentarioForm.value.errors.comentario = 'O comentário é obrigatório.';
    return;
  }

  console.log('Payload a ser enviado:', comentarioForm.value.data());
  console.log('ID da CAMPANHA:', props.campanha.id);

  comentarioForm.value.post(
    route('sgc.contratada.produtos.comentario', [props.contrato, props.produto.toLowerCase(), props.campanha.id]),
    {
      onSuccess: () => {
        comentarioForm.value.reset('comentario');
        router.reload({ only: ['comentarios'] });
        alert('Comentário salvo com sucesso!');
      },
      onError: (errors) => {
        console.error('Erro ao salvar comentário:', errors);
        alert('Erro ao salvar comentário: ' + (Object.values(errors).join(', ') || 'Tente novamente.'));
      },
    }
  );
};

// Função para excluir comentário
const excluirComentario = (comentarioId) => {
  if (!confirm('Tem certeza que deseja excluir este comentário?')) {
    return;
  }

  router.delete(
    route('sgc.contratada.produtos.comentario.destroy', [props.contrato, props.produto.toLowerCase(), props.campanha.id, comentarioId]),
    {
      onSuccess: () => {
        router.reload({ only: ['comentarios'] });
        alert('Comentário excluído com sucesso!');
      },
      onError: (errors) => {
        console.error('Erro ao excluir comentário:', errors);
        alert('Erro ao excluir comentário: ' + (Object.values(errors).join(', ') || 'Tente novamente.'));
      },
    }
  );
};

// Funções para navegação
const setActiveTab = (tab) => {
  activeTab.value = tab;
  if (tab === 'apresentacao') {
    subStep.value = 1;
  }
};

const prevSubStep = () => {
  if (subStep.value > 1) {
    subStep.value -= 1;
  }
};

// Funções para manipulação de dados
const adicionarPontoCavernicola = (ponto) => {
  const newPonto = {
    id: ponto.id || null,
    cavidade: ponto.cavidade || '',
    latitude: ponto.latitude || null,
    longitude: ponto.longitude || null,
    distancia_eixo_rodovia: ponto.distancia_eixo_rodovia || null,
    formacao_associada: ponto.formacao_associada || '',
    temperatura_media_interna: ponto.temperatura_media_interna || null,
    temperatura_media_externa: ponto.temperatura_media_externa || null,
    umidade_relativa_interna: ponto.umidade_relativa_interna || null,
    umidade_relativa_externa: ponto.umidade_relativa_externa || null,
  };
  if (newPonto.id) {
    const index = form.pontos_cavernicola.findIndex(p => p.id === newPonto.id);
    if (index !== -1) {
      form.pontos_cavernicola[index] = newPonto;
    } else {
      form.pontos_cavernicola.push(newPonto);
    }
  } else {
    newPonto.id = Math.max(0, ...form.pontos_cavernicola.map(p => p.id || 0)) + 1;
    form.pontos_cavernicola.push(newPonto);
  }
};

const excluirPontoCavernicola = (id) => {
  form.pontos_cavernicola = form.pontos_cavernicola.filter(ponto => ponto.id !== id);
};

const adicionarPontoQuelonios = (ponto) => {
  const newPonto = {
    ...ponto.value,
    id: ponto.value.id || null,
  };
  if (newPonto.id) {
    const index = form.pontos_quelo_crocod.findIndex(p => p.id === newPonto.id);
    if (index !== -1) {
      form.pontos_quelo_crocod[index] = newPonto;
    }
  } else {
    form.pontos_quelo_crocod.push(newPonto);
  }
};

const excluirPontoQuelonios = (id) => {
  form.pontos_quelo_crocod = form.pontos_quelo_crocod.filter(ponto => ponto.id !== id);
};

const adicionarModulo = (modulo) => {
  const newModulo = {
    ...modulo.value,
    id: modulo.value.id || null,
    arquivo_existente: modulo.value.arquivo_existente || null,
  };
  if (newModulo.id) {
    const index = form.modulos_amostrais.findIndex(m => m.id === newModulo.id);
    if (index !== -1) {
      form.modulos_amostrais[index] = newModulo;
    }
  } else {
    form.modulos_amostrais.push(newModulo);
  }
};

const excluirModulo = (id) => {
  form.modulos_amostrais = form.modulos_amostrais.filter(modulo => modulo.id !== id);
};

const adicionarMetodologia = (metodologia) => {
  const newMetodologia = {
    id: metodologia.id || null,
    grupo_faunistico: metodologia.grupo_faunistico || '',
    metodologia: metodologia.metodologia || '',
  };
  if (newMetodologia.id) {
    const index = form.metodologias.findIndex(m => m.id === newMetodologia.id);
    if (index !== -1) {
      form.metodologias[index] = newMetodologia;
    } else {
      form.metodologias.push(newMetodologia);
    }
  } else {
    newMetodologia.id = Math.max(0, ...form.metodologias.map(m => m.id || 0)) + 1;
    form.metodologias.push(newMetodologia);
  }
};

const excluirMetodologia = (id) => {
  if (id) {
    form.metodologias = form.metodologias.filter(metodologia => metodologia.id !== id);
  }
};

const vincularAbio = (abioData) => {
  console.log('Recebido em vincularAbio:', abioData);
  if (abioData && abioData.id && abioData.abio) {
    form.abios.push({
      id: abioData.id,
      abio: {
        id: abioData.abio.id,
        numero_licenca: abioData.abio.numero_licenca || 'N/A',
      },
    });
    form.abio.id_abio = null;
  } else {
    console.error('Dados do ABIO inválidos:', abioData);
  }
};

const excluirAbio = (id) => {
  form.abios = form.abios.filter(a => a.id !== id);
};

const vincularProfissional = (profissionalData) => {
  console.log('Recebido em vincularProfissional:', profissionalData);
  if (profissionalData && profissionalData.id && profissionalData.grupo_faunistico && profissionalData.profissional) {
    form.profissionais.push({
      id: profissionalData.id,
      profissional: {
        id: profissionalData.profissional.id,
        profissional: profissionalData.profissional.profissional || 'N/A',
        formacao: profissionalData.profissional.formacao || 'N/A',
      },
      grupo_faunistico: profissionalData.grupo_faunistico,
    });
    form.profissional.id_profissional = null;
    form.profissional.grupo_faunistico = null;
  } else {
    console.error('Dados do profissional inválidos:', profissionalData);
  }
};

const excluirProfissional = (id) => {
  form.profissionais = form.profissionais.filter(p => p.id !== id);
};

const salvarNovoProfissional = (novoProfissional) => {
  form.post(route('profissionais.store'), {
    data: novoProfissional,
    onSuccess: (page) => {
      const novoProfissionalRetornado = page.props.flash.novoProfissional;
      if (novoProfissionalRetornado) {
        props.profissionais.push({
          id: novoProfissionalRetornado.id,
          profissional: novoProfissionalRetornado.profissional,
          formacao: novoProfissionalRetornado.formacao,
        });
      }
    },
  });
};

const anexoTipos = [
  'anuencia_proprietarios',
  'registro_fotografico',
  'dados_secundarios',
  'art',
  'ret',
  'cr',
  'ctf',
  'anuencia_colecoes',
  'oficio_atividades_campo',
];

const formatAnexoLabel = (tipo) => {
  return tipo
    .replace(/_/g, ' ')
    .split(' ')
    .map(word => word.charAt(0).toUpperCase() + word.slice(1))
    .join(' ');
};

// Computed para análises rejeitadas ordenadas por número da análise
const analisesRejeitadas = computed(() => {
  return (props.campanha.analises || [])
    .filter(a => a.status === 'Rejeitada' && etapaMap[modalEtapa.value]?.includes(a.etapa))
    .sort((a, b) => Number(a.analise) - Number(b.analise));
});

// Computed para a análise mais recente por etapa
const analiseAtualPorEtapa = computed(() => {
  return analisesRejeitadas.value.reduce((max, analise) => {
    return !max || Number(analise.analise) > Number(max.analise) ? analise : max;
  }, null);
});

// Computed para comentários por análise
const comentariosPorAnalise = computed(() => (analise) => {
  return props.comentarios
    .filter(c => c.analise === analise.analise && c.etapa === analise.etapa && c.campanha_id === props.campanha.id)
    .sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
});

const submitForm = () => {
  console.log('form.abios antes de enviar:', JSON.stringify(form.abios, null, 2));
  console.log('form.profissionais antes de enviar:', JSON.stringify(form.profissionais, null, 2));
  console.log('form.modulos_amostrais antes de enviar:', JSON.stringify(form.modulos_amostrais, null, 2));
  console.log('form.pontos_quelo_crocod antes de enviar:', JSON.stringify(form.pontos_quelo_crocod, null, 2));

  const formData = new FormData();
  formData.append('_method', 'PUT');
  formData.append('cod_emp', form.cod_emp);
  formData.append('subproduto', form.subproduto);
  if (form.data_campanha_inicial) formData.append('data_campanha_inicial', form.data_campanha_inicial);
  if (form.data_campanha_final) formData.append('data_campanha_final', form.data_campanha_final);
  if (form.periodo) formData.append('periodo', form.periodo);
  if (form.obs) formData.append('observacoes', form.obs);
  if (form.nao_se_aplica) formData.append('nao_se_aplica', form.nao_se_aplica);
  if (form.consideracoes) formData.append('consideracoes', form.consideracoes);
  if (form.planilha) formData.append('planilha', form.planilha);

  form.abios.forEach((abio, index) => {
    formData.append(`abios[${index}][id]`, abio.abio.id);
    formData.append(`abios[${index}][abio][id]`, abio.abio.id);
    formData.append(`abios[${index}][abio][numero_licenca]`, abio.abio.numero_licenca || 'N/A');
  });

  form.profissionais.forEach((prof, index) => {
    if (prof.profissional && prof.profissional.id) {
      formData.append(`profissionais[${index}][id_profissional]`, prof.profissional.id);
      formData.append(`profissionais[${index}][grupo_faunistico]`, prof.grupo_faunistico);
      if (prof.profissional.formacao) formData.append(`profissionais[${index}][formacao]`, prof.profissional.formacao);
    }
  });

  form.modulos_amostrais.forEach((modulo, index) => {
    if (modulo.id) formData.append(`modulos_amostrais[${index}][id]`, modulo.id);
    if (modulo.data_cadastro) formData.append(`modulos_amostrais[${index}][data_cadastro]`, modulo.data_cadastro);
    if (modulo.tamanho_modulo) formData.append(`modulos_amostrais[${index}][tamanho_modulo]`, modulo.tamanho_modulo);
    if (modulo.uf) formData.append(`modulos_amostrais[${index}][uf]`, modulo.uf);
    if (modulo.municipio) formData.append(`modulos_amostrais[${index}][municipio]`, modulo.municipio);
    if (modulo.bioma) formData.append(`modulos_amostrais[${index}][bioma]`, modulo.bioma);
    if (modulo.fitofisionomia) formData.append(`modulos_amostrais[${index}][fitofisionomia]`, modulo.fitofisionomia);
    if (modulo.latitude_inicial) formData.append(`modulos_amostrais[${index}][latitude_inicial]`, modulo.latitude_inicial);
    if (modulo.longitude_inicial) formData.append(`modulos_amostrais[${index}][longitude_inicial]`, modulo.longitude_inicial);
    if (modulo.latitude_final) formData.append(`modulos_amostrais[${index}][latitude_final]`, modulo.latitude_final);
    if (modulo.longitude_final) formData.append(`modulos_amostrais[${index}][longitude_final]`, modulo.longitude_final);
    if (modulo.obs) formData.append(`modulos_amostrais[${index}][obs]`, modulo.obs);
    if (modulo.arquivo instanceof File) formData.append(`modulos_amostrais[${index}][arquivo]`, modulo.arquivo);
    if (modulo.arquivo_existente) formData.append(`modulos_amostrais[${index}][arquivo_existente]`, modulo.arquivo_existente);
  });

  form.pontos_quelo_crocod.forEach((ponto, index) => {
    if (ponto.id) formData.append(`pontos_quelo_crocod[${index}][id]`, ponto.id);
    if (ponto.ponto_de_coleta) formData.append(`pontos_quelo_crocod[${index}][ponto_de_coleta]`, ponto.ponto_de_coleta);
    if (ponto.nome_curso_hidrico) formData.append(`pontos_quelo_crocod[${index}][nome_curso_hidrico]`, ponto.nome_curso_hidrico);
    if (ponto.coordenadas) formData.append(`pontos_quelo_crocod[${index}][coordenadas]`, ponto.coordenadas);
    if (ponto.bacia) formData.append(`pontos_quelo_crocod[${index}][bacia]`, ponto.bacia);
    if (ponto.profundidade) formData.append(`pontos_quelo_crocod[${index}][profundidade]`, ponto.profundidade);
    if (ponto.largura) formData.append(`pontos_quelo_crocod[${index}][largura]`, ponto.largura);
    if (ponto.tipo_substrato) formData.append(`pontos_quelo_crocod[${index}][tipo_substrato]`, ponto.tipo_substrato);
  });

  form.pontos_cavernicola.forEach((ponto, index) => {
    if (ponto.id) formData.append(`pontos_cavernicola[${index}][id]`, ponto.id);
    if (ponto.cavidade) formData.append(`pontos_cavernicola[${index}][cavidade]`, ponto.cavidade);
    if (ponto.latitude) formData.append(`pontos_cavernicola[${index}][latitude]`, ponto.latitude);
    if (ponto.longitude) formData.append(`pontos_cavernicola[${index}][longitude]`, ponto.longitude);
    if (ponto.distancia_eixo_rodovia) formData.append(`pontos_cavernicola[${index}][distancia_eixo_rodovia]`, ponto.distancia_eixo_rodovia);
    if (ponto.formacao_associada) formData.append(`pontos_cavernicola[${index}][formacao_associada]`, ponto.formacao_associada);
    if (ponto.temperatura_media_interna) formData.append(`pontos_cavernicola[${index}][temperatura_media_interna]`, ponto.temperatura_media_interna);
    if (ponto.temperatura_media_externa) formData.append(`pontos_cavernicola[${index}][temperatura_media_externa]`, ponto.temperatura_media_externa);
    if (ponto.umidade_relativa_interna) formData.append(`pontos_cavernicola[${index}][umidade_relativa_interna]`, ponto.umidade_relativa_interna);
    if (ponto.umidade_relativa_externa) formData.append(`pontos_cavernicola[${index}][umidade_relativa_externa]`, ponto.umidade_relativa_externa);
  });

  form.metodologias.forEach((met, index) => {
    if (met.grupo_faunistico) formData.append(`metodologias[${index}][grupo_faunistico]`, met.grupo_faunistico);
    if (met.metodologia) formData.append(`metodologias[${index}][metodologia]`, met.metodologia);
  });

  Object.keys(form.anexos).forEach(tipo => {
    if (form.anexos[tipo]) {
      formData.append(`anexos[${tipo}]`, form.anexos[tipo]);
    }
  });

  console.log('Enviando formData:', Array.from(formData.entries()));

  form.post(route('sgc.contratada.produtos.update', [props.contrato, props.produto, props.campanha.id]), {
    preserveState: true,
    onSuccess: () => {
      console.log('Campanha atualizada com sucesso!');
      form.reset('planilha', 'anexos');
      form.anexos = {
        anuencia_proprietarios: null,
        registro_fotografico: null,
        dados_secundarios: null,
        art: null,
        ret: null,
        cr: null,
        ctf: null,
        anuencia_colecoes: null,
        oficio_atividades_campo: null,
      };
    },
    onError: (errors) => {
      console.error('Erros ao salvar campanha:', errors);
      Object.keys(errors).forEach(key => {
        if (key.startsWith('pontos_quelo_crocod.')) {
          const [_, index, field] = key.split('.');
          if (!form.pontos_quelo_crocod[index].errors) {
            form.pontos_quelo_crocod[index].errors = {};
          }
          form.pontos_quelo_crocod[index].errors[field] = errors[key];
        }
      });
    },
  });
};

const etapasStatus = computed(() => {
  const statusMap = {};
  props.campanha.analises.forEach(a => {
    statusMap[a.etapa] = a.status;
  });
  return statusMap;
});

const updateAnexo = (tipo, file) => {
    console.log('Atualizando anexo temporário:', tipo, file);
    form.anexos[tipo] = file || null;
    console.log('Anexos temporários após atualização:', form.anexos);
    form.anexos = { ...form.anexos }; // Forçar reatividade
};

const excluirAnexoTemporario = (tipo) => {
    console.log('Excluindo anexo temporário:', tipo, form.anexos[tipo]);
    form.anexos[tipo] = null;
    console.log('Anexos temporários após exclusão:', form.anexos);
    form.anexos = { ...form.anexos }; // Forçar reatividade
};

const excluirAnexo = (anexoId) => {
    if (!confirm('Tem certeza que deseja excluir este anexo?')) return;
    console.log('Excluindo anexo salvo:', anexoId);
    router.delete(route('sgc.contratada.produtos.anexo.destroy', [props.contrato, props.produto, props.campanha.id, anexoId]), {
        onSuccess: () => {
            router.reload({ only: ['campanha'] });
            alert('Anexo excluído com sucesso!');
        },
        onError: (errors) => {
            console.error('Erro ao excluir anexo:', errors);
            alert('Erro ao excluir anexo: ' + (Object.values(errors).join(', ') || 'Tente novamente.'));
        },
    });
};
</script>

<template>
  <AuthenticatedLayout>
    <template #header>
      <Breadcrumb
        :links="[
          { route: route('sgc.contratada.produtos.index', [props.contrato, props.produto.toLowerCase()]), label: props.contratos.contratada },
          { route: '#', label: `Editar Campanha ${props.campanha.id_campanha || props.campanha.id}` },
        ]"
      />
    </template>
    <NavbarContrato :tipo="{ id: props.contrato }">
      <template #body>
        <div class="card">
          <div class="card-body">
            <h2 class="text-center mb-4">EDITAR CAMPANHA {{ props.produto.toUpperCase() }}</h2>
            <h4 class="mb-3">Status: {{ props.campanha.status || 'Rejeitada' }}</h4>

            <ul class="nav nav-tabs mb-4">
              <li class="nav-item">
                <a
                  class="nav-link"
                  :class="{ active: activeTab === 'apresentacao' }"
                  @click.prevent="setActiveTab('apresentacao')"
                >Apresentação</a>
              </li>
              <li class="nav-item">
                <a
                  class="nav-link"
                  :class="{ active: activeTab === 'metodologia' }"
                  @click.prevent="setActiveTab('metodologia')"
                >Metodologia</a>
              </li>
              <li class="nav-item">
                <a
                  class="nav-link"
                  :class="{ active: activeTab === 'resultados' }"
                  @click.prevent="setActiveTab('resultados')"
                >Resultados</a>
              </li>
              <li class="nav-item">
                <a
                  class="nav-link"
                  :class="{ active: activeTab === 'anexos' }"
                  @click.prevent="setActiveTab('anexos')"
                >Anexos</a>
              </li>
            </ul>

            <!-- Alerta para análises rejeitadas -->
            <div v-if="!props.isDraft && props.campanha.analises?.some(a => a.status === 'Rejeitada' && etapaMap[activeTab]?.includes(a.etapa))" class="alert alert-warning d-flex align-items-center mb-4">
              <svg class="bi me-2" width="24" height="24" fill="currentColor">
                <use xlink:href="/bootstrap-icons.svg#exclamation-triangle" />
              </svg>
              <span>Essa etapa foi reprovada pelo Fiscal.</span>
              <button
                type="button"
                class="btn btn-primary btn-sm ms-auto"
                @click="openAnaliseModal(activeTab)"
              >
                Ver Análise
              </button>
            </div>

            <form @submit.prevent="submitForm" enctype="multipart/form-data">
              <div class="tab-content">
                <div v-if="activeTab === 'apresentacao'" class="tab-pane fade" :class="{ 'show active': activeTab === 'apresentacao' }">
                  <div v-if="subStep === 1">
                    <h4 class="text-center mb-3" style="font-weight: bold;">APRESENTAÇÃO</h4>
                    <div class="mb-3">
                      <label for="cod_emp" class="form-label">Empreendimento</label>
                      <select
                        v-model="form.cod_emp"
                        class="form-select"
                        id="cod_emp"
                        required
                        :disabled="etapasStatus['apresentacao_geral'] === 'Aprovada'"
                      >
                        <option value="">Selecione um empreendimento</option>
                        <option v-for="emp in props.empreendimentos" :key="emp" :value="emp">{{ emp }}</option>
                      </select>
                      <InputError :message="form.errors.cod_emp" />
                    </div>
                    <div class="mb-3">
                      <label class="form-label">Família</label>
                      <input
                        v-model="form.subproduto"
                        type="text"
                        class="form-control"
                        required
                        :disabled="etapasStatus['apresentacao_geral'] === 'Aprovada'"
                      />
                      <InputError :message="form.errors.subproduto" />
                    </div>
                    <div class="d-flex justify-content-end">
                      <NavButton
                        type="button"
                        type-button="primary"
                        title="Avançar"
                        @click="subStep = 2"
                      />
                    </div>
                    <h4 class="text-center mt-4" style="font-weight: bold; color: #6c757d;">{{ subStep }}/5</h4>
                  </div>
                  <DadosGeraisEditar
                    v-if="subStep === 2"
                    :form="form"
                    :abios="props.abios"
                    :profissionais="props.profissionais"
                    :abio-records="form.abios"
                    :profissional-records="form.profissionais"
                    :sub-step="subStep"
                    :disabled="etapasStatus['caracterizacao_area'] === 'Aprovada'"
                    @vincular-abio="vincularAbio"
                    @excluir-abio="excluirAbio"
                    @salvar-novo-profissional="salvarNovoProfissional"
                    @vincular-profissional="vincularProfissional"
                    @excluir-profissional="excluirProfissional"
                    @next="subStep = 3"
                    @prev="prevSubStep"
                  />
                  <ModulosAmostraisEditar
                    v-if="subStep === 3"
                    :form="form"
                    :modulo-records="form.modulos_amostrais"
                    :ufs="props.ufs"
                    :biomas="props.biomas"
                    :sub-step="subStep"
                    :disabled="etapasStatus['modulos_amostrais'] === 'Aprovada'"
                    @adicionar-modulo="adicionarModulo"
                    @excluir-modulo="excluirModulo"
                    @next="subStep = 4"
                    @prev="prevSubStep"
                  />
                  <QueloniosCrocodilianosEditar
                    v-if="subStep === 4"
                    :form="form"
                    :ponto-records="form.pontos_quelo_crocod"
                    :sub-step="subStep"
                    :disabled="etapasStatus['pontos_quelo_crocod'] === 'Aprovada'"
                    @adicionar-ponto="adicionarPontoQuelonios"
                    @excluir-ponto="excluirPontoQuelonios"
                    @next="subStep = 5"
                    @prev="prevSubStep"
                  >
                    <template #footer>
                      <h4 class="text-center mt-4" style="font-weight: bold; color: #6c757d;">{{ subStep }}/5</h4>
                    </template>
                  </QueloniosCrocodilianosEditar>
                  <FaunaCavernicolaEditar
                    v-if="subStep === 5"
                    :form="form"
                    :ponto-records="form.pontos_cavernicola"
                    :sub-step="subStep"
                    :set-active-tab="setActiveTab"
                    :disabled="etapasStatus['pontos_cavernicola'] === 'Aprovada'"
                    @adicionar-ponto-cavernicola="adicionarPontoCavernicola"
                    @excluir-ponto-cavernicola="excluirPontoCavernicola"
                    @next="setActiveTab('metodologia')"
                    @prev="prevSubStep"
                  >
                    <template #footer>
                      <h4 class="text-center mt-4" style="font-weight: bold; color: #6c757d;">{{ subStep }}/5</h4>
                    </template>
                  </FaunaCavernicolaEditar>
                </div>
                <div v-if="activeTab === 'metodologia'" class="tab-pane fade" :class="{ 'show active': activeTab === 'metodologia' }">
                  <MetodologiaEditar
                    :form="form"
                    :metodologia-records="form.metodologias"
                    :set-active-tab="setActiveTab"
                    :disabled="etapasStatus['metodologia'] === 'Aprovada'"
                    @adicionar-metodologia="adicionarMetodologia"
                    @excluir-metodologia="excluirMetodologia"
                    @prev="setActiveTab('apresentacao')"
                    @next="setActiveTab('resultados')"
                  />
                </div>
                <div v-if="activeTab === 'resultados'" class="tab-pane fade" :class="{ 'show active': activeTab === 'resultados' }">
                  <ResultadosEditar
                    :form="form"
                    :resultados-records="form.resultados"
                    :set-active-tab="setActiveTab"
                    :disabled="etapasStatus['resultados'] === 'Aprovada'"
                    @prev="setActiveTab('metodologia')"
                    @next="setActiveTab('anexos')"
                  />
                </div>
                <div v-if="activeTab === 'anexos'" class="tab-pane fade" :class="{ 'show active': activeTab === 'anexos' }">
                  <h4 class="mb-3" style="text-align: center;">ANEXOS</h4>
                  <form @submit.prevent="submitForm" enctype="multipart/form-data">
                      <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                          <div v-for="tipo in anexoTipos" :key="tipo">
                              <label class="form-label">{{ formatAnexoLabel(tipo) }}</label>
                              <input
                                  type="file"
                                  @change="updateAnexo(tipo, $event.target.files[0])"
                                  accept=".pdf,.jpg,.jpeg,.png"
                                  class="form-control"
                                  :disabled="etapasStatus['anexos'] === 'Aprovada'"
                              />
                              <InputError :message="form.errors[`anexos.${tipo}`]" />
                              <small v-if="form.anexos[tipo]" class="text-muted">{{ form.anexos[tipo].name }}</small>
                          </div>
                      </div>
                      <!-- Tabela para anexos temporários -->
                      {{ console.log('Anexos temporários:', form.anexos) }}
                      <div v-if="Object.keys(form.anexos).some(tipo => form.anexos[tipo])" class="overflow-x-auto mb-6">
                          <table class="min-w-full bg-white border border-gray-300">
                              <thead>
                                  <tr class="bg-gray-100">
                                      <th class="py-2 px-4 border-b text-left">Tipo de Anexo</th>
                                      <th class="py-2 px-4 border-b text-left">Nome do Arquivo</th>
                                      <th class="py-2 px-4 border-b text-left">Ação</th>
                                  </tr>
                              </thead>
                              <tbody>
                                  <tr v-for="(file, tipo) in form.anexos" :key="tipo">
                                      {{ console.log('Renderizando anexo temporário:', tipo, file) }}
                                      <td v-if="file" class="py-2 px-4 border-b">{{ formatAnexoLabel(tipo) }}</td>
                                      <td v-if="file" class="py-2 px-4 border-b">{{ file.name || 'Não informado' }}</td>
                                      <td v-if="file" class="py-2 px-4 border-b">
                                          <button class="btn btn-link text-danger" @click="excluirAnexoTemporario(tipo)">Excluir</button>
                                      </td>
                                  </tr>
                              </tbody>
                          </table>
                      </div>
                      <div v-else class="alert alert-info text-center mb-6">
                          Nenhum anexo temporário selecionado.
                      </div>
                      <!-- Tabela para anexos salvos -->
                      <div v-if="props.campanha.anexos?.length" class="overflow-x-auto mb-6">
                          <table class="min-w-full bg-white border border-gray-300">
                              <thead>
                                  <tr class="bg-gray-100">
                                      <th class="py-2 px-4 border-b text-left">ID</th>
                                      <th class="py-2 px-4 border-b text-left">Tipo de Anexo</th>
                                      <th class="py-2 px-4 border-b text-left">Nome do Arquivo</th>
                                      <th class="py-2 px-4 border-b text-left">Data de Criação</th>
                                      <th class="py-2 px-4 border-b text-left">Ação</th>
                                  </tr>
                              </thead>
                              <tbody>
                                  <tr v-for="anexo in props.campanha.anexos" :key="anexo.id" class="hover:bg-gray-50">
                                      <td class="py-2 px-4 border-b">{{ anexo.id || 'Não informado' }}</td>
                                      <td class="py-2 px-4 border-b">{{ anexo.tipo_anexo ? formatAnexoLabel(anexo.tipo_anexo) : 'Não informado' }}</td>
                                      <td class="py-2 px-4 border-b">{{ anexo.nome_arquivo || 'Não informado' }}</td>
                                      <td class="py-2 px-4 border-b">{{ anexo.created_at || 'Não informado' }}</td>
                                      <td class="py-2 px-4 border-b">
                                          <a v-if="anexo.caminho" :href="anexo.caminho" target="_blank" class="btn btn-link">Visualizar</a>
                                          <span v-else>Nenhum arquivo</span>
                                          <button
                                              v-if="anexo.id && etapasStatus['anexos'] !== 'Aprovada'"
                                              class="btn btn-link text-danger"
                                              @click="excluirAnexo(anexo.id)"
                                          >Excluir</button>
                                      </td>
                                  </tr>
                              </tbody>
                          </table>
                      </div>
                      <div v-else class="alert alert-info text-center mb-6">
                          Nenhum anexo salvo disponível.
                      </div>
                      <div class="d-flex justify-content-between mt-4">
                          <NavButton
                              type="button"
                              type-button="secondary"
                              title="Voltar"
                              @click="setActiveTab('resultados')"
                          />
                          <NavButton
                              type="button"
                              type-button="secondary"
                              title="Cancelar"
                              @click="router.get(route('sgc.contratada.produtos.index', [props.contrato, props.produto.toLowerCase()]))"
                          />
                          <NavButton
                              type="submit"
                              type-button="primary"
                              title="Salvar Alterações"
                              :disabled="form.processing"
                          />
                      </div>
                  </form>
              </div>
              </div>
            </form>

            <!-- Modal para exibir análises rejeitadas -->
            <div class="modal fade" :class="{ 'show d-block': showModal }" tabindex="-1" role="dialog" aria-labelledby="analiseModalLabel" aria-hidden="true">
              <div class="modal-dialog modal-lg">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="analiseModalLabel">Análises Rejeitadas - {{ etapas.find(e => etapaMap[modalEtapa]?.includes(e.value))?.label || modalEtapa }}</h5>
                    <button type="button" class="btn-close" @click="showModal = false" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                    <div v-if="analisesRejeitadas.length">
                      <ul class="list-none pl-0">
                        <li
                          v-for="analise in analisesRejeitadas"
                          :key="analise.id"
                          class="analise-item"
                          :class="{ 
                            'analise-item-even': analisesRejeitadas.indexOf(analise) % 2 === 0, 
                            'analise-item-odd': analisesRejeitadas.indexOf(analise) % 2 !== 0,
                            'analise-item-current': analise.id === analiseAtualPorEtapa?.id
                          }"
                        >
                          <div class="analise-content">
                            <div class="analise-header">
                              <span class="etapa">Análise {{ analise.analise }} - {{ etapas.find(e => e.value === analise.etapa)?.label || analise.etapa }}</span>
                              <span class="analise-date">{{ analise.created_at ? new Date(analise.created_at).toLocaleString('pt-BR') : 'Data não informada' }}</span>
                            </div>
                            <div class="analise-text">{{ analise.comentario || 'Não informado' }}</div>
                            <div class="comentarios-list mt-2">
                              <h6>Seus Comentários:</h6>
                              <div v-if="comentariosPorAnalise(analise).length">
                                <div v-for="comentario in comentariosPorAnalise(analise)" :key="comentario.id" class="comentario-content">
                                  <div class="d-flex align-items-start">
                                    <div class="flex-grow-1">
                                      <span class="comentario-text">{{ comentario.comentario }}</span>
                                      <span class="comentario-date">Salvo em: {{ new Date(comentario.created_at).toLocaleString('pt-BR') }}</span>
                                    </div>
                                    <button
                                      v-if="props.campanha.status === 'Rejeitada' && analise.id === analiseAtualPorEtapa?.id"
                                      class="btn btn-link text-danger p-0 ms-2"
                                      title="Excluir Comentário"
                                      @click="excluirComentario(comentario.id)"
                                    >
                                      x
                                    </button>
                                  </div>
                                </div>
                              </div>
                              <div v-else class="comentario-content">
                                <span class="comentario-label">Nenhum comentário salvo para esta análise.</span>
                              </div>
                            </div>
                            <form 
                              v-if="props.campanha.status === 'Rejeitada' && analise.id === analiseAtualPorEtapa?.id"
                              @submit.prevent="salvarComentario"
                              class="mt-2"
                            >
                              <div class="mb-3">
                                <label for="novo_comentario" class="form-label">Adicionar Novo Comentário</label>
                                <textarea
                                  v-model="comentarioForm.comentario"
                                  id="novo_comentario"
                                  class="form-control"
                                  rows="4"
                                  placeholder="Digite seu comentário sobre a análise do fiscal"
                                  required
                                ></textarea>
                                <InputError :message="comentarioForm.errors.comentario" />
                              </div>
                              <div class="d-flex justify-content-end">
                                <NavButton type="submit" type-button="primary" title="Salvar Comentário" :disabled="comentarioForm.processing" />
                              </div>
                            </form>
                          </div>
                        </li>
                      </ul>
                    </div>
                    <div v-else class="text-center">
                      Nenhuma análise rejeitada para esta etapa.
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" @click="showModal = false">Fechar</button>
                  </div>
                </div>
              </div>
            </div>
            <div v-if="showModal" class="modal-backdrop fade show"></div>

            <!-- Mensagens de Sucesso/Erro -->
            <div v-if="$page.props.flash.success" class="alert alert-success mt-4">
              {{ $page.props.flash.success }}
            </div>
            <div v-if="$page.props.flash.error" class="alert alert-danger mt-4">
              {{ $page.props.flash.error }}
            </div>
          </div>
        </div>
      </template>
    </NavbarContrato>
  </AuthenticatedLayout>
</template>

<style scoped>
.card {
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}
.nav-tabs .nav-link {
  color: #6c757d;
  font-weight: 500;
}
.nav-tabs .nav-link.active {
  color: #007bff;
  border-bottom: 2px solid #007bff;
}
.tab-content {
  padding: 20px;
}
table {
  border-collapse: collapse;
}
th,
td {
  padding: 0.5rem 1rem;
  border: 1px solid #dee2e6;
}
thead {
  background-color: #f8f9fa;
}
tr:hover {
  background-color: #f1f5f9;
}
.alert {
  font-size: 1rem;
  padding: 1rem;
  border-radius: 6px;
}
.alert-info {
  background-color: #e7f1ff;
  color: #084298;
}
.alert-warning {
  background-color: #fff3cd;
  color: #856404;
  border: 1px solid #ffeeba;
}
.alert-success {
  background-color: #d4edda;
  color: #155724;
}
.alert-danger {
  background-color: #f8d7da;
  color: #721c24;
}
.form-control {
  border: 1px solid #ced4da;
  border-radius: 4px;
  padding: 0.375rem 0.75rem;
}
.form-label {
  font-weight: 500;
  margin-bottom: 0.5rem;
}
.analise-item {
  padding: 0.75rem 1rem;
  margin-bottom: 0.5rem;
  border-radius: 4px;
  font-size: 1rem;
  line-height: 1.5;
  transition: background-color 0.2s ease;
}
.analise-item-even {
  background-color: #f8f9fa;
}
.analise-item-odd {
  background-color: #ffffff;
}
.analise-item-current {
  border: 2px solid #007bff;
  background-color: #e7f1ff;
}
.analise-item:hover {
  background-color: #e2e6ea;
}
.analise-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 0.5rem;
}
.etapa {
  font-weight: 600;
  color: #084298;
}
.analise-date {
  font-size: 0.85rem;
  color: #6c757d;
}
.analise-text {
  margin-bottom: 0.5rem;
}
.comentarios-list {
  margin-top: 0.5rem;
}
.comentario-content {
  margin-top: 0.5rem;
  padding: 0.5rem;
  background-color: #d4edda;
  border-radius: 4px;
}
.comentario-label {
  font-weight: 600;
  color: #155724;
}
.comentario-text {
  display: block;
  margin: 0.25rem 0;
}
.comentario-date {
  font-size: 0.85rem;
  color: #155724;
}
.modal-lg {
  max-width: 800px;
}
.form-control:disabled,
.form-select:disabled,
input[type="file"]:disabled {
  background-color: #f5f5f5;
  color: #999;
  cursor: not-allowed;
}
</style>
