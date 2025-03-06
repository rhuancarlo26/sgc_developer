<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoricoDav extends Model
{
    use HasFactory;

    protected $fillable = ['dav_id', 'status_anterior', 'status_novo', 'observacao', 'user_id'];

    public function dav()
    {
        return $this->belongsTo(Dav::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
