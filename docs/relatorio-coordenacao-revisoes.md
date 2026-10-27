# Relatorio de coordenacao: tabelas e revisoes

Mapeamento conferido nos models, controllers e services em 14/10/2026. Nao houve consulta nem alteracao do banco existente. Colunas abaixo sao as utilizadas pelo codigo; nao representam uma inspecao completa do schema ou de suas constraints.

| Tabela | Model | Papel e campos utilizados |
| --- | --- | --- |
| contratos | Contrato | Contrato ao qual pertence o relatorio; id referenciado como contrato_id. |
| sgc_relatorio_coordenacao | SgcRelatorioCoordenacao | Estado atual dos topicos. contrato_id, relatorio_num, id_item, nome_topico, periodo, status, aprovado, versao, caminho, data_atualizacao e timestamps. Um relatorio possui varias linhas, uma por topico. |
| sgc_historico_relatorios | SgcHistoricoRelatorio | Retrato dos topicos ao devolver para revisao. contrato_id, relatorio_num, id_item, versao, nome_topico, periodo, status, aprovado, caminho e timestamps. versao indica a revisao arquivada. |
| sgc_upload | SgcRelatorioUpload | Arquivos de cada topico/revisao. id, contrato_id, num_relatorio, item_id, versao, nome, tipo, caminho, created_at, updated_at. E a fonte efetiva para abrir e baixar arquivos. |
| sgc_comentario | SgcComentario | Marcadores de comentarios no documento. id, contrato_id, item_id, posicao_x, posicao_y. |
| sgc_comentarios | SgcComentarios | Mensagens dos marcadores. comentario_id aponta para sgc_comentario.id; item_id, relatorio_num, comentario e demais metadados enviados pelo componente Comment. O fluxo atual nao separa comentarios por revisao. |
| sgc_status_relatorio | SgcStatusRelatorio | Cadastro auxiliar declarado pelos models, com id_status na relacao. O fluxo investigado grava textos diretamente em sgc_relatorio_coordenacao.status; nao usa esse cadastro para calcular revisoes. |
| relatorio_upload | SgcDocx | Tabela usada pelo endpoint legado VisualizarDocxController. Nao e a fonte do fluxo corrigido, que usa sgc_upload. |

## Correspondencias

- sgc_relatorio_coordenacao.relatorio_num = sgc_historico_relatorios.relatorio_num = sgc_upload.num_relatorio, sempre combinados com contrato_id.
- sgc_relatorio_coordenacao.id_item = sgc_historico_relatorios.id_item = sgc_upload.item_id. O id primario da linha nao e o numero do topico.
- Arquivo especifico: contrato_id + num_relatorio + item_id + versao.
- O conteudo binario fica no filesystem, normalmente storage/app/public/Relatorio/Sgc. caminho registra a localizacao relativa, nao o conteudo.

## Como a revisao funciona

Sem historico, a revisao atual e 0. Ao reprovar, o sistema arquiva os topicos na revisao atual e copia apenas o anexo escolhido dessa revisao para a seguinte. O upload substitui o registro escolhido apenas na revisao atual. Um topico sem arquivo pode receber seu primeiro anexo diretamente na revisao atual.

A revisao atual e max(sgc_historico_relatorios.versao) + 1 para o contrato e relatorio, ou 0 quando nao ha historico. A coluna versao dos topicos atuais nao e incrementada pelo fluxo existente; nao deve ser usada isoladamente para descobrir a revisao atual.

AnexosRelatorioService centraliza a consulta. Dentro da revisao exata, o registro mais recentemente atualizado prevalece; em empate, o maior id. Visualizacao, upload, download, PDF consolidado e copia usam essa prioridade. O historico filtra contrato e revisao, e o modal transmite a revisao selecionada.

## Dados antigos

Nenhum registro duplicado foi apagado. A leitura aplica a prioridade acima, e novas revisoes copiam apenas um arquivo por topico. Nao ha reconstrucao automatica da autoria ou da sequencia real de uploads: se duplicatas tiverem metadados inconsistentes, sera necessaria auditoria especifica antes de qualquer limpeza. Os testes usam SQLite em memoria e arquivos de teste isolados; nenhuma migration foi executada.

## Validacao

- PHP: tests/Unit/RelatorioRevisoesTest.php e tests/Unit/RelatorioPeriodoTest.php.
- Frontend: node --test tests/Frontend/relatorioDocumentos.test.cjs.
- Cenarios: revisoes 0 ate 5; preservacao das anteriores; duplicatas antigas; primeiro upload tardio; contrato/revisao no historico; mesma selecao no DOCX e download; ausencia de arquivo na revisao solicitada sem fallback para arquivo antigo.

## Auditoria da aprovacao (14/10/2026)

Novas aprovacoes registram a transicao de status de cada topico na tabela existente change_logs: user_id (usuario autenticado), table_name=sgc_relatorio_coordenacao, record_id (id da linha do topico), field=status, old_value, new_value=Relatorio Aprovado (texto com acentos no banco), created_at e updated_at. O relacionamento aprovacao seleciona o ultimo registro dessa transicao, com o usuario em users.id/name. Cliques repetidos em um relatorio ja aprovado nao substituem autoria/data.

A listagem e a tela do relatorio exibem nome e instante de aprovacao desse registro. Relatorios antigos sem auditoria nao recebem dados retroativos. O formato visual das datas e brasileiro, com fuso America/Sao_Paulo. Nao foram criadas tabelas/colunas nem executadas migrations no banco existente.

## Correcao manual de periodos antigos

O perfil Master corresponde a Super Admin no controle de acesso existente. Apenas esse perfil ve o icone de edicao na listagem e pode enviar PATCH a sgc.contratada.relatorio.periodo.update. O FormRequest valida o perfil no backend, exige datas validas e rejeita data final anterior a inicial.

A correcao atualiza periodo dos topicos atuais e dos historicos do mesmo contrato/numero, em transacao. Status, anexos, autoria/data da aprovacao e timestamps originais sao preservados. Cada valor efetivamente alterado gera registro em change_logs com usuario autenticado, tabela, id, field=periodo, old_value e new_value. Nenhum periodo antigo e corrigido automaticamente. Nao ha alteracao de schema.
