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

	public static function recalculateOrders($oldPrice, $newPrice, $productId)
	{
		$diffPrice = $oldPrice - $newPrice;
		$ob = OrderTable::query()
			->registerRuntimeField('PRODUCT_ORDERS', [
				'data_class' => OrderTable::PRODUCT_ORDERS_TABLE,
				'reference' => [
					'this' => 'ID',
					'ref' => 'ORDER_ID',
				],
				'join_type' => 'inner'
			])
			->where('PRODUCT_ORDERS.PRODUCT_ID', $productId)
			->addSelect('TOTAL_PRICE', 'ORDER_TOTAL_PRICE')
			->addSelect('ID', 'ORDER_ID');

		$_SESSION['dbQuery'][] = $ob->getQuery();

		$orders = $ob->exec();

		while ($itm = $orders->fetch())
		{
			OrderTable::update($itm['ORDER_ID'], ['TOTAL_PRICE' => $itm['ORDER_TOTAL_PRICE'] - $diffPrice]);
		}
	}
}