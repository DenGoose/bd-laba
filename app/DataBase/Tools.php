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
}