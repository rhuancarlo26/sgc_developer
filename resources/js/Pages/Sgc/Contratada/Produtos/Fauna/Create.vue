<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import NavbarContrato from '@/Pages/Sgc/Contratada/NavbarContrato.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import InputError from '@/Components/InputError.vue';
import NavButton from '@/Components/NavButton.vue';
import { ref, reactive, onMounted, computed } from 'vue';
import DadosGerais from './DadosGerais.vue';
import ModulosAmostragem from './ModulosAmostrais.vue';
import CampanhaAtropelamento from './CampanhaAtropelamento.vue';
import QueloniosCrocodilian from './QueloniosCrocodilianos.vue';
import FaunaCavernic from './FaunaCavernicola.vue';
import Metodologia from './Metodologia.vue';
import FaunaResultados from './FaunaResultados.vue';
import { IconScanEye, IconTrash } from "@tabler/icons-vue";

// ─── Props vindas do ProdutosController ──────────────────────────────────────
const props = defineProps({
    contrato:      [Number, String],
    produto:       String,
    contratos:     Object,
    subproduto:    [String, null],
    empreendimentos: Array,
    abios:         { type: Array, default: () => [] },
    profissionais: { type: Array, default: () => [] },
    campanhaId:    [Number, String, null],  // ID do rascunho já criado no backend
    etapaAtual:    { type: String, default: 'apresentacao' }, // progresso salvo
    draftData:     { type: Object, default: () => ({}) },     // dados já salvos
    ufs:           { type: Array, default: () => [] },
    biomas:        { type: Array, default: () => [] },
});

// ─── Estado de navegação ──────────────────────────────────────────────────────
const activeTab = ref('apresentacao');
const subStep   = ref(1);

// ─── ID do rascunho — começa com o que veio do backend ───────────────────────
const campanhaId = ref(props.campanhaId ?? null);

// ─── Estado de salvamento ─────────────────────────────────────────────────────
const salvando = ref(false);
const erroSalvamento = ref(null);

// ─── Formulários — inicializados com draftData se existir ────────────────────
const form = reactive({
    cod_emp: props.draftData?.cod_emp ?? '',
    familia: props.produto === 'Fauna' ? 'Fauna' : (props.draftData?.familia ?? ''),
    id_campanha: props.draftData?.id_campanha ?? ''
});

const isAtropelamento = computed(() =>
    props.subproduto?.toLowerCase().includes('atropelamento') ?? false
);

const formCampanha = reactive({
    rodovia: null, data_inicial: null, data_final: null,
    uf_inicial: null, uf_final: null,
    km_inicial: null, km_final: null,
    latitude_inicial: null, longitude_inicial: null,
    latitude_final: null, longitude_final: null,
    obs: null, errors: {},
});

const formDadosGerais = reactive({
    data_campanha_inicial: props.draftData?.data_campanha_inicial ?? '',
    data_campanha_final:   props.draftData?.data_campanha_final   ?? '',
    periodo:               props.draftData?.periodo               ?? '',
    sei_dnit:              props.draftData?.sei_dnit              ?? '',
    obs:                   props.draftData?.observacoes           ?? '',
    errors:                {},
});

const formAbio        = reactive({ id_abio: null, errors: {} });
const formProfissional = reactive({ profissional: null, grupo_faunistico: null, errors: {} });
const formNovoProfissional = reactive({
    profissional: '', formacao: '', telefone: '', cpf: '', email: '',
    curriculum_lattes: '', funcao: '', ctf: '', validade: '',
    conselho_de_classe: 'Não', numero_de_registro: null,
    status: 'Ativo', observacao: '', errors: {},
});
const formModuloAmostral = reactive({
    data_cadastro: null, tamanho_modulo: null, uf: null, municipio: null,
    bioma: null, fitofisionomia: null, latitude_inicial: null,
    longitude_inicial: null, latitude_final: null, longitude_final: null,
    obs: null, arquivo: null, errors: {},
});
const formPontosAmostragem = reactive({
    ponto_de_coleta: '', nome_curso_hidrico: '', latitude: '', longitude: '',
    bacia: '', profundidade: null, largura: null, tipo_substrato: '', errors: {},
});
const formPontosCavernicola = reactive({
    cavidade: '', latitude: null, longitude: null, distancia_eixo_rodovia: null,
    formacao_associada: '', temperatura_media_interna: null,
    temperatura_media_externa: null, umidade_relativa_interna: null,
    umidade_relativa_externa: null, errors: {},
});
const formMetodologia = reactive({ grupo_faunistico: null, metodologia: '', errors: {} });
const formResultados  = reactive({
    planilha_terrestre: null, planilha_aquatica: null, planilha_cavernicola: null,
    consideracoes: '', errors: {},
});

