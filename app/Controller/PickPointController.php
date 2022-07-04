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