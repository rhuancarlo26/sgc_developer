<script setup>
import Modal from "@/Components/Modal.vue";
import axios from "axios";
import { ref } from "vue";
import { useForm } from "@inertiajs/vue3";
import PieChart from "@/Components/PieChart.vue";
import BarChart from "@/Components/BarChart.vue";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import { useToast } from 'vue-toastification';

const toast = useToast();

const modalDetalhes = ref(null);

const resultado = ref({
    id: null,
    nome: '',
    periodo: '',
    mes: '',
    ano: '',
    semestre: '',
    dt_inicio: '',
    dt_final: '',
});

const form = useForm({
    id: null,
    id_resultado: null,
    registros_identificados: '',
    registros_classe: '',
    registros_periodo: '',
    registros_km: '',
    registros_mes: '',
    registros_especie: '',
    formas_registros: '',
    graf_reg_classe: '',
    graf_reg_periodo: '',
    graf_formas_registros: '',
    graf_reg_km: '',
    graf_reg_mes: '',
    graf_reg_especie: '',
});

const outraAnalise = useForm({
    nome: '',
});

const chartData = ref({ labels: [], datasets: [] });
const chartOptions = ref({});

const montarRegistrosClasse = () => {
    let classes = [];
    let numPorClasse = [];
    let number;

    if (resultado.value.classe) {
        resultado.value.classe.forEach(i => {
            classes.push(i.classe);
            number = Number(i.n_individuos);
            numPorClasse.push(number);
        });
    }

    chartData.value = {
        labels: classes,
        datasets: [{
            data: numPorClasse,
            backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40'],
        }]
    };

    chartOptions.value = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'top',
            },
            title: {
                display: true,
                text: 'Registros por Classe'
            }
        }
    };
};

const chartSeries = ref({ labels: [], datasets: [{ label: 'Registrados por Período', data: [] }] });
const charttOptions = ref({});

const montarRegistrosPeriodo = () => {
    let periodo = [];
    let qntdRegistros = [];

    resultado.value.periodo.forEach(i => {
        periodo.push(`Período: ${i.dt_inicio} - ${i.dt_final}`);
        qntdRegistros.push(i.n_individuos);
    });

    let max = qntdRegistros.reduce((a, b) => Math.max(a, b), -Infinity);

    chartSeries.value = {
        labels: periodo,
        datasets: [{
            label: 'Registrados por Período',
            data: qntdRegistros,
            backgroundColor: '#4BC0C0',
        }]
    };

    charttOptions.value = {
        responsive: true,
        plugins: {
            legend: {
                position: 'top',
            },
            title: {
                display: true,
                text: 'Registrados por Período'
            }
        },
        scales: {
            x: {
                title: {
                    display: true,
                }
            },
            y: {
                title: {
                    display: true,
                    text: 'Quantidade de Registros'
                },
                max: max * 1.5
            }
        }
    };
};

const charttData = ref({ labels: [], datasets: [] });
const chaartOptions = ref({});

const montarFormasRegistros = () => {
    let nomes = [];
    let numPorForma = [];
    let number;

    resultado.value.formas.forEach(i => {
        nomes.push(i.nome);
        number = Number(i.n_individuos);
        numPorForma.push(number);
    });

    charttData.value = {
        labels: nomes,
        datasets: [{
            data: numPorForma,
            backgroundColor: ['#FFCE56', '#FF6384', '#36A2EB', '#4BC0C0', '#9966FF', '#FF9F40'],
        }]
    };

    chaartOptions.value = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
            },
            title: {
                display: true,
                text: 'Formas de Registros',
                align: 'center',
            }
        }
    };
};

const chartKmData = ref({ labels: [], datasets: [] });
const chartKmOptions = ref({});

