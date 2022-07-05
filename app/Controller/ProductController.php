<?php

namespace App\Controller;

use App\Controller;
use App\DataBase\ORM\Query;
use App\DataBase\Tools;
use App\Tables\ProductTable;
use App\Tables\SectionTable;
use App\Tables\StockTable;
use App\ViewManager;

class ProductController extends Controller
{
	public static function show()
	{
		ViewManager::show('header', ['title' => 'Товары']);

		$result['currentUrl'] = $_SERVER['REQUEST_URI'];
		$result['result'] = [
			'columns' => [
				'ID', 'Название', 'Категория', 'Цена', 'Склады'
			]
		];

		$ob = ProductTable::query()
			->registerRuntimeField('SECTION', [
				'data_class' => SectionTable::class,
				'reference' => [
					'this' => 'SECTION_ID',
					'ref' => 'ID',
				],
				'join_type' => 'inner'
			])
			->registerRuntimeField('PRODUCT_STOCK', [
				'data_class' => ProductTable::PRODUCT_STOCK_TABLE,
				'reference' => [
					'this' => 'ID',
					'ref' => 'PRODUCT_ID',
				],
				'join_type' => 'inner'
			])
			->registerRuntimeField('STOCK', [
				'data_class' => StockTable::class,
				'reference' => [
					'this' => 'PRODUCT_STOCK.STOCK_ID',
					'ref' => 'ID',
				],
				'join_type' => 'inner'
			])
			->addOrder('ID')
			->addSelect('product.ID', 'PRODUCT_ID')
			->addSelect('product.NAME', 'PRODUCT_NAME')
			->addSelect('SECTION.NAME', 'SECTION_NAME')
			->addSelect('product.PRICE', 'PRODUCT_PRICE')
			->addSelect('STOCK.CITY', 'STOCK_CITY')
			->addSelect('STOCK.ADDRESS', 'STOCK_ADDRESS');

		$query = $ob->getQuery();
		$users = $ob->exec();
		$stocks = [];

		while ($itm = $users->fetch())
		{
			if (!isset($stocks[$itm['PRODUCT_ID']]))
			{
				$stocks[$itm['PRODUCT_ID']] = [
					'PRODUCT_ID' => $itm['PRODUCT_ID'],
					'PRODUCT_NAME' => $itm['PRODUCT_NAME'],
					'SECTION_NAME' => $itm['SECTION_NAME'],
					'PRODUCT_PRICE' => $itm['PRODUCT_PRICE']
				];
			}
			$stocks[$itm['PRODUCT_ID']]['STOCKS'][] = 'г. ' . $itm['STOCK_CITY'] . ' ' . $itm['STOCK_ADDRESS'];
		}

		foreach ($stocks as $stock)
		{
			$result['result']['items'][] = [
				'ID' => $stock['PRODUCT_ID'],
				'PRODUCT_NAME' => $stock['PRODUCT_NAME'],
				'SECTION_NAME' => $stock['SECTION_NAME'],
				'PRODUCT_PRICE' => $stock['PRODUCT_PRICE'],
				'STOCKS' => '<p>' . implode('</p><p>', $stock['STOCKS']) . '</p>'
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
		ViewManager::show('header', ['title' => 'Добавление товара']);

		$query = [];

		$ob = SectionTable::query()
			->addSelect('ID', 'SECTION_ID')
			->addSelect('NAME', 'SECTION_NAME');

		$query[] = $ob->getQuery();
		$sectionObj = $ob->exec();
		$sections = [];

		while ($itm = $sectionObj->fetch())
		{
			$sections[] = [
				'id' => $itm['SECTION_ID'],
				'name' => $itm['SECTION_NAME']
			];
		}

		$ob = StockTable::query()
			->addSelect('ID', 'STOCK_ID')
			->addSelect('CITY', 'STOCK_CITY')
			->addSelect('ADDRESS', 'STOCK_ADDRESS');

		$query[] = $ob->getQuery();
		$stocksObj = $ob->exec();
		$stocks = [];

		while ($itm = $stocksObj->fetch())
		{
			$stocks[] = [
				'id' => $itm['STOCK_ID'],
				'name' => 'г. ' . $itm['STOCK_CITY'] . ' ' . $itm['STOCK_ADDRESS']
			];
		}

		$result['result'] = [
			'action' => '/product/add/',
			'items' => [
				[
					'name' => 'Название',
					'code' => 'NAME',
					'type' => 'text',
					'value' => '',
					'list_values' => []
				],
				[
					'name' => 'Категория',
					'code' => 'SECTION',
					'type' => 'list',
					'value' => '',
					'list_values' => $sections
				],
				[
					'name' => 'Цена',
					'code' => 'PRICE',
					'type' => 'int',
					'value' => '',
					'list_values' => []
				],
				[
					'name' => 'Склады',
					'code' => 'STOCK',
					'type' => 'multiple_list',
					'value' => [],
					'list_values' => $stocks
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
		if (!$_POST['STOCK'])
		{
			header('Location: /product/');
			die();
		}

		$productId = ProductTable::add([
			'NAME' => $_POST['NAME'],
			'SECTION_ID' => $_POST['SECTION'],
			'PRICE' => $_POST['PRICE']
		]);

		foreach ($_POST['STOCK'] as $item)
		{
			ProductTable::add([
				'PRODUCT_ID' => $productId,
				'STOCK_ID' => $item
			], ProductTable::PRODUCT_STOCK_TABLE);
		}

		header('Location: /product/');
		die();
	}

	public static function update()
	{
		$ob = ProductTable::query()
			->registerRuntimeField('SECTION', [
				'data_class' => SectionTable::class,
				'reference' => [
					'this' => 'SECTION_ID',
					'ref' => 'ID',
				],
				'join_type' => 'inner'
			])
			->registerRuntimeField('PRODUCT_STOCK', [
				'data_class' => ProductTable::PRODUCT_STOCK_TABLE,
				'reference' => [
					'this' => 'ID',
					'ref' => 'PRODUCT_ID',
				],
				'join_type' => 'inner'
			])
			->registerRuntimeField('STOCK', [
				'data_class' => StockTable::class,
				'reference' => [
					'this' => 'PRODUCT_STOCK.STOCK_ID',
					'ref' => 'ID',
				],
				'join_type' => 'inner'
			])
			->where('ID', $_GET['id'])
			->addSelect('product.ID', 'PRODUCT_ID')
			->addSelect('product.NAME', 'PRODUCT_NAME')
			->addSelect('product.PRICE', 'PRODUCT_PRICE')
			->addSelect('SECTION.ID', 'SECTION_ID')
			->addSelect('STOCK.ID', 'STOCK_ID');

		$query = [];
		$query[] = $ob->getQuery();
		$users = $ob->exec();
		$result = [];

		while ($itm = $users->fetch())
		{
			if (!isset($result['PRODUCT_ID']))
			{
				$result = [
					'PRODUCT_ID' => $itm['PRODUCT_ID'],
					'PRODUCT_NAME' => $itm['PRODUCT_NAME'],
					'SECTION_ID' => $itm['SECTION_ID'],
					'PRODUCT_PRICE' => $itm['PRODUCT_PRICE']
				];
			}
			$result['STOCKS'][] = $itm['STOCK_ID'];
		}

		ViewManager::show('header', ['title' => 'Обновление товара №' . $result['PRODUCT_ID']]);

		$ob = SectionTable::query()
			->addSelect('ID', 'SECTION_ID')
			->addSelect('NAME', 'SECTION_NAME');

		$query[] = $ob->getQuery();
		$sectionObj = $ob->exec();
		$sections = [];

		while ($itm = $sectionObj->fetch())
		{
			$sections[] = [
				'id' => $itm['SECTION_ID'],
				'name' => $itm['SECTION_NAME']
			];
		}

		$ob = StockTable::query()
			->addSelect('ID', 'STOCK_ID')
			->addSelect('CITY', 'STOCK_CITY')
			->addSelect('ADDRESS', 'STOCK_ADDRESS');

		$query[] = $ob->getQuery();
		$stocksObj = $ob->exec();
		$stocks = [];


		while ($itm = $stocksObj->fetch())
		{
			$stocks[] = [
				'id' => $itm['STOCK_ID'],
				'name' => 'г. ' . $itm['STOCK_CITY'] . ' ' . $itm['STOCK_ADDRESS']
			];
		}

		$result['result'] = [
			'action' => '/product/update/',
			'items' => [
				[
					'name' => 'Название',
					'code' => 'NAME',
					'type' => 'text',
					'value' => $result['PRODUCT_NAME'],
					'list_values' => []
				],
				[
					'name' => 'Категория',
					'code' => 'SECTION',
					'type' => 'list',
					'value' => $result['SECTION_ID'],
					'list_values' => $sections
				],
				[
					'name' => 'Цена',
					'code' => 'PRICE',
					'type' => 'int',
					'value' => $result['PRODUCT_PRICE'],
					'list_values' => []
				],
				[
					'name' => 'Склады',
					'code' => 'STOCK',
					'type' => 'multiple_list',
					'value' => $result['STOCKS'],
					'list_values' => $stocks
				],
				[
					'code' => 'ID',
					'value' => $result['PRODUCT_ID']
				]
			],
		];
		ViewManager::show('query', ['query' => $query]);
		ViewManager::show('record', $result);

		ViewManager::show('footer');
		return '';
	}

	/**
	 * @throws \Exception
	 */
	public static function updateAction()
	{
		$ob = ProductTable::query()
			->registerRuntimeField('PRODUCT_STOCK', [
				'data_class' => ProductTable::PRODUCT_STOCK_TABLE,
				'reference' => [
					'this' => 'ID',
					'ref' => 'PRODUCT_ID',
				],
				'join_type' => 'inner'
			])
			->where('ID', $_POST['ID'])
			->addSelect('PRODUCT_STOCK.STOCK_ID', 'STOCK_ID')
			->addSelect('PRICE', 'PRODUCT_PRICE');

		$_SESSION['dbQuery'][] = $ob->getQuery();
		$products = $ob->exec()->fetchAll();
		$product = array_column($products, 'STOCK_ID');
		$oldPrice = array_pop($products)['PRODUCT_PRICE'];

		foreach ($product as $item)
		{
			if (!in_array($item, $_POST['STOCK']))
			{
				Tools::deleteForManyToMany(ProductTable::PRODUCT_STOCK_TABLE, [
					'PRODUCT_ID' => $_POST['ID'],
					'STOCK_ID' => $item
				]);
			}
		}

		foreach ($_POST['STOCK'] as $item)
		{
			if (!in_array($item, $product))
			{
				ProductTable::add([
					'PRODUCT_ID' => $_POST['ID'],
					'STOCK_ID' => $item
				], ProductTable::PRODUCT_STOCK_TABLE);
			}
		}

		// todo сделать переподсчёт суммы у заказов
		ProductTable::update($_POST['ID'], [
			'NAME' => $_POST['NAME'],
			'SECTION_ID' => $_POST['SECTION'],
			'PRICE' => $_POST['PRICE']
		]);

		ProductTable::recalculateOrders($oldPrice, $_POST['PRICE'], $_POST['ID']);

		header('Location: /product/');
		die();
	}

	public static function deleteAction()
	{
		Tools::deleteForManyToMany(ProductTable::PRODUCT_STOCK_TABLE, ['PRODUCT_ID' => $_GET['id']]);
		ProductTable::delete($_GET['id']);
		header('Location: /product/');
		die();
	}
}