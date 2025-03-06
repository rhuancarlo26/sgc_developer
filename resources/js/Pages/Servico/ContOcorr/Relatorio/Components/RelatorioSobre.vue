<script setup>
const props = defineProps({
    relatorio: {type: Object}
});
</script>
<template>
    <div>
        <div class="accordion" id="accordion-example">
            <div class="accordion-item">
                <h2 class="accordion-header" id="heading-2">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#sobre" aria-expanded="false">
                        7. Sobre o relatório
                    </button>
                </h2>
                <div id="sobre" class="accordion-collapse collapse" data-bs-parent="#accordion-example" style="">
                    <div class="accordion-body pt-0">
                        {{ relatorio.sobre_relatorio }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
