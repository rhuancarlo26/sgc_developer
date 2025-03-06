<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Destinacao extends Model
{
    use SoftDeletes;

    protected $table = 'destinacaos';

    protected $guarded = ['id', 'created_at'];

    public function pilhas(): BelongsToMany
    {
        return $this->belongsToMany(ControlePilha::class, 'destinacao_pilhas', 'destinacao_id', 'controle_pilha_id');
    }

    public function arquivos(): BelongsToMany
    {
        return $this->belongsToMany(Arquivo::class, 'arquivo_destinacao_pilha', 'destinacaos_id', 'arquivo_id');
    }

}
