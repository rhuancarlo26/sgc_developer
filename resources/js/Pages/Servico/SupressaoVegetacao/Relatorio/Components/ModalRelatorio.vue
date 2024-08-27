<script setup>
import {ref} from "vue";
import Modal from "@/Components/Modal.vue";
import {dateTimeFormat} from "@/Utils/DateTimeUtils.js";
import Table from "@/Components/Table.vue";
import axios from "axios";
import BarChart from "@/Components/BarChart.vue";

const props = defineProps({
    contrato: {type: Object},
})

const modalRef = ref();
const relatorio = ref({});
const abrirModal = async (item) => {
    const {data} = await axios.get(route('contratos.contratada.servicos.supressao-vegetacao.relatorio.get-relatorio'), {
        params: {
            id: item.id,
            contrato_id: props.contrato.id,
            fk_servico: item.fk_servico,
            fk_resultado: item.fk_resultado,
        }
    })

    relatorio.value = {
        nome: item.nome_relatorio,
        data_relatorio: item.created_at,
        conclusao: item.conclusao,
        sobre_relatorio: item.sobre_relatorio,
        ...data
    }
    modalRef.value.getBsModal().show();

    const {data: supressaoData} = await axios.get(route('contratos.contratada.servicos.supressao-vegetacao.resultado.supressao-analise', {
        servico: item.fk_servico,
        resultado: item.fk_resultado,
    }))

    const {data: pilhaData} = await axios.get(route('contratos.contratada.servicos.supressao-vegetacao.resultado.pilha-analise', {
        servico: item.fk_servico,
        resultado: item.fk_resultado,
    }))

    const {data: destincacaoData} = await axios.get(route('contratos.contratada.servicos.supressao-vegetacao.resultado.destinacao-analise', {
        servico: item.fk_servico,
        resultado: item.fk_resultado,
    }))

    relatorio.value = {
        ...relatorio.value,
        ...{
            ...supressaoData,
            cortes: supressaoData.cortes,
            supressao: supressaoData.supressao,
            resumo_supressao: supressaoData.resumo,
            percentual_supressao: supressaoData.percentual,
        },
        ...{
            ...pilhaData,
            pilhas: pilhaData.pilhas,
            pilhas_protegidas: pilhaData.pilhaProtegida,
            resumo_pilhas: pilhaData.resumo,
            volume_pilhas: pilhaData.volume,
        },
        ...{
            ...destincacaoData,
            destinacao: destincacaoData.destinacao,
            volume_destinado: destincacaoData.volumeDestinado,
            volume_estocado: destincacaoData.volumeEstocado,
            percent_volume_destinado: destincacaoData.percentTotalDestinado,
        },
    }
}

