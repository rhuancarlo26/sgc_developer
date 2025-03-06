<script setup>
const props = defineProps({
    servico: {type: Object}
});
</script>
<template>
    <div>
        <div class="accordion" id="accordion-example">
            <div class="accordion-item">
                <h2 class="accordion-header" id="heading-2">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#introducao" aria-expanded="false">
                        1. Introdução
                    </button>
                </h2>
                <div id="introducao" class="accordion-collapse collapse" data-bs-parent="#accordion-example" style="">
                    <div class="accordion-body pt-0">
                        {{ servico.introducao }}
                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header" id="heading-2">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#justificativa" aria-expanded="false">
                        2. justificativa
                    </button>
                </h2>
                <div id="justificativa" class="accordion-collapse collapse" data-bs-parent="#accordion-example"
                     style="">
                    <div class="accordion-body pt-0">
                        {{ servico.justificativa }}
                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header" id="heading-2">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#objetivo"
                            aria-expanded="false">
                        3. Objetivos
                    </button>
                </h2>
                <div id="objetivo" class="accordion-collapse collapse" data-bs-parent="#accordion-example" style="">
                    <div class="accordion-body pt-0">
                        {{ servico.objetivo }}
                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header" id="heading-2">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#metodologia" aria-expanded="false">
                        4. Metodologias
                    </button>
                </h2>
                <div id="metodologia" class="accordion-collapse collapse" data-bs-parent="#accordion-example" style="">
                    <div class="accordion-body pt-0">
                        {{ servico.metodologia }}
                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header" id="heading-2">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#publico_alvo" aria-expanded="false">
                        5. Público alvo
                    </button>
                </h2>
                <div id="publico_alvo" class="accordion-collapse collapse" data-bs-parent="#accordion-example" style="">
                    <div class="accordion-body pt-0">
                        {{ servico.publico_alvo }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
