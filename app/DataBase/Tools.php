<?php

namespace App\DataBase;

class Tools
{
	/**
	 * @param string|array $tableName
	 * @return array|false
	 */
	public static function getMap($tableName)
	{
		$sql = "select TABLE_NAME, COLUMN_NAME, DATA_TYPE from information_schema.COLUMNS where TABLE_NAME ";

		if (is_array($tableName) && $tableName)
		{
			$sql .= "in ('". implode("', '", $tableName) ."')";
		}
		elseif (is_string($tableName) && mb_strlen($tableName))
		{
			$sql .= "= '$tableName'";
		}

		$ob = DB::getInstance()
			->getConnection()
			->query($sql);

		$result = [];

		while ($itm = $ob->fetch(\PDO::FETCH_ASSOC))
		{
			$result[$itm["TABLE_NAME"]][] = $itm;
		}

		return $result ?? [];
	}

	public static function getSum($table, $field, $ids)
	{
		$sql = "select sum(${field}) as SUM from ${table} where ID in (";
		$prepare = [];
		foreach ($ids as $item)
		{
			$alias = ':' . md5(time() + $item);
			$prepare[$alias] = $item;
		}

		$sql .= implode(', ', array_keys($prepare)) . ')';

		$_SESSION['dbQuery'][] = 'select sum(${field}) as SUM from ${table} where ID in ('. implode(', ', $prepare) . ')';

		$smt = DB::getInstance()->getConnection()->prepare($sql);
		$smt->execute($prepare);

		return $smt->fetch(\PDO::FETCH_ASSOC)['SUM'];
	}

	public static function deleteForManyToMany($table, $fieldId, $id): bool
	{
		$sql = "delete from `" . $table . "` where ${fieldId} = :id";

		$db = DB::getInstance()->getConnection();

		$stmt = $db->prepare($sql);

		$tempSql = $sql;
		$alias = ':id';
		$field = $id;
		$tempSql = str_replace($alias, $field, $tempSql);
		$_SESSION['dbQuery'][] = $tempSql;

		return $stmt->execute([':id' => $id]);
	}
}