<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChangeLog extends Model
{
    protected $fillable = [
        'user_id', 'table_name', 'record_id',
        'field', 'old_value', 'new_value', 'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
