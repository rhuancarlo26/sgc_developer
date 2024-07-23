<div>
    <div class="d-flex flex-column justify-content-between min-vh-100">
        <div class="text-center">
            <div class="mb-4">
                <span><strong>{{ $contrato->contratada }}</strong></span>
            </div>
            <div class="d-flex justify-content-center">
                <div class="px-2">
                    @if ($contrato->ufs)
                        @foreach (explode(',', $contrato->ufs) as $item)
                            <span class="badge bg-warning text-white m-1">
                                {{ $item }}
                            </span>
                        @endforeach
                    @endif
                </div>
                <div class="px-2">
                    @if ($contrato->brs)
                        @foreach (explode(',', $contrato->brs) as $item)
                            <span class="badge bg-warning text-white m-1">
                                {{ $item }}
                            </span>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
        <div class="text-center">
            <h1>{{ $relatorio->nome }}</h1>
        </div>
        <div class="text-center">
            <span><strong>{{ date('d-m-Y', strtotime($relatorio->created_at)) }}</strong></span>
        </div>
    </div>
</div>

<div>
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

<div>
    <h1>6. Vinculos</h1>
    <h4>6.1. Recursos humanos</h4>
    <div class="card">
        <div class="table-responsive">
            <table class="table card-table table-bordered">
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
    </div>
    <div>
        <h4>6.2. Veículos</h4>
        <div class="card">
            <div class="table-responsive">
                <table class="table card-table table-bordered">
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
        </div>
    </div>
    <div>
        <h4>6.3. Equipamentos</h4>
        <div class="card">
            <div class="table-responsive">
                <table class="table card-table table-bordered">
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
        </div>
    </div>
    <div>
        <h4>6.4. Licenças</h4>
        <div class="card">
            <div class="table-responsive">
                <table class="table card-table table-bordered">
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
                                <td class="text-center">{{ $item->condicionante->numero_condicionante }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div>
    <h1>7. Pontos</h1>
    <div class="card">
        <div class="table-responsive">
            <table class="table card-table table-bordered">
                <thead>
                    <tr>
                        <th class="text-center">Cod. ponto</th>
                        <th class="text-center">Pt. coleta</th>
                        <th class="text-center">Latitude</th>
                        <th class="text-center">Longitude</th>
                        <th class="text-center">Classificação</th>
                        <th class="text-center">Classe</th>
                        <th class="text-center">Tipo de ambiente</th>
                        <th class="text-center">UF</th>
                        <th class="text-center">Municipio</th>
                        <th class="text-center">Bacia hidrografica</th>
                        <th class="text-center">Km rodovia</th>
                        <th class="text-center">Estaca</th>
                        <th class="text-center">Observações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($pontosVinculados as $item)
                        <tr>
                            <td class="text-center">{{ $item->id }}</td>
                            <td class="text-center">{{ $item->nomepontocoleta }}</td>
                            <td class="text-center">{{ $item->lat_x }}</td>
                            <td class="text-center">{{ $item->long_y }}</td>
                            <td class="text-center">{{ $item->classificacao }}</td>
                            <td class="text-center">{{ $item->classe }}</td>
                            <td class="text-center">{{ $item->tipoambiente }}</td>
                            <td class="text-center">{{ $item->uf }}</td>
                            <td class="text-center">{{ $item->municipio }}</td>
                            <td class="text-center">{{ $item->baciahidrografica }}</td>
                            <td class="text-center">{{ $item->km_rodovia }}</td>
                            <td class="text-center">{{ $item->estaca }}</td>
                            <td class="text-center">{{ $item->observacoes }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<div>
    <h1>8. Parâmetros</h1>
    <div>
        <div class="card">
            <div class="table-responsive">
                <table class="table card-table table-bordered">
                    <thead>
                        <tr>
                            <th rowspan="3">Parâmetro</th>
                            <th rowspan="3">Unidade</th>
                            <th colspan="4">Limites - resoluções CONAMA N° 357/2005</th>
                        </tr>
                        <tr>
                            <th colspan="4">Água doce</th>
                        </tr>
                        <tr>
                            <th>Classe 1</th>
                            <th>Classe 2</th>
                            <th>Classe 3</th>
                            <th>Classe 4</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($parametrosVinculados as $item)
                            <tr>
                                <td>{{ $item->nome }}</td>
                                <td>{{ $item->unidade }}</td>
                                <td>{{ $item->classe_1 }}</td>
                                <td>{{ $item->classe_2 }}</td>
                                <td>{{ $item->classe_3 }}</td>
                                <td>{{ $item->classe_4 }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="footer">
            <div class="indice"> * V.A. = Virtualmente Ausente</div>
            <div class="indice">
                ** Limite é binário, ou seja, presença ou não de óleos e graxas.
                Apesar disso, na etapa de execução, a contratada deverá inserir o
                valor medido pelo laboratório. Podemos considerar sem limite
                definido.
            </div>
            <div class="indice"> *** São 2 limites: superior e inferior</div>
            <div class="indice"> **** O limite é condicionado ao "TipoAmbiente": Lótico ou Lêntico.</div>
            <div class="indice"> ***** Definidos de acordo com o pH da amostra.</div>
        </div>
        <div class="card">
            <div class="table-responsive">
                <table class="table card-table table-bordered">
                    <thead>
                        <tr>
                            <th class="thead_relatorio" colspan="2"><span>PARÂMETROS DO IQA (CETESB/SP)</span></th>
                            <th class="thead_relatorio"> PESO (w)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="tbody_relatorio">1</td>
                            <td class="tbody_relatorio">OXIGÊNIO DISSOLVIDO - OD</td>
                            <td class="tbody_relatorio">0,17</td>
                        </tr>
                        <tr>
                            <td class="tbody_relatorio">2</td>
                            <td class="tbody_relatorio">COLIFORMES TERMOTOLERANTES</td>
                            <td class="tbody_relatorio">0,15</td>
                        </tr>
                        <tr>
                            <td class="tbody_relatorio">3</td>
                            <td class="tbody_relatorio">pH***</td>
                            <td class="tbody_relatorio">0,12</td>
                        </tr>
                        <tr>
                            <td class="tbody_relatorio">4</td>
                            <td class="tbody_relatorio">DEMANDA BIOQUÍMICA DE OXIGÊNIO - DBO</td>
                            <td class="tbody_relatorio">0,1</td>
                        </tr>
                        <tr>
                            <td class="tbody_relatorio">5</td>
                            <td class="tbody_relatorio">TEMPERATURA - TEMP.</td>
                            <td class="tbody_relatorio">0,1</td>
                        </tr>
                        <tr>
                            <td class="tbody_relatorio">6</td>
                            <td class="tbody_relatorio">NITROGÊNIO TOTAL</td>
                            <td class="tbody_relatorio">0,1</td>
                        </tr>
                        <tr>
                            <td class="tbody_relatorio">7</td>
                            <td class="tbody_relatorio">FÓSFORO TOTAL - P****</td>
                            <td class="tbody_relatorio">0,1</td>
                        </tr>
                        <tr>
                            <td class="tbody_relatorio">8</td>
                            <td class="tbody_relatorio">TURBIDEZ - TURB</td>
                            <td class="tbody_relatorio">0,08</td>
                        </tr>
                        <tr>
                            <td class="tbody_relatorio">9</td>
                            <td class="tbody_relatorio">SÓLIDOS DISSOLVIDOS TOTAIS - SDT</td>
                            <td class="tbody_relatorio">0,08</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div>
    <h1>Campanhas</h1>
    <div class="card">
        <div class="table-responsive">
            <table class="table card-table table-bordered">
                <thead>
                    <tr>
                        <th class="text-center">Nome da campanha</th>
                        <th class="text-center">Data de início</th>
                        <th class="text-center">Data de fim</th>
                        <th class="text-center">Lista de pontos</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($relatorio->resultado->campanhas as $item)
                        <tr>
                            <td class="text-center">{{ $item->nome }}</td>
                            <td class="text-center">{{ date('d-m-Y', strtotime($item->data_inicio)) }}</td>
                            <td class="text-center">{{ date('d-m-Y', strtotime($item->data_termino)) }}</td>
                            <td>
                                @foreach ($campanhaPontos as $item)
                                    <span class="badge bg-warning text-white m-1">
                                        {{ $item }}
                                    </span>
                                @endforeach
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
