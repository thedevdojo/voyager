<?php

use Illuminate\Database\Migrations\Migration;

class AddVoyagerUserFields extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table(get_prefixed_table('users'), function ($table) {
            if (!Schema::hasColumn(get_prefixed_table('users'), 'avatar')) {
                $table->string('avatar')->nullable()->after('email')->default('users/default.png');
            }
            $table->bigInteger('role_id')->nullable()->after('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        if (Schema::hasColumn(get_prefixed_table('users'), 'avatar')) {
            Schema::table(get_prefixed_table('users'), function ($table) {
                $table->dropColumn('avatar');
            });
        }
        if (Schema::hasColumn(get_prefixed_table('users'), 'role_id')) {
            Schema::table(get_prefixed_table('users'), function ($table) {
                $table->dropColumn('role_id');
            });
        }
    }
}
