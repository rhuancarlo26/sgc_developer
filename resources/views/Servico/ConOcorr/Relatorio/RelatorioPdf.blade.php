<style>
    @page {
        size: A4;
        margin: 0;
    }

    h1 {
        background-color: lightgrey;
    }

    h3 {
        background-color: #f2f2f2;
    }

    .full-page-div {
        width: 19cm;
        min-height: 27cm;
        box-sizing: border-box;
        padding: 1cm;
    }

    .mb-4 {
        margin-bottom: 1rem;
    }

    .full-page-div-column {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .full-page-image {
        width: 100%;
        height: auto;
    }

    .table {
        width: 100%;
        border-collapse: collapse;
    }

    .table th,
    .table td {
        border: 1px solid #000;
        padding: 8px;
        text-align: left;
    }

    .table th {
        background-color: #f2f2f2;
    }

    .table-container {
        font-size: 10px;
    }

    .table-sm {
        font-size: 8px;
    }

    .badge {
        display: inline-block;
        padding: 0.25em 0.4em;
        font-size: 75%;
        font-weight: 700;
        line-height: 1;
        text-align: center;
        white-space: nowrap;
        vertical-align: baseline;
        border-radius: 0.375rem;
    }

    .bg-warning {
        background-color: #ffc107;
    }

    .text-white {
        color: #fff;
    }

    .text-center {
        text-align: center;
    }

    .card {
        box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2);
    }

    .row {
        display: flex;
        flex-wrap: wrap;
    }
</style>

<div class="full-page-div full-page-div-column">
    <div>
        <div class="text-center">
            <span><strong>{{ $contrato->contratada }}</strong></span>
        </div>
        <div class="row text-center">
            <div>
                @if ($contrato->ufs)
                    @foreach (explode(',', $contrato->ufs) as $item)
                        <span class="badge bg-warning text-white">
                            {{ $item }}
                        </span>
                    @endforeach
                @endif
            </div>
            <div>
                @if ($contrato->brs)
                    @foreach (explode(',', $contrato->brs) as $item)
                        <span class="badge bg-warning text-white">
                            {{ $item }}
                        </span>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
    <div class="text-center">
        <h2>{{ $relatorio->nome }}</h2>
    </div>
    <div class="text-center">
        <span><strong>{{ date('d-m-Y', strtotime($relatorio->created_at)) }}</strong></span>
    </div>
</div>

<div class="full-page-div full-page-div-column">
    <div>
        <h1>1. Introdução</h1>
        <span>
            {{ $servico->introducao }}
        </span>
    </div>
    <div>
        <h1>2. Justificativa</h1>
        <span>
            {{ $servico->justificativa }}
        </span>
    </div>
    <div>
        <h1>3. Objetivos</h1>
        <span>
            {{ $servico->objetivo }}
        </span>
    </div>
    <div>
        <h1>4. Metodologias</h1>
        <span>
            {{ $servico->metodologia }}
        </span>
    </div>
    <div>
        <h1>5. Público alvo</h1>
        <span>
            {{ $servico->publico_alvo }}
        </span>
    </div>
</div>