// ─── Tabelas — inicializadas com draftData ────────────────────────────────────
const naoSeAplicaQuelonios   = ref(props.draftData?.nao_se_aplica ?? false);
const naoSeAplicaCavernicola = ref(props.draftData?.nao_se_aplica ?? false);
const abioRecords            = ref(props.draftData?.abios             ?? []);
const profissionalRecords    = ref(props.draftData?.profissionais      ?? []);
const moduloRecords          = ref(props.draftData?.modulos_amostrais  ?? []);
const campanhaRecords        = ref(props.draftData?.atropelamento_campanha ?? []);
const pontoRecords           = ref(props.draftData?.pontos_quelo_crocod ?? []);
const pontoCavernicolaRecords = ref(props.draftData?.pontos_cavernicola ?? []);
const metodologiaRecords     = ref(props.draftData?.formMetodologia    ?? []);
const resultadosRecords      = ref({
    terrestre:   props.draftData?.resultadosTerrestre   ?? [],
    aquatica:    props.draftData?.resultadosAquatica    ?? [],
    cavernicola: props.draftData?.resultadosCavernicola ?? [],
});

// Atropelamento
const planilhaAtropelamento       = ref(null);
const consideracoesAtropelamento  = ref('');

// ─── Anexos ───────────────────────────────────────────────────────────────────
const anexos = ref({
    anuencia_proprietarios: null, registro_fotografico: null,
    dados_secundarios: null, art: null, ret: null, cr: null,
    ctf: null, anuencia_colecoes: null, oficio_atividades_campo: null,
    rfaef: null, cartas_anuencia: null,
});

const anexosLabels = {
    anuencia_proprietarios:  'Anuência dos Proprietários',
    registro_fotografico:    'Registro Fotográfico',
    dados_secundarios:       'Dados Secundários',
    art:                     'ART',
    ret:                     'RET',
    cr:                      'CR',
    ctf:                     'CTF',
    anuencia_colecoes:       'Anuência de Coleções',
    oficio_atividades_campo: 'Ofício de Atividades de Campo',
};

const anexosLabelsAtropelamento = {
    registro_fotografico: 'Registro Fotográfico',
    ctf:                  'CTF',
    dados_secundarios:    'Dados Secundários',
    art:                  'ART',
    rfaef:                'Formulário de Registro de Atropelamentos (RFAEF)',
    cartas_anuencia:      'Cartas de Anuência',
};

const anexosLabelsAtivos = computed(() =>
    isAtropelamento.value ? anexosLabelsAtropelamento : anexosLabels
);

// ─── Preview de anexos ────────────────────────────────────────────────────────
const previewFile = ref(null);
const previewUrl  = ref(null);
const previewType = ref(null);

// ─── Ao montar: abre na etapa onde o usuário parou ───────────────────────────
onMounted(() => {
    const etapa = props.etapaAtual;
    if (etapa && etapa !== 'apresentacao') {
        activeTab.value = etapa;
    }
});

// ─── HELPERS DE SALVAMENTO ────────────────────────────────────────────────────

/**
 * Monta o FormData da aba de apresentação.
 */
