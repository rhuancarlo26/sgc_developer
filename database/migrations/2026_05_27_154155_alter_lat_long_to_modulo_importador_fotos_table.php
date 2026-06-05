<?php

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
        Schema::table('modulo_importador_fotos', function (Blueprint $table) {
            $table->decimal('latitude', 10, 8)->nullable()->change();
            $table->decimal('longitude', 11, 8)->nullable()->change();

            $table->timestamp('data_captura')->nullable()->after('descricao');
            $table->string('fabricante')->nullable()->after('data_captura');
            $table->string('modelo')->nullable()->after('fabricante');
            $table->integer('largura')->nullable()->after('modelo');
            $table->integer('altura')->nullable()->after('largura');
            $table->string('orientacao')->nullable()->after('altura');
            $table->json('metadados')->nullable()->after('orientacao');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('modulo_importador_fotos', function (Blueprint $table) {
            $table->double('latitude', 8, 2)->nullable()->change();
            $table->double('longitude', 8, 2)->nullable()->change();

            $table->dropColumn([
                'data_captura',
                'fabricante',
                'modelo',
                'largura',
                'altura',
                'orientacao',
                'metadados',
            ]);
        });
    }
};
