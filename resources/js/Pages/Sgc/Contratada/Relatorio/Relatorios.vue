<script setup>
import { Head } from "@inertiajs/vue3";
import { onMounted, ref } from "vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import NavbarContrato from "../NavbarContrato.vue";
import NavLink from '@/Components/NavLink.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import { defineProps } from 'vue';
import { IconDoorEnter } from "@tabler/icons-vue";

const user = usePage().props.auth.user;

const props = defineProps({
  contrato: Object,
  dadosrelat: { type: Array }
});

const form = ref({
  id: null,
  contrato_id: props.contrato.id,
  item_id: null
});

// Método para verificar se há históricos para o contrato atual
const hasHistoricosForContrato = (relatorio) => {
  console.log('Verificando históricos para contrato:', props.contrato.id, 'Relatório:', relatorio.relatorio_num, 'Históricos:', relatorio.historicos);
  const hasHistoricos = relatorio.historicos && relatorio.historicos.length > 0 && relatorio.historicos.some(h => h.contrato_id == props.contrato.id);
  console.log('Tem históricos para o contrato?', hasHistoricos);
  return hasHistoricos;
};

// Método para obter a primeira versão de um histórico válido
const getFirstVersionForContrato = (relatorio) => {
  console.log('Obtendo primeira versão para contrato:', props.contrato.id, 'Relatório:', relatorio.relatorio_num, 'Históricos:', relatorio.historicos);
  const historico = relatorio.historicos.find(h => h.contrato_id == props.contrato.id);
  console.log('Primeiro histórico encontrado:', historico);
  return historico ? historico.versao : 0; // Retorna 0 se não houver históricos
};

// Método para obter a versão mais recente
const getLatestVersion = (relatorio) => {
  console.log('Calculando versão mais recente para contrato:', props.contrato.id, 'Relatório:', relatorio.relatorio_num, 'Históricos:', relatorio.historicos);
  if (relatorio.historicos && relatorio.historicos.length > 0) {
    const filteredHistoricos = relatorio.historicos.filter(h => h.contrato_id == props.contrato.id);
    console.log('Históricos filtrados:', filteredHistoricos);
    if (filteredHistoricos.length > 0) {
      return Math.max(...filteredHistoricos.map(h => h.versao)) + 1;
    }
  }
  console.log('Sem históricos válidos, retornando 0');
  return 0;
};

const filteredRelatorios = ref([]);

const filtrarRelatorios = () => {
  console.log('Filtrando relatórios para contrato:', props.contrato.id, 'Dados recebidos:', props.dadosrelat);
  const seen = new Set();
  filteredRelatorios.value = props.dadosrelat.filter(relatorio => {
    if (seen.has(relatorio.relatorio_num)) {
      return false;
    }
    seen.add(relatorio.relatorio_num);
    return true;
  });
};

onMounted(() => {
  console.log('Contrato ID (onMounted):', props.contrato.id);
  filtrarRelatorios();
});
</script>

<template>
  <div>
    <Head :title="`${contrato.contratada.slice(0, 10)}...`" />
    <AuthenticatedLayout>
      <template #header>
        <div class="w-100 d-flex justify-content-between">
          <Breadcrumb
            class="align-self-center"
            :links="[
              { route: route('contratos.gestao.listagem', contrato.tipo_contrato), label: `Gestão de Contratos` },
              { route: '#', label: contrato.contratada }
            ]"
          />
          <div>
            <button @click="iniciarNovoRelatorio" class="btn btn-info me-2 w-500">
              <i class="fas fa-file-alt"></i> Gerar Novo Relatório
            </button>
          </div>
        </div>
      </template>

      <NavbarContrato :tipo="contrato">
        <template #body>
          <div class="container mt-4">
            <div class="row mb-3">
              <div class="col-12 text-center">
                <h3 class="titulo-relatorio">
                  RELATÓRIO DE COORDENAÇÃO E EXECUÇÃO DOS SERVIÇOS
                </h3>
              </div>
            </div>
            
            <div class="row">
              <div class="col-12">
                <div class="table-responsive">
                <table class="table table-striped">
                  <thead>
                    <tr style="background-color: #237D9E; text-align: left;">
                      <th scope="col">RELATÓRIO Nº</th>
                      <th scope="col">PERÍODO</th>
                      <th scope="col">STATUS</th>
                      <th scope="col">VERSÕES</th>
                      <th scope="col">ACESSAR</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="relatorio in filteredRelatorios" :key="relatorio.id">
                      <td>{{ relatorio.relatorio_num }}</td>
                      <td>{{ relatorio.periodo }}</td>
                      <td>
                        <span :class="['badge', getStatusBadgeClass(relatorio.status)]">
                          {{ relatorio.status }}
                        </span>
                      </td>
                      <td>
                        <!-- Caso sem históricos: apenas "VER 0" -->
                        <select v-if="!hasHistoricosForContrato(relatorio)" @change="goToVersion(contrato.id, relatorio.relatorio_num, $event.target.value)">
                          <option value="" selected>VER 0</option>
                        </select>
                        <!-- Caso com históricos: "REV" com versão mais recente e "VER 0" -->
                        <select v-else @change="goToVersion(contrato.id, relatorio.relatorio_num, $event.target.value)">
                          <option :value="''" selected>
                            REV {{ getLatestVersion(relatorio) }}
                          </option>
                          <option :value="getFirstVersionForContrato(relatorio)">
                            VER 0
                          </option>
                        </select>
                      </td>
                      <td class="list-unstyled">
                        <NavLink 
                          :route-name="'sgc.contratada.relatorio.detalhes'"
                          :param="[contrato.id, relatorio.relatorio_num]"
                          title="Acessar Relatório"
                          :icon="IconDoorEnter"
                        />
                      </td>
                    </tr>
                  </tbody>
                </table>
                </div>
              </div>
            </div>
          </div>
        </template>
      </NavbarContrato>
    </AuthenticatedLayout>
  </div>
</template>

<script>
export default {
  methods: {
    // Método para obter a classe do badge de status
    getStatusBadgeClass(status) {
      switch(status) {
        case 'Análise DNIT':
          return 'text-bg-warning';
        case 'Revisão Contratada':
          return 'text-bg-primary';
        case 'Relatório Aprovado':
          return 'text-bg-success';
        default:
          return 'text-bg-secondary';
      }
    },
    // Método para navegar para uma versão específica do relatório
    goToVersion(contratoId, relatorioNum, versao) {
      if (versao) {
        this.$inertia.get(route('sgc.contratada.relatorio.historico', { contrato: contratoId, relatorio_num: relatorioNum, versao: versao }));
      }
    },
    // Método para iniciar um novo relatório
    async iniciarNovoRelatorio() {
      try {
        // Pega o contrato da página atual
        const contrato = this.$page.props.contrato || 1;

        await this.$inertia.post(route('sgc.contratada.relatorio.iniciar'), { contrato });
        alert('Relatório criado com sucesso!');
        window.location.reload();
      } catch (error) {
        console.error('Erro ao iniciar novo relatório:', error);
        alert('Erro ao criar o relatório.');
      }
    },
  },
};
</script>