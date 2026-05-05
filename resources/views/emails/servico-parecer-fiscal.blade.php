@php
$tipoNome = data_get($servico, 'tipo.nome', 'Não informado');
$temaNome = data_get($servico, 'tema.nome_tema', 'Não informado');
$servicoNome = data_get($servico, 'servico', 'Não informado');

$statusAprovacao = (int) data_get($servico, 'status_aprovacao');

$aprovado = $statusAprovacao === 3;
$reprovado = $statusAprovacao === 4;

$statusTexto = $aprovado
? 'Serviço aprovado'
: ($reprovado ? 'Serviço reprovado' : 'Status não informado');

$statusCor = $aprovado
? '#16a34a'
: ($reprovado ? '#dc2626' : '#64748b');

$statusBg = $aprovado
? '#ecfdf5'
: ($reprovado ? '#fef2f2' : '#f8fafc');

$parecerFinal = data_get($servico, 'parecer.parecer', 'Nenhum parecer informado.');

$dataParecer = data_get($servico, 'parecer.created_at')
? \Carbon\Carbon::parse(data_get($servico, 'parecer.created_at'))->format('d/m/Y H:i')
: null;
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
                            <div style="margin-top:26px;">
                                <h3 style="color:#2563eb; margin-bottom:12px;">
                                    Parecer final do fiscal
                                </h3>

                                @if($dataParecer)
                                <p style="margin:0 0 8px 0; color:#64748b; font-size:13px;">
                                    Emitido em: {{ $dataParecer }}
                                </p>
                                @endif

                                <div style="padding:16px; background:#f8fafc; border-left:4px solid #4facfe; border-radius:6px;">
                                    {!! nl2br(e($parecerFinal)) !!}
                                </div>
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:32px; color:#334155; font-size:14px; line-height:1.6;">

                            <p style="margin-top:0;">Olá,</p>

                            <p>
                                O serviço abaixo recebeu o parecer final da fiscalização.
                            </p>

                            <div style="margin:22px 0; padding:16px; background:{{ $statusBg }}; border-left:5px solid {{ $statusCor }}; border-radius:8px;">
                                <div style="font-size:16px; font-weight:bold; color:{{ $statusCor }};">
                                    {{ $statusTexto }}
                                </div>
                            </div>

                            <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:20px; border-collapse:collapse;">
                                <tr>
                                    <td style="padding:10px; background:#f8fafc; font-weight:bold;">Serviço</td>
                                    <td style="padding:10px; background:#f8fafc;"><a href="{{ config('app.url') }}/contratos/contratada/{{ $servico->contrato->id }}/servicos" style="color:#4facfe; text-decoration:none;">{{ $servicoNome }}</a></td>
                                    
                                </tr>
                                <tr>
                                    <td style="padding:10px; background:#f8fafc; font-weight:bold; width:180px;">Tipo do serviço</td>
                                    <td style="padding:10px; background:#f8fafc;">{{ $tipoNome }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:10px; font-weight:bold;">Tema</td>
                                    <td style="padding:10px;">{{ $temaNome }}</td>
                                </tr>
                            </table>

                            <div style="margin-top:26px;">
                                <h3 style="color:#2563eb; margin-bottom:12px;">
                                    Parecer final do fiscal
                                </h3>

                                <div style="padding:16px; background:#f8fafc; border-left:4px solid #4facfe; border-radius:6px;">
                                    {!! nl2br(e($parecerFinal)) !!}
                                </div>
                            </div>

                            <p style="margin-top:28px;">
                                Acesse o sistema para consultar os detalhes completos do serviço.
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