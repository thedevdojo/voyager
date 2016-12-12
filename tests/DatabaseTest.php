<?php

namespace TCG\Voyager\Tests;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use TCG\Voyager\Http\Controllers\Traits\DatabaseQueryBuilder;
use TCG\Voyager\Models\Role;

class DatabaseTest extends TestCase
{
    use DatabaseTransactions;
    use DatabaseQueryBuilder;

    public function setUp()
    {
        parent::setUp();

        $this->install();
    }

    public function test_can_create_table()
    {
        Auth::loginUsingId(1);

        $this->post(route('voyager.database.store'), [
            "name"     => "voyagertest",
            "field"    => [
                "id",
                "tiny_int_field",
                "small_int_field",
                "med_int_field",
                "integer_field",
                "big_int_field",
                "string_field",
                "text_field",
                "med_text_field",
                "long_text_field",
                "float_field",
                "double_field",
                "decimal_field",
                "boolean_field",
                "enum_field",
                "date_field",
                "date_time_field",
                "time_field",
                "time_stamp_field",
                "binary_field",
                "created_at & updated_at",
            ],
            "type"     => [
                "integer",
                "tinyInteger",
                "smallInteger",
                "mediumInteger",
                "integer",
                "bigInteger",
                "string",
                "text",
                "mediumText",
                "longText",
                "float",
                "double",
                "decimal",
                "boolean",
                "enum",
                "date",
                "dateTime",
                "time",
                "timestamp",
                "binary",
                "timestamp",
            ],
            "enum"     => [
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "valueA,valueB",
                "",
                "",
                "",
                "",
                "",
                "",
            ],
            "nullable" => [
                "0",
                "0",
                "0",
                "0",
                "0",
                "0",
                "0",
                "0",
                "0",
                "0",
                "0",
                "0",
                "0",
                "0",
                "0",
                "0",
                "0",
                "0",
                "0",
                "0",
                "0",
            ],
            "key"      => [
                "PRI",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
            ],
            "default"  => [
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "CURRENT_TIMESTAMP",
            ],
        ]);

        // Test redirected to correct page
        $this->assertRedirectedTo(route('voyager.database.index'), [
            'message'    => "Successfully created voyagertest table",
            'alert-type' => 'success',
        ]);

        // Test table exist
        $this->assertTrue(Schema::hasTable('voyagertest'));

        // Test column type
        $columns = DB::select(DB::raw('PRAGMA table_info(voyagertest)'));
        $this->assertEquals("integer", $columns[0]->type);
        $this->assertEquals("integer", $columns[1]->type);
        $this->assertEquals("integer", $columns[2]->type);
        $this->assertEquals("integer", $columns[3]->type);
        $this->assertEquals("integer",$columns[4]->type);
        $this->assertEquals("integer",$columns[5]->type);
        $this->assertEquals("varchar", $columns[6]->type);
        $this->assertEquals("text", $columns[7]->type);
        $this->assertEquals("text", $columns[8]->type);
        $this->assertEquals("text", $columns[9]->type);
        $this->assertEquals("float", $columns[10]->type);
        $this->assertEquals("float",$columns[11]->type);
        $this->assertEquals("numeric", $columns[12]->type);
        $this->assertEquals("tinyint(1)",$columns[13]->type);
        $this->assertEquals("varchar", $columns[14]->type);
        $this->assertEquals("date", $columns[15]->type);
        $this->assertEquals("datetime", $columns[16]->type);
        $this->assertEquals("time", $columns[17]->type);
        $this->assertEquals("datetime", $columns[18]->type);
        $this->assertEquals("blob", $columns[19]->type);
        $this->assertEquals("datetime", $columns[20]->type);
        $this->assertEquals("datetime", $columns[21]->type);

    }

    public function test_can_create_nullable_column()
    {
        Auth::loginUsingId(1);

        $this->post(route('voyager.database.store'), [
            "name"     => "voyagertest",
            "field"    => [
                "string_field",
            ],
            "type"     => [
                "string",
            ],
            "nullable" => [
                "1",
            ],
        ]);

        // Test redirected to correct page
        $this->assertRedirectedTo(route('voyager.database.index'), [
            'message'    => "Successfully created voyagertest table",
            'alert-type' => 'success',
        ]);

        // Test table exist
        $this->assertTrue(Schema::hasTable('voyagertest'));

        // Test column type
        $columns = DB::select(DB::raw('PRAGMA table_info(voyagertest)'));

        $this->assertEquals("0", $columns[0]->notnull); // inverse in sqlite (notnull)

    }

    public function test_can_create_primary_key_column()
    {
        Auth::loginUsingId(1);

        $this->post(route('voyager.database.store'), [
            "name"     => "voyagertest",
            "field"    => [
                "id",
            ],
            "type"     => [
                "integer",
            ],
            "nullable" => [
                "0",
            ],
            "key" => [
                "PRI",
            ],
        ]);

        // Test redirected to correct page
        $this->assertRedirectedTo(route('voyager.database.index'), [
            'message'    => "Successfully created voyagertest table",
            'alert-type' => 'success',
        ]);

        // Test table exist
        $this->assertTrue(Schema::hasTable('voyagertest'));

        // Test column type
        $columns = DB::select(DB::raw('PRAGMA table_info(voyagertest)'));
        $this->assertEquals("1", $columns[0]->pk);

    }

    public function test_can_create_unique_key_column()
    {
        Auth::loginUsingId(1);

        $this->post(route('voyager.database.store'), [
            "name"     => "voyagertest",
            "field"    => [
                "name",
            ],
            "type"     => [
                "string",
            ],
            "key" => [
                "UNI",
            ],
        ]);

        // Test redirected to correct page
        $this->assertRedirectedTo(route('voyager.database.index'), [
            'message'    => "Successfully created voyagertest table",
            'alert-type' => 'success',
        ]);

        // Test table exist
        $this->assertTrue(Schema::hasTable('voyagertest'));

        // Test column type
        $columns = DB::select(DB::raw('PRAGMA INDEX_LIST(voyagertest)'));

        $this->assertEquals("1", $columns[0]->unique);

    }

    public function test_can_create_column_with_default_value()
    {
        Auth::loginUsingId(1);

        $this->post(route('voyager.database.store'), [
            "name"     => "voyagertest",
            "field"    => [
                "name",
            ],
            "type"     => [
                "string",
            ],
            "default" => [
                "this-is-a-default-value",
            ],
        ]);

        // Test redirected to correct page
        $this->assertRedirectedTo(route('voyager.database.index'), [
            'message'    => "Successfully created voyagertest table",
            'alert-type' => 'success',
        ]);

        // Test table exist
        $this->assertTrue(Schema::hasTable('voyagertest'));

        // Test column type
        $columns = DB::select(DB::raw('PRAGMA table_info(voyagertest)'));
        $this->assertEquals("'this-is-a-default-value'", $columns[0]->dflt_value);

    }
}
