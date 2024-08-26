<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AfugentFaunaFormaRegistroModel extends Model
{
    use HasFactory;

    protected $table = 'afugent_fauna_forma_registro';

    protected $guarded = ['id'];
}
