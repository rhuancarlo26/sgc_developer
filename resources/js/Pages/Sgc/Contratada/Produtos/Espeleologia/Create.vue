<template>
  <AuthenticatedLayout>
    <Head title="Criar Campanha de Espeleologia - Apresentação" />
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
            <h2 class="text-center mb-4">Criar Campanha de Espeleologia - Apresentação</h2>
            <Apresentacao :campanha="form" :empreendimentos="empreendimentos" :errors="errors" @salvar="salvar" @update-form="updateForm" />
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
import { reactive, ref } from 'vue';

const props = defineProps(['contrato', 'produto', 'subproduto', 'empreendimentos', 'campanhaId', 'draftData', 'contratos']);

const form = reactive({
    id_campanha: '3', 
    subproduto: props.subproduto || '',
    subtrecho: props.draftData.subtrecho || '',
    segmento: props.draftData.segmento || '',
    extensao: props.draftData.extensao || '',
    tipo_de_intervencao: props.draftData.tipo_de_intervencao || '',
    descricao: props.draftData.descricao || '',
    bioma: props.draftData.bioma || '',
});

const errors = ref({});

const updateForm = (data) => {
    Object.assign(form, data);
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
    };
    console.log('Enviando dados para salvar:', payload);

    // Ajuste na rota para evitar duplicação
    router.post(route('sgc.contratada.produtos.espeleo.salvar_campanha', {
        contrato: props.contrato,
        produto: 'espeleologia', // Forçar o produto correto
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