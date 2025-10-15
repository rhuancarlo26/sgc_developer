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
                  @click.prevent="activeTab = 'apresentacao'"
                  >Apresentação</a
                >
              </li>
              <li class="nav-item">
                <a
                  class="nav-link"
                  :class="{ active: activeTab === 'metodologias' }"
                  @click.prevent="activeTab = 'metodologias'"
                  >Metodologias</a
                >
              </li>
              <li class="nav-item">
                <a
                  class="nav-link"
                  :class="{ active: activeTab === 'resultados' }"
                  @click.prevent="activeTab = 'resultados'"
                  >Resultados</a
                >
              </li>
              <li class="nav-item">
                <a
                  class="nav-link"
                  :class="{ active: activeTab === 'anexos' }"
                  @click.prevent="activeTab = 'anexos'"
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
                  @update-form="updateForm"
                  @vincular-profissional="vincularProfissional"
                  @salvar-novo-profissional="salvarNovoProfissional"
                  @update-justificativas="updateJustificativas"
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
                <!-- request aqui: {{ form.subproduto }} -->
                <Resultados
                  :empreendimentos="empreendimentos"
                  :errors="errors"
                  :campanha-id="campanhaId"
                  :contrato="contrato"
                  :categoria_subproduto="definecategoriaSubproduto(form.subproduto)"
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
import { reactive, ref, onMounted, watch } from 'vue';

const props = defineProps([
  'contrato',
  'produto',
  'subproduto',
  'empreendimentos',
  'campanhaId',
  'draftData',
  'contratos',
  'profissionais',
  'justificativas',
  'codigoSei',
  'resultadosAnexos', // Novo
]);

const activeTab = ref('apresentacao');
const showModal = ref(false);

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
  metodologia: props.draftData?.metodologia || '',
  resultados_anexos: props.resultadosAnexos || [],
  anexos_fotos: props.draftData?.anexos_fotos || [],
});

// função que captura o trecho selecionado duma string e retorna true ou false
const definecategoriaSubproduto = (subproduto) => {
  const categorias = ['Prospecção', 'Potencial'];
    // return categorias.some(categoria => subproduto.toLowerCase().includes(categoria));
    // retorna o nome da categoria pelo nome do subproduto
    // se não encontrar, retorna 'Outro tipo'
    for (const categoria of categorias) {
        if (subproduto.toLowerCase().includes(categoria.toLowerCase())) {
            return categoria;
        }
    }
    return 'Outro tipo';
};
console.log('Categoria do subproduto:', definecategoriaSubproduto(props.subproduto));

const errors = ref({});
const profissionalRecords = ref(props.draftData?.profissionais ?? []);
const localProfissionais = ref(Array.isArray(props.profissionais) ? [...props.profissionais] : []);
const justificativas = ref(props.justificativas || [{ justificativa: '', tipo: 'citacao', titulo: '', codigo_sei: '' }]);
const codigoSei = ref(props.codigoSei || '');

const updateForm = (data) => {
  Object.assign(form, data);
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

const updateAnexosFotos = (newAnexos) => {
  form.anexos_fotos = newAnexos;
  console.log('Anexos de fotos atualizados:', newAnexos);
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
  router.post(route('sgc.contratada.produtos.espeleo.profissional.store', {
    contrato: props.contrato,
    produto: 'espeleologia',
  }), payload, {
    onSuccess: (page) => {
      const { success, profissional } = page.props.flash || {};
      if (success && profissional) {
        alert(success);
        localProfissionais.value = page.props.profissionais || [];
      }
      showModal.value = false;
    },
    onError: (err) => {
      console.error('Erro ao cadastrar profissional:', err);
      showModal.value = false;
    },
  });
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
    resultados_anexos: form.resultados_anexos, // Novo
  };
  console.log('Enviando dados:', JSON.stringify(payload, null, 2));
  router.post(route('sgc.contratada.produtos.espeleo.salvar_campanha', {
    contrato: props.contrato,
    produto: 'espeleologia',
  }), payload, {
    onError: (err) => {
      errors.value = err;
      console.error(err);
    },
    onSuccess: () => {
      errors.value = {};
      alert('Campanha salva com sucesso');
      justificativas.value = [{ justificativa: '', tipo: 'citacao', titulo: '', codigo_sei: '' }];
      codigoSei.value = '';
    },
  });
};

const excluirProfissional = (id) => {
  profissionalRecords.value = profissionalRecords.value.filter(p => p.id !== id);
  console.log('Profissional excluído, nova lista:', profissionalRecords.value);
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
