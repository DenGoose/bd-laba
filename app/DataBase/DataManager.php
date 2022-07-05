<?php

namespace App\DataBase;

use Exception;
use App\DataBase\Tools;
use App\DataBase\ORM\Query;

abstract class DataManager
{
	public static function getTableName(): string
	{
		return '';
	}

	/**
	 * @return array
	 */
	public static function getMap(): array
	{
		return Tools::getMap(static::getTableName())[static::getTableName()];
	}

	/**
	 * @return Query
	 * @throws Exception
	 */
	public static function query(): Query
	{
		return new Query(static::getTableName());
	}

	/**
	 * @param $params
	 * @return int
	 * @throws Exception
	 */
	public static function add($params, $table = ''): int
	{
		if (!$params)
		{
			throw new Exception("Empty parameters");
		}

		if (!mb_strlen($table))
		{
			$table = static::getTableName();
		}

		$fields = [];

		$columns = array_filter(array_keys($params), function($itm) {
			return !(mb_strtolower($itm) == 'id');
		});

		$sql = "insert into `" . $table . "` (" . implode(', ', $columns) . ") values (";

		$lastItm = array_pop($columns);

		foreach ($params as $key => $itm)
		{
			if (!(mb_strtolower($key) == 'id'))
			{
				$hash = md5($itm);
				$fields[':' . $hash] = $itm;

				$sql .= ($key != $lastItm) ? ":$hash, " : ':' . $hash;
			}
		}

		$sql .= ")";

		$tempSql = $sql;
		foreach ($fields as $alias => $field)
		{
			$tempSql = str_replace($alias, $field, $tempSql);
		}
		$_SESSION['dbQuery'][] = $tempSql;

		$db = DB::getInstance()->getConnection();

		$stmt = $db->prepare($sql);

		$stmt->execute($fields);

		return $db->lastInsertId();
	}

	/**
	 * @param $id
	 * @param $params
	 * @return bool
	 * @throws Exception
	 */
	public static function update($id, $params): bool
	{
		if (!intval($id) || !$$params)
		{
			throw new Exception("Empty parameters");
		}

		// TODO update
	}

	/**
	 * @throws Exception
	 */
	public static function delete($id): bool
	{
		if (!intval($id))
		{
			throw new Exception("Empty parameters");
		}


		$sql = 'delete from `' . static::getTableName() . '` where ID = :id';


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