defineExpose({abrirModal});
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
                                    <li class="titulo_lista">1. Introdução</li>
                                    <li class="titulo_lista"><h6 class="">{{ relatorio.servico.introducao }}</h6></li>
                                    <li class="titulo_lista">2. Justificativa</li>
                                    <li class="titulo_lista"><h6 class="">{{ relatorio.servico.justificativa }}</h6>
                                    </li>
                                    <li class="titulo_lista">3. Objetivos</li>
                                    <li class="titulo_lista"><h6 class="">{{ relatorio.servico.objetivos }}</h6></li>
                                    <li class="titulo_lista">4. Metodologia</li>
                                    <li class="titulo_lista"><h6 class="">{{ relatorio.servico.metodologia }}</h6></li>
                                    <li class="titulo_lista">5. Público Alvo</li>
                                    <li class="titulo_lista"><h6 class="">{{ relatorio.servico.publico_alvo }}</h6></li>
                                </ul>
                            </div>
                        </div>
                        <div class="carousel-item">
                            <div class="execucao global">
                                <ul class="lista">
                                    <li class=" titulo_lista">6. Vinculos</li>
                                    <ul class="lista">
                                        <li class="subtitulo_lista">6.1 RECURSOS HUMANOS</li>
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
                            <div class="pontos_parametros global">
                                <ul class="lista table_relatorio">
                                    <li class="lista titulo_lista">7. Sobre o relatório</li>
                                    <li class="conclusao d-block">
                                        <span>{{ relatorio.sobre_relatorio }}</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="carousel-item">
                            <div class="pontos_parametros global">
                                <ul class="lista table_relatorio">
                                    <li class="lista titulo_lista">8. Tabela com a relação de autorizações de supressão
                                        de vegetação emitidas para o empreendimento.
                                    </li>
                                    <ul class="lista">
                                        <li>

                                            <table id="table-parametros" class="table_relatorio col-12">
                                                <thead>
                                                <tr>
                                                    <th class="thead_relatorio">Número ASV</th>
                                                    <th class="thead_relatorio">Data de emissão</th>
                                                    <th class="thead_relatorio">Data da validade</th>
                                                    <th class="thead_relatorio">Volume (m³)</th>
                                                    <th class="thead_relatorio">Área em App</th>
                                                    <th class="thead_relatorio">Área fora de App</th>
                                                    <th class="thead_relatorio">Área Total</th>
                                                </tr>
                                                </thead>

                                                <tbody>
                                                <tr v-for="i in relatorio.asvs">
                                                    <td class="tbody_relatorio">{{ i.numero_licenca }}</td>
                                                    <td class="tbody_relatorio">{{
                                                            dateTimeFormat(i.data_emissao)
                                                        }}
                                                    </td>
                                                    <td class="tbody_relatorio">{{ dateTimeFormat(i.vencimento) }}</td>
                                                    <td class="tbody_relatorio">{{ i.in_app }}</td>
                                                    <td class="tbody_relatorio">{{ i.in_app }}</td>
                                                    <td class="tbody_relatorio">{{ i.out_app }}</td>
                                                    <td class="tbody_relatorio">{{ i.area_ha }}</td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </li>
                                    </ul>
                                    <li class="lista titulo_lista">9. Mapa com a localização das Autorizações de
                                        Supressão de Vegetação.
                                    </li>
                                    <ul class="lista">
                                    </ul>
                                </ul>
                            </div>
                        </div>
                        <div class="carousel-item">
                            <div class="pontos_parametros global">
                                <ul class="lista table_relatorio">
                                    <li class="lista titulo_lista">10. Tabela com o Plano de Supressão de Vegetação</li>
                                    <ul class="lista">
                                        <table id="table-parametros" class="table_relatorio col-12">
                                            <thead>
                                            <tr>
                                                <th class="thead_relatorio">Código</th>
                                                <th class="thead_relatorio">Data inicial</th>
                                                <th class="thead_relatorio">Data final</th>
                                                <th class="thead_relatorio">Área APP(ha)</th>
                                                <th class="thead_relatorio">Área fora APP(ha)</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr v-for="i in relatorio.plano_supressao">
                                                <td class="tbody_relatorio">{{ i.chave }}</td>
                                                <td class="tbody_relatorio">{{ dateTimeFormat(i.dt_inicial) }}</td>
                                                <td class="tbody_relatorio">{{ dateTimeFormat(i.dt_final) }}</td>
                                                <td class="tbody_relatorio">{{ i.area_em_app }}</td>
                                                <td class="tbody_relatorio">{{ i.area_fora_app }}</td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </ul>
                                    <li class="lista titulo_lista">11. Mapa com a localização do plano de Supressão do
                                        cadastrado no empreendimento
                                    </li>
                                    <ul class="lista">
                                    </ul>
                                </ul>
                            </div>
                        </div>
                        <div class="carousel-item">
                            <div class="resultados global">
                                <ul class="lista table_relatorio">
                                    <li class="lista titulo_lista">12. Tabela referente aos pátios de estocagens
                                        cadastrados no empreendimento
                                    </li>
                                    <ul class="lista">
                                        <table id="table-parametros" class="table_relatorio col-12">
                                            <thead>
                                            <tr>
                                                <th class="thead_relatorio">Código</th>
                                                <th class="thead_relatorio">Data do cadastro</th>
                                                <th class="thead_relatorio">Nº ASV</th>
                                                <th class="thead_relatorio">Tipo de pátio</th>
                                                <th class="thead_relatorio">Observação</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr v-for="i in relatorio.patio_estocagem">
                                                <td class="tbody_relatorio">{{ i.chave }}</td>
                                                <td class="tbody_relatorio">{{ i.dt_cadastroF }}</td>
                                                <td class="tbody_relatorio">{{ i.numero_licenca }}</td>
                                                <td class="tbody_relatorio">{{ i.tipo_patio }}</td>
                                                <td class="tbody_relatorio">{{ i.observacao }}</td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </ul>
                                    <li class="lista titulo_lista">
                                        13. Mapa da localização do(s) pátio(s) de estocagem cadastrados no
                                        empreendimento
                                    </li>
                                    <ul class="lista">
                                    </ul>
                                </ul>
                            </div>
                        </div>
                        <div class="carousel-item">
                            <div class="resultados global">
                                <ul class="lista">
                                    <li class="titulo_lista">14. Resultados</li>
                                    <ul class="lista pt-3">
                                        <li class="titulo_lista">14.1 Supressão</li>
                                    </ul>
                                    <ul class="lista">
                                        <li class="subtitulo_lista">14.1.1 Tabela com a relação das áreas suprimidas no
                                            empreendimentos
                                        </li>
                                        <ul class="lista">
                                            <table class="table_relatorio col-12" style="width:100% !important;">
                                                <thead>
                                                <tr>
                                                    <th class="thead_relatorio">Código</th>
                                                    <th class="thead_relatorio">Data inicial</th>
                                                    <th class="thead_relatorio">Data final</th>
                                                    <th class="thead_relatorio">Nº ASV</th>
                                                    <th class="thead_relatorio">Bioma</th>
                                                    <th class="thead_relatorio">Área em APP <small>(ha)</small></th>
                                                    <th class="thead_relatorio">Área Fora APP <small>(ha)</small></th>
                                                    <th class="thead_relatorio">Área Total <small>(ha)</small></th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr v-for="i in relatorio.supressao">
                                                    <td class="tbody_relatorio">{{ i.chave }}</td>
                                                    <td class="tbody_relatorio">{{ dateTimeFormat(i.dt_inicial) }}</td>
                                                    <td class="tbody_relatorio">{{ dateTimeFormat(i.dt_final) }}</td>
                                                    <td class="tbody_relatorio">{{ i.licenca_id }}</td>
                                                    <td class="tbody_relatorio">{{ i.bioma?.nome }}</td>
                                                    <td class="tbody_relatorio">{{ i.area_em_app }}</td>
                                                    <td class="tbody_relatorio">{{ i.area_fora_app }}</td>
                                                    <td class="tbody_relatorio">{{ i.area_total }}</td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </ul>
                                        <li class="subtitulo_lista">14.1.2 Tabela resumo das áreas suprimidas</li>
                                        <ul class="lista">
                                            <table class="table_relatorio col-12" style="width:100% !important;">
                                                <thead>
                                                <tr>
                                                    <th class="thead_relatorio">Descrição</th>
                                                    <th class="thead_relatorio">Área em APP <small>(ha)</small></th>
                                                    <th class="thead_relatorio">Área Fora APP <small>(ha)</small></th>
                                                    <th class="thead_relatorio">Área Total <small>(ha) Soma</small></th>
                                                </tr>
                                                </thead>

                                                <tbody>
                                                <tr v-for="i in relatorio.resumo">
                                                    <td class="tbody_relatorio">{{ i.desc }}</td>
                                                    <td class="tbody_relatorio">{{ i.area_app }}</td>
                                                    <td class="tbody_relatorio">{{ i.area_fora }}</td>
                                                    <td class="tbody_relatorio">{{ i.total }}</td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </ul>
                                        <div>
                                                <BarChart
                                                    :style="{ height: '200px' }"
                                                    :chart_data="{
                                                labels: relatorio.areaMes?.map((item) => item.mes),
                                                datasets: [{
                                                    label: 'Área total (ha)',
                                                    backgroundColor: '#008ffb',
                                                    data: relatorio.areaMes?.map((item) => parseFloat(item.area_total))
                                                }]
                                            }"
                                                    :chart_options="{ responsive: true }"
                                                />
                                        </div>
                                        <ul class="lista">
                                            <div class="conclusao">
                                                <span>{{ relatorio.resultado.analise_supressao_vegetacao }}</span>
                                            </div>
                                        </ul>
                                    </ul>
                                </ul>

                            </div>
                        </div>
                        <div class="carousel-item">
                            <div class="resultados global">
                                <ul class="lista">
                                    <li class="subtitulo_lista">14.1.3 Tabela com a relação espécies ameaçadas/protegidas suprimidas.</li>
                                    <ul class="lista mt-3">
                                        <table class="table_relatorio col-12" style="width:100% !important;">
                                            <thead>
                                            <tr>
                                                <th class="thead_relatorio">Espécies ameaçadas/protegidas</th>
                                                <th class="thead_relatorio">Nome popular</th>
                                                <th class="thead_relatorio">Legislação</th>
                                                <th class="thead_relatorio">Nº de Indivíduos</th>
                                                <th class="thead_relatorio">Compensação</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr v-for="i in relatorio.cortes">
                                                <td class="tbody_relatorio">{{i.nome}}</td>
                                                <td class="tbody_relatorio">{{i.nome_popular}}</td>
                                                <td class="tbody_relatorio">{{i.legislacao}}</td>
                                                <td class="tbody_relatorio">{{i.n_individuos}}</td>
                                                <td class="tbody_relatorio">{{i.n_compensacao}}</td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </ul>
                                    <ul class="lista">
                                        <div class="conclusao">
                                            <span>{{relatorio.resultado.analise_supressao_especies_protegida}}</span>
                                        </div>
                                    </ul>
                                </ul>

                            </div>
                        </div>
                        <div class="carousel-item">
                            <div class="resultados global">
                                <ul class="lista">
                                    <li class="subtitulo_lista">14.1.4 Mapa da localização com a localização das áreas suprimidas.</li>
                                </ul>
                            </div>
                        </div>
                        <div class="carousel-item">
                            <div class="resultados global">
                                <ul class="lista">
                                    <li class="titulo_lista">14.2 Pilhas</li>
                                    <ul class="lista">
                                        <li class="subtitulo_lista">14.2.1 Tabela com a relação de pilhas cadastradas no empreendimento  </li>
                                        <ul class="lista">
                                            <table class="table_relatorio col-12" style="width:100% !important;">
                                                <thead>
                                                <tr>
                                                    <th class="thead_relatorio">Código</th>
                                                    <th class="thead_relatorio">Área de supressão</th>
                                                    <th class="thead_relatorio">Data cadastro</th>
                                                    <th class="thead_relatorio">Nº ASV</th>
                                                    <th class="thead_relatorio">Tipo de pilha</th>
                                                    <th class="thead_relatorio">Tipo de produto</th>
                                                    <th class="thead_relatorio">Nome científico</th>
                                                    <!--                    <th class="thead_relatorio">Nº indivíduos</th>-->
                                                    <th class="thead_relatorio">Volume (m³)</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr v-for="i in relatorio.pilhas">
                                                    <td class="tbody_relatorio">{{i.chave}}</td>
                                                    <td class="tbody_relatorio">{{i.area_supressao}}</td>
                                                    <td class="tbody_relatorio">{{formatarData(i.created_at)}}</td>
                                                    <td class="tbody_relatorio">{{i.licenca_id}}</td>
                                                    <td class="tbody_relatorio">{{i.pilha}}</td>
                                                    <td class="tbody_relatorio">{{i.produto}}</td>
                                                    <td class="tbody_relatorio">{{i.corte}}</td>
                                                    <!--                    <td class="tbody_relatorio">{{i.qtd_cortes}}</td>-->
                                                    <td class="tbody_relatorio">{{i.volume}}</td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </ul>
                                        <li class="subtitulo_lista">14.2.2 Mapa da localização das pilhas cadastradas </li>
                                        <ul class="lista">

                                        </ul>
                                        <ul class="lista">
                                            <div class="conclusao">
                                                <span>{{relatorio.resultado.analise_pilhas_cadastradas}}</span>
                                            </div>
                                        </ul>
                                    </ul>
                                </ul>

                            </div>
                        </div>
                        <div class="carousel-item">
                            <div class="resultados global">
                                <ul class="lista">
                                    <ul class="lista">
                                        <li class="subtitulo_lista">14.2.3 Tabela de cadastro de pilha espécies protegidas</li>
                                        <ul class="lista">
                                            <table class="table_relatorio col-12" style="width:100% !important;">
                                                <thead>
                                                <tr>
                                                    <th class="thead_relatorio">Código</th>
                                                    <th class="thead_relatorio">Área de supressão</th>
                                                    <th class="thead_relatorio">Data cadastro</th>
                                                    <th class="thead_relatorio">Nº ASV</th>
                                                    <th class="thead_relatorio">Tipo de pilha</th>
                                                    <th class="thead_relatorio">Tipo de produto</th>
                                                    <th class="thead_relatorio">Nome científico</th>
                                                    <th class="thead_relatorio">Nº indivíduos</th>
                                                    <th class="thead_relatorio">Volume (m³)</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr v-for="i in relatorio.pilhas_protegidas">
                                                    <td class="tbody_relatorio">{{i.chave}}</td>
                                                    <td class="tbody_relatorio">{{i.area_supressao}}</td>
                                                    <td class="tbody_relatorio">{{formatarData(i.created_at)}}</td>
                                                    <td class="tbody_relatorio">{{i.licenca_id}}</td>
                                                    <td class="tbody_relatorio">{{i.pilha}}</td>
                                                    <td class="tbody_relatorio">{{i.produto}}</td>
                                                    <td class="tbody_relatorio">{{i.corte}}</td>
                                                    <td class="tbody_relatorio">{{i.qtd_cortes}}</td>
                                                    <td class="tbody_relatorio">{{i.volume}}</td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </ul>
                                        <ul class="lista">
                                            <div class="conclusao">
                                                <span>{{relatorio.resultado.analise_pilhas_especie_protetigas}}</span>
                                            </div>
                                        </ul>
                                    </ul>
                                </ul>

                            </div>
                        </div>
                        <div class="carousel-item">
                            <div class="resultados global">
                                <ul class="lista">
                                    <li class="titulo_lista">14.3 Destinação</li>
                                    <ul class="lista">
                                        <li class="subtitulo_lista">14.3.1 Tabela com a destinação de pilhas de madeiras</li>
                                        <ul class="lista">
                                            <table class="table_relatorio col-12" style="width:100% !important;">
                                                <thead>
                                                <tr>
                                                    <th class="thead_relatorio">Código</th>
                                                    <th class="thead_relatorio">Data do envio</th>
                                                    <th class="thead_relatorio">Pilhas</th>
                                                    <th class="thead_relatorio">Destinatário</th>
                                                    <th class="thead_relatorio">Uso da madeira</th>
                                                    <th class="thead_relatorio">Volume <small>(m³)</small></th>
                                                    <th class="thead_relatorio">Observação</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr v-for="i in relatorio.destinacao">
                                                    <td class="tbody_relatorio">{{i.chave}}</td>
                                                    <td class="tbody_relatorio">{{formatarData(i.dt_envio)}}</td>
                                                    <td class="tbody_relatorio">{{i.pilhas}}</td>
                                                    <td class="tbody_relatorio">{{i.destinatario}}</td>
                                                    <td class="tbody_relatorio">{{i.uso_da_madeira}}</td>
                                                    <td class="tbody_relatorio">{{i.volume}}</td>
                                                    <td class="tbody_relatorio">{{i.observacao}}</td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </ul>
                                        <ul class="lista">
                                            <div class="conclusao">
                                                <span>{{relatorio.resultado.analise_destinacao_pilhas}}</span>
                                            </div>
                                        </ul>
                                    </ul>
                                </ul>
                            </div>
                        </div>
                        <div class="carousel-item">
                            <div class="conclusao global">
                                <ul class="lista">
                                    <li class="titulo_lista">15. Conclusão</li>
                                    <div class="conclusao">
                                        <span>{{relatorio.conclusao}}</span>
                                    </div>
                                </ul>
                            </div>
                        </div>
                        <div class="carousel-item">
                            <div class="anexos global">
                                <ul class="lista">
                                    <li class="titulo_lista">16. Anexos</li>
                                    <ul class="lista">
                                        <li class="subtitulo_lista">
                                            <table id="table-parametros" class="table_relatorio col-12">
                                                <thead>
                                                <tr>
                                                    <th class="thead_relatorio"><span>Arquivos</span></th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr v-for="i in relatorio.anexos">
                                                    <td class="tbody_relatorio">
                                                        <a :href="item.caminho" target="_blank" >
                                                            {{i.nome_arquivo}}
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

.img > img {
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

.analise > span {
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