const montarRegistrosKm = () => {
    let title = 'Registrados por Km';
    let km = [];
    let qntdRegistros = [];

    resultado.value.km.forEach(i => {
        km.push(i.km);
        qntdRegistros.push(i.n_individuos);
    });

    let max = qntdRegistros.reduce((a, b) => Math.max(a, b), -Infinity);

    chartKmData.value = {
        labels: km,
        datasets: [{
            label: title,
            data: qntdRegistros,
            backgroundColor: '#4BC0C0',
        }]
    };

    chartKmOptions.value = {
        responsive: true,
        plugins: {
            legend: {
                position: 'top',
            },
            title: {
                display: true,
                text: title,
                align: 'center',
            }
        },
        scales: {
            x: {
                title: {
                    display: true,
                    text: 'Km'
                },
                ticks: {
                    autoSkip: false,
                    maxRotation: 90,
                    minRotation: 45
                }
            },
            y: {
                title: {
                    display: true,
                    text: 'Quantidade de Registros'
                },
                max: max * 1.5
            }
        }
    };
};

const chartMesData = ref({ labels: [], datasets: [] });
const chartMesOptions = ref({});

const montarRegistrosMes = () => {
    let title = 'Registrados por Mês';
    let mes = [];
    let qntdRegistros = [];

    resultado.value.mes.forEach(i => {
        mes.push(i.mes);
        qntdRegistros.push(i.n_individuos);
    });

    let max = qntdRegistros.reduce((a, b) => Math.max(a, b), -Infinity);

    chartMesData.value = {
        labels: mes,
        datasets: [{
            label: title,
            data: qntdRegistros,
            backgroundColor: '#4BC0C0',
        }]
    };

    chartMesOptions.value = {
        responsive: true,
        plugins: {
            legend: {
                position: 'top',
                horizontalAlign: 'right',
                floating: true,
                offsetY: -10,
                offsetX: 5
            },
            title: {
                display: true,
                text: title,
                align: 'center',
            }
        },
        scales: {
            x: {
                title: {
                    display: true,
                },
                labels: {
                    rotate: -45
                },
                categories: mes,
            },
            y: {
                title: {
                    display: true,
                    text: 'Quantidade de Registros'
                },
                max: max * 1.5
            }
        },
        plotOptions: {
            bar: {
                borderRadius: 10,
                dataLabels: {
                    position: 'top', // top, center, bottom
                },
            }
        },
        dataLabels: {
            enabled: true
        },
        grid: {
            row: {
                colors: ['#f3f3f3', 'transparent'],
                opacity: 0.5
            },
        },
        stroke: {
            curve: 'smooth'
        }
    };
};

const chartEspecieSeries = ref({ labels: [], datasets: [] });
const chartEspecieOptions = ref({});

const montarRegistrosEspecie = () => {
    let title = 'Registrados por Espécie';
    let especies = [];
    let qntdRegistros = [];

    resultado.value.especie.forEach(i => {
        especies.push(i.especie);
        qntdRegistros.push(i.n_individuos);
    });

    let max = qntdRegistros.reduce((a, b) => Math.max(a, b), -Infinity);

    chartEspecieSeries.value = {
        labels: especies,
        datasets: [{
            label: title,
            data: qntdRegistros,
            backgroundColor: '#4BC0C0',
        }]
    };

    chartEspecieOptions.value = {
        responsive: true,
        plugins: {
            legend: {
                position: 'top',
                horizontalAlign: 'right',
                floating: true,
                offsetY: -10,
                offsetX: 5
            },
            title: {
                display: true,
                text: title,
                align: 'center',
            }
        },
        scales: {
            x: {
                title: {
                    display: true,
                    text: 'Quantidade de Registros'
                },
                max: max * 1.2
            },
            y: {
                title: {
                    display: true,
                },
                ticks: {
                    autoSkip: false,
                    maxRotation: 0,
                    minRotation: 0
                }
            }
        },
        indexAxis: 'y',
        dataLabels: {
            enabled: true
        },
        grid: {
            row: {
                colors: ['#f3f3f3', 'transparent'],
                opacity: 0.5
            },
        },
        stroke: {
            curve: 'smooth'
        }
    };
};

const abrirModal = (item) => {
    resultado.value = item;
    consultaDados();
    modalDetalhes.value.getBsModal().show();
}

const preencherCamposFormulario = (dados) => {
    form.id = dados.id;
    form.id_resultado = dados.id_resultado;
    form.registros_identificados = dados.registros_identificados;
    form.registros_classe = dados.registros_classe;
    form.registros_periodo = dados.registros_periodo;
    form.registros_km = dados.registros_km;
    form.registros_mes = dados.registros_mes;
    form.registros_especie = dados.registros_especie;
    form.formas_registros = dados.formas_registros;
    form.graf_reg_classe = dados.graf_reg_classe;
    form.graf_reg_periodo = dados.graf_reg_periodo;
    form.graf_formas_registros = dados.graf_formas_registros;
    form.graf_reg_km = dados.graf_reg_km;
    form.graf_reg_mes = dados.graf_reg_mes;
    form.graf_reg_especie = dados.graf_reg_especie;
};

