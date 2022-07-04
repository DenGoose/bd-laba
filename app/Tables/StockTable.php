<?php

namespace App\Tables;

class StockTable extends \App\DataBase\DataManager
{
	public const PRODUCT_STOCK_TABLE = 'product_stock';

	public static function getTableName(): string
	{
		return 'stock';
	}
}