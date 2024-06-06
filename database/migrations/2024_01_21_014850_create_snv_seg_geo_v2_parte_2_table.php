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
        $db = [
            'DB_CONNECTION' => env('DB_CONNECTION'),
            'DB_HOST' => env('DB_HOST'),
            'DB_PORT' => env('DB_PORT'),
            'DB_DATABASE' => env('DB_DATABASE'),
            'DB_USERNAME' => env('DB_USERNAME'),
            'DB_PASSWORD' => env('DB_PASSWORD'),
        ];

        $sql = storage_path('sql') . '/snv_seg_geo_v2_parte2.sql';

        exec("mysql --user={$db['DB_USERNAME']} --password={$db['DB_PASSWORD']} --host={$db['DB_HOST']} --port={$db['DB_PORT']} --database={$db['DB_DATABASE']} < $sql");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};