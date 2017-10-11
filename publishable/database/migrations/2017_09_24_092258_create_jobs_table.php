<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jobs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('author_id')->comment('作者');
            $table->integer('category_id')->default(0)->comment('分类');
            $table->string('position')->comment('职位');
            $table->unsignedInteger('numbers')->default(1)->comment('人数');
            $table->string('department')->nullable()->comment('部门');
            $table->string('address')->nullable()->comment('工作地点');
            $table->text('description')->nullable()->comment('描述');
            $table->text('requirements')->nullable()->comment('要求');
            $table->text('contact')->nullable()->comment('联系人');
            $table->date('published_at')->nullable()->comment('截止日期');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('jobs');
    }
}
