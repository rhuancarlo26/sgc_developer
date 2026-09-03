<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import { Head, router, usePage } from "@inertiajs/vue3";
import NavbarContrato from "../../NavbarContrato.vue";
import { computed } from "vue";

const props = defineProps({
  contratos: { type: Object, required: true },
  title: { type: String, required: true },
  activeTab: { type: String, required: true },
  pmqa: { type: Object },
  produto: { type: [String, Object], default: 'eia' }
});

const emit = defineEmits(["update:activeTab"]);

const page = usePage()

const pmqaId = computed(() => {
  return props.pmqa?.id ?? page.props.ziggy.query?.pmqa ?? route().params?.pmqa
})

const produtoParam = computed(() =>
  typeof props.produto === "string" ? props.produto.toLowerCase() : (props.produto?.slug ?? "eia")
)

const podeGerenciarPmqa = computed(() =>
  ['Em elaboração', 'Rejeitada'].includes(props.pmqa?.status_aprovacao)
)

const setTab = (tab) => {
  if (tab === "apresentacao") {
    router.visit(
      route("sgc.contratada.produtos.create", [props.contratos.id, produtoParam.value]),
      { data: { id: pmqaId.value, tab: "apresentacao", subStep: 1 }, preserveScroll: true }
    );
    return;
  }

  // Navigation allowed in all statuses

  const baseParams = [props.contratos.id, produtoParam.value, pmqaId.value];

  if (tab === "configuracao") {
    router.visit(route("contratos.contratada.sgc.pmqa.configuracao.ponto.index", baseParams));
    return;
  }

  if (tab === "execucao") {
    router.visit(route("contratos.contratada.sgc.pmqa.execucao.index", baseParams));
    return;
  }

  if (tab === "resultados") {
    router.visit(route("contratos.contratada.sgc.pmqa.resultado.index", baseParams));
    return;
  }

  if (tab === "relatorio") {
    router.visit(route("contratos.contratada.relatorio.pmqa.relatorio.index", baseParams))
  }
};
</script>

<template>
  <Head :title="`${contratos.contratada.slice(0, 10)}...`" />
  <AuthenticatedLayout>
    <template #header>
      <div class="w-100 d-flex justify-content-between">
        <Breadcrumb
          class="align-self-center"
          :links="[
            {
              route: route('contratos.gestao.listagem', contratos.tipo_contrato),
              label: `Gestão de Contratos`,
            },
            { route: '#', label: contratos.contratada },
          ]"
        />
      </div>
    </template>

    <NavbarContrato :tipo="contratos">
      <template #body>
        <div class="card">
          <div class="card-body">
            <h2 class="text-center mb-4">{{ title }}</h2>

            <ul class="nav nav-tabs mb-4">
              <!-- Apresentação: Sempre livre, ou segue própria regra -->
              <li class="nav-item">
                <a class="nav-link" :class="{ active: activeTab === 'apresentacao' }" @click.prevent="setTab('apresentacao')">
                  Apresentação
                </a>
              </li>

              <!-- Configuração: Liberada se Apresentação for Aprovada -->
              <li class="nav-item">
                <a class="nav-link"
                   :class="{ active: activeTab === 'configuracao', disabled: pmqa?.status_apresentacao !== 'Aprovada' }"
                   @click.prevent="pmqa?.status_apresentacao === 'Aprovada' && setTab('configuracao')">
                  Configuração
                </a>
              </li>

              <!-- Execução: Liberada se Configuração for Aprovada -->
              <li class="nav-item">
                <a class="nav-link"
                   :class="{ active: activeTab === 'execucao', disabled: pmqa?.status_configuracao !== 'Aprovada' }"
                   @click.prevent="pmqa?.status_configuracao === 'Aprovada' && setTab('execucao')">
                  Execução
                </a>
              </li>

              <!-- Resultados: Liberada se Execução for Aprovada -->
              <li class="nav-item">
                <a class="nav-link"
                   :class="{ active: activeTab === 'resultados', disabled: pmqa?.status_execucao !== 'Aprovada' }"
                   @click.prevent="pmqa?.status_execucao === 'Aprovada' && setTab('resultados')">
                  Resultados
                </a>
              </li>

              <!-- Relatório: Liberada se Resultados for Aprovada -->
              <li class="nav-item">
                <a class="nav-link"
                   :class="{ active: activeTab === 'relatorio', disabled: pmqa?.status_resultado !== 'Aprovada' }"
                   @click.prevent="pmqa?.status_resultado === 'Aprovada' && setTab('relatorio')">
                  Relatório
                </a>
              </li>
            </ul>

            <div class="tab-content">
              <div v-show="activeTab === 'apresentacao'">
                <slot name="apresentacao" />
              </div>

              <div v-show="activeTab === 'relatorio'">
                <slot name="relatorio" />
              </div>

              <div v-show="activeTab === 'configuracao'">
                <slot name="configuracao" />
              </div>

              <div v-show="activeTab === 'execucao'">
                <slot name="execucao" />
              </div>

              <div v-show="activeTab === 'resultados'">
                <slot name="resultados" />
              </div>
            </div>
          </div>
        </div>
      </template>
    </NavbarContrato>
  </AuthenticatedLayout>
</template>
