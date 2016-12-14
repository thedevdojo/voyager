<?php

namespace TCG\Voyager\Tests;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use TCG\Voyager\Http\Controllers\Traits\DatabaseQueryBuilder;

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

        // Create table
        $this->post(route('voyager.database.store'), [
            'name'     => 'voyagertest',
            'field'    => [
                'id',
                'tiny_int_field',
                'small_int_field',
                'med_int_field',
                'integer_field',
                'big_int_field',
                'string_field',
                'text_field',
                'med_text_field',
                'long_text_field',
                'float_field',
                'double_field',
                'decimal_field',
                'boolean_field',
                'enum_field',
                'date_field',
                'date_time_field',
                'time_field',
                'time_stamp_field',
                'binary_field',
                'created_at & updated_at',
            ],
            'type'     => [
                'integer',
                'tinyInteger',
                'smallInteger',
                'mediumInteger',
                'integer',
                'bigInteger',
                'string',
                'text',
                'mediumText',
                'longText',
                'float',
                'double',
                'decimal',
                'boolean',
                'enum',
                'date',
                'dateTime',
                'time',
                'timestamp',
                'binary',
                'timestamp',
            ],
            'enum'     => [
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                'valueA,valueB',
                '',
                '',
                '',
                '',
                '',
                '',
            ],
            'nullable' => [
                '0',
                '0',
                '0',
                '0',
                '0',
                '0',
                '0',
                '0',
                '0',
                '0',
                '0',
                '0',
                '0',
                '0',
                '0',
                '0',
                '0',
                '0',
                '0',
                '0',
                '0',
            ],
            'key'      => [
                'PRI',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
            ],
            'default'  => [
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                'CURRENT_TIMESTAMP',
            ],
        ]);

        // Test redirect to correct page
        $this->assertRedirectedToRoute('voyager.database.index', [], [
            'message'    => 'Successfully created voyagertest table',
            'alert-type' => 'success',
        ]);

        // Test table exist
        $this->assertTrue(Schema::hasTable('voyagertest'));

        // Test column type
        $columns = DB::select(DB::raw('PRAGMA table_info(voyagertest)'));
        $this->assertEquals('integer', $columns[0]->type);
        $this->assertEquals('integer', $columns[1]->type);
        $this->assertEquals('integer', $columns[2]->type);
        $this->assertEquals('integer', $columns[3]->type);
        $this->assertEquals('integer', $columns[4]->type);
        $this->assertEquals('integer', $columns[5]->type);
        $this->assertEquals('varchar', $columns[6]->type);
        $this->assertEquals('text', $columns[7]->type);
        $this->assertEquals('text', $columns[8]->type);
        $this->assertEquals('text', $columns[9]->type);
        $this->assertEquals('float', $columns[10]->type);
        $this->assertEquals('float', $columns[11]->type);
        $this->assertEquals('numeric', $columns[12]->type);
        $this->assertEquals('tinyint(1)', $columns[13]->type);
        $this->assertEquals('varchar', $columns[14]->type);
        $this->assertEquals('date', $columns[15]->type);
        $this->assertEquals('datetime', $columns[16]->type);
        $this->assertEquals('time', $columns[17]->type);
        $this->assertEquals('datetime', $columns[18]->type);
        $this->assertEquals('blob', $columns[19]->type);
        $this->assertEquals('datetime', $columns[20]->type);
        $this->assertEquals('datetime', $columns[21]->type);
    }

    public function test_can_create_nullable_column()
    {
        Auth::loginUsingId(1);

        // Create table
        $this->post(route('voyager.database.store'), [
            'name'     => 'voyagertest',
            'field'    => [
                'string_field',
            ],
            'type'     => [
                'string',
            ],
            'nullable' => [
                '1',
            ],
        ]);

        // Test redirect to correct page
        $this->assertRedirectedToRoute('voyager.database.index', [], [
            'message'    => 'Successfully created voyagertest table',
            'alert-type' => 'success',
        ]);

        // Test table exist
        $this->assertTrue(Schema::hasTable('voyagertest'));

        // Test column type
        $columns = DB::select(DB::raw('PRAGMA table_info(voyagertest)'));

        $this->assertEquals('0', $columns[0]->notnull); // inverse in sqlite (notnull)
    }

    public function test_can_create_primary_key_column()
    {
        Auth::loginUsingId(1);

        // Create table
        $this->post(route('voyager.database.store'), [
            'name'     => 'voyagertest',
            'field'    => [
                'id',
            ],
            'type'     => [
                'integer',
            ],
            'nullable' => [
                '0',
            ],
            'key'      => [
                'PRI',
            ],
        ]);

        /// Test redirect to correct page
        $this->assertRedirectedToRoute('voyager.database.index', [], [
            'message'    => 'Successfully created voyagertest table',
            'alert-type' => 'success',
        ]);

        // Test table exist
        $this->assertTrue(Schema::hasTable('voyagertest'));

        // Test column type
        $columns = DB::select(DB::raw('PRAGMA table_info(voyagertest)'));
        $this->assertEquals('1', $columns[0]->pk);
    }

    public function test_can_create_unique_key_column()
    {
        Auth::loginUsingId(1);

        // Create table
        $this->post(route('voyager.database.store'), [
            'name'  => 'voyagertest',
            'field' => [
                'name',
            ],
            'type'  => [
                'string',
            ],
            'key'   => [
                'UNI',
            ],
        ]);

        // Test redirect to correct page
        $this->assertRedirectedToRoute('voyager.database.index', [], [
            'message'    => 'Successfully created voyagertest table',
            'alert-type' => 'success',
        ]);

        // Test table exist
        $this->assertTrue(Schema::hasTable('voyagertest'));

        // Test column type
        $columns = DB::select(DB::raw('PRAGMA INDEX_LIST(voyagertest)'));

        $this->assertEquals('1', $columns[0]->unique);
    }

    public function test_can_create_column_with_default_value()
    {
        Auth::loginUsingId(1);

        // Create table
        $this->post(route('voyager.database.store'), [
            'name'    => 'voyagertest',
            'field'   => [
                'name',
            ],
            'type'    => [
                'string',
            ],
            'default' => [
                'this-is-a-default-value',
            ],
        ]);

        // Test redirect to correct page
        $this->assertRedirectedToRoute('voyager.database.index', [], [
            'message'    => 'Successfully created voyagertest table',
            'alert-type' => 'success',
        ]);

        // Test table exist
        $this->assertTrue(Schema::hasTable('voyagertest'));

        // Test column type
        $columns = DB::select(DB::raw('PRAGMA table_info(voyagertest)'));
        $this->assertEquals("'this-is-a-default-value'", $columns[0]->dflt_value);
    }

    public function test_can_update_table_with_same_fields()
    {
        Auth::loginUsingId(1);

        // Setup table
        $this->post(route('voyager.database.store'), [
            'name'  => 'voyagertest',
            'field' => [
                'id',
                'title',
                'address',
                'created_at & updated_at',
            ],
            'type'  => [
                'integer',
                'string',
                'text',
                'timestamp',
            ],
            'key'   => [
                'PRI',
                '',
                '',
                '',
            ],
        ]);

        // Test table exist
        $this->assertTrue(Schema::hasTable('voyagertest'));

        // Update table with same fields
        $this->put(route('voyager.database.update', ['voyagertest']), [
            'name'           => 'voyagertest',
            'original_name'  => 'voyagertest',
            'field'          => [
                'id',
                'title',
                'address',
                'created_at',
                'updated_at',
            ],
            'original_field' => [
                'id',
                'title',
                'address',
                'created_at',
                'updated_at',
            ],
            'delete_field'   => [
                '0',
                '0',
                '0',
                '0',
                '0',
            ],
            'type'           => [
                'integer',
                'string',
                'text',
                'timestamp',
                'timestamp',
            ],
            'key'            => [
                'PRI',
                '',
                '',
                '',
                '',
            ],
        ]);

        // Test redirect to correct page
        $this->assertRedirectedToRoute('voyager.database.index', [], [
            'message'    => 'Successfully updated voyagertest table',
            'alert-type' => 'success',
        ]);

        // Test table exist
        $this->assertTrue(Schema::hasTable('voyagertest'));

        $columns = DB::select(DB::raw('PRAGMA table_info(voyagertest)'));
        $this->assertEquals('id', $columns[0]->name);
        $this->assertEquals('title', $columns[1]->name);
        $this->assertEquals('address', $columns[2]->name);
        $this->assertEquals('created_at', $columns[3]->name);
        $this->assertEquals('updated_at', $columns[4]->name);
    }

    public function test_can_change_table_name()
    {
        Auth::loginUsingId(1);

        // Setup table
        $this->post(route('voyager.database.store'), [
            'name'  => 'voyagertest',
            'field' => [
                'id',
            ],
            'type'  => [
                'integer',
            ],
            'key'   => [
                'PRI',
            ],
        ]);

        // Test table exist
        $this->assertTrue(Schema::hasTable('voyagertest'));

        // Test update table with same fields
        $this->put(route('voyager.database.update', ['voyagertest']), [
            'name'          => 'voyagerpost',
            'original_name' => 'voyagertest',
        ]);

        // Test redirect to correct page
        $this->assertRedirectedToRoute('voyager.database.index', [], [
            'message'    => 'Successfully updated voyagerpost table',
            'alert-type' => 'success',
        ]);

        // Test table exist
        $this->assertTrue(Schema::hasTable('voyagerpost'));
    }

    public function test_can_change_column_name()
    {
        Auth::loginUsingId(1);

        // Setup table
        $this->post(route('voyager.database.store'), [
            'name'  => 'voyagertest',
            'field' => [
                'id',
                'title',
            ],
            'type'  => [
                'integer',
                'string',
            ],
            'key'   => [
                'PRI',
                '',
            ],
        ]);

        // Test table exist
        $this->assertTrue(Schema::hasTable('voyagertest'));

        // Update table with same fields
        $this->put(route('voyager.database.update', ['voyagertest']), [
            'name'           => 'voyagertest',
            'field'          => [
                'id',
                'headline',
            ],
            'original_field' => [
                'id',
                'title',
            ],
        ]);

        // Test redirect to correct page
        $this->assertRedirectedToRoute('voyager.database.index', [], [
            'message'    => 'Successfully updated voyagertest table',
            'alert-type' => 'success',
        ]);

        // Test table exist
        $this->assertTrue(Schema::hasTable('voyagertest'));

        $columns = DB::select(DB::raw('PRAGMA table_info(voyagertest)'));
        $this->assertEquals('id', $columns[0]->name);
        $this->assertEquals('headline', $columns[1]->name);
    }

    public function test_can_change_column_type()
    {
        Auth::loginUsingId(1);

        // Setup table
        $this->post(route('voyager.database.store'), [
            'name'  => 'voyagertest',
            'field' => [
                'id',
                'title',
                'content',
            ],
            'type'  => [
                'integer',
                'string',
                'text',
            ],
            'key'   => [
                'PRI',
                '',
                '',
            ],
        ]);

        // Test table exist
        $this->assertTrue(Schema::hasTable('voyagertest'));

        // Update table with same fields
        $this->put(route('voyager.database.update', ['voyagertest']), [
            'name'  => 'voyagertest',
            'field' => [
                'id',
                'title',
                'content',
            ],
            'type'  => [
                'integer',
                'text',
                'string',
            ],
            'key'   => [
                'PRI',
                '',
                '',
            ],
        ]);

        // Test redirect to correct page
        $this->assertRedirectedToRoute('voyager.database.index', [], [
            'message'    => 'Successfully updated voyagertest table',
            'alert-type' => 'success',
        ]);

        // Test table exist
        $this->assertTrue(Schema::hasTable('voyagertest'));

        // Test column types. Sqlite using Affinty that will change type name https://www.sqlite.org/datatype3.html
        $columns = DB::select(DB::raw('PRAGMA table_info(voyagertest)'));
        $this->assertEquals('INTEGER', $columns[0]->type);
        $this->assertEquals('CLOB', $columns[1]->type);
        $this->assertEquals('VARCHAR(255)', $columns[2]->type);
    }

    public function test_can_change_nullable()
    {
        Auth::loginUsingId(1);

        // Setup table
        $this->post(route('voyager.database.store'), [
            'name'     => 'voyagertest',
            'field'    => [
                'id',
                'title',
                'content',
            ],
            'type'     => [
                'integer',
                'string',
                'text',
            ],
            'key'      => [
                'PRI',
                '',
                '',
            ],
            'nullable' => [
                '0',
                '1',
                '0',
            ],
        ]);

        // Test table exist
        $this->assertTrue(Schema::hasTable('voyagertest'));

        // Update table with same fields
        $this->put(route('voyager.database.update', ['voyagertest']), [
            'name'     => 'voyagertest',
            'field'    => [
                'id',
                'title',
                'content',
            ],
            'type'     => [
                'integer',
                'text',
                'string',
            ],
            'key'      => [
                'PRI',
                '',
                '',
            ],
            'nullable' => [
                '0',
                '0',
                '1',
            ],
        ]);

        // Test redirect to correct page
        $this->assertRedirectedToRoute('voyager.database.index', [], [
            'message'    => 'Successfully updated voyagertest table',
            'alert-type' => 'success',
        ]);

        // Test table exist
        $this->assertTrue(Schema::hasTable('voyagertest'));

        // Test nullable changes
        $columns = DB::select(DB::raw('PRAGMA table_info(voyagertest)'));
        $this->assertEquals('1', $columns[0]->notnull);
        $this->assertEquals('1', $columns[1]->notnull);
        $this->assertEquals('0', $columns[2]->notnull);
    }

    public function test_can_change_key_to_unique()
    {
        Auth::loginUsingId(1);

        // Setup table
        $this->post(route('voyager.database.store'), [
            'name'  => 'voyagertest',
            'field' => [
                'id',
                'title',
                'content',
            ],
            'type'  => [
                'integer',
                'string',
                'text',
            ],
            'key'   => [
                'PRI',
                '',
                '',
            ],
        ]);

        // Test table exist
        $this->assertTrue(Schema::hasTable('voyagertest'));

        // Update table with same fields
        $this->put(route('voyager.database.update', ['voyagertest']), [
            'name'  => 'voyagertest',
            'field' => [
                'id',
                'title',
                'content',
            ],
            'type'  => [
                'integer',
                'text',
                'string',
            ],
            'key'   => [
                'PRI',
                'UNI',
                '',
            ],
        ]);

        // Test redirect to correct page
        $this->assertRedirectedToRoute('voyager.database.index', [], [
            'message'    => 'Successfully updated voyagertest table',
            'alert-type' => 'success',
        ]);

        // Test table exist
        $this->assertTrue(Schema::hasTable('voyagertest'));

        $columns = DB::select(DB::raw('PRAGMA INDEX_LIST(voyagertest)'));
        $this->assertEquals('1', $columns[0]->unique);
    }

    // We can only one PRIMARY key actually.
    public function test_can_change_to_add_primary_key()
    {
        Auth::loginUsingId(1);

        // Setup table
        $this->post(route('voyager.database.store'), [
            'name'  => 'voyagertest',
            'field' => [
                'id',
                'title',
                'content',
            ],
            'type'  => [
                'integer',
                'string',
                'text',
            ],
            'key'   => [
                'PRI',
                '',
                '',
            ],
        ]);

        // Test table exist
        $this->assertTrue(Schema::hasTable('voyagertest'));

        // Update table with same fields
        $this->put(route('voyager.database.update', ['voyagertest']), [
            'name'  => 'voyagertest',
            'field' => [
                'id',
                'title',
                'content',
            ],
            'type'  => [
                'integer',
                'text',
                'string',
            ],
            'key'   => [
                'PRI',
                'PRI',
                '',
            ],
        ]);

        // Test redirect to correct page
        $this->assertRedirectedToRoute('voyager.database.index', [], [
            'message'    => 'Successfully updated voyagertest table',
            'alert-type' => 'success',
        ]);

        // Test table exist
        $this->assertTrue(Schema::hasTable('voyagertest'));

        $columns = DB::select(DB::raw('PRAGMA table_info(voyagertest)'));
        $this->assertEquals('1', $columns[0]->pk);
        $this->assertEquals('0', $columns[1]->pk);
        $this->assertEquals('0', $columns[2]->pk);
    }

    public function test_can_drop_column()
    {
        Auth::loginUsingId(1);

        // Setup table
        $this->post(route('voyager.database.store'), [
            'name'  => 'voyagertest',
            'field' => [
                'id',
                'title',
                'content',
            ],
            'type'  => [
                'integer',
                'string',
                'text',
            ],
            'key'   => [
                'PRI',
                '',
                '',
            ],
        ]);

        // Test table exist
        $this->assertTrue(Schema::hasTable('voyagertest'));

        // Update table with same fields
        $this->put(route('voyager.database.update', ['voyagertest']), [
            'name'           => 'voyagertest',
            'field'          => [
                'id',
                'title',
                'content',
            ],
            'original_field' => [
                'id',
                'title',
                'content',
            ],
            'delete_field'   => [
                '0',
                '0',
                '1',
            ],
        ]);

        // Test redirect to correct page
        $this->assertRedirectedToRoute('voyager.database.index', [], [
            'message'    => 'Successfully updated voyagertest table',
            'alert-type' => 'success',
        ]);

        // Test table exist
        $this->assertTrue(Schema::hasTable('voyagertest'));

        // Test total columns after dropped
        $columns = DB::select(DB::raw('PRAGMA table_info(voyagertest)'));
        $this->assertEquals(2, collect($columns)->count());
    }

    public function test_can_change_default_value()
    {
        Auth::loginUsingId(1);

        // Setup table
        $this->post(route('voyager.database.store'), [
            'name'  => 'voyagertest',
            'field' => [
                'id',
                'title',
                'content',
            ],
            'type'  => [
                'integer',
                'string',
                'text',
            ],
            'key'   => [
                'PRI',
                '',
                '',
            ],
        ]);

        // Test table exist
        $this->assertTrue(Schema::hasTable('voyagertest'));

        // Update table with same fields
        $this->put(route('voyager.database.update', ['voyagertest']), [
            'name'    => 'voyagertest',
            'field'   => [
                'id',
                'title',
                'content',
            ],
            'default' => [
                '',
                'this is a new default value',
                'another default value',
            ],
        ]);

        // Test redirect to correct page
        $this->assertRedirectedToRoute('voyager.database.index', [], [
            'message'    => 'Successfully updated voyagertest table',
            'alert-type' => 'success',
        ]);

        // Test table exist
        $this->assertTrue(Schema::hasTable('voyagertest'));

        // Test total columns after dropped
        $columns = DB::select(DB::raw('PRAGMA table_info(voyagertest)'));
        $this->assertEquals('', $columns[0]->dflt_value);
        $this->assertEquals("'this is a new default value'", $columns[1]->dflt_value);
        $this->assertEquals("'another default value'", $columns[2]->dflt_value);
    }

    public function test_can_delete_table()
    {
        Auth::loginUsingId(1);

        // Setup table
        $this->post(route('voyager.database.store'), [
            'name'  => 'voyagertest',
            'field' => [
                'id',
                'title',
                'content',
            ],
            'type'  => [
                'integer',
                'string',
                'text',
            ],
            'key'   => [
                'PRI',
                '',
                '',
            ],
        ]);

        // Test table exist
        $this->assertTrue(Schema::hasTable('voyagertest'));

        $this->delete(route('voyager.database.destroy', ['voyagertest']));

        // Test redirect to correct page
        $this->assertRedirectedToRoute('voyager.database.index', [], [
            'message'    => 'Successfully deleted voyagertest table',
            'alert-type' => 'success',
        ]);

        // Test table exist
        $this->assertFalse(Schema::hasTable('voyagertest'));
    }
}
