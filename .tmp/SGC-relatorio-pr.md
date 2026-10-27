# SGC: corrigir revisoes, periodos e auditoria dos relatorios

A partir da REV 2, a copia de todos os anexos anteriores podia gerar duplicatas e abrir um documento antigo. A selecao e a copia passam a respeitar contrato, relatorio, topico e revisao, usando a mesma prioridade no upload, visualizacao e download. O historico respeita a revisao selecionada e os anexos I, II e III continuam sendo downloads.

Inclui periodo obrigatorio na criacao, sugestao bimestral, correcao manual de periodos pelo Super Admin e auditoria de novas aprovacoes e correcoes na tabela change_logs existente. A listagem e o historico seguem o padrao visual do SGC, com datas no horario de Brasilia. Nenhum registro legado foi apagado ou corrigido automaticamente, e nao ha migrations ou alteracoes de schema.

## Validacao

- 18 testes PHP, 100 assercoes, no clone com as versoes do composer.lock de producao e SQLite em memoria.
- 8 testes frontend e build Vite completo com package-lock.json de producao.
- Sintaxe dos 16 arquivos PHP e compilacao dos componentes Vue conferidas.
- Diff limitado a 21 arquivos de codigo do relatorio; sem alteracoes em Produtos/Fauna ou nos arquivos de dependencias.
- Build concluiu com avisos de tamanho de chunks e de imagem do layout de login fora deste escopo.

Os testes e a documentacao foram usados localmente e nao integram este commit.

## Observacoes

Aprovacoes antigas sem auditoria nao recebem autor ou data presumidos. Duplicatas antigas de anexos sao mantidas; a consulta prioriza updated_at e, em empate, id. O menu e as rotas compartilhados de producao foram preservados, acrescentando apenas a identificacao da secao ativa e o endpoint de correcao de periodo.
