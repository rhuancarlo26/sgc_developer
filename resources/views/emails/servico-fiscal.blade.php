@php
    $tipoNome = data_get($servico, 'tipo.nome', 'Não informado');
    $temaNome = data_get($servico, 'tema.nome_tema', 'Não informado');

    $servicoNome = data_get($servico, 'servico', 'Não informado');
    $introducao = data_get($servico, 'introducao');
    $justificativa = data_get($servico, 'justificativa');
    $objetivos = data_get($servico, 'objetivos');
    $metodologia = data_get($servico, 'metodologia');
    $publicoAlvo = data_get($servico, 'publico_alvo');
    $especificacao = data_get($servico, 'especificacao');

    $statusAprovacao = data_get($servico, 'status_aprovacao', 'Não informado');

    $createdAt = data_get($servico, 'created_at')
        ? \Carbon\Carbon::parse(data_get($servico, 'created_at'))->format('d/m/Y H:i')
        : 'Não informado';

    $updatedAt = data_get($servico, 'updated_at')
        ? \Carbon\Carbon::parse(data_get($servico, 'updated_at'))->format('d/m/Y H:i')
        : 'Não informado';
@endphp

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
</head>

<body style="margin:0; padding:0; background-color:#f4f8fb; font-family:Arial, sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f8fb; padding:24px 0;">
    <tr>
        <td align="center">

            <table width="680" cellpadding="0" cellspacing="0"
                   style="background:#ffffff; border-radius:10px; overflow:hidden; box-shadow:0 3px 10px rgba(0,0,0,0.06);">

                <tr>
                    <td style="background:linear-gradient(90deg,#4facfe,#00c6ff); padding:24px; text-align:center; color:#ffffff;">
                        <div style="font-size:22px; font-weight:bold;">
                            Novo serviço enviado para fiscalização
                        </div>
                        <div style="font-size:13px; margin-top:6px;">
                            {{ config('app.name') }}
                        </div>
                    </td>
                </tr>

                <tr>
                    <td style="padding:32px; color:#334155; font-size:14px; line-height:1.6;">

                        <p style="margin-top:0;">
                            Olá,
                        </p>

                        <p>
                            Um novo serviço foi encaminhado para análise do fiscal. Confira abaixo as informações principais:
                        </p>

                        <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:20px; border-collapse:collapse;">
                            <tr>
                                <td style="padding:10px; background:#f8fafc; font-weight:bold; width:180px;">Tipo do serviço</td>
                                <td style="padding:10px; background:#f8fafc;"><a href="{{ config('app.url') }}/fiscal/{{ $servico->contrato->id }}/servicos/" style="color:#4facfe; text-decoration:none;">{{ $tipoNome }}</a></td>
                            </tr>

                            <tr>
                                <td style="padding:10px; font-weight:bold;">Tema</td>
                                <td style="padding:10px;">{{ $temaNome }}</td>
                            </tr>

                            <tr>
                                <td style="padding:10px; background:#f8fafc; font-weight:bold;">Serviço</td>
                                <td style="padding:10px; background:#f8fafc;">{{ $servicoNome }}</td>
                            </tr>

                            <tr>
                                <td style="padding:10px; font-weight:bold;">Status de aprovação</td>
                                <td style="padding:10px;">{{ $statusAprovacao }}</td>
                            </tr>

                            <tr>
                                <td style="padding:10px; background:#f8fafc; font-weight:bold;">Criado em</td>
                                <td style="padding:10px; background:#f8fafc;">{{ $createdAt }}</td>
                            </tr>

                            <tr>
                                <td style="padding:10px; font-weight:bold;">Atualizado em</td>
                                <td style="padding:10px;">{{ $updatedAt }}</td>
                            </tr>
                        </table>

                        <div style="margin-top:26px;">
                            <h3 style="color:#2563eb; margin-bottom:12px;">Informações do serviço</h3>

                            @if($introducao)
                                <div style="margin-bottom:16px;">
                                    <strong>Introdução</strong>
                                    <div style="margin-top:6px; padding:14px; background:#f8fafc; border-left:4px solid #4facfe; border-radius:6px;">
                                        {!! nl2br(e($introducao)) !!}
                                    </div>
                                </div>
                            @endif

                            @if($justificativa)
                                <div style="margin-bottom:16px;">
                                    <strong>Justificativa</strong>
                                    <div style="margin-top:6px; padding:14px; background:#f8fafc; border-left:4px solid #4facfe; border-radius:6px;">
                                        {!! nl2br(e($justificativa)) !!}
                                    </div>
                                </div>
                            @endif

                            @if($objetivos)
                                <div style="margin-bottom:16px;">
                                    <strong>Objetivos</strong>
                                    <div style="margin-top:6px; padding:14px; background:#f8fafc; border-left:4px solid #4facfe; border-radius:6px;">
                                        {!! nl2br(e($objetivos)) !!}
                                    </div>
                                </div>
                            @endif

                            @if($metodologia)
                                <div style="margin-bottom:16px;">
                                    <strong>Metodologia</strong>
                                    <div style="margin-top:6px; padding:14px; background:#f8fafc; border-left:4px solid #4facfe; border-radius:6px;">
                                        {!! nl2br(e($metodologia)) !!}
                                    </div>
                                </div>
                            @endif

                            @if($publicoAlvo)
                                <div style="margin-bottom:16px;">
                                    <strong>Público-alvo</strong>
                                    <div style="margin-top:6px; padding:14px; background:#f8fafc; border-left:4px solid #4facfe; border-radius:6px;">
                                        {!! nl2br(e($publicoAlvo)) !!}
                                    </div>
                                </div>
                            @endif

                            @if($especificacao)
                                <div style="margin-bottom:16px;">
                                    <strong>Especificação</strong>
                                    <div style="margin-top:6px; padding:14px; background:#f8fafc; border-left:4px solid #4facfe; border-radius:6px;">
                                        {!! nl2br(e($especificacao)) !!}
                                    </div>
                                </div>
                            @endif
                        </div>

                        <p style="margin-top:28px;">
                            Acesse o sistema para consultar os detalhes completos e realizar a fiscalização do serviço.
                        </p>

                    </td>
                </tr>

                <tr>
                    <td style="background:#f1f5f9; padding:16px; text-align:center; font-size:12px; color:#64748b;">
                        © {{ date('Y') }} - {{ config('app.name') }}<br>
                        Este é um e-mail automático, não responda.
                    </td>
                </tr>

            </table>

        </td>
    </tr>
</table>

</body>
</html>