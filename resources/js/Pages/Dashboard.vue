<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import BotoesMapa from './BotoesMapa.vue';
import ModalFiltro from './ModalFiltro.vue';
import CoordanadasCentroUFs from "@/Utils/CoordanadasCentroUFs.js";
import { onBeforeUnmount, onMounted, ref } from 'vue';

const props = defineProps({
    ufs: { type: Array },
    rodovias: { type: Array }
});

let map = null;
let selectedLayer = null;
let temporarySections = null;

const layersGroups = {};
const filterOffcanvas = ref();
const mapContainer = ref(null);
const zoomLevel = ref(5);

onBeforeUnmount(() => {
    map?.remove();
})

onMounted(() => renderMap());

const renderMap = () => {
    map = L.map(mapContainer.value, {
        zoomControl: false,
        renderer: L.canvas({ tolerance: 8 }),
        fadeAnimation: true,
        zoomSnap: 0.25
    }).setView([-15.9325, -49.8362], zoomLevel.value);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    L.control.scale({ position: 'topleft' }).addTo(map);

    selectedLayer = L.featureGroup().addTo(map)
    temporarySections = L.featureGroup().addTo(map)
}

const clearMap = () => {
    zoomLevel.value = 5;

    map.setView([-15.9325, -49.8362], zoomLevel.value);

    selectedLayer.clearLayers();
    temporarySections.clearLayers();
}

const ufZoom = (ufs) => {
    if (!ufs.length) {
        map.setView([-15.9325, -49.8362], 5)
        return;
    }

    const featureGroup = L.featureGroup();

    ufs.forEach(uf => {
        featureGroup.addLayer(L.marker(CoordanadasCentroUFs[uf]))
    })

    map.fitBounds(featureGroup.getBounds(), { maxZoom: 8 })
}

const setSelectedLayer = (geoJson, style) => {
    selectedLayer.clearLayers().addLayer(L.geoJSON(geoJson, { style }));
}

const removeWms = (layerGroupName) => {

    selectedLayer.clearLayers();
    map.closePopup();

    if (layersGroups[layerGroupName]) {
        layersGroups[layerGroupName].remove()
        delete layersGroups[layerGroupName]
    }
}

const renderWms = (layerGroupName, filter, color = '#0000FF', wmsLayers = []) => {
    map.closePopup();

    removeWms(layerGroupName);

    const { url, workspace } = usePage().props.geoserver
    const { x: width, y: height } = map.getSize();

    layersGroups[layerGroupName] = L.featureGroup();

    const options = {
        version: '1.1.1',
        format: "image/png8",
        transparent: true,
        CQL_FILTER: filter,
        env: `color:${color};width:3`,
        srs: 'EPSG:4674',
        width,
        height
    }

    wmsLayers.forEach(layer => {
        L.tileLayer.wms(
            `${url}/${workspace}`,
            { ...options, layers: `${workspace}:${layer}` }
        ).addTo(layersGroups[layerGroupName]);
    })

    layersGroups[layerGroupName].addTo(map);
}

</script>

<template>

    <Head title="Dashboard" />

    <AuthenticatedLayout :mapa-principal="true">

        <div class="map" ref="mapContainer"></div>

        <BotoesMapa />

        <ModalFiltro ref="filterOffcanvas" :ufs="ufs" :rodovias="rodovias" @ufChanged="ufZoom" @filtersReset="clearMap"
            @layerSelected="renderWms" @layerUnselected="removeWms" />

    </AuthenticatedLayout>
</template>
<style>
.map {
    height: calc(100svh - 9.4em);
    width: 100%;
}
</style>