<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */

    public function up()
    {
        Schema::create('change_logs', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id')->nullable();         // quem fez a alteração
            $table->string('table_name');                               // nome da tabela alterada
            $table->unsignedBigInteger('record_id');                    // id do registro alterado
            $table->string('field');                                    // campo alterado
            $table->text('old_value')->nullable();                      // valor anterior
            $table->text('new_value')->nullable();                      // novo valor
            $table->string('status')->nullable();                       // status (se quiser usar)

            $table->timestamps();                                       // created_at como data da mudança

            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('change_logs');
    }
};
