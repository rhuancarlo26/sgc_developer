<template>
  <AuthenticatedLayout>
    <Head title="Criar Campanha de Espeleologia" />
    <template #header>
      <Breadcrumb
        :links="[
          { route: route('sgc.gestao.listagem', contratos.tipo_contrato), label: `Gestão de Contratos` },
          { route: route('sgc.contratada.produtos.index', [contrato, produto]), label: produto },
          { route: '#', label: 'Criar Campanha' }
        ]"
      />
    </template>
    <NavbarContrato :tipo="{ id: contrato }">
      <template #body>
        <div class="card">
          <div class="card-body">
            <h2 class="text-center mb-4">Criar Campanha de Espeleologia</h2>
            <ul class="nav nav-tabs mb-4">
              <li class="nav-item">
                <a
                  class="nav-link"
                  :class="{ active: activeTab === 'apresentacao' }"
                  @click.prevent="changeTab('apresentacao')"
                  >Apresentação</a
                >
              </li>
              <li class="nav-item">
                <a
                  class="nav-link"
                  :class="{ active: activeTab === 'metodologias' }"
                  @click.prevent="changeTab('metodologias')"
                  >Metodologias</a
                >
              </li>
              <li class="nav-item">
                <a
                  class="nav-link"
                  :class="{ active: activeTab === 'resultados' }"
                  @click.prevent="changeTab('resultados')"
                  >Resultados</a
                >
              </li>
              <li class="nav-item">
                <a
                  class="nav-link"
                  :class="{ active: activeTab === 'anexos' }"
                  @click.prevent="changeTab('anexos')"
                  >Anexos</a
                >
              </li>
            </ul>
            <div class="tab-content">
              <div v-if="activeTab === 'apresentacao'" class="tab-pane fade show active">
                <Apresentacao
                  :campanha="form"
                  :empreendimentos="empreendimentos"
                  :errors="errors"
                  :profissionais="localProfissionais"
                  :profissional-records="profissionalRecords"
                  :justificativas="justificativas"
                  :codigo-sei="codigoSei"
                  :contrato="contrato"
                  :subproduto="subproduto"
                  @update-form="updateForm"
                  @vincular-profissional="vincularProfissional"
                  @salvar-novo-profissional="salvarNovoProfissional"
                  @update:justificativas="updateJustificativas"
                  @update:codigo-sei="updateCodigoSei"
                  @excluir-profissional="excluirProfissional"
                />
              </div>
              <div v-if="activeTab === 'metodologias'" class="tab-pane fade" :class="{ 'show active': activeTab === 'metodologias' }">
                <Metodologias
                  :metodologia="form.metodologia"
                  :errors="errors"
                  @update-metodologia="updateMetodologia"
                />
              </div>
              <div v-if="activeTab === 'resultados'" class="tab-pane fade" :class="{ 'show active': activeTab === 'resultados' }">
                <Resultados
                  :empreendimentos="empreendimentos"
                  :errors="errors"
                  :campanha-id="campanhaId"
                  :contrato="contrato"
                  :resultados-anexos="form.resultados_anexos"
                  @update-resultados-anexos="updateResultadosAnexos"
                />
              </div>
              <div v-if="activeTab === 'anexos'" class="tab-pane fade" :class="{ 'show active': activeTab === 'anexos' }">
                <Anexos
                  :campanha-id="campanhaId"
                  :contrato="contrato"
                  :anexos="anexos"
                  :errors="errors"
                  @update-anexos="anexos = $event"
                />
              </div>
            </div>
            <button @click="salvar" class="btn btn-primary mt-3">Salvar</button>
          </div>
        </div>
      </template>
    </NavbarContrato>
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import NavbarContrato from '@/Pages/Sgc/Contratada/NavbarContrato.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import Apresentacao from './Apresentacao.vue';
import Metodologias from './Metodologias.vue';
import Resultados from './Resultados.vue';
import Anexos from './Anexos.vue';
import { reactive, ref, onMounted } from 'vue';
import { useToast } from 'vue-toastification';

const toast = useToast();

const props = defineProps({
  contrato: [String, Number],
  produto: String,
  subproduto: String,
  empreendimentos: Array,
  campanhaId: [String, Number],
  draftData: Object,
  contratos: Object,
  profissionais: Array,
  justificativas: Array,
  codigoSei: String,
  resultadosAnexos: Array,
});

