<?php

namespace App\Controller;

use App\Controller;
use App\DataBase\Tools;
use App\Tables\StockTable;
use App\ViewManager;

class StockController extends Controller
{
	public static function show(): string
	{
		ViewManager::show('header', ['title' => 'Склады']);

		$result['currentUrl'] = $_SERVER['REQUEST_URI'];
		$result['result'] = [
			'columns' => [
				'ID', 'Город', 'Адрес'
			]
		];

		$ob = StockTable::query()
			->addOrder('ID')
			->addSelect('ID', 'STOCK_ID')
			->addSelect('CITY', 'STOCK_CITY')
			->addSelect('ADDRESS', 'STOCK_ADDRESS');

		$query = $ob->getQuery();
		$users = $ob->exec();

		while ($itm = $users->fetch())
		{
			$result['result']['items'][] = [
				'ID' => $itm['STOCK_ID'],
				'CITY' => $itm['STOCK_CITY'],
				'ADDRESS' => $itm['STOCK_ADDRESS'],
			];
		}

		ViewManager::show('query', ['query' => Tools::getQuery()]);
		ViewManager::show('table', $result);
		ViewManager::show('footer');
		return '';
	}

	public static function add(): string
	{
		ViewManager::show('header', ['title' => 'Добавление склада']);

		$result['result'] = [
			'action' => '/stock/add/',
			'items' => [
				[
					'name' => 'Город',
					'code' => 'CITY',
					'type' => 'text',
					'value' => '',
					'list_values' => []
				],
				[
					'name' => 'Адрес',
					'code' => 'ADDRESS',
					'type' => 'text',
					'value' => '',
					'list_values' => []
				],
			],
		];
		ViewManager::show('query', ['query' => Tools::getQuery()]);
		ViewManager::show('record', $result);

		ViewManager::show('footer');
		return '';
	}

	public static function addAction()
	{
		$productId = StockTable::add([
			'CITY' => $_POST['CITY'],
			'ADDRESS' => $_POST['ADDRESS']
		]);

		header('Location: /stock/');
		die();
	}

	public static function update()
	{
		$ob = StockTable::query()
			->where('ID', $_GET['id'])
			->addSelect('ID', 'STOCK_ID')
			->addSelect('CITY', 'STOCK_CITY')
			->addSelect('ADDRESS', 'STOCK_ADDRESS');

		$query = $ob->getQuery();
		$stock = $ob->exec()->fetch();

		ViewManager::show('header', ['title' => 'Обновление склада №' . $stock['STOCK_ID']]);

		$result['result'] = [
			'action' => '/stock/update/',
			'items' => [
				[
					'name' => 'Город',
					'code' => 'CITY',
					'type' => 'text',
					'value' => $stock['STOCK_CITY'],
					'list_values' => []
				],
				[
					'name' => 'Адрес',
					'code' => 'ADDRESS',
					'type' => 'text',
					'value' => $stock['STOCK_ADDRESS'],
					'list_values' => []
				],
				[
					'code' => 'ID',
					'value' => $stock['STOCK_ID']
				]
			],
		];
		ViewManager::show('query', ['query' => Tools::getQuery()]);
		ViewManager::show('record', $result);

		ViewManager::show('footer');
		return '';
	}

	public static function updateAction()
	{
		StockTable::update($_POST['ID'], [
			'CITY' => $_POST['CITY'],
			'ADDRESS' => $_POST['ADDRESS'],
		]);

		header('Location: /stock/');
		die();
	}

	public static function deleteAction()
	{
		StockTable::delete($_GET['id']);
		header('Location: /stock/');
		die();
	}
}