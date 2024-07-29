<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServicoLicencaCondicionante extends Model
{
    use HasFactory;

    protected $table = 'servico_licenca_condicionantes';
    protected $guarded = ['id', 'created_at'];

    public function licenca()
    {
        return $this->belongsTo(Licenca::class, 'licenca_id');
    }

    public function condicionante()
    {
        return $this->belongsTo(LicencaCondicionante::class, 'condicionante_id');
    }
}
