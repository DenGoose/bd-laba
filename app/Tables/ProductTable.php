<?php

namespace App\Tables;

class ProductTable extends \App\DataBase\DataManager
{
	public const PRODUCT_ORDERS_TABLE = 'product_orders';
	public const PRODUCT_STOCK_TABLE = 'product_stock';

	public static function getTableName(): string
	{
		return 'product';
	}
}