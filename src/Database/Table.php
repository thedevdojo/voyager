<?php

namespace TCG\Voyager\Database;

use Doctrine\DBAL\Schema\Table as DoctrineTable;
use Doctrine\DBAL\Schema\Index;
use Doctrine\DBAL\Schema\SchemaException;

class Table
{
	protected $table;
	protected $originalKeys;
	protected $columns;
	protected $originalColumns;

	// todo: improve checking for existing columns
	//        improve adding new columns to the table. how can that be done?

	public function __construct($table)
	{
		if($table instanceof DoctrineTable) {
			$this->table = $table;
		} else if(is_string($table)) {
			$this->table = Schema::getDoctrineTable($table);
		} else {
			throw new \InvalidArgumentException("Invalid table");
		}

		if(! Schema::tableExists($this->name)) {
			throw SchemaException::tableDoesNotExist($this->name);
		}
		
		$this->columns = $this->setupColumns();
		$this->originalColumns = $this->setupColumns(true);
		$this->originalKeys = $this->setupKeys();
	}

	public function __get($property)
	{
		$getter = 'get' . ucfirst($property);

		if(! method_exists($this, $getter)) {
			throw new \Exception("Property {$property} doesn't exist or is unavailable");
		}

		return $this->$getter();
	}

	public function __set($property, $value)
	{
		$setter = 'set' . ucfirst($property);

		if(! method_exists($this, $setter)) {
			throw new \Exception("Property {$property} doesn't exist or is unavailable");
		}

		$this->$setter($value);
	}

	public function getName()
	{
		return $this->table->getName();
	}

	public function getColumns()
	{
		return $this->columns;
	}

	public function getOriginalColumns()
	{
		return $this->originalColumns;
	}

	public function getKeys()
	{
		return $this->setupKeys();
	}

	public function getOriginalKeys()
	{
		return $this->originalKeys;
	}

	protected function validateKey($key)
	{
		$availableKeys = [
			'',
			'PRI',
			'UNI',
			'MUL',
		];

		$key = strtoupper(trim($key));

		return in_array($key, $availableKeys) ? $key : false;
	}

	public function changeKey($column, $key)
	{
		// TODO: Add Foreign keys support?
			// hasForeignKey , getForeignKey , getForeignKeys , addForeignKeyConstraint , removeForeignKey

		if(($newKey = $this->validateKey($key)) === false) {
			throw new \InvalidArgumentException("Key {$key} is invalid");
		}

		$currentKey = $this->keys[$column];

		// if the key already exists
		if($currentKey && ($currentKey->type == $newKey)) {
			return;
		}

		// Drop current key
		$this->dropKey($currentKey);

		// Create new key
		$this->addKey($column, $newKey);
	}

	protected function dropKey($key)
	{
		switch ($key->type) {
			case 'PRI':
				$this->table->dropPrimaryKey();
				break;

			case 'UNI':
			case 'MUL':
				$this->table->dropIndex($key->name);
				break;
		}
	}

	protected function addKey($column, $key)
	{
		// NOTE: Composite keys are not supported for now
		$column = [$column];

		switch ($key) {
			case 'PRI':
				$this->table->setPrimaryKey($column);
				break;

			case 'UNI':
				$this->table->addUniqueIndex($column);
				break;

			case 'MUL':
				$this->table->addIndex($column);
				break;
		}
	}

	public function getDoctrineColumn($column)
	{
		return $this->table->getColumn($column);
	}

	public function hasColumn($column)
	{
		return $this->table->hasColumn($column);
	}

	protected function setupColumns($clone = false)
	{
		$doctrineColumns = $clone ? $this->cloneDoctrineColumns() : $this->table->getColumns();
		$columns = [];

		foreach ($doctrineColumns as $column) {
			$column = new Column($column, $this);
			$columns[$column->name] = $column;
		}

		return $columns;
	}

	protected function cloneDoctrineColumns()
	{
		$cloned = [];

		foreach ($this->table->getColumns() as $column) {
			$cloned[] = clone $column;
		}

		return $cloned;
	}

	protected function setupKeys()
	{
		// Note: this currently doesn't support Composite Keys

		if(! $this->columns) {
			return [];
		}

		$keys = [];

		foreach ($this->columns as $column) {
			$keys[$column->name] = null;
		}

		foreach ($this->table->getIndexes() as $indexName => $index) {
			// Get only the first column
			// This won't work for composite keys
			// For now, let's keep things simple
			$columnName = $index->getColumns()[0];

			$keys[$columnName] = (object) [
				'type' => $this->getIndexType($index),
				'name' => $indexName
			];
		}

		return $keys;
	}

	protected function getIndexType(Index $index)
	{
		if($index->isPrimary()) {
			return 'PRI';
		} else if($index->isUnique()) {
			return 'UNI';
		} else {
			return 'MUL';
		}
	}
}
