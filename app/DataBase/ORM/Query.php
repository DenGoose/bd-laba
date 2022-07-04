<?php

namespace App\DataBase\ORM;

use Exception;
use App\DataBase\Tools;
use App\DataBase\DB;

class Query
{
	private string $tableName = '';

	private array $values = [];

	private array $select = [];
	private array $filter = [];
	private array $order = [];
	private int $limit = 0;
	private array $join = [];

	private ?\PDOStatement $result = null;

	/**
	 * @throws Exception
	 */
	public function __construct($tableName)
	{
		$isTable = DB::getInstance()->ifTableExists($tableName);

		if (!mb_strlen($tableName) || !$isTable)
		{
			throw new Exception("Error table name");
		}

		$this->tableName = $tableName;

	}

	public function where($column, string $value): Query
	{
		$hash = md5($value);

		$temp = explode('.', $column);

		$newColumnName = (count($temp) > 1) ? $column : $this->tableName . '.' . $column;

		$this->values[':' .$hash] = htmlspecialchars($value);

		$this->filter[] = "$newColumnName = :$hash";

		return $this;
	}

	public function whereIn($column, array $values): Query
	{
		$temp = explode('.', $column);
		$count = count($values);

		$newColumnName = (count($temp) > 1) ? $column : $this->tableName . '.' . $column;

		$str = "$newColumnName in (";

		foreach ($values as $key => $value)
		{
			$hash = md5($value);

			$this->values[':' .$hash] = htmlspecialchars($value);

			$str .= ':' . $hash;

			if ($key + 1 < $count)
			{
				$str .= ', ';
			}
		}

		$this->filter[] = $str . ')';

		return $this;
	}

	public function whereNot(string $column, string $value): Query
	{
		$hash = md5($value);

		$temp = explode('.', $column);

		$newColumnName = (count($temp) > 1) ? $column : $this->tableName . '.' . $column;

		$this->values[':' .$hash] = htmlspecialchars($value);

		$this->filter[] = "$newColumnName != :$hash";

		return $this;
	}

	public function addSelect(string $column, string $alias = ''): Query
	{
		$temp = explode('.', $column);

		$newColumnName = (count($temp) > 1) ? $column : $this->tableName . '.' . $column;

		if (mb_strlen($alias))
		{
			$this->select[] = "$newColumnName as $alias";
		}
		else
		{
			$this->select[] = "$newColumnName as " . str_replace('.', '_', $newColumnName) . "_ALIAS";
		}

		return $this;
	}

	public function addOrder($column, $value = "ASC"): Query
	{
		$temp = explode('.', $column);

		$newColumnName = (count($temp) > 1) ? $column : $this->tableName . '.' . $column;

		$this->order[] = "$newColumnName $value";

		return $this;
	}

	public function setLimit($limit): Query
	{
		$this->limit = $limit;

		return $this;
	}

	public function registerRuntimeField(string $alias, array $fields): Query
	{
		$this->join[$alias] = $fields;

		return $this;
	}

	public function getQuery(): string
	{
		$sql = "select";

		if (!$this->select || array_search('*', $this->select))
		{
			if ($this->join)
			{
				$tables = [$this->tableName => $this->tableName];

				foreach ($this->join as $alias => $join)
				{
					$tables[$alias] = $join["data_class"]::getTableName();
				}

				$columns = Tools::getMap($tables);

				foreach ($tables as $key => $alias)
				{
					foreach ($columns[$alias] as $column)
					{
						$this->select[] = ' ' . $key . '.' . $column['COLUMN_NAME'] . ' as ' . $key. '_'. $column['COLUMN_NAME'];
					}
				}

				$sql .= ' ' . implode(',', $this->select) . ' ';
			}
			else
			{
				$sql .= ' * ';
			}
		}
		else
		{
			$sql .= ' ' . implode(', ', $this->select) . ' ';
		}

		$sql .= "\nfrom `" . $this->tableName . "`\n";

		if ($this->join)
		{
			$arJoin = [];

			foreach ($this->join as $alias => $itm)
			{
				$joinStr = ($itm["join_type"]) ? ' ' . $itm["join_type"] : '';
				$joinStr .= " join " . $itm["data_class"]::getTableName() . ' as '. $alias . " on `$this->tableName`." .
					$itm["reference"]["this"] . ' = ' .
					$alias . '.' . $itm["reference"]["ref"] . ' ';

				$arJoin[] = $joinStr;
			}

			$sql .= implode("\n", $arJoin);
		}

		$sql .= ($this->filter) ? "\nwhere " . implode(' and ', $this->filter) : '';
		$sql .= ($this->order) ? "\norder by " . implode(', ', $this->order) : '';
		$sql .= ($this->limit) ? "\nlimit $this->limit" : '';

		return $sql;
	}

	/**
	 * @throws Exception
	 */
	public function exec(): Query
	{
		try
		{
			$this->result = DB::getInstance()->getConnection()->prepare($this->getQuery());

			$this->result->execute($this->values);

		} catch (\Throwable $e)
		{
			throw new Exception($e->getMessage());
		}

		return $this;
	}

	public function fetch()
	{

		return $this->result->fetch(\PDO::FETCH_ASSOC);
	}

	public function fetchAll(): array
	{
		return $this->result->fetchAll(\PDO::FETCH_ASSOC);
	}
}