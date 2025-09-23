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
                  @update-form="updateForm"
                  @vincular-profissional="vincularProfissional"
                  @salvar-novo-profissional="salvarNovoProfissional"
                />
              </div>
              <div v-if="activeTab === 'metodologias'" class="tab-pane fade" :class="{ 'show active': activeTab === 'metodologias' }">
                <h3>Metodologias - A implementar</h3>
                <p>Esta aba será preenchida com os detalhes das metodologias usadas na campanha.</p>
              </div>
              <div v-if="activeTab === 'resultados'" class="tab-pane fade" :class="{ 'show active': activeTab === 'resultados' }">
                <h3>Resultados - A implementar</h3>
                <p>Esta aba será preenchida com os resultados da campanha.</p>
              </div>
              <div v-if="activeTab === 'anexos'" class="tab-pane fade" :class="{ 'show active': activeTab === 'anexos' }">
                <h3>Anexos - A implementar</h3>
                <p>Esta aba será preenchida com os anexos da campanha.</p>
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
import { reactive, ref, onMounted, watch } from 'vue';

const props = defineProps(['contrato', 'produto', 'subproduto', 'empreendimentos', 'campanhaId', 'draftData', 'contratos', 'profissionais']);

const activeTab = ref('apresentacao');
const showModal = ref(false); // Controle do modal

const form = reactive({
    id_campanha: '3', // Fixo como "3"
    cod_emp: props.draftData?.cod_emp || '',
    subproduto: props.subproduto || '',
    subtrecho: props.draftData?.subtrecho || '',
    segmento: props.draftData?.segmento || '',
    extensao: props.draftData?.extensao || '',
    tipo_de_intervencao: props.draftData?.tipo_de_intervencao || '',
    descricao: props.draftData?.descricao || '',
    bioma: props.draftData?.bioma || '',
});

const errors = ref({});
const profissionalRecords = ref(props.draftData?.profissionais ?? []);
const localProfissionais = ref(Array.isArray(props.profissionais) ? [...props.profissionais] : []); // Inicialização segura

const updateForm = (data) => {
    Object.assign(form, data);
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
        selectedProfissional.value = null;
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
        subproduto: props.subproduto, // Preserva subproduto
    };
    router.post(route('sgc.contratada.produtos.espeleo.profissional.store', {
        contrato: props.contrato,
        produto: 'espeleologia',
    }), payload, {
        onSuccess: (response) => {
            alert('Profissional cadastrado com sucesso!');
            localProfissionais.value.push(response.profissional);
            showModal.value = false;
        },
        onError: (err) => console.error('Erro ao cadastrar profissional:', err),
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
        profissionais: profissionalRecords.value.map(p => ({
            campanha_id: props.campanhaId,
            id_contrato: props.contrato,
            profissional_id: p.profissional_id,
            id_modulo: null,
        })),
    };
    console.log('Enviando dados para salvar:', payload);

    router.post(route('sgc.contratada.produtos.espeleo.salvar_campanha', {
        contrato: props.contrato,
        produto: 'espeleologia',
    }), payload, {
        onError: (err) => {
            errors.value = err;
            console.error('Erro ao salvar:', err);
        },
        onSuccess: () => {
            errors.value = {};
            alert('Campanha salva com sucesso');
        },
    });
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

