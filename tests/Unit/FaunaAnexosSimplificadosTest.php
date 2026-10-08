<?php

namespace Tests\Unit;

use App\Domain\Sgc\Contratada\Produtos\Fauna\Requests\StoreEntregaSimplificadaFaunaRequest;
use App\Domain\Sgc\Contratada\Produtos\Fauna\Services\SgcFaunaEntregaSimplificadaService;
use App\Models\SgcEntregaSimplificada;
use App\Models\SgcEntregaSimplificadaArquivo;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class FaunaAnexosSimplificadosTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');
        Schema::create('sgc_entrega_simplificada_arquivos', function (Blueprint $table) {
            $table->id();
            $table->integer('entrega_simplificada_id');
            $table->string('tipo');
            $table->string('nome_arquivo');
            $table->string('caminho_arquivo');
            $table->string('mime_type')->nullable();
            $table->integer('tamanho_bytes')->nullable();
            $table->text('descricao')->nullable();
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->string('data_captura')->nullable();
            $table->text('metadados')->nullable();
            $table->timestamps();
        });
    }

    private function entrega(int $id = 1): SgcEntregaSimplificada
    {
        $entrega = new SgcEntregaSimplificada();
        $entrega->id = $id;
        $entrega->entidade_id = 1;
        $entrega->id_contrato = 6;
        return $entrega;
    }

    private function arquivo(string $nome): UploadedFile
    {
        $arquivo = \Mockery::mock(UploadedFile::class);
        $arquivo->shouldReceive('store')->andReturn('tests-anexos-inexistentes/' . $nome);
        $arquivo->shouldReceive('getClientOriginalName')->andReturn($nome);
        $arquivo->shouldReceive('getMimeType')->andReturn('application/pdf');
        $arquivo->shouldReceive('getSize')->andReturn(100);
        return $arquivo;
    }

    private function salvar(array $anexos, int $entregaId = 1): void
    {
        $metodo = new \ReflectionMethod(SgcFaunaEntregaSimplificadaService::class, 'salvarAnexos');
        DB::transaction(fn () => $metodo->invoke(new SgcFaunaEntregaSimplificadaService(), $this->entrega($entregaId), $anexos));
    }

    public function test_multiple_files_keep_their_group_and_original_names(): void
    {
        $this->salvar([
            ['arquivo' => $this->arquivo('art-1.pdf'), 'classe' => 'art', 'titulo_bloco' => 'ARTs'],
            ['arquivo' => $this->arquivo('art-2.pdf'), 'classe' => 'art', 'titulo_bloco' => 'ARTs'],
            ['arquivo' => $this->arquivo('outros.pdf'), 'classe' => 'outros_abc', 'titulo_bloco' => 'Outros'],
        ]);
        $arquivos = $this->entrega()->anexos()->get();
        $this->assertCount(3, $arquivos);
        $this->assertSame(['art-1.pdf', 'art-2.pdf', 'outros.pdf'], $arquivos->pluck('nome_arquivo')->all());
        $this->assertSame('art', $arquivos[1]->metadados['classe']);
        $this->assertSame('outros_abc', $arquivos[2]->metadados['classe']);

        $this->salvar([['id' => $arquivos[0]->id, 'classe' => 'art', 'titulo_bloco' => 'ARTs']]);
        $this->assertSame(3, $this->entrega()->anexos()->count());
    }

    public function test_legacy_files_can_be_classified_without_reuploading(): void
    {
        $arquivo = SgcEntregaSimplificadaArquivo::create([
            'entrega_simplificada_id' => 1, 'tipo' => 'anexo',
            'nome_arquivo' => 'antigo.pdf', 'caminho_arquivo' => 'antigo.pdf',
        ]);
        $this->salvar([['id' => $arquivo->id, 'classe' => 'outros', 'titulo_bloco' => 'Outros']]);
        $this->assertSame('antigo.pdf', $arquivo->fresh()->nome_arquivo);
        $this->assertSame('outros', $arquivo->fresh()->metadados['classe']);
    }

    public function test_cannot_modify_an_attachment_from_another_delivery(): void
    {
        $this->salvar([['arquivo' => $this->arquivo('art.pdf'), 'classe' => 'art']]);
        $id = $this->entrega()->anexos()->first()->id;
        $this->expectException(ModelNotFoundException::class);
        $this->salvar([['id' => $id, 'remover' => true]], 2);
    }

    public function test_removal_keeps_other_files_and_runs_after_commit(): void
    {
        $this->salvar([
            ['arquivo' => $this->arquivo('remover.pdf'), 'classe' => 'art'],
            ['arquivo' => $this->arquivo('manter.pdf'), 'classe' => 'art'],
        ]);
        Storage::shouldReceive('disk')->with('public')->once()->andReturnSelf();
        Storage::shouldReceive('delete')->with('tests-anexos-inexistentes/remover.pdf')->once()->andReturn(true);
        $this->salvar([['id' => $this->entrega()->anexos()->first()->id, 'remover' => true]]);
        $this->assertSame(['manter.pdf'], $this->entrega()->anexos()->pluck('nome_arquivo')->all());
    }

    public function test_missing_new_upload_is_rejected_but_saved_files_are_accepted(): void
    {
        $rules = array_filter((new StoreEntregaSimplificadaFaunaRequest())->rules(), fn ($key) => str_starts_with($key, 'anexos'), ARRAY_FILTER_USE_KEY);
        $this->assertFalse(Validator::make(['anexos' => [['id' => 1, 'classe' => 'art']]], $rules)->fails());
        $this->assertTrue(Validator::make(['anexos' => [['classe' => 'art']]], $rules)->fails());
    }

    public function test_attachment_limit_is_twenty_megabytes(): void
    {
        $request = new StoreEntregaSimplificadaFaunaRequest();
        $rules = array_filter($request->rules(), fn ($key) => str_starts_with($key, 'anexos'), ARRAY_FILTER_USE_KEY);
        $accepted = Validator::make(['anexos' => [['arquivo' => UploadedFile::fake()->create('anexo.pdf', 20480), 'classe' => 'art']]], $rules, $request->messages());
        $this->assertFalse($accepted->fails());
        $rejected = Validator::make(['anexos' => [['arquivo' => UploadedFile::fake()->create('anexo.pdf', 20481), 'classe' => 'art']]], $rules, $request->messages());
        $this->assertTrue($rejected->fails());
        $this->assertSame('Cada anexo deve ter no máximo 20 MB.', $rejected->errors()->first('anexos.0.arquivo'));
    }

    public function test_uploaded_reference_can_be_saved_without_sending_the_file_again(): void
    {
        Storage::fake('local');
        Storage::fake('public');
        $this->app['auth']->setUser(new \Illuminate\Auth\GenericUser(['id' => 1]));
        $temporarios = new \App\Domain\Sgc\Contratada\Produtos\Fauna\Services\AnexoTemporarioService();
        $token = $temporarios->receber(UploadedFile::fake()->create('art.pdf', 12), 6, 1);
        $dados = $temporarios->consultar($token, 6, 1);
        $this->salvar([['upload_token' => $token, 'classe' => 'art', 'titulo_bloco' => 'ARTs']]);
        $arquivo = $this->entrega()->anexos()->first();
        $this->assertSame('art.pdf', $arquivo->nome_arquivo);
        $this->assertSame('art', $arquivo->metadados['classe']);
        Storage::disk('public')->assertExists($arquivo->caminho_arquivo);
        Storage::disk('local')->assertMissing($dados['caminho']);

        $request = new StoreEntregaSimplificadaFaunaRequest();
        $rules = array_filter($request->rules(), fn ($key) => str_starts_with($key, 'anexos'), ARRAY_FILTER_USE_KEY);
        $this->assertFalse(Validator::make(['anexos' => [['upload_token' => $token, 'classe' => 'art']]], $rules)->fails());
    }

    public function test_temporary_upload_cannot_be_used_by_another_user_or_contract(): void
    {
        Storage::fake('local');
        $temporarios = new \App\Domain\Sgc\Contratada\Produtos\Fauna\Services\AnexoTemporarioService();
        $token = $temporarios->receber(UploadedFile::fake()->create('art.pdf', 12), 6, 1);
        foreach ([[6, 2], [7, 1]] as [$contrato, $usuario]) {
            try {
                $temporarios->consultar($token, $contrato, $usuario);
                $this->fail('Foreign temporary upload accepted.');
            } catch (\Illuminate\Validation\ValidationException $e) {
                $this->assertArrayHasKey('anexos', $e->errors());
            }
        }
        $this->travel(25)->hours();
        $this->expectException(\Illuminate\Validation\ValidationException::class);
        $temporarios->consultar($token, 6, 1);
    }
}
