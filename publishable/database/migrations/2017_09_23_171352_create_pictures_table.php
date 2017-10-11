<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\Picture;

class CreatePicturesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pictures', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('author_id')->comment('作者');
            $table->integer('category_id')->default(0)->comment('分类');
            $table->string('image')->comment('图片');
            $table->string('url')->nullable()->comment('链接');
            $table->string('name')->nullable('名称');
            $table->date('published_at')->nullable()->comment('日期');
            $table->integer('order')->nullable()->comment('排序');
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
        Schema::dropIfExists('pictures');
    }
}