const activeTab = ref('apresentacao');
const anexos = ref([]);
const form = reactive({
  id_campanha: '3',
  cod_emp: props.draftData?.cod_emp || '',
  subproduto: props.subproduto || '',
  subtrecho: props.draftData?.subtrecho || '',
  segmento: props.draftData?.segmento || '',
  extensao: props.draftData?.extensao || '',
  tipo_de_intervencao: props.draftData?.tipo_de_intervencao || '',
  descricao: props.draftData?.descricao || '',
  bioma: props.draftData?.bioma || '',
  coordenadas: props.draftData?.coordenadas || null,
  metodologia: props.draftData?.metodologia || '',
  resultados_anexos: props.resultadosAnexos || [],
  anexos_fotos: props.draftData?.anexos_fotos || [],
});

const errors = ref({});
const profissionalRecords = ref(props.draftData?.profissionais ?? []);
const localProfissionais = ref(Array.isArray(props.profissionais) ? [...props.profissionais] : []);
const justificativas = ref(props.justificativas || [{ justificativa: '', tipo: 'citacao', titulo: '', codigo_sei: props.codigoSei || '' }]);
const codigoSei = ref(props.codigoSei || '');

onMounted(() => {
  console.log('Props recebidas em Create.vue:', {
    subproduto: props.subproduto,
    campanhaId: props.campanhaId,
    profissionais: props.profissionais,
    currentUrl: window.location.href,
  });
});

const updateForm = (data) => {
  Object.assign(form, data);
  console.log('Form atualizado:', form);
};

const updateJustificativas = (newValue) => {
  justificativas.value = newValue;
  console.log('Justificativas atualizadas:', justificativas.value);
};

const updateCodigoSei = (newValue) => {
  codigoSei.value = newValue;
  console.log('Código SEI atualizado:', codigoSei.value);
  if (justificativas.value.length > 0) {
    justificativas.value[0].codigo_sei = newValue;
  }
};

const updateMetodologia = (value) => {
  form.metodologia = value;
  console.log('Metodologia atualizada:', value);
};

const updateResultadosAnexos = (newAnexos) => {
  form.resultados_anexos = newAnexos;
  console.log('Anexos de resultados atualizados:', newAnexos);
};

const vincularProfissional = (profissional) => {
  console.log('Tentando vincular profissional:', profissional);
  if (profissional && profissional.profissional) {
    profissionalRecords.value.push({
      id: Date.now(),
      profissional_id: profissional.id,
      profissional: profissional.profissional,
      formacao: profissional.formacao,
    });
    console.log('Profissional vinculado:', profissionalRecords.value);
  } else {
    console.log('Nenhum profissional selecionado ou inválido');
  }
};

const salvarNovoProfissional = (novoProfissional) => {
  const payload = {
    id_contrato: props.contrato,
    profissional: novoProfissional.profissional,
    formacao: novoProfissional.formacao,
    telefone: novoProfissional.telefone,
    cpf: novoProfissional.cpf,
    email: novoProfissional.email,
    curriculum_lattes: novoProfissional.curriculum_lattes,
    funcao: novoProfissional.funcao,
    ctf: novoProfissional.ctf,
    validade: novoProfissional.validade,
    conselho_de_classe: novoProfissional.conselho_de_classe,
    numero_de_registro: novoProfissional.numero_de_registro,
    status: novoProfissional.status,
    observacao: novoProfissional.observacao,
    subproduto: props.subproduto,
  };

  console.log('Enviando POST para salvar profissional:', {
    url: route('sgc.contratada.produtos.espeleo.profissional.store', {
      contrato: props.contrato,
      produto: 'espeleologia',
    }) + (props.subproduto ? `?subproduto=${encodeURIComponent(props.subproduto)}` : ''),
    payload,
    currentUrl: window.location.href,
  });

  router.post(
    route('sgc.contratada.produtos.espeleo.profissional.store', {
      contrato: props.contrato,
      produto: 'espeleologia',
    }) + (props.subproduto ? `?subproduto=${encodeURIComponent(props.subproduto)}` : ''),
    payload,
    {
      preserveState: true,
      preserveScroll: true,
      replace: true,
      onSuccess: (page) => {
        console.log('Resposta do POST salvar profissional:', {
          page,
          currentUrl: window.location.href,
          flash: page.props.flash,
        });
        const { success, profissional } = page.props.flash || {};
        if (success && profissional) {
          localProfissionais.value = [...localProfissionais.value, profissional];
          console.log('localProfissionais atualizado:', localProfissionais.value);
          toast.success(success || 'Profissional cadastrado com sucesso');
        } else {
          toast.success('Profissional cadastrado com sucesso');
        }
        const url = route('sgc.contratada.produtos.create', {
          contrato: props.contrato,
          produto: props.produto,
        }) + (props.subproduto ? `?subproduto=${encodeURIComponent(props.subproduto)}` : '');
        console.log('Forçando navegação para:', url);
        router.visit(url, { preserveState: true, preserveScroll: true, replace: true });
      },
      onError: (err) => {
        console.error('Erro ao cadastrar profissional:', err);
        toast.error('Erro ao cadastrar profissional: ' + Object.values(err)[0]);
      },
    }
  );
};

