<script setup>
import {IconSun, IconMoon} from '@tabler/icons-vue';

const theme = localStorage.getItem('theme');

toggleTheme(theme)

window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', ({matches}) => {
    toggleTheme(matches ? 'dark' : 'light')
});

function toggleTheme(theme = 'light') {
    localStorage.setItem('theme', theme);
    document.body.setAttribute('data-bs-theme', theme);
}

</script>


<template>
    <a @click="toggleTheme('dark')" href="#" class="nav-link px-0 hide-theme-dark" data-bs-toggle="tooltip"
       data-bs-placement="bottom" aria-label="Habilitar modo escuro"
       data-bs-original-title="Habilitar modo escuro">
        <IconMoon stroke-width="1"/>
    </a>

    <a @click="toggleTheme('light')" href="#" class="nav-link px-0 hide-theme-light" data-bs-toggle="tooltip"
       data-bs-placement="bottom" aria-label="Habilitar modo claro"
       data-bs-original-title="Habilitar modo claro">
        <IconSun stroke-width="1"/>
    </a>
</template>
