<?php

use Illuminate\Database\Migrations\Migration;

class AddUserAvatar extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table({*users_table*}, function ($table) {
            $table->string('avatar')->default('users/default.png');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table({*users_table*}, function ($table) {
            $table->dropColumn('avatar');
        });
    }
}
