<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import { Head, router } from "@inertiajs/vue3";
import NavbarContrato from "../NavbarContrato.vue";

const props = defineProps({
  contratos: { type: Object, required: true },
  title: { type: String, required: true },
  activeTab: { type: String, required: true },
});

const emit = defineEmits(["update:activeTab"]);

const setTab = (tab) => {
  if (props.activeTab === "execucao" && tab !== "execucao") {
    router.visit(
      route("contratos.contratada.sgc.pmqa.configuracao.ponto.index", [
        props.contratos.id,
        'eia',
        9
      ], {
        tab,
        subStep: tab === "configuracao" ? 2 : 1,
      })
    );
    return;
  }

  emit("update:activeTab", tab);
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
                <a class="nav-link" :class="{ active: activeTab === 'anexos' }" @click.prevent="setTab('anexos')">
                  Anexos
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

              <div v-show="activeTab === 'anexos'">
                <slot name="anexos" />
              </div>
            </div>
          </div>
        </div>
      </template>
    </NavbarContrato>
  </AuthenticatedLayout>
</template>
