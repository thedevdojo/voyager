<?php

namespace TCG\Voyager\Database;

use Doctrine\DBAL\Schema\Table as DoctrineTable;
use Doctrine\DBAL\Schema\Index;
use Doctrine\DBAL\Schema\SchemaException;

class Table
{
	protected $table;
	protected $keys;
	protected $columns;
	protected $originalColumns;

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
		
		$this->keys = $this->setupKeys();
		$this->columns = $this->setupColumns();
		$this->originalColumns = $this->setupColumns(true);
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
		return $this->keys;
	}

	public function changeKey(Column $column, $fromKey, $toKey)
	{
		// TODO: learn more about Indexes
		// implement this
		if($fromKey == $toKey) {
			return;
		}

		$this->dropKey($column, $fromKey);
		$this->addKey($column, $toKey);
	}

	public function dropKey(Column $column, $key)
	{

	}

	public function addKey(Column $column, $key)
	{

	}

	public function getDoctrineColumn($column)
	{
		return $this->table->getColumn($column);
	}

	public function hasColumn($column)
	{
		return $this->table->hasColumn($column);
	}

	public function getColumnKey($column)
	{
		if(! $this->hasColumn($column)) {
            throw SchemaException::columnDoesNotExist($column, $this->name);
		}

		if( in_array($column, $this->keys['PRI']) ) {
			return 'PRI';
		}

		if( in_array($column, $this->keys['UNI']) ) {
			return 'UNI';
		}

		if( in_array($column, $this->keys['MUL']) ) {
			return 'MUL';
		}

		return null;
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
		$keys = [
			'PRI' => [],
			'UNI' => [],
			'MUL' => []
		];
		
		foreach ($this->table->getIndexes() as $name => $index) {
			$columns = $index->getColumns();

			if(count($columns) == 1) {
				$columns = $columns[0];
			}

			$keys[$this->getIndexType($index)][$name] = $columns;
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
