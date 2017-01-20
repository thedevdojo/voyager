<?php

namespace TCG\Voyager\Database;

use Doctrine\DBAL\Schema\Column as DoctrineColumn;
use Doctrine\DBAL\Types\Type;

class Column
{
	protected $column;
	protected $originalName;
	protected $newName;

	protected $table;

	public function __construct($column, $table)
	{
		if(!($table instanceof Table)) {
			$table = new Table($table);
		}

		$this->table = $table;

		// Get the doctrine column instance
		if($column instanceof Column) {
			$this->column = clone $column->getDoctrineColumn();
		} else if($column instanceof DoctrineColumn) {
			$this->column = $column;
		} else if(is_string($column)) {
			$this->column = $table->getDoctrineColumn($column);
		} else if(is_array($column)) {
			$name = $column['name'];
			$originalName = isset($column['originalName']) ? $column['originalName'] : $name;
			$type = Type::getType($column['type']);
			$extra = $column['extra'];
			$options = [
				'notnull' => !$column['null'],
				'default' => $column['default'],
				'autoincrement' => $extra['autoincrement'],
			];


			$this->column = new DoctrineColumn($name, $type, $options);
		} else {
			throw new \InvalidArgumentException("Invalid column");
		}

		$this->originalName = isset($originalName) ? $originalName : $this->name;

		if($this->name != $this->originalName) {
			$this->newName = $this->name;
		}
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

	public function getDoctrineColumn()
	{
		return $this->column;
	}

	public function getTable()
	{
		return $this->table;
	}

	public function setKey($key)
	{
		if($this->table) {
			$this->table->changeKey($this, $this->key, $key);
		} else {
			throw new \Exception("Cannot set key: the column {$this->name} doesn't have an associated table");
		}
	}

	public function setName($name)
	{
		if($name != $this->name) {
			$this->newName = $name;
		}
	}

	public function getName()
	{
		return isset($this->newName) ? $this->newName : $this->column->getName();
	}

	public function getOriginalName()
	{
		return $this->originalName;
	}

	public function getType()
	{
		return $this->column->getType()->getName();
	}

	public function getNull()
	{
		return ! $this->column->getNotNull();
	}

	public function getKey()
	{
		if($this->table) {
			return $this->table->getColumnKey($this->originalName);
		}

		throw new \Exception("Cannot get key: the column {$this->originalName} doesn't have an associated table");
	}

	public function getDefault()
	{
		return $this->column->getDefault();
	}

	public function getAutoincrement()
	{
		return $this->column->getAutoincrement();
	}

	public function getExtra()
	{
		return [
			'autoincrement' => $this->autoincrement,
		];
	}

	public function getOriginal()
	{
		if($this->table) {
			return $this->table->originalColumns[$this->originalName];
		}
		
		return null;
	}

	public function isNew()
	{
		return ! $this->table->hasColumn($this->originalName);
	}

	public function diffOriginal()
	{
		if($this->isNew()) {
			throw new \Exception("Column {$this->name} is a new column");
		}

		return $this->diff($this->getOriginal());
	}

	public function diff(Column $column)
	{
		// return diff between this and column
		// so only perform necessary actions based on what changed
		$properties = [
			'name',
			'type',
			'null',
			'default',
			'key',
			'autoincrement',
		];

		$diff = [];

		foreach ($properties as $property) {
			if($this->$property != $column->$property) {
				$diff[$property] = [
					'current' => $this->$property,
					'compare' => $column->$property
				];
			}
		}

		return $diff;
	}
}
