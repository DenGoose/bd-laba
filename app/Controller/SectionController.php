<?php

namespace App\Controller;

use App\Controller;
use App\Tables\SectionTable;
use App\Tables\UserTable;
use App\ViewManager;

class SectionController extends Controller
{
	public static function show()
	{
		ViewManager::show('header', ['title' => 'Категории товаров']);

		$result['currentUrl'] = $_SERVER['REQUEST_URI'];
		$result['result'] = [
			'columns' => [
				'ID', 'Название категории',
			]
		];

		$ob = SectionTable::query()
			->addOrder('ID')
			->addSelect('ID')
			->addSelect('NAME');

		$query = $ob->getQuery();
		$users = $ob->exec();

		while ($itm = $users->fetch())
		{
			$result['result']['items'][] = [
				'ID' => $itm['product_section_ID_ALIAS'],
				'NAME' => $itm['product_section_NAME_ALIAS'],
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
		ViewManager::show('header', ['title' => 'Добавление категории товара']);

		$result['result'] = [
			'action' => '/product-section/add/',
			'items' => [
				[
					'name' => 'Название категории',
					'code' => 'NAME',
					'type' => 'text',
					'value' => '',
					'list_values' => []
				],
			],
		];
		ViewManager::show('record', $result);

		ViewManager::show('footer');
		return '';
	}

	public static function addAction()
	{
		$productId = SectionTable::add([
			'NAME' => $_POST['NAME']
		]);

		header('Location: /product-section/');
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
		SectionTable::delete($_GET['id']);
		header('Location: /product-section/');
		die();
	}
}