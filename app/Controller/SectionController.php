<?php

namespace App\Controller;

use App\Controller;
use App\DataBase\Tools;
use App\Tables\SectionTable;
use App\Tables\UserTable;
use App\ViewManager;

class SectionController extends Controller
{
	public static function show()
	{
		ViewManager::show('header', ['title' => 'Категории товаров']);

		try
		{
			$result['currentUrl'] = $_SERVER['REQUEST_URI'];
			$result['result'] = [
				'columns' => [
					'ID', 'Название категории',
				]
			];

			$ob = SectionTable::query()
				->addOrder('ID')
				->addSelect('ID', 'SECTION_ID')
				->addSelect('NAME', 'SECTION_NAME');

			$query = $ob->getQuery();
			$users = $ob->exec();

			while ($itm = $users->fetch())
			{
				$result['result']['items'][] = [
					'ID' => $itm['SECTION_ID'],
					'NAME' => $itm['SECTION_NAME'],
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
		ViewManager::show('query', ['query' => Tools::getQuery()]);
		ViewManager::show('record', $result);
		ViewManager::show('footer');
		return '';
	}

	public static function addAction()
	{
		try
		{
			SectionTable::add([
				'NAME' => $_POST['NAME']
			]);
		}catch (\Throwable $e)
		{
			$_SESSION['alert'][] = $e->getMessage();
		}

		header('Location: /product-section/');
		die();
	}

	public static function update()
	{
		try
		{
			$ob = SectionTable::query()
				->where('ID', $_GET['id'])
				->addSelect('ID', 'SECTION_ID')
				->addSelect('NAME', 'SECTION_NAME');

			$query = $ob->getQuery();
			$section = $ob->exec()->fetch();

			$result['result'] = [
				'action' => '/product-section/update/',
				'items' => [
					[
						'name' => 'Название категории',
						'code' => 'NAME',
						'type' => 'text',
						'value' => $section['SECTION_NAME'],
						'list_values' => []
					],
					[
						'code' => 'ID',
						'value' => $section['SECTION_ID']
					]
				],
			];
		}catch (\Throwable $e)
		{
			$_SESSION['alert'][] = $e->getMessage();
		}
		ViewManager::show('header', ['title' => 'Обновление категории товара №' . $section['SECTION_ID']]);

		ViewManager::show('query', ['query' => Tools::getQuery()]);
		ViewManager::show('record', $result ?? []);

		ViewManager::show('footer');
		return '';
	}

	public static function updateAction()
	{
		try
		{
			SectionTable::update($_POST['ID'], [
				'NAME' => $_POST['NAME'],
			]);
		}catch (\Throwable $e)
		{
			$_SESSION['alert'][] = $e->getMessage();
		}

		header('Location: /product-section/');
		die();
	}

	public static function deleteAction()
	{
		try
		{
			SectionTable::delete($_GET['id']);
		}catch (\Throwable $e)
		{
			$_SESSION['alert'][] = $e->getMessage();
		}
		header('Location: /product-section/');
		die();
	}
}