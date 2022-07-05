<?php

namespace App\Controller;

use App\Controller;
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
			->addSelect('ID')
			->addSelect('CITY')
			->addSelect('ADDRESS');

		$query = $ob->getQuery();
		$users = $ob->exec();

		while ($itm = $users->fetch())
		{
			$result['result']['items'][] = [
				'ID' => $itm['stock_ID_ALIAS'],
				'CITY' => $itm['stock_CITY_ALIAS'],
				'ADDRESS' => $itm['stock_ADDRESS_ALIAS'],
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
		ViewManager::show('query', ['query' => []]);
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
		ViewManager::show('header', ['title' => 'Заказы']);
		ViewManager::show('footer');
		return '';
	}

	public static function updateAction()
	{

	}

	public static function deleteAction()
	{
		StockTable::delete($_GET['id']);
		header('Location: /stock/');
		die();
	}
}