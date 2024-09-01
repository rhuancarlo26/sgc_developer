<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServicoLicencaCondicionante extends Model
{

    protected $table = 'servico_licenca_condicionante';
    protected $guarded = ['id', 'created_at'];

    public function licenca()
    {
        return $this->belongsTo(Licenca::class, 'id_licenca');
    }

    public function condicionante()
    {
        return $this->belongsTo(LicencaCondicionante::class, 'condicionante_id');
    }
}
