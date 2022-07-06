<?php

namespace App\Controller;

use App\Controller;
use App\DataBase\Tools;
use App\Tables\PickPointTable;
use App\ViewManager;

class PickPointController extends Controller
{
	public static function show()
	{
		ViewManager::show('header', ['title' => 'Пункты выдачи']);

		try
		{
			$result['currentUrl'] = $_SERVER['REQUEST_URI'];
			$result['result'] = [
				'columns' => [
					'ID', 'Адрес',
				]
			];

			$ob = PickPointTable::query()
				->addSelect('ID', 'PICK_POINT_ID')
				->addSelect('ADDRESS', 'PICK_POINT_ADDRESS');

			$query = $ob->getQuery();
			$users = $ob->exec();

			while ($itm = $users->fetch())
			{
				$result['result']['items'][] = [
					'ID' => $itm['PICK_POINT_ID'],
					'ADDRESS' => $itm['PICK_POINT_ADDRESS'],
				];
			}
		}catch (\Throwable $e)
		{
			$_SESSION['alert'][] = $e->getMessage();
		}

		ViewManager::show('query', ['query' => Tools::getQuery()]);
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
		ViewManager::show('query', ['query' => Tools::getQuery()]);
		ViewManager::show('record', $result);

		ViewManager::show('footer');
		return '';
	}

	public static function addAction()
	{
		try
		{
			PickPointTable::add([
				'ADDRESS' => $_POST['ADDRESS']
			]);
		}catch (\Throwable $e)
		{
			$_SESSION['alert'][] = $e->getMessage();
		}

		header('Location: /pick-point/');
		die();
	}

	public static function update()
	{
		try
		{
			$ob = PickPointTable::query()
				->where('ID', $_GET['id'])
				->addSelect('ID', 'PICK_POINT_ID')
				->addSelect('ADDRESS', 'PICK_POINT_ADDRESS');

			$query = $ob->getQuery();
			$stock = $ob->exec()->fetch();

			$result['result'] = [
				'action' => '/pick-point/update/',
				'items' => [
					[
						'name' => 'Адрес',
						'code' => 'ADDRESS',
						'type' => 'text',
						'value' => $stock['PICK_POINT_ADDRESS'],
						'list_values' => []
					],
					[
						'code' => 'ID',
						'value' => $stock['PICK_POINT_ID']
					]
				],
			];
		}catch (\Throwable $e)
		{
			$_SESSION['alert'][] = $e->getMessage();
		}
		ViewManager::show('header', ['title' => 'Обновление пункта выдачи №' . $stock['PICK_POINT_ID']]);

		ViewManager::show('query', ['query' => Tools::getQuery()]);
		ViewManager::show('record', $result ?? []);

		ViewManager::show('footer');
		return '';
	}

	public static function updateAction()
	{
		try
		{
			PickPointTable::update($_POST['ID'], [
				'ADDRESS' => $_POST['ADDRESS'],
			]);
		}catch (\Throwable $e)
		{
			$_SESSION['alert'][] = $e->getMessage();
		}

		header('Location: /pick-point/');
		die();
	}

	public static function deleteAction()
	{
		try
		{
			PickPointTable::delete($_GET['id']);
		}catch (\Throwable $e)
		{
			$_SESSION['alert'][] = $e->getMessage();
		}
		header('Location: /pick-point/');
		die();
	}
}