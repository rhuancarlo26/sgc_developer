<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import BotoesMapa from './BotoesMapa.vue';
import ModalFiltro from './ModalFiltro.vue';
import CoordanadasCentroUFs from "@/Utils/CoordanadasCentroUFs.js";
import { onBeforeUnmount, onMounted, ref } from 'vue';
import Popups from './Dashboard/MapPopup/Popups';
import Detalhe from './Detalhe.vue';

const props = defineProps({
    ufs: { type: Array },
    rodovias: { type: Array },
    contratos: { type: Array },
});

let map = null;
let selectedLayer = null;
let temporarySections = null;

const layersGroups = {};
const filterOffcanvas = ref();
const detalhesOffcanvas = ref();
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

    map.on('click', handleMapClick);
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

const handleOwnWmsModal = (layer, wmsParams, groupName, latLng) => {
    axios.get(layer._url, { params: wmsParams })
        .then(async ({ data }) => {
            const props = data.features ? data.features[0]?.properties : null;

            if (!props) return;

            // Caso mais parâmetros sejam passados para a prop env, esse código deverá ser ajustado
            const [color, weight] = wmsParams.env.split(';').map(i => i.split(':')[1])

            setSelectedLayer(data, { color, weight: Number(weight) * 3, opacity: 0.5 });

            Object.assign(props, { latLng })

            L.popup()
                .setLatLng(latLng)
                .setContent(await Popups.getContent(groupName, props, detalhesOffcanvas))
                .on('remove', () => selectedLayer.clearLayers())
                .openOn(map)
                .unbindPopup()

        })
        .catch(console.log)
}

const handleMapClick = (clickEvent) => {
    map.closePopup();

    const { x, y } = map.latLngToContainerPoint(clickEvent.latlng);
    const { x: width, y: height } = map.getSize();

    for (const groupName in layersGroups) {

        layersGroups[groupName].getLayers().forEach(layer => {

            const params = {
                ...layer.options,
                service: 'WMS',
                request: 'GetFeatureInfo',
                query_layers: layer.wmsParams.layers,
                x: Math.round(x),
                y: Math.round(y),
                info_format: 'application/json',
                exceptions: 'application/json',
                buffer: 5, // Tolerância do click em pixels (Nem sempre funciona),
                bbox: map.getBounds().toBBoxString(),
                width, height
            }

            handleOwnWmsModal(layer, params, groupName, clickEvent.latlng);

        })

    }

}

</script>

<template>

    <Head title="Dashboard" />

    <AuthenticatedLayout :mapa-principal="true">
        <div class="map" ref="mapContainer"></div>

        <BotoesMapa />

        <ModalFiltro ref="filterOffcanvas" :ufs="ufs" :rodovias="rodovias" :contratos="contratos" @ufChanged="ufZoom"
            @filtersReset="clearMap" @layerSelected="renderWms" @layerUnselected="removeWms" />

        <Detalhe ref="detalhesOffcanvas" />

    </AuthenticatedLayout>
</template>
<style>
.map {
    height: calc(100svh - 9.4em);
    width: 100%;
}
</style>