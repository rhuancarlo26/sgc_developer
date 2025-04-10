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
                    <td class="text-center">{{ $item->formacao }}</td>
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
                    <td class="text-center">{{ $item->codigo->descricao ?? '' }}</td>
                    <td class="text-center">{{ $item->codigo->codigo ?? '' }}</td>
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
                    <td class="text-center">{{ $item->descricao }}</td>
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
                    <td class="text-center">{{ $item->licenca->numero_licenca ?? '' }}</td>
                    <td class="text-center">{{ $item->condicionante->numero_condicionante ?? '' }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="full-page-div">
    <h1>7. Campanhas</h1>
    <table class="table">
        <thead>
        <tr>
            <th class="text-center">ID</th>
            <th class="text-center">N° ABIO</th>
            <th class="text-center">Período</th>
            <th class="text-center">Data inicial</th>
            <th class="text-center">Data final</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($relatorio->resultado->campanhas as $item)
            <tr>
                <td class="text-center">{{ $item->id }}</td>
                <td class="text-center">
                    @foreach ($item->abios as $abio)

                    @endforeach
                </td>
                <td class="text-center">{{$item->periodo}}</td>
                <td class="text-center">{{\Carbon\Carbon::parse($item->data_inicial)->format('d/m/Y')}}</td>
                <td class="text-center">{{\Carbon\Carbon::parse($item->data_final)->format('d/m/Y')}}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>

<div class="full-page-div">
    <h1>8. Sobre o relatório</h1>
    <div>
        <span>{{$relatorio->sobre_relatorio}}</span>
    </div>
</div>

<div class="full-page-div">
    <h1>9. Tabela com a relação de ABIO's emitidas para o empreendimento</h1>
    <div class="mb-4">
        <table class="table table-container">
            <thead>
            <tr>
                <th class="text-center">Tipo</th>
                <th class="text-center">N° licença</th>
                <th class="text-center">Empreendimento</th>
                <th class="text-center">Emissor</th>
                <th class="text-center">Data emissão</th>
                <th class="text-center">Vencimento</th>
                <th class="text-center">Responsável</th>
                <th class="text-center">Processo DNIT</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($relatorio->resultado->campanhas as $campanha)
                @foreach ($campanha->abios as $abio)
                    <tr>
                        <td class="text-center">{{ $abio->abio->licenca->tipo_rel->sigla ?? 'N/A' }}</td>
                        <td class="text-center">{{ $abio->abio->licenca->numero_licenca ?? 'N/A' }}</td>
                        <td class="text-center">{{ $abio->abio->licenca->empreendimento ?? 'N/A' }}</td>
                        <td class="text-center">{{ $abio->abio->licenca->emissor ?? 'N/A' }}</td>
                        <td class="text-center">{{ isset($abio->abio->licenca->data_emissao) ? \Carbon\Carbon::parse($abio->abio->licenca->data_emissao)->format('d/m/Y') : 'N/A' }}</td>
                        <td class="text-center">{{ isset($abio->abio->licenca->vencimento) ? \Carbon\Carbon::parse($abio->abio->licenca->vencimento)->format('d/m/Y') : 'N/A' }}</td>
                        <td class="text-center">{{ $abio->abio->licenca->fiscal ?? 'N/A' }}</td>
                        <td class="text-center">{{ $abio->abio->licenca->processo_dnit ?? 'N/A' }}</td>
                    </tr>
                @endforeach
            @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="full-page-div">
    <h1>10. Dados das passagens de fauna</h1>
    <div class="mb-4">
        <table class="table table-container">
            <thead>
            <tr>
                <th class="text-center">ID passagem</th>
                <th class="text-center">Rodovia</th>
                <th class="text-center">KM</th>
                <th class="text-center">UF</th>
                <th class="text-center">Tipo</th>
                <th class="text-center">Classificação</th>
                <th class="text-center">Dimensões</th>
                <th class="text-center">Nome</th>
                <th class="text-center">Data cadastro</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($servico->passagem_fauna_passagens as $passagem)
                <tr>
                    <td class="text-center">{{ $passagem->id }}</td>
                    <td class="text-center">{{ $passagem->rodovia ?? 'N/A' }}</td>
                    <td class="text-center">{{ $passagem->km ?? 'N/A' }}</td>
                    <td class="text-center">{{ $passagem->uf ?? 'N/A' }}</td>
                    <td class="text-center">{{ $passagem->tipo_de_estrutura ?? 'N/A' }}</td>
                    <td class="text-center">{{ $passagem->classificacao ?? 'N/A' }}</td>
                    <td class="text-center">{{ $passagem->dimensoes ?? 'N/A' }}</td>
                    <td class="text-center">{{ $passagem->nome ?? 'N/A' }}</td>
                    <td class="text-center">{{ isset($passagem->created_at) ? \Carbon\Carbon::parse($passagem->created_at)->format('d/m/Y') : 'N/A' }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="full-page-div">
    @php
        $numTotalIndividuos = 0;
        foreach ($relatorio->resultado->campanhas as $campanha) {
            foreach ($campanha->registros as $registro) {
                $numTotalIndividuos += $registro->n_individuos ?? 0;
            }
        }
    @endphp

    <h1>11. Resultados</h1>
    <h3>11.1 Tabela com o registro identificados das espécies nas passagens de fauna</h3>
    <div class="mb-4">
        <table class="table table-container">
            <thead>
            <tr>
                <th class="text-center" rowspan="2">Espécie</th>
                <th class="text-center" rowspan="2">Nome comum</th>
                <th class="text-center" rowspan="2">N° indivíduos</th>
                <th class="text-center" rowspan="2">Frequência (%)</th>
                <th class="text-center" colspan="2">Status conservação</th>
            </tr>
            <tr>
                <th class="text-center">Espécie</th>
                <th class="text-center">Nome comum</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($relatorio->resultado->campanhas as $campanha)
                @foreach ($campanha->registros as $registro)
                    <tr>
                        <td class="text-center">{{ $registro->especie ?? 'N/A' }}</td>
                        <td class="text-center">{{ $registro->nome_comum ?? 'N/A' }}</td>
                        <td class="text-center">{{ $registro->n_individuos ?? 'N/A' }}</td>
                        <td class="text-center">
                            @if(isset($registro->n_individuos) && $numTotalIndividuos > 0)
                                {{ number_format(($registro->n_individuos * 100) / $numTotalIndividuos, 4) }}%
                            @else
                                N/A
                            @endif
                        </td>
                        <td class="text-center">
                            {{ $registro->status_iunc->sigla ?? '' }} - {{ $registro->status_iunc->nome ?? 'N/A' }}
                        </td>
                        <td class="text-center">
                            {{ $registro->status_federal->sigla ?? '' }}
                            - {{ $registro->status_federal->nome ?? 'N/A' }}
                        </td>
                    </tr>
                @endforeach
            @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="full-page-div">
    <h1>11. Resultados</h1>
    <h3>11.2 Gráfico com o número de animais registrados por classe</h3>

    @if ($analises['graf_reg_classe'])
        <div class="mb-4">
            <img class="full-page-image" src="{{ $analises['graf_reg_classe'] }}" alt="Gráfico">
            <div>
                <span><strong>Análise: </strong>{{ $relatorio->resultado->analise->registros_classe ?? '' }}</span>
            </div>
        </div>
    @endif
</div>

<div class="full-page-div">
    <h1>11. Resultados</h1>
    <h3>11.3 Gráfico com o número de animais registrados por campanha</h3>

    @if ($analises['graf_reg_campanha'])
        <div class="mb-4">
            <img class="full-page-image" src="{{ $analises['graf_reg_campanha'] }}" alt="Gráfico">
            <div>
                <span><strong>Análise: </strong>{{ $relatorio->resultado->analise->registros_campanha ?? '' }}</span>
            </div>
        </div>
    @endif
</div>

<div class="full-page-div">
    <h1>11. Resultados</h1>
    <h3>11.4 Gráfico com o número de animais registrados por tipo</h3>

    @if ($analises['graf_reg_tipo'])
        <div class="mb-4">
            <img class="full-page-image" src="{{ $analises['graf_reg_tipo'] }}" alt="Gráfico">
            <div>
                <span><strong>Análise: </strong>{{ $relatorio->resultado->analise->registros_tipo ?? '' }}</span>
            </div>
        </div>
    @endif
</div>

<div class="full-page-div">
    <h1>11. Resultados</h1>
    <h3>11.5 Gráfico com o número de animais registrados por passagem</h3>

    @if ($analises['graf_reg_passagem'])
        <div class="mb-4">
            <img class="full-page-image" src="{{ $analises['graf_reg_passagem'] }}" alt="Gráfico">
            <div>
                <span><strong>Análise: </strong>{{ $relatorio->resultado->analise->registros_passagem ?? '' }}</span>
            </div>
        </div>
    @endif
</div>

<div class="full-page-div">
    <h1>11. Resultados</h1>
    <h3>11.6 Gráfico com o número de animais registrados por espécie</h3>

    @if ($analises['graf_reg_especie'])
        <div class="mb-4">
            <img class="full-page-image" src="{{ $analises['graf_reg_especie'] }}" alt="Gráfico">
            <div>
                <span><strong>Análise: </strong>{{ $relatorio->resultado->analise->registros_especie ?? '' }}</span>
            </div>
        </div>
    @endif
</div>

<div class="full-page-div">
    <h1>12. Outras analises</h1>

    @foreach ($outrasAnalises as $key => $item)
        <h3>11.{{ $key + 1 }}. {{ $item->nome }}</h3>
        <div class="mb-4">
            <img class="full-page-image" src="{{ $item->imagem }}" alt="Gráfico">
            <div>
                <span><strong>Análise: </strong>{{ $item->analise }}</span>
            </div>
        </div>
    @endforeach
</div>
