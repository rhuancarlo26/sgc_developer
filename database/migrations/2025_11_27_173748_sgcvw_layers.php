<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('sgcvw_layers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('file_path');        // zip original
            $table->string('mbtiles_path')->nullable();
            $table->enum('status', [
                'pending',
                'processing',
                'ready',
                'failed'
            ])->default('pending');
            $table->text('error')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('sgcvw_layers');
    }
};
