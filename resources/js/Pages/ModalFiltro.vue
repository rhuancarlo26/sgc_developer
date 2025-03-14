<script setup>
import { IconFilterOff } from '@tabler/icons-vue';
import { computed, watch } from 'vue';
import { ref } from 'vue';

const props = defineProps({
  ufs: { type: Array },
  rodovias: { type: Array },
  contratos: { type: Array },
});

const contrato = ref([
  { id: 1, layer: 'ecosistema_contrato_view', color: "#17a2b8", nome: "Gestão Ambiental" },
  { id: 2, layer: 'ecosistema_contrato_view', color: "#e7515a", nome: "Estudo Ambiental" },
  { id: 3, layer: 'ecosistema_contrato_view', color: "#e2a03f", nome: "Regularização Ambiental" },
])

const contrato_selecionadas = ref([]);

const filters = ref({
  uf: [],
  rodovia: [],
  nr_contrato: []
});

const rodovias_ = computed(() => {
  if (filters.value.uf?.length) {

    const uf_ids = props.ufs.filter(u => filters.value.uf.includes(u.uf)).map(u => u.id);

    return props.rodovias.filter(r => uf_ids.includes(r.estados_id))
  }

  return []
})

const getParsedCQLFilter = (layerTypeFilterParam, filtersToIgnore = [], likeComparison = false) => {

  let filterString = ''

  const comparator = likeComparison ? 'like' : '=';

  for (const [key, value] of Object.entries(filters.value)) {

    if (filtersToIgnore.length && filtersToIgnore.includes(key)) continue;

    if (!value || (Array.isArray(value) && !value.length)) continue;

    if (Array.isArray(value)) {

      if (likeComparison) {

        let arrayLikeComparison = ''

        value.forEach((v, i) => {
          arrayLikeComparison += `${key} LIKE '%${v}%' ${i === value.length - 1 ? '' : 'OR '}`
        });

        filterString += `(${arrayLikeComparison}) AND `

        continue
      }

      filterString += `${key} IN (${value.map(v => `'${v}'`).join(',')}) AND `
      continue;
    }

    if (key === 'ativo') {
      filterString += `${key} = '${!value}' AND ` // Coluna no banco se chama 'ativo' por isso a negação em value
      continue;
    }

    if (key === 'novo_pac') {
      filterString += `id_novo_pac IS NOT NULL AND `
      continue;
    }

    if (key === 'delegado') {
      filterString += `qtd_delegacoes > 0 AND `
      continue;
    }

    filterString += `${key} ${comparator} '${value}' AND `

  }

  return filterString + layerTypeFilterParam
}

const toggleContrato = (layer, check = true) => {

  if (check) {
    emit(
      'layerSelected',
      layer.layer,
      getParsedCQLFilter(`tipo_contrato = ${layer.id}`, ['fase_id', 'nr_contrato', 'novo_pac', 'ativo', 'empreendimento_id', 'delegado'], true),
      layer.color,
      [layer.layer]
    );
  } else {
    emit('layerUnselected', layer.layer)
  }
}

const resetFilters = () => {

  filters.value = {
    uf: [],
    rodovia: [],
    nr_contrato: [],
  };

  contrato_selecionadas.value = [];

  emit('filtersReset');
}

const updateFilters = (current, old) => {
  Promise.all([
    new Promise(() => contrato_selecionadas.value.forEach((i) => toggleContrato(i))),
  ])
}

watch(() => filters, updateFilters, { deep: true })

const emit = defineEmits(['layerSelected', 'layerUnselected', 'filtersReset', 'ufChanged']);

</script>
<template>
  <div class="offcanvas offcanvas-start" id="filterOffCanvas" aria-labelledby="offcanvasStartLabel" aria-modal="true"
    role="dialog">
    <div class="offcanvas-header p-3">
      <h2 class="offcanvas-title" id="filterOffCanvasLabel">Filtro de Camadas</h2>
      <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body p-3">

      <div class="d-flex flex-column gap-3">

        <div class="form-group">
          <v-select v-model="filters.uf" class="w-100" :options="ufs" label="uf" :reduce="uf => uf.uf" :multiple="true"
            @update:model-value="(ufs_) => emit('ufChanged', ufs_)" placeholder="Selecione uma ou mais UFs">
            <template #no-options="{ }">
              {{ `Nenhuma opção encontrada.` }}
            </template>
          </v-select>
        </div>
        <div class="form-group">
          <v-select v-model="filters.rodovia" class="w-100" :options="rodovias_" label="rodovia" :multiple="true"
            :reduce="rodovia => rodovia.rodovia" placeholder="Selecione uma ou mais Rodovias">
            <template #no-options="{ }">
              {{ `Nenhuma opção encontrada.` }}
            </template>
          </v-select>
        </div>
        <div class="form-group">
          <v-select v-model="filters.nr_contrato" class="w-100" :options="[]" label="nr_contrato" :multiple="true"
            :reduce="contratacao => contratacao.nr_contrato" placeholder="Selecione um ou mais Contratos">
            <template #no-options="{ }">
              {{ `Nenhuma opção encontrada.` }}
            </template>
          </v-select>
        </div>

        <div class="hr-text my-0">Camadas</div>

        <div class="accordion" id="layersAccordion">
          <div class="accordion-item">
            <h2 class="accordion-header" id="headingLayersContrato">
              <button class="accordion-button collapsed px-3 py-2" type="button" data-bs-toggle="collapse"
                data-bs-target="#collapseLayersContrato" aria-expanded="false" aria-controls="collapseLayersContrato">
                Contrato
              </button>
            </h2>
            <div id="collapseLayersContrato" class="accordion-collapse collapse" aria-labelledby="headingLayersContrato"
              data-bs-parent="#layersAccordion">
              <div class="accordion-body border-top p-2">
                <div v-for="entry in contrato" :key="entry.id"
                  class="d-flex gap-2 justify-content-between align-items-center my-2 border p-1 rounded">
                  <div class="form-check my-auto">
                    <input class="form-check-input check-layers" v-model="contrato_selecionadas" :value="entry"
                      @change="(e) => toggleContrato(entry, e.target.checked)" type="checkbox">
                    <label class="form-check-label" for="contratoCheck">
                      {{ entry.nome }}
                    </label>
                  </div>
                  <input type="color" style="max-width: 3em"
                    class="form-control form-control-sm form-control-color p-1 my-auto" v-model="entry.color"
                    title="Escolha uma cor para a camada">
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>

    <div class="offcanvas-footer p-2 text-end border-top">
      <button class="btn btn-secondary px-2 py-1" type="button" @click="() => resetFilters()">
        <IconFilterOff class="me-2" />
        Limpar Filtros
      </button>
    </div>
  </div>
</template>
<style>
.offcanvas {
  min-width: 23vw;
}
</style>