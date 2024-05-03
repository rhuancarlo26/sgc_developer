<template>
    <footer class="footer footer-transparent d-print-none border-top">

        <div class="container-xl">
            <strong>{{ Intl.DateTimeFormat('pt-br', {dateStyle: 'long'}).format(new Date()) }}</strong>
        </div>

    </footer>
</template>
<script setup>
</script>
