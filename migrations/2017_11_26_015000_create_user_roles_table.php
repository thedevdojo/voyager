<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(get_prefixed_table('user').'_'.get_prefixed_table('roles'), function (Blueprint $table) {
            $type = DB::connection()->getDoctrineColumn(get_prefixed_table('users'), 'id')->getType()->getName();
            if ($type == 'bigint') {
                $table->bigInteger('user_id')->unsigned()->index();
            } else {
                $table->integer('user_id')->unsigned()->index();
            }

            $table->foreign('user_id')->references('id')->on(get_prefixed_table('users'))->onDelete('cascade');
            $table->bigInteger('role_id')->unsigned()->index();
            $table->foreign('role_id')->references('id')->on(get_prefixed_table('roles'))->onDelete('cascade');
            $table->primary(['user_id', 'role_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(get_prefixed_table('user').'_'.get_prefixed_table('roles'));
    }
}
