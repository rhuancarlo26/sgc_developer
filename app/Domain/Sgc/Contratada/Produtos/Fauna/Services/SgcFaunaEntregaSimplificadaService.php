<?php

namespace App\Domain\Sgc\Contratada\Produtos\Fauna\Services;

use App\Models\SgcEntregaSimplificada;
use App\Models\SgcEntregaSimplificadaArquivo;
use App\Models\SgcEntregaSimplificadaAnalise;
use App\Models\SgcFaunaCampanha;
use App\Models\SgcFaunaEntregaSimplificadaPlanilha;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SgcFaunaEntregaSimplificadaService
{
    public function criar(array $data): SgcEntregaSimplificada
    {
        return DB::transaction(function () use ($data) {
            $campanha = SgcFaunaCampanha::create([
                'id_contrato' => $data['contrato_id'],
                'cod_emp' => $data['cod_emp'],
                'id_campanha' => $data['id_campanha'],
                'subproduto' => $data['subproduto'],
                'sei_dnit' => $data['sei_dnit'] ?? null,
                'data_ini' => $data['data_ini'] ?? null,
                'data_fim' => $data['data_fim'] ?? null,
                'modo_preenchimento' => 'simplificado',
                'status' => $data['enviar_analise'] ? 'Em análise' : 'Em elaboração',
                'etapa_atual' => 'entrega_simplificada',
                'versao_analise' => 1,
            ]);

            $entrega = SgcEntregaSimplificada::create([
                'id_contrato' => $campanha->id_contrato,
                'produto_tipo' => 'fauna',
                'entidade_tipo' => 'fauna_campanha',
                'entidade_id' => $campanha->id,
                'status' => $data['enviar_analise'] ? 'Em análise' : 'Em elaboração',
                'versao_analise' => 1,
            ]);

            $this->salvarPlanilhas($entrega, $campanha, $data['planilhas'] ?? []);

            if (!empty($data['fotos'])) {
                foreach ($data['fotos'] as $foto) {
                    if (!empty($foto['arquivo']) && $foto['arquivo'] instanceof UploadedFile) {
                        $this->salvarArquivo($entrega, $foto['arquivo'], 'foto', $foto['descricao'] ?? null, $foto['latitude'] ?? null, $foto['longitude'] ?? null, $foto['data_captura'] ?? null);
                    }
                }
            }

            $this->salvarAnexos($entrega, $data['anexos'] ?? []);

            return $entrega->load(['planilhasFauna.modulo', 'fotos', 'anexos', 'analises']);
        });
    }

    public function registrarAnalise(
        SgcEntregaSimplificada $entrega,
        string $status,
        ?string $observacoes,
        int $fiscalId
    ): SgcEntregaSimplificadaAnalise {
        return DB::transaction(function () use ($entrega, $status, $observacoes, $fiscalId) {
            $entrega = SgcEntregaSimplificada::query()
                ->lockForUpdate()
                ->findOrFail($entrega->id);

            $campanha = SgcFaunaCampanha::query()
                ->lockForUpdate()
                ->find($entrega->entidade_id);

            if (!$campanha || $campanha->status !== 'Em análise') {
                throw new \LogicException('A campanha não está disponível para análise.');
            }

            // Corrige campanhas reenviadas antes desta regra existir: elas ainda
            // podem estar em análise com uma decisão registrada na mesma versão.
            $versao = $entrega->versao_analise;
            if (SgcEntregaSimplificadaAnalise::where('entrega_simplificada_id', $entrega->id)
                ->where('versao_analise', $versao)
                ->exists()) {
                $versao = SgcEntregaSimplificadaAnalise::where('entrega_simplificada_id', $entrega->id)
                    ->max('versao_analise') + 1;

                $entrega->update(['versao_analise' => $versao]);
                $campanha->update(['versao_analise' => $versao]);
            }

            $analise = SgcEntregaSimplificadaAnalise::create([
                'entrega_simplificada_id' => $entrega->id,
                'versao_analise' => $versao,
                'status' => $status,
                'observacoes' => $observacoes,
                'fiscal_id' => $fiscalId,
            ]);

            $dados = ['status' => $status];
            if ($status === 'Aprovada') {
                $dados['aprovado_por'] = $fiscalId;
                $dados['data_aprovacao'] = Carbon::now();
            } else {
                $dados['versao_analise'] = $versao + 1;
            }
            $entrega->update($dados);

            $dadosCampanha = ['status' => $status === 'Aprovada' ? 'Aprovada' : 'Rejeitada'];
            if ($status === 'Rejeitada') {
                $dadosCampanha['versao_analise'] = $versao + 1;
            }
            $campanha->update($dadosCampanha);

            return $analise;
        });
    }

    public function atualizar(SgcFaunaCampanha $campanha, SgcEntregaSimplificada $entrega, array $data): SgcEntregaSimplificada
    {
        return DB::transaction(function () use ($campanha, $entrega, $data) {
            $status = $data['enviar_analise'] ? 'Em análise' : 'Em elaboração';

            $campanha->update([
                'cod_emp' => $data['cod_emp'],
                'id_campanha' => $data['id_campanha'],
                'sei_dnit' => $data['sei_dnit'] ?? null,
                'data_ini' => array_key_exists('data_ini', $data) ? $data['data_ini'] : $campanha->data_ini,
                'data_fim' => array_key_exists('data_fim', $data) ? $data['data_fim'] : $campanha->data_fim,
                'subproduto' => $data['subproduto'],
                'status' => $status,
            ]);

            $entrega->update(['status' => $status]);

            $this->salvarPlanilhas($entrega, $campanha, $data['planilhas'] ?? []);

            foreach ($data['fotos'] ?? [] as $foto) {
                if (!empty($foto['arquivo']) && $foto['arquivo'] instanceof UploadedFile) {
                    $this->salvarArquivo($entrega, $foto['arquivo'], 'foto', $foto['descricao'] ?? null, $foto['latitude'] ?? null, $foto['longitude'] ?? null, $foto['data_captura'] ?? null);
                }
            }

            $this->salvarAnexos($entrega, $data['anexos'] ?? []);

            return $entrega->fresh(['planilhasFauna.modulo', 'fotos', 'anexos', 'analises']);
        });
    }

    private function salvarAnexos(SgcEntregaSimplificada $entrega, array $anexos): void
    {
        $removidos = collect($anexos)->filter(fn ($anexo) => !empty($anexo['remover']))->pluck('id')->filter()->all();
        $novos = collect($anexos)->filter(fn ($anexo) => empty($anexo['remover']) && empty($anexo['id'])
            && (($anexo['arquivo'] ?? null) instanceof UploadedFile || !empty($anexo['upload_token'])));
        if ($entrega->anexos()->whereNotIn('id', $removidos)->count() + $novos->count() > 100) {
            throw \Illuminate\Validation\ValidationException::withMessages(['anexos' => 'O limite é de 100 anexos por campanha.']);
        }

        foreach ($anexos as $anexo) {
            // Todo acesso a um arquivo existente é limitado à entrega atual.
            $existente = !empty($anexo['id']) ? $entrega->anexos()->findOrFail($anexo['id']) : null;
            if (!empty($anexo['remover'])) {
                if ($existente) {
                    $caminho = $existente->caminho_arquivo;
                    $existente->delete();
                    DB::afterCommit(fn () => $this->removerArquivo($caminho));
                }
                continue;
            }

            $metadados = [
                'classe' => $anexo['classe'] ?? 'outros',
                'titulo_bloco' => $anexo['titulo_bloco'] ?? 'Outros',
            ];
            if ($existente) {
                $existente->update(['metadados' => array_merge($existente->metadados ?? [], $metadados)]);
            } elseif (($anexo['arquivo'] ?? null) instanceof UploadedFile) {
                $this->salvarArquivo($entrega, $anexo['arquivo'], 'anexo', metadados: $metadados);
            } elseif (!empty($anexo['upload_token'])) {
                $temporario = (new AnexoTemporarioService())->consultar($anexo['upload_token'], (int) $entrega->id_contrato, (int) auth()->id());
                $caminho = "entrega_simplificada/fauna/{$entrega->entidade_id}/anexos/" . basename($temporario['caminho']);
                $origem = \Illuminate\Support\Facades\Storage::disk('local')->readStream($temporario['caminho']);
                try {
                    $gravado = \Illuminate\Support\Facades\Storage::disk('public')->put($caminho, $origem);
                } finally {
                    if (is_resource($origem)) fclose($origem);
                }
                if (!$gravado) throw \Illuminate\Validation\ValidationException::withMessages(['anexos' => 'Não foi possível salvar o anexo. Tente novamente.']);
                SgcEntregaSimplificadaArquivo::create([
                    'entrega_simplificada_id' => $entrega->id, 'tipo' => 'anexo',
                    'nome_arquivo' => $temporario['nome'], 'caminho_arquivo' => $caminho,
                    'mime_type' => $temporario['mime'], 'tamanho_bytes' => $temporario['tamanho'], 'metadados' => $metadados,
                ]);
                DB::afterCommit(function () use ($temporario, $caminho) {
                    $this->sincronizarArquivoPublico($caminho);
                    \Illuminate\Support\Facades\Storage::disk('local')->delete($temporario['caminho']);
                });
            }
        }
    }

    private function salvarPlanilhas(SgcEntregaSimplificada $entrega, SgcFaunaCampanha $campanha, array $planilhas): void
    {
        foreach (['terrestre', 'aquatica', 'cavernicola'] as $tipo) {
            $dados = $planilhas[$tipo] ?? [];
            $planilha = $entrega->planilhasFauna()->where('tipo', $tipo)->first();

            if (!empty($dados['remover'])) {
                if ($planilha) {
                    $this->removerPlanilha($planilha);
                }
                continue;
            }

            if (empty($dados['arquivo']) || !$dados['arquivo'] instanceof UploadedFile) {
                continue;
            }

            if ($planilha) {
                $this->removerArquivo($planilha->caminho_arquivo);
            }

            $arquivo = $dados['arquivo'];
            $caminho = $arquivo->store("entrega_simplificada/fauna/{$campanha->id}/planilhas/{$tipo}", 'public');
            $this->sincronizarArquivoPublico($caminho);

            SgcFaunaEntregaSimplificadaPlanilha::updateOrCreate(
                ['entrega_simplificada_id' => $entrega->id, 'tipo' => $tipo],
                [
                    'modulo_id' => $dados['modulo_id'],
                    'nome_arquivo' => $arquivo->getClientOriginalName(),
                    'caminho_arquivo' => $caminho,
                    'mime_type' => $arquivo->getMimeType(),
                    'tamanho_bytes' => $arquivo->getSize(),
                ]
            );
        }
    }

    private function removerPlanilha(SgcFaunaEntregaSimplificadaPlanilha $planilha): void
    {
        $this->removerArquivo($planilha->caminho_arquivo);
        $planilha->delete();
    }

    private function removerArquivo(?string $caminho): void
    {
        if (!$caminho) {
            return;
        }

        \Illuminate\Support\Facades\Storage::disk('public')->delete($caminho);
        $publico = public_path('storage/' . $caminho);
        if (is_file($publico)) {
            @unlink($publico);
        }
    }

    private function salvarArquivo(
        SgcEntregaSimplificada $entrega,
        UploadedFile $arquivo,
        string $tipo,
        ?string $descricao = null,
        $latitude = null,
        $longitude = null,
        $dataCaptura = null,
        array $metadados = []
    ): SgcEntregaSimplificadaArquivo {
        $caminho = $arquivo->store("entrega_simplificada/fauna/{$entrega->entidade_id}/{$tipo}s", 'public');
        $this->sincronizarArquivoPublico($caminho);

        return SgcEntregaSimplificadaArquivo::create([
            'entrega_simplificada_id' => $entrega->id,
            'tipo' => $tipo,
            'nome_arquivo' => $arquivo->getClientOriginalName(),
            'caminho_arquivo' => $caminho,
            'mime_type' => $arquivo->getMimeType(),
            'tamanho_bytes' => $arquivo->getSize(),
            'descricao' => $descricao,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'data_captura' => $dataCaptura,
            'metadados' => $metadados,
        ]);
    }

    private function sincronizarArquivoPublico(?string $caminho): void
    {
        try {
            if (!$caminho) {
                return;
            }
            $origem = storage_path('app/public/' . $caminho);

            if (!is_file($origem)) {
                return;
            }

            $destino = public_path('storage/' . $caminho);
            $destinoDir = dirname($destino);

            if (!is_dir($destinoDir)) {
                @mkdir($destinoDir, 0777, true);
            }

            @copy($origem, $destino);
        } catch (\Throwable $e) {
            // Não interrompe o cadastro se a cópia auxiliar falhar.
        }
    }

}
