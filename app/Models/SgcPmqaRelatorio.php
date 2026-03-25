<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SgcPmqaRelatorio extends Model
{
    use HasFactory;

    protected $table = 'sgc_pmqa_relatorio';
    protected $guarded = ['id', 'created_at'];

    public function status()
    {
        return $this->belongsTo(SgcPmqaStatus::class, 'status_id');
    }

    public function resultado()
    {
        return $this->belongsTo(SgcPmqaResultado::class, 'pmqa_resultado_id');
    }

    public function pmqa()
    {
        return $this->belongsTo(SgcPmqa::class, 'pmqa_id');
    }
}
