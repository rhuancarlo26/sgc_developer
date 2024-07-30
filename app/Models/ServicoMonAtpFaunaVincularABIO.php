<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServicoMonAtpFaunaVincularABIO extends Model
{
    use HasFactory;

    protected $table = 'at_fauna_config_vinculacao';
    protected $guarded = ['id'];

    public function licenca()
    {
        return $this->belongsTo(Licenca::class, 'fk_licenca');
    }
}
