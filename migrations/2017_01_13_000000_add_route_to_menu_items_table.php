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
        Schema::table(get_prefixed_table('menu_items'), function (Blueprint $table) {
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
        if (Schema::hasColumn(get_prefixed_table('menu_items'), 'route')) {
            Schema::table(get_prefixed_table('menu_items'), function (Blueprint $table) {
                $table->dropColumn('route');
            });
        }

        if (Schema::hasColumn(get_prefixed_table('menu_items'), 'parameters')) {
            Schema::table(get_prefixed_table('menu_items'), function (Blueprint $table) {
                $table->dropColumn('parameters');
            });
        }
    }
}
