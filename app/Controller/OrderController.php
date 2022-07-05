<?php

namespace App\Controller;

use App\Controller;
use App\DataBase\Tools;
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
					'TOTAL_PRICE' => $itm['ORDER_TOTAL_PRICE'] // todo удалить колонку, считать на лету (пока что костыль)
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
		if (isset($_SESSION['dbQuery']) && $_SESSION['dbQuery'])
		{
			$arQuery = $_SESSION['dbQuery'];
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
		ViewManager::show('header', ['title' => 'Добавление заказа']);

		$query = [];

		$ob = UserTable::query()
			->addSelect('ID', 'USER_ID')
			->addSelect('NAME', 'USER_NAME')
			->addSelect('SECOND_NAME', 'USER_SECOND_NAME')
			->addSelect('LAST_NAME', 'USER_LAST_NAME');

		$query[] = $ob->getQuery();
		$usersObj = $ob->exec();
		$users = [];

		while ($itm = $usersObj->fetch())
		{
			$users[] = [
				'id' => $itm['USER_ID'],
				'name' => implode(' ', [$itm['USER_NAME'], $itm['USER_SECOND_NAME'], $itm['USER_LAST_NAME']])
			];
		}

		$ob = PickPointTable::query()
			->addSelect('ID', 'PICK_POINT_ID')
			->addSelect('ADDRESS', 'PICK_POINT_ADDRESS');

		$query[] = $ob->getQuery();
		$pickPointObj = $ob->exec();
		$pickPoints = [];

		while ($itm = $pickPointObj->fetch())
		{
			$pickPoints[] = [
				'id' => $itm['PICK_POINT_ID'],
				'name' => $itm['PICK_POINT_ADDRESS']
			];
		}

		$ob = ProductTable::query()
			->addSelect('ID', 'PRODUCT_ID')
			->addSelect('NAME', 'PRODUCT_NAME');

		$query[] = $ob->getQuery();
		$productsObj = $ob->exec();
		$products = [];

		while ($itm = $productsObj->fetch())
		{
			$products[] = [
				'id' => $itm['PRODUCT_ID'],
				'name' => $itm['PRODUCT_NAME']
			];
		}

		$result['result'] = [
			'action' => '/order/add/',
			'items' => [
				[
					'name' => 'Покупатель',
					'code' => 'USER',
					'type' => 'list',
					'value' => '',
					'list_values' => $users
				],
				[
					'name' => 'Пункт выдачи',
					'code' => 'PICK_POINT',
					'type' => 'list',
					'value' => '',
					'list_values' => $pickPoints
				],
				[
					'name' => 'Товары',
					'code' => 'PRODUCT',
					'type' => 'multiple_list',
					'value' => [],
					'list_values' => $products
				],
			],
		];
		ViewManager::show('query', ['query' => $query]);
		ViewManager::show('record', $result);

		ViewManager::show('footer');
		return '';
	}

	public static function addAction()
	{
		if (!$_POST['PRODUCT'])
		{
			header('Location: /order/');
			die();
		}

		$sum = Tools::getSum(ProductTable::getTableName(), 'PRICE', $_POST['PRODUCT']); // todo удалить колонку, считать на лету (пока что костыль)

		$orderId = OrderTable::add([
			'USER_ID' => $_POST['USER'],
			'PICK_POINT_ID' => $_POST['PICK_POINT'],
			'TOTAL_PRICE' => $sum
		]);

		foreach ($_POST['PRODUCT'] as $item)
		{
			OrderTable::add([
				'PRODUCT_ID' => $item,
				'ORDER_ID' => $orderId
			], OrderTable::PRODUCT_ORDERS_TABLE);
		}

		header('Location: /order/');
		die();
	}

	public static function update()
	{
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
			->where('ID', $_GET['id'])
			->addSelect('order.ID', 'ORDER_ID')
			->addSelect('order.TOTAL_PRICE', 'ORDER_TOTAL_PRICE')
			->addSelect('USER.SECOND_NAME', 'USER_SECOND_NAME')
			->addSelect('USER.ID', 'USER_ID')
			->addSelect('PICK_POINT.ID', 'PICK_POINT_ID')
			->addSelect('PRODUCT.ID', 'PRODUCT_ID');

		$query = [];
		$query[] = $ob->getQuery();
		$orders = $ob->exec();
		$result = [];

		while ($itm = $orders->fetch())
		{
			if (!isset($result['ORDER_ID']))
			{
				$result = [
					'ORDER_ID' => $itm['ORDER_ID'],
					'USER_ID' => $itm['USER_ID'],
					'PICK_POINT_ID' => $itm['PICK_POINT_ID'],
					'TOTAL_PRICE' => $itm['ORDER_TOTAL_PRICE']
				];
			}
			$result['PRODUCT_ID'][] = $itm['PRODUCT_ID'];
		}

		ViewManager::show('header', ['title' => 'Обновление товара №' . $result['ORDER_ID']]);

		$ob = PickPointTable::query()
			->addSelect('ID', 'PICK_POINT_ID')
			->addSelect('ADDRESS', 'PICK_POINT_ADDRESS');

		$query[] = $ob->getQuery();
		$sectionObj = $ob->exec();
		$pickPoints = [];

		while ($itm = $sectionObj->fetch())
		{
			$pickPoints[] = [
				'id' => $itm['PICK_POINT_ID'],
				'name' => $itm['PICK_POINT_ADDRESS']
			];
		}

		$ob = ProductTable::query()
			->addSelect('ID', 'PRODUCT_ID')
			->addSelect('NAME', 'PRODUCT_NAME');

		$query[] = $ob->getQuery();
		$productsObj = $ob->exec();
		$products = [];

		while ($itm = $productsObj->fetch())
		{
			$products[] = [
				'id' => $itm['PRODUCT_ID'],
				'name' => $itm['PRODUCT_NAME']
			];
		}

		$ob = UserTable::query()
			->addSelect('ID', 'USER_ID')
			->addSelect('NAME', 'USER_NAME')
			->addSelect('SECOND_NAME', 'USER_SECOND_NAME')
			->addSelect('LAST_NAME', 'USER_LAST_NAME');

		$query[] = $ob->getQuery();
		$usersObj = $ob->exec();
		$users = [];

		while ($itm = $usersObj->fetch())
		{
			$users[] = [
				'id' => $itm['USER_ID'],
				'name' => implode(' ', [$itm['USER_NAME'], $itm['USER_SECOND_NAME'], $itm['USER_LAST_NAME']])
			];
		}

		$result['result'] = [
			'action' => '/order/update/',
			'items' => [
				[
					'name' => 'Покупатель',
					'code' => 'USER',
					'type' => 'list',
					'value' => $result['USER_ID'],
					'list_values' => $users
				],
				[
					'name' => 'Пункт выдачи',
					'code' => 'PICK_POINT',
					'type' => 'list',
					'value' => $result['PICK_POINT_ID'],
					'list_values' => $pickPoints
				],
				[
					'name' => 'Товары',
					'code' => 'PRODUCT',
					'type' => 'multiple_list',
					'value' => $result['PRODUCT_ID'],
					'list_values' => $products
				],
			],
		];
		ViewManager::show('query', ['query' => $query]);
		ViewManager::show('record', $result);

		ViewManager::show('footer');
		return '';
	}

	public static function updateAction()
	{
		// todo удалить колонку с суммой заказа, считать на лету (пока что костыль)
		// todo сделать переподсчёт суммы
		echo '<pre>' . __FILE__ . ':' . __LINE__ . ':<br>' . print_r($_POST, true) . '</pre>';
		return '';
	}

	public static function deleteAction()
	{
		Tools::deleteForManyToMany(OrderTable::PRODUCT_ORDERS_TABLE, 'ORDER_ID', $_GET['id']);
		OrderTable::delete($_GET['id']);
		header('Location: /order/');
		die();
	}
}