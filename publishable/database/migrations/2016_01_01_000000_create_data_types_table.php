<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDataTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Create table for storing roles
        Schema::create('data_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique()->comment('名称');
            $table->string('slug')->unique();
            $table->string('display_name_singular')->comment('单数显示名');
            $table->string('display_name_plural')->comment('负数显示名');
            $table->string('icon')->nullable()->comment('图标');
            $table->string('model_name')->nullable()->comment('模型');
            $table->string('description')->nullable()->comment('描述');
            $table->boolean('generate_permissions')->default(false)->comment('权限');
            $table->timestamps();
        });

        // Create table for storing roles
        Schema::create('data_rows', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('data_type_id')->unsigned();
            $table->string('field')->comment('字段名');
            $table->string('type')->comment('字段类型');
            $table->string('display_name')->comment('显示名');
            $table->boolean('required')->default(false);
            $table->boolean('browse')->default(true)->comment('可浏览');
            $table->boolean('read')->default(true)->comment('可读取');
            $table->boolean('edit')->default(true)->comment('可编写');
            $table->boolean('add')->default(true)->comment('可添加');
            $table->boolean('delete')->default(true)->comment('可删除');
            $table->text('details')->nullable();

            $table->foreign('data_type_id')->references('id')->on('data_types')
                ->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('data_rows');
        Schema::drop('data_types');
    }
}
