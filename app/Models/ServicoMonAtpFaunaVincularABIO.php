<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServicoMonAtpFaunaVincularABIO extends Model
{
    use HasFactory;

    protected $table = 'at_fauna_config_vinculacao';
    protected $guarded = ['id'];

    public function licenca()
    {
        return $this->belongsTo(Licenca::class, 'fk_licenca');
    }

    public function rets(): HasMany
    {
        return $this->hasMany(ServicoMonAtpFaunaVincularRetABIO::class, 'fk_at_config_vinculacao');
    }
}
