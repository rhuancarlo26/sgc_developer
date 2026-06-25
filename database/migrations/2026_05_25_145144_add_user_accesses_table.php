<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_accesses', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id')->index();

            $table->ipAddress('ip_address')->nullable();
            $table->text('user_agent')->nullable();

            $table->timestamp('logged_in_at')->useCurrent();

            $table->index(['user_id', 'logged_in_at']);

            $table->index('logged_in_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_accesses');
    }
};