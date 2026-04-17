<?php

use App\Models\ModuloImportador;
use App\Models\User;
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
        Schema::create('modulo_importador_historicos', function (Blueprint $table) {
            $table->id();

            // $table->foreignIdFor(User::class, 'usuario_id')->constrained('users')->cascadeOnDelete();
            $table->integer('usuario_id');
            $table->foreign('usuario_id')->references('id')->on('users')->cascadeOnDelete();

            $table->foreignIdFor(ModuloImportador::class, 'modulo_importador_id')->constrained('modulo_importadores')->cascadeOnDelete();
            $table->integer('status');
            $table->text('parecer')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('modulo_importador_historicos');
    }
};