function buildApresentacaoPayload() {
    const fd = new FormData();

    fd.append('cod_emp', form.cod_emp ?? '');
    fd.append('data_campanha_inicial',     formDadosGerais.data_campanha_inicial ?? '');
    fd.append('data_campanha_final',       formDadosGerais.data_campanha_final   ?? '');
    fd.append('periodo',                   formDadosGerais.periodo               ?? '');
    fd.append('sei_dnit',                  formDadosGerais.sei_dnit              ?? '');
    fd.append('observacoes',               formDadosGerais.obs                   ?? '');
    fd.append('nao_se_aplica_quelo',       naoSeAplicaQuelonios.value ? '1' : '0');
    fd.append('nao_se_aplica_cavernicola', naoSeAplicaCavernicola.value ? '1' : '0');
    fd.append('id_campanha',               form.id_campanha ?? '');


    abioRecords.value.forEach((abio, i) => {
        fd.append(`id_abio[${i}]`, abio.id);
    });

    profissionalRecords.value.forEach((prof, i) => {
        fd.append(`profissionais[${i}][profissional]`,     prof.profissional     ?? '');
        fd.append(`profissionais[${i}][grupo_faunistico]`, prof.grupo_faunistico ?? '');
    });

    moduloRecords.value.forEach((m, i) => {
        fd.append(`modulos_amostrais[${i}][data_cadastro]`,     m.data_cadastro     ?? '');
        fd.append(`modulos_amostrais[${i}][tamanho_modulo]`,    m.tamanho_modulo    ?? '');
        fd.append(`modulos_amostrais[${i}][uf]`,                m.uf                ?? '');
        fd.append(`modulos_amostrais[${i}][municipio]`,         m.municipio         ?? '');
        fd.append(`modulos_amostrais[${i}][bioma]`,             m.bioma             ?? '');
        fd.append(`modulos_amostrais[${i}][fitofisionomia]`,    m.fitofisionomia    ?? '');
        fd.append(`modulos_amostrais[${i}][latitude_inicial]`,  m.latitude_inicial  ?? '');
        fd.append(`modulos_amostrais[${i}][longitude_inicial]`, m.longitude_inicial ?? '');
        fd.append(`modulos_amostrais[${i}][latitude_final]`,    m.latitude_final    ?? '');
        fd.append(`modulos_amostrais[${i}][longitude_final]`,   m.longitude_final   ?? '');
        fd.append(`modulos_amostrais[${i}][obs]`,               m.obs               ?? '');
            if (m.arquivo) fd.append(`modulos_amostrais[${i}][arquivo]`, m.arquivo);
    });

    // Campanhas de atropelamento
    campanhaRecords.value.forEach((c, i) => {
        fd.append(`atropelamento_campanha[${i}][rodovia]`,            c.rodovia            ?? '');
        fd.append(`atropelamento_campanha[${i}][data_inicial]`,       c.data_inicial        ?? '');
        fd.append(`atropelamento_campanha[${i}][data_final]`,         c.data_final          ?? '');
        fd.append(`atropelamento_campanha[${i}][uf_inicial]`,         c.uf_inicial          ?? '');
        fd.append(`atropelamento_campanha[${i}][uf_final]`,           c.uf_final            ?? '');
        if (c.km_inicial != null) fd.append(`atropelamento_campanha[${i}][km_inicial]`, c.km_inicial);
        if (c.km_final   != null) fd.append(`atropelamento_campanha[${i}][km_final]`,   c.km_final);
        if (c.obs) fd.append(`atropelamento_campanha[${i}][obs]`, c.obs);
    });

    // Planilha e considerações de atropelamento
    if (planilhaAtropelamento.value)       fd.append('planilha_atropelamento',      planilhaAtropelamento.value);
    if (consideracoesAtropelamento.value)  fd.append('consideracoes_atropelamento', consideracoesAtropelamento.value);

    pontoRecords.value.forEach((p, i) => {
        fd.append(`pontos_quelo_crocod[${i}][ponto_de_coleta]`,    p.ponto_de_coleta    ?? '');
        fd.append(`pontos_quelo_crocod[${i}][nome_curso_hidrico]`, p.nome_curso_hidrico ?? '');
        fd.append(`pontos_quelo_crocod[${i}][latitude]`,           p.latitude           ?? '');
        fd.append(`pontos_quelo_crocod[${i}][longitude]`,          p.longitude          ?? '');
        fd.append(`pontos_quelo_crocod[${i}][bacia]`,              p.bacia              ?? '');
        fd.append(`pontos_quelo_crocod[${i}][profundidade]`,       p.profundidade       ?? '');
        fd.append(`pontos_quelo_crocod[${i}][largura]`,            p.largura            ?? '');
        fd.append(`pontos_quelo_crocod[${i}][tipo_substrato]`,     p.tipo_substrato     ?? '');
    });

    pontoCavernicolaRecords.value.forEach((p, i) => {
        fd.append(`pontos_cavernicola[${i}][cavidade]`,                   p.cavidade                   ?? '');
        fd.append(`pontos_cavernicola[${i}][latitude]`,                   p.latitude                   ?? '');
        fd.append(`pontos_cavernicola[${i}][longitude]`,                  p.longitude                  ?? '');
        fd.append(`pontos_cavernicola[${i}][distancia_eixo_rodovia]`,     p.distancia_eixo_rodovia     ?? '');
        fd.append(`pontos_cavernicola[${i}][formacao_associada]`,         p.formacao_associada         ?? '');
        fd.append(`pontos_cavernicola[${i}][temperatura_media_interna]`,  p.temperatura_media_interna  ?? '');
        fd.append(`pontos_cavernicola[${i}][temperatura_media_externa]`,  p.temperatura_media_externa  ?? '');
        fd.append(`pontos_cavernicola[${i}][umidade_relativa_interna]`,   p.umidade_relativa_interna   ?? '');
        fd.append(`pontos_cavernicola[${i}][umidade_relativa_externa]`,   p.umidade_relativa_externa   ?? '');
    });

    return fd;
}

/**
 * Salva uma etapa via PATCH rascunho/{id}/etapa/{etapa}.
 * Retorna true se ok, false se erro.
 */
async function salvarEtapa(etapa, formData) {
    if (!campanhaId.value) return false;

    salvando.value = true;
    erroSalvamento.value = null;

    try {
        const url = route('sgc.contratada.produtos.rascunho.etapa', [
            props.contrato,
            props.produto.toLowerCase(),
            campanhaId.value,
            etapa,
        ]);

        const response = await fetch(url, {
            method: 'POST',  // ← POST em vez de PATCH
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'X-Inertia': 'false',
            },
            body: formData,
        });

        const data = await response.json();

        if (!response.ok) {
            erroSalvamento.value = data.message ?? 'Erro ao salvar.';
            return false;
        }

        return true;

    } catch (e) {
        erroSalvamento.value = 'Erro de conexão.';
        return false;
    } finally {
        salvando.value = false;
    }
}

// ─── NAVEGAÇÃO E SALVAMENTO POR ETAPA ────────────────────────────────────────

/**
 * Sub-etapa 1: inicializa o rascunho no backend e avança.
 */
