<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServicoPassagemFaunaRelatorio extends Model
{
    use HasFactory;

    protected $table = 'passagem_fauna_relatorio';
    protected $guarded = ['id'];

    public function resultado(): BelongsTo
    {
        return $this->belongsTo(ServicoPassagemFaunaResultado::class, 'id_resultado');
    }
}
