<?php

namespace App\Controller;

use App\Controller;
use App\Tables\OrderTable;
use App\Tables\PickPointTable;
use App\Tables\UserTable;
use App\ViewManager;

class OrderController extends Controller
{
	/**
	 * @throws \Exception
	 */
	public static function show()
	{
		ViewManager::show('header', ['title' => 'Заказы']);

		$result['currentUrl'] = $_SERVER['REQUEST_URI'];
		$result['result'] = [
			'columns' => [
				'ID', 'Покупатель', 'Пункт выдачи', 'Сумма'
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
			->addSelect('order.ID', 'ORDER_ID')
			->addSelect('order.TOTAL_PRICE', 'ORDER_TOTAL_PRICE')
			->addSelect('USER.SECOND_NAME', 'USER_SECOND_NAME')
			->addSelect('USER.ID', 'USER_ID')
			->addSelect('PICK_POINT.ADDRESS', 'PICK_POINT_ADDRESS')
			->addOrder('order.ID', 'ASC');

		$query = $ob->getQuery();
		$users = $ob->exec();

		while ($itm = $users?->fetch())
		{
			$result['result']['items'][] = [
				'ID' => $itm['ORDER_ID'],
				'USER_SECOND_NAME' => $itm['USER_SECOND_NAME'] . " (${itm['USER_ID']})",
				'PICK_POINT_ADDRESS' => $itm['PICK_POINT_ADDRESS'],
				'TOTAL_PRICE' => $itm['ORDER_TOTAL_PRICE'],
			];
		}

		ViewManager::show('query', ['query' => [$query]]);
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