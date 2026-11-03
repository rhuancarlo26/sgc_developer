<?php

return [
    // Prazos com data-limite dentro desta janela aparecem como "A vencer"
    'janela_a_vencer_dias' => (int) env('SGC_LICENCAS_JANELA_A_VENCER', 30),

    // Antecedência padrão para requerer renovação (condicionante usual das AOs do Ibama)
    'antecedencia_renovacao_padrao' => 120,

    // Dias antes da data-limite em que um alerta é enviado (um por marco); "vencido" é enviado uma vez
    'marcos_alerta' => [90, 60, 30, 15, 7, 0],

    // Prazos vencidos há mais tempo que isso não geram alerta "vencido" (evita disparos na carga de dados históricos)
    'alerta_vencidos_ate_dias' => 30,

    // Destinatários quando a licença não tem responsáveis cadastrados (separados por vírgula)
    'emails_padrao' => array_filter(array_map('trim', explode(',', (string) env('SGC_LICENCAS_EMAILS_PADRAO', '')))),

    // Disco privado dos arquivos (licenças e comprovantes de cumprimento)
    'disco' => env('SGC_LICENCAS_DISCO', 'local'),
];
