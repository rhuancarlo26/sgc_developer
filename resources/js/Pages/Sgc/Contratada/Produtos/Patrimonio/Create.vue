<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import axios from 'axios';
import NavbarContrato from '@/Pages/Sgc/Contratada/NavbarContrato.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import { ref, computed } from 'vue';
import Apresentacao from "@/Pages/Sgc/Contratada/Produtos/Patrimonio/Apresentacao.vue";
import Metodologia from "@/Pages/Sgc/Contratada/Produtos/Patrimonio/Metodologia.vue";

const props = defineProps({
  contrato: { type: [Number, String], required: true },
  produto: { type: String, default: 'Patrimonio' },
  contratos: { type: Object, required: true },
  subproduto: { type: String, default: '' },
  empreendimentos: { type: Array, default: () => [] },
  paipa: { type: Object, default: null },
  paipaId: { type: [Number, String, null], default: null },
  profissionais: { type: Array, default: () => [] },
});

const empreendimentoSelecionadoId = ref(props.paipa?.empreendimento_id ?? '');
const etapa = ref(props.paipa?.empreendimento_id ? 'apresentacao' : 'selecao');
const paipaAtualId = ref(props.paipaId);

const empreendimentoSelecionado = computed(() => {
  return props.empreendimentos.find(
    empreendimento => Number(empreendimento.id) === Number(empreendimentoSelecionadoId.value)
  );
});

const avancar = async () => {
  if (!empreendimentoSelecionado.value) {
    return;
  }

  // No primeiro acesso ao Patrimonio, ainda nao existe paipa_id.
  // Permitimos seguir para apresentacao e tentamos persistir quando houver ID.
  if (!paipaAtualId.value) {
    etapa.value = 'apresentacao';
    return;
  }

  try {
    await axios.post(route('patrimonio.store', {
      contrato: props.contrato,
      produto: 'patrimonio',
    }), {
      tipo: 'paipa',
      paipa_id: paipaAtualId.value,
      empreendimento_id: empreendimentoSelecionado.value.id,
    });

    etapa.value = 'apresentacao';
  } catch (error) {
    console.error('Erro ao salvar empreendimento do PAIPA:', error);
  }
};
const salvarApresentacao = async (payload) => {
  try {
    const response = await axios.post(route('patrimonio.store', {
      contrato: props.contrato,
      produto: 'patrimonio',
    }), {
      tipo: 'paipa',
      ...payload,
    });
    if (response.data?.data?.id) {
      paipaAtualId.value = response.data.data.id;
    }
  } catch (error) {
    console.error('Erro ao salvar apresentacao do Patrimonio:', error);
  }

  etapa.value = 'metodologia';
};
const voltar = () => {
  router.get(route('sgc.contratada.produtos.index', [props.contrato, 'patrimonio']));
};
</script>

<template>
  <AuthenticatedLayout>
    <Head :title="`${produto} - Contrato ${contrato}`" />

    <template #header>
      <div class="w-100 d-flex justify-content-between">
        <Breadcrumb
          class="align-self-center"
          :links="[
            { route: route('sgc.gestao.listagem', contratos.tipo_contrato), label: 'Gestão de Contratos' },
            { route: '#', label: contratos.contratada }
          ]"
        />
      </div>
    </template>

    <NavbarContrato :tipo="{ id: Number(contrato) }">
      <template #body>
        <div class="card">
          <div class="card-body">
            <h2 class="text-center mb-4">Patrimônio</h2>

            <div class="alert alert-info mb-4">
              Subproduto selecionado: <strong>{{ subproduto || 'Não informado' }}</strong>
            </div>

            <div v-if="etapa === 'selecao'">
              <div class="mb-4">
                <label class="form-label">Empreendimento</label>
                <select class="form-select" v-model="empreendimentoSelecionadoId">
                  <option value="">Selecione</option>
                  <option v-for="empreendimento in empreendimentos"
                        :key="empreendimento.id"
                        :value="empreendimento.id">
                    {{ empreendimento.cod_emp }}
                  </option>
                </select>
              </div>
              <div class="d-flex justify-content-between">
                <button type="button" class="btn btn-secondary" @click="voltar">
                  Voltar
                </button>
                <button type="button"
                    class="btn btn-primary"
                    :disabled="!empreendimentoSelecionadoId"
                    @click="avancar">
                  Avançar
                </button>
              </div>
            </div>
            <Apresentacao
              v-else-if="etapa === 'apresentacao'"
              :empreendimento="empreendimentoSelecionado"
              :subproduto="subproduto"
              :paipa-id="paipaAtualId"
              :paipa="paipa"
              :profissionais="profissionais"
              @voltar="etapa = 'selecao'"
              @salvar-apresentacao="salvarApresentacao"
            />
            <Metodologia
              v-else-if="etapa === 'metodologia'"
              :empreendimento="empreendimentoSelecionado"
              :subproduto="subproduto"
              :paipa-id="paipaAtualId"
              :contrato="contrato"
              @voltar="etapa = 'apresentacao'"
            />
          </div>
        </div>
      </template>
    </NavbarContrato>
  </AuthenticatedLayout>
</template>