const preencheAnalise = () => {
    axios.get(route('contratos.contratada.servicos.afugentamento.resgate.fauna.resultado.analise.get', resultado.value.id))
        .then(response => {
            preencherCamposFormulario(response.data[0]);
        })
        .catch(error => {
            console.log(error);
        });
};

const totalIndividuos = ref(0);

const calcularTotalIndividuos = () => {
    totalIndividuos.value = 0;
    for (const i of resultado.value.identificados) {
        let total = Number(i.n_individuos);
        totalIndividuos.value += total;
    }
};

const consultaDados = () => {
    axios.get(route('contratos.contratada.servicos.afugentamento.resgate.fauna.resultado.get', resultado.value.id))
        .then(response => {
            resultado.value.identificados = response.data.registros_identificados;
            resultado.value.classe = response.data.registros_classe;
            resultado.value.periodo = response.data.registros_periodo;
            resultado.value.formas = response.data.formas_registros;
            resultado.value.km = response.data.registros_km;
            resultado.value.mes = response.data.registros_mes;
            resultado.value.especie = response.data.registros_especie;

            calcularTotalIndividuos();
            montarRegistrosClasse();
            montarRegistrosPeriodo();
            montarFormasRegistros();
            montarRegistrosKm();
            montarRegistrosMes();
            montarRegistrosEspecie();

            preencheAnalise();
        })
        .catch(error => {
            console.log(error);
        });
}

const salvarAnalise = (coluna) => {
    if (form.id) {
        form.transform((data) => {
            return { [coluna]: data[coluna] }
        }).patch(route('contratos.contratada.servicos.afugentamento.resgate.fauna.resultado.analise.update', form.id), {
            onSuccess: () => {
                toast.success('Análise atualizada com sucesso!');
                preencheAnalise();
            }
        });
        return;
    }

    form.transform((data) => {
        return { [coluna]: data[coluna] }
    }).post(route('contratos.contratada.servicos.afugentamento.resgate.fauna.resultado.analise.create', { resultado: resultado.value.id }), {
        onSuccess: () => {
            toast.success('Análise salva com sucesso!');
            preencheAnalise();
        }
    });
};

const cancelar = (ref) => {
    ref.querySelector('.accordion-collapse').classList.remove('show');
    preencheAnalise();
};

defineExpose({ abrirModal });

</script>

