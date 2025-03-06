<?php

use App\Models\ServicoStatus;
use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('servico_status', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->timestamps();
        });

        $now = Carbon::now();

        $status = [
            [
                'nome' => 'Em confecção',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'nome' => 'Em análise',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'nome' => 'Aprovado',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'nome' => 'Pendencia',
                'created_at' => $now,
                'updated_at' => $now
            ],
        ];

        ServicoStatus::insert($status);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('servico_status');
    }
};