<div class="full-page-div">
    <h1>6. Vinculos</h1>
    <div class="mb-4">
        <h3>6.1. Recursos humanos</h3>
        <table class="table">
            <thead>
            <tr>
                <th class="text-center">Nome do profissional</th>
                <th class="text-center">Profissão</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($servico->rhs as $item)
                <tr>
                    <td class="text-center">{{ $item->nome }}</td>
                    <td class="text-center">{{ $item->profissao }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div class="mb-4">
        <h3>6.2. Veículos</h3>
        <table class="table">
            <thead>
            <tr>
                <th class="text-center">Nome do veículo</th>
                <th class="text-center">Modelo</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($servico->veiculos as $item)
                <tr>
                    <td class="text-center">{{ $item->codigo->descricao }}</td>
                    <td class="text-center">{{ $item->codigo->codigo }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div class="mb-4">
        <h3>6.3. Equipamentos</h3>
        <table class="table">
            <thead>
            <tr>
                <th class="text-center">Nome do equipamento</th>
                <th class="text-center">Modelo</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($servico->equipamentos as $item)
                <tr>
                    <td class="text-center">{{ $item->nome }}</td>
                    <td class="text-center">{{ $item->modelo }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div>
        <h3>6.4. Licenças</h3>
        <table class="table">
            <thead>
            <tr>
                <th class="text-center">Número da licenca</th>
                <th class="text-center">Condicionante</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($servico->licencas_condicionantes as $item)
                <tr>
                    <td class="text-center">{{ $item->licenca->numero_licenca }}</td>
                    <td class="text-center">{{ $item->condicionante }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="full-page-div">
    <h1>7. Sobre o relatório</h1>
    <div>
        <span>
            {{ $relatorio->nome_relatorio }}
        </span>
    </div>
</div>

<div class="full-page-div">
    <h1>8. Tabela de empreendimentos</h1>
    <div>
        <table class="table">
            <thead>
            <tr>
                <th class="text-center">Tipo</th>
                <th class="text-center">N° licença</th>
                <th class="text-center">Empreendimento</th>
                <th class="text-center">Uf inicial</th>
                <th class="text-center">Uf final</th>
                <th class="text-center">Rodovia</th>
                <th class="text-center">Km inicial</th>
                <th class="text-center">Km final</th>
                <th class="text-center">Extensão</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($servico->licencas_condicionantes as $item)
                <tr>
                    <td class="text-center">{{ $item->licenca->tipo->silga ?? '' }}</td>
                    <td class="text-center">{{ $item->licenca->numero_licenca ?? '' }}</td>
                    <td class="text-center">{{ $item->licenca->empreendimento ?? '' }}</td>
                    <td class="text-center">
                        @if(!empty($item->licenca->iniciais))
                            @foreach(explode(',', $item->licenca->iniciais) as $uf)
                                <span class="badge bg-warning text-white m-1">
                                    {{ $uf }}
                                </span>
                            @endforeach
                        @endif
                    </td>
                    <td class="text-center">
                        @if(!empty($item->licenca->finais))
                            @foreach(explode(',', $item->licenca->finais) as $uf)
                                <span class="badge bg-warning text-white m-1">
                                    {{ $uf }}
                                </span>
                            @endforeach
                        @endif
                    </td>
                    <td class="text-center">
                        @if(!empty($item->licenca->brs))
                            @foreach(explode(',', $item->licenca->brs) as $br)
                                <span class="badge bg-warning text-white m-1">
                                    {{ $br }}
                                </span>
                            @endforeach
                        @endif
                    </td>
                    <td class="text-center">
                        @if(!empty($item->licenca->segmentos) && count($item->licenca->segmentos) > 0)
                            {{
                                min(array_map(function($segmento) {
                                    return $segmento['km_inicio'];
                                }, $item->licenca->segmentos->toArray()))
                            }}
                        @endif
                    </td>
                    <td class="text-center">
                        @if(!empty($item->licenca->segmentos) && count($item->licenca->segmentos) > 0)
                            {{
                                max(array_map(function($segmento) {
                                    return $segmento['km_fim'];
                                }, $item->licenca->segmentos->toArray()))
                            }}
                        @endif
                    </td>
                    <td class="text-center">{{$item->licenca->extensao ?? ''}}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="full-page-div">
    <h1>9. Dados dos lotes de obra</h1>
    <div>
        <table class="table table-container">
            <thead>
            <tr>
                <th class="text-center">ID lote</th>
                <th class="text-center">Nome</th>
                <th class="text-center">BR</th>
                <th class="text-center">UF</th>
                <th class="text-center">Km inicial</th>
                <th class="text-center">Km final</th>
                <th class="text-center">Construtora / Consórcio (lote de obra)</th>
                <th class="text-center">N° do contrato (lote de obra)</th>
                <th class="text-center">Situação (lote de obra)</th>
                <th class="text-center">Supervisora de obra</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($variaveis['lotes'] as $item)
                <tr>
                    <td class="text-center">{{$item->nome_id ?? ''}}</td>
                    <td class="text-center">{{$item->nome ?? ''}}</td>
                    <td class="text-center">{{$item->rodovia['rodovia'] ?? ''}}</td>
                    <td class="text-center">{{$item->uf['uf'] ?? ''}}</td>
                    <td class="text-center">{{$item->km_inicial ?? ''}}</td>
                    <td class="text-center">{{$item->km_final ?? ''}}</td>
                    <td class="text-center">{{$item->empresa ?? ''}}</td>
                    <td class="text-center">{{$item->num_contrato ?? ''}}</td>
                    <td class="text-center">{{$item->situacao_contrato ?? ''}}</td>
                    <td class="text-center">{{$item->supervisora_obras ?? ''}}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="full-page-div">
    <h1>10. Resultados</h1>
    <div class="mb-4">
        <h2>10.1 Tabela com os ROA's atendidos no período</h2>
        <table class="table table-container">
            <thead>
            <tr>
                <th class="text-center">ID ocorrência</th>
                <th class="text-center">Intensidade</th>
                <th class="text-center">Local ocorrência</th>
                <th class="text-center">Classificação ocorrência</th>
                <th class="text-center">Data da ocorrência</th>
                <th class="text-center">Lote</th>
                <th class="text-center">Empresa / Consórcio</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($variaveis['roasAtendidos'] as $item)
                <tr>
                    <td class="text-center">{{ $item->nome_id }}</td>
                    <td class="text-center">{{ $item->intensidade }}</td>
                    <td class="text-center">{{ $item->local }}</td>
                    <td class="text-center">{{ $item->classificacao }}</td>
                    <td class="text-center">{{ \Carbon\Carbon::parse($item->data_ocorrencia)->format('d/m/Y') }}</td>
                    <td class="text-center">{{ $item->lote->nome_id }}</td>
                    <td class="text-center">{{ $item->lote->empresa }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div class="mb-4">
        <h2>10.2 Tabela com os ROA's em aberto no período</h2>
        <table class="table table-container">
            <thead>
            <tr>
                <th class="text-center">ID ocorrência</th>
                <th class="text-center">Intensidade</th>
                <th class="text-center">Local ocorrência</th>
                <th class="text-center">Classificação ocorrência</th>
                <th class="text-center">Data da ocorrência</th>
                <th class="text-center">Lote</th>
                <th class="text-center">Empresa / Consórcio</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($variaveis['roasEmAberto'] as $item)
                <tr>
                    <td class="text-center">{{ $item->nome_id }}</td>
                    <td class="text-center">{{ $item->intensidade }}</td>
                    <td class="text-center">{{ $item->local }}</td>
                    <td class="text-center">{{ $item->classificacao }}</td>
                    <td class="text-center">{{ \Carbon\Carbon::parse($item->data_ocorrencia)->format('d/m/Y') }}</td>
                    <td class="text-center">{{ $item->lote->nome_id }}</td>
                    <td class="text-center">{{ $item->lote->empresa }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div class="mb-4">
        <h2>10.3 Tabela com os RNC's atendidos no período</h2>
        <table class="table table-container">
            <thead>
            <tr>
                <th class="text-center">ID ocorrência</th>
                <th class="text-center">Intensidade</th>
                <th class="text-center">Local ocorrência</th>
                <th class="text-center">Classificação ocorrência</th>
                <th class="text-center">Data da ocorrência</th>
                <th class="text-center">Lote</th>
                <th class="text-center">Empresa / Consórcio</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($variaveis['rncsAtendidos'] as $item)
                <tr>
                    <td class="text-center">{{ $item->nome_id }}</td>
                    <td class="text-center">{{ $item->intensidade }}</td>
                    <td class="text-center">{{ $item->local }}</td>
                    <td class="text-center">{{ $item->classificacao }}</td>
                    <td class="text-center">{{ \Carbon\Carbon::parse($item->data_ocorrencia)->format('d/m/Y') }}</td>
                    <td class="text-center">{{ $item->lote->nome_id }}</td>
                    <td class="text-center">{{ $item->lote->empresa }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div>
        <h2>10.4 Tabela com os RNC's em aberto no período</h2>
        <table class="table table-container">
            <thead>
            <tr>
                <th class="text-center">ID ocorrência</th>
                <th class="text-center">Intensidade</th>
                <th class="text-center">Local ocorrência</th>
                <th class="text-center">Classificação ocorrência</th>
                <th class="text-center">Data da ocorrência</th>
                <th class="text-center">Lote</th>
                <th class="text-center">Empresa / Consórcio</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($variaveis['rncsEmAberto'] as $item)
                <tr>
                    <td class="text-center">{{ $item->nome_id }}</td>
                    <td class="text-center">{{ $item->intensidade }}</td>
                    <td class="text-center">{{ $item->local }}</td>
                    <td class="text-center">{{ $item->classificacao }}</td>
                    <td class="text-center">{{ \Carbon\Carbon::parse($item->data_ocorrencia)->format('d/m/Y') }}</td>
                    <td class="text-center">{{ $item->lote->nome_id }}</td>
                    <td class="text-center">{{ $item->lote->empresa }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="full-page-div">
    <h1>10. Resultados</h1>
    <div class="mb-4">
        <h2>10.5 Gráfico da frequência de ocorrências por intensidade</h2>
        <hr>
        <img class="full-page-image" src="{{ $variaveis['analise']['graf_reg_intensidade_imagem'] }}" alt="Gráfico">
        <div>
            <span><strong>Análise: </strong>{{ $variaveis['analise']['ocorr_por_intensidade'] }}</span>
        </div>
    </div>
    <div class="mb-4">
        <h2>10.6 Gráfico do número de ocorrências por local</h2>
        <hr>
        <img class="full-page-image" src="{{ $variaveis['analise']['graf_reg_local_imagem'] }}" alt="Gráfico">
        <div>
            <span><strong>Análise: </strong>{{ $variaveis['analise']['ocorr_por_local'] }}</span>
        </div>
    </div>
    <div class="mb-4">
        <h2>10.7 Gráfico do número de ocorrências por classificação</h2>
        <hr>
        <img class="full-page-image" src="{{ $variaveis['analise']['graf_reg_classificacao_imagem'] }}" alt="Gráfico">
        <div>
            <span><strong>Análise: </strong>{{ $variaveis['analise']['ocorr_por_classificacao'] }}</span>
        </div>
    </div>
    <div class="mb-4">
        <h2>10.8 Gráfico do número de ocorrências por lote</h2>
        <hr>
        <img class="full-page-image" src="{{ $variaveis['analise']['graf_reg_lote_imagem'] }}" alt="Gráfico">
        <div>
            <span><strong>Análise: </strong>{{ $variaveis['analise']['ocorr_por_lote'] }}</span>
        </div>
    </div>
    <div>
        <h2>10.9 Tabela com os ACA's Gerados</h2>
        <table class="table table-sm">
            <thead>
            <tr>
                <th class="text-center">ID ACA</th>
                <th class="text-center">Data do ACA</th>
                <th class="text-center">Relação de RNC's</th>
                <th class="text-center">Local do RNC</th>
                <th class="text-center">Classificação do RNC</th>
                <th class="text-center">Lote</th>
                <th class="text-center">Empresa / Consórcio</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($variaveis['acas'] as $item)
                <tr>
                    <td class="text-center">{{ $item->nome_id ?? '' }}</td>
                    <td class="text-center">{{ \Carbon\Carbon::parse($item->data_aca)->format('d/m/Y') ?? '' }}</td>
                    <td class="text-center">
                        @if(!empty($item->rncs) && count($item->rncs) > 0)
                            @foreach($item->rncs as $rnc)
                                <span class="badge bg-warning text-white m-1">
                                    {{ $rnc->nome_id }}
                                </span>
                            @endforeach
                        @endif
                    </td>
                    <td class="text-center">
                        @if(!empty($item->rncs) && count($item->rncs) > 0)
                            @php
                                $locaisUnicos = $item->rncs->pluck('local')->unique();
                            @endphp

                            @foreach($locaisUnicos as $local)
                                <span class="badge bg-warning text-white m-1">
                                    {{ $local }}
                                </span>
                            @endforeach
                        @endif
                    </td>
                    <td class="text-center">
                        @if(!empty($item->rncs) && count($item->rncs) > 0)
                            @php
                                $classificacaoUnicos = $item->rncs->pluck('classificacao')->unique();
                            @endphp

                            @foreach($classificacaoUnicos as $local)
                                <span class="badge bg-warning text-white m-1">
                                    {{ $local }}
                                </span>
                            @endforeach
                        @endif
                    </td>
                    <td class="text-center">{{ $item->lote->nome_id ?? '' }}</td>
                    <td class="text-center">{{ $item->lote->empresa ?? '' }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="full-page-div">
    <h1>11. Outras analises</h1>

    @foreach ($outrasAnalises as $key => $item)
        <div class="mb-4">
            <h3>11.{{ $key + 1 }}. {{ $item->nome }}</h3>
            <img class="full-page-image" src="{{ $item->imagem }}" alt="Gráfico">
            <div>
                <span><strong>Análise: </strong>{{ $item->analise }}</span>
            </div>
        </div>
    @endforeach
</div>

<div class="full-page-div">
    <h1>12. Registro fotográfico</h1>
    <div>
        <table class="table table-container">
            <thead>
            <tr>
                <th class="text-center">ID</th>
                <th class="text-center">Data da ocorrência</th>
                <th class="text-center">Intensidade</th>
                <th class="text-center">Tipo</th>
                <th class="text-center">Local</th>
                <th class="text-center">Classificação</th>
                <th class="text-center">Corrigido</th>
                <th class="text-center">Imagens</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($variaveis['ocorrencias'] as $item)
                <tr>
                    <td class="text-center">{{$item->nome_id ?? ''}}</td>
                    <td class="text-center">{{\Carbon\Carbon::parse($item->data_ocorrencia)->format('d/m/Y')}}</td>
                    <td class="text-center">{{$item->intensidade ?? ''}}</td>
                    <td class="text-center">{{$item->tipo ?? ''}}</td>
                    <td class="text-center">{{$item->local ?? ''}}</td>
                    <td class="text-center">{{$item->classificacao ?? ''}}</td>
                    <td class="text-center">{{$item->vistorias[0]->corrigido ?? ''}}</td>
                    <td class="text-center">

                        @foreach($item->registros as $imagem)
                            @php
                                $extensao = pathinfo($imagem->nome, PATHINFO_EXTENSION);
                            @endphp
                            @if(in_array($extensao, ['png', 'jpeg']))
                                <img class="full-page-image" src="{{ $imagem->caminho_arquivo_imagem }}" alt="Gráfico">
                            @endif
                        @endforeach
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
