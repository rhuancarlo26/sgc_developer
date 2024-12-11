<?php

namespace App\Models;

use App\Domain\Servico\SupressaoVegetacao\Execucao\Pilhas\Enums\TipoPilha;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ControlePilha extends Model
{
    use SoftDeletes;

    protected $guarded = ['id', 'created_at'];

    protected $appends = ['tipo_pilha_label'];

    public function tipoPilhaLabel(): Attribute
    {
        return Attribute::make(
            get: fn() => TipoPilha::getLabel($this->tipo_pilha)
        );
    }

    public function fotos(): BelongsToMany
    {
        return $this->belongsToMany(Arquivo::class, 'arquivo_controle_pilha', 'controle_pilhas_id', 'arquivo_id');
    }

    public function areaSupressao(): BelongsTo
    {
        return $this->belongsTo(AreaSupressao::class, 'area_supressao_id');
    }

    public function licenca(): BelongsTo
    {
        return $this->belongsTo(Licenca::class, 'licenca_id');
    }

    public function produto(): BelongsTo
    {
        return $this->belongsTo(TipoProduto::class, 'tipo_produto_id');
    }

    public function corteEspecie(): BelongsTo
    {
        return $this->belongsTo(CorteEspecie::class, 'corte_especie_id');
    }

    public function patio(): BelongsTo
    {
        return $this->belongsTo(PatioEstocagem::class, 'patio_estocagem_id');
    }

}