const inicializarRascunho = async () => {

    if (!form.cod_emp || !form.familia || !form.id_campanha) {
        alert('Selecione o empreendimento, campanha e família antes de avançar.');
        return;
    }

    // Se já temos campanhaId (rascunho existente), só avança
    if (campanhaId.value) {
        subStep.value = 2;
        return;
    }

    salvando.value = true;
    erroSalvamento.value = null;

    try {
        const response = await fetch(
            route('sgc.contratada.produtos.rascunho.inicializar', [
                props.contrato,
                props.produto.toLowerCase(),
            ]),
            {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    campanha_id: campanhaId.value,
                    cod_emp:    form.cod_emp,
                    subproduto: props.subproduto ?? form.familia,
                    id_campanha: form.id_campanha,
                }),
            }
        );

        const data = await response.json();

        if (data.campanha_id) {
            campanhaId.value = data.campanha_id;
            subStep.value    = 2;
        } else {
            erroSalvamento.value = 'Não foi possível inicializar o rascunho.';
        }
    } catch (e) {
        erroSalvamento.value = 'Erro de conexão ao inicializar rascunho.';
    } finally {
        salvando.value = false;
    }
};

const adicionarCampanha = () => {
    if (formCampanha.data_inicial && formCampanha.data_final && formCampanha.uf_inicial && formCampanha.uf_final) {
        campanhaRecords.value.push({ ...formCampanha, id: Date.now() });
        Object.assign(formCampanha, {
            rodovia: null, data_inicial: null, data_final: null,
            uf_inicial: null, uf_final: null, km_inicial: null, km_final: null,
            latitude_inicial: null, longitude_inicial: null,
            latitude_final: null, longitude_final: null, obs: null,
        });
    }
};

const excluirCampanha = (id) => {
    campanhaRecords.value = campanhaRecords.value.filter(c => c.id !== id);
};

const totalSubSteps = computed(() => isAtropelamento.value ? 3 : 5);

const nextSubStep = async () => {
    const maxStep = totalSubSteps.value;

    // Sub-etapas intermediárias: salva e avança
    if (subStep.value >= 2 && subStep.value < maxStep) {
        const ok = await salvarEtapa('apresentacao', buildApresentacaoPayload());
        if (!ok) return;
        subStep.value++;
        return;
    }

    // Última sub-etapa → vai para aba metodologia
    if (subStep.value === maxStep) {
        const ok = await salvarEtapa('apresentacao', buildApresentacaoPayload());
        if (!ok) return;
        setActiveTab('metodologia');
    }
};

const prevSubStep = () => {
    if (subStep.value > 1) {
        subStep.value--;
    }
};

const setActiveTab = (tab) => {
    activeTab.value = tab;
    if (tab === 'apresentacao') subStep.value = totalSubSteps.value;
};

const salvarMetodologia = async () => {
    const fd = new FormData();
    metodologiaRecords.value.forEach((m, i) => {
        fd.append(`metodologias[${i}][grupo_faunistico]`, m.grupo_faunistico ?? '');
        fd.append(`metodologias[${i}][metodologia]`,      m.metodologia      ?? '');
    });

    const ok = await salvarEtapa('metodologia', fd);
    if (ok) setActiveTab('resultados');
};

const salvarResultados = async () => {
    const fd = new FormData();

    if (formResultados.planilha_terrestre)
        fd.append('planilha_terrestre', formResultados.planilha_terrestre);
    if (formResultados.planilha_aquatica)
        fd.append('planilha_aquatica', formResultados.planilha_aquatica);
    if (formResultados.planilha_cavernicola)
        fd.append('planilha_cavernicola', formResultados.planilha_cavernicola);
    if (formResultados.consideracoes)
        fd.append('consideracoes', formResultados.consideracoes);

    // Atropelamento
    if (planilhaAtropelamento.value)
        fd.append('planilha_atropelamento', planilhaAtropelamento.value);
    if (consideracoesAtropelamento.value)
        fd.append('consideracoes_atropelamento', consideracoesAtropelamento.value);

    const ok = await salvarEtapa('resultados', fd);
    if (ok) setActiveTab('anexos');
};

const submeterCampanha = async () => {
    // Salva anexos primeiro
    const fd = new FormData();
    Object.entries(anexos.value).forEach(([key, file]) => {
        if (file) fd.append(`anexos[${key}]`, file);
    });

    const okAnexos = await salvarEtapa('anexos', fd);
    if (!okAnexos) return;

    // ────────────────────────────────────────────────────────────────────────────
    // Submete o rascunho
    // ────────────────────────────────────────────────────────────────────────────
    salvando.value = true;
    erroSalvamento.value = null;

    try {
        const url = route('sgc.contratada.produtos.rascunho.submeter', [
            props.contrato,
            props.produto.toLowerCase(),
            campanhaId.value,
        ]);

        console.log('Submetendo campanha para:', url);

        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'X-Inertia': 'false',
            },
        });

        console.log('Response status:', response.status);
        console.log('Response headers:', response.headers);

        // Se a resposta for vazia (409 ou outra sem body)
        if (!response.ok) {
            const text = await response.text();
            console.error('Response body:', text);

            if (response.status === 409) {
                erroSalvamento.value = 'Conflito: Verifique se a campanha já foi submetida ou se há outro rascunho ativo.';
            } else {
                erroSalvamento.value = `Erro ${response.status}: ${text || 'Erro ao submeter campanha.'}`;
            }
            salvando.value = false;
            return;
        }

        // Tenta parsear JSON apenas se houver conteúdo
        let data = null;
        const contentType = response.headers.get('content-type');
        if (contentType && contentType.includes('application/json')) {
            const text = await response.text();
            if (text) data = JSON.parse(text);
        }

        console.log('✅ Campanha submetida com sucesso!', data);
        salvando.value = false;

        // Mostrar sucesso e redirecionar
        alert('✅ Campanha enviada para análise com sucesso!');

        router.visit(
            route('sgc.contratada.produtos.index', [
                props.contrato,
                props.produto.toLowerCase(),
            ])
        );

    } catch (e) {
        console.error('❌ Erro ao submeter campanha:', e);
        erroSalvamento.value = `Erro: ${e.message || 'Tente novamente.'}`;
        salvando.value = false;
    }
};

