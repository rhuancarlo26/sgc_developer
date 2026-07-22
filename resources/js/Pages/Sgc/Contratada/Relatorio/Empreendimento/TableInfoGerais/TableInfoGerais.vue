<script setup>
import { computed, ref } from 'vue';

const props = defineProps({
  contrato: Object,
  empreendimentos: Array, 
  empreendimentos2: Object, 
  estudos: Object,
  subprodutos: Object,
  campanhas_por_produto: {
    type: Array,
    default: () => []
  },
  campanhas_detalhadas: {
    type: Array,
    default: () => []
  }
});

const campanhasEncontradas = computed(() => {
  return (props.campanhas_por_produto || [])
    .filter(item => Number(item?.quantidade) > 0)
    .sort((a, b) => a.produto.localeCompare(b.produto, 'pt-BR'));
});

const campanhasDetalhadasAgrupadas = computed(() => {
  const grupos = campanhasFiltradas.value.reduce((acc, campanha) => {
    const produto = campanha?.produto || 'Outros';
    if (!acc[produto]) acc[produto] = [];
    acc[produto].push(campanha);
    return acc;
  }, {});

  return Object.entries(grupos)
    .sort((a, b) => a[0].localeCompare(b[0], 'pt-BR'))
    .map(([produto, campanhas]) => ({ produto, campanhas }));
});

const produtosDisponiveis = computed(() => {
  return [...new Set((props.campanhas_detalhadas || []).map(campanha => campanha?.produto).filter(Boolean))]
    .sort((a, b) => a.localeCompare(b, 'pt-BR'));
});

const produtoSelecionado = ref('Todos');

const campanhasFiltradas = computed(() => {
  return (props.campanhas_detalhadas || []).filter((campanha) => {
    const produto = campanha?.produto || '';
    const isAprovada = String(campanha?.status || '').trim().toLowerCase() === 'aprovada';

    if (!isAprovada) {
      return false;
    }

    return produtoSelecionado.value === 'Todos' || produto === produtoSelecionado.value;
  });
});

const formatarData = (valor) => {
  if (!valor) return 'N/A';
  const data = new Date(valor);
  return Number.isNaN(data.getTime()) ? 'N/A' : data.toLocaleDateString('pt-BR');
};

const formatarSei = (valor) => {
  return valor ? valor : 'N/A';
};

const getVisualizarRoute = (campanha) => {
  const contratoId = campanha?.contrato_id || props.empreendimentos2?.contrato_id || props.contrato?.id;
  if (!contratoId || !campanha?.campanha_id) return '#';

  if (campanha.produto === 'Fauna') {
    return route('sgc.contratada.produtos.show', [contratoId, 'fauna', campanha.campanha_id]);
  }

  if (campanha.produto === 'Espeleologia') {
    return route('sgc.contratada.produtos.espeleo.show', [contratoId, 'espeleologia', campanha.campanha_id]);
  }

  if (campanha.produto === 'PMQA') {
    return route('contratos.contratada.sgc.pmqa.configuracao.ponto.index', [contratoId, 'eia', campanha.campanha_id]);
  }

  if (campanha.produto === 'Malarígeno') {
    return route('sgc.contratada.produtos.malarigeno.show', [contratoId, 'malarigeno', campanha.campanha_id]);
  }

  return '#';
};

</script>

