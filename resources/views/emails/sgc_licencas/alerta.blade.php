<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title>Alertas de prazos</title>
</head>
<body style="margin:0;padding:24px;background:#f4f6fa;font-family:Arial,Helvetica,sans-serif;color:#182433;">
<div style="max-width:720px;margin:0 auto;background:#ffffff;border-radius:6px;padding:24px;">
    <h2 style="margin:0 0 8px;font-size:18px;">Gestão de prazos de licenças e autorizações</h2>
    <p style="margin:0 0 16px;font-size:14px;color:#525f7f;">
        Os prazos abaixo atingiram um marco de alerta. Registre o cumprimento ou a prorrogação no SGC.
    </p>
    <table style="width:100%;border-collapse:collapse;font-size:13px;">
        <thead>
        <tr style="background:#eef1f6;text-align:left;">
            <th style="padding:8px;">Licença</th>
            <th style="padding:8px;">Prazo</th>
            <th style="padding:8px;">Data-limite</th>
            <th style="padding:8px;">Situação</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($prazos as $prazo)
            <tr style="border-bottom:1px solid #e6e9ef;">
                <td style="padding:8px;vertical-align:top;">
                    <a href="{{ route('sgc.licencas.show', $prazo['licenca_id']) }}">{{ $prazo['licenca']['rotulo'] ?? 'Licença' }}</a>
                    @if (!empty($prazo['licenca']['uf']))
                        <br><span style="color:#525f7f;">{{ $prazo['licenca']['uf'] }} · {{ $prazo['licenca']['orgao_emissor'] }}</span>
                    @endif
                </td>
                <td style="padding:8px;vertical-align:top;">{{ $prazo['descricao'] }}</td>
                <td style="padding:8px;vertical-align:top;white-space:nowrap;">
                    {{ \Carbon\Carbon::parse($prazo['data_limite'])->format('d/m/Y') }}
                </td>
                <td style="padding:8px;vertical-align:top;white-space:nowrap;font-weight:bold;color:{{ $prazo['dias_restantes'] < 0 ? '#d63939' : '#b07d00' }};">
                    @if ($prazo['dias_restantes'] < 0)
                        Vencido há {{ abs($prazo['dias_restantes']) }} dia(s)
                    @elseif ($prazo['dias_restantes'] === 0)
                        Vence hoje
                    @else
                        Faltam {{ $prazo['dias_restantes'] }} dia(s)
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <p style="margin:16px 0 0;">
        <a href="{{ route('sgc.licencas.painel') }}" style="display:inline-block;background:#0054a6;color:#ffffff;padding:8px 16px;border-radius:4px;text-decoration:none;font-size:14px;">
            Abrir painel de prazos
        </a>
    </p>
</div>
</body>
</html>
