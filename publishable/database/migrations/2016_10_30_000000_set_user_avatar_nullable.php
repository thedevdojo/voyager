<?php

use Illuminate\Database\Migrations\Migration;

class SetUserAvatarNullable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table({*users_table*}, function ($table) {
            $table->string('avatar')->nullable()->change();
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
            $table->string('avatar')->nullable(false)->change();
        });
    }
}
