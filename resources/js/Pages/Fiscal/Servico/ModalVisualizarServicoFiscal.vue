<script setup>
import Modal from "@/Components/Modal.vue";
import { ref } from "vue";

const modalServico = ref(null);
const servico = ref(null);

const abrirModal = (item) => {
    servico.value = item;
    console.log(servico.value);
    modalServico.value.getBsModal().show();
}

defineExpose({ abrirModal });
</script>

<template>
    <Modal ref="modalServico" title="Visualização Serviço Cadastrado" modal-dialog-class="modal-xl">
        <template #body>
            <div class="accordion" id="accordion-example">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="heading-1">
                        <button class="accordion-button " type="button" data-bs-toggle="collapse"
                            data-bs-target="#introducao" aria-expanded="true">
                            Introdução
                        </button>
                    </h2>
                    <div id="introducao" class="accordion-collapse collapse show" data-bs-parent="#accordion-example">
                        <div class="accordion-body pt-0">
                            {{ servico?.introducao }}
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="heading-2">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#justificativa" aria-expanded="false">
                            Justificativa
                        </button>
                    </h2>
                    <div id="justificativa" class="accordion-collapse collapse" data-bs-parent="#accordion-example">
                        <div class="accordion-body pt-0">
                            {{ servico?.justificativa }}
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="heading-3">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#objetivos" aria-expanded="false">
                            Objetivos
                        </button>
                    </h2>
                    <div id="objetivos" class="accordion-collapse collapse" data-bs-parent="#accordion-example">
                        <div class="accordion-body pt-0">
                            {{ servico?.objetivo }}
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="heading-4">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#metodologias" aria-expanded="false">
                            Metodologias
                        </button>
                    </h2>
                    <div id="metodologias" class="accordion-collapse collapse" data-bs-parent="#accordion-example">
                        <div class="accordion-body pt-0">
                            {{ servico?.metodologia }}
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="heading-5">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#publicoAlvo" aria-expanded="false">
                            Público alvo
                        </button>
                    </h2>
                    <div id="publicoAlvo" class="accordion-collapse collapse" data-bs-parent="#accordion-example">
                        <div class="accordion-body pt-0">
                            {{ servico?.publico_alvo }}
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="heading-6">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#recursosHumanos" aria-expanded="false">
                            Recursos Humanos
                        </button>
                    </h2>
                    <div id="recursosHumanos" class="accordion-collapse collapse" data-bs-parent="#accordion-example">
                        <div class="accordion-body pt-0">
                            <div class="card-body">
                                <div class="table-responsive mb-4">
                                    <table class="table card-table table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th>Nome do profissional</th>
                                                <th>Profissão</th>
                                                <th>Ação</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="rh in servico?.rhs" :key="rh.id" class="cursor-pointer">
                                                <td>{{ rh.nome }}</td>
                                                <td>{{ rh.funcao }}</td>
                                                <td>Ação</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="heading-7">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#veiculos" aria-expanded="false">
                            Veículos
                        </button>
                    </h2>
                    <div id="veiculos" class="accordion-collapse collapse" data-bs-parent="#accordion-example">
                        <div class="accordion-body pt-0">
                            <div class="card-body">
                                <div class="table-responsive mb-4">
                                    <table class="table card-table table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th>Código Veiculo</th>
                                                <th>Descrição</th>
                                                <th>Observação</th>
                                                <th>Foto(s) do veículo</th>
                                                <th>Documentos do veículo</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="veiculo in servico?.veiculos" :key="veiculo.id"
                                                class="cursor-pointer">
                                                <td>{{ veiculo.codigo?.codigo }}</td>
                                                <td>{{ veiculo.codigo?.descricao }}</td>
                                                <td>{{ veiculo.observacao }}</td>
                                                <td>Foto(s)</td>
                                                <td>Documentos</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="heading-8">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#equipamentos" aria-expanded="false">
                            Equipamentos
                        </button>
                    </h2>
                    <div id="equipamentos" class="accordion-collapse collapse" data-bs-parent="#accordion-example">
                        <div class="accordion-body pt-0">
                            <div class="card-body">
                                <div class="table-responsive mb-4">
                                    <table class="table card-table table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th>Nome do equipamento</th>
                                                <th>Modelo</th>
                                                <th>Tipo Equipamento</th>
                                                <th>Descrição / Modelo</th>
                                                <th>Especificação Técnica</th>
                                                <th>Nº Patrimônio</th>
                                                <th>Foto(s) do Equipamento</th>
                                                <th>Número Interno</th>
                                                <th>Número de Série</th>
                                                <th>Observações</th>
                                                <th>Código SICRO</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="equipamento in servico?.equipamentos" :key="equipamento.id"
                                                class="cursor-pointer">
                                                <td>{{ equipamento.nome }}</td>
                                                <td>Modelo</td>
                                                <td>Tipo</td>
                                                <td>{{ equipamento.descricao }}</td>
                                                <td>{{ equipamento.atividade }}</td>
                                                <td>Nº Patrimônio</td>
                                                <td>Foto(s)</td>
                                                <td>Número Interno</td>
                                                <td>{{ equipamento.numero_serie }}</td>
                                                <td>{{ equipamento.observacao }}</td>
                                                <td>Código SICRO</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="heading-9">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#licencas" aria-expanded="false">
                            Licenças
                        </button>
                    </h2>
                    <div id="licencas" class="accordion-collapse collapse" data-bs-parent="#accordion-example">
                        <div class="accordion-body pt-0">
                            Licença
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </Modal>
</template>