const salvar = () => {
  const payload = {
    id: props.campanhaId,
    id_campanha: form.id_campanha,
    cod_emp: form.cod_emp,
    subproduto: form.subproduto,
    subtrecho: form.subtrecho || null,
    segmento: form.segmento || null,
    extensao: form.extensao || null,
    tipo_de_intervencao: form.tipo_de_intervencao || null,
    descricao: form.descricao || null,
    bioma: form.bioma || null,
    coordenadas: form.coordenadas || null,
    metodologia: form.metodologia || null,
    anexos_fotos: form.anexos_fotos,
    profissionais: profissionalRecords.value.map(p => ({
      campanha_id: props.campanhaId,
      id_contrato: props.contrato,
      profissional_id: p.profissional_id,
      id_modulo: null,
    })),
    codigo_sei: codigoSei.value,
    justificativas: justificativas.value.map((j, i) => ({
      ...j,
      tipo: i === 0 ? 'citacao' : 'justificativa',
    })),
    resultados_anexos: form.resultados_anexos,
  };

  console.log('Enviando POST para salvar campanha:', {
    url: route('sgc.contratada.produtos.espeleo.salvar_campanha', {
      contrato: props.contrato,
      produto: 'espeleologia',
    }) + (props.subproduto ? `?subproduto=${encodeURIComponent(props.subproduto)}` : ''),
    payload: JSON.stringify(payload, null, 2),
    currentUrl: window.location.href,
  });

  router.post(
    route('sgc.contratada.produtos.espeleo.salvar_campanha', {
      contrato: props.contrato,
      produto: 'espeleologia',
    }) + (props.subproduto ? `?subproduto=${encodeURIComponent(props.subproduto)}` : ''),
    payload,
    {
      preserveState: true,
      preserveScroll: true,
      replace: true,
      onSuccess: (page) => {
        console.log('Resposta do POST salvar campanha:', {
          page,
          currentUrl: window.location.href,
          flash: page.props.flash,
        });
        errors.value = {};
        toast.success('Campanha salva com sucesso');
        justificativas.value = [{ justificativa: '', tipo: 'citacao', titulo: '', codigo_sei: '' }];
        codigoSei.value = '';
      },
      onError: (err) => {
        errors.value = err;
        console.error('Erro ao salvar campanha:', err);
        toast.error('Erro ao salvar campanha: ' + Object.values(err)[0]);
      },
    }
  );
};

const excluirProfissional = (id) => {
  profissionalRecords.value = profissionalRecords.value.filter(p => p.id !== id);
  console.log('Profissional excluído, nova lista:', profissionalRecords.value);
};

const changeTab = (tab) => {
  activeTab.value = tab;
  const url = route('sgc.contratada.produtos.create', {
    contrato: props.contrato,
    produto: props.produto,
  }) + (props.subproduto ? `?subproduto=${encodeURIComponent(props.subproduto)}` : '');
  console.log('Mudando para aba:', { tab, url, currentUrl: window.location.href });
  router.get(url, {}, { preserveState: true, preserveScroll: true, replace: true });
};
</script>

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
</style>