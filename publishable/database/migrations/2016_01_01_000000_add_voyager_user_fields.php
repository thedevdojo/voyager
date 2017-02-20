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
            $table->integer('role_id')->after('id');
            $table->string('avatar')->nullable()->after('email');
            $table->mediumText('bio')->nullable()->after('avatar');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('users', function ($table) {
            $table->dropColumn('role_id');
            $table->dropColumn('avatar');
            $table->dropColumn('bio');
        });
    }
}
