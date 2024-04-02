<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import { useForm } from '@inertiajs/vue3';
import { IconX } from '@tabler/icons-vue';
import { IconDeviceFloppy } from '@tabler/icons-vue';

// FORM
const form = useForm({
    tema: null,
    servico: null,
    especificacao: null,
    introducao: null,
    justificativa: null,
    objetivos: null,
    metodologia: null,

    // ...props.licenca,
});
</script>

<template>
    <!-- Modal -->
    <div class="modal fade" id="modalNovoServico" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title" id="modalNovoServicoLabel">
                        Novo Serviço
                    </h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <ul class="nav nav-tabs card-header-tabs nav-fill mb-1" data-bs-toggle="tabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a href="#dadosGerais" class="nav-link active" data-bs-toggle="tab" aria-selected="true"
                                role="tab">
                                Dados Gerais
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a href="#vincularRecursos" class="nav-link" data-bs-toggle="tab" aria-selected="false"
                                role="tab" tabindex="-1">
                                Vincular Recursos
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a href="#vincularCondicionantes" class="nav-link" data-bs-toggle="tab"
                                aria-selected="false" role="tab" tabindex="-1">
                                Vincular Condicionantes
                            </a>
                        </li>
                    </ul>

                    <div class="card-body">
                        <div class="tab-content">
                            <!-- Dados Gerais -->
                            <div class="tab-pane fade active show" id="dadosGerais" role="tabpanel">
                                <!-- ROW 1 -->
                                <div class="row mb-4">
                                    <!-- Tema: -->
                                    <div class="col-4">
                                        <InputLabel value="Tema:" for="tema" />
                                        <select name="tema" id="tema" class="form-select" v-model="form.tema">
                                            <option :value="null"> -- SELECIONE -- </option>
                                            <option value="Fauna">
                                                Fauna
                                            </option>
                                            <option value="Flora">
                                                Flora
                                            </option>
                                            <option value="Recursos Hídricos">
                                                Recursos Hídricos
                                            </option>
                                            <option value="Bens Culturais Acautelados">
                                                Bens Culturais Acautelados
                                            </option>
                                            <option value="Espeleologia">
                                                Espeleologia
                                            </option>
                                            <option value="Indígena">
                                                Indígena
                                            </option>
                                            <option value="Quilombola">
                                                Quilombola
                                            </option>
                                            <option value="Saúde">
                                                Saúde
                                            </option>
                                            <option value="PAC">
                                                PAC
                                            </option>
                                            <option value="Sociais">
                                                Sociais
                                            </option>
                                            <option value="Físicos">
                                                Físicos
                                            </option>
                                            <option value="PRAD">
                                                PRAD
                                            </option>
                                            <option value="Outros">
                                                Outros
                                            </option>
                                            <option value="Supervisão Ambiental">
                                                Supervisão Ambiental
                                            </option>
                                        </select>
                                        <InputError :message="form.errors.tema" />
                                    </div>

                                    <!-- Serviço: -->
                                    <div class="col-8">
                                        <InputLabel value="Servico:" for="servico" />
                                        <!-- form.tema == 'Fauna' -->
                                        <select v-if="form.tema == 'Fauna'" name="servico" id="servico"
                                            class="form-select" v-model="form.servico">
                                            <option :value="null"> -- SELECIONE -- </option>
                                            <option value="Afugentamento e Resgate de Fauna">
                                                Afugentamento e Resgate de Fauna
                                            </option>
                                            <option value="Monitoramento do Atropelamento da Fauna">
                                                Monitoramento do Atropelamento da Fauna
                                            </option>
                                            <option value="Monitoramento da Fauna">
                                                Monitoramento da Fauna
                                            </option>
                                            <option value="Monitoramento de Passagem da Fauna">
                                                Monitoramento de Passagem da Fauna
                                            </option>
                                            <option value="Outros">
                                                Outros
                                            </option>
                                        </select>
                                        <!-- form.tema == 'Flora' -->
                                        <select v-else-if="form.tema == 'Flora'" name="servico" id="servico"
                                            class="form-select" v-model="form.servico">
                                            <option :value="null"> -- SELECIONE -- </option>
                                            <option value="Supressão de Vegetação">
                                                Supressão de Vegetação
                                            </option>
                                            <option value="Outros">
                                                Outros
                                            </option>
                                        </select>
                                        <!-- form.tema == 'Recursos Hídricos' -->
                                        <select v-else-if="form.tema == 'Recursos Hídricos'" name="servico" id="servico"
                                            class="form-select" v-model="form.servico">
                                            <option :value="null"> -- SELECIONE -- </option>
                                            <option value="Monitoramento da Qualidade da Água">
                                                Monitoramento da Qualidade da Água
                                            </option>
                                            <option value="Outros">
                                                Outros
                                            </option>
                                        </select>
                                        <!-- form.tema == 'Supervisão Ambiental' -->
                                        <select v-else-if="form.tema == 'Supervisão Ambiental'" name="servico"
                                            id="servico" class="form-select" v-model="form.servico">
                                            <option :value="null"> -- SELECIONE -- </option>
                                            <option value="Controle de Ocorrências">
                                                Controle de Ocorrências
                                            </option>
                                            <option value="Outros">
                                                Outros
                                            </option>
                                        </select>
                                        <!-- form.tema == outros -->
                                        <select v-else name="servico" id="servico" class="form-select"
                                            v-model="form.servico">
                                            <option :value="null"> -- SELECIONE -- </option>
                                            <option value="Outros">
                                                Outros
                                            </option>
                                        </select>
                                        <InputError :message="form.errors.servico" />
                                    </div>
                                </div>

                                <!-- ROW 2 -->
                                <div class="row mb-4">
                                    <!-- Especificação: -->
                                    <div class="col-12">
                                        <InputLabel value="Especificação:" for="especificacao" />
                                        <textarea name="especificacao" id="especificacao" class="form-control" rows="3"
                                            v-model="form.especificacao"></textarea>
                                        <InputError :message="form.errors.especificacao" />
                                    </div>
                                </div>

                                <!-- ROW 3 -->
                                <div class="row mb-4">
                                    <!-- Introdução: -->
                                    <div class="col-12">
                                        <InputLabel value="Introdução:" for="introducao" />
                                        <textarea name="introducao" id="introducao" class="form-control" rows="3"
                                            v-model="form.introducao"></textarea>
                                        <InputError :message="form.errors.introducao" />
                                    </div>
                                </div>

                                <!-- ROW 4 -->
                                <div class="row mb-4">
                                    <!-- Justificativa: -->
                                    <div class="col-12">
                                        <InputLabel value="Justificativa:" for="justificativa" />
                                        <textarea name="justificativa" id="justificativa" class="form-control" rows="3"
                                            v-model="form.justificativa"></textarea>
                                        <InputError :message="form.errors.justificativa" />
                                    </div>
                                </div>

                                <!-- ROW 5 -->
                                <div class="row mb-4">
                                    <!-- Objetivos: -->
                                    <div class="col-12">
                                        <InputLabel value="Objetivos:" for="objetivos" />
                                        <textarea name="objetivos" id="objetivos" class="form-control" rows="3"
                                            v-model="form.objetivos"></textarea>
                                        <InputError :message="form.errors.objetivos" />
                                    </div>
                                </div>

                                <!-- ROW 6 -->
                                <div class="row mb-4">
                                    <!-- Metodologia: -->
                                    <div class="col-12">
                                        <InputLabel value="Metodologia:" for="metodologia" />
                                        <textarea name="metodologia" id="metodologia" class="form-control" rows="3"
                                            v-model="form.metodologia"></textarea>
                                        <InputError :message="form.errors.metodologia" />
                                    </div>
                                </div>

                                <div class="row mb-4">
                                    <div class="col-12 d-flex justify-content-end">
                                        <button type="button" class="btn btn-secondary" style="margin-right: 15px;"  data-bs-dismiss="modal">
                                            <IconX class="me-2"/> Close
                                        </button>
                                        <button type="button" class="btn btn-primary">
                                            <IconDeviceFloppy class="me-2"/> Salvar
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Vincular Recursos -->
                            <div class="tab-pane fade" id="vincularRecursos" role="tabpanel">
                                <div class="row mb-4">
                                    <!-- Recursos Humanos Disponíveis: -->
                                    <div class="col-4">
                                        <InputLabel value="Recursos Humanos Disponíveis:" for="rh_disponiveis" />
                                        <select name="rh_disponiveis" id="rh_disponiveis" class="form-select"
                                            v-model="form.rh_disponiveis">
                                            <option :value="null"> -- SELECIONE -- </option>
                                            <option value="Cleber Pinto">
                                                Cleber Pinto
                                            </option>
                                            <option value="Felipe Abrahão Monteiro">
                                                Felipe Abrahão Monteiro
                                            </option>
                                            <option value="ADM">
                                                ADM
                                            </option>
                                        </select>
                                        <InputError :message="form.errors.rh_disponiveis" />
                                    </div>

                                    <!-- Veiculos disponíveis: -->
                                    <div class="col-4">
                                        <InputLabel value="Veiculos disponíveis:" for="veiculos_disponiveis" />
                                        <select name="veiculos_disponiveis" id="veiculos_disponiveis"
                                            class="form-select" v-model="form.veiculos_disponiveis">
                                            <option :value="null"> -- SELECIONE -- </option>
                                            <option value="Cleber Pinto">
                                                Cleber Pinto
                                            </option>
                                            <option value="Felipe Abrahão Monteiro">
                                                Felipe Abrahão Monteiro
                                            </option>
                                            <option value="ADM">
                                                ADM
                                            </option>
                                        </select>
                                        <InputError :message="form.errors.veiculos_disponiveis" />
                                    </div>

                                    <!-- Equipamentos disponíveis: -->
                                    <div class="col-4">
                                        <InputLabel value="Equipamentos disponíveis:" for="equipamentos_disponiveis" />
                                        <select name="equipamentos_disponiveis" id="equipamentos_disponiveis"
                                            class="form-select" v-model="form.equipamentos_disponiveis">
                                            <option :value="null"> -- SELECIONE -- </option>
                                            <option value="Cleber Pinto">
                                                Cleber Pinto
                                            </option>
                                            <option value="Felipe Abrahão Monteiro">
                                                Felipe Abrahão Monteiro
                                            </option>
                                            <option value="ADM">
                                                ADM
                                            </option>
                                        </select>
                                        <InputError :message="form.errors.equipamentos_disponiveis" />
                                    </div>
                                </div>

                                <div class="row mb-4">
                                    <div class="col-12 d-flex justify-content-end">
                                        <button type="button" class="btn btn-secondary" style="margin-right: 15px;"  data-bs-dismiss="modal">
                                            <IconX class="me-2"/> Close
                                        </button>
                                        <button type="button" class="btn btn-primary">
                                            <IconDeviceFloppy class="me-2"/> Salvar
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Vincular Condicionantes -->
                            <div class="tab-pane fade" id="vincularCondicionantes" role="tabpanel">
                                <div class="row mb-4">
                                    <!-- Licença: -->
                                    <div class="col-6">
                                        <InputLabel value="Licença:" for="licenca" />
                                        <select name="licenca" id="licenca" class="form-select" v-model="form.licenca">
                                            <option :value="null"> -- SELECIONE -- </option>
                                            <option value="Cleber Pinto">
                                                Cleber Pinto
                                            </option>
                                            <option value="Felipe Abrahão Monteiro">
                                                Felipe Abrahão Monteiro
                                            </option>
                                            <option value="ADM">
                                                ADM
                                            </option>
                                        </select>
                                        <InputError :message="form.errors.licenca" />
                                    </div>

                                    <!-- Condicionante: -->
                                    <div class="col-6">
                                        <InputLabel value="Condicionante:" for="condicionante" />
                                        <select name="condicionante" id="condicionante" class="form-select"
                                            v-model="form.condicionante">
                                            <option :value="null"> -- SELECIONE -- </option>
                                            <option value="Cleber Pinto">
                                                Cleber Pinto
                                            </option>
                                            <option value="Felipe Abrahão Monteiro">
                                                Felipe Abrahão Monteiro
                                            </option>
                                            <option value="ADM">
                                                ADM
                                            </option>
                                        </select>
                                        <InputError :message="form.errors.condicionante" />
                                    </div>
                                </div>

                                <div class="row mb-4">
                                    <div class="col-12 d-flex justify-content-end">
                                        <button type="button" class="btn btn-secondary" style="margin-right: 15px;"  data-bs-dismiss="modal">
                                            <IconX class="me-2"/> Close
                                        </button>
                                        <button type="button" class="btn btn-primary">
                                            <IconDeviceFloppy class="me-2"/> Salvar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
