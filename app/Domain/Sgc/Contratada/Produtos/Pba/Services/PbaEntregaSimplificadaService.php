<?php

namespace App\Domain\Sgc\Contratada\Produtos\Pba\Services;

use App\Models\SgcPbaCampanha;
use App\Models\SgcEntregaSimplificada;
use App\Models\SgcEntregaSimplificadaAnalise;
use App\Models\SgcEntregaSimplificadaArquivo;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PbaEntregaSimplificadaService
{
    public function criar(array $dados): SgcPbaCampanha
    {
        return DB::transaction(function () use ($dados) {
            $status = $dados['enviar_analise'] ? 'Em análise' : 'Em elaboração';
            $campanha = SgcPbaCampanha::create([
                'id_contrato' => $dados['contrato_id'], 'cod_emp' => $dados['cod_emp'],
                'id_campanha' => $dados['id_campanha'], 'sei_dnit' => $dados['sei_dnit'] ?? null,
                'subproduto' => $dados['subproduto'], 'status' => $status, 'versao_analise' => 1,
            ]);
            $entrega = SgcEntregaSimplificada::create([
                'id_contrato' => $campanha->id_contrato, 'produto_tipo' => 'pba',
                'entidade_tipo' => 'pba_campanha', 'entidade_id' => $campanha->id,
                'modelo_id' => $dados['modulo_id'] ?? null, 'status' => $status, 'versao_analise' => 1,
                ...$this->guardarPlanilha($dados['arquivo'] ?? null, $campanha->id),
            ]);
            $this->sincronizarArquivos($entrega, $dados);
            return $campanha;
        });
    }

    public function atualizar(SgcPbaCampanha $campanha, SgcEntregaSimplificada $entrega, array $dados): void
    {
        DB::transaction(function () use ($campanha, $entrega, $dados) {
            $status = $dados['enviar_analise'] ? 'Em análise' : 'Em elaboração';
            $campanha->update([
                'cod_emp' => $dados['cod_emp'], 'id_campanha' => $dados['id_campanha'],
                'sei_dnit' => $dados['sei_dnit'] ?? null, 'subproduto' => $dados['subproduto'], 'status' => $status,
            ]);
            $update = ['modelo_id' => $dados['modulo_id'] ?? null, 'status' => $status];
            if (!empty($dados['arquivo']) && $dados['arquivo'] instanceof UploadedFile) {
                $this->removerArquivo($entrega->planilha_caminho);
                $update += $this->guardarPlanilha($dados['arquivo'], $campanha->id);
            }
            $entrega->update($update);
            $this->sincronizarArquivos($entrega, $dados);
        });
    }

    public function registrarAnalise(SgcPbaCampanha $campanha, SgcEntregaSimplificada $entrega, string $status, ?string $observacoes, int $fiscalId): void
    {
        DB::transaction(function () use ($campanha, $entrega, $status, $observacoes, $fiscalId) {
            $campanha = SgcPbaCampanha::lockForUpdate()->findOrFail($campanha->id);
            $entrega = SgcEntregaSimplificada::lockForUpdate()->findOrFail($entrega->id);
            if ($campanha->status !== 'Em análise') throw new \LogicException('Campanha não está em análise.');
            $versao = $entrega->versao_analise;
            SgcEntregaSimplificadaAnalise::create([
                'entrega_simplificada_id' => $entrega->id, 'versao_analise' => $versao,
                'status' => $status, 'observacoes' => $observacoes, 'fiscal_id' => $fiscalId,
            ]);
            $aprovada = $status === 'Aprovada';
            $entrega->update($aprovada
                ? ['status' => 'Aprovada', 'aprovado_por' => $fiscalId, 'data_aprovacao' => Carbon::now()]
                : ['status' => 'Rejeitada', 'versao_analise' => $versao + 1]);
            $campanha->update($aprovada
                ? ['status' => 'Aprovada']
                : ['status' => 'Rejeitada', 'versao_analise' => $versao + 1]);
        });
    }

    private function guardarPlanilha(?UploadedFile $arquivo, int $campanhaId): array
    {
        if (!$arquivo) return ['planilha_nome' => '', 'planilha_caminho' => ''];
        $nome = $arquivo->getClientOriginalName();
        $caminho = $arquivo->storeAs("entrega_simplificada/pba/{$campanhaId}/planilhas", uniqid().'_'.$nome, 'public');
        $this->sincronizarArquivoPublico($caminho);
        return ['planilha_nome' => $nome, 'planilha_caminho' => $caminho];
    }

    private function sincronizarArquivos(SgcEntregaSimplificada $entrega, array $dados): void
    {
        foreach (['fotos_remover' => 'foto', 'anexos_remover' => 'anexo'] as $campo => $tipo) {
            $arquivos = SgcEntregaSimplificadaArquivo::where('entrega_simplificada_id', $entrega->id)
                ->where('tipo', $tipo)->whereIn('id', $dados[$campo] ?? [])->get();
            foreach ($arquivos as $arquivo) {
                $this->removerArquivo($arquivo->caminho_arquivo);
                $arquivo->delete();
            }
        }

        foreach ($dados['fotos_atualizadas'] ?? [] as $foto) {
            if (empty($foto['id'])) continue;

            SgcEntregaSimplificadaArquivo::where('entrega_simplificada_id', $entrega->id)
                ->where('tipo', 'foto')
                ->where('id', $foto['id'])
                ->update([
                    'latitude' => $foto['latitude'] ?? null,
                    'longitude' => $foto['longitude'] ?? null,
                    'descricao' => $foto['descricao'] ?? null,
                ]);
        }

        foreach (['fotos' => 'foto', 'anexos' => 'anexo'] as $campo => $tipo) {
            foreach ($dados[$campo] ?? [] as $item) {
                if (!empty($item['arquivo']) && $item['arquivo'] instanceof UploadedFile) {
                    $arquivo = $item['arquivo']; $nome = $arquivo->getClientOriginalName();
                    $caminho = $arquivo->storeAs("entrega_simplificada/pba/{$entrega->entidade_id}/{$campo}", uniqid().'_'.$nome, 'public');
                    $this->sincronizarArquivoPublico($caminho);
                    SgcEntregaSimplificadaArquivo::create([
                        'entrega_simplificada_id' => $entrega->id, 'tipo' => $tipo, 'nome_arquivo' => $nome,
                        'caminho_arquivo' => $caminho,
                        'mime_type' => $arquivo->getMimeType(), 'tamanho_bytes' => $arquivo->getSize(),
                        'descricao' => $item['descricao'] ?? null, 'latitude' => $item['latitude'] ?? null,
                        'longitude' => $item['longitude'] ?? null, 'data_captura' => $item['data_captura'] ?? null,
                    ]);
                }
            }
        }
    }

    private function removerArquivo(?string $caminho): void { if ($caminho) Storage::disk('public')->delete($caminho); }

    private function sincronizarArquivoPublico(string $caminho): void
    {
        $origem = storage_path('app/public/'.$caminho);
        $destino = public_path('storage/'.$caminho);
        if (!is_file($origem)) return;
        if (!is_dir(dirname($destino))) @mkdir(dirname($destino), 0777, true);
        @copy($origem, $destino);
    }
}


