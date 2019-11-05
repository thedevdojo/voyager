<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserRoleRelationship extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(get_prefixed_table('users'), function (Blueprint $table) {
            $table->bigInteger('role_id')->unsigned()->change();
            $table->foreign('role_id')->references('id')->on(get_prefixed_table('roles'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(get_prefixed_table('users'), function (Blueprint $table) {
            $table->dropForeign(['role_id']);
        });

        Schema::table(get_prefixed_table('users'), function (Blueprint $table) {
            $table->bigInteger('role_id')->change();
        });
    }
}
