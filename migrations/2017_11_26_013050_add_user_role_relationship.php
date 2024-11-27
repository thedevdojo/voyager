<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('users')->where('role_id', '<', 0)->update(['role_id' => 0]);

        Schema::table('users', function (Blueprint $table) {
            // Make 'role_id' unsigned
            $table->bigInteger('role_id')->unsigned()->change();

            // Add the foreign key constraint to 'roles' table
            $table->foreign('role_id')->references('id')->on('roles');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop the foreign key constraint
            $table->dropForeign(['role_id']);
        });

        Schema::table('users', function (Blueprint $table) {
            // Revert 'role_id' back to signed if needed
            $table->bigInteger('role_id')->change();
        });
    }
};