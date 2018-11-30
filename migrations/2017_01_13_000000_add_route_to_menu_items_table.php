<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRouteToMenuItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('menu_items', function (Blueprint $table) {
            $table->string('route')->nullable()->default(null);
            $table->text('parameters')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('menu_items', 'route')) {
            Schema::table('menu_items', function (Blueprint $table) {
                $table->dropColumn('route');
            });
        }

        if (Schema::hasColumn('menu_items', 'parameters')) {
            Schema::table('menu_items', function (Blueprint $table) {
                $table->dropColumn('parameters');
            });
        }
    }
}
