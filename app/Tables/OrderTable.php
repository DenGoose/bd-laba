<?php

namespace App\Tables;

class OrderTable extends \App\DataBase\DataManager
{
	public const PRODUCT_ORDERS_TABLE = 'product_orders';

	public static function getTableName(): string
	{
		return 'order';
	}
}