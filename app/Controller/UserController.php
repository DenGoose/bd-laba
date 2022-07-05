<?php

namespace App\Controller;

use App\Controller;
use App\Tables\UserTable;
use App\ViewManager;

class UserController extends Controller
{
	public static function show()
	{
		ViewManager::show('header', ['title' => 'Покупатели']);

		$result['currentUrl'] = $_SERVER['REQUEST_URI'];
		$result['result'] = [
			'columns' => [
				'ID', 'Имя', 'Фамилия' , 'Отчество'
			]
		];

		$ob = UserTable::query()
			->addOrder('ID')
			->addSelect('ID')
			->addSelect('NAME')
			->addSelect('SECOND_NAME')
			->addSelect('LAST_NAME');

		$query = $ob->getQuery(); // todo
		$users = $ob->exec();

		while ($itm = $users->fetch())
		{
			$result['result']['items'][] = [
				'ID' => $itm['user_ID_ALIAS'],
				'NAME' => $itm['user_NAME_ALIAS'],
				'SECOND_NAME' => $itm['user_SECOND_NAME_ALIAS'],
				'LAST_NAME' => $itm['user_LAST_NAME_ALIAS']
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
		ViewManager::show('header', ['title' => 'Добавление покупателя']);

		$result['result'] = [
			'action' => '/user/add/',
			'items' => [
				[
					'name' => 'Имя',
					'code' => 'NAME',
					'type' => 'text',
					'value' => '',
					'list_values' => []
				],
				[
					'name' => 'Фамилия',
					'code' => 'SECOND_NAME',
					'type' => 'text',
					'value' => '',
					'list_values' => []
				],
				[
					'name' => 'Отчество',
					'code' => 'LAST_NAME',
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
		$productId = UserTable::add([
			'NAME' => $_POST['NAME'],
			'SECOND_NAME' => $_POST['SECOND_NAME'],
			'LAST_NAME' => $_POST['LAST_NAME']
		]);

		header('Location: /user/');
		die();
	}

	public static function update()
	{
		$ob = UserTable::query()
			->where('ID', $_GET['id'])
			->addSelect('ID', 'USER_ID')
			->addSelect('NAME', 'USER_NAME')
			->addSelect('SECOND_NAME', 'USER_SECOND_NAME')
			->addSelect('LAST_NAME', 'USER_LAST_NAME');

		$query = $ob->getQuery();
		$user = $ob->exec()->fetch();

		ViewManager::show('header', ['title' => 'Обновление покупателя №' . $user['USER_ID']]);

		$result['result'] = [
			'action' => '/user/update/',
			'items' => [
				[
					'name' => 'Имя',
					'code' => 'NAME',
					'type' => 'text',
					'value' => $user['USER_NAME'],
					'list_values' => []
				],
				[
					'name' => 'Фамилия',
					'code' => 'SECOND_NAME',
					'type' => 'text',
					'value' => $user['USER_SECOND_NAME'],
					'list_values' => []
				],
				[
					'name' => 'Отчество',
					'code' => 'LAST_NAME',
					'type' => 'text',
					'value' => $user['USER_LAST_NAME'],
					'list_values' => []
				],
				[
					'code' => 'ID',
					'value' => $user['USER_ID']
				]
			],
		];
		ViewManager::show('query', ['query' => [$query]]);
		ViewManager::show('record', $result);

		ViewManager::show('footer');
		return '';
	}

	public static function updateAction()
	{
		UserTable::update($_POST['ID'], [
			'NAME' => $_POST['NAME'],
			'SECOND_NAME' => $_POST['SECOND_NAME'],
			'LAST_NAME' => $_POST['LAST_NAME']
		]);

		header('Location: /user/');
		die();
	}

	public static function deleteAction()
	{
		UserTable::delete($_GET['id']);
		header('Location: /user/');
		die();
	}
}