// ─── ABIO ────────────────────────────────────────────────────────────────────
const vincularAbio = () => {
    if (!formAbio.id_abio) return;
    const abioData = props.abios.find(a => a.id === formAbio.id_abio);
    if (abioData) {
        abioRecords.value.push({ id: abioData.id, abio: { licenca: { numero_licenca: abioData.licenca?.numero_licenca || 'N/A' } } });
        formAbio.id_abio = null;
    }
};
const excluirAbio = (id) => { abioRecords.value = abioRecords.value.filter(i => i.id !== id); };

// ─── PROFISSIONAIS ────────────────────────────────────────────────────────────
const salvarNovoProfissional = () => {
    router.post(
        route('sgc.contratada.produtos.profissional.store', [props.contrato, props.produto.toLowerCase()]),
        { ...formNovoProfissional },
        {
            preserveState: true,
            onSuccess: () => {
                profissionalRecords.value.push({
                    id: Date.now(),
                    profissional: formNovoProfissional.profissional,
                    formacao:     formNovoProfissional.formacao,
                    grupo_faunistico: formProfissional.grupo_faunistico,
                });
                Object.assign(formNovoProfissional, {
                    profissional: '', formacao: '', telefone: '', cpf: '', email: '',
                    curriculum_lattes: '', funcao: '', ctf: '', validade: '',
                    conselho_de_classe: 'Não', numero_de_registro: null,
                    status: 'Ativo', observacao: '',
                });
            },
        }
    );
};
const vincularProfissional = () => {
    if (!formProfissional.profissional || !formProfissional.grupo_faunistico) return;
    const p = props.profissionais.find(x => x.profissional === formProfissional.profissional);
    profissionalRecords.value.push({
        id: Date.now(),
        profissional:     formProfissional.profissional,
        formacao:         p?.formacao || 'N/A',
        grupo_faunistico: formProfissional.grupo_faunistico,
    });
    formProfissional.profissional    = null;
    formProfissional.grupo_faunistico = null;
};
const excluirProfissional = (id) => { profissionalRecords.value = profissionalRecords.value.filter(i => i.id !== id); };

// ─── MÓDULOS AMOSTRAIS ────────────────────────────────────────────────────────
const adicionarModulo = () => {
    if (!formModuloAmostral.data_cadastro || !formModuloAmostral.uf || !formModuloAmostral.bioma) return;
    moduloRecords.value.push({ id: Date.now(), ...formModuloAmostral });
    Object.assign(formModuloAmostral, {
        data_cadastro: null, tamanho_modulo: null, uf: null, municipio: null,
        bioma: null, fitofisionomia: null, latitude_inicial: null,
        longitude_inicial: null, latitude_final: null, longitude_final: null,
        obs: null, arquivo: null,
    });
};
const excluirModulo = (id) => { moduloRecords.value = moduloRecords.value.filter(i => i.id !== id); };

// ─── PONTOS QUELÔNIOS ─────────────────────────────────────────────────────────
const adicionarPonto = () => {
    if (naoSeAplicaQuelonios.value) return;
    if (!formPontosAmostragem.ponto_de_coleta || !formPontosAmostragem.largura) {
        alert('Preencha os campos obrigatórios ou marque "Não se aplica".');
        return;
    }
    pontoRecords.value.push({ id: Date.now(), ...formPontosAmostragem });
    Object.assign(formPontosAmostragem, {
        ponto_de_coleta: '', nome_curso_hidrico: '', latitude: '', longitude: '',
        bacia: '', profundidade: null, largura: null, tipo_substrato: '',
    });
};
const excluirPonto = (id) => { pontoRecords.value = pontoRecords.value.filter(p => p.id !== id); };

// ─── PONTOS CAVERNÍCOLA ───────────────────────────────────────────────────────
const adicionarPontoCavernicola = () => {
    if (naoSeAplicaCavernicola.value) return;
    if (!formPontosCavernicola.cavidade) {
        alert('Preencha os campos obrigatórios ou marque "Não se aplica".');
        return;
    }
    pontoCavernicolaRecords.value.push({ id: Date.now(), ...formPontosCavernicola });
    Object.assign(formPontosCavernicola, {
        cavidade: '', latitude: null, longitude: null, distancia_eixo_rodovia: null,
        formacao_associada: '', temperatura_media_interna: null,
        temperatura_media_externa: null, umidade_relativa_interna: null,
        umidade_relativa_externa: null,
    });
};
const excluirPontoCavernicola = (id) => { pontoCavernicolaRecords.value = pontoCavernicolaRecords.value.filter(p => p.id !== id); };

