<script setup>
import { Head } from "@inertiajs/vue3";
import { onMounted, ref } from "vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import Navbar from "../Navbar.vue";
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

const filteredRelatorios = ref([]);

const filtrarRelatorios = () => {
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
        </div>
      </template>

      <Navbar :contrato="contrato">
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
                <table class="table table-striped">
                  <thead>
                    <tr style="background-color: #237D9E; text-align: left;">
                      <th scope="col">RELATÓRIO Nº</th>
                      <th scope="col">PERÍODO</th>
                      <th scope="col">RELATÓRIO</th>
                      <th scope="col">STATUS</th>
                      <th scope="col">ACESSAR</th>
                      <th scope="col">REVISÕES</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="relatorio in filteredRelatorios" :key="relatorio.id">
                      <td>{{ relatorio.relatorio_num }}</td>
                      <td>{{ relatorio.periodo }}</td>
                      <td>{{ relatorio.nome_relatorio }}</td>
                      <td>
                        <span :class="['badge', getStatusBadgeClass(relatorio.status)]">
                          {{ relatorio.status }}
                        </span>
                      </td>
                      <td class="list-unstyled">
                        <NavLink 
                          :route-name="'sgc.contratada.relatorio.detalhes'"
                          :param="[contrato.id, relatorio.relatorio_num]"
                          title="Acessar Relatório"
                          :icon="IconDoorEnter"
                        />
                      </td>
                      <td>
                        <select @change="goToVersion(contrato.id, relatorio.relatorio_num, $event.target.value)">
                          <option value="" disabled selected>REV</option>
                          <option v-if="relatorio.historicos.length" :value="relatorio.historicos[0].versao">
                            REV {{ relatorio.historicos[0].versao }}
                          </option>
                        </select>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </template>
      </Navbar>
    </AuthenticatedLayout>
  </div>
</template>

<script>
export default {
  methods: {
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
    goToVersion(contratoId, relatorioNum, versao) {
      if (versao) {
        this.$inertia.get(route('sgc.contratada.relatorio.historico', { contrato: contratoId, relatorio_num: relatorioNum, versao: versao }));
      }
    }
  }
}
</script>
