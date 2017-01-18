<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DataTypesAddServerSide extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('voyager_data_types', function (Blueprint $table) {
            $table->tinyInteger('server_side')->default(0)->after('generate_permissions');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('voyager_data_types', function (Blueprint $table) {
            $table->dropColumn('server_side');
        });
    }
}
