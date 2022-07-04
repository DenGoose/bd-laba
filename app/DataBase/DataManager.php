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
	public static function add($params): int
	{
		if (!$params)
		{
			throw new Exception("Empty parameters");
		}

		$fields = [];

		$columns = array_filter(array_keys($params), function($itm) {
			return !(mb_strtolower($itm) == 'id');
		});

		$sql = "insert into " . static::getTableName() . " (" . implode(', ', $columns) . ") values (";

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

		//TODO delete
	}
}