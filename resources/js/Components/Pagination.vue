<script setup>
import axios from "axios";
import { Link } from '@inertiajs/vue3';
import AxiosErrorHandler from "@/Utils/AxiosErrorHandler.js";
import { IconTableShare } from "@tabler/icons-vue";

defineProps({
    records: { type: Object, default: { data: [], links: [] } },
    inertiaPagination: { type: Boolean, default: true },
    axiosPagination: { type: Boolean, default: false },
    only: { type: Array, default: [] },
    excelRoute: String
})

const emit = defineEmits(['pageChange']);

const paramsURL = window.location.search;

const getUrlWithSearchParams = (url) => {

    if (!url) {
        return '#';
    }

    const urlObj = new URL(url);

    Object.keys(route().params).forEach(key => {

        if (!urlObj.searchParams.has(key)) {
            urlObj.searchParams.append(key, route().params[key]);
        }

    });

    return urlObj.toString();
}

const
    fetchPageData = (url) => {

        if (!url) {
            return;
        }

        axios.get(getUrlWithSearchParams(url)).then(r => {
            emit('pageChange', r.data);
        }).catch(AxiosErrorHandler);
    }

</script>


<template>
    <div v-if="records.links.length" class="d-flex align-items-center justify-content-between">
        <p class="m-0 text-secondary">
            Mostrando {{ records.from }} até {{ records.to }} de {{ records.total }} registros
        </p>

        <div class="d-flex">
            <!-- Excel Export -->
            <div>
                <a v-if="excelRoute" class="btn btn-primary me-2" :href="`${excelRoute}${paramsURL}`">
                    <IconTableShare class="me-2" /> Excel
                </a>
            </div>

            <ul class="pagination m-0 ms-auto">
                <li v-for="link in records.links" class="page-item mx-1" :class="{ active: link.active }">
                    <button v-if="axiosPagination" class="page-link" :class="{ disabled: !link.url }"
                        @click="fetchPageData(link.url)" v-html="link.label" />
                    <Link v-else class="page-link" :class="{ disabled: !link.url }" :href="getUrlWithSearchParams(link.url)"
                        :only="only" v-html="link.label" />
                </li>
            </ul>
        </div>
    </div>
</template>
