<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterMenuItem extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('menu_items', function (Blueprint $table)
        {
            $table->unsignedInteger('parent_id')->nullable()->change();

            $table->foreign('parent_id')->references('id')->on('menu_items')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('menu_items', function (Blueprint $table)
        {
            $table->dropForeign(['parent_id']);
        });

        Schema::table('menu_items', function (Blueprint $table)
        {
            $table->integer('parent_id')->nullable()->change();
        });
    }
}