<template>
  <div class="container-cards mt-4">
    <div class="card-info">
      <h3 class="card-title">CAMPANHAS POR PRODUTO</h3>

      <div class="top-bar">
        <div class="filtro-container">
          <label for="filtro-produto" class="filtro-label">
            Filtrar família/produto
          </label>

          <select
            id="filtro-produto"
            v-model="produtoSelecionado"
            class="form-select form-select-sm filtro-select"
          >
            <option value="Todos">Todos</option>

            <option
              v-for="produto in produtosDisponiveis"
              :key="produto"
              :value="produto"
            >
              {{ produto }}
            </option>
          </select>
        </div>

        <div
          v-if="campanhasEncontradas.length"
          class="contador-container"
        >
          <div
            v-for="item in campanhasEncontradas"
            :key="item.produto"
            class="contador-badge"
          >
            <span class="contador-nome">
              {{ item.produto }}
            </span>

            <span class="contador-valor">
              {{ item.quantidade }}
            </span>
          </div>
        </div>
      </div>

      <p
        v-if="!campanhasDetalhadasAgrupadas.length"
        class="empty-state"
      >
        Não existem dados aprovados no sistema. Favor verificar com a fiscalização do contrato.
      </p>

      <div
        v-else
        class="lista-campanhas"
      >
        <div
          v-for="grupo in campanhasDetalhadasAgrupadas"
          :key="grupo.produto"
          class="grupo-campanhas"
        >
          <div class="grupo-titulo">
            {{ grupo.produto }}
          </div>

        <table class="table table-sm table-bordered tabela-campanhas mb-0">
            <colgroup>
                <col style="width: 10%">
                <col style="width: 60%">
                <col style="width: 10%">
                <col style="width: 10%">
                <col style="width: 10%">
                <col style="width: 10%">
                <col style="width: 10%">  
            </colgroup>

            <thead>
                <tr>
                    <th class="text-center">Campanha</th>
                    <th class="text-center">Subproduto</th>
                    <th class="text-center">Data Inicial</th>
                    <th class="text-center">Data Final</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">SEI DNIT</th>
                    <th class="text-center">Ação</th>
                </tr>
            </thead>

            <tbody>
              <tr
                v-for="item in grupo.campanhas"
                :key="`${grupo.produto}-${item.campanha_id}`"
              >
                <td class="text-center">
                  {{ item.identificador }}
                </td>

                <td class="text-center">
                  {{ item.subproduto || 'N/A' }}
                </td>

                <td class="text-center">
                  {{ formatarData(item.data_inicial) }}
                </td>

                <td class="text-center">
                  {{ formatarData(item.data_final) }}
                </td>

                <td class="text-center">
                  {{ item.status || 'N/A' }}
                </td>

                <td class="text-center">
                  {{ formatarSei(item.sei_dnit) }}
                </td>

                <td class="text-center">
                  <a
                    :href="getVisualizarRoute(item)"
                    class="btn btn-outline-primary btn-sm"
                    target="_blank"
                    rel="noopener noreferrer"
                  >
                    Visualizar
                  </a>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

    </div>
  </div>
</template>

<style scoped>
.container-cards {
  background-color: #fff;
  padding: 20px;
}

.card-info {
  background-color: #fdfdfd;
  border: 1px solid #5a595e;
  border-radius: 10px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, .10);
  overflow: hidden;
}

.card-title {
  font-size: 17px;
  text-align: center;
  font-weight: bold;
  padding: 15px;
  margin: 0;
  background-color: #dde1e4;
  color: #0a0101;
}

/* ---------- TOPO ---------- */

.top-bar {
  display: flex;
  justify-content: space-between;
  align-items: flex-end;
  gap: 20px;
  padding: 18px 20px 10px;
  flex-wrap: wrap;
}

.filtro-container {
  width: 300px;
}

.filtro-label {
  display: block;
  font-size: 13px;
  font-weight: 700;
  margin-bottom: 6px;
}

.filtro-select {
  border-color: #c8ccd0;
}

.contador-container {
  display: flex;
  align-items: center;
  gap: 12px;
  flex-wrap: wrap;
}

.contador-badge {
  display: flex;
  align-items: center;
  gap: 10px;
  background: #f5f7fa;
  border: 1px solid #d9dee3;
  border-radius: 6px;
  padding: 6px 12px;
}

.contador-nome {
  font-size: 14px;
  font-weight: 600;
  color: #333;
}

.contador-valor {
  background: #0d6efd;
  color: #fff;
  border-radius: 20px;
  min-width: 24px;
  text-align: center;
  padding: 2px 8px;
  font-size: 13px;
  font-weight: bold;
}

/* ---------- CONTEÚDO ---------- */

.empty-state {
  padding: 20px;
  margin: 0;
  font-size: 14px;
  color: #666;
}

.lista-campanhas {
  padding: 0 20px 20px;
}

.grupo-campanhas {
  margin-top: 20px;
}

.grupo-titulo {
  background: #f4f6f8;
  border-left: 4px solid #0d6efd;
  padding: 10px 14px;
  margin-bottom: 10px;
  font-size: 15px;
  font-weight: 700;
  color: #333;
}

/* ---------- TABELAS ---------- */

.table {
  margin-bottom: 0;
}

.table th {
  background: #fafafa;
  font-size: 12px;
  font-weight: 700;
  vertical-align: middle;
}

.table td {
  vertical-align: middle;
  font-size: 13px;
}

.btn-outline-primary {
  padding: 2px 10px;
}

.tabela-campanhas{
    width:100%;
    table-layout:fixed;
}

.tabela-campanhas th,
.tabela-campanhas td{
    vertical-align:middle;
    word-wrap:break-word;
    overflow-wrap:break-word;
}

/* ---------- RESPONSIVO ---------- */

@media (max-width: 768px) {
  .top-bar {
    flex-direction: column;
    align-items: stretch;
  }

  .filtro-container {
    width: 100%;
  }

  .contador-container {
    justify-content: flex-start;
  }
}
</style>