<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->increments('id');
            $table->string('key')->unique()->comment('外部调用键');
            $table->string('display_name')->comment('显示名');
            $table->text('value')->comment('值');
            $table->text('details')->nullable()->default(null);
            $table->string('type')->comment('类型');
            $table->integer('order')->default('1')->comment('排序');
            $table->string('group')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('settings');
    }
}
