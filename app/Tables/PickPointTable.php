<?php

namespace App\Tables;

class PickPointTable extends \App\DataBase\DataManager
{
	public static function getTableName(): string
	{
		return "pick_point";
	}
}