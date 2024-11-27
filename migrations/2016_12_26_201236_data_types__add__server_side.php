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
        Schema::table('data_types', function (Blueprint $table) {
            $table->tinyInteger('server_side')->default(0)->after('generate_permissions');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('data_types', function (Blueprint $table) {
            $table->dropColumn('server_side');
        });
    }
};