// ─── METODOLOGIA ─────────────────────────────────────────────────────────────
const adicionarMetodologia = () => {
    if (!formMetodologia.grupo_faunistico || !formMetodologia.metodologia) return;
    metodologiaRecords.value.push({ id: Date.now(), ...formMetodologia });
    formMetodologia.grupo_faunistico = null;
    formMetodologia.metodologia      = '';
};
const excluirMetodologia = (id) => { metodologiaRecords.value = metodologiaRecords.value.filter(i => i.id !== id); };

// ─── ANEXOS ───────────────────────────────────────────────────────────────────
const updateAnexo  = (tipo, file) => { anexos.value[tipo] = file || null; };
const excluirAnexo = (tipo) => { anexos.value[tipo] = null; };

const openPreview = (tipo) => {
    const file = anexos.value[tipo];
    if (!file) return;
    previewFile.value = file;
    previewType.value = file.type.includes('image') ? 'image' : 'pdf';
    previewUrl.value  = URL.createObjectURL(file);
};
const closePreview = () => {
    previewFile.value = null;
    previewUrl.value  = null;
    previewType.value = null;
};
</script>

<template>
    <AuthenticatedLayout>
        <template #header>
            <Breadcrumb
                :links="[
                    { route: route('sgc.gestao.listagem', props.contratos.tipo_contrato), label: 'Gestão de Contratos' },
                    { route: route('sgc.contratada.produtos.index', [props.contrato, props.produto.toLowerCase()]), label: props.contratos.contratada },
                    { route: '#', label: `Cadastrar ${props.produto}` },
                ]"
            />
        </template>

        <NavbarContrato :tipo="{ id: props.contrato }">
            <template #body>
                <div class="card">
                    <div class="card-body">
                        <h2 class="text-center mb-4">{{ isAtropelamento ? 'CADASTRAR ATROPELAMENTO DE FAUNA' : 'CADASTRAR ' + props.produto.toUpperCase() }}</h2>

                        <!-- Aviso de salvamento automático -->
                        <div v-if="campanhaId" class="alert alert-success py-1 mb-3 small">
                            ✅ Rascunho ativo — seus dados são salvos automaticamente ao avançar.
                        </div>

                        <!-- Erro de salvamento -->
                        <div v-if="erroSalvamento" class="alert alert-danger py-1 mb-3 small">
                            ⚠️ {{ erroSalvamento }}
                        </div>

                        <!-- Indicador de salvamento -->
                        <div v-if="salvando" class="alert alert-info py-1 mb-3 small">
                            ⏳ Salvando...
                        </div>

                        <!-- Abas principais -->
                        <ul class="nav nav-tabs mb-4">
                            <li class="nav-item">
                                <a class="nav-link" :class="{ active: activeTab === 'apresentacao' }" @click.prevent="setActiveTab('apresentacao')">Apresentação</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" :class="{ active: activeTab === 'metodologia' }" @click.prevent="setActiveTab('metodologia')">Metodologia</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" :class="{ active: activeTab === 'resultados' }" @click.prevent="setActiveTab('resultados')">Resultados</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" :class="{ active: activeTab === 'anexos' }" @click.prevent="setActiveTab('anexos')">Anexos</a>
                            </li>
                        </ul>

                        <div class="tab-content">

                            <!-- ── ABA APRESENTAÇÃO ─────────────────────────── -->
                            <div v-if="activeTab === 'apresentacao'" class="tab-pane fade show active">

                                <!-- Sub-etapa 1: Empreendimento + Família -->
                                <div v-if="subStep === 1">
                                    <h4 class="mb-3 text-center">APRESENTAÇÃO</h4>
                                    <form @submit.prevent="inicializarRascunho">
                                        <div class="mb-3">
                                            <label class="form-label">Empreendimento</label>
                                            <select v-model="form.cod_emp" class="form-select" required>
                                                <option value="">Selecione um empreendimento</option>
                                                <option v-for="emp in props.empreendimentos" :key="emp" :value="emp">{{ emp }}</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Campanha</label>
                                            <select v-model="form.id_campanha" class="form-select" required>
                                                <option value="">Selecione a campanha</option>
                                                <option v-for="n in 12" :key="n" :value="n">
                                                    Campanha {{ n }}
                                                </option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Família</label>
                                            <input v-model="form.familia" type="text" class="form-control" :disabled="props.produto === 'Fauna'" required />
                                        </div>
                                        <div class="d-flex justify-content-end">
                                            <NavButton type="submit" type-button="primary" title="Avançar" :disabled="salvando" />
                                        </div>
                                        <h4 class="text-center mt-4" style="font-weight: bold; color: #6c757d;">{{ subStep }}/{{ totalSubSteps }}</h4>
                                    </form>
                                </div>

                                <!-- Sub-etapas 2 a N -->
                                <DadosGerais
                                    v-if="subStep === 2"
                                    :form-dados-gerais="formDadosGerais"
                                    :form-abio="formAbio"
                                    :form-profissional="formProfissional"
                                    :form-novo-profissional="formNovoProfissional"
                                    :abios="props.abios"
                                    :profissionais="props.profissionais"
                                    :abio-records="abioRecords"
                                    :profissional-records="profissionalRecords"
                                    @vincular-abio="vincularAbio"
                                    @excluir-abio="excluirAbio"
                                    @salvar-novo-profissional="salvarNovoProfissional"
                                    @vincular-profissional="vincularProfissional"
                                    @excluir-profissional="excluirProfissional"
                                    @next="nextSubStep"
                                    @prev="prevSubStep"
                                >
                                    <template #footer>
                                        <h4 class="text-center mt-4" style="font-weight: bold; color: #6c757d;">{{ subStep }}/5</h4>
                                    </template>
                                </DadosGerais>

                                <!-- SubStep 3: Módulos (fauna regular) -->
                                <ModulosAmostragem
                                    v-if="subStep === 3 && !isAtropelamento"
                                    :form-modulo-amostral="formModuloAmostral"
                                    :ufs="props.ufs.map(uf => ({ uf }))"
                                    :biomas="props.biomas"
                                    :modulo-records="moduloRecords"
                                    @adicionar-modulo="adicionarModulo"
                                    @excluir-modulo="excluirModulo"
                                    @next="nextSubStep"
                                    @prev="prevSubStep"
                                >
                                    <template #footer>
                                        <h4 class="text-center mt-4" style="font-weight: bold; color: #6c757d;">{{ subStep }}/5</h4>
                                    </template>
                                </ModulosAmostragem>

                                <!-- SubStep 3: Campanhas de atropelamento -->
                                <CampanhaAtropelamento
                                    v-if="subStep === 3 && isAtropelamento"
                                    :form-campanha="formCampanha"
                                    :campanha-records="campanhaRecords"
                                    :ufs="props.ufs.map(uf => ({ uf }))"
                                    @adicionar-campanha="adicionarCampanha"
                                    @excluir-campanha="excluirCampanha"
                                    @next="nextSubStep"
                                    @prev="prevSubStep"
                                >
                                    <template #footer>
                                        <h4 class="text-center mt-4" style="font-weight: bold; color: #6c757d;">{{ subStep }}/3</h4>
                                    </template>
                                </CampanhaAtropelamento>

                                <QueloniosCrocodilian
                                    v-if="subStep === 4 && !isAtropelamento"
                                    v-model:naoSeAplica="naoSeAplicaQuelonios"
                                    :form-pontos-amostragem="formPontosAmostragem"
                                    :ponto-records="pontoRecords"
                                    @adicionar-ponto="adicionarPonto"
                                    @excluir-ponto="excluirPonto"
                                    @next="nextSubStep"
                                    @prev="prevSubStep"
                                >
                                    <template #footer>
                                        <h4 class="text-center mt-4" style="font-weight: bold; color: #6c757d;">{{ subStep }}/5</h4>
                                    </template>
                                </QueloniosCrocodilian>

                                <FaunaCavernic
                                    v-if="subStep === 5 && !isAtropelamento"
                                    v-model:naoSeAplica="naoSeAplicaCavernicola"
                                    :form-pontos-cavernicola="formPontosCavernicola"
                                    :ponto-cavernicola-records="pontoCavernicolaRecords"
                                    @adicionar-ponto-cavernicola="adicionarPontoCavernicola"
                                    @excluir-ponto-cavernicola="excluirPontoCavernicola"
                                    @next="nextSubStep"
                                    @prev="prevSubStep"
                                >
                                    <template #footer>
                                        <h4 class="text-center mt-4" style="font-weight: bold; color: #6c757d;">{{ subStep }}/5</h4>
                                    </template>
                                </FaunaCavernic>
                            </div>

                            <!-- ── ABA METODOLOGIA ─────────────────────────── -->
                            <div v-if="activeTab === 'metodologia'" class="tab-pane fade show active">
                                <Metodologia
                                    :form-metodologia="formMetodologia"
                                    :metodologia-records="metodologiaRecords"
                                    @adicionar-metodologia="adicionarMetodologia"
                                    @excluir-metodologia="excluirMetodologia"
                                    @next="salvarMetodologia"
                                    @prev="setActiveTab('apresentacao')"
                                />
                            </div>

                            <!-- ── ABA RESULTADOS ──────────────────────────── -->
                            <div v-if="activeTab === 'resultados'" class="tab-pane fade show active">
                                <!-- Fauna regular -->
                                <FaunaResultados
                                    v-if="!isAtropelamento"
                                    :form-resultados="formResultados"
                                    :resultados-records="resultadosRecords"
                                    :id-campanha="campanhaId ?? props.contrato"
                                    @update:resultadosRecords="resultadosRecords = $event"
                                    @prev="setActiveTab('metodologia')"
                                    @next="salvarResultados"
                                />
                                <!-- Atropelamento: planilha + considerações -->
                                <div v-else>
                                    <h4 class="text-center mb-4 fw-bold">Resultados — Atropelamento de Fauna</h4>
                                    <div class="card p-4 mb-4">
                                        <div class="d-flex gap-3 mb-4">
                                            <a
                                                :href="route('sgc.contratada.produtos.fauna.atropelamento.modelo', [props.contrato, props.produto.toLowerCase()])"
                                                class="btn btn-outline-success"
                                                download
                                            >⬇ Baixar Planilha Modelo</a>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Planilha de Resultados</label>
                                            <input type="file" class="form-control" accept=".xlsx,.xls"
                                                @change="planilhaAtropelamento = $event.target.files[0]" />
                                            <small class="text-muted">Formatos aceitos: .xlsx, .xls (máx. 10MB)</small>
                                            <div v-if="planilhaAtropelamento" class="mt-2 text-success small">
                                                Arquivo: {{ planilhaAtropelamento.name }}
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Considerações</label>
                                            <textarea v-model="consideracoesAtropelamento" class="form-control" rows="4"
                                                placeholder="Descreva as considerações sobre os resultados..."></textarea>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between mt-2">
                                        <button type="button" class="btn btn-outline-secondary px-4" @click="setActiveTab('metodologia')">Voltar</button>
                                        <button type="button" class="btn btn-primary px-4" :disabled="salvando" @click="salvarResultados">{{ salvando ? 'Salvando...' : 'Avançar' }}</button>
                                    </div>
                                </div>
                            </div>

                            <!-- ── ABA ANEXOS ──────────────────────────────── -->
                            <div v-if="activeTab === 'anexos'" class="tab-pane fade show active">
                                <h3 class="text-center mb-4 fw-bold">Anexos</h3>

                                <div class="row g-4">
                                    <div v-for="(label, tipo) in anexosLabelsAtivos" :key="tipo" class="col-md-6">
                                        <div class="anexo-card shadow-sm p-3 rounded bg-white">
                                            <label class="form-label fw-semibold">{{ label }}</label>
                                            <input
                                                type="file"
                                                class="form-control"
                                                :id="tipo"
                                                accept=".pdf,.jpg,.jpeg,.png"
                                                @change="updateAnexo(tipo, $event.target.files[0])"
                                            />
                                            <div v-if="anexos[tipo]" class="mt-3 selected-box">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span class="small fw-semibold">{{ anexos[tipo]?.name }}</span>
                                                    <div class="actions-icons">
                                                        <button type="button" class="icon-btn" @click="openPreview(tipo)" title="Visualizar">
                                                            <IconScanEye />
                                                        </button>
                                                        <button type="button" class="icon-btn text-danger" @click="excluirAnexo(tipo)" title="Excluir">
                                                            <IconTrash />
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div v-if="!Object.keys(anexos).some(k => anexos[k])" class="alert alert-info text-center mt-4">
                                    Nenhum anexo selecionado.
                                </div>

                                <div class="d-flex justify-content-between mt-4">
                                    <button class="btn btn-outline-secondary px-4" @click="setActiveTab('resultados')">
                                        Voltar
                                    </button>
                                    <button
                                        type="button"
                                        class="btn btn-primary px-4 fw-semibold"
                                        :disabled="salvando"
                                        @click="submeterCampanha"
                                    >
                                        {{ salvando ? 'Salvando...' : 'Enviar para Análise' }}
                                    </button>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- Modal de preview de anexo -->
                <div v-if="previewFile" class="preview-modal" @click="closePreview">
                    <div class="preview-content" @click.stop>
                        <button class="btn-close preview-close" @click="closePreview"></button>
                        <img v-if="previewType === 'image'" :src="previewUrl" class="preview-image" />
                        <iframe v-if="previewType === 'pdf'" :src="previewUrl" class="preview-pdf"></iframe>
                    </div>
                </div>

            </template>
        </NavbarContrato>
    </AuthenticatedLayout>
