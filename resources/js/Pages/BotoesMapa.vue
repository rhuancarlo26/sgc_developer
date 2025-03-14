<script setup>
import { onMounted, ref, watch } from 'vue';
import {
  IconPlus,
  IconMinus,
  IconFilter,
  IconArrowsDiagonal,
  IconRecycle,
  IconMap,
  IconSearch,
  IconRulerMeasure,
  IconRoad,
  IconChevronRight
} from '@tabler/icons-vue';
import 'leaflet/dist/leaflet.css';


onMounted(() => {
  // Inicialize os tooltips do Bootstrap
  const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))

  tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl)
  })
});

const zoomExpanded = ref(false);

const props = defineProps({ zoomLevel: Number, default: 5 })

const btnMeasure = ref(false);

const emit = defineEmits(['filter', 'openBaseMaps', 'clearMap', 'expandMap', 'zoomLevelChanged']);

const openBaseMaps = () => emit('openBaseMaps')

const clearMap = () => emit('clearMap')

const expandMap = () => emit('expandMap')

const setZoomLevel = (sliderVal) => emit('zoomLevelChanged', sliderVal)

const search = () => {
  const searchhMap = document.querySelector('.leaflet-geosearch-bar form');

  if (searchhMap) {
    searchhMap.style.display === 'block' ? searchhMap.style.display = 'none' : searchhMap.style.display = 'block';
  }
}

const toggleZoom = () => {
  zoomExpanded.value = !zoomExpanded.value;
};

const measure = () => {

  const measureMapContainer = document.querySelector('.leaflet-control-measure');
  const measureMap = document.querySelector('.leaflet-control-measure-interaction');

  measureMapContainer.style.display = 'block';
  measureMap.style.display = 'block';

  const startButton = document.querySelector('.js-start.start');
  startButton.click();
}

</script>

<template>
  <div class="btn-mapa">
    <button data-bs-target="#filterOffCanvas" class="btn-list" data-bs-toggle="offcanvas" title="Filtrar Dados"
      data-bs-title="Filtrar Dados">
      <IconFilter />
    </button>
  </div>
</template>
<style scoped>
.btn-mapa {
  height: calc(100svh - 150px);
  position: absolute;
  top: 7.5rem;
  right: 0.5rem;
  display: flex;
  flex-direction: column;
  justify-content: start;
  align-items: end;
  gap: 0.5rem;
  z-index: 500;
}

.btn-list:hover {
  background: linear-gradient(59deg, #104394 0%, #000000 100%) !important;
  border: 1px solid rgb(255, 255, 255);
  color: #FFFFFF;
  transform: translateX(-0.5rem);
}

.btn-list {
  z-index: 1001;
  display: flex;
  justify-content: center;
  align-items: center;
  height: 2.5rem;
  width: 2.5rem;
  padding: 0;
  margin: 0;
  text-align: center;
  border-radius: 5px;
  background-color: #fff;
  border: 1px solid rgb(177, 175, 175);
  color: #000000;
  transition: all 0.4s;
}
</style>
