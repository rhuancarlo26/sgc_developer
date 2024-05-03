<script setup>
import {Link} from '@inertiajs/vue3';

defineProps({
    links: {type: Array, default: []}
})

</script>

<template>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb breadcrumb-arrows">
            <li v-for="(link, i) in links" class="breadcrumb-item"
                :class="{'active':  i === links.length - 1}">
                <Link :href="link.route ?? '#'" :class="{disabled: link.route ?? '#' === '#'}">
                    {{ link.label }}
                </Link>
            </li>
        </ol>
    </nav>
</template>
