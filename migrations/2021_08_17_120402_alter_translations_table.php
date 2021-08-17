<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use Illuminate\Support\Facades\DB;

class AlterTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // clean all possible dead key to avoid the Illuminate\Database\QueryException :
        //  Integrity constraint violation: 1452 Cannot add or update a child row: a foreign key constraint fails
        DB::table('translations')->whereNotIn('foreign_key', DB::table('data_rows')->select('id'))->delete();

        Schema::table('translations', function (Blueprint $table)
        {
            $table->foreign('foreign_key')->references('id')->on('data_rows')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('translations', function (Blueprint $table)
        {
            $table->dropForeign(['foreign_key']);
        });
    }
}