</template>

<style scoped>
.card { border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
.nav-tabs .nav-link { color: #6c757d; font-weight: 500; cursor: pointer; }
.nav-tabs .nav-link.active { color: #007bff; border-bottom: 2px solid #007bff; }
.tab-content { padding: 20px; }
.anexo-card { border: 1px solid #f1f1f1; transition: 0.2s ease-in-out; }
.anexo-card:hover { border-color: #dedede; box-shadow: 0 4px 14px rgba(0,0,0,0.06); }
.selected-box { background: #fafafa; border-radius: 6px; padding: 10px; }
.preview-modal { position: fixed; inset: 0; background: rgba(0,0,0,0.65); display: flex; align-items: center; justify-content: center; z-index: 999999; padding: 20px; }
.preview-content { background: #fff; padding: 15px; border-radius: 8px; position: relative; max-width: 90vw; max-height: 90vh; overflow: hidden; }
.preview-image { max-width: 100%; max-height: 80vh; object-fit: contain; }
.preview-pdf { width: 80vw; height: 80vh; border: none; }
.preview-close { position: absolute; top: 10px; right: 10px; }
.actions-icons { display: flex; align-items: center; gap: 8px; }
.icon-btn { background: #fff; border: 1px solid #dcdcdc; padding: 6px 8px; border-radius: 6px; cursor: pointer; transition: 0.2s; display: flex; align-items: center; }
.icon-btn:hover { background: #f2f2f2; border-color: #bbb; }
.icon-btn.text-danger:hover { background: #ffe9e9; border-color: #ffb3b3; }
</style>
