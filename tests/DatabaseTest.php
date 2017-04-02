<?php

namespace TCG\Voyager\Tests;

use Doctrine\DBAL\Schema\SchemaException;
use Illuminate\Support\Facades\Auth;
use TCG\Voyager\Database\Schema\SchemaManager;
use TCG\Voyager\Database\Schema\Table;
use TCG\Voyager\Database\Types\Type;
use TCG\Voyager\Traits\AlertsMessages;

class DatabaseTest extends TestCase
{
    use AlertsMessages;

    protected $table;

    public function setUp()
    {
        parent::setUp();

        $this->install();

        // todo: make sure tests are isolated and do not effect other ones
        // todo: interract with Table object directly instead of array?
        // todo: maybe perform the updates using one call to update_table?
        Type::registerCustomPlatformTypes(true);
        Auth::loginUsingId(1);

        // Prepare table
        $newTable = new Table('test_table_new');

        $newTable->addColumn('id', 'integer', [
            'autoincrement' => true,
        ]);

        $newTable->addColumn('details', 'json', [
            'notnull' => true,
        ]);

        $newTable->setPrimaryKey(['id'], 'primary');

        $this->table = $newTable->toArray();

        // Create table
        $this->post(route('voyager.database.store'), [
            'table' => json_encode($this->table),
        ]);
    }

    public function test_table_created_successfully()
    {
        // Test correct response
        $this->assertSessionHasAll($this->alertSuccess("Successfully created {$this->table['name']} table"));
        $this->assertRedirectedToRoute('voyager.database.edit', $this->table['name']);

        // Test table exists
        $this->assertTrue(SchemaManager::tableExists($this->table['name']));

        // Test database table details to be correct
        $dbTable = SchemaManager::listTableDetails($this->table['name']);

        $id = $dbTable->getColumn('id');
        $details = $dbTable->getColumn('details');
        // Column Type
        $this->assertEquals('integer', $id->getType()->getName());
        $this->assertEquals('json', $details->getType()->getName());
        // Column auto increment
        $this->assertTrue($id->getAutoIncrement());
        // Column not null
        $this->assertTrue($details->getNotnull());

        // Test Index
        $primary = $dbTable->getPrimaryKey();
        $this->assertEquals('primary', $primary->getName());

        // Test creating a table that already exists
        $this->expectExceptionMessage("table {$this->table['name']} already exists");
        SchemaManager::createTable($this->table);
    }

    /* Table Update tests */

    public function test_can_update_table()
    {
        $this->update_table_that_not_exist();

        $this->can_add_column();

        $this->can_change_column_type();

        $this->can_change_column_options();

        $this->can_add_index();

        $this->can_change_index();

        $this->can_rename_column();

        $this->can_drop_column();

        $this->can_rename_table();
    }

    public function test_can_drop_table()
    {
        $this->assertTrue(SchemaManager::tableExists($this->table['name']));

        $this->delete(route('voyager.database.destroy', $this->table['name']));

        // Test correct response
        $this->assertSessionHasAll($this->alertSuccess("Successfully deleted {$this->table['name']} table"));
        $this->assertRedirectedToRoute('voyager.database.index');

        $this->assertFalse(SchemaManager::tableExists($this->table['name']));
    }

    protected function update_table_that_not_exist()
    {
        $table = (new Table('i_dont_exist_please_create_me_first'))->toArray();

        $this->put(route('voyager.database.update', $table['oldName']), [
            'table' => json_encode($table),
        ]);

        $this->assertSessionHasAll(
            $this->alertException(SchemaException::tableDoesNotExist($table['name']))
        );
    }

    protected function can_rename_table()
    {
        $this->table['name'] = 'table_new_name_test';

        $this->update_table($this->table);

        $this->assertFalse(SchemaManager::tableExists($this->table['oldName']));
        $this->assertTrue(SchemaManager::tableExists($this->table['name']));
    }

    protected function can_add_column()
    {
        $dbTable = SchemaManager::listTableDetails($this->table['name']);

        $column = 'new_voyager_column';
        $dbTable->addColumn($column, 'text', [
            'notnull' => false,
        ]);

        $dbTable = $this->update_table($dbTable->toArray());

        $this->assertTrue($dbTable->hasColumn($column));
        $this->assertEquals('text', $dbTable->getColumn($column)->getType()->getName());
    }

    protected function can_rename_column()
    {
        $column = 1;
        $oldColumn = $this->table['columns'][$column]['oldName'];
        $newColumn = 'details_renamed_test';
        $this->table['columns'][$column]['name'] = $newColumn;

        $dbTable = $this->update_table($this->table);

        $this->assertFalse($dbTable->hasColumn($oldColumn));
        $this->assertTrue($dbTable->hasColumn($newColumn));
    }

    protected function can_change_column_type()
    {
        $column = 1;
        $columnName = $this->table['columns'][$column]['name'];
        $newType = 'text';
        $oldType = $this->table['columns'][$column]['type']['name'];

        $this->assertTrue($oldType != $newType);

        $this->table['columns'][$column]['type']['name'] = $newType;

        $dbTable = $this->update_table($this->table);

        $this->assertEquals($newType, $dbTable->getColumn($columnName)->getType()->getName());
    }

    protected function can_change_column_options()
    {
        $column = 1;
        $columnName = $this->table['columns'][$column]['name'];

        $notnull = false;
        $default = 'voyager admin';

        $this->table['columns'][$column]['notnull'] = $notnull;
        $this->table['columns'][$column]['default'] = $default;

        $dbTable = $this->update_table($this->table);
        $column = $dbTable->getColumn($columnName);

        $this->assertEquals($notnull, $column->getNotnull());
        $this->assertEquals($default, $column->getDefault());
    }

    protected function can_drop_column()
    {
        $column = 1;
        $columnName = $this->table['columns'][$column]['name'];

        $dbTable = SchemaManager::listTableDetails($this->table['name']);

        $this->assertTrue($dbTable->hasColumn($columnName));

        unset($this->table['columns'][$column]);

        $dbTable = $this->update_table($this->table);

        $this->assertFalse($dbTable->hasColumn($columnName));
    }

    protected function can_add_index()
    {
        $dbTable = SchemaManager::listTableDetails($this->table['name']);

        $indexName = 'details_unique';
        $dbTable->addUniqueIndex(['details'], $indexName);

        $dbTable = $this->update_table($dbTable->toArray());

        $this->assertTrue($dbTable->hasIndex($indexName));
        $this->assertTrue($dbTable->getIndex($indexName)->isUnique());
    }

    protected function can_change_index()
    {
        $dbTable = SchemaManager::listTableDetails($this->table['name']);

        $this->assertTrue($dbTable->hasIndex('primary'));

        $dbTable->dropPrimaryKey();
        $dbTable->addIndex(['id'], 'id_index');

        $dbTable = $this->update_table($dbTable->toArray());

        $this->assertFalse($dbTable->hasIndex('primary'));
        $this->assertTrue($dbTable->hasIndex('id_index'));
    }

    protected function update_table(array $table)
    {
        // Update table
        $this->put(route('voyager.database.update', $table['oldName']), [
            'table' => json_encode($table),
        ]);

        // Test correct response
        $this->assertSessionHasAll($this->alertSuccess("Successfully updated {$table['name']} table"));
        $this->assertRedirectedToRoute('voyager.database.edit', $table['name']);

        return SchemaManager::listTableDetails($table['name']);
    }
}
