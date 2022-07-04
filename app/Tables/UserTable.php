<?php

namespace App\Tables;

use App\DataBase\DataManager;

class UserTable extends DataManager
{
	public static function getTableName(): string
	{
		return 'user';
	}
}