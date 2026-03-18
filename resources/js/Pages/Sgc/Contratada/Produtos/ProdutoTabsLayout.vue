<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import { Head, router, usePage } from "@inertiajs/vue3";
import NavbarContrato from "../NavbarContrato.vue";
import { computed } from "vue";

const props = defineProps({
  contratos: { type: Object, required: true },
  title: { type: String, required: true },
  activeTab: { type: String, required: true },
});

const emit = defineEmits(["update:activeTab"]);

const page = usePage()

const pmqaId = computed(() => {
  return props.pmqa?.id ?? page.props.ziggy.query?.pmqa ?? route().params?.pmqa
})

const setTab = (tab) => {
  const baseParams = [props.contratos.id, 'eia', pmqaId.value];

  if (tab === "configuracao") {
    router.visit(
      route("contratos.contratada.sgc.pmqa.configuracao.ponto.index", baseParams),
      { data: { tab: "configuracao", subStep: 2 }, preserveScroll: true }
    );
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
              <li class="nav-item">
                <a class="nav-link" :class="{ active: activeTab === 'apresentacao' }" @click.prevent="setTab('apresentacao')">
                  Apresentação
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" :class="{ active: activeTab === 'configuracao' }" @click.prevent="setTab('configuracao')">
                  Configuração
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" :class="{ active: activeTab === 'execucao' }" @click.prevent="setTab('execucao')">
                  Execução
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" :class="{ active: activeTab === 'resultados' }" @click.prevent="setTab('resultados')">
                  Resultados
                </a>
              </li>
            </ul>

            <div class="tab-content">
              <div v-show="activeTab === 'apresentacao'">
                <slot name="apresentacao" />
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
