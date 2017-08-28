<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddPolicyNameToDataTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('data_types', function (Blueprint $table) {
            $table->string('policy_name')->nullable()->after('model_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('data_types', function (Blueprint $table) {
            $table->dropColumn('policy_name');
        });
    }
}
