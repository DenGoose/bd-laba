<?php

namespace App\Controller;

use App\Controller;
use App\Tables\OrderTable;
use App\Tables\PickPointTable;
use App\Tables\ProductTable;
use App\Tables\UserTable;
use App\ViewManager;

class OrderController extends Controller
{
	/**
	 * @throws \Exception
	 */
	public static function show(): string
	{
		ViewManager::show('header', ['title' => 'Заказы']);

		$result['currentUrl'] = $_SERVER['REQUEST_URI'];
		$result['result'] = [
			'columns' => [
				'ID', 'Покупатель', 'Пункт выдачи', 'Сумма', 'Товары'
			]
		];

		$ob = OrderTable::query()
			->registerRuntimeField('USER', [
				'data_class' => UserTable::class,
				'reference' => [
					'this' => 'USER_ID',
					'ref' => 'ID',
				],
				'join_type' => 'inner'
			])
			->registerRuntimeField('PICK_POINT', [
				'data_class' => PickPointTable::class,
				'reference' => [
					'this' => 'PICK_POINT_ID',
					'ref' => 'ID',
				],
				'join_type' => 'inner'
			])
			->registerRuntimeField('PRODUCT_ORDERS', [
				'data_class' => OrderTable::PRODUCT_ORDERS_TABLE,
				'reference' => [
					'this' => 'ID',
					'ref' => 'ORDER_ID',
				],
				'join_type' => 'inner'
			])
			->registerRuntimeField('PRODUCT', [
				'data_class' => ProductTable::class,
				'reference' => [
					'this' => 'PRODUCT_ORDERS.PRODUCT_ID',
					'ref' => 'ID',
				],
				'join_type' => 'inner'
			])
			->addSelect('order.ID', 'ORDER_ID')
			->addSelect('order.TOTAL_PRICE', 'ORDER_TOTAL_PRICE')
			->addSelect('USER.SECOND_NAME', 'USER_SECOND_NAME')
			->addSelect('USER.ID', 'USER_ID')
			->addSelect('PICK_POINT.ADDRESS', 'PICK_POINT_ADDRESS')
			->addSelect('PRODUCT.NAME', 'PRODUCT_NAME')
			->addOrder('order.ID', 'ASC');

		$query = $ob->getQuery();
		$users = $ob->exec();
		$orders = [];

		while ($itm = $users->fetch())
		{
			if (!isset($orders[$itm['ORDER_ID']]))
			{
				$orders[$itm['ORDER_ID']] = [
					'ORDER_ID' => $itm['ORDER_ID'],
					'USER_SECOND_NAME' => $itm['USER_SECOND_NAME'] . " (${itm['USER_ID']})",
					'PICK_POINT_ADDRESS' => $itm['PICK_POINT_ADDRESS'],
					'TOTAL_PRICE' => $itm['ORDER_TOTAL_PRICE']
				];
			}
			$orders[$itm['ORDER_ID']]['PRODUCTS'][] = $itm['PRODUCT_NAME'];
		}

		foreach ($orders as $order)
		{
			$result['result']['items'][] = [
				'ID' => $order['ORDER_ID'],
				'USER_SECOND_NAME' => $order['USER_SECOND_NAME'],
				'PICK_POINT_ADDRESS' => $order['PICK_POINT_ADDRESS'],
				'TOTAL_PRICE' => $order['TOTAL_PRICE'],
				'PRODUCTS' => '<p>' . implode('</p><p>', $order['PRODUCTS']) . '</p>'
			];
		}

		$arQuery = [];
		if (isset($_SESSION['dbQuery']) && mb_strlen($_SESSION['dbQuery']))
		{
			$arQuery[] = $_SESSION['dbQuery'];
			unset($_SESSION['dbQuery']);
		}
		$arQuery[] = $query;
		ViewManager::show('query', ['query' => $arQuery]);
		ViewManager::show('table', $result);
		ViewManager::show('footer');
		return '';
	}

	public static function add()
	{
		ViewManager::show('header', ['title' => 'Заказы']);
		ViewManager::show('footer');
		return '';
	}

	public static function addAction()
	{

	}

	public static function update()
	{
		ViewManager::show('header', ['title' => 'Заказы']);
		ViewManager::show('footer');
		return '';
	}

	public static function updateAction()
	{

	}

	public static function deleteAction()
	{

	}
}