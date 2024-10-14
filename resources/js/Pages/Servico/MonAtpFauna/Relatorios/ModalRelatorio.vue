<script setup>
import { ref } from "vue";
import Modal from "@/Components/Modal.vue";
import { dateTimeFormat } from "@/Utils/DateTimeUtils.js";
import Table from "@/Components/Table.vue";
import axios from "axios";
import BarChart from "@/Components/BarChart.vue";
import PieChart from "@/Components/PieChart.vue";

const props = defineProps({
    contrato: { type: Object },
})

const modalRef = ref();
const relatorio = ref({});
const abrirModal = async (item) => {
    const { data } = await axios.post(route('contratos.contratada.servicos.mon_atp_fauna.relatorios.relatorio'), item)
    relatorio.value = {
        nome: item.nome_relatorio,
        data_relatorio: item.created_at,
        conclusao: item.conclusao,
        sobre_relatorio: item.sobre_relatorio,
        ...data
    }
    modalRef.value.getBsModal().show();
}

defineExpose({ abrirModal });
</script>

<template>
    <Modal ref="modalRef" title="" modal-dialog-class="modal-xl">
        <template #body>
            <div v-if="Object.keys(relatorio).length > 0">
                <a data-bs-target="#carousel-sample" role="button" data-bs-slide="prev">
                    <span class="btn btn-info me-2">Anterior</span>
                </a>
                <a data-bs-target="#carousel-sample" role="button" data-bs-slide="next">
                    <span class="btn btn-info">Próxima</span>
                </a>
                <div id="carousel-sample" class="carousel slide mt-3">
                    <div class="carousel-inner">
                        <div class="carousel-item active">
                            <div class="capa global">
                                <div class="subtitulo">
                                    <h4 class="nome_contratada">{{ relatorio.contrato.contratada }}</h4>
                                    <h4 class="rodovia">{{ relatorio.contrato.rodovia }} /
                                        {{ relatorio.contrato.uf }}</h4>
                                </div>
                                <div class="titulo">
                                    <h2 class="nome_titulo">
                                        {{ relatorio.nome }}
                                    </h2>
                                </div>
                                <div class="footer_capa">
                                    <span class="mes">{{ dateTimeFormat(relatorio.data_relatorio) }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="carousel-item">
                            <div class="execucao global">
                                <ul class="lista">
                                    <li class="lista_content titulo_lista">1. Introdução</li>
                                    <li class="lista_content titulo_lista">
                                        <h6 class="">{{ relatorio.servico.introducao }}</h6>
                                    </li>
                                    <li class="lista_content titulo_lista">2. Justificativa</li>
                                    <li class="lista_content titulo_lista">
                                        <h6 class="">{{ relatorio.servico.justificativa }}</h6>
                                    </li>
                                    <li class="lista_content titulo_lista">3. Objetivos</li>
                                    <li class="lista_content titulo_lista">
                                        <h6 class="">{{ relatorio.servico.objetivos }}</h6>
                                    </li>
                                    <li class="lista_content titulo_lista">4. Metodologia</li>
                                    <li class="lista_content titulo_lista">
                                        <h6 class="">{{ relatorio.servico.metodologia }}</h6>
                                    </li>
                                    <li class="lista_content titulo_lista">5. Público Alvo</li>
                                    <li class="lista_content titulo_lista">
                                        <h6 class="">{{ relatorio.servico.publico_alvo }}</h6>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="carousel-item">
                            <div class="execucao global">
                                <ul class="lista">
                                    <li class="lista_content titulo_lista">6. Vinculos</li>
                                    <ul class="lista">
                                        <li class="lista_content subtitulo_lista">6.1 RECURSOS HUMANOS</li>
                                        <table id="table-parametros" class="table_relatorio col-12">
                                            <thead>
                                                <tr>
                                                    <th class="thead_relatorio"><span>Nome do Profissional</span></th>
                                                    <th class="thead_relatorio"><span>Profissão</span></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="i in relatorio.rh">
                                                    <td class="tbody_relatorio">{{ i.nome }}</td>
                                                    <td class="tbody_relatorio">{{ i.profissao }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </ul>
                                    <ul class="lista">
                                        <li class="lista_content subtitulo_lista">6.2 VEÍCULOS</li>
                                        <table id="table-parametros" class="table_relatorio col-12">
                                            <thead>
                                                <tr>
                                                    <th class="thead_relatorio"><span>Nome do Veiculo</span></th>
                                                    <th class="thead_relatorio"><span>Modelo</span></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="i in relatorio.veiculo">
                                                    <td class="tbody_relatorio">{{ i.tipo }}-{{ i.marca }}</td>
                                                    <td class="tbody_relatorio">{{ i.modelo }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </ul>
                                    <ul class="lista">
                                        <li class="lista_content subtitulo_lista">6.3 EQUIPAMENTOS</li>
                                        <table id="table-parametros" class="table_relatorio col-12">
                                            <thead>
                                                <tr>
                                                    <th class="thead_relatorio"><span>Nome do equipamento</span></th>
                                                    <th class="thead_relatorio"><span>Modelo</span></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="i in relatorio.equipamento">
                                                    <td class="tbody_relatorio">{{ i.nome }}</td>
                                                    <td class="tbody_relatorio">{{ i.modelo }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </ul>
                                    <ul class="lista">
                                        <li class="lista_content subtitulo_lista">6.4 LICENÇAS</li>
                                        <table id="table-parametros" class="table_relatorio col-12">
                                            <thead>
                                                <tr>
                                                    <th class="thead_relatorio"><span>Licença</span></th>
                                                    <th class="thead_relatorio"><span>Condicionante</span></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="i in relatorio.licenca">
                                                    <td class="tbody_relatorio">{{ i.numero_licenca }}</td>
                                                    <td class="tbody_relatorio">{{ i.titulo_condicionante }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </ul>
                                </ul>
                            </div>
                        </div>
                        <div class="carousel-item">
                            <ul class="lista table_relatorio">
                                <li class="lista titulo_lista">7. Campanha</li>
                                <ul class="lista" v-for="c in relatorio.campanhas">
                                    <li class="lista_content subtitulo_lista">Campanha: {{ c.fk_campanha }}</li>
                                    <table id="table-parametros" class="table_relatorio col-12">
                                        <thead>
                                            <tr>
                                                <th class="thead_relatorio">Nº Licença</th>
                                                <th class="thead_relatorio">Empreendimento</th>
                                                <th class="thead_relatorio">Emissor</th>
                                                <th class="thead_relatorio">Data de emissão</th>
                                                <th class="thead_relatorio">Vencimento</th>
                                                <th class="thead_relatorio">Responsável</th>
                                                <th class="thead_relatorio">Processo DNIT</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="lista in relatorio.abios_campanha">
                                                <td class="tbody_relatorio">{{ lista.numero_licenca }}</td>
                                                <td class="tbody_relatorio">{{ lista.empreendimento }}</td>
                                                <td class="tbody_relatorio">{{ lista.emissor }}</td>
                                                <td class="tbody_relatorio">{{ lista.data_emissao }}</td>
                                                <td class="tbody_relatorio">{{ lista.vencimento }}</td>
                                                <td class="tbody_relatorio">{{ lista.fiscal }}</td>
                                                <td class="tbody_relatorio">{{ lista.processo_dnit }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </ul>
                                <li class="lista titulo_lista">8. Sobre o relatório</li>
                                <div class="conclusao">
                                    <span>{{ relatorio.sobre_relatorio }}</span>
                                </div>
                            </ul>
                        </div>
                        <div class="carousel-item">
                            <ul class="lista table_relatorio">
                                <li class="lista titulo_lista">9. Tabela com a relação de Abio emitidas para o
                                    empreendimento.</li>
                                <li class="lista">
                                    <table id="table-parametros" class="table_relatorio col-12">
                                        <thead>
                                            <tr>
                                                <th class="thead_relatorio">Tipo</th>
                                                <th class="thead_relatorio">Nº Licença</th>
                                                <th class="thead_relatorio">Empreendimento</th>
                                                <th class="thead_relatorio">Emissor</th>
                                                <th class="thead_relatorio">Data de emissão</th>
                                                <th class="thead_relatorio">Vencimento</th>
                                                <th class="thead_relatorio">Responsável</th>
                                                <th class="thead_relatorio">Processo DNIT</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="lista in relatorio.abios">
                                                <td class="tbody_relatorio">{{ lista.sigla }}</td>
                                                <td class="tbody_relatorio">{{ lista.numero_licenca }}</td>
                                                <td class="tbody_relatorio">{{ lista.empreendimento }}</td>
                                                <td class="tbody_relatorio">{{ lista.emissor }}</td>
                                                <td class="tbody_relatorio">{{ lista.data_emissao }}</td>
                                                <td class="tbody_relatorio">{{ lista.vencimento }}</td>
                                                <td class="tbody_relatorio">{{ lista.fiscal }}</td>
                                                <td class="tbody_relatorio">{{ lista.processo_dnit }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </li>
                                <li class="lista titulo_lista">10. Dados da Malha viária</li>
                                <li class="lista">
                                    <div class="grupo_cards">
                                        <div class="card_relatorio">
                                            <div class="card_r">
                                                <div class="info">
                                                    <div class="info_relatorio">BR:</div>
                                                    <div class="valor_relatorio">{{ relatorio.malha_viaria?.rodovia }}
                                                    </div>
                                                </div>
                                                <div class="border_bottom"></div>
                                                <div class="info">
                                                    <div class="info_relatorio">Empreendimento:</div>
                                                    <div class="valor_relatorio">
                                                        {{ relatorio.malha_viaria?.empreendimento }}</div>
                                                </div>
                                                <div class="border_bottom"></div>
                                                <div class="info">
                                                    <div class="info_relatorio">KM inicial:</div>
                                                    <div class="valor_relatorio">{{ relatorio.malha_viaria?.km_inicio }}
                                                    </div>
                                                </div>
                                                <div class="border_bottom"></div>
                                                <div class="info">
                                                    <div class="info_relatorio">KM final:</div>
                                                    <div class="valor_relatorio">{{ relatorio.malha_viaria?.km_fim }}
                                                    </div>
                                                </div>
                                                <div class="border_bottom"></div>
                                                <div class="info">
                                                    <div class="info_relatorio">Extensão:</div>
                                                    <div class="valor_relatorio">{{ relatorio.malha_viaria?.extensao }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                        <div class="carousel-item">
                            <ul class="lista table_relatorio">
                                <li class="lista titulo_lista">11. Mapa com a localização da malha viária objeto par ao
                                    programa de atropelamento de fauna</li>
                                <div id="relatorio_map"></div>
                            </ul>
                        </div>

                        <div class="carousel-item">
                            <ul class="lista">
                                <li class="lista_content titulo_lista">12. Resultados</li>
                                <ul class="lista">
                                    <li class="lista_content subtitulo_lista">12.1 Tabela com a relação de espécies
                                        atropeladas da malha viária</li>
                                    <ul class="lista">
                                        <table id="table-resultado" class="table table-hover non-hover"
                                            style="width:100% !important;">
                                            <thead>
                                                <tr role="row">
                                                    <th rowspan="2">Espécie</th>
                                                    <th rowspan="2">Nome comum</th>
                                                    <th rowspan="2" class="text-center">Nº Indivíduos</th>
                                                    <th rowspan="2" class="text-center">Frequência (%)</th>
                                                    <th colspan="2" class="text-center">Status Conservação</th>
                                                </tr>
                                                <tr role="row">
                                                    <th class="text-center">IUCN</th>
                                                    <th class="text-center">FEDERAL</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="lista in tabela_registro">
                                                    <td>{{ lista.especie }}</td>
                                                    <td>{{ lista.nome_comum }}</td>
                                                    <td class="text-center">{{ lista.n_individuos }}</td>
                                                    <td class="text-center">{{ (lista.n_individuos * 100 /
                                                        total_individuos).toFixed(2) }}%
                                                    </td>
                                                    <td class="text-center">{{ lista.iucn }}</td>
                                                    <td class="text-center">{{ lista.federal }}</td>
                                                </tr>
                                            </tbody>
                                            <tfoot>
                                                <th colspan="5">Número Total de Indivíduos: <b>{{ total_individuos
                                                        }}</b>
                                                </th>
                                            </tfoot>
                                        </table>
                                    </ul>
                                    <div class="conclusao">
                                        <span>{{ relatorio.analise?.analise_registros_identificados }}</span>
                                    </div>
                                </ul>
                            </ul>
                        </div>
                        <div class="carousel-item">
                            <ul class="lista">
                                <li class="lista_content titulo_lista">12. Resultados</li>
                                <ul class="lista">
                                    <li class="lista_content subtitulo_lista">12.2 Gráfico com o número de animais
                                        atropelados por campanha</li>
                                    <BarChart :style="{ height: 300 }" :chart_data="{
                                        labels: relatorio.atropelados_campanha?.map((item) => 'Campanha: ' + item.id),
                                        datasets: [{
                                            label: 'Atropelamentos por campanha',
                                            data: relatorio.atropelados_campanha?.map((item) => parseInt(item.n_individuos))
                                        }]
                                    }" :chart_options="{
                                        responsive: true,
                                        plugins: {
                                            tooltip: {
                                                enabled: false
                                            },
                                            datalabels: {
                                                display: true,
                                                color: 'white',
                                                align: 'top',
                                                font: {
                                                    weight: 'bold',
                                                },
                                            }
                                        }
                                    }" />
                                </ul>
                                <div class="conclusao">
                                    <span>{{ relatorio.analise?.analise_animais_atropelados_campanha }}</span>
                                </div>
                            </ul>
                        </div>
                        <div class="carousel-item">
                            <ul class="lista">
                                <li class="lista_content titulo_lista">12. Resultados</li>
                                <ul class="lista">
                                    <li class="lista_content subtitulo_lista">12.3 Gráfico com o número de animais
                                        atropelados por km</li>
                                    <BarChart :style="{ height: 300 }" :chart_data="{
                                        labels: relatorio.atropelados_km?.map((item) => 'Km: ' + item.km),
                                        datasets: [{
                                            label: 'Atropelamentos por km',
                                            data: relatorio.atropelados_km?.map((item) => parseInt(item.n_individuos))
                                        }]
                                    }" :chart_options="{
                                        responsive: true,
                                        plugins: {
                                            tooltip: {
                                                enabled: false
                                            },
                                            datalabels: {
                                                display: true,
                                                color: 'white',
                                                align: 'top',
                                                font: {
                                                    weight: 'bold',
                                                },
                                            }
                                        }
                                    }" />
                                </ul>
                                <div class="conclusao">
                                    <span>{{ relatorio.analise?.analise_animais_atropelados_km }}</span>
                                </div>
                            </ul>
                        </div>
                        <div class="carousel-item">
                            <ul class="lista">
                                <li class="lista_content titulo_lista">12. Resultados</li>
                                <ul class="lista">
                                    <li class="lista_content subtitulo_lista">12.4 Gráfico de Frequência de
                                        atropelamentos por classe</li>
                                    <PieChart :style="{ height: 300 }" :chart_data="{
                                        labels: relatorio.atropelados_classe?.map((item) => item.nome),
                                        datasets: [{
                                            label: 'Atropelamentos por classe',
                                            data: relatorio.atropelados_classe?.map((item) => parseInt(item.n_individuos))
                                        }]
                                    }" :chart_options="{
                                        responsive: true,
                                        plugins: {
                                            tooltip: {
                                                enabled: false
                                            },
                                            datalabels: {
                                                display: true,
                                                color: 'white',
                                                align: 'top',
                                                font: {
                                                    weight: 'bold',
                                                },
                                            }
                                        }
                                    }" />
                                </ul>
                                <div class="conclusao">
                                    <span>{{ relatorio.analise?.analise_frequencia_atropelamentos }}</span>
                                </div>
                            </ul>
                        </div>
                        <div class="carousel-item">
                            <ul class="lista">
                                <li class="lista_content titulo_lista">12. Resultados</li>
                                <ul class="lista">
                                    <li class="lista_content subtitulo_lista">12.5 Gráfico com o número de animais
                                        atropelados por espécies</li>
                                    <BarChart :style="{ height: 300 }" :chart_data="{
                                        labels: relatorio.atropelados_especie?.map((item) => item.especie),
                                        datasets: [{
                                            label: 'Atropelamentos por espécie',
                                            data: relatorio.atropelados_especie?.map((item) => parseInt(item.n_individuos))
                                        }]
                                    }" :chart_options="{
                                        responsive: true,
                                        plugins: {
                                            tooltip: {
                                                enabled: false
                                            },
                                            datalabels: {
                                                display: true,
                                                color: 'white',
                                                align: 'top',
                                                font: {
                                                    weight: 'bold',
                                                },
                                            }
                                        }
                                    }" />
                                </ul>
                                <div class="conclusao">
                                    <span>{{ relatorio.analise?.analise_animais_atropelados_especie }}</span>
                                </div>
                            </ul>
                        </div>
                        <div class="carousel-item">
                            <ul class="lista">
                                <li class="lista_content titulo_lista">12. Resultados</li>
                                <ul class="lista">
                                    <li class="lista_content subtitulo_lista">
                                        12.6 Gráfico com o número de animais atropelados por mês de campanha (s).
                                    </li>
                                    <BarChart :style="{ height: 300 }" :chart_data="{
                                        labels: relatorio.atropelados_mes?.map((item) => item.mes + ' - Campanha: ' + item.campanha),
                                        datasets: [{
                                            label: '',
                                            data: relatorio.atropelados_mes?.map((item) => parseInt(item.n_individuos))
                                        }]
                                    }" :chart_options="{
                                        responsive: true,
                                        plugins: {
                                            tooltip: {
                                                enabled: false
                                            },
                                            datalabels: {
                                                display: true,
                                                color: 'white',
                                                align: 'top',
                                                font: {
                                                    weight: 'bold',
                                                },
                                            }
                                        }
                                    }" />
                                </ul>
                                <div class="conclusao">
                                    <span>{{ relatorio.analise?.analise_animais_atropelados_mes }}</span>
                                </div>
                            </ul>
                        </div>
                        <div class="carousel-item">
                            <ul class="lista">
                                <li class="lista_content titulo_lista">13. Outras análises</li>
                                <ul class="lista" v-for="(i, key) in relatorio.outras_analises">
                                    <li class="lista_content subtitulo_lista">
                                        13.{{ key + 1 }} <span>{{ i.analise }}</span>
                                    </li>
                                    <div class="row">
                                        <div class="col-6">
                                            <img :src="'/atropelamento-fauna/arquivo/analise/' + i.id"
                                                class="w-100 h-100" />
                                        </div>
                                    </div>
                                </ul>
                            </ul>
                        </div>
                        <div class="carousel-item">
                            <ul class="lista">
                                <li class="lista_content titulo_lista">14. Registro fotográfico</li>
                                <ul class="lista row">
                                    <div class="grupo_cards col-4" v-for="(i, key) in relatorio.registro_rotografico">
                                        <div class="card_relatorio">
                                            <div class="card_r">
                                                <div class="info">
                                                    <div class="info_relatorio">Familia:</div>
                                                    <div class="valor_relatorio">{{ i.familia }}</div>
                                                </div>
                                                <div class="border_bottom"></div>
                                                <div class="info">
                                                    <div class="info_relatorio">Espécie:</div>
                                                    <div class="valor_relatorio">{{ i.especie }}</div>
                                                </div>
                                                <div class="border_bottom"></div>
                                                <div class="info">
                                                    <div class="info_relatorio">Nome comum:</div>
                                                    <div class="valor_relatorio">{{ i.nome_comum }}</div>
                                                </div>
                                                <div class="border_bottom"></div>
                                                <div class="info">
                                                    <div class="info_relatorio">KM:</div>
                                                    <div class="valor_relatorio">{{ i.km }}</div>
                                                </div>
                                                <div class="border_bottom"></div>
                                                <div class="info">
                                                    <div class="info_relatorio">Status de conservação:</div>
                                                    <div class="valor_relatorio">{{ i.familia }}</div>
                                                </div>
                                                <div class="border_bottom"></div>
                                                <div class="info">
                                                    <img :src="'/arquivo/registro/fotografico/' + i.id" width="100%"
                                                        alt="" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </ul>
                            </ul>
                        </div>
                        <div class="carousel-item">
                            <ul class="lista">
                                <li class="lista_content titulo_lista">15. Conclusão</li>
                                <div class="conclusao">
                                    <span>{{ relatorio.conclusao }}</span>
                                </div>
                            </ul>
                        </div>
                        <div class="carousel-item">
                            <ul class="lista">
                                <li class="lista_content titulo_lista">16. Anexos</li>
                                <ul class="lista">
                                    <li class="lista_content subtitulo_lista">
                                        <table id="table-parametros" class="table_relatorio col-12">
                                            <thead>
                                                <tr>
                                                    <th class="thead_relatorio"><span>Arquivos</span></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="i in relatorio.anexos">
                                                    <td class="tbody_relatorio">
                                                        <a href="javascript:void(0);">
                                                            {{ i.nome_arquivo }}
                                                        </a>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </li>
                                </ul>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </template>
        <template #footer>
            <button @click="modalRef.getBsModal().hide()" type="button" class="btn btn-secondary">Fechar</button>
        </template>
    </Modal>
</template>

<style scoped>
/* ----- GLOBAIS */
.global {
    font-family: 'Quicksand', sans-serif;
    color: #000 !important;
}

ul.lista {
    padding: 0;
}

.lista {
    list-style: none;
    margin-bottom: 20px;
}

.lista_content {
    text-transform: uppercase;
    padding-bottom: 20px;
}

.titulo_lista {
    text-transform: uppercase;
    font-weight: bold;
}

.subtitulo_lista {
    text-transform: uppercase;
    text-decoration: underline;
}

/* ----- CAPA */
.capa {
    height: 960px;
    text-align: center;
    position: relative;
}

.subtitulo,
.titulo,
.footer_capa {
    width: 80%;
    text-align: center;
    position: absolute;
    left: 0;
    right: 0;
    margin: 0 auto;
}

.nome_contratada {
    padding-bottom: 20px;
    color: rgba(0, 0, 0, 0.6);
}

.nome_contratada,
.rodovia,
.trecho {
    font-size: 18px;
    font-weight: normal;
}

.titulo {
    top: 450px;
}

.nome_titulo {
    font-size: 32px;
    font-weight: normal;
}

.footer_capa {
    bottom: 50px;
    font-size: 14px;
}

/* ----- EXECUCAO */

/* ----- PONTOS E PARAMETROS */

.grupo_cards {
    display: grid;
    grid-template-columns: 1fr 1fr;
}

.card_relatorio {
    width: 320px;
    float: left;
    padding-right: 20px;
    padding-bottom: 20px;
}

.card_r {
    padding: 20px;
    border-radius: 6px;
    border: 1px solid #e0e6ed;
    text-transform: uppercase;
    display: grid;
    font-size: 10px;
}

.table_relatorio {
    margin-bottom: 20px;
}

.info_relatorio,
.valor_relatorio {
    width: 50%;
}

.info_relatorio {
    float: left;
    font-weight: bold;
    text-align: left;
}

.valor_relatorio {
    float: right;
    text-align: right;
}

.border_bottom {
    border-bottom: 1px solid #e0e6ed;
    margin-top: 5px;
    margin-bottom: 5px;
}

.thead_relatorio {
    max-width: 97px;
    font-size: 10px;
    font-weight: bold;
    padding: 5px;
    background-color: rgba(234, 241, 255, 0.74);
    border: 1px solid #000;
    text-align: center;
}

.thead_relatorio span {
    display: block;
}

.tbody_relatorio {
    font-size: 10px;
    text-align: center;
    border: 1px solid #000;
    text-transform: initial;
}

.default {
    background-color: #fafafa;
    border: 1px solid #000;
}

.footer {
    font-size: 12px;
    width: 50%;
    text-transform: none;
}

.indice {
    margin-bottom: 5px;
}

/* ----- CAMPANHAS */
.info_campanha,
.info_ponto,
.fotos_coleta {
    padding-bottom: 20px;
}

.block_img {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
}

.figure {
    width: 210px;
    text-align: center;
    float: left;
    padding-right: 20px;
}

.img {
    width: 200px;
    border: 1px solid #000;
}

.img>img {
    width: 100%;
}

/* ----- RESULTADO */
.img_parametro {
    padding-bottom: 20px;
    width: 310px;
}

.img_parametro .img {
    width: 300px;
}

.analise {
    padding-bottom: 20px;
}

.analise>span {
    font-weight: bold;
}

.dados_result {
    padding-bottom: 20px;
}

/* ----- ANEXOS */
.anexos {
    height: 960px;
    text-align: center;
    position: relative;
}

.titulo_anexos {
    top: 530px;
}
</style>
