<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SgcProdutos extends Model
{
    use HasFactory;

    protected $table = 'sgc_produtos';
    protected $guarded = ['id', 'created_at'];
    public function changelogs()
    {
        return $this->hasMany(ChangeLog::class, 'record_id')
            ->where('table_name', $this->table)
            ->with('user')
            ->orderBy('created_at', 'desc');
    }
}
