<?php

use Illuminate\Database\Migrations\Migration;

class AddVoyagerUserFields extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('users', function ($table) {
            $table->string('avatar')->nullable()->after('email');
            $table->integer('role_id')->nullable()->after('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('users', function ($table) {
            $table->dropColumn('avatar');
            $table->dropColumn('role_id');
        });
    }
}
