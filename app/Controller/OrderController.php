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

		try
		{
			$result['currentUrl'] = $_SERVER['REQUEST_URI'];
			$result['result'] = [
				'columns' => [
					'ID', 'Покупатель', 'Пункт выдачи', 'Сумма', 'Товары'
				]
			];

			$users = OrderTable::query()
				->registerRuntimeField('USER', [
					'data_class' => UserTable::class,
					'reference' => [
						'this' => 'USER_ID',
						'ref' => 'ID',
					],
					'join_type' => 'left'
				])
				->registerRuntimeField('PICK_POINT', [
					'data_class' => PickPointTable::class,
					'reference' => [
						'this' => 'PICK_POINT_ID',
						'ref' => 'ID',
					],
					'join_type' => 'left'
				])
				->registerRuntimeField('PRODUCT_ORDERS', [
					'data_class' => OrderTable::PRODUCT_ORDERS_TABLE,
					'reference' => [
						'this' => 'ID',
						'ref' => 'ORDER_ID',
					],
					'join_type' => 'left'
				])
				->registerRuntimeField('PRODUCT', [
					'data_class' => ProductTable::class,
					'reference' => [
						'this' => 'PRODUCT_ORDERS.PRODUCT_ID',
						'ref' => 'ID',
					],
					'join_type' => 'left'
				])
				->addSelect('order.ID', 'ORDER_ID')
				->addSelect('order.TOTAL_PRICE', 'ORDER_TOTAL_PRICE')
				->addSelect('USER.SECOND_NAME', 'USER_SECOND_NAME')
				->addSelect('USER.ID', 'USER_ID')
				->addSelect('PICK_POINT.ADDRESS', 'PICK_POINT_ADDRESS')
				->addSelect('PRODUCT.NAME', 'PRODUCT_NAME')
				->addOrder('order.ID', 'ASC')
				->exec();

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
		}catch (\Throwable $e)
		{
			$_SESSION['alert'][] = $e->getMessage();
		}

		ViewManager::show('query', ['query' => Tools::getQuery()]);
		ViewManager::show('table', $result ?? []);
		ViewManager::show('footer');
		return '';
	}

	public static function add()
	{
		ViewManager::show('header', ['title' => 'Добавление заказа']);

		try
		{
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
				->addSelect('NAME', 'PRODUCT_NAME')
				->addOrder('ID');

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
		}catch (\Throwable $e)
		{
			$_SESSION['alert'][] = $e->getMessage();
		}

		ViewManager::show('query', ['query' => Tools::getQuery()]);
		ViewManager::show('record', $result ?? []);

		ViewManager::show('footer');
		return '';
	}

	public static function addAction()
	{
		try
		{
			if (!$_POST['PRODUCT'])
			{
				throw new \Exception('Не выбраны товары');
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
		}catch (\Throwable $e)
		{
			$_SESSION['alert'][] = $e->getMessage();
		}

		header('Location: /order/');
		die();
	}

	public static function update()
	{
		try
		{
			$ob = OrderTable::query()
				->registerRuntimeField('USER', [
					'data_class' => UserTable::class,
					'reference' => [
						'this' => 'USER_ID',
						'ref' => 'ID',
					],
					'join_type' => 'left'
				])
				->registerRuntimeField('PICK_POINT', [
					'data_class' => PickPointTable::class,
					'reference' => [
						'this' => 'PICK_POINT_ID',
						'ref' => 'ID',
					],
					'join_type' => 'left'
				])
				->registerRuntimeField('PRODUCT_ORDERS', [
					'data_class' => OrderTable::PRODUCT_ORDERS_TABLE,
					'reference' => [
						'this' => 'ID',
						'ref' => 'ORDER_ID',
					],
					'join_type' => 'left'
				])
				->registerRuntimeField('PRODUCT', [
					'data_class' => ProductTable::class,
					'reference' => [
						'this' => 'PRODUCT_ORDERS.PRODUCT_ID',
						'ref' => 'ID',
					],
					'join_type' => 'left'
				])
				->where('ID', $_GET['id'])
				->addSelect('order.ID', 'ORDER_ID')
				->addSelect('order.TOTAL_PRICE', 'ORDER_TOTAL_PRICE')
				->addSelect('USER.SECOND_NAME', 'USER_SECOND_NAME')
				->addSelect('USER.ID', 'USER_ID')
				->addSelect('PICK_POINT.ID', 'PICK_POINT_ID')
				->addSelect('PRODUCT.ID', 'PRODUCT_ID')
				->addOrder('PRODUCT.ID');

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
						'value' => $result['USER_ID'] ?? '',
						'list_values' => $users
					],
					[
						'name' => 'Пункт выдачи',
						'code' => 'PICK_POINT',
						'type' => 'list',
						'value' => $result['PICK_POINT_ID'] ?? '',
						'list_values' => $pickPoints
					],
					[
						'name' => 'Товары',
						'code' => 'PRODUCT',
						'type' => 'multiple_list',
						'value' => $result['PRODUCT_ID'] ?? [],
						'list_values' => $products
					],
					[
						'code' => 'ID',
						'value' => $result['ORDER_ID']
					]
				],
			];
		}catch (\Throwable $e)
		{
			$_SESSION['alert'][] = $e->getMessage();
		}

		ViewManager::show('header', ['title' => 'Обновление товара №' . $result['ORDER_ID']]);
		ViewManager::show('query', ['query' => Tools::getQuery()]);
		ViewManager::show('record', $result);

		ViewManager::show('footer');
		return '';
	}

	public static function updateAction()
	{
		try
		{
			if (!$_POST['PRODUCT'])
			{
				throw new \Exception('Не выбраны товары');
			}

			$ob = OrderTable::query()
				->registerRuntimeField('PRODUCT_ORDERS', [
					'data_class' => OrderTable::PRODUCT_ORDERS_TABLE,
					'reference' => [
						'this' => 'ID',
						'ref' => 'ORDER_ID',
					],
					'join_type' => 'inner'
				])
				->where('ID', $_POST['ID'])
				->addSelect('PRODUCT_ORDERS.PRODUCT_ID', 'PRODUCT_ID');

			$order = array_column($ob->exec()->fetchAll(), 'PRODUCT_ID');
			$resultIds = [];

			foreach ($order as $item)
			{
				if (!in_array($item, $_POST['PRODUCT']))
				{
					Tools::deleteForManyToMany(OrderTable::PRODUCT_ORDERS_TABLE, [
						'ORDER_ID' => $_POST['ID'],
						'PRODUCT_ID' => $item
					]);
				}
				else
				{
					$resultIds[] = $item;
				}
			}

			foreach ($_POST['PRODUCT'] as $item)
			{
				if (!in_array($item, $order))
				{
					OrderTable::add([
						'ORDER_ID' => $_POST['ID'],
						'PRODUCT_ID' => $item
					], OrderTable::PRODUCT_ORDERS_TABLE);

					$resultIds[] = $item;
				}
			}

			$sum = Tools::getSum(ProductTable::getTableName(), 'PRICE', $resultIds); // todo удалить колонку с суммой заказа, считать на лету (пока что костыль)

			OrderTable::update($_POST['ID'], [
				'USER_ID' => $_POST['USER'],
				'PICK_POINT_ID' => $_POST['PICK_POINT'],
				'TOTAL_PRICE' => $sum
			]);
		}catch (\Throwable $e)
		{
			$_SESSION['alert'][] = $e->getMessage();
		}

		header('Location: /order/');
		die();
	}

	/**
	 * @throws \Exception
	 */
	public static function deleteAction()
	{
		try
		{
			Tools::deleteForManyToMany(OrderTable::PRODUCT_ORDERS_TABLE, ['ORDER_ID' => $_GET['id']]);
			OrderTable::delete($_GET['id']);
		}catch (\Throwable $e)
		{
			$_SESSION['alert'][] = $e->getMessage();
		}
		header('Location: /order/');
		die();
	}
}