<template>
    <form @submit.prevent="salvar">

        <Modal ref="modalDetalhes" title="Análise dos resultados programa de Afugentamento de Fauna"
            modalClass="modal-blur" modal-dialog-class="modal-xl">
            <template #body>
                <div class="mb-4">
                    <div class="card-body">
                        <div class="accordion" id="servico">
                            <div class="accordion-item" ref="accordion1">
                                <h2 class="accordion-header" id="heading-1">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#accordion-1" aria-expanded="true">
                                        Tabela com os Registros Identificados
                                    </button>
                                </h2>
                                <div id="accordion-1" class="accordion-collapse collapse show"
                                    data-bs-parent="#servico">
                                    <div class="accordion-body pt-0 card-body space-y-5">
                                        <table id="table-resultado-analise"
                                            class="table table-bordered table-striped table-hover js-basic-example dataTable"
                                            style="width:100%;">
                                            <thead>
                                                <tr class="bg-light text-center">
                                                    <th rowspan="3">Espécie</th>
                                                    <th rowspan="3">Nome Comum</th>
                                                </tr>
                                                <tr class="bg-light text-center">
                                                    <th rowspan="3">Nº Indivíduos</th>
                                                    <th rowspan="3">Frequência (%)</th>
                                                    <th colspan="2">Status Conservação</th>
                                                </tr>
                                                <tr class="bg-light text-center">
                                                    <th>IUCN</th>
                                                    <th>FEDERAL</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr class="text-center" v-for="item in resultado.identificados">
                                                    <td>{{ item.especie }}</td>
                                                    <td>{{ item.nome_comum }}</td>
                                                    <td>{{ item.n_individuos }}</td>
                                                    <td>
                                                        {{
                                                            item.n_individuos * 100 / totalIndividuos | formatarNumero
                                                        }}%
                                                    </td>
                                                    <td>{{ item.sigla_iucn }} - {{ item.nome_iucn }}</td>
                                                    <td>{{ item.sigla_federal }} - {{ item.nome_federal }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <div class="col-md-4">
                                            <label>Número Total de Indivíduos:
                                                <strong>
                                                    {{ totalIndividuos }}
                                                </strong>
                                            </label>
                                        </div>
                                        <div class="col-md-12 mb-4">
                                            <InputLabel value="Análise" for="registros_identificados" />
                                            <textarea id="registros_identificados"
                                                v-model="form.registros_identificados" rows="2"
                                                class="form-control"></textarea>
                                            <button type="button" class="btn btn-red mt-2 me-2"
                                                @click="cancelar($refs.accordion1)">Cancelar</button>
                                            <button type="button" class="btn btn-primary mt-2"
                                                @click="salvarAnalise('registros_identificados')">Salvar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-item" ref="accordion2">
                                <h2 class="accordion-header" id="heading-2">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#accordion-2" aria-expanded="false">
                                        Frequência de Registros por Classe
                                    </button>
                                </h2>
                                <div id="accordion-2" class="accordion-collapse collapse" data-bs-parent="#servico">
                                    <div class="accordion-body pt-0 card-body space-y-5">
                                        <div style="height: 250px;">
                                            <PieChart :chart_data="chartData" :chart_options="chartOptions" />
                                        </div>

                                        <div class="col-md-12 mb-4">
                                            <InputLabel value="Análise" for="registros_classe" />
                                            <textarea id="registros_classe" v-model="form.registros_classe" rows="2"
                                                class="form-control"></textarea>
                                            <button type="button" class="btn btn-red mt-2 me-2"
                                                @click="cancelar($refs.accordion2)">Cancelar</button>
                                            <button type="button" class="btn btn-primary mt-2"
                                                @click="salvarAnalise('registros_classe')">Salvar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-item" ref="accordion3">
                                <h2 class="accordion-header" id="heading-3">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#accordion-3" aria-expanded="false">
                                        Número de Animais Registrados por Período
                                    </button>
                                </h2>
                                <div id="accordion-3" class="accordion-collapse collapse" data-bs-parent="#servico">
                                    <div class="accordion-body pt-0 card-body space-y-5">
                                        <BarChart :chart_data="chartSeries" :chart_options="charttOptions" />

                                        <div class="col-md-12 mb-4">
                                            <InputLabel value="Análise" for="registros_periodo" />
                                            <textarea id="registros_periodo" v-model="form.registros_periodo" rows="2"
                                                class="form-control"></textarea>
                                            <button type="button" class="btn btn-red mt-2 me-2"
                                                @click="cancelar($refs.accordion3)">Cancelar</button>
                                            <button type="button" class="btn btn-primary mt-2"
                                                @click="salvarAnalise('registros_periodo')">Salvar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-item" ref="accordion4">
                                <h2 class="accordion-header" id="heading-4">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#accordion-4" aria-expanded="false">
                                        Frequência nas Formas de Registros
                                    </button>
                                </h2>
                                <div id="accordion-4" class="accordion-collapse collapse" data-bs-parent="#servico">
                                    <div class="accordion-body pt-0 card-body space-y-5">
                                        <div style="height: 250px;">
                                            <PieChart :chart_data="charttData" :chart_options="chaartOptions" />
                                        </div>

                                        <div class="col-md-12 mb-4">
                                            <InputLabel value="Análise" for="formas_registros" />
                                            <textarea id="formas_registros" v-model="form.formas_registros" rows="2"
                                                class="form-control"></textarea>
                                            <button type="button" class="btn btn-red mt-2 me-2"
                                                @click="cancelar($refs.accordion4)">Cancelar</button>
                                            <button type="button" class="btn btn-primary mt-2"
                                                @click="salvarAnalise('formas_registros')">Salvar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-item" ref="accordion5">
                                <h2 class="accordion-header" id="heading-5">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#accordion-5" aria-expanded="false">
                                        Número de Animais Registrados por KM
                                    </button>
                                </h2>
                                <div id="accordion-5" class="accordion-collapse collapse" data-bs-parent="#servico">
                                    <div class="accordion-body pt-0 card-body space-y-5">
                                        <BarChart :chart_data="chartKmData" :chart_options="chartKmOptions" />

                                        <div class="col-md-12 mb-4">
                                            <InputLabel value="Análise" for="registros_km" />
                                            <textarea id="registros_km" v-model="form.registros_km" rows="2"
                                                class="form-control"></textarea>
                                            <button type="button" class="btn btn-red mt-2 me-2"
                                                @click="cancelar($refs.accordion5)">Cancelar</button>
                                            <button type="button" class="btn btn-primary mt-2"
                                                @click="salvarAnalise('registros_km')">Salvar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-item" ref="accordion6">
                                <h2 class="accordion-header" id="heading-6">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#accordion-6" aria-expanded="false">
                                        Número de Animais Registrados por Mês
                                    </button>
                                </h2>
                                <div id="accordion-6" class="accordion-collapse collapse" data-bs-parent="#servico">
                                    <div class="accordion-body pt-0 card-body space-y-5">
                                        <BarChart :chart_data="chartSeries" :chart_options="charttOptions" />

                                        <div class="col-md-12 mb-4">
                                            <InputLabel value="Análise" for="registros_mes" />
                                            <textarea id="registros_mes" v-model="form.registros_mes" rows="2"
                                                class="form-control"></textarea>
                                            <button type="button" class="btn btn-red mt-2 me-2"
                                                @click="cancelar($refs.accordion6)">Cancelar</button>
                                            <button type="button" class="btn btn-primary mt-2"
                                                @click="salvarAnalise('registros_mes')">Salvar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-item" ref="accordion7">
                                <h2 class="accordion-header" id="heading-7">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#accordion-7" aria-expanded="false">
                                        Número de Animais Registrados por Espécie
                                    </button>
                                </h2>
                                <div id="accordion-7" class="accordion-collapse collapse" data-bs-parent="#servico">
                                    <div class="accordion-body pt-0 card-body space-y-5">
                                        <BarChart :chart_data="chartEspecieSeries"
                                            :chart_options="chartEspecieOptions" />

                                        <div class="col-md-12 mb-4">
                                            <InputLabel value="Análise" for="registros_especie" />
                                            <textarea id="registros_especie" v-model="form.registros_especie" rows="2"
                                                class="form-control"></textarea>
                                            <button type="button" class="btn btn-red mt-2 me-2"
                                                @click="cancelar($refs.accordion7)">Cancelar</button>
                                            <button type="button" class="btn btn-primary mt-2"
                                                @click="salvarAnalise('registros_especie')">Salvar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-item" ref="accordion8">
                                <h2 class="accordion-header" id="heading-8">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#accordion-8" aria-expanded="false">
                                        Outras análises
                                    </button>
                                </h2>
                                <div id="accordion-8" class="accordion-collapse collapse" data-bs-parent="#servico">
                                    <div class="accordion-body pt-0 card-body space-y-5">
                                        <InputLabel value="Nome da Análise" v-model="outraAnalise.nome" />
                                        <input type="text" class="form-control" />
                                        <InputError :message="outraAnalise.errors.nome" />

                                        <input type="file" @input="outraAnalise.arquivo = $event.target.files[0]"
                                            class="form-control" />
                                        <InputError :message="outraAnalise.errors.arquivo" />

                                        <div class="col-md-12 mb-4">
                                            <label>Análise dos resultados</label>
                                            <textarea id="analise" v-model="outraAnalise.analise" rows="2"
                                                class="form-control"></textarea>
                                            <button type="button" class="btn btn-red mt-2 me-2"
                                                @click="cancelarOutraAnalise($refs.accordion8)">Cancelar</button>
                                            <button type="button" class="btn btn-primary mt-2"
                                                @click="salvarOutraAnalise(outraAnalise)">Salvar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </Modal>

    </form>
</template>
<style scoped>
#my-pie-chart-id {
    height: 100% !important;
}
</style>