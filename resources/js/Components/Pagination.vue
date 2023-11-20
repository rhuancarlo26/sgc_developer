<script setup>
import axios from "axios";
import {Link} from '@inertiajs/vue3';

defineProps({
    records: {type: Object, default: {data: [], links: []}},
    inertiaPagination: {type: Boolean, default: true},
    axiosPagination: {type: Boolean, default: false},
    only: {type: Array, default: []}
})

const emit = defineEmits(['pageChange']);

function fetchPageData(link) {

    if (!link) {
        return;
    }

    axios.get(link).then(r => {
        emit('pageChange', r.data);
    });
}

</script>


<template>
    <div v-if="records.links.length" class="d-flex align-items-center justify-content-between">

        <p class="m-0 text-secondary">
            Mostrando {{ records.from }} até {{ records.to }} de {{ records.total }} registros
        </p>

        <ul class="pagination m-0 ms-auto">
            <li v-for="link in records.links" class="page-item mx-1" :class="{active: link.active}">
                <button v-if="axiosPagination"
                        class="page-link"
                        :class="{disabled: !link.url}"
                        @click="fetchPageData(link.url)"
                        v-html="link.label"/>
                <Link v-else
                      class="page-link" :class="{disabled: !link.url}"
                      :href="link.url ?? '#'"
                      :only="only"
                      v-html="link.label"/>
            </li>
        </ul>
    </div>
</template>
