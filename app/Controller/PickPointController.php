<?php

namespace App\Controller;

use App\Controller;
use App\Tables\PickPointTable;
use App\ViewManager;

class PickPointController extends Controller
{
	public static function show()
	{
		ViewManager::show('header', ['title' => 'Пункты выдачи']);

		$result['currentUrl'] = $_SERVER['REQUEST_URI'];
		$result['result'] = [
			'columns' => [
				'ID', 'Адрес',
			]
		];

		$ob = PickPointTable::query()
			->addSelect('ID')
			->addSelect('ADDRESS');

		$query = $ob->getQuery();
		$users = $ob->exec();

		while ($itm = $users->fetch())
		{
			$result['result']['items'][] = [
				'ID' => $itm['pick_point_ID_ALIAS'],
				'ADDRESS' => $itm['pick_point_ADDRESS_ALIAS'],
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
		ViewManager::show('header', ['title' => 'Добавление пункта выдачи']);

		$result['result'] = [
			'action' => '/pick-point/add/',
			'items' => [
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
		$productId = PickPointTable::add([
			'ADDRESS' => $_POST['ADDRESS']
		]);

		header('Location: /pick-point/');
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

	}
}