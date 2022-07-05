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
					$tables[$alias] = class_exists($join["data_class"]) ? $join["data_class"]::getTableName() : $join["data_class"];
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
				$class = class_exists($itm["data_class"]) ? $itm["data_class"]::getTableName() : $itm["data_class"];
				$ref = explode('.', $itm['reference']['this']);
				$refTable = count($ref) > 1 ? $ref[0] : $this->tableName;
				$refOn = count($ref) > 1 ? $ref[1] : $itm["reference"]["this"];
				$joinStr = ($itm["join_type"]) ? ' ' . $itm["join_type"] : '';
				$joinStr .= " join " . $class . ' as '. $alias . " on `${refTable}`." .
					$refOn . ' = ' .
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
			$sql = $this->getQuery();

			$tempSql = $sql;
			foreach ($this->values as $alias => $field)
			{
				$tempSql = str_replace($alias, $field, $tempSql);
			}
			$_SESSION['dbQuery'][] = $tempSql;

			$this->result = DB::getInstance()->getConnection()->prepare($sql);

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