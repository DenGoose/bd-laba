<?php

namespace App\Tables;

class SectionTable extends \App\DataBase\DataManager
{
	public static function getTableName(): string
	{
		return 'product_section';